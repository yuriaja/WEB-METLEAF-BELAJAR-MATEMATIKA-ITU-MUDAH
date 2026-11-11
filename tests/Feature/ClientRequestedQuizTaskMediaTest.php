<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientRequestedQuizTaskMediaTest extends TestCase
{
    use RefreshDatabase;

    private function actor(string $role, string $suffix): User
    {
        return User::create([
            'name' => $role.' '.$suffix,
            'email' => $role.$suffix.'@example.test',
            'password' => 'secret123',
            'role' => $role,
        ]);
    }

    private function classroom(User $teacher): SchoolClass
    {
        return SchoolClass::create([
            'name' => 'VII A',
            'teacher_id' => $teacher->id,
            'join_code' => 'ABCD1234',
        ]);
    }

    private function quiz(User $teacher, SchoolClass $class): Assessment
    {
        $this->actingAs($teacher)->post(route('teacher.quizzes.create'), [
            'title' => 'Kuis Campuran',
            'class_id' => $class->id,
            'type' => 'kuis',
            'time_limit_minutes' => 30,
        ])->assertRedirect();
        return Assessment::where('title', 'Kuis Campuran')->firstOrFail();
    }

    private function questionsPayload(SchoolClass $class): array
    {
        return [
            'title' => 'Kuis Campuran',
            'class_id' => $class->id,
            'type' => 'kuis',
            'time_limit_minutes' => 30,
            'passing_score' => 70,
            'max_attempts' => 2,
            'questions' => [
                [
                    'question_type' => 'multiple_choice',
                    'text' => '2 + 3 = ?',
                    'options' => ['3', '5', '6', '7'],
                    'correct_answer' => 1,
                    'points' => 10,
                    'resource_url' => 'https://youtu.be/abcd',
                    'file' => UploadedFile::fake()->image('soal.png'),
                ],
                [
                    'question_type' => 'short_answer',
                    'text' => 'Tuliskan angka lima dengan huruf.',
                    'short_answer' => 'Lima',
                    'points' => 10,
                ],
            ],
        ];
    }

    public function test_teacher_can_publish_mixed_quiz_and_student_can_answer_both_types(): void
    {
        Storage::fake('local');
        $teacher = $this->actor('guru', 'mix');
        $student = $this->actor('siswa', 'mix');
        $class = $this->classroom($teacher);
        $class->students()->attach($student->id);
        $quiz = $this->quiz($teacher, $class);

        $this->actingAs($teacher)->put(route('teacher.quizzes.update', $quiz), $this->questionsPayload($class))
            ->assertRedirect(route('teacher.quizzes'));
        $questions = $quiz->questions()->get();
        $this->assertCount(2, $questions);
        $this->assertSame('multiple_choice', $questions[0]->type);
        $this->assertSame('short_answer', $questions[1]->type);
        $this->assertSame('https://youtu.be/abcd', $questions[0]->resource_url);
        Storage::disk('local')->assertExists($questions[0]->attachment_path);
        $this->actingAs($teacher)->get(route('classroom.quiz.attachment', $questions[0]))->assertOk();
        $this->actingAs($student)->get(route('classroom.quiz.take', $quiz))
            ->assertOk()->assertSee('Tulis jawaban singkat...')->assertSee('Buka video / tautan soal');

        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$questions[0]->id => '1', $questions[1]->id => '  LIMA  '],
        ])->assertRedirect();
        $attempt = AssessmentAttempt::where('user_id', $student->id)->firstOrFail();
        $this->assertSame(100.0, (float) $attempt->score);
        $this->actingAs($student)->get(route('siswa.quiz.result', $attempt))
            ->assertOk()->assertSee('LIMA');
        $this->actingAs($teacher)->get(route('teacher.quizzes.attempt', $attempt))
            ->assertOk()->assertSee('LIMA');
    }

    public function test_unauthorized_users_cannot_access_question_file_and_bad_answers_rejected(): void
    {
        Storage::fake('local');
        $teacher = $this->actor('guru', 'secure');
        $student = $this->actor('siswa', 'secure');
        $outsider = $this->actor('siswa', 'outsider');
        $class = $this->classroom($teacher);
        $class->students()->attach($student->id);
        $quiz = $this->quiz($teacher, $class);
        $this->actingAs($teacher)->put(route('teacher.quizzes.update', $quiz), $this->questionsPayload($class))
            ->assertRedirect();
        $questions = $quiz->questions()->get();

        $this->actingAs($outsider)->get(route('classroom.quiz.attachment', $questions[0]))->assertForbidden();
        $this->actingAs($student)->get(route('classroom.quiz.attachment', $questions[0]).'?preview=1')->assertOk();
        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$questions[0]->id => '99', $questions[1]->id => 'Lima'],
        ])->assertSessionHasErrors('answers');
        $this->assertSame(0, AssessmentAttempt::count());
    }

    public function test_editing_quiz_preserves_existing_file_unless_removed(): void
    {
        Storage::fake('local');
        $teacher = $this->actor('guru', 'edit');
        $class = $this->classroom($teacher);
        $quiz = $this->quiz($teacher, $class);
        $payload = $this->questionsPayload($class);
        $this->actingAs($teacher)->put(route('teacher.quizzes.update', $quiz), $payload)->assertRedirect();
        $old = $quiz->questions()->firstOrFail();
        $oldPath = $old->attachment_path;
        $edit = $this->questionsPayload($class);
        unset($edit['questions'][0]['file']);
        $edit['questions'][0]['id'] = $old->id;
        $edit['questions'][1]['id'] = $quiz->questions()->orderBy('sort_order')->skip(1)->firstOrFail()->id;
        $this->actingAs($teacher)->put(route('teacher.quizzes.update', $quiz), $edit)->assertRedirect();
        $this->assertSame($oldPath, $old->fresh()->attachment_path);
        Storage::disk('local')->assertExists($oldPath);

        $edit['questions'][0]['remove_file'] = 1;
        $this->actingAs($teacher)->put(route('teacher.quizzes.update', $quiz), $edit)->assertRedirect();
        $this->assertNull($old->fresh()->attachment_path);
        Storage::disk('local')->assertMissing($oldPath);
    }

    public function test_teacher_can_attach_task_file_and_link_without_exposing_file_to_other_class(): void
    {
        Storage::fake('local');
        $teacher = $this->actor('guru', 'task');
        $student = $this->actor('siswa', 'task');
        $outsider = $this->actor('siswa', 'other');
        $class = $this->classroom($teacher);
        $class->students()->attach($student->id);
        $this->actingAs($teacher)->post(route('classroom.tasks.store'), [
            'class_id' => $class->id,
            'title' => 'Tugas PDF',
            'instructions' => 'Baca dokumen dulu.',
            'fpbl_stage' => 1,
            'resource_url' => 'https://www.youtube.com/watch?v=abcdefghijk',
            'attachment' => UploadedFile::fake()->create('panduan.pdf', 30, 'application/pdf'),
        ])->assertRedirect();
        $task = Activity::where('title', 'Tugas PDF')->firstOrFail();
        Storage::disk('local')->assertExists($task->attachment_path);
        $this->actingAs($student)->get(route('classroom.tasks.index'))
            ->assertOk()->assertSee('panduan.pdf')->assertSee('Buka video / tautan tugas');
        $this->actingAs($student)->get(route('classroom.tasks.attachment', $task))->assertOk();
        $this->actingAs($outsider)->get(route('classroom.tasks.attachment', $task))->assertForbidden();

        $oldFile = $task->attachment_path;
        $this->actingAs($teacher)->put(route('classroom.tasks.update', $task), [
            'class_id' => $class->id, 'title' => 'Tugas PDF', 'instructions' => 'Baca dokumen dulu.',
            'fpbl_stage' => 1, 'remove_attachment' => '1',
        ])->assertRedirect();
        $this->assertNull($task->fresh()->attachment_path);
        Storage::disk('local')->assertMissing($oldFile);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizRemedialFlowTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $key): User
    {
        return User::create([
            'name' => ucfirst($role) . ' ' . $key,
            'email' => $role . $key . '@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function room(User $teacher, string $code): SchoolClass
    {
        return SchoolClass::create([
            'name' => 'VII ' . $code,
            'teacher_id' => $teacher->id,
            'join_code' => 'TEST' . $code . '2026',
        ]);
    }

    private function material(User $teacher, SchoolClass $class, string $name, bool $published = true): Material
    {
        return Material::create([
            'author_id' => $teacher->id,
            'class_id' => $class->id,
            'slug' => 'remedial-' . strtolower(str_replace(' ', '-', $name)) . '-' . $class->id,
            'title' => $name,
            'type' => 'modul',
            'is_published' => $published,
        ]);
    }

    private function setupQuiz(User $teacher, SchoolClass $class, Material $material): Assessment
    {
        $this->actingAs($teacher)->post(route('teacher.quizzes.create'), [
            'title' => 'Kuis Penjumlahan',
            'class_id' => $class->id,
            'type' => 'kuis',
            'time_limit_minutes' => 30,
        ])->assertRedirect();

        $quiz = Assessment::where('title', 'Kuis Penjumlahan')->where('class_id', $class->id)->firstOrFail();
        $this->actingAs($teacher)->put(route('teacher.quizzes.update', $quiz), $this->payload($class, $material))
            ->assertRedirect(route('teacher.quizzes'));

        return $quiz->fresh();
    }

    private function payload(SchoolClass $class, Material $material): array
    {
        return [
            'title' => 'Kuis Penjumlahan',
            'class_id' => $class->id,
            'type' => 'kuis',
            'time_limit_minutes' => 30,
            'passing_score' => 75,
            'max_attempts' => 2,
            'material_id' => $material->id,
            'questions' => [[
                'text' => 'Berapakah 2 + 3?',
                'options' => ['4', '5', '6', '7'],
                'correct_answer' => 1,
                'points' => 10,
                'material_id' => $material->id,
                'explanation' => 'Dua ditambah tiga sama dengan lima.',
            ]],
        ];
    }

    public function test_teacher_can_set_passing_score_attempt_limit_and_link_material(): void
    {
        $teacher = $this->user('guru', 'aa');
        $class = $this->room($teacher, 'AA');
        $material = $this->material($teacher, $class, 'Materi Bilangan');

        $quiz = $this->setupQuiz($teacher, $class, $material);
        $this->assertSame(75, (int) $quiz->passing_score);
        $this->assertSame(2, (int) $quiz->max_attempts);
        $this->assertSame($material->id, (int) $quiz->material_id);
        $this->assertSame($material->id, (int) $quiz->questions()->firstOrFail()->material_id);
        $this->actingAs($teacher)->get(route('teacher.quizzes.edit', $quiz))
            ->assertOk()->assertSee('Nilai Ketuntasan')->assertSee('Materi Bilangan');
    }

    public function test_teacher_cannot_attach_draft_or_another_class_material(): void
    {
        $teacher = $this->user('guru', 'bb');
        $class = $this->room($teacher, 'BB');
        $other = $this->room($teacher, 'BC');
        $published = $this->material($teacher, $class, 'Materi Sah');
        $draft = $this->material($teacher, $class, 'Materi Draft', false);
        $outside = $this->material($teacher, $other, 'Materi Lain');
        $this->actingAs($teacher)->post(route('teacher.quizzes.create'), [
            'title' => 'Cek Keamanan', 'class_id' => $class->id, 'type' => 'kuis', 'time_limit_minutes' => 15,
        ])->assertRedirect();
        $quiz = Assessment::where('title', 'Cek Keamanan')->firstOrFail();

        $draftPayload = $this->payload($class, $published);
        $draftPayload['material_id'] = $draft->id;
        $this->actingAs($teacher)->from(route('teacher.quizzes.edit', $quiz))
            ->put(route('teacher.quizzes.update', $quiz), $draftPayload)
            ->assertSessionHasErrors('material_id');

        $outsidePayload = $this->payload($class, $published);
        $outsidePayload['questions'][0]['material_id'] = $outside->id;
        $this->actingAs($teacher)->from(route('teacher.quizzes.edit', $quiz))
            ->put(route('teacher.quizzes.update', $quiz), $outsidePayload)
            ->assertSessionHasErrors('material_id');
        $this->assertFalse((bool) $quiz->fresh()->is_published);
    }

    public function test_wrong_answer_suggests_material_and_second_attempt_can_pass(): void
    {
        $teacher = $this->user('guru', 'cc');
        $student = $this->user('siswa', 'cc');
        $class = $this->room($teacher, 'CC');
        $class->students()->attach($student->id);
        $material = $this->material($teacher, $class, 'Operasi Bilangan');
        $quiz = $this->setupQuiz($teacher, $class, $material);
        $question = $quiz->questions()->firstOrFail();

        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$question->id => 0],
        ])->assertRedirect();
        $failed = AssessmentAttempt::where('user_id', $student->id)->firstOrFail();
        $this->assertSame(0.0, (float) $failed->score);

        $this->actingAs($student)->get(route('siswa.quiz.result', $failed))
            ->assertOk()->assertSee('Rekomendasi Belajar Ulang')
            ->assertSee('Operasi Bilangan')->assertSee('Ulangi Kuis (Remedial)');

        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$question->id => 1],
        ])->assertRedirect();
        $this->assertSame(2, AssessmentAttempt::where('user_id', $student->id)->count());
        $passed = AssessmentAttempt::where('user_id', $student->id)->orderByDesc('id')->firstOrFail();
        $this->assertSame(100.0, (float) $passed->score);
        $this->actingAs($student)->get(route('siswa.quiz.result', $passed))
            ->assertOk()->assertSee('Tuntas');
        $this->actingAs($student)->get(route('classroom.quiz.take', $quiz))
            ->assertRedirect(route('siswa.kuis'));
        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$question->id => 0],
        ])->assertForbidden();
    }

    public function test_results_are_private_but_teacher_can_review_own_students(): void
    {
        $teacher = $this->user('guru', 'dd');
        $otherTeacher = $this->user('guru', 'de');
        $student = $this->user('siswa', 'dd');
        $outsider = $this->user('siswa', 'de');
        $class = $this->room($teacher, 'DD');
        $class->students()->attach($student->id);
        $material = $this->material($teacher, $class, 'Bilangan Bulat');
        $quiz = $this->setupQuiz($teacher, $class, $material);
        $question = $quiz->questions()->firstOrFail();
        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$question->id => 0],
        ])->assertRedirect();
        $attempt = AssessmentAttempt::firstOrFail();

        $this->actingAs($outsider)->get(route('siswa.quiz.result', $attempt))->assertForbidden();
        $this->actingAs($teacher)->get(route('teacher.quizzes.attempt', $attempt))
            ->assertOk()->assertSee('Lembar Jawaban Kuis')->assertSee('Siswa dd');
        $response = $this->actingAs($otherTeacher)->get(route('teacher.quizzes.attempt', $attempt));
        $this->assertContains($response->getStatusCode(), [403, 404]);
    }

    public function test_api_cannot_bypass_a_passing_browser_attempt(): void
    {
        $teacher = $this->user('guru', 'ff');
        $student = $this->user('siswa', 'ff');
        $class = $this->room($teacher, 'FF');
        $class->students()->attach($student->id);
        $material = $this->material($teacher, $class, 'Materi Untuk API');
        $quiz = $this->setupQuiz($teacher, $class, $material);
        $question = $quiz->questions()->firstOrFail();

        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$question->id => 1],
        ])->assertRedirect();
        $this->actingAs($student)->postJson('/api/mathleaf/assessments/' . $quiz->id . '/submit', [
            'answers' => [['question_id' => $question->id, 'answer' => '1']],
        ])->assertStatus(422);
        $this->assertSame(1, AssessmentAttempt::count());
    }

    public function test_old_quiz_keeps_one_attempt_until_edited(): void
    {
        $teacher = $this->user('guru', 'ee');
        $student = $this->user('siswa', 'ee');
        $class = $this->room($teacher, 'EE');
        $class->students()->attach($student->id);
        $quiz = Assessment::create([
            'author_id' => $teacher->id, 'class_id' => $class->id,
            'title' => 'Kuis Lama', 'type' => 'kuis', 'is_published' => true, 'max_attempts' => 1,
        ]);
        $question = $quiz->questions()->create([
            'question_text' => '2 + 2', 'options' => ['3', '4', '5', '6'],
            'correct_answer' => '1', 'points' => 10,
        ]);
        $this->assertSame(70, (int) $quiz->fresh()->passing_score);
        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$question->id => 0],
        ])->assertRedirect();
        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$question->id => 1],
        ])->assertForbidden();
        $this->assertSame(1, AssessmentAttempt::count());
    }
}

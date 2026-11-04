<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use App\Support\QuizSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizScheduleFlowTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role, string $name): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '', $name)).'@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function fixture(): array
    {
        $teacher = $this->account('guru', 'Guru Jadwal');
        $student = $this->account('siswa', 'Siswa Jadwal');
        $class = SchoolClass::create(['name' => 'VII A Matematika', 'teacher_id' => $teacher->id, 'join_code' => 'JADWAL26']);
        $class->students()->attach($student->id);
        $quiz = Assessment::create([
            'author_id' => $teacher->id,
            'class_id' => $class->id,
            'title' => 'Kuis Bilangan',
            'type' => 'kuis',
            'max_attempts' => 3,
            'time_limit_minutes' => 30,
            'is_published' => true,
        ]);
        $question = $quiz->questions()->create([
            'question_text' => '2 + 2 = ?',
            'options' => ['3', '4', '5', '6'],
            'correct_answer' => '1',
            'points' => 10,
            'type' => 'multiple_choice',
        ]);
        return compact('teacher', 'student', 'class', 'quiz', 'question');
    }

    public function test_teacher_can_set_schedule_in_wib_and_student_cannot_start_early(): void
    {
        extract($this->fixture());
        $start = now()->addDay()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i');
        $end = now()->addDays(2)->timezone('Asia/Jakarta')->format('Y-m-d\TH:i');
        $this->actingAs($teacher)->patch(route('teacher.quizzes.schedule', $quiz), [
            'starts_at' => $start, 'due_at' => $end,
        ])->assertRedirect(route('teacher.quizzes'));
        $quiz->refresh();
        $this->assertNotNull($quiz->starts_at);
        $this->assertNotNull($quiz->due_at);
        $this->assertSame($start, $quiz->starts_at->timezone('Asia/Jakarta')->format('Y-m-d\TH:i'));
        $this->actingAs($student)->get(route('classroom.quiz.take', $quiz))->assertForbidden();
        $this->actingAs($student)->get('/api/mathleaf/assessments/'.$quiz->id.'/questions')->assertForbidden();
        $this->actingAs($student)->get(route('siswa.kuis'))->assertDontSee('Kuis Bilangan');
    }

    public function test_deadline_auto_archives_without_deleting_existing_student_scores(): void
    {
        extract($this->fixture());
        $attempt = AssessmentAttempt::create([
            'assessment_id' => $quiz->id, 'user_id' => $student->id, 'attempt_number' => 1,
            'status' => 'graded', 'score' => 68, 'submitted_at' => now()->subDay(),
        ]);
        $quiz->update(['due_at' => now()->subMinute()]);
        $this->actingAs($teacher)->get(route('teacher.quizzes'))->assertOk();
        $quiz->refresh();
        $this->assertNotNull($quiz->archived_at);
        $this->assertFalse((bool) $quiz->is_published);
        $this->assertDatabaseHas('assessment_attempts', ['id' => $attempt->id, 'score' => 68]);
        $this->actingAs($teacher)->get(route('teacher.quizzes', ['arsip' => 1]))->assertSee('Kuis Bilangan');
        $this->actingAs($student)->get(route('siswa.quiz.result', $attempt))->assertOk();
    }

    public function test_browser_and_api_cannot_submit_after_deadline(): void
    {
        extract($this->fixture());
        $quiz->update(['due_at' => now()->subSecond()]);
        $this->actingAs($student)->post(route('classroom.quiz.submit', $quiz), [
            'answers' => [$question->id => 1],
        ])->assertForbidden();
        $this->actingAs($student)->postJson('/api/mathleaf/assessments/'.$quiz->id.'/submit', [
            'answers' => [['question_id' => $question->id, 'answer' => '1']],
        ])->assertForbidden();
        $this->assertSame(0, AssessmentAttempt::count());
    }

    public function test_teacher_can_reschedule_archived_quiz_without_losing_old_results(): void
    {
        extract($this->fixture());
        $quiz->update(['due_at' => now()->subHour(), 'archived_at' => now(), 'is_published' => false]);
        $attempt = AssessmentAttempt::create([
            'assessment_id' => $quiz->id, 'user_id' => $student->id, 'attempt_number' => 1,
            'status' => 'graded', 'score' => 30, 'submitted_at' => now()->subHours(2),
        ]);
        $newDeadline = now()->addDay()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i');
        $this->actingAs($teacher)->patch(route('teacher.quizzes.schedule', $quiz), [
            'starts_at' => '', 'due_at' => $newDeadline,
        ])->assertRedirect(route('teacher.quizzes'));
        $quiz->refresh();
        $this->assertTrue((bool) $quiz->is_published);
        $this->assertNull($quiz->archived_at);
        $this->actingAs($student)->get(route('classroom.quiz.take', $quiz))->assertOk();
        $this->assertDatabaseHas('assessment_attempts', ['id' => $attempt->id, 'score' => 30]);
    }

    public function test_other_teacher_cannot_change_schedule(): void
    {
        extract($this->fixture());
        $foreign = $this->account('guru', 'Guru Lain');
        $this->actingAs($foreign)->patch(route('teacher.quizzes.schedule', $quiz), [
            'due_at' => now()->addDays(2)->timezone('Asia/Jakarta')->format('Y-m-d\TH:i'),
        ])->assertForbidden();
        $this->assertNull($quiz->fresh()->due_at);
    }

    public function test_invalid_schedule_rejected_and_old_quiz_stays_unscheduled(): void
    {
        extract($this->fixture());
        $start = now()->addDays(2)->timezone('Asia/Jakarta')->format('Y-m-d\TH:i');
        $end = now()->addDay()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i');
        $this->actingAs($teacher)->patch(route('teacher.quizzes.schedule', $quiz), [
            'starts_at' => $start, 'due_at' => $end,
        ])->assertSessionHasErrors('due_at');
        $this->assertNull($quiz->fresh()->starts_at);
        $this->assertNull($quiz->fresh()->due_at);
        $this->assertTrue(QuizSchedule::canStart($quiz->fresh()));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizDeleteArchiveFlowTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role, string $suffix): User
    {
        return User::create([
            'name' => ucfirst($role).' '.$suffix,
            'email' => $role.$suffix.'@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function room(User $teacher): SchoolClass
    {
        return SchoolClass::create([
            'name' => 'VII A', 'teacher_id' => $teacher->id, 'join_code' => 'HAPUS123',
        ]);
    }

    private function quiz(User $teacher, SchoolClass $class): Assessment
    {
        return Assessment::create([
            'class_id' => $class->id,
            'author_id' => $teacher->id,
            'title' => 'Kuis Pecahan',
            'type' => 'kuis',
            'is_published' => true,
            'max_attempts' => 2,
        ]);
    }

    public function test_teacher_can_permanently_delete_quiz_without_attempts(): void
    {
        $teacher = $this->account('guru', 'one');
        $class = $this->room($teacher);
        $quiz = $this->quiz($teacher, $class);
        $question = $quiz->questions()->create([
            'question_text' => '1+1?', 'type' => 'multiple_choice',
            'options' => ['1', '2', '3', '4'], 'correct_answer' => '1', 'points' => 10,
        ]);

        $this->actingAs($teacher)->get(route('teacher.quizzes'))
            ->assertOk()->assertSee(route('teacher.quizzes.destroy', $quiz));
        $this->delete(route('teacher.quizzes.destroy', $quiz))->assertRedirect(route('teacher.quizzes'));
        $this->assertDatabaseMissing('assessments', ['id' => $quiz->id]);
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }

    public function test_attempted_quiz_archived_and_student_grades_preserved(): void
    {
        $teacher = $this->account('guru', 'two');
        $student = $this->account('siswa', 'two');
        $class = $this->room($teacher);
        $class->students()->attach($student->id);
        $quiz = $this->quiz($teacher, $class);
        $attempt = AssessmentAttempt::create([
            'assessment_id' => $quiz->id, 'user_id' => $student->id,
            'attempt_number' => 1, 'score' => 95, 'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($teacher)->delete(route('teacher.quizzes.destroy', $quiz))
            ->assertRedirect(route('teacher.quizzes'));
        $this->assertNotNull($quiz->fresh()->archived_at);
        $this->assertFalse((bool) $quiz->fresh()->is_published);
        $this->assertDatabaseHas('assessment_attempts', ['id' => $attempt->id, 'score' => 95]);
        $this->get(route('teacher.quizzes'))->assertDontSee('Kuis Pecahan');
        $this->get(route('teacher.quizzes', ['arsip' => 1]))->assertSee('Kuis Pecahan');
        $this->get(route('teacher.quizzes.attempt', $attempt))->assertOk();
        $this->actingAs($student)->get(route('siswa.nilai'))->assertOk()->assertSee('Kuis Pecahan');
        $this->get(route('classroom.quiz.take', $quiz))->assertForbidden();
        $this->post(route('classroom.quiz.submit', $quiz), ['answers' => [1 => 0]])->assertForbidden();
        $this->get('/api/mathleaf/assessments/'.$quiz->id.'/questions')->assertNotFound();
    }

    public function test_teacher_can_restore_archived_quiz_as_draft(): void
    {
        $teacher = $this->account('guru', 'three');
        $class = $this->room($teacher);
        $quiz = $this->quiz($teacher, $class);
        $quiz->update(['archived_at' => now(), 'is_published' => false]);

        $this->actingAs($teacher)->patch(route('teacher.quizzes.restore', $quiz))
            ->assertRedirect(route('teacher.quizzes'));
        $this->assertNull($quiz->fresh()->archived_at);
        $this->assertFalse((bool) $quiz->fresh()->is_published);
        $this->get(route('teacher.quizzes'))->assertSee('Kuis Pecahan');
    }

    public function test_other_teacher_and_student_cannot_delete_or_restore(): void
    {
        $owner = $this->account('guru', 'four');
        $other = $this->account('guru', 'five');
        $student = $this->account('siswa', 'four');
        $class = $this->room($owner);
        $quiz = $this->quiz($owner, $class);
        $url = route('teacher.quizzes.destroy', $quiz);

        $this->delete($url)->assertRedirect(route('login'));
        $this->actingAs($other)->delete($url)->assertForbidden();
        $this->actingAs($student)->delete($url)->assertForbidden();
        $this->assertDatabaseHas('assessments', ['id' => $quiz->id]);

        $quiz->update(['archived_at' => now()]);
        $this->actingAs($other)->patch(route('teacher.quizzes.restore', $quiz))->assertForbidden();
    }
}

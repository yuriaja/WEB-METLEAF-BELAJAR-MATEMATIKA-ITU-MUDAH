<?php
namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLearningFlowTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role, string $suffix): User
    {
        return User::create([
            'name' => ucfirst($role) . $suffix,
            'email' => $role . $suffix . '@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function quiz(SchoolClass $class, User $teacher, string $title): Assessment
    {
        $quiz = Assessment::create([
            'class_id' => $class->id, 'author_id' => $teacher->id,
            'title' => $title, 'type' => 'kuis',
            'is_published' => true, 'max_attempts' => 1,
        ]);
        $quiz->questions()->create([
            'question_text' => 'Berapakah 4 + 3?',
            'options' => ['5', '6', '7', '8'],
            'correct_answer' => '2', 'points' => 10, 'sort_order' => 0,
        ]);
        return $quiz;
    }

    public function test_student_can_take_quiz_and_see_saved_grade(): void
    {
        $teacher = $this->account('guru', 'one');
        $student = $this->account('siswa', 'one');
        $class = SchoolClass::create([
            'name' => 'VII A', 'teacher_id' => $teacher->id, 'join_code' => 'VIIA0123',
        ]);
        $class->students()->attach($student->id);
        $quiz = $this->quiz($class, $teacher, 'Kuis Bilangan');
        $question = $quiz->questions()->firstOrFail();

        $this->actingAs($student)->get('/kuis')->assertOk()->assertSee('Kuis Bilangan');
        $this->actingAs($student)->get('/kelas/kuis/' . $quiz->id)->assertOk()->assertSee('Berapakah 4 + 3?');
        $this->actingAs($student)->post('/kelas/kuis/' . $quiz->id . '/jawab', [
            'answers' => [$question->id => 2],
        ])->assertRedirect();

        $attempt = AssessmentAttempt::where('user_id', $student->id)->firstOrFail();
        $this->assertEquals(100, (float) $attempt->score);
        $this->actingAs($student)->get('/kuis/hasil/' . $attempt->id)->assertOk()->assertSee('100');
        $this->actingAs($student)->get('/nilai')->assertOk()->assertSee('Kuis Bilangan');
        $this->actingAs($student)->post('/kelas/kuis/' . $quiz->id . '/jawab', [
            'answers' => [$question->id => 1],
        ])->assertForbidden();
        $this->assertSame(1, AssessmentAttempt::count());
    }

    public function test_student_cannot_access_another_class_quiz_or_result(): void
    {
        $teacher = $this->account('guru', 'two');
        $member = $this->account('siswa', 'two');
        $outside = $this->account('siswa', 'three');
        $class = SchoolClass::create([
            'name' => 'VII B', 'teacher_id' => $teacher->id, 'join_code' => 'VIIB0123',
        ]);
        $class->students()->attach($member->id);
        $quiz = $this->quiz($class, $teacher, 'Rahasia Kelas');
        $question = $quiz->questions()->firstOrFail();

        $this->actingAs($outside)->get('/kuis')->assertOk()->assertDontSee('Rahasia Kelas');
        $this->actingAs($outside)->get('/kelas/kuis/' . $quiz->id)->assertForbidden();
        $this->actingAs($outside)->post('/kelas/kuis/' . $quiz->id . '/jawab', [
            'answers' => [$question->id => 2],
        ])->assertForbidden();

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $quiz->id, 'user_id' => $member->id,
            'attempt_number' => 1, 'status' => 'graded',
            'score' => 100, 'submitted_at' => now(),
        ]);
        $this->actingAs($outside)->get('/kuis/hasil/' . $attempt->id)->assertForbidden();
    }

    public function test_teacher_dashboard_has_no_fake_student_activity(): void
    {
        $teacher = $this->account('guru', 'four');
        $this->actingAs($teacher)->get('/guru')->assertOk()
            ->assertSee('Belum ada aktivitas siswa')
            ->assertDontSee('Budi Santoso');
    }
}

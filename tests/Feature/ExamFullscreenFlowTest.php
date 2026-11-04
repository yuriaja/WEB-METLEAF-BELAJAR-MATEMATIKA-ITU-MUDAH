<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamFullscreenFlowTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role, string $suffix): User
    {
        return User::create([
            'name' => ucfirst($role).$suffix,
            'email' => $role.$suffix.'@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function exam(User $teacher, SchoolClass $class, string $type = 'ujian'): Assessment
    {
        $exam = Assessment::create([
            'author_id' => $teacher->id, 'class_id' => $class->id,
            'title' => 'Ujian Bilangan', 'type' => $type,
            'is_published' => true, 'max_attempts' => 1,
        ]);
        $exam->questions()->create([
            'question_text' => 'Berapa 2 + 2?', 'options' => ['3','4','5','6'],
            'correct_answer' => '1', 'points' => 10, 'sort_order' => 0,
        ]);
        return $exam;
    }

    public function test_exam_requires_fullscreen_click_but_normal_quiz_does_not(): void
    {
        $teacher = $this->account('guru', 'one');
        $student = $this->account('siswa', 'one');
        $class = SchoolClass::create(['name' => 'VII A', 'teacher_id' => $teacher->id, 'join_code' => 'EXAM0001']);
        $class->students()->attach($student->id);
        $exam = $this->exam($teacher, $class);
        $this->actingAs($student)->get(route('classroom.quiz.take', $exam))
            ->assertOk()->assertSee('ml-exam-start')->assertSee('requestFullscreen');
        $normal = $this->exam($teacher, $class, 'kuis');
        $this->get(route('classroom.quiz.take', $normal))
            ->assertOk()->assertDontSee('id="ml-exam-start"', false);
    }

    public function test_student_incidents_are_saved_and_teacher_can_review_them(): void
    {
        $teacher = $this->account('guru', 'two');
        $student = $this->account('siswa', 'two');
        $class = SchoolClass::create(['name' => 'VII B', 'teacher_id' => $teacher->id, 'join_code' => 'EXAM0002']);
        $class->students()->attach($student->id);
        $exam = $this->exam($teacher, $class);
        $this->actingAs($student)->post(route('classroom.quiz.incident', $exam), ['event_type' => 'tab_hidden'])
            ->assertStatus(202);
        $this->assertDatabaseHas('exam_incidents', [
            'assessment_id' => $exam->id, 'user_id' => $student->id, 'event_type' => 'tab_hidden',
        ]);
        $this->actingAs($teacher)->get(route('teacher.quizzes.incidents', $exam))
            ->assertOk()->assertSee('Beralih tab')->assertSee($student->name);
    }

    public function test_incidents_do_not_change_grades_and_duplicate_events_are_throttled(): void
    {
        $teacher = $this->account('guru', 'three');
        $student = $this->account('siswa', 'three');
        $class = SchoolClass::create(['name' => 'VII C', 'teacher_id' => $teacher->id, 'join_code' => 'EXAM0003']);
        $class->students()->attach($student->id);
        $exam = $this->exam($teacher, $class);
        $url = route('classroom.quiz.incident', $exam);
        $this->actingAs($student)->post($url, ['event_type' => 'fullscreen_exit'])->assertStatus(202);
        $this->post($url, ['event_type' => 'fullscreen_exit'])->assertStatus(202);
        $this->assertDatabaseCount('exam_incidents', 1);
        $this->assertDatabaseCount('assessment_attempts', 0);
    }

    public function test_other_students_and_teachers_cannot_access_exam_logs(): void
    {
        $teacher = $this->account('guru', 'four');
        $teacher2 = $this->account('guru', 'five');
        $student = $this->account('siswa', 'four');
        $outside = $this->account('siswa', 'five');
        $class = SchoolClass::create(['name' => 'VII D', 'teacher_id' => $teacher->id, 'join_code' => 'EXAM0004']);
        $class->students()->attach($student->id);
        $exam = $this->exam($teacher, $class);
        $this->actingAs($outside)->post(route('classroom.quiz.incident', $exam), ['event_type' => 'tab_hidden'])->assertForbidden();
        $this->actingAs($teacher2)->get(route('teacher.quizzes.incidents', $exam))->assertForbidden();
        $this->actingAs($student)->get(route('teacher.quizzes.incidents', $exam))->assertForbidden();
    }
}

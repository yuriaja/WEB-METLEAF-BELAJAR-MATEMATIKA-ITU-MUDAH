<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\ForumComment;
use App\Models\ForumThread;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInsightsFlowTest extends TestCase
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

    private function classroom(User $teacher, string $suffix): SchoolClass
    {
        return SchoolClass::create([
            'name' => 'VII '.$suffix,
            'teacher_id' => $teacher->id,
            'join_code' => 'DASH'.$suffix,
        ]);
    }

    private function material(SchoolClass $class, User $teacher, string $title, bool $published = true): Material
    {
        return Material::create([
            'class_id' => $class->id,
            'author_id' => $teacher->id,
            'title' => $title,
            'slug' => 'dash-'.strtolower($class->join_code).'-'.strtolower(str_replace(' ', '-', $title)),
            'type' => 'link',
            'resource_url' => 'https://example.org/materi',
            'is_published' => $published,
        ]);
    }

    private function task(SchoolClass $class, string $title): Activity
    {
        return Activity::create([
            'class_id' => $class->id,
            'title' => $title,
            'mode' => 'tugas',
            'fpbl_stage' => 1,
            'due_at' => now()->addDays(2),
        ]);
    }

    private function quiz(SchoolClass $class, User $teacher, string $title): Assessment
    {
        $quiz = Assessment::create([
            'class_id' => $class->id,
            'author_id' => $teacher->id,
            'title' => $title,
            'type' => 'kuis',
            'is_published' => true,
            'passing_score' => 70,
            'max_attempts' => 2,
        ]);
        $quiz->questions()->create([
            'question_text' => 'Hitung 2 + 2', 'options' => ['3', '4', '5', '6'],
            'correct_answer' => '1', 'points' => 10, 'sort_order' => 0,
        ]);
        return $quiz;
    }

    public function test_guest_redirected_and_student_teacher_dashboard_roles_are_separated(): void
    {
        $this->get('/dashboard')->assertRedirect('/masuk');
        $this->get('/guru')->assertRedirect('/masuk');
        $teacher = $this->account('guru', 'roles');
        $student = $this->account('siswa', 'roles');
        $this->actingAs($teacher)->get('/dashboard')->assertForbidden();
        $this->actingAs($student)->get('/guru')->assertForbidden();
    }

    public function test_student_dashboard_empty_state_does_not_display_fake_activity(): void
    {
        $student = $this->account('siswa', 'empty');
        $this->actingAs($student)->get('/dashboard')
            ->assertOk()
            ->assertSee('Belum ada kelas yang diikuti')
            ->assertSee('0%')
            ->assertDontSee('Guru Budi')
            ->assertDontSee('Pecahan Lanjutan');
    }

    public function test_student_dashboard_uses_real_scoped_material_tasks_quiz_and_scores(): void
    {
        $teacher = $this->account('guru', 'scoped');
        $student = $this->account('siswa', 'scoped');
        $a = $this->classroom($teacher, 'A1');
        $b = $this->classroom($teacher, 'B1');
        $a->students()->attach($student->id);
        $visibleMaterial = $this->material($a, $teacher, 'Materi Pecahan');
        $this->material($a, $teacher, 'Draft Rahasia', false);
        $this->material($b, $teacher, 'Materi Kelas Lain');
        MaterialProgress::create([
            'user_id' => $student->id, 'material_id' => $visibleMaterial->id,
            'progress_percent' => 50,
        ]);
        $done = $this->task($a, 'Tugas Sudah Masuk');
        $this->task($a, 'Tugas Menunggu');
        $this->task($b, 'Tugas Rahasia Kelas Lain');
        Submission::create([
            'activity_id' => $done->id, 'user_id' => $student->id,
            'content' => 'Jawaban', 'submitted_at' => now(), 'score' => 80,
        ]);
        $quiz = $this->quiz($a, $teacher, 'Kuis Remedial Pecahan');
        AssessmentAttempt::create([
            'assessment_id' => $quiz->id, 'user_id' => $student->id,
            'attempt_number' => 1, 'status' => 'graded',
            'score' => 60, 'submitted_at' => now(),
        ]);
        $view = $this->actingAs($student)->get('/dashboard');
        $view->assertOk()->assertSee('Materi Pecahan')->assertSee('Tugas Menunggu')
            ->assertSee('Kuis Remedial Pecahan')->assertDontSee('Draft Rahasia')
            ->assertDontSee('Materi Kelas Lain')->assertDontSee('Tugas Rahasia Kelas Lain')
            ->assertViewHas('materialProgress', 50)
            ->assertViewHas('submittedCount', 1)
            ->assertViewHas('pendingCount', 1)
            ->assertViewHas('averageScore', 70.0);
    }

    public function test_active_class_filters_student_dashboard_without_leaking_other_joined_class(): void
    {
        $teacher = $this->account('guru', 'active');
        $student = $this->account('siswa', 'active');
        $a = $this->classroom($teacher, 'A2');
        $b = $this->classroom($teacher, 'B2');
        $a->students()->attach($student->id);
        $b->students()->attach($student->id);
        $this->material($a, $teacher, 'Konten Kelas Aktif');
        $this->material($b, $teacher, 'Konten Kelas Tidak Aktif');
        $this->actingAs($student)->withSession(['kelas_aktif_id' => $a->id])
            ->get('/dashboard')->assertOk()
            ->assertSee('Konten Kelas Aktif')
            ->assertDontSee('Konten Kelas Tidak Aktif');
    }

    public function test_teacher_dashboard_counts_only_owned_classes_and_real_metrics(): void
    {
        $teacher = $this->account('guru', 'owner');
        $other = $this->account('guru', 'other');
        $student = $this->account('siswa', 'owner');
        $a = $this->classroom($teacher, 'A3');
        $b = $this->classroom($other, 'B3');
        $a->students()->attach($student->id);
        $this->material($a, $teacher, 'Materi Guru Kita');
        $this->material($b, $other, 'Materi Guru Lain');
        $quiz = $this->quiz($a, $teacher, 'Kuis Guru Kita');
        $this->quiz($b, $other, 'Kuis Guru Lain');
        AssessmentAttempt::create([
            'assessment_id' => $quiz->id, 'user_id' => $student->id,
            'attempt_number' => 1, 'score' => 90,
            'status' => 'graded', 'submitted_at' => now(),
        ]);
        $task = $this->task($a, 'Tugas Punya Kita');
        $this->task($b, 'Tugas Milik Orang Lain');
        Submission::create([
            'activity_id' => $task->id, 'user_id' => $student->id,
            'submitted_at' => now(), 'content' => 'Jawaban tugas',
        ]);
        $this->actingAs($teacher)->get('/guru')
            ->assertOk()->assertSee('Tugas Punya Kita')
            ->assertDontSee('Tugas Milik Orang Lain')
            ->assertViewHas('publishedMaterialCount', 1)
            ->assertViewHas('publishedQuizCount', 1)
            ->assertViewHas('totalTasks', 1)
            ->assertViewHas('uniqueStudentCount', 1)
            ->assertViewHas('pendingTasks', 1);
    }

    public function test_teacher_dashboard_shows_student_forum_topics_and_replies_from_owned_classes(): void
    {
        $teacher = $this->account('guru', 'forum-activity');
        $student = $this->account('siswa', 'forum-activity');
        $otherTeacher = $this->account('guru', 'other-forum-activity');
        $class = $this->classroom($teacher, 'FORUM');
        $otherClass = $this->classroom($otherTeacher, 'OTHERFORUM');
        $class->students()->attach($student->id);

        $studentTopic = ForumThread::create([
            'class_id' => $class->id,
            'user_id' => $student->id,
            'title' => 'Pertanyaan siswa terbaru',
            'body' => 'Bagaimana menyelesaikan soal ini?',
        ]);
        $teacherTopic = ForumThread::create([
            'class_id' => $class->id,
            'user_id' => $teacher->id,
            'title' => 'Topik guru',
            'body' => 'Bahan diskusi.',
        ]);
        $studentReply = ForumComment::create([
            'forum_thread_id' => $teacherTopic->id,
            'user_id' => $student->id,
            'body' => 'Ini balasan siswa.',
        ]);
        $otherClassTopic = ForumThread::create([
            'class_id' => $otherClass->id,
            'user_id' => $student->id,
            'title' => 'Diskusi kelas lain',
            'body' => 'Tidak boleh muncul.',
        ]);
        ForumComment::create([
            'forum_thread_id' => $otherClassTopic->id,
            'user_id' => $student->id,
            'body' => 'Balasan di kelas lain.',
        ]);

        $this->actingAs($teacher)->get('/guru')
            ->assertOk()
            ->assertSee('membuat topik diskusi')
            ->assertSee('Pertanyaan siswa terbaru')
            ->assertSee('membalas diskusi')
            ->assertSee($student->name)
            ->assertDontSee('Diskusi kelas lain');

        $this->assertStringContainsString(
            'class_id='.$class->id.'&amp;thread='.$studentTopic->id,
            $this->get(route('teacher.dashboard'))->getContent()
        );
        $this->assertStringContainsString(
            'class_id='.$class->id.'&amp;thread='.$studentReply->forum_thread_id,
            $this->get(route('teacher.dashboard'))->getContent()
        );
    }
}

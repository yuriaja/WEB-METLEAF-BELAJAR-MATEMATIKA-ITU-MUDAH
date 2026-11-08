<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\ForumComment;
use App\Models\ForumThread;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_gets_recent_tasks_grades_forum_topics_and_quizzes_from_their_classes(): void
    {
        $teacher = $this->user('guru', 'student-notifications');
        $student = $this->user('siswa', 'student-notifications');
        $class = $this->schoolClass($teacher, 'SN101');
        $class->students()->attach($student->id);

        $task = Activity::create([
            'class_id' => $class->id,
            'title' => 'Tugas terbaru',
            'instructions' => 'Kerjakan.',
            'fpbl_stage' => 1,
            'mode' => 'tugas',
        ]);
        Submission::create([
            'activity_id' => $task->id,
            'user_id' => $student->id,
            'content' => 'Jawaban siswa',
            'score' => 90,
            'submitted_at' => now()->subDay(),
        ]);
        ForumThread::create([
            'class_id' => $class->id,
            'user_id' => $teacher->id,
            'title' => 'Diskusi terbaru',
            'body' => 'Mari berdiskusi.',
        ]);
        Assessment::create([
            'class_id' => $class->id,
            'author_id' => $teacher->id,
            'title' => 'Kuis terbaru',
            'type' => 'kuis',
            'is_published' => true,
        ]);

        $this->actingAs($student)->getJson('/notifikasi')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Tugas baru: Tugas terbaru'])
            ->assertJsonFragment(['id' => 'task:'.$task->id])
            ->assertJsonFragment(['title' => 'Tugas dinilai: Tugas terbaru'])
            ->assertJsonFragment(['title' => 'Topik forum baru: Diskusi terbaru'])
            ->assertJsonFragment(['title' => 'Kuis baru: Kuis terbaru']);
    }

    public function test_teacher_gets_student_submissions_forum_topics_and_quiz_attempts(): void
    {
        $teacher = $this->user('guru', 'teacher-notifications');
        $student = $this->user('siswa', 'teacher-notifications');
        $class = $this->schoolClass($teacher, 'TN202');
        $class->students()->attach($student->id);

        $task = Activity::create([
            'class_id' => $class->id,
            'title' => 'Tugas siswa',
            'instructions' => 'Kerjakan.',
            'fpbl_stage' => 2,
            'mode' => 'tugas',
        ]);
        $submission = Submission::create([
            'activity_id' => $task->id,
            'user_id' => $student->id,
            'content' => 'Jawaban siswa',
            'submitted_at' => now(),
        ]);
        ForumThread::create([
            'class_id' => $class->id,
            'user_id' => $student->id,
            'title' => 'Pertanyaan siswa',
            'body' => 'Saya ingin bertanya.',
        ]);
        $assessment = Assessment::create([
            'class_id' => $class->id,
            'author_id' => $teacher->id,
            'title' => 'Kuis kelas',
            'type' => 'kuis',
            'is_published' => true,
        ]);
        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $student->id,
            'attempt_number' => 1,
            'score' => 85,
            'status' => 'graded',
            'submitted_at' => now(),
        ]);

        $this->actingAs($teacher)->getJson('/notifikasi')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Siswa teacher-notifications mengumpulkan tugas'])
            ->assertJsonFragment(['id' => 'submission:'.$submission->id])
            ->assertJsonFragment(['title' => 'Topik forum baru: Pertanyaan siswa'])
            ->assertJsonFragment(['title' => 'Siswa teacher-notifications menyelesaikan kuis']);
    }

    public function test_student_gets_replies_to_their_forum_topic_but_not_their_own_replies(): void
    {
        $teacher = $this->user('guru', 'forum-reply-student');
        $student = $this->user('siswa', 'forum-reply-student');
        $class = $this->schoolClass($teacher, 'FRS101');
        $class->students()->attach($student->id);
        $thread = ForumThread::create([
            'class_id' => $class->id,
            'user_id' => $student->id,
            'title' => 'Pertanyaan siswa',
            'body' => 'Bagaimana caranya?',
        ]);
        $reply = ForumComment::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $teacher->id,
            'body' => 'Ini jawaban guru.',
        ]);
        ForumComment::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $student->id,
            'body' => 'Terima kasih.',
        ]);

        $response = $this->actingAs($student)->getJson('/notifikasi')->assertOk();

        $response->assertJsonFragment(['id' => 'forum-comment:'.$reply->id])
            ->assertJsonFragment(['title' => 'Balasan baru: Pertanyaan siswa'])
            ->assertJsonFragment(['description' => 'Guru forum-reply-student · Kelas FRS101 · Ini jawaban guru.']);
        $this->assertSame(1, collect($response->json())->where('title', 'Balasan baru: Pertanyaan siswa')->count());
    }

    public function test_teacher_gets_new_forum_replies_in_their_class(): void
    {
        $teacher = $this->user('guru', 'forum-reply-teacher');
        $student = $this->user('siswa', 'forum-reply-teacher');
        $class = $this->schoolClass($teacher, 'FRT202');
        $class->students()->attach($student->id);
        $thread = ForumThread::create([
            'class_id' => $class->id,
            'user_id' => $student->id,
            'title' => 'Diskusi kelas',
            'body' => 'Mari diskusi.',
        ]);
        $reply = ForumComment::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $student->id,
            'body' => 'Jawaban siswa.',
        ]);

        $this->actingAs($teacher)->getJson('/notifikasi')
            ->assertOk()
            ->assertJsonFragment(['id' => 'forum-comment:'.$reply->id])
            ->assertJsonFragment(['title' => 'Balasan baru: Diskusi kelas'])
            ->assertJsonFragment(['url' => route('forum.index', ['class_id' => $class->id, 'thread' => $thread->id])]);
    }

    public function test_student_gets_recent_published_materials_only_from_their_classes(): void
    {
        $teacher = $this->user('guru', 'material-notifications');
        $student = $this->user('siswa', 'material-notifications');
        $class = $this->schoolClass($teacher, 'MAT101');
        $otherClass = $this->schoolClass($teacher, 'MAT202');
        $class->students()->attach($student->id);

        $material = Material::create([
            'class_id' => $class->id,
            'author_id' => $teacher->id,
            'title' => 'Materi pecahan terbaru',
            'slug' => 'materi-pecahan-terbaru',
            'type' => 'dokumen',
            'is_published' => true,
        ]);
        Material::create([
            'class_id' => $class->id,
            'author_id' => $teacher->id,
            'title' => 'Draf materi',
            'slug' => 'draf-materi',
            'type' => 'dokumen',
            'is_published' => false,
        ]);
        Material::create([
            'class_id' => $otherClass->id,
            'author_id' => $teacher->id,
            'title' => 'Materi kelas lain',
            'slug' => 'materi-kelas-lain',
            'type' => 'dokumen',
            'is_published' => true,
        ]);

        $this->actingAs($student)->getJson('/notifikasi')
            ->assertOk()
            ->assertJsonFragment(['id' => 'material:'.$material->id])
            ->assertJsonFragment(['title' => 'Materi baru: Materi pecahan terbaru'])
            ->assertJsonFragment(['url' => route('materi.show', ['material' => $material->id])])
            ->assertJsonMissing(['title' => 'Materi baru: Draf materi'])
            ->assertJsonMissing(['title' => 'Materi baru: Materi kelas lain']);
    }

    private function user(string $role, string $suffix): User
    {
        return User::create([
            'name' => ucfirst($role).' '.$suffix,
            'email' => $role.'-'.$suffix.'@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function schoolClass(User $teacher, string $code): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Kelas '.$code,
            'join_code' => $code,
        ]);
    }
}

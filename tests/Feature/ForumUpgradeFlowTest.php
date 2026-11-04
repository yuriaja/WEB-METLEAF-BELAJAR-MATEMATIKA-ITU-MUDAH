<?php

namespace Tests\Feature;

use App\Models\ForumComment;
use App\Models\ForumThread;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumUpgradeFlowTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $name): User
    {
        return User::create([
            'name' => ucfirst($role).' '.$name,
            'email' => strtolower($role.$name).'@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function classroom(User $teacher, string $code): SchoolClass
    {
        return SchoolClass::create([
            'name' => 'Kelas '.$code,
            'join_code' => $code,
            'teacher_id' => $teacher->id,
        ]);
    }

    private function thread(SchoolClass $class, User $author, string $title): ForumThread
    {
        return ForumThread::create([
            'class_id' => $class->id,
            'user_id' => $author->id,
            'title' => $title,
            'body' => 'Mari diskusikan materi ini.',
        ]);
    }

    public function test_teacher_can_pin_lock_and_unlock_own_class_topic(): void
    {
        $teacher = $this->user('guru', 'pin');
        $student = $this->user('siswa', 'pin');
        $class = $this->classroom($teacher, 'PIN1');
        $class->students()->attach($student->id);
        $thread = $this->thread($class, $student, 'Topik Utama Guru');

        $this->actingAs($teacher)->patch(route('forum.threads.pin', $thread))->assertRedirect();
        $this->assertTrue($thread->fresh()->is_pinned);
        $this->actingAs($teacher)->patch(route('forum.threads.lock', $thread))->assertRedirect();
        $this->assertTrue($thread->fresh()->is_locked);

        $this->actingAs($student)->post(route('forum.comments.store', $thread), [
            'body' => 'Dilarang setelah dikunci',
        ])->assertForbidden();
        $this->actingAs($student)->postJson('/api/mathleaf/threads/'.$thread->id.'/comments', [
            'body' => 'Mencoba melewati aturan dari API',
        ])->assertForbidden();

        $this->actingAs($teacher)->patch(route('forum.threads.lock', $thread))->assertRedirect();
        $this->assertFalse($thread->fresh()->is_locked);
        $this->actingAs($student)->post(route('forum.comments.store', $thread), [
            'body' => 'Sudah terbuka lagi',
        ])->assertRedirect();
        $this->assertDatabaseHas('forum_comments', ['forum_thread_id' => $thread->id, 'body' => 'Sudah terbuka lagi']);
    }

    public function test_other_teacher_or_student_cannot_moderate_foreign_class(): void
    {
        $teacher = $this->user('guru', 'own');
        $otherTeacher = $this->user('guru', 'other');
        $student = $this->user('siswa', 'visitor');
        $class = $this->classroom($teacher, 'OWN1');
        $class->students()->attach($student->id);
        $thread = $this->thread($class, $teacher, 'Rahasia Kelas');

        $this->actingAs($otherTeacher)->patch(route('forum.threads.pin', $thread))->assertForbidden();
        $this->actingAs($otherTeacher)->patch(route('forum.threads.lock', $thread))->assertForbidden();
        $this->actingAs($student)->patch(route('forum.threads.pin', $thread))->assertForbidden();
        $this->actingAs($student)->delete(route('forum.threads.destroy', $thread))->assertForbidden();
        $this->assertFalse($thread->fresh()->is_pinned);
    }

    public function test_thread_author_can_edit_but_cannot_delete_a_thread_with_replies(): void
    {
        $teacher = $this->user('guru', 'edit');
        $student = $this->user('siswa', 'edit');
        $otherStudent = $this->user('siswa', 'editother');
        $class = $this->classroom($teacher, 'EDIT1');
        $class->students()->attach([$student->id, $otherStudent->id]);
        $thread = $this->thread($class, $student, 'Judul sebelum diubah');

        $this->actingAs($otherStudent)->put(route('forum.threads.update', $thread), [
            'title' => 'Milik orang lain', 'body' => 'Ini tidak boleh.',
        ])->assertForbidden();
        $this->actingAs($student)->put(route('forum.threads.update', $thread), [
            'title' => 'Judul sesudah diubah', 'body' => 'Diskusi yang sudah diperbarui.',
        ])->assertRedirect();
        $this->assertDatabaseHas('forum_threads', ['id' => $thread->id, 'title' => 'Judul sesudah diubah']);

        $reply = ForumComment::create([
            'forum_thread_id' => $thread->id, 'user_id' => $teacher->id, 'body' => 'Jawaban guru.',
        ]);
        $this->actingAs($student)->delete(route('forum.threads.destroy', $thread))->assertForbidden();
        $this->assertDatabaseHas('forum_comments', ['id' => $reply->id]);
        $this->actingAs($teacher)->delete(route('forum.threads.destroy', $thread))->assertRedirect();
        $this->assertDatabaseMissing('forum_threads', ['id' => $thread->id]);
    }

    public function test_comment_deletion_is_author_or_moderator_only(): void
    {
        $teacher = $this->user('guru', 'comment');
        $student = $this->user('siswa', 'comment');
        $stranger = $this->user('siswa', 'stranger');
        $class = $this->classroom($teacher, 'COM1');
        $class->students()->attach($student->id);
        $thread = $this->thread($class, $teacher, 'Komentar kelas');
        $comment = ForumComment::create([
            'forum_thread_id' => $thread->id, 'user_id' => $student->id, 'body' => 'Jawaban siswa.',
        ]);
        $this->actingAs($stranger)->delete(route('forum.comments.destroy', $comment))->assertForbidden();
        $this->actingAs($student)->delete(route('forum.comments.destroy', $comment))->assertRedirect();
        $this->assertDatabaseMissing('forum_comments', ['id' => $comment->id]);

        $comment = ForumComment::create([
            'forum_thread_id' => $thread->id, 'user_id' => $student->id, 'body' => 'Balasan yang dikunci.',
        ]);
        $thread->update(['is_locked' => true]);
        $this->actingAs($student)->delete(route('forum.comments.destroy', $comment))->assertForbidden();
        $this->actingAs($teacher)->delete(route('forum.comments.destroy', $comment))->assertRedirect();
        $this->assertDatabaseMissing('forum_comments', ['id' => $comment->id]);
    }

    public function test_search_and_filters_do_not_leak_threads_from_other_classes(): void
    {
        $teacher = $this->user('guru', 'search');
        $otherTeacher = $this->user('guru', 'secret');
        $student = $this->user('siswa', 'search');
        $class = $this->classroom($teacher, 'SEAR1');
        $foreignClass = $this->classroom($otherTeacher, 'SEAR2');
        $class->students()->attach($student->id);
        $unanswered = $this->thread($class, $student, 'Pecahan Campuran Unik');
        $answered = $this->thread($class, $teacher, 'Operasi Pecahan Lain');
        ForumComment::create([
            'forum_thread_id' => $answered->id, 'user_id' => $teacher->id, 'body' => 'Terjawab.',
        ]);
        $this->thread($foreignClass, $otherTeacher, 'Pecahan Rahasia Kelas Lain');

        $this->actingAs($student)->get('/forum?q=Pecahan')->assertOk()
            ->assertSee('Pecahan Campuran Unik')->assertSee('Operasi Pecahan Lain')
            ->assertDontSee('Pecahan Rahasia Kelas Lain');
        $this->actingAs($student)->get('/forum?mode=mine')->assertOk()
            ->assertSee('Pecahan Campuran Unik')->assertDontSee('Operasi Pecahan Lain');
        $this->actingAs($student)->get('/forum?mode=unanswered')->assertOk()
            ->assertSee('Pecahan Campuran Unik')->assertDontSee('Operasi Pecahan Lain');
        $this->actingAs($student)->get('/forum?class_id='.$foreignClass->id)->assertForbidden();
        $this->actingAs($teacher)->patch(route('forum.threads.pin', $unanswered))->assertRedirect();
        $this->actingAs($student)->get('/forum')->assertOk()->assertSeeInOrder([
            'Pecahan Campuran Unik', 'Operasi Pecahan Lain',
        ]);
    }

    public function test_student_dashboard_only_lists_forum_threads_from_active_class(): void
    {
        $teacher = $this->user('guru', 'dashboard');
        $otherTeacher = $this->user('guru', 'dashboard2');
        $student = $this->user('siswa', 'dashboard');
        $classA = $this->classroom($teacher, 'DAS1');
        $classB = $this->classroom($otherTeacher, 'DAS2');
        $classA->students()->attach($student->id);
        $classB->students()->attach($student->id);
        $this->thread($classA, $teacher, 'Forum Kelas A Saja');
        $this->thread($classB, $otherTeacher, 'Forum Kelas B Saja');

        $this->actingAs($student)->withSession(['kelas_aktif_id' => $classA->id])
            ->get('/dashboard')->assertOk()
            ->assertSee('Forum Kelas A Saja')->assertDontSee('Forum Kelas B Saja');
        $this->actingAs($student)->withSession(['kelas_aktif_id' => $classB->id])
            ->get('/dashboard')->assertOk()
            ->assertSee('Forum Kelas B Saja')->assertDontSee('Forum Kelas A Saja');
    }
}

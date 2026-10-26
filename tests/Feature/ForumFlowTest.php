<?php

namespace Tests\Feature;

use App\Models\ForumThread;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role, string $suffix): User
    {
        return User::create([
            'name' => ucfirst($role) . ' ' . $suffix,
            'email' => $role . $suffix . '@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function createClass(User $teacher, string $code): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Kelas ' . $code,
            'join_code' => $code,
        ]);
    }

    public function test_teacher_and_member_student_can_post_and_reply(): void
    {
        $teacher = $this->createUser('guru', 'a');
        $student = $this->createUser('siswa', 'a');
        $class = $this->createClass($teacher, 'A101');
        $class->students()->attach($student->id);

        $this->actingAs($teacher)->post('/forum/topik', [
            'class_id' => $class->id,
            'title' => 'Penjumlahan Pecahan',
            'body' => 'Bagaimana cara menjumlahkan dua pecahan?',
        ])->assertRedirect();

        $thread = ForumThread::query()->firstOrFail();
        $this->assertSame($teacher->id, $thread->user_id);
        $this->assertSame($class->id, $thread->class_id);

        $this->actingAs($student)->get('/forum')->assertOk()->assertSee('Penjumlahan Pecahan');
        $this->actingAs($student)->post('/forum/topik/' . $thread->id . '/balas', [
            'body' => 'Cari KPK dari penyebutnya, Bu.',
        ])->assertRedirect();

        $this->assertDatabaseHas('forum_comments', [
            'forum_thread_id' => $thread->id,
            'user_id' => $student->id,
            'body' => 'Cari KPK dari penyebutnya, Bu.',
        ]);
    }

    public function test_student_cannot_see_or_change_another_class_forum(): void
    {
        $teacher = $this->createUser('guru', 'b');
        $student = $this->createUser('siswa', 'b');
        $stranger = $this->createUser('siswa', 'c');
        $class = $this->createClass($teacher, 'B202');
        $class->students()->attach($student->id);
        $thread = ForumThread::create([
            'class_id' => $class->id,
            'user_id' => $teacher->id,
            'title' => 'Forum Rahasia Kelas',
            'body' => 'Diskusi khusus kelas ini.',
        ]);

        $this->actingAs($stranger)->get('/forum')->assertOk()->assertDontSee('Forum Rahasia Kelas');
        $this->actingAs($stranger)->get('/forum?class_id=' . $class->id)->assertForbidden();
        $this->actingAs($stranger)->post('/forum/topik', [
            'class_id' => $class->id,
            'title' => 'Tidak boleh',
            'body' => 'Mencoba mengakses kelas lain.',
        ])->assertForbidden();
        $this->actingAs($stranger)->post('/forum/topik/' . $thread->id . '/balas', [
            'body' => 'Tidak boleh komentar di sini.',
        ])->assertForbidden();
        $this->actingAs($stranger)->getJson('/api/mathleaf/threads')
            ->assertOk()->assertExactJson([]);
    }
}

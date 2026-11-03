<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClassTaskFlowTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $suffix): User
    {
        return User::create([
            'name' => ucfirst($role) . $suffix,
            'email' => $role . $suffix . '@example.test',
            'password' => 'secret123',
            'role' => $role,
        ]);
    }

    private function schoolClass(User $teacher, string $code): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'VII ' . $code,
            'join_code' => $code,
        ]);
    }

    private function task(SchoolClass $class, string $title = 'Investigasi Pecahan'): Activity
    {
        return Activity::create([
            'class_id' => $class->id,
            'title' => $title,
            'instructions' => 'Kerjakan dengan teliti.',
            'fpbl_stage' => 2,
            'mode' => 'tugas',
            'starts_at' => now(),
            'due_at' => now()->addDay(),
        ]);
    }

    public function test_teacher_can_create_edit_and_delete_an_unsubmitted_task(): void
    {
        $teacher = $this->user('guru', 'crud');
        $class = $this->schoolClass($teacher, 'A111');
        $this->actingAs($teacher)->post('/kelas/tugas', [
            'class_id' => $class->id,
            'title' => 'Bilangan Bulat',
            'instructions' => 'Kerjakan nomor 1 sampai 5.',
            'fpbl_stage' => 1,
        ])->assertRedirect();

        $task = Activity::query()->where('title', 'Bilangan Bulat')->firstOrFail();
        $this->actingAs($teacher)->get('/kelas/tugas/' . $task->id . '/edit')->assertOk();
        $this->actingAs($teacher)->put('/kelas/tugas/' . $task->id, [
            'class_id' => $class->id,
            'title' => 'Bilangan Bulat Revisi',
            'instructions' => 'Kerjakan nomor 1 sampai 10.',
            'fpbl_stage' => 3,
        ])->assertRedirect();
        $this->assertDatabaseHas('activities', ['id' => $task->id, 'fpbl_stage' => 3, 'title' => 'Bilangan Bulat Revisi']);
        $this->actingAs($teacher)->delete('/kelas/tugas/' . $task->id)->assertRedirect();
        $this->assertDatabaseMissing('activities', ['id' => $task->id]);
    }

    public function test_student_sees_only_enrolled_class_tasks_and_real_submission_form(): void
    {
        $teacher = $this->user('guru', 'filter');
        $student = $this->user('siswa', 'filter');
        $class = $this->schoolClass($teacher, 'B222');
        $other = $this->schoolClass($teacher, 'C333');
        $class->students()->attach($student->id);
        $task = $this->task($class, 'Tugas Kelas Saya');
        $this->task($other, 'Tugas Kelas Lain');
        $this->actingAs($student)->get('/tugas')->assertRedirect('/kelas/tugas');
        $this->actingAs($student)->get('/kelas/tugas')
            ->assertOk()->assertSee('Tugas Kelas Saya')->assertDontSee('Tugas Kelas Lain')
            ->assertSee('Kumpulkan tugas');
        $this->actingAs($student)->get('/kelas/tugas?class_id=' . $other->id)->assertForbidden();
        $this->actingAs($student)->get('/kelas/tugas/' . $task->id . '/edit')->assertForbidden();
    }

    public function test_student_can_submit_file_and_receive_grade_and_cannot_change_graded_submission(): void
    {
        Storage::fake('local');
        $teacher = $this->user('guru', 'grade');
        $student = $this->user('siswa', 'grade');
        $class = $this->schoolClass($teacher, 'D444');
        $class->students()->attach($student->id);
        $task = $this->task($class);

        $this->actingAs($student)->post('/kelas/tugas/' . $task->id . '/kumpul', [
            'content' => 'Ini jawabanku.',
            'file' => UploadedFile::fake()->create('jawaban.pdf', 10, 'application/pdf'),
        ])->assertRedirect();
        $submission = Submission::query()->where('activity_id', $task->id)->where('user_id', $student->id)->firstOrFail();
        Storage::disk('local')->assertExists($submission->file_path);
        $this->actingAs($teacher)->patch('/kelas/tugas/pengumpulan/' . $submission->id . '/nilai', [
            'score' => 92,
            'teacher_feedback' => 'Bagus, lanjutkan.',
        ])->assertRedirect();
        $this->actingAs($student)->get('/kelas/tugas')->assertOk()->assertSee('Bagus, lanjutkan.')->assertSee('92');
        $this->actingAs($student)->post('/kelas/tugas/' . $task->id . '/kumpul', [
            'content' => 'Mau ubah jawaban',
        ])->assertSessionHasErrors('tugas');
        $this->assertSame('Ini jawabanku.', $submission->fresh()->content);
        $this->actingAs($teacher)->delete('/kelas/tugas/' . $task->id)->assertSessionHasErrors('tugas');
        $this->assertDatabaseHas('activities', ['id' => $task->id]);
    }

    public function test_other_teacher_and_unenrolled_student_cannot_manage_or_submit_tasks(): void
    {
        $teacher = $this->user('guru', 'own');
        $otherTeacher = $this->user('guru', 'other');
        $outsider = $this->user('siswa', 'outside');
        $class = $this->schoolClass($teacher, 'E555');
        $task = $this->task($class);
        $this->actingAs($otherTeacher)->put('/kelas/tugas/' . $task->id, [
            'class_id' => $class->id,
            'title' => 'Tidak berhak',
            'instructions' => 'Coba edit',
            'fpbl_stage' => 4,
        ])->assertNotFound();
        $this->actingAs($otherTeacher)->delete('/kelas/tugas/' . $task->id)->assertNotFound();
        $this->actingAs($outsider)->post('/kelas/tugas/' . $task->id . '/kumpul', [
            'content' => 'Tidak boleh',
        ])->assertForbidden();
    }

    public function test_student_cannot_submit_after_due_date(): void
    {
        $teacher = $this->user('guru', 'late');
        $student = $this->user('siswa', 'late');
        $class = $this->schoolClass($teacher, 'F666');
        $class->students()->attach($student->id);
        $task = $this->task($class);
        $task->update(['due_at' => now()->subMinute()]);
        $this->actingAs($student)->post('/kelas/tugas/' . $task->id . '/kumpul', [
            'content' => 'Terlambat',
        ])->assertSessionHasErrors('tugas');
        $this->assertDatabaseCount('submissions', 0);
    }
}

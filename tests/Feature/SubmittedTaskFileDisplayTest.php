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

class SubmittedTaskFileDisplayTest extends TestCase
{
    use RefreshDatabase;

    private function setupTask(): array
    {
        $teacher = User::create(['name'=>'Guru Lampiran', 'email'=>'guru.lampiran@example.test', 'password'=>'password123', 'role'=>'guru']);
        $student = User::create(['name'=>'Siswa Lampiran', 'email'=>'siswa.lampiran@example.test', 'password'=>'password123', 'role'=>'siswa']);
        $class = SchoolClass::create(['teacher_id'=>$teacher->id, 'name'=>'VII A Matematika', 'join_code'=>'LMPRN123']);
        $class->students()->attach($student->id);
        $task = Activity::create(['class_id'=>$class->id, 'title'=>'Tugas Bangun Ruang', 'instructions'=>'Unggah jawabannya', 'fpbl_stage'=>2, 'mode'=>'tugas', 'starts_at'=>now(), 'due_at'=>now()->addDay()]);
        return [$teacher, $student, $task];
    }

    public function test_submitted_file_is_displayed_with_original_name_and_can_be_previewed(): void
    {
        Storage::fake('local');
        [$teacher, $student, $task] = $this->setupTask();
        $this->actingAs($student)->post(route('classroom.tasks.submit', $task), [
            'content'=>'Jawaban bangun ruang',
            'file'=>UploadedFile::fake()->create('jawaban-bangun-ruang.pdf', 24, 'application/pdf'),
        ])->assertRedirect();
        $submission = Submission::where('activity_id', $task->id)->firstOrFail();
        $this->assertSame('jawaban-bangun-ruang.pdf', $submission->original_file_name);
        $this->assertGreaterThan(0, $submission->file_size_bytes);
        $this->actingAs($student)->get(route('classroom.tasks.index'))
            ->assertOk()->assertSee('jawaban-bangun-ruang.pdf')->assertSee('Lihat File')->assertSee('Unduh File')
            ->assertSee('Ubah jawaban / Ganti file')->assertSee('Jawaban bangun ruang');
        $this->actingAs($student)->get(route('classroom.tasks.download', $submission).'?preview=1')
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($student)->get(route('classroom.tasks.download', $submission))
            ->assertOk()->assertDownload('jawaban-bangun-ruang.pdf');
        $outsider = User::create(['name'=>'Siswa Lain', 'email'=>'siswa.lain@example.test', 'password'=>'password123', 'role'=>'siswa']);
        $this->actingAs($outsider)->get(route('classroom.tasks.download', $submission).'?preview=1')->assertForbidden();
    }

    public function test_file_replacement_updates_name_without_erasing_saved_text(): void
    {
        Storage::fake('local');
        [$teacher, $student, $task] = $this->setupTask();
        $this->actingAs($student)->post(route('classroom.tasks.submit', $task), [
            'content'=>'Penjelasan tetap tersimpan',
            'file'=>UploadedFile::fake()->create('awal.pdf', 4, 'application/pdf'),
        ])->assertRedirect();
        $submission = Submission::where('activity_id', $task->id)->firstOrFail();
        $oldPath = $submission->file_path;
        $this->actingAs($student)->post(route('classroom.tasks.submit', $task), [
            'file'=>UploadedFile::fake()->create('revisi.pdf', 8, 'application/pdf'),
        ])->assertRedirect();
        $submission->refresh();
        $this->assertSame('revisi.pdf', $submission->original_file_name);
        $this->assertSame('Penjelasan tetap tersimpan', $submission->content);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($submission->file_path);
    }

    public function test_old_attachment_without_original_filename_still_shows_download_button(): void
    {
        Storage::fake('local');
        [$teacher, $student, $task] = $this->setupTask();
        $path = UploadedFile::fake()->create('arsip.pdf', 4, 'application/pdf')->store('mathleaf/tasks', 'local');
        Submission::create(['activity_id'=>$task->id, 'user_id'=>$student->id, 'file_path'=>$path, 'submitted_at'=>now(), 'content'=>'Tugas lama']);
        $this->actingAs($student)->get(route('classroom.tasks.index'))
            ->assertOk()->assertSee('Lampiran tersimpan (PDF)')->assertSee('Unduh File');
    }
}

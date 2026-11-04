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

class TeacherTaskInlinePreviewTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(string $filename = 'jawaban.pdf'): array
    {
        Storage::fake('local');
        $teacher = User::create(['name'=>'Guru Utama', 'email'=>'guru.preview@example.test', 'password'=>'password123', 'role'=>'guru']);
        $other = User::create(['name'=>'Guru Lain', 'email'=>'guru.lain.preview@example.test', 'password'=>'password123', 'role'=>'guru']);
        $student = User::create(['name'=>'Siswa Preview', 'email'=>'siswa.preview@example.test', 'password'=>'password123', 'role'=>'siswa']);
        $class = SchoolClass::create(['teacher_id'=>$teacher->id, 'name'=>'VII A Matematika', 'join_code'=>'PREVIEW1']);
        $class->students()->attach($student->id);
        $task = Activity::create(['class_id'=>$class->id, 'title'=>'Tugas Bangun Ruang', 'instructions'=>'Kirim jawaban', 'fpbl_stage'=>2, 'mode'=>'tugas', 'starts_at'=>now(), 'due_at'=>now()->addDay()]);
        $path = UploadedFile::fake()->create($filename, 3, $filename === 'jawaban.pdf' ? 'application/pdf' : 'application/zip')->store('mathleaf/tasks', 'local');
        $submission = Submission::create(['activity_id'=>$task->id, 'user_id'=>$student->id,
            'content'=>'Jawaban matematika', 'file_path'=>$path, 'submitted_at'=>now(),
            'original_file_name'=>$filename, 'file_size_bytes'=>3072]);
        return [$teacher, $other, $student, $task, $submission];
    }

    public function test_teacher_sees_inline_preview_button_and_private_pdf_stream(): void
    {
        [$teacher, $other, $student, $task, $submission] = $this->makeSubmission();
        $this->actingAs($teacher)->get(route('classroom.tasks.index'))->assertOk()
            ->assertSee('Lihat Jawaban')->assertSee('mlt-teacher-preview')
            ->assertSee('jawaban.pdf')->assertDontSee('Unduh Lampiran Siswa');
        $this->actingAs($teacher)->get(route('classroom.tasks.download', $submission).'?preview=1')
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($other)->get(route('classroom.tasks.download', $submission).'?preview=1')
            ->assertForbidden();
    }

    public function test_unsupported_zip_is_not_streamed_as_browser_preview(): void
    {
        [$teacher, , , , $submission] = $this->makeSubmission('jawaban.zip');
        $this->actingAs($teacher)->get(route('classroom.tasks.index'))->assertOk()
            ->assertSee('Lihat Jawaban')->assertSee('jawaban.zip');
        $this->actingAs($teacher)->get(route('classroom.tasks.download', $submission).'?preview=1')
            ->assertOk()->assertDownload('jawaban.zip');
    }
}

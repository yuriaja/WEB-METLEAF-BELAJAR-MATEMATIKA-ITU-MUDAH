<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialLearningFlowTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role, string $id): User
    {
        return User::create([
            'name' => ucfirst($role) . $id,
            'email' => $role . $id . '@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function studyClass(User $teacher, string $code): SchoolClass
    {
        return SchoolClass::create(['teacher_id' => $teacher->id, 'name' => 'VII ' . $code, 'join_code' => $code]);
    }

    private function material(SchoolClass $class, User $teacher, string $title, bool $published = true): Material
    {
        return Material::create([
            'class_id' => $class->id,
            'author_id' => $teacher->id,
            'title' => $title,
            'slug' => 'materi-' . strtolower($class->join_code) . '-' . \Illuminate\Support\Str::random(8),
            'type' => 'link',
            'resource_url' => 'https://example.org/math',
            'is_published' => $published,
        ]);
    }

    public function test_teacher_uploads_private_draft_then_publishes_for_enrolled_student(): void
    {
        Storage::fake('local');
        $teacher = $this->account('guru', 'upload');
        $student = $this->account('siswa', 'upload');
        $class = $this->studyClass($teacher, 'A111');
        $class->students()->attach($student->id);

        $this->actingAs($teacher)->post('/materi', [
            'class_id' => $class->id,
            'title' => 'Bilangan Bulat 7A',
            'description' => 'Materi bilangan bulat',
            'type' => 'modul',
            'file' => UploadedFile::fake()->create('bilangan.pdf', 12, 'application/pdf'),
        ])->assertRedirect('/materi');

        $material = Material::where('title', 'Bilangan Bulat 7A')->firstOrFail();
        $this->assertFalse((bool) $material->is_published);
        Storage::disk('local')->assertExists($material->file_path);

        $this->actingAs($student)->get('/materi')->assertOk()->assertDontSee('Bilangan Bulat 7A');
        $this->actingAs($student)->get('/materi/' . $material->id)->assertForbidden();
        $this->actingAs($teacher)->patch('/materi/' . $material->id . '/publikasi')->assertRedirect();
        $this->actingAs($student)->get('/materi')->assertOk()->assertSee('Bilangan Bulat 7A');
        $this->actingAs($student)->get('/materi/' . $material->id)->assertOk()->assertSee('Bilangan Bulat 7A');
        $this->actingAs($student)->get('/materi/' . $material->id . '/file?download=1')->assertOk();
    }

    public function test_student_cannot_see_other_class_material_or_download_its_private_file(): void
    {
        Storage::fake('local');
        $teacher = $this->account('guru', 'scope');
        $student = $this->account('siswa', 'scope');
        $class = $this->studyClass($teacher, 'B222');
        $other = $this->studyClass($teacher, 'C333');
        $class->students()->attach($student->id);
        $visible = $this->material($class, $teacher, 'Materi yang boleh');
        $hidden = $this->material($other, $teacher, 'Materi yang rahasia');
        $hidden->update(['file_path' => 'mathleaf/materials/rahasia.pdf', 'original_filename' => 'rahasia.pdf']);
        Storage::disk('local')->put('mathleaf/materials/rahasia.pdf', 'isi rahasia');

        $this->actingAs($student)->get('/materi')->assertOk()->assertSee($visible->title)->assertDontSee($hidden->title);
        $this->actingAs($student)->get('/materi/' . $hidden->id)->assertForbidden();
        $this->actingAs($student)->get('/materi/' . $hidden->id . '/file')->assertForbidden();
        $this->actingAs($student)->post('/materi/' . $hidden->id . '/progres', ['progress_percent' => 100])->assertForbidden();
        $this->actingAs($student)->get('/materi?class_id=' . $other->id)->assertForbidden();
    }

    public function test_student_progress_is_saved_and_can_be_resumed(): void
    {
        $teacher = $this->account('guru', 'progress');
        $student = $this->account('siswa', 'progress');
        $class = $this->studyClass($teacher, 'D444');
        $class->students()->attach($student->id);
        $material = $this->material($class, $teacher, 'Latihan Pecahan');

        $this->actingAs($student)->post('/materi/' . $material->id . '/progres', ['progress_percent' => 50])->assertRedirect();
        $this->assertDatabaseHas('material_progress', ['user_id' => $student->id, 'material_id' => $material->id, 'progress_percent' => 50]);
        $this->actingAs($student)->post('/materi/' . $material->id . '/progres', ['progress_percent' => 100])->assertRedirect();
        $this->assertDatabaseHas('material_progress', ['user_id' => $student->id, 'material_id' => $material->id, 'progress_percent' => 100]);
        $this->actingAs($student)->get('/materi?status=done')->assertOk()->assertSee('Latihan Pecahan');
        $this->actingAs($student)->get('/materi?status=undone')->assertOk()->assertDontSee('Latihan Pecahan');
    }

    public function test_other_teacher_cannot_edit_publish_or_delete_material(): void
    {
        $teacher = $this->account('guru', 'owner');
        $outsider = $this->account('guru', 'visitor');
        $student = $this->account('siswa', 'visitor');
        $class = $this->studyClass($teacher, 'E555');
        $material = $this->material($class, $teacher, 'Materi milik guru pertama');

        $this->actingAs($outsider)->put('/materi/' . $material->id, [
            'class_id' => $class->id, 'title' => 'Perubahan ilegal', 'type' => 'link',
            'resource_url' => 'https://example.org/evil',
        ])->assertForbidden();
        $this->actingAs($outsider)->patch('/materi/' . $material->id . '/publikasi')->assertForbidden();
        $this->actingAs($outsider)->delete('/materi/' . $material->id)->assertForbidden();
        $this->actingAs($student)->post('/materi', [
            'class_id' => $class->id, 'title' => 'Tidak boleh', 'type' => 'link', 'resource_url' => 'https://example.org',
        ])->assertForbidden();
        $this->assertSame('Materi milik guru pertama', $material->fresh()->title);
    }

    public function test_teacher_can_edit_and_delete_unused_material_but_not_material_with_student_progress(): void
    {
        $teacher = $this->account('guru', 'edit');
        $student = $this->account('siswa', 'edit');
        $class = $this->studyClass($teacher, 'F666');
        $class->students()->attach($student->id);
        $material = $this->material($class, $teacher, 'Versi Lama');

        $this->actingAs($teacher)->put('/materi/' . $material->id, [
            'class_id' => $class->id, 'title' => 'Versi Revisi', 'type' => 'link',
            'resource_url' => 'https://example.org/revisi',
        ])->assertRedirect();
        $this->assertSame('Versi Revisi', $material->fresh()->title);
        $this->actingAs($student)->post('/materi/' . $material->id . '/progres', ['progress_percent' => 100])->assertRedirect();
        $this->actingAs($teacher)->delete('/materi/' . $material->id)->assertSessionHasErrors('materi');
        $this->assertDatabaseHas('materials', ['id' => $material->id]);
        $unused = $this->material($class, $teacher, 'Boleh dihapus');
        $this->actingAs($teacher)->delete('/materi/' . $unused->id)->assertRedirect('/materi');
        $this->assertDatabaseMissing('materials', ['id' => $unused->id]);
    }
}

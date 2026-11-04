<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherTaskZoomPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_task_page_has_zoom_controls_in_private_preview(): void
    {
        $teacher = User::create([
            'name' => 'Guru Zoom',
            'email' => 'guru.zoom@example.test',
            'password' => 'password123',
            'role' => 'guru',
        ]);
        SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'VII A Matematika',
            'join_code' => 'ZOOMTEST',
        ]);

        $this->actingAs($teacher)->get(route('classroom.tasks.index'))
            ->assertOk()
            ->assertSee('mlt-preview-zoom-tools')
            ->assertSee('mlt-zoom-in')
            ->assertSee('mlt-zoom-out')
            ->assertSee('mlt-zoom-reset')
            ->assertSee('mlt-preview-open-pdf');
    }
}

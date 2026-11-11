<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Berkas soal/tugas tetap privat; hanya anggota kelas terkait atau guru yang berhak. */
class LearningAttachmentController extends Controller
{
    public function question(Request $request, Question $question)
    {
        $assessment = $question->assessment;
        abort_unless($assessment && $assessment->class_id, 404);
        $user = $request->user();
        $teacher = ($user->role === 'admin') ||
            (in_array($user->role, ['guru'], true) && (int) $assessment->author_id === (int) $user->id);
        $student = $user->role === 'siswa' && $assessment->is_published &&
            $user->classes()->whereKey($assessment->class_id)->exists();
        abort_unless($teacher || $student, 403);
        return $this->serve($request, $question->attachment_path, $question->attachment_name);
    }

    public function task(Request $request, Activity $activity)
    {
        abort_unless($activity->mode === 'tugas' && $activity->class_id, 404);
        $user = $request->user();
        $teacher = $user->role === 'admin' || ($user->role === 'guru' &&
            $user->teachingClasses()->whereKey($activity->class_id)->exists());
        $student = $user->role === 'siswa' && $user->classes()->whereKey($activity->class_id)->exists();
        abort_unless($teacher || $student, 403);
        return $this->serve($request, $activity->attachment_path, $activity->attachment_name);
    }

    private function serve(Request $request, ?string $path, ?string $originalName)
    {
        $disk = Storage::disk('local');
        abort_unless($path && $disk->exists($path), 404);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        // Inline hanya untuk gambar/PDF; dokumen lain selalu berupa unduhan.
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            default => null,
        };
        if ($request->boolean('preview') && $mime) {
            return response()->file($disk->path($path), [
                'Content-Type' => $mime,
                'X-Content-Type-Options' => 'nosniff',
                'Content-Security-Policy' => "default-src 'none'; sandbox",
            ]);
        }
        return $disk->download($path, $originalName ?: basename($path), [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

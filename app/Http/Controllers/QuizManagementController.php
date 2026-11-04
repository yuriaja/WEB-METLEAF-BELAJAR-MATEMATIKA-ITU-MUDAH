<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizManagementController extends Controller
{
    private function owner(Request $request, Assessment $quiz): void
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['guru', 'admin'], true), 403);
        abort_unless((int) $quiz->author_id === (int) $user->id, 403);
        if ($user->role !== 'admin') {
            abort_unless($quiz->class_id && $user->teachingClasses()->whereKey($quiz->class_id)->exists(), 403);
        }
    }

    public function destroy(Request $request, Assessment $assessment)
    {
        $this->owner($request, $assessment);

        // Kunci assessment juga dipakai saat siswa mengirim jawaban.
        $result = DB::transaction(function () use ($assessment) {
            $quiz = Assessment::query()->lockForUpdate()->findOrFail($assessment->id);
            if ($quiz->attempts()->exists()) {
                $quiz->forceFill(['archived_at' => now(), 'is_published' => false])->save();
                return 'archive';
            }
            $quiz->delete(); // Pertanyaan ikut terhapus via FK. Tanpa percobaan siswa.
            return 'delete';
        });

        return redirect()->route('teacher.quizzes')
            ->with('success', $result === 'archive'
                ? 'Kuis telah diarsipkan. Nilai dan lembar jawaban siswa tetap disimpan.'
                : 'Kuis berhasil dihapus permanen.');
    }

    public function restore(Request $request, Assessment $assessment)
    {
        $this->owner($request, $assessment);
        abort_if($assessment->archived_at === null, 404);

        // Dipulihkan sebagai draft; guru perlu menerbitkannya lagi.
        $assessment->forceFill(['archived_at' => null, 'is_published' => false])->save();

        return redirect()->route('teacher.quizzes')
            ->with('success', 'Kuis berhasil dipulihkan sebagai draft.');
    }
}

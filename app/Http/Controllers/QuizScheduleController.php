<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Support\QuizSchedule;
use Illuminate\Http\Request;

class QuizScheduleController extends Controller
{
    public function update(Request $request, Assessment $assessment)
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['guru', 'admin'], true), 403);
        abort_unless((int) $assessment->author_id === (int) $user->id, 403);
        abort_unless($assessment->class_id && $user->teachingClasses()->whereKey($assessment->class_id)->exists(), 403);
        $values = $request->validate([
            'starts_at' => 'nullable|date_format:Y-m-d\TH:i',
            'due_at' => 'required|date_format:Y-m-d\TH:i',
        ]);
        $schedule = QuizSchedule::validateDates($values);
        abort_if(!$assessment->questions()->exists(), 422, 'Tambahkan soal sebelum menerbitkan kuis.');

        // May reactivate archived quizzes without changing previous submissions.
        $assessment->forceFill($schedule + ['archived_at' => null, 'is_published' => true])->save();

        return redirect()->route('teacher.quizzes')->with('success', 'Jadwal kuis diperbarui (WIB). Kuis dibuka kembali sesuai waktu mulai.');
    }
}

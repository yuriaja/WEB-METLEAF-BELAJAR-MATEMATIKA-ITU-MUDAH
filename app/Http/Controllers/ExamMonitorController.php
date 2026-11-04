<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamMonitorController extends Controller
{
    public function record(Request $request, Assessment $assessment)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'siswa', 403);
        abort_unless(
            $assessment->type === 'ujian'
            && (bool) $assessment->is_published
            && $assessment->archived_at === null
            && $assessment->class_id !== null
            && $user->classes()->whereKey($assessment->class_id)->exists(),
            403
        );
        $data = $request->validate([
            'event_type' => ['required', 'in:tab_hidden,fullscreen_exit'],
        ]);

        // Jangan menghitung berulang satu kejadian yang dipicu beberapa event browser.
        $recentlyRecorded = DB::table('exam_incidents')
            ->where('assessment_id', $assessment->id)
            ->where('user_id', $user->id)
            ->where('event_type', $data['event_type'])
            ->where('created_at', '>=', now()->subSeconds(3))
            ->exists();

        if (! $recentlyRecorded) {
            DB::table('exam_incidents')->insert([
                'assessment_id' => $assessment->id,
                'user_id' => $user->id,
                'event_type' => $data['event_type'],
                'created_at' => now(),
            ]);
        }
        return response()->json(['recorded' => true], 202);
    }

    public function index(Request $request, Assessment $assessment)
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['guru', 'admin'], true), 403);
        abort_unless($assessment->type === 'ujian', 404);
        abort_unless(
            $user->role === 'admin'
            || ((int) $assessment->author_id === (int) $user->id
                && $user->teachingClasses()->whereKey($assessment->class_id)->exists()),
            403
        );

        $incidents = DB::table('exam_incidents')
            ->join('users', 'users.id', '=', 'exam_incidents.user_id')
            ->where('exam_incidents.assessment_id', $assessment->id)
            ->select('exam_incidents.*', 'users.name as student_name')
            ->orderByDesc('exam_incidents.created_at')
            ->paginate(40);

        return view('pages.guru.exam-incidents', compact('assessment', 'incidents'));
    }
}

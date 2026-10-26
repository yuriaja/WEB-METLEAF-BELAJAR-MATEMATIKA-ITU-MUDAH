<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\AssessmentAttempt;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Submission;
use Illuminate\Http\Request;

class StudentGradesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->role === 'siswa', 403);

        $request->validate(['class_id' => ['nullable', 'integer']]);
        $classes = $user->classes()->orderBy('name')->get(['classes.id', 'name']);
        $selectedClassId = $request->filled('class_id') ? (int) $request->query('class_id') : null;
        if ($selectedClassId !== null) {
            abort_unless($classes->contains('id', $selectedClassId), 403);
        }
        $classIds = $selectedClassId === null
            ? $classes->pluck('id')->all()
            : [$selectedClassId];

        $attempts = AssessmentAttempt::query()
            ->where('user_id', $user->id)
            ->whereHas('assessment', fn ($query) => $query->whereIn('class_id', $classIds))
            ->with('assessment:id,title,class_id')
            ->latest('submitted_at')->get();

        $submissions = Submission::query()
            ->where('user_id', $user->id)
            ->whereHas('activity', fn ($query) => $query->where('mode', 'tugas')->whereIn('class_id', $classIds))
            ->with('activity:id,title,class_id')
            ->latest('submitted_at')->get();

        $quizHistory = $attempts->map(fn ($item) => (object) [
            'title' => $item->assessment?->title ?? 'Kuis',
            'category' => 'Kuis',
            'date' => $item->submitted_at ?? $item->created_at,
            'score' => $item->score,
            'feedback' => null,
            'result_id' => $item->id,
        ]);
        $taskHistory = $submissions->map(fn ($item) => (object) [
            'title' => $item->activity?->title ?? 'Tugas',
            'category' => 'Tugas',
            'date' => $item->submitted_at ?? $item->created_at,
            'score' => $item->score,
            'feedback' => $item->teacher_feedback,
            'result_id' => null,
        ]);
        $history = $quizHistory->concat($taskHistory)->sortByDesc('date')->values();
        $scored = $history->filter(fn ($item) => $item->score !== null);
        $average = $scored->isNotEmpty() ? round($scored->avg('score'), 1) : null;

        $tasksTotal = Activity::query()->where('mode', 'tugas')->whereIn('class_id', $classIds)->count();
        $tasksSubmitted = $submissions->pluck('activity_id')->unique()->count();

        $materials = Material::query()
            ->where('is_published', true)
            ->whereIn('class_id', $classIds)
            ->orderBy('title')->get(['id', 'title']);
        $progressByMaterial = MaterialProgress::query()
            ->where('user_id', $user->id)
            ->whereIn('material_id', $materials->pluck('id'))
            ->pluck('progress_percent', 'material_id');
        $progressAverage = $materials->isNotEmpty()
            ? (int) round($materials->avg(fn ($material) => (int) $progressByMaterial->get($material->id, 0)))
            : 0;

        return view('pages.siswa.nilai', compact(
            'user', 'classes', 'selectedClassId', 'history',
            'average', 'tasksTotal', 'tasksSubmitted', 'materials',
            'progressByMaterial', 'progressAverage', 'attempts'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\ForumThread;
use App\Models\Submission;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->role === 'siswa', 403);

        $classes = $user->classes()->orderBy('name')->get(['classes.id', 'classes.name']);
        $activeClassId = (int) $request->session()->get('kelas_aktif_id', 0);
        $activeClass = $classes->firstWhere('id', $activeClassId);
        $classIds = $activeClass ? [$activeClass->id] : $classes->pluck('id')->all();

        $materials = Material::query()
            ->where('is_published', true)
            ->whereIn('class_id', $classIds)
            ->get(['id', 'title', 'class_id']);
        $materialIds = $materials->pluck('id')->all();
        $progress = MaterialProgress::query()
            ->where('user_id', $user->id)
            ->whereIn('material_id', $materialIds)
            ->with('material:id,title')
            ->get();
        $progressById = $progress->keyBy('material_id');
        $materialProgress = $materials->isEmpty() ? 0 : (int) round(
            $materials->avg(fn (Material $material) => (int) ($progressById->get($material->id)?->progress_percent ?? 0))
        );
        $materialsCompleted = $materials->filter(fn (Material $material) =>
            (int) ($progressById->get($material->id)?->progress_percent ?? 0) === 100
        )->count();
        $incompleteMaterials = $materials->filter(fn (Material $material) =>
            (int) ($progressById->get($material->id)?->progress_percent ?? 0) < 100
        )->take(4);

        $tasks = Activity::query()
            ->where('mode', 'tugas')
            ->whereIn('class_id', $classIds)
            ->orderBy('due_at')
            ->get(['id', 'class_id', 'title', 'due_at']);
        $submissions = Submission::query()
            ->where('user_id', $user->id)
            ->whereIn('activity_id', $tasks->pluck('id'))
            ->with('activity:id,title')
            ->get();
        $submittedIds = $submissions->pluck('activity_id')->unique();
        $submittedCount = $tasks->filter(fn (Activity $task) => $submittedIds->contains($task->id))->count();
        $pendingTasks = $tasks->reject(fn (Activity $task) => $submittedIds->contains($task->id))->values();
        $pendingCount = $pendingTasks->count();
        $overdueCount = $pendingTasks->filter(fn (Activity $task) => $task->due_at && $task->due_at->isPast())->count();
        $upcomingTasks = $pendingTasks->filter(fn (Activity $task) =>
            $task->due_at && $task->due_at->isFuture()
        )->sortBy('due_at')->take(4)->values();

        $quizzes = Assessment::query()
            ->whereIn('class_id', $classIds)
            ->where('is_published', true)
            ->whereHas('questions')
            ->with(['attempts' => fn ($query) => $query->where('user_id', $user->id)->latest('created_at')])
            ->latest()->get();
        $availableQuizzes = $quizzes->filter(function (Assessment $quiz) {
            $attempts = $quiz->attempts;
            $passed = $attempts->contains(fn ($attempt) =>
                $attempt->score !== null && (float) $attempt->score >= (float) ($quiz->passing_score ?? 70)
            );
            return !$passed && $attempts->count() < max(1, (int) $quiz->max_attempts);
        })->values();
        $remedialQuizzes = $availableQuizzes->filter(fn (Assessment $quiz) =>
            $quiz->attempts->contains(fn ($attempt) => $attempt->score !== null)
        )->values();

        $scoredAttempts = AssessmentAttempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('score')
            ->whereHas('assessment', fn ($query) => $query
                ->whereIn('class_id', $classIds)->where('is_published', true))
            ->with('assessment:id,title')
            ->latest('created_at')->get();
        // Untuk kuis remedial, gunakan nilai percobaan terbaru per kuis agar tidak menghitung satu kuis berulang kali.
        $latestScores = $scoredAttempts->unique('assessment_id')->pluck('score')
            ->concat($submissions->whereNotNull('score')->pluck('score'));
        $averageScore = $latestScores->isEmpty() ? null : round((float) $latestScores->avg(), 1);

        $quizActivities = $scoredAttempts->take(5)->map(fn ($attempt) => (object) [
            'title' => 'Mengerjakan kuis: '.($attempt->assessment?->title ?? 'Kuis'),
            'detail' => 'Nilai '.number_format((float) $attempt->score, 0),
            'date' => $attempt->submitted_at ?? $attempt->created_at,
            'url' => route('siswa.quiz.result', $attempt),
        ]);
        $taskActivities = $submissions->sortByDesc('submitted_at')->take(5)->map(fn ($submission) => (object) [
            'title' => 'Mengumpulkan tugas: '.($submission->activity?->title ?? 'Tugas'),
            'detail' => $submission->score === null ? 'Menunggu penilaian' : 'Nilai '.number_format((float) $submission->score, 0),
            'date' => $submission->submitted_at ?? $submission->created_at,
            'url' => route('classroom.tasks.index'),
        ]);
        $materialActivities = $progress->sortByDesc('updated_at')->take(5)->map(fn ($item) => (object) [
            'title' => 'Mempelajari: '.($item->material?->title ?? 'Materi'),
            'detail' => ((int) $item->progress_percent).'% selesai',
            'date' => $item->updated_at,
            'url' => route('materi.show', $item->material_id),
        ]);
        $recentActivities = $quizActivities->concat($taskActivities)->concat($materialActivities)
            ->sortByDesc('date')->take(5)->values();

        // Forum kelas terbaru: hanya topik kelas yang sedang diikuti/aktif oleh siswa.
        $recentForumThreads = ForumThread::query()
            ->whereIn('class_id', $classIds)
            ->with('schoolClass:id,name')
            ->orderByDesc('is_pinned')
            ->latest('created_at')
            ->limit(3)
            ->get(['id', 'class_id', 'title', 'created_at', 'is_pinned', 'is_locked']);

        return view('pages.siswa.dashboard', compact(
            'classes', 'activeClass', 'materials', 'progressById', 'incompleteMaterials', 'materialProgress', 'materialsCompleted',
            'tasks', 'pendingTasks', 'pendingCount', 'submittedCount', 'overdueCount', 'upcomingTasks',
            'quizzes', 'availableQuizzes', 'remedialQuizzes', 'averageScore', 'recentActivities', 'recentForumThreads'
        ));
    }
}

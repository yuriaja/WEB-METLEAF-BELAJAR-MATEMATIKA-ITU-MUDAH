<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $classIds = $this->classIds($user);
        $activeClassId = (int) $request->session()->get('kelas_aktif_id');

        if ($activeClassId && in_array($activeClassId, $classIds, true)) {
            $classIds = [$activeClassId];
        }

        if ($classIds === []) {
            return response()->json([]);
        }

        $notifications = collect();
        $since = now()->subDays(7);

        if ($user->role === 'siswa') {
            $this->addStudentNotifications($notifications, $user, $classIds, $since);
        } else {
            $this->addTeacherNotifications($notifications, $user, $classIds, $since);
        }

        return response()->json(
            $notifications
                ->sortByDesc(fn (array $item) => $item['occurred_at']->timestamp)
                ->take(8)
                ->values()
        );
    }

    /**
     * @return list<int>
     */
    private function classIds(User $user): array
    {
        if ($user->role === 'guru') {
            return $user->teachingClasses()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        if ($user->role === 'admin') {
            return SchoolClass::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        abort_unless($user->role === 'siswa', 403);

        return $user->classes()->pluck('classes.id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * @param  list<int>  $classIds
     */
    private function addStudentNotifications(Collection $notifications, User $user, array $classIds, Carbon $since): void
    {
        $materials = DB::table('materials')
            ->join('classes', 'classes.id', '=', 'materials.class_id')
            ->whereIn('materials.class_id', $classIds)
            ->where('materials.is_published', true)
            ->where(function ($query) use ($since) {
                $query->where('materials.created_at', '>=', $since)
                    ->orWhere('materials.updated_at', '>=', $since);
            })
            ->latest('materials.updated_at')
            ->limit(8)
            ->get([
                'materials.id',
                'materials.title',
                'materials.class_id',
                'materials.updated_at as occurred_at',
                'classes.name as class_name',
            ]);

        foreach ($materials as $material) {
            $this->add($notifications, 'material:'.$material->id, 'Materi baru: '.$material->title,
                'Kelas '.$material->class_name,
                route('materi.show', ['material' => $material->id]), $material->occurred_at);
        }

        $tasks = DB::table('activities')
            ->join('classes', 'classes.id', '=', 'activities.class_id')
            ->whereIn('activities.class_id', $classIds)
            ->where('activities.mode', 'tugas')
            ->where('activities.created_at', '>=', $since)
            ->latest('activities.created_at')
            ->limit(8)
            ->get([
                'activities.id',
                'activities.title',
                'activities.class_id',
                'activities.created_at as occurred_at',
                'classes.name as class_name',
            ]);

        foreach ($tasks as $task) {
            $this->add($notifications, 'task:'.$task->id, 'Tugas baru: '.$task->title, 'Kelas '.$task->class_name,
                route('classroom.tasks.index', ['class_id' => $task->class_id]), $task->occurred_at);
        }

        $grades = DB::table('submissions')
            ->join('activities', 'activities.id', '=', 'submissions.activity_id')
            ->join('classes', 'classes.id', '=', 'activities.class_id')
            ->where('submissions.user_id', $user->id)
            ->whereNotNull('submissions.score')
            ->where('submissions.updated_at', '>=', $since)
            ->where('activities.mode', 'tugas')
            ->whereIn('activities.class_id', $classIds)
            ->latest('submissions.updated_at')
            ->limit(8)
            ->get([
                'submissions.id',
                'activities.title',
                'activities.class_id',
                'submissions.score',
                'submissions.updated_at as occurred_at',
                'classes.name as class_name',
            ]);

        foreach ($grades as $grade) {
            $this->add($notifications, 'grade:'.$grade->id, 'Tugas dinilai: '.$grade->title,
                'Nilai '.$grade->score.' · '.$grade->class_name,
                route('classroom.tasks.index', ['class_id' => $grade->class_id, 'status' => 'dinilai']),
                $grade->occurred_at);
        }

        $quizzes = DB::table('assessments')
            ->join('classes', 'classes.id', '=', 'assessments.class_id')
            ->whereIn('assessments.class_id', $classIds)
            ->where('assessments.is_published', true)
            ->whereNull('assessments.archived_at')
            ->whereIn('assessments.type', ['kuis', 'sumatif'])
            ->where('assessments.created_at', '>=', $since)
            ->latest('assessments.created_at')
            ->limit(8)
            ->get([
                'assessments.id',
                'assessments.title',
                'assessments.created_at as occurred_at',
                'classes.name as class_name',
            ]);

        foreach ($quizzes as $quiz) {
            $this->add($notifications, 'quiz:'.$quiz->id, 'Kuis baru: '.$quiz->title, 'Kelas '.$quiz->class_name,
                route('siswa.kuis'), $quiz->occurred_at);
        }

        $this->addForumNotifications($notifications, $user, $classIds, $since);
    }

    /**
     * @param  list<int>  $classIds
     */
    private function addTeacherNotifications(Collection $notifications, User $user, array $classIds, Carbon $since): void
    {
        $submissions = DB::table('submissions')
            ->join('activities', 'activities.id', '=', 'submissions.activity_id')
            ->join('classes', 'classes.id', '=', 'activities.class_id')
            ->join('users', 'users.id', '=', 'submissions.user_id')
            ->whereIn('activities.class_id', $classIds)
            ->where('activities.mode', 'tugas')
            ->where('submissions.user_id', '!=', $user->id)
            ->whereNotNull('submissions.submitted_at')
            ->where('submissions.submitted_at', '>=', $since)
            ->latest('submissions.submitted_at')
            ->limit(8)
            ->get([
                'submissions.id',
                'activities.title',
                'activities.class_id',
                'submissions.submitted_at as occurred_at',
                'classes.name as class_name',
                'users.name as student_name',
            ]);

        foreach ($submissions as $submission) {
            $this->add($notifications, 'submission:'.$submission->id, $submission->student_name.' mengumpulkan tugas',
                $submission->title.' · '.$submission->class_name,
                route('classroom.tasks.index', [
                    'class_id' => $submission->class_id,
                    'submission' => $submission->id,
                ]),
                $submission->occurred_at);
        }

        $this->addForumNotifications($notifications, $user, $classIds, $since);

        $attempts = DB::table('assessment_attempts')
            ->join('assessments', 'assessments.id', '=', 'assessment_attempts.assessment_id')
            ->join('classes', 'classes.id', '=', 'assessments.class_id')
            ->join('users', 'users.id', '=', 'assessment_attempts.user_id')
            ->whereIn('assessments.class_id', $classIds)
            ->where('assessment_attempts.user_id', '!=', $user->id)
            ->whereNotNull('assessment_attempts.submitted_at')
            ->where('assessment_attempts.submitted_at', '>=', $since)
            ->latest('assessment_attempts.submitted_at')
            ->limit(8)
            ->get([
                'assessment_attempts.id',
                'assessment_attempts.score',
                'assessment_attempts.submitted_at as occurred_at',
                'assessments.title',
                'classes.name as class_name',
                'users.name as student_name',
            ]);

        foreach ($attempts as $attempt) {
            $this->add($notifications, 'attempt:'.$attempt->id, $attempt->student_name.' menyelesaikan kuis',
                $attempt->title.' · '.$attempt->class_name.' · Nilai '.$attempt->score,
                route('teacher.quizzes.attempt', ['attempt' => $attempt->id]), $attempt->occurred_at);
        }
    }

    /**
     * @param  list<int>  $classIds
     */
    private function addForumNotifications(Collection $notifications, User $user, array $classIds, Carbon $since): void
    {
        $threads = DB::table('forum_threads')
            ->join('classes', 'classes.id', '=', 'forum_threads.class_id')
            ->join('users', 'users.id', '=', 'forum_threads.user_id')
            ->whereIn('forum_threads.class_id', $classIds)
            ->where('forum_threads.user_id', '!=', $user->id)
            ->where('forum_threads.created_at', '>=', $since)
            ->latest('forum_threads.created_at')
            ->limit(8)
            ->get([
                'forum_threads.id',
                'forum_threads.title',
                'forum_threads.class_id',
                'forum_threads.created_at as occurred_at',
                'classes.name as class_name',
                'users.name as author_name',
            ]);

        foreach ($threads as $thread) {
            $this->add($notifications, 'forum:'.$thread->id, 'Topik forum baru: '.$thread->title,
                $thread->author_name.' · '.$thread->class_name,
                route('forum.index', ['class_id' => $thread->class_id, 'thread' => $thread->id]),
                $thread->occurred_at);
        }

        $replies = DB::table('forum_comments')
            ->join('forum_threads', 'forum_threads.id', '=', 'forum_comments.forum_thread_id')
            ->join('classes', 'classes.id', '=', 'forum_threads.class_id')
            ->join('users', 'users.id', '=', 'forum_comments.user_id')
            ->whereIn('forum_threads.class_id', $classIds)
            ->where('forum_comments.user_id', '!=', $user->id)
            ->where('forum_comments.created_at', '>=', $since)
            ->when($user->role === 'siswa', fn ($query) => $query->where('forum_threads.user_id', $user->id))
            ->latest('forum_comments.created_at')
            ->limit(8)
            ->get([
                'forum_comments.id',
                'forum_comments.body',
                'forum_comments.created_at as occurred_at',
                'forum_threads.id as thread_id',
                'forum_threads.title as thread_title',
                'forum_threads.class_id',
                'classes.name as class_name',
                'users.name as author_name',
            ]);

        foreach ($replies as $reply) {
            $this->add($notifications, 'forum-comment:'.$reply->id,
                'Balasan baru: '.$reply->thread_title,
                $reply->author_name.' · '.$reply->class_name.' · '.Str::limit($reply->body, 120),
                route('forum.index', ['class_id' => $reply->class_id, 'thread' => $reply->thread_id]),
                $reply->occurred_at);
        }
    }

    private function add(Collection $notifications, string $id, string $title, string $description, string $url, string $occurredAt): void
    {
        $occurredAt = Carbon::parse($occurredAt);
        $notifications->push([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'occurred_at' => $occurredAt,
            'time_ago' => $occurredAt->diffForHumans(),
        ]);
    }
}

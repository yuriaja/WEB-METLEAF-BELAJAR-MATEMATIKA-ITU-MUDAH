<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use App\Models\ForumThread;
use App\Models\SchoolClass;
use App\Support\ForumAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'class_id' => ['nullable', 'integer'],
            'q' => ['nullable', 'string', 'max:100'],
            'mode' => ['nullable', Rule::in(['all', 'mine', 'unanswered'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'thread' => ['nullable', 'integer'],
        ]);

        $user = $request->user();
        $classIds = ForumAccess::classIds($user);
        $openThreadId = isset($filters['thread']) ? (int) $filters['thread'] : session('forum_open_thread');
        if ($openThreadId !== null) {
            abort_unless(
                ForumThread::query()->whereIn('class_id', $classIds)->whereKey($openThreadId)->exists(),
                404
            );
        }
        $classes = SchoolClass::query()->whereIn('id', $classIds)
            ->orderBy('name')->get(['id', 'name']);

        $selectedClassId = !empty($filters['class_id']) ? (int) $filters['class_id'] : null;
        if ($selectedClassId !== null) {
            abort_unless(in_array($selectedClassId, $classIds, true), 403);
        }

        $query = trim((string) ($filters['q'] ?? ''));
        $mode = $filters['mode'] ?? 'all';
        $isModerator = in_array($user->role, ['guru', 'admin'], true);

        $threads = ForumThread::query()
            ->whereIn('class_id', $classIds)
            ->when(isset($filters['thread']), fn ($builder) => $builder->whereKey((int) $filters['thread']))
            ->when($selectedClassId !== null, fn ($builder) => $builder->where('class_id', $selectedClassId))
            ->when($query !== '', fn ($builder) => $builder->where(function ($search) use ($query) {
                $search->where('title', 'like', '%'.$query.'%')
                    ->orWhere('body', 'like', '%'.$query.'%');
            }))
            ->when($mode === 'mine', fn ($builder) => $builder->where('user_id', $user->id))
            ->when($mode === 'unanswered', fn ($builder) => $builder->doesntHave('comments'))
            ->with([
                'author:id,name,role',
                'schoolClass:id,name',
                'comments' => fn ($builder) => $builder->oldest(),
                'comments.author:id,name,role',
            ])
            ->withCount('comments')
            ->orderByDesc('is_pinned')->latest()
            ->paginate(10)->withQueryString();

        $shown = $threads->getCollection();
        $teacherThreads = $shown->filter(fn ($thread) => in_array($thread->author?->role, ['guru', 'admin'], true));
        $myThreads = $shown->where('user_id', $user->id);
        $drawerData = $shown->mapWithKeys(function ($thread) use ($user, $isModerator) {
            $canModerate = $isModerator && ForumAccess::canModerate($user, (int) $thread->class_id);
            $canDelete = $canModerate || ((int) $thread->user_id === (int) $user->id && !$thread->is_locked && $thread->comments_count === 0);

            return [(string) $thread->id => [
                'title' => $thread->title,
                'body' => $thread->body,
                'author' => $thread->author?->name ?? 'Pengguna',
                'role' => in_array($thread->author?->role, ['guru', 'admin'], true) ? 'Guru' : 'Siswa',
                'className' => $thread->schoolClass?->name ?? 'Kelas',
                'replyUrl' => route('forum.comments.store', ['thread' => $thread->id], false),
                'editUrl' => route('forum.threads.update', ['thread' => $thread->id], false),
                'canEdit' => $canModerate || ((int) $thread->user_id === (int) $user->id && !$thread->is_locked),
                'canDelete' => $canDelete,
                'locked' => (bool) $thread->is_locked,
                'comments' => $thread->comments->map(fn ($comment) => [
                    'author' => $comment->author?->name ?? 'Pengguna',
                    'role' => in_array($comment->author?->role, ['guru', 'admin'], true) ? 'Guru' : 'Siswa',
                    'body' => $comment->body,
                    'canDelete' => $canModerate || ((int) $comment->user_id === (int) $user->id && !$thread->is_locked),
                    'deleteUrl' => route('forum.comments.destroy', ['comment' => $comment->id], false),
                ])->all(),
            ]];
        })->all();

        return view('pages.shared.forum', compact(
            'classes', 'selectedClassId', 'threads', 'teacherThreads', 'myThreads',
            'drawerData', 'openThreadId', 'query', 'mode', 'isModerator'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validateWithBag('new_thread', [
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
        ]);
        abort_unless(ForumAccess::canAccess($request->user(), (int) $data['class_id']), 403);

        $thread = ForumThread::create([
            'class_id' => $data['class_id'],
            'user_id' => $request->user()->id,
            'title' => trim($data['title']),
            'body' => trim($data['body']),
        ]);
        return $this->backToForum($request, $thread, 'Topik diskusi berhasil dibuat.');
    }

    public function update(Request $request, ForumThread $thread)
    {
        $this->canEditThread($request, $thread);
        $data = $request->validateWithBag('edit_thread', [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
        ]);
        $thread->update(['title' => trim($data['title']), 'body' => trim($data['body'])]);
        return $this->backToForum($request, $thread, 'Topik diskusi berhasil diperbarui.');
    }

    public function destroy(Request $request, ForumThread $thread)
    {
        abort_unless(ForumAccess::canAccess($request->user(), $thread->class_id), 403);
        $isModerator = ForumAccess::canModerate($request->user(), $thread->class_id);
        $isOwnUnanswered = (int) $thread->user_id === (int) $request->user()->id
            && !$thread->is_locked && !$thread->comments()->exists();
        abort_unless($isModerator || $isOwnUnanswered, 403);

        $classId = $thread->class_id;
        $thread->delete();
        return $this->backToForum($request, null, 'Topik diskusi berhasil dihapus.', $classId);
    }

    public function togglePin(Request $request, ForumThread $thread)
    {
        $this->moderatorOnly($request, $thread);
        $thread->update(['is_pinned' => !$thread->is_pinned]);
        return $this->backToForum($request, $thread, $thread->is_pinned ? 'Topik disematkan.' : 'Sematan topik dilepas.');
    }

    public function toggleLock(Request $request, ForumThread $thread)
    {
        $this->moderatorOnly($request, $thread);
        $thread->update(['is_locked' => !$thread->is_locked]);
        return $this->backToForum($request, $thread, $thread->is_locked ? 'Diskusi dikunci.' : 'Diskusi dibuka kembali.');
    }

    public function comment(Request $request, ForumThread $thread)
    {
        abort_unless(ForumAccess::canAccess($request->user(), $thread->class_id), 403);
        abort_if($thread->is_locked, 403, 'Diskusi ini telah dikunci oleh guru.');
        $data = $request->validateWithBag('forum_reply', [
            'body' => ['required', 'string', 'max:10000'],
        ]);
        ForumComment::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);
        return $this->backToForum($request, $thread, 'Balasan berhasil dikirim.');
    }

    public function destroyComment(Request $request, ForumComment $comment)
    {
        $thread = $comment->forumThread;
        abort_unless($thread && ForumAccess::canAccess($request->user(), $thread->class_id), 403);
        $isModerator = ForumAccess::canModerate($request->user(), $thread->class_id);
        $isOwner = (int) $comment->user_id === (int) $request->user()->id && !$thread->is_locked;
        abort_unless($isModerator || $isOwner, 403);
        $comment->delete();
        return $this->backToForum($request, $thread, 'Balasan berhasil dihapus.');
    }

    private function canEditThread(Request $request, ForumThread $thread): void
    {
        abort_unless(ForumAccess::canAccess($request->user(), $thread->class_id), 403);
        $isModerator = ForumAccess::canModerate($request->user(), $thread->class_id);
        $isOwner = (int) $thread->user_id === (int) $request->user()->id && !$thread->is_locked;
        abort_unless($isModerator || $isOwner, 403);
    }

    private function moderatorOnly(Request $request, ForumThread $thread): void
    {
        abort_unless(ForumAccess::canModerate($request->user(), $thread->class_id), 403);
    }

    private function backToForum(Request $request, ?ForumThread $thread, string $message, ?int $classId = null)
    {
        $classId ??= $thread?->class_id;
        $request->session()->flash('forum_success', $message);
        if ($thread) {
            $request->session()->flash('forum_open_thread', $thread->id);
        }
        // URL relatif agar cookie dan sesi berfungsi di localhost serta tunnel HTTPS.
        $returnPath = (string) $request->input('return_path', '');
        if (!preg_match('~^/forum(?:\?(?!.*[\r\n]).*)?$~D', $returnPath)) {
            $returnPath = route('forum.index', ['class_id' => $classId], false);
        }
        return response('', 303, ['Location' => $returnPath]);
    }
}

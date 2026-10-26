<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use App\Models\ForumThread;
use App\Models\SchoolClass;
use App\Support\ForumAccess;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'class_id' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $classIds = ForumAccess::classIds($request->user());
        $classes = SchoolClass::query()
            ->whereIn('id', $classIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedClassId = $request->filled('class_id')
            ? (int) $request->query('class_id')
            : null;

        if ($selectedClassId !== null) {
            abort_unless(in_array($selectedClassId, $classIds, true), 403);
        }

        $threads = ForumThread::query()
            ->whereIn('class_id', $classIds)
            ->when($selectedClassId !== null, fn ($query) => $query->where('class_id', $selectedClassId))
            ->with([
                'author:id,name,role',
                'schoolClass:id,name',
                'comments' => fn ($query) => $query->oldest(),
                'comments.author:id,name,role',
            ])
            ->withCount('comments')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $shown = $threads->getCollection();
        $teacherThreads = $shown->filter(fn ($thread) => in_array($thread->author?->role, ['guru', 'admin'], true));
        $myThreads = $shown->where('user_id', $request->user()->id);

        $drawerData = $shown->mapWithKeys(function ($thread) {
            return [(string) $thread->id => [
                'title' => $thread->title,
                'body' => $thread->body,
                'author' => $thread->author?->name ?? 'Pengguna',
                'role' => in_array($thread->author?->role, ['guru', 'admin'], true) ? 'Guru' : 'Siswa',
                'className' => $thread->schoolClass?->name ?? 'Kelas',
                'replyUrl' => route('forum.comments.store', ['thread' => $thread->id], false),
                'comments' => $thread->comments->map(fn ($comment) => [
                    'author' => $comment->author?->name ?? 'Pengguna',
                    'role' => in_array($comment->author?->role, ['guru', 'admin'], true) ? 'Guru' : 'Siswa',
                    'body' => $comment->body,
                ])->all(),
            ]];
        })->all();

        $openThreadId = session('forum_open_thread');

        return view('pages.shared.forum', compact(
            'classes',
            'selectedClassId',
            'threads',
            'teacherThreads',
            'myThreads',
            'drawerData',
            'openThreadId'
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
            'title' => $data['title'],
            'body' => $data['body'],
        ]);

        $request->session()->flash('forum_success', 'Topik diskusimu berhasil dibuat.');
        $request->session()->flash('forum_open_thread', $thread->id);

        // URL relatif menjaga browser tetap di HTTPS ketika dibuka lewat ngrok.
        return response('', 303, [
            'Location' => route('forum.index', ['class_id' => $thread->class_id], false),
        ]);
    }

    public function comment(Request $request, ForumThread $thread)
    {
        abort_unless(ForumAccess::canAccess($request->user(), $thread->class_id), 403);

        $data = $request->validateWithBag('forum_reply', [
            'body' => ['required', 'string', 'max:10000'],
        ]);

        ForumComment::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        $request->session()->flash('forum_success', 'Balasan berhasil dikirim.');
        $request->session()->flash('forum_open_thread', $thread->id);

        // Hanya izinkan URL lokal /forum, bukan tautan eksternal dari input pengguna.
        $returnPath = (string) $request->input('return_path', '');
        if (!preg_match('~^/forum(?:\?|$)~', $returnPath)) {
            $returnPath = route('forum.index', ['class_id' => $thread->class_id], false);
        }

        return response('', 303, ['Location' => $returnPath]);
    }
}

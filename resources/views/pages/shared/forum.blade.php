@extends('layouts.app')

@section('title', 'Forum Diskusi | MathLeaf')
@section('header_title', 'Forum Diskusi')

@section('content')
    @php
        $isTeacher = Auth::check() && in_array(Auth::user()->role, ['guru', 'admin']);
        $userName = Auth::user()->name ?? 'Karin';
    @endphp

    <style>
        .app-content {
            font-size: 11.2px;
        }


        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
        }

        body {
            font-size: 12.8px;
        }

        .page-heading {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-heading h1 {
            font-size: 1.6rem;
            color: #0f172a;
            margin: 0.5rem 0;
            font-weight: 800;
        }

        .page-heading p {
            color: #475569;
            font-size: 0.88rem;
            margin: 0;
            line-height: 1.6;
        }

        .btn-primary {
            background: var(--ml-green);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: var(--ml-green-hover);
        }

        .two-columns {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 900px) {
            .two-columns {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.02);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1rem;
        }

        .panel-head h2 {
            font-size: 1.12rem;
            margin: 0;
            color: #0f172a;
            font-weight: 700;
        }

        .badge {
            background: var(--ml-green-light);
            color: var(--ml-green);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        /* THREAD / DISKUSI */
        .thread {
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            background: #fafafa;
        }

        .thread-title {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .avatar-large {
            width: 45px;
            height: 45px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.96rem;
            flex-shrink: 0;
        }

        .thread-title strong {
            display: block;
            font-size: 0.88rem;
            color: #0f172a;
        }

        .thread-title small {
            color: #64748b;
            font-size: 0.72rem;
        }

        .thread h3 {
            font-size: 1.04rem;
            color: #0f172a;
            margin: 0 0 0.5rem 0;
            line-height: 1.4;
        }

        .thread p {
            font-size: 0.84rem;
            color: #334155;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .reply {
            margin-top: 1rem;
            padding: 1.2rem;
            background: #ffffff;
            border-left: 4px solid var(--ml-green);
            border-radius: 0 8px 8px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .reply b {
            display: block;
            font-size: 0.8rem;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }

        .reply p {
            font-size: 0.8rem;
            color: #475569;
            margin: 0;
        }

        .field label {
            display: block;
            font-weight: 600;
            font-size: 0.88rem;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }

        .answer-input {
            width: 100%;
            padding: 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.84rem;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
            outline: none;
            transition: border-color 0.2s;
        }

        .answer-input:focus {
            border-color: var(--ml-green);
        }

        /* PANEL KANAN (TOPIK) */
        .side-panel-box {
            margin-bottom: 1.5rem;
        }

        .side-panel-box h3 {
            font-size: 0.96rem;
            color: #0f172a;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .topic-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .topic-item {
            display: block;
            padding: 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            border-radius: 8px;
            text-decoration: none;
            color: #334155;
            transition: all 0.2s;
        }

        .topic-item:hover {
            background: #ffffff;
            border-color: var(--ml-green);
            box-shadow: 0 2px 8px rgba(8, 101, 52, 0.1);
            transform: translateX(-3px);
        }

        .topic-item strong {
            display: block;
            font-size: 0.8rem;
            color: #0f172a;
            margin-bottom: 0.3rem;
            line-height: 1.3;
        }

        .topic-item small {
            font-size: 0.68rem;
            color: #64748b;
        }

        /* MODAL BUAT PERTANYAAN */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .modal-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            transform: translateY(20px);
            transition: transform 0.3s;
        }

        .modal-backdrop.active .modal {
            transform: translateY(0);
        }

        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-head h2 {
            font-size: 1.12rem;
            margin: 0;
        }

        .modal-head button {
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
        }

        .field input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        /* SLIDE-OUT DRAWER (TOPIC DETAIL) */
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.3);
            z-index: 9998;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .drawer-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .topic-drawer {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 600px;
            height: 100vh;
            background: #ffffff;
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            transition: right 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            /* Efek membal mulus */
            display: flex;
            flex-direction: column;
        }

        .topic-drawer.active {
            right: 0;
        }

        .drawer-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .drawer-header h2 {
            font-size: 1.04rem;
            margin: 0;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-close-drawer {
            background: #e2e8f0;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            transition: background 0.2s;
        }

        .btn-close-drawer:hover {
            background: #cbd5e1;
            color: #0f172a;
        }

        .drawer-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .drawer-footer {
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .forum-filter { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:1.4rem; }
        .forum-filter select { padding:0.7rem; border:1px solid #cbd5e1; border-radius:8px; background:white; }
        .forum-link { border:0; padding:0; background:transparent; cursor:pointer; color:var(--ml-green); font:inherit; font-weight:700; text-align:left; }
        .forum-link:hover { text-decoration:underline; }
        .forum-alert { border-radius:8px; padding:0.9rem 1rem; margin-bottom:1.2rem; background:#edf8f0; color:#086534; }
        .forum-alert-error { background:#fff1f2; color:#9f1239; }
        .forum-empty { color:#64748b; line-height:1.6; padding:1rem 0; }
        .topic-item { width:100%; text-align:left; font:inherit; }
        .forum-pages { display:flex; justify-content:space-between; align-items:center; gap:1rem; }
        .forum-pages a { font-weight:600; color:var(--ml-green); text-decoration:none; }
        .forum-pages a:hover { text-decoration:underline; }
        .forum-modal-class { display:block; width:100%; margin:0.4rem 0 1rem; border:1px solid #cbd5e1; border-radius:6px; padding:0.7rem; }
        .reply-empty { margin-top:1rem; color:#64748b; }
        .forum-controls { display:flex; align-items:center; flex-wrap:wrap; gap:.45rem; margin:.75rem 0; }
        .forum-controls form { margin:0; }
        .forum-small-btn { display:inline-flex; align-items:center; padding:.4rem .65rem; border:1px solid #cbd5e1; background:#fff; color:#086534; border-radius:7px; font-size:.72rem; font-weight:700; cursor:pointer; }
        .forum-small-btn:hover { background:#edf8f0; }
        .forum-danger { color:#b91c1c; border-color:#fecaca; }
        .forum-tags { display:inline-flex; gap:.35rem; align-items:center; margin-left:.45rem; }
        .forum-tag { display:inline-flex; padding:.22rem .5rem; border-radius:30px; background:#eaf7ee; color:#086534; font-size:.66rem; font-weight:700; }
        .forum-tag-locked { background:#f1f5f9; color:#475569; }
        .forum-filter input { min-width:145px; flex:1; padding:.7rem; border:1px solid #cbd5e1; border-radius:8px; }
        .forum-filter select { min-width:125px; }
        .forum-muted { color:#64748b; font-size:.74rem; }
        .forum-reply-card { display:flex; gap:1rem; justify-content:space-between; }
        .forum-reply-card > div { min-width:0; flex:1; }
        .forum-reply-delete { align-self:flex-start; }
        .forum-edit-modal { max-width:560px; }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow" style="display:inline-flex;align-items:center;gap:.4rem;font-weight:700;color:#086534;font-size:.9rem;letter-spacing:.05em;text-transform:uppercase;">
                FORUM PEMBELAJARAN
            </span>
            <h1>Ide Hebat Tumbuh Bersama</h1>
            <p>Bertanya, berbagi pemahaman, dan belajar dari satu sama lain.</p>
        </div>
        <button type="button" class="btn-primary" onclick="openForumModal()" @disabled($classes->isEmpty())>
            + Buat Pertanyaan
        </button>
    </div>

    @if(session('forum_success'))
        <div class="forum-alert" role="status">{{ session('forum_success') }}</div>
    @endif
    @if($errors->new_thread->any())
        <div class="forum-alert forum-alert-error" role="alert">{{ $errors->new_thread->first() }}</div>
    @endif
    @if($errors->forum_reply->any())
        <div class="forum-alert forum-alert-error" role="alert">{{ $errors->forum_reply->first() }}</div>
    @endif
    @if($errors->edit_thread->any())
        <div class="forum-alert forum-alert-error" role="alert">{{ $errors->edit_thread->first() }}</div>
    @endif

    <form method="GET" action="{{ route('forum.index', [], false) }}" class="forum-filter">
        <label for="forum-filter-class"><strong>Pilih kelas:</strong></label>
        <select name="class_id" id="forum-filter-class">
            <option value="">Semua kelas saya</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" @selected($selectedClassId === (int) $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        <label for="forum-search" class="forum-muted">Cari:</label>
        <input id="forum-search" name="q" type="search" maxlength="100" value="{{ $query }}" placeholder="Judul atau isi topik">
        <label for="forum-mode" class="forum-muted">Lihat:</label>
        <select name="mode" id="forum-mode">
            <option value="all" @selected($mode === 'all')>Semua topik</option>
            <option value="mine" @selected($mode === 'mine')>Topik saya</option>
            <option value="unanswered" @selected($mode === 'unanswered')>Belum dibalas</option>
        </select>
        <button type="submit" class="btn-primary">Cari Topik</button>
    </form>

    <div class="two-columns">
        <!-- Panel diskusi: data langsung dari forum_threads dan forum_comments -->
        <section class="panel">
            <div class="panel-head">
                <h2>{{ $selectedClassId ? 'Diskusi ' . ($classes->firstWhere('id', $selectedClassId)?->name ?? 'Kelas') : 'Diskusi Kelas' }}</h2>
                <span class="badge">{{ $threads->total() }} topik aktif</span>
            </div>

            <div id="forum-topics">
                @forelse($threads as $thread)
                    <article class="thread">
                        <div class="thread-title">
                            <span class="avatar-large">{{ mb_strtoupper(mb_substr($thread->author?->name ?? 'P', 0, 1)) }}</span>
                            <div>
                                <strong>{{ $thread->author?->name ?? 'Pengguna' }}</strong>
                                <small>
                                    {{ in_array($thread->author?->role, ['guru', 'admin'], true) ? 'Guru' : 'Siswa' }}
                                    · {{ $thread->schoolClass?->name ?? 'Kelas' }}
                                    · {{ $thread->created_at?->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        <h3>
                            <button type="button" class="forum-link" onclick="openDrawer({{ $thread->id }})">{{ $thread->title }}</button>
                            <span class="forum-tags">
                                @if($thread->is_pinned)<span class="forum-tag">📌 Disematkan</span>@endif
                                @if($thread->is_locked)<span class="forum-tag forum-tag-locked">🔒 Dikunci</span>@endif
                            </span>
                        </h3>
                        <p style="white-space:pre-line;overflow-wrap:anywhere;">{{ $thread->body }}</p>
                        @php
                            $canModerateThread = $isModerator && \App\Support\ForumAccess::canModerate(auth()->user(), (int) $thread->class_id);
                            $isOwnThread = (int)$thread->user_id === (int)auth()->id();
                        @endphp
                        <div class="forum-controls">
                            @if($canModerateThread || ($isOwnThread && !$thread->is_locked))
                                <button type="button" class="forum-small-btn" onclick="openForumEdit({{ $thread->id }})">Edit topik</button>
                            @endif
                            @if($canModerateThread)
                                <form method="POST" action="{{ route('forum.threads.pin', $thread, false) }}">@csrf @method('PATCH')
                                    <input type="hidden" name="return_path" value="{{ request()->getRequestUri() }}">
                                    <button type="submit" class="forum-small-btn">{{ $thread->is_pinned ? 'Lepas sematan' : 'Sematkan' }}</button>
                                </form>
                                <form method="POST" action="{{ route('forum.threads.lock', $thread, false) }}">@csrf @method('PATCH')
                                    <input type="hidden" name="return_path" value="{{ request()->getRequestUri() }}">
                                    <button type="submit" class="forum-small-btn">{{ $thread->is_locked ? 'Buka diskusi' : 'Kunci diskusi' }}</button>
                                </form>
                            @endif
                            @if($canModerateThread || ($isOwnThread && !$thread->is_locked && $thread->comments_count === 0))
                                <form method="POST" action="{{ route('forum.threads.destroy', $thread, false) }}" onsubmit="return confirm('Hapus topik ini? Balasan yang ada juga akan terhapus.')">@csrf @method('DELETE')
                                    <input type="hidden" name="return_path" value="{{ request()->getRequestUri() }}">
                                    <button type="submit" class="forum-small-btn forum-danger">Hapus topik</button>
                                </form>
                            @endif
                        </div>
                        @foreach($thread->comments->take(2) as $comment)
                            <div class="reply">
                                <b>{{ $comment->author?->name ?? 'Pengguna' }} · {{ in_array($comment->author?->role, ['guru', 'admin'], true) ? 'Guru' : 'Siswa' }}</b>
                                <p style="white-space:pre-line;overflow-wrap:anywhere;">{{ $comment->body }}</p>
                            </div>
                        @endforeach
                        <button type="button" class="forum-link" onclick="openDrawer({{ $thread->id }})" style="margin-top:1rem;">
                            Buka diskusi & balas ({{ $thread->comments_count }} balasan)
                        </button>
                    </article>
                @empty
                    <p class="forum-empty">
                        @if($classes->isEmpty())
                            Belum ada kelas yang bisa kamu akses. Guru dapat membuat kelas, sedangkan siswa perlu bergabung ke kelas terlebih dahulu.
                        @else
                            Belum ada diskusi untuk pilihan kelas ini. Yuk mulai dengan membuat pertanyaan baru!
                        @endif
                    </p>
                @endforelse
            </div>

            @if($threads->hasPages())
                <div class="forum-pages">
                    <span>
                        @if($threads->previousPageUrl())
                            <a href="{{ $threads->previousPageUrl() }}">← Sebelumnya</a>
                        @endif
                    </span>
                    <span>Halaman {{ $threads->currentPage() }} dari {{ $threads->lastPage() }}</span>
                    <span>
                        @if($threads->nextPageUrl())
                            <a href="{{ $threads->nextPageUrl() }}">Berikutnya →</a>
                        @endif
                    </span>
                </div>
            @endif
        </section>

        <aside>
            <section class="panel side-panel-box">
                <h3>📗 Topik dari Guru</h3>
                <div class="topic-list">
                    @forelse($teacherThreads as $thread)
                        <button type="button" class="topic-item" onclick="openDrawer({{ $thread->id }})">
                            <strong>{{ $thread->title }}</strong>
                            <small>Oleh {{ $thread->author?->name ?? 'Guru' }} · {{ $thread->comments_count }} balasan</small>
                        </button>
                    @empty
                        <p class="forum-empty">Belum ada topik guru pada halaman ini.</p>
                    @endforelse
                </div>
            </section>

            <section class="panel side-panel-box">
                <h3>💬 Topik yang Saya Buat</h3>
                <div class="topic-list">
                    @forelse($myThreads as $thread)
                        <button type="button" class="topic-item" onclick="openDrawer({{ $thread->id }})">
                            <strong>{{ $thread->title }}</strong>
                            <small>{{ $thread->comments_count }} balasan · {{ $thread->schoolClass?->name ?? 'Kelas' }}</small>
                        </button>
                    @empty
                        <p class="forum-empty">Belum ada pertanyaan yang kamu buat pada halaman ini.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>

    <!-- Modal tetap memakai desain asal, sekarang terhubung dengan POST Laravel -->
    <div class="modal-backdrop" id="forum-modal" role="presentation">
        <form class="modal" id="forum-create-form" method="POST" action="{{ route('forum.threads.store', [], false) }}">
            @csrf
            <div class="modal-head">
                <h2>Buat Pertanyaan Baru</h2>
                <button type="button" aria-label="Tutup" onclick="closeForumModal()">✕</button>
            </div>
            <div class="field">
                <label for="forum-new-class">Kelas Diskusi</label>
                <select id="forum-new-class" name="class_id" class="forum-modal-class" required>
                    <option value="">Pilih kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected((string) old('class_id', $selectedClassId ?? ($classes->count() === 1 ? $classes->first()->id : '')) === (string) $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
                <label for="forum-new-title">Judul Diskusi</label>
                <input id="forum-new-title" name="title" type="text" maxlength="255" required value="{{ old('title') }}" placeholder="Contoh: Bingung cara mengubah pecahan ke desimal">
            </div>
            <div class="field">
                <label for="forum-new-body">Isi Pertanyaan</label>
                <textarea id="forum-new-body" name="body" class="answer-input" maxlength="10000" required placeholder="Tuliskan detail pertanyaanmu di sini...">{{ old('body') }}</textarea>
            </div>
            <button type="submit" class="btn-primary" style="margin-top:1rem;width:100%;justify-content:center;">Publikasikan Pertanyaan</button>
        </form>
    </div>

    <!-- Edit topik asli, tidak mengubah class_id maupun pemilik topik -->
    <div class="modal-backdrop" id="forum-edit-modal" role="presentation">
        <form class="modal forum-edit-modal" id="forum-edit-form" method="POST" action="#">
            @csrf @method('PUT')
            <input type="hidden" name="return_path" value="{{ request()->getRequestUri() }}">
            <div class="modal-head">
                <h2>Edit Topik Diskusi</h2>
                <button type="button" aria-label="Tutup" onclick="closeForumEdit()">✕</button>
            </div>
            <div class="field">
                <label for="forum-edit-title">Judul Topik</label>
                <input id="forum-edit-title" name="title" maxlength="255" required>
                <label for="forum-edit-body">Isi Topik</label>
                <textarea class="answer-input" id="forum-edit-body" name="body" maxlength="10000" required></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">Simpan Perubahan</button>
        </form>
    </div>

    <!-- Drawer detail diskusi tetap dipakai, kini memuat judul dan komentar asli -->
    <div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawer()"></div>
    <div class="topic-drawer" id="topic-drawer" role="dialog" aria-label="Detail diskusi" aria-modal="true">
        <div class="drawer-header">
            <h2>💬 Detail Diskusi</h2>
            <button type="button" class="btn-close-drawer" aria-label="Tutup diskusi" onclick="closeDrawer()">✕</button>
        </div>
        <div class="drawer-content">
            <article class="thread" style="background:transparent;border:none;padding:0;">
                <div class="thread-title">
                    <span class="avatar-large" id="drawer-avatar">?</span>
                    <div>
                        <strong id="drawer-author"></strong>
                        <small id="drawer-role"></small>
                    </div>
                </div>
                <h3 id="drawer-title"></h3>
                <p id="drawer-body" style="white-space:pre-line;overflow-wrap:anywhere;"></p>
                <div style="margin-top:2rem;border-top:1px solid #e2e8f0;padding-top:1.5rem;">
                    <h4 id="drawer-reply-count" style="margin:0 0 1rem;color:#64748b;"></h4>
                    <div id="drawer-replies"></div>
                </div>
            </article>
        </div>
        <div class="drawer-footer">
            <form id="forum-reply-form" method="POST" action="#">
                @csrf
                <input type="hidden" name="return_path" value="{{ request()->getRequestUri() }}">
                <p id="forum-locked-notice" class="forum-muted" style="display:none;">🔒 Diskusi dikunci guru. Balasan baru tidak diizinkan.</p>
                <div id="forum-reply-fields">
                    <label for="forum-reply-body" style="display:block;margin-bottom:.5rem;font-weight:600;">Tulis Balasan</label>
                    <textarea id="forum-reply-body" name="body" class="answer-input" style="min-height:80px;margin-bottom:.5rem;" maxlength="10000" required placeholder="Balas diskusi ini..."></textarea>
                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">Kirim Balasan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const forumThreadData = {{ \Illuminate\Support\Js::from($drawerData) }};

        function openForumModal() {
            document.getElementById('forum-modal').classList.add('active');
        }

        function closeForumModal() {
            document.getElementById('forum-modal').classList.remove('active');
        }

        document.getElementById('forum-modal').addEventListener('click', function (event) {
            if (event.target === this) closeForumModal();
        });

        function openForumEdit(id) {
            const thread = forumThreadData[String(id)];
            if (!thread || !thread.canEdit) return;
            document.getElementById('forum-edit-form').action = thread.editUrl;
            document.getElementById('forum-edit-title').value = thread.title;
            document.getElementById('forum-edit-body').value = thread.body;
            document.getElementById('forum-edit-modal').classList.add('active');
        }

        function closeForumEdit() {
            document.getElementById('forum-edit-modal').classList.remove('active');
        }

        document.getElementById('forum-edit-modal').addEventListener('click', function (event) {
            if (event.target === this) closeForumEdit();
        });

        function openDrawer(id) {
            const thread = forumThreadData[String(id)];
            if (!thread) return;

            document.getElementById('drawer-title').textContent = thread.title;
            document.getElementById('drawer-body').textContent = thread.body;
            document.getElementById('drawer-author').textContent = thread.author;
            document.getElementById('drawer-role').textContent = thread.role + ' · ' + thread.className;
            document.getElementById('drawer-avatar').textContent = Array.from(thread.author)[0]?.toUpperCase() ?? '?';
            document.getElementById('drawer-reply-count').textContent = 'Balasan (' + thread.comments.length + ')';
            document.getElementById('forum-reply-form').action = thread.replyUrl;
            document.getElementById('forum-reply-body').value = '';
            document.getElementById('forum-reply-fields').style.display = thread.locked ? 'none' : '';
            document.getElementById('forum-locked-notice').style.display = thread.locked ? '' : 'none';
            document.getElementById('forum-reply-body').required = !thread.locked;

            const replies = document.getElementById('drawer-replies');
            replies.replaceChildren();

            if (thread.comments.length === 0) {
                const empty = document.createElement('p');
                empty.className = 'reply-empty';
                empty.textContent = 'Belum ada balasan. Jadilah yang pertama menjawab!';
                replies.appendChild(empty);
            }

            thread.comments.forEach(function (item) {
                const card = document.createElement('div');
                card.className = 'reply forum-reply-card';
                const body = document.createElement('div');
                const name = document.createElement('b');
                name.textContent = item.author + ' · ' + item.role;
                const content = document.createElement('p');
                content.textContent = item.body;
                content.style.whiteSpace = 'pre-line';
                content.style.overflowWrap = 'anywhere';
                body.append(name, content);
                card.appendChild(body);
                if (item.canDelete) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = item.deleteUrl;
                    form.className = 'forum-reply-delete';
                    const token = document.createElement('input');
                    token.type = 'hidden'; token.name = '_token'; token.value = {{ \Illuminate\Support\Js::from(csrf_token()) }};
                    const method = document.createElement('input');
                    method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
                    const returnPath = document.createElement('input');
                    returnPath.type = 'hidden'; returnPath.name = 'return_path'; returnPath.value = window.location.pathname + window.location.search;
                    const button = document.createElement('button');
                    button.type = 'submit'; button.className = 'forum-small-btn forum-danger'; button.textContent = 'Hapus';
                    form.addEventListener('submit', function (event) {
                        if (!confirm('Hapus balasan ini?')) event.preventDefault();
                    });
                    form.append(token, method, returnPath, button);
                    card.appendChild(form);
                }
                replies.appendChild(card);
            });

            document.getElementById('drawer-overlay').classList.add('active');
            document.getElementById('topic-drawer').classList.add('active');
        }

        function closeDrawer() {
            document.getElementById('drawer-overlay').classList.remove('active');
            document.getElementById('topic-drawer').classList.remove('active');
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeForumModal();
                closeForumEdit();
                closeDrawer();
            }
        });

        @if($errors->new_thread->any())
            openForumModal();
        @endif

        @if($openThreadId !== null && array_key_exists((string) $openThreadId, $drawerData))
            openDrawer({{ (int) $openThreadId }});
        @endif
    </script>
@endsection

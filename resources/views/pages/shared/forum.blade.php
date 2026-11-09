@extends('layouts.app')

@section('title', 'Forum Diskusi | MathLeaf')
@section('header_title', Auth::check() && in_array(Auth::user()->role, ['guru', 'admin']) ? 'Kelola Diskusi' : 'Forum
    Diskusi')

@section('content')
    @php
        $isTeacher = Auth::check() && in_array(Auth::user()->role, ['guru', 'admin']);
        $userName = Auth::user()->name ?? 'Karin';
    @endphp

    <style>
        .app-content {
            font-size: 11.2px;
            overflow-x: visible;
        }

        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-soft: #dff3e5;
        }

        /* ---------- Header ---------- */
        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
        }

        .page-heading h1 {
            font-size: clamp(1.44rem, 2.4vw, 1.84rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .4rem;
            line-height: 1.2;
        }

        .page-heading p {
            color: #475569;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.5;
        }

        /* ---------- Tombol ---------- */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            border: 0;
            border-radius: 10px;
            padding: .75rem 1.2rem;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            background: var(--ml-green);
            color: #ffffff;
            transition: background .2s, transform .15s, box-shadow .2s;
        }

        .btn-primary:hover {
            background: var(--ml-green-hover);
            box-shadow: 0 6px 16px rgba(8, 101, 52, .25);
            color: #ffffff;
        }

        .btn-primary:active {
            transform: scale(.97);
        }

        .btn-primary:disabled {
            opacity: .5;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* ---------- Alert ---------- */
        .forum-alert {
            border-radius: 12px;
            padding: .85rem 1.1rem;
            margin-bottom: 1.25rem;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-left: 5px solid var(--ml-green);
            color: #1f4d33;
            font-size: 0.84rem;
            line-height: 1.6;
        }

        .forum-alert-error {
            background: #fef2f2;
            border-color: #fecaca;
            border-left-color: #ef4444;
            color: #991b1b;
        }

        /* ---------- Filter ---------- */
        .forum-filter {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .8rem;
            padding: 1.1rem;
            margin-bottom: 1.5rem;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
        }

        .forum-filter label strong {
            color: #14301f;
        }

        .forum-filter input,
        .forum-filter select,
        .field input,
        .forum-modal-class {
            min-height: 44px;
            border: 1.5px solid #cfe0d4;
            border-radius: 10px;
            padding: .6rem 1rem;
            background: #ffffff;
            color: #14301f;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .forum-filter input:focus,
        .forum-filter select:focus,
        .field input:focus,
        .forum-modal-class:focus,
        .answer-input:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        .forum-filter input {
            min-width: 145px;
            flex: 1 1 200px;
        }

        .forum-filter select {
            min-width: 125px;
        }

        .forum-muted {
            color: #64748b;
            font-size: .8rem;
        }

        /* ---------- Layout ---------- */
        .two-columns {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.25rem;
            align-items: start;
        }

        .two-columns>* {
            min-width: 0;
        }

        @media (max-width: 900px) {
            .two-columns {
                grid-template-columns: 1fr;
            }
        }

        /* Kolom utama transparan supaya kartu diskusi berdiri sendiri seperti kartu materi */
        .two-columns>section.panel {
            background: transparent;
            border: 0;
            box-shadow: none;
            padding: 0;
            margin-bottom: 0;
        }

        aside .panel,
        .side-panel-box {
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            padding: 1.4rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .8rem;
            margin-bottom: 1.1rem;
        }

        .panel-head h2 {
            font-size: 1.15rem;
            margin: 0;
            color: #14301f;
            font-weight: 800;
        }

        .badge,
        .forum-tag {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: .25rem .7rem;
            background: var(--ml-green-soft);
            color: var(--ml-green);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: .03em;
        }

        .forum-tag-locked {
            background: #f1f5f9;
            color: #475569;
        }

        /* ---------- Kartu Thread ---------- */
        #forum-topics {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        #forum-topics .thread {
            padding: 1.4rem;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            margin: 0;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            transition: transform .25s cubic-bezier(.2, .8, .2, 1), box-shadow .25s, border-color .25s;
            animation: miUp .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        #forum-topics .thread:nth-child(1) {
            animation-delay: 0ms;
        }

        #forum-topics .thread:nth-child(2) {
            animation-delay: 60ms;
        }

        #forum-topics .thread:nth-child(3) {
            animation-delay: 120ms;
        }

        #forum-topics .thread:nth-child(4) {
            animation-delay: 180ms;
        }

        #forum-topics .thread:nth-child(5) {
            animation-delay: 240ms;
        }

        #forum-topics .thread:nth-child(n+6) {
            animation-delay: 300ms;
        }

        @keyframes miUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        #forum-topics .thread:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(8, 101, 52, .08);
            border-color: #b5d9c1;
        }

        .thread-title {
            display: flex;
            gap: .9rem;
            align-items: center;
            margin-bottom: .9rem;
        }

        .avatar-large {
            width: 44px;
            height: 44px;
            background: var(--ml-green-soft);
            color: var(--ml-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.96rem;
            flex-shrink: 0;
        }

        .thread-title strong {
            display: block;
            font-size: 0.92rem;
            color: #14301f;
        }

        .thread-title small {
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 500;
        }

        .thread h3 {
            font-size: 1.15rem;
            color: #14301f;
            margin: 0 0 .5rem 0;
            line-height: 1.35;
        }

        .thread h3 .forum-link {
            color: #14301f;
            font-weight: 800;
            font-size: inherit;
        }

        .thread h3 .forum-link:hover {
            color: var(--ml-green);
            text-decoration: none;
        }

        .thread p {
            font-size: 0.9rem;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .forum-tags {
            display: inline-flex;
            gap: .35rem;
            align-items: center;
            margin-left: .45rem;
            vertical-align: middle;
        }

        .forum-link {
            border: 0;
            padding: 0;
            background: transparent;
            cursor: pointer;
            color: var(--ml-green);
            font: inherit;
            font-weight: 700;
            text-align: left;
        }

        .forum-link:hover {
            text-decoration: underline;
        }

        /* Kontrol moderasi */
        .forum-controls {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .5rem;
            margin: .75rem 0;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
        }

        .forum-controls form {
            margin: 0;
        }

        .forum-small-btn {
            display: inline-flex;
            align-items: center;
            padding: .55rem .95rem;
            border: 0;
            background: var(--ml-green-light);
            color: var(--ml-green);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .15s;
        }

        .forum-small-btn:hover {
            background: var(--ml-green-soft);
        }

        .forum-small-btn:active {
            transform: scale(.97);
        }

        .forum-danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .forum-danger:hover {
            background: #fee2e2;
        }

        /* Balasan */
        .reply {
            margin-top: .8rem;
            padding: 1rem 1.2rem;
            background: #f6fbf8;
            border-left: 4px solid var(--ml-green);
            border-radius: 0 12px 12px 0;
        }

        .reply b {
            display: block;
            font-size: 0.84rem;
            color: #14301f;
            margin-bottom: 0.4rem;
        }

        .reply p {
            font-size: 0.86rem;
            color: #475569;
            margin: 0;
        }

        .forum-reply-card {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
        }

        .forum-reply-card>div {
            min-width: 0;
            flex: 1;
        }

        .forum-reply-delete {
            align-self: flex-start;
        }

        .reply-empty {
            margin-top: 1rem;
            color: #64748b;
        }

        /* ---------- Empty State ---------- */
        .forum-empty {
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-radius: 16px;
            padding: 2.5rem 1.5rem;
            text-align: center;
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
            animation: miUp .4s ease both;
        }

        aside .forum-empty {
            padding: 1.5rem 1rem;
            font-size: 0.86rem;
        }

        /* ---------- Sidebar ---------- */
        .side-panel-box h3 {
            font-size: 1rem;
            color: #14301f;
            font-weight: 800;
            margin: 0 0 1rem;
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
            width: 100%;
            padding: 1rem;
            background: #f6fbf8;
            border: 1px solid #dfe9e2;
            cursor: pointer;
            border-radius: 12px;
            text-decoration: none;
            text-align: left;
            font: inherit;
            color: #334155;
            transition: all 0.2s;
        }

        .topic-item:hover {
            background: #ffffff;
            border-color: #b5d9c1;
            box-shadow: 0 6px 16px rgba(8, 101, 52, 0.08);
            transform: translateY(-2px);
        }

        .topic-item strong {
            display: block;
            font-size: 0.88rem;
            color: #14301f;
            margin-bottom: 0.3rem;
            line-height: 1.3;
        }

        .topic-item small {
            font-size: 0.76rem;
            color: #64748b;
        }

        /* ---------- Pagination ---------- */
        .forum-pages {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-top: 1.75rem;
            color: #475569;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .forum-pages a {
            display: inline-flex;
            align-items: center;
            padding: .55rem .95rem;
            border-radius: 10px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            font-weight: 700;
            text-decoration: none;
            transition: background .2s;
        }

        .forum-pages a:hover {
            background: var(--ml-green-soft);
            text-decoration: none;
        }

        /* ---------- Form ---------- */
        .field label {
            display: block;
            font-weight: 700;
            font-size: 0.88rem;
            margin-bottom: 0.5rem;
            color: #14301f;
        }

        .field input {
            width: 100%;
            margin-bottom: 1rem;
        }

        .forum-modal-class {
            display: block;
            width: 100%;
            margin: 0.4rem 0 1rem;
        }

        .answer-input {
            width: 100%;
            padding: .8rem 1rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 10px;
            color: #14301f;
            font-size: 0.95rem;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        /* ---------- Modal ---------- */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(20, 48, 31, 0.55);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
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
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            max-height: 92vh;
            overflow-y: auto;
            padding: 1.75rem;
            box-shadow: 0 24px 48px rgba(8, 101, 52, .18);
            transform: translateY(20px);
            transition: transform 0.3s;
        }

        .modal-backdrop.active .modal {
            transform: translateY(0);
        }

        .forum-edit-modal {
            max-width: 560px;
        }

        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-head h2 {
            font-size: 1.2rem;
            font-weight: 800;
            color: #14301f;
            margin: 0;
        }

        .modal-head button {
            background: var(--ml-green-light);
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            color: var(--ml-green);
            transition: background .2s;
        }

        .modal-head button:hover {
            background: var(--ml-green-soft);
        }

        /* ---------- Drawer ---------- */
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(20, 48, 31, 0.35);
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
            box-shadow: -8px 0 28px rgba(8, 101, 52, 0.12);
            z-index: 9999;
            transition: right 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            display: flex;
            flex-direction: column;
        }

        .topic-drawer.active {
            right: 0;
        }

        .drawer-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #dfe9e2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--ml-green-light);
        }

        .drawer-header h2 {
            font-size: 1.1rem;
            font-weight: 800;
            margin: 0;
            color: #14301f;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-close-drawer {
            background: #ffffff;
            border: 1px solid #cfe0d4;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ml-green);
            transition: background .2s;
        }

        .btn-close-drawer:hover {
            background: var(--ml-green-soft);
        }

        .drawer-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .drawer-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #dfe9e2;
            background: #f6fbf8;
        }

        /* ---------- Ikon SVG ---------- */
        .ic {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .forum-tag {
            gap: .3rem;
        }

        .forum-tag .ic {
            width: 12px;
            height: 12px;
        }

        .side-panel-box h3 .ic {
            width: 18px;
            height: 18px;
            color: var(--ml-green);
        }

        .drawer-header h2 .ic {
            width: 20px;
            height: 20px;
            color: var(--ml-green);
        }

        .forum-pages a {
            gap: .4rem;
        }

        .forum-pages a .ic {
            width: 14px;
            height: 14px;
        }

        .modal-head button {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .forum-locked-notice {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin: 0;
        }

        /* ---------- Topik Baru (sisi guru): kartu pesan masuk ---------- */
        .side-caption {
            margin: -.5rem 0 1rem;
        }

        .inbox-item {
            display: flex;
            flex-direction: column;
            gap: .7rem;
            padding: 1rem;
            background: #f6fbf8;
            border: 1px solid #dfe9e2;
            border-radius: 12px;
            transition: border-color .2s, box-shadow .2s;
        }

        .inbox-item:hover {
            border-color: #b5d9c1;
            box-shadow: 0 6px 16px rgba(8, 101, 52, .08);
        }

        .inbox-head {
            display: flex;
            align-items: center;
            gap: .7rem;
        }

        .avatar-small {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--ml-green-soft);
            color: var(--ml-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: .8rem;
            flex-shrink: 0;
        }

        .inbox-head strong {
            display: block;
            font-size: .86rem;
            color: #14301f;
        }

        .inbox-head small {
            font-size: .74rem;
            color: #64748b;
        }

        .inbox-title {
            margin: 0;
            font-size: .88rem;
            font-weight: 700;
            color: #14301f;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .inbox-snippet {
            margin: 0;
            font-size: .82rem;
            color: #475569;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .reply-bar {
            display: flex;
            align-items: center;
            gap: .6rem;
            width: 100%;
            padding: .65rem .9rem;
            background: #ffffff;
            border: 1.5px solid #cfe0d4;
            border-radius: 10px;
            color: #64748b;
            font: inherit;
            font-size: .84rem;
            text-align: left;
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s, color .2s;
        }

        .reply-bar span {
            flex: 1;
        }

        .reply-bar:hover {
            border-color: var(--ml-green);
            color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        /* ---------- Drawer buat topik & tombol tambah ---------- */
        #forum-create-form .answer-input {
            min-height: 180px;
        }

        .empty-add {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .7rem;
            width: 100%;
            padding: 1.5rem 1rem;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-radius: 16px;
            color: #64748b;
            font: inherit;
            font-size: .86rem;
            line-height: 1.5;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, color .2s, background .2s;
        }

        .empty-add-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--ml-green-soft);
            color: var(--ml-green);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s, color .2s, transform .2s;
        }

        .empty-add-icon .ic {
            width: 20px;
            height: 20px;
        }

        .empty-add:hover:not(:disabled) {
            border-color: var(--ml-green);
            color: var(--ml-green);
            background: var(--ml-green-soft);
        }

        .empty-add:hover:not(:disabled) .empty-add-icon {
            background: var(--ml-green);
            color: #ffffff;
            transform: scale(1.06);
        }

        .empty-add:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        @media (prefers-reduced-motion: reduce) {

            #forum-topics .thread,
            .forum-empty {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>

    <div class="page-heading">
        <div>
            <h1>{{ $isTeacher ? 'Kelola Diskusi' : 'Forum Diskusi' }}</h1>
            <p>{{ $isTeacher ? 'Pantau pertanyaan siswa, balas, dan atur diskusi di kelas yang kamu ajar.' : 'Ajukan pertanyaan dan lihat jawaban dari guru atau teman sekelas.' }}
            </p>
        </div>
        <button type="button" class="btn-primary" onclick="openForumModal()" @disabled($classes->isEmpty())>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            {{ $isTeacher ? 'Buat Topik' : 'Buat Pertanyaan' }}
        </button>
    </div>

    @if (session('forum_success'))
        <div class="forum-alert" role="status">{{ session('forum_success') }}</div>
    @endif
    @if ($errors->new_thread->any())
        <div class="forum-alert forum-alert-error" role="alert">{{ $errors->new_thread->first() }}</div>
    @endif
    @if ($errors->forum_reply->any())
        <div class="forum-alert forum-alert-error" role="alert">{{ $errors->forum_reply->first() }}</div>
    @endif
    @if ($errors->edit_thread->any())
        <div class="forum-alert forum-alert-error" role="alert">{{ $errors->edit_thread->first() }}</div>
    @endif

    <form method="GET" action="{{ route('forum.index', [], false) }}" class="forum-filter">
        <label for="forum-filter-class"><strong>Pilih kelas:</strong></label>
        <select name="class_id" id="forum-filter-class">
            <option value="">Semua kelas saya</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected($selectedClassId === (int) $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        <label for="forum-search" class="forum-muted">Cari:</label>
        <input id="forum-search" name="q" type="search" maxlength="100" value="{{ $query }}"
            placeholder="Judul atau isi topik">
        <label for="forum-mode" class="forum-muted">Lihat:</label>
        <select name="mode" id="forum-mode">
            <option value="all" @selected($mode === 'all')>Semua topik</option>
            <option value="mine" @selected($mode === 'mine')>{{ $isTeacher ? 'Topik Anda' : 'Topik saya' }}</option>
            <option value="unanswered" @selected($mode === 'unanswered')>Belum dibalas</option>
        </select>
        <button type="submit" class="btn-primary">Cari Topik</button>
    </form>

    <div class="two-columns">
        <!-- Panel diskusi: data langsung dari forum_threads dan forum_comments -->
        <section class="panel">
            <div class="panel-head">
                <h2>{{ $selectedClassId ? 'Diskusi ' . ($classes->firstWhere('id', $selectedClassId)?->name ?? 'Kelas') : 'Diskusi Kelas' }}
                </h2>
                <span class="badge">{{ $threads->total() }} topik aktif</span>
            </div>

            <div id="forum-topics">
                @forelse($threads as $thread)
                    <article class="thread">
                        <div class="thread-title">
                            <span
                                class="avatar-large">{{ mb_strtoupper(mb_substr($thread->author?->name ?? 'P', 0, 1)) }}</span>
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
                            <button type="button" class="forum-link"
                                onclick="openDrawer({{ $thread->id }})">{{ $thread->title }}</button>
                            <span class="forum-tags">
                                @if ($thread->is_pinned)
                                    <span class="forum-tag"><svg class="ic" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 17v5"></path>
                                            <path
                                                d="M9 10.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H8a2 2 0 0 0 0 4 1 1 0 0 1 1 1z">
                                            </path>
                                        </svg> Disematkan</span>
                                @endif
                                @if ($thread->is_locked)
                                    <span class="forum-tag forum-tag-locked"><svg class="ic" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" aria-hidden="true">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2">
                                            </rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg> Dikunci</span>
                                @endif
                            </span>
                        </h3>
                        <p style="white-space:pre-line;overflow-wrap:anywhere;">{{ $thread->body }}</p>
                        @php
                            $canModerateThread =
                                $isModerator &&
                                \App\Support\ForumAccess::canModerate(auth()->user(), (int) $thread->class_id);
                            $isOwnThread = (int) $thread->user_id === (int) auth()->id();
                        @endphp
                        <div class="forum-controls">
                            @if ($isOwnThread && (!$thread->is_locked || $canModerateThread))
                                <button type="button" class="forum-small-btn"
                                    onclick="openForumEdit({{ $thread->id }})">Edit topik</button>
                            @endif
                            @if ($canModerateThread)
                                <form method="POST" action="{{ route('forum.threads.pin', $thread, false) }}">@csrf
                                    @method('PATCH')
                                    <input type="hidden" name="return_path" value="{{ request()->getRequestUri() }}">
                                    <button type="submit" class="forum-small-btn"
                                        title="Tampilkan topik ini sebagai penting (label Disematkan) agar mudah ditemukan">{{ $thread->is_pinned ? 'Lepas sematan' : 'Sematkan' }}</button>
                                </form>
                                <form method="POST" action="{{ route('forum.threads.lock', $thread, false) }}">@csrf
                                    @method('PATCH')
                                    <input type="hidden" name="return_path" value="{{ request()->getRequestUri() }}">
                                    <button type="submit" class="forum-small-btn"
                                        title="Tutup balasan: tidak ada yang bisa membalas, dan pembuat topik tidak bisa mengedit atau menghapusnya">{{ $thread->is_locked ? 'Buka diskusi' : 'Kunci diskusi' }}</button>
                                </form>
                            @endif
                            @if ($canModerateThread || ($isOwnThread && !$thread->is_locked && $thread->comments_count === 0))
                                <form method="POST" action="{{ route('forum.threads.destroy', $thread, false) }}"
                                    onsubmit="return confirm('Hapus topik ini? Balasan yang ada juga akan terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="return_path" value="{{ request()->getRequestUri() }}">
                                    <button type="submit" class="forum-small-btn forum-danger">Hapus topik</button>
                                </form>
                            @endif
                        </div>
                        @foreach ($thread->comments->take(2) as $comment)
                            <div class="reply">
                                <b>{{ $comment->author?->name ?? 'Pengguna' }} ·
                                    {{ in_array($comment->author?->role, ['guru', 'admin'], true) ? 'Guru' : 'Siswa' }}</b>
                                <p style="white-space:pre-line;overflow-wrap:anywhere;">{{ $comment->body }}</p>
                            </div>
                        @endforeach
                        <button type="button" class="forum-link" onclick="openDrawer({{ $thread->id }})"
                            style="margin-top:1rem;">
                            Buka diskusi & balas ({{ $thread->comments_count }} balasan)
                        </button>
                    </article>
                @empty
                    <p class="forum-empty">
                        @if ($classes->isEmpty())
                            Belum ada kelas yang bisa kamu akses. Guru dapat membuat kelas, sedangkan siswa perlu bergabung
                            ke kelas terlebih dahulu.
                        @else
                            Belum ada diskusi untuk pilihan kelas ini. Yuk mulai dengan membuat pertanyaan baru!
                        @endif
                    </p>
                @endforelse
            </div>

            @if ($threads->hasPages())
                <div class="forum-pages">
                    <span>
                        @if ($threads->previousPageUrl())
                            <a href="{{ $threads->previousPageUrl() }}"><svg class="ic" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg> Sebelumnya</a>
                        @endif
                    </span>
                    <span>Halaman {{ $threads->currentPage() }} dari {{ $threads->lastPage() }}</span>
                    <span>
                        @if ($threads->nextPageUrl())
                            <a href="{{ $threads->nextPageUrl() }}">Berikutnya <svg class="ic" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg></a>
                        @endif
                    </span>
                </div>
            @endif
        </section>

        <aside>
            @if ($isTeacher)
                @php
                    $newStudentThreads = $threads
                        ->getCollection()
                        ->filter(
                            fn($item) => (int) $item->comments_count === 0 &&
                                !$item->is_locked &&
                                !in_array($item->author?->role, ['guru', 'admin'], true),
                        )
                        ->take(5);
                @endphp

                <section class="panel side-panel-box">
                    <h3><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg> Topik Anda</h3>
                    <div class="topic-list">
                        @forelse($myThreads as $thread)
                            <button type="button" class="topic-item" onclick="openDrawer({{ $thread->id }})">
                                <strong>{{ $thread->title }}</strong>
                                <small>{{ $thread->comments_count }} balasan ·
                                    {{ $thread->schoolClass?->name ?? 'Kelas' }}</small>
                            </button>
                        @empty
                            <button type="button" class="empty-add" onclick="openForumModal()"
                                @disabled($classes->isEmpty()) aria-label="Tambah topik baru">
                                <span class="empty-add-icon"><svg class="ic" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg></span>
                                <span>Belum ada topik. Klik untuk menambah topik baru.</span>
                            </button>
                        @endforelse
                    </div>
                </section>

                <section class="panel side-panel-box">
                    <h3><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                            <path
                                d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z">
                            </path>
                        </svg> Topik Baru</h3>
                    <p class="forum-muted side-caption">Pertanyaan siswa yang belum dibalas.</p>
                    <div class="topic-list">
                        @forelse($newStudentThreads as $thread)
                            <div class="inbox-item">
                                <div class="inbox-head">
                                    <span
                                        class="avatar-small">{{ mb_strtoupper(mb_substr($thread->author?->name ?? 'P', 0, 1)) }}</span>
                                    <div>
                                        <strong>{{ $thread->author?->name ?? 'Siswa' }}</strong>
                                        <small>{{ $thread->schoolClass?->name ?? 'Kelas' }} ·
                                            {{ $thread->created_at?->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <p class="inbox-title">{{ $thread->title }}</p>
                                <p class="inbox-snippet">{{ \Illuminate\Support\Str::limit($thread->body, 80) }}</p>
                                <button type="button" class="reply-bar" onclick="openDrawer({{ $thread->id }})">
                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <polyline points="9 14 4 9 9 4"></polyline>
                                        <path d="M20 20v-7a4 4 0 0 0-4-4H4"></path>
                                    </svg>
                                    <span>Balas pesan...</span>
                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p class="forum-empty">Tidak ada pertanyaan baru dari siswa pada halaman ini.</p>
                        @endforelse
                    </div>
                </section>
            @else
                <section class="panel side-panel-box">
                    <h3><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg> Topik dari Guru</h3>
                    <div class="topic-list">
                        @forelse($teacherThreads as $thread)
                            <button type="button" class="topic-item" onclick="openDrawer({{ $thread->id }})">
                                <strong>{{ $thread->title }}</strong>
                                <small>Oleh {{ $thread->author?->name ?? 'Guru' }} · {{ $thread->comments_count }}
                                    balasan</small>
                            </button>
                        @empty
                            <p class="forum-empty">Belum ada topik guru pada halaman ini.</p>
                        @endforelse
                    </div>
                </section>

                <section class="panel side-panel-box">
                    <h3><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg> Topik yang Saya Buat</h3>
                    <div class="topic-list">
                        @forelse($myThreads as $thread)
                            <button type="button" class="topic-item" onclick="openDrawer({{ $thread->id }})">
                                <strong>{{ $thread->title }}</strong>
                                <small>{{ $thread->comments_count }} balasan ·
                                    {{ $thread->schoolClass?->name ?? 'Kelas' }}</small>
                            </button>
                        @empty
                            <p class="forum-empty">Belum ada pertanyaan yang kamu buat pada halaman ini.</p>
                        @endforelse
                    </div>
                </section>
            @endif
        </aside>
    </div>

    <!-- Drawer buat topik: muncul dari samping seperti drawer balasan, terhubung ke POST Laravel -->
    <div class="drawer-overlay" id="forum-create-overlay" onclick="closeForumModal()"></div>
    <form class="topic-drawer" id="forum-create-form" method="POST"
        action="{{ route('forum.threads.store', [], false) }}" role="dialog" aria-modal="true"
        aria-label="{{ $isTeacher ? 'Buat topik baru' : 'Buat pertanyaan baru' }}">
        @csrf
        <div class="drawer-header">
            <h2><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg> {{ $isTeacher ? 'Buat Topik Baru' : 'Buat Pertanyaan Baru' }}</h2>
            <button type="button" class="btn-close-drawer" aria-label="Tutup" onclick="closeForumModal()"><svg
                    class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg></button>
        </div>
        <div class="drawer-content">
            <div class="field">
                <label for="forum-new-class">Kelas Diskusi</label>
                <select id="forum-new-class" name="class_id" class="forum-modal-class" required>
                    <option value="">Pilih kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected((string) old('class_id', $selectedClassId ?? ($classes->count() === 1 ? $classes->first()->id : '')) === (string) $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
                <label for="forum-new-title">Judul Diskusi</label>
                <input id="forum-new-title" name="title" type="text" maxlength="255" required
                    value="{{ old('title') }}"
                    placeholder="{{ $isTeacher ? 'Contoh: Latihan soal pecahan minggu ini' : 'Contoh: Bingung cara mengubah pecahan ke desimal' }}">
            </div>
            <div class="field">
                <label for="forum-new-body">{{ $isTeacher ? 'Isi Topik' : 'Isi Pertanyaan' }}</label>
                <textarea id="forum-new-body" name="body" class="answer-input" maxlength="10000" required
                    placeholder="{{ $isTeacher ? 'Tuliskan materi atau pengumuman diskusi di sini...' : 'Tuliskan detail pertanyaanmu di sini...' }}">{{ old('body') }}</textarea>
            </div>
        </div>
        <div class="drawer-footer">
            <button type="submit" class="btn-primary"
                style="width:100%;justify-content:center;">{{ $isTeacher ? 'Publikasikan Topik' : 'Publikasikan Pertanyaan' }}</button>
        </div>
    </form>

    <!-- Edit topik asli, tidak mengubah class_id maupun pemilik topik -->
    <div class="modal-backdrop" id="forum-edit-modal" role="presentation">
        <form class="modal forum-edit-modal" id="forum-edit-form" method="POST" action="#">
            @csrf @method('PUT')
            <input type="hidden" name="return_path" value="{{ request()->getRequestUri() }}">
            <div class="modal-head">
                <h2>Edit Topik Diskusi</h2>
                <button type="button" aria-label="Tutup" onclick="closeForumEdit()"><svg class="ic"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg></button>
            </div>
            <div class="field">
                <label for="forum-edit-title">Judul Topik</label>
                <input id="forum-edit-title" name="title" maxlength="255" required>
                <label for="forum-edit-body">Isi Topik</label>
                <textarea class="answer-input" id="forum-edit-body" name="body" maxlength="10000" required></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">Simpan
                Perubahan</button>
        </form>
    </div>

    <!-- Drawer detail diskusi tetap dipakai, kini memuat judul dan komentar asli -->
    <div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawer()"></div>
    <div class="topic-drawer" id="topic-drawer" role="dialog" aria-label="Detail diskusi" aria-modal="true">
        <div class="drawer-header">
            <h2><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg> Detail Diskusi</h2>
            <button type="button" class="btn-close-drawer" aria-label="Tutup diskusi" onclick="closeDrawer()"><svg
                    class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg></button>
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
                <p id="forum-locked-notice" class="forum-muted forum-locked-notice" style="display:none;"><svg
                        class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>Diskusi dikunci guru. Balasan baru tidak diizinkan.</span>
                </p>
                <div id="forum-reply-fields">
                    <label for="forum-reply-body" style="display:block;margin-bottom:.5rem;font-weight:600;">Tulis
                        Balasan</label>
                    <textarea id="forum-reply-body" name="body" class="answer-input" style="min-height:80px;margin-bottom:.5rem;"
                        maxlength="10000" required placeholder="Balas diskusi ini..."></textarea>
                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">Kirim
                        Balasan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const forumThreadData = {{ \Illuminate\Support\Js::from($drawerData) }};

        function openForumModal() {
            closeDrawer();
            document.getElementById('forum-create-overlay').classList.add('active');
            document.getElementById('forum-create-form').classList.add('active');
            setTimeout(function() {
                const title = document.getElementById('forum-new-title');
                if (title) title.focus();
            }, 350);
        }

        function closeForumModal() {
            document.getElementById('forum-create-overlay').classList.remove('active');
            document.getElementById('forum-create-form').classList.remove('active');
        }

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

        document.getElementById('forum-edit-modal').addEventListener('click', function(event) {
            if (event.target === this) closeForumEdit();
        });

        function openDrawer(id) {
            const thread = forumThreadData[String(id)];
            if (!thread) return;
            closeForumModal();

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

            thread.comments.forEach(function(item) {
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
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = {{ \Illuminate\Support\Js::from(csrf_token()) }};
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    const returnPath = document.createElement('input');
                    returnPath.type = 'hidden';
                    returnPath.name = 'return_path';
                    returnPath.value = window.location.pathname + window.location.search;
                    const button = document.createElement('button');
                    button.type = 'submit';
                    button.className = 'forum-small-btn forum-danger';
                    button.textContent = 'Hapus';
                    form.addEventListener('submit', function(event) {
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

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeForumModal();
                closeForumEdit();
                closeDrawer();
            }
        });

        @if ($errors->new_thread->any())
            openForumModal();
        @endif

        @if ($openThreadId !== null && array_key_exists((string) $openThreadId, $drawerData))
            openDrawer({{ (int) $openThreadId }});
        @endif
    </script>
@endsection

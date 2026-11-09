@extends('layouts.app')

@section('title', 'Materi Pembelajaran | MathLeaf')
@section('header_title', $isTeacher ? 'Kelola Materi' : 'Materi Pembelajaran')

@section('content')
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

        /* ---------- Header & Alert ---------- */
        .mi-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
        }

        .mi-title {
            font-size: clamp(1.44rem, 2.4vw, 1.84rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .4rem;
            line-height: 1.2;
        }

        .mi-subtitle {
            color: #475569;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.5;
        }

        .mi-alert {
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

        .mi-alert.error {
            background: #fef2f2;
            border-color: #fecaca;
            border-left-color: #ef4444;
            color: #991b1b;
        }

        /* ---------- Filter ---------- */
        .mi-filter {
            display: flex;
            flex-wrap: wrap;
            gap: .8rem;
            padding: 1.1rem;
            margin-bottom: 1.5rem;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
        }

        .mi-input,
        .mi-select {
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

        .mi-input:focus,
        .mi-select:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        .mi-filter .mi-input[name=q] {
            flex: 1 1 240px;
        }

        /* ---------- Grid & Kartu ---------- */
        .mi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.25rem;
        }

        .mi-card {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            padding: 1.4rem;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            transition: transform .25s cubic-bezier(.2, .8, .2, 1), box-shadow .25s, border-color .25s;
            animation: miUp .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        /* Memberikan delay berjenjang untuk animasi kartu */
        .mi-card:nth-child(1) {
            animation-delay: 0ms;
        }

        .mi-card:nth-child(2) {
            animation-delay: 60ms;
        }

        .mi-card:nth-child(3) {
            animation-delay: 120ms;
        }

        .mi-card:nth-child(4) {
            animation-delay: 180ms;
        }

        .mi-card:nth-child(5) {
            animation-delay: 240ms;
        }

        .mi-card:nth-child(6) {
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

        .mi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(8, 101, 52, .08);
            border-color: #b5d9c1;
        }

        .mi-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-bottom: .8rem;
        }

        .mi-tag {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: .25rem .7rem;
            background: var(--ml-green-soft);
            color: var(--ml-green);
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: capitalize;
            letter-spacing: .03em;
        }

        .mi-tag.draft {
            background: #ffedd5;
            color: #c2410c;
        }

        .mi-card h2 {
            margin: 0 0 .5rem;
            font-size: 1.15rem;
            line-height: 1.35;
        }

        .mi-card h2 a {
            text-decoration: none;
            color: #14301f;
        }

        .mi-card h2 a:hover {
            color: var(--ml-green);
        }

        .mi-card p {
            color: #475569;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0 0 1.2rem;
            flex: 1;
            /* Mendorong footer ke bawah */
        }

        /* Progress Bar Siswa */
        .mi-progress-wrap {
            margin-top: auto;
            margin-bottom: 1rem;
        }

        .mi-progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.76rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: .4rem;
        }

        .mi-progress-label span:last-child {
            color: var(--ml-green);
        }

        .mi-track {
            height: 8px;
            background: #e3efe6;
            border-radius: 999px;
            overflow: hidden;
        }

        .mi-track div {
            height: 100%;
            background: linear-gradient(90deg, #22a35a, #086534);
            border-radius: 999px;
            transition: width 1s cubic-bezier(.2, .8, .2, 1);
        }

        /* Footer Kartu */
        .mi-card-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .8rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
            margin-top: auto;
        }

        .mi-date {
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 500;
        }

        /* ---------- Tombol ---------- */
        .mi-btn {
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

        .mi-btn:hover {
            background: var(--ml-green-hover);
            box-shadow: 0 6px 16px rgba(8, 101, 52, .25);
            color: #ffffff;
        }

        .mi-btn:active {
            transform: scale(.97);
        }

        .mi-btn.soft {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .mi-btn.soft:hover {
            background: var(--ml-green-soft);
            box-shadow: none;
            color: var(--ml-green);
        }

        .mi-btn.sm {
            padding: .55rem .95rem;
            font-size: 0.8rem;
        }

        /* ---------- Empty State ---------- */
        .mi-empty {
            grid-column: 1 / -1;
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 3rem 1.5rem;
            text-align: center;
            color: #64748b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: miUp .4s ease both;
        }

        .mi-empty svg {
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .mi-empty strong {
            display: block;
            color: #14301f;
            font-size: 1.15rem;
            margin-bottom: .4rem;
        }

        .mi-empty p {
            margin: 0;
            font-size: 0.95rem;
        }

        /* ---------- Pagination ---------- */
        .mi-pager {
            margin-top: 2rem;
        }

        @media (prefers-reduced-motion: reduce) {

            .mi-card,
            .mi-empty {
                animation: none !important;
                transition: none !important;
            }

            .mi-track div {
                transition: none;
            }
        }
    </style>

    <div class="mi-header">
        <div>
            <h1 class="mi-title">{{ $isTeacher ? 'Kelola Materi' : 'Materi Pembelajaran' }}</h1>
            <p class="mi-subtitle">
                {{ $isTeacher ? 'Materi dan draf untuk kelas yang kamu ajar.' : 'Pilih materi untuk mulai belajar.' }}</p>
        </div>
        @if ($isTeacher)
            <a class="mi-btn" href="{{ route('teacher.materials.create') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Buat Materi
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="mi-alert" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mi-alert error" role="alert">{{ $errors->first() }}</div>
    @endif

    <form class="mi-filter" method="GET" action="{{ route('materi.index') }}">
        <input type="search" name="q" class="mi-input" value="{{ $search }}"
            placeholder="Cari judul atau ringkasan..." aria-label="Cari materi">

        @if ($classes->isNotEmpty())
            <select name="class_id" class="mi-select" aria-label="Pilih kelas">
                <option value="">Semua kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected((string) $selectedClassId === (string) $class->id)>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        @endif

        <select name="status" class="mi-select" aria-label="Filter status">
            @if ($isTeacher)
                <option value="" @selected(request('status') === null || request('status') === '')>Semua status</option>
                <option value="published" @selected(request('status') === 'published')>Terbit</option>
                <option value="draft" @selected(request('status') === 'draft')>Draf</option>
            @else
                <option value="" @selected(request('status') === null || request('status') === '')>Semua progres</option>
                <option value="undone" @selected(request('status') === 'undone')>Belum selesai</option>
                <option value="done" @selected(request('status') === 'done')>Selesai</option>
            @endif
        </select>

        <button class="mi-btn soft" type="submit">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            Terapkan Filter
        </button>
    </form>

    <div class="mi-grid" aria-label="Daftar materi">
        @forelse ($materials as $material)
            @php
                $progressPercent = (int) ($material->progress->first()->progress_percent ?? 0);
            @endphp
            <article class="mi-card">
                <div class="mi-meta">
                    <span class="mi-tag">{{ $material->type }}</span>
                    @if ($material->schoolClass)
                        <span class="mi-tag">{{ $material->schoolClass->name }}</span>
                    @endif
                    @if ($isTeacher)
                        <span class="mi-tag {{ $material->is_published ? '' : 'draft' }}">
                            {{ $material->is_published ? 'Terbit' : 'Draf' }}
                        </span>
                    @else
                        <span style="font-size:0.85rem; color:#64748b; align-self:center; font-weight:600;">
                            Oleh {{ $material->author->name ?? 'Guru' }}
                        </span>
                    @endif
                </div>

                <h2><a href="{{ route('materi.show', $material) }}">{{ $material->title }}</a></h2>

                @if ($material->description)
                    <p>{{ \Illuminate\Support\Str::limit($material->description, 120) }}</p>
                @else
                    <p>Belum ada ringkasan untuk materi ini.</p>
                @endif

                @unless ($isTeacher)
                    <div class="mi-progress-wrap">
                        <div class="mi-progress-label">
                            <span>Progres belajar</span>
                            <span>{{ $progressPercent }}%</span>
                        </div>
                        <div class="mi-track" role="progressbar" aria-label="Progres materi" aria-valuemin="0"
                            aria-valuemax="100" aria-valuenow="{{ $progressPercent }}">
                            <div style="width:{{ $progressPercent }}%"></div>
                        </div>
                    </div>
                @endunless

                <div class="mi-card-foot">
                    <span class="mi-date">
                        {{ $material->created_at?->translatedFormat('d M Y') }}
                    </span>
                    <a class="mi-btn soft sm" href="{{ route('materi.show', $material) }}">
                        @if ($isTeacher)
                            Detail
                        @else
                            {{ $progressPercent === 100 ? 'Buka Kembali' : 'Mulai Belajar' }}
                        @endif
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </article>
        @empty
            <div class="mi-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    <path d="M11 8v6"></path>
                    <path d="M8 11h6"></path>
                </svg>
                <strong>Belum ada materi yang cocok.</strong>
                <p>{{ $isTeacher ? 'Buat materi baru atau ubah filter pencarian di atas.' : 'Materi yang diterbitkan untuk kelasmu akan muncul di sini.' }}
                </p>
            </div>
        @endforelse
    </div>

    <div class="mi-pager">
        {{ $materials->links() }}
    </div>
@endsection

@extends('layouts.app')

{{-- Letakkan di: resources/views/pages/guru/materi-form.blade.php (sesuaikan folder) --}}

@php
    abort_unless(Auth::check() && Auth::user()->role === 'guru', 403);

    $material = $material ?? null;
    $isEdit = $material && isset($material->id);
    $Route = \Illuminate\Support\Facades\Route::class;

    // Kelas milik guru ini
    $myClasses = \Illuminate\Support\Facades\DB::table('classes')
        ->where('teacher_id', Auth::id())
        ->select('id', 'name', 'school_year')
        ->get();
    $selectedClass = old('class_id', $material->class_id ?? session('kelas_aktif_id'));

    // Nilai awal (old() dipakai saat validasi server gagal)
    $v = fn($key, $default = '') => old($key, $material->{$key} ?? $default);
    $formAction = $isEdit
        ? ($Route::has('teacher.materials.update')
            ? route('teacher.materials.update', $material->id)
            : '#')
        : ($Route::has('teacher.materials.store')
            ? route('teacher.materials.store')
            : '#');
    $hasFile = $isEdit && !empty($material->file_path);
@endphp

@section('title', ($isEdit ? 'Edit Materi' : 'Tambah Materi') . ' | MathLeaf')
@section('header_title', $isEdit ? 'Edit Materi' : 'Tambah Materi')

@section('content')
    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-soft: #dff3e5;
        }

        .app-content {
            overflow-x: visible;
        }

        .mf-back {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: #64748b;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: .9rem;
            transition: color .15s, transform .15s;
        }

        .mf-back:hover {
            color: var(--ml-green);
            transform: translateX(-3px);
        }

        .mf-title {
            font-size: clamp(1.8rem, 3vw, 2.3rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .4rem;
        }

        .mf-sub {
            color: #475569;
            font-size: 1.1rem;
            margin: 0 0 1.6rem;
        }

        .mf-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 2rem;
            align-items: start;
        }

        .mf-main {
            display: flex;
            flex-direction: column;
            gap: 1.6rem;
            min-width: 0;
        }

        @media (max-width: 960px) {
            .mf-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .mf-side {
                position: static !important;
            }
        }

        .mf-alert {
            padding: .9rem 1.1rem;
            border-radius: 12px;
            margin-bottom: 1.2rem;
            font-size: 1.02rem;
        }

        .mf-alert.ok {
            background: var(--ml-green-light);
            border: 1px solid #bfe0c9;
            color: #1f4d33;
        }

        .mf-alert.bad {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .mf-card {
            background: #fff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            scroll-margin-top: 90px;
            animation: mfUp .5s cubic-bezier(.2, .8, .2, 1) both;
            animation-delay: var(--d, 0ms);
        }

        @keyframes mfUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .mf-head {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #e8f0ea;
        }

        .mf-head .ic {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .mf-head h2 {
            margin: 0;
            font-size: 1.25rem;
            color: #14301f;
        }

        .mf-head small {
            display: block;
            color: #64748b;
            font-size: .95rem;
            margin-top: 2px;
        }

        .mf-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .mf-grid2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 640px) {
            .mf-grid2 {
                grid-template-columns: 1fr;
            }
        }

        .mf-field label,
        .mf-label {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            font-size: 1rem;
            font-weight: 700;
            color: #14301f;
            margin-bottom: .45rem;
        }

        .mf-label em,
        .mf-field label em {
            font-style: normal;
            font-weight: 500;
            font-size: .88rem;
            color: #94a3b8;
        }

        .mf-field .req {
            color: #dc2626;
        }

        .mf-input,
        .mf-select,
        .mf-textarea {
            width: 100%;
            padding: .8rem 1rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1.05rem;
            color: #14301f;
            background: #fff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .mf-textarea {
            min-height: 96px;
            resize: vertical;
            line-height: 1.55;
        }

        .mf-input:focus,
        .mf-select:focus,
        .mf-textarea:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        .mf-input.invalid,
        .mf-select.invalid {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .1);
        }

        .mf-hint {
            color: #64748b;
            font-size: .92rem;
            margin: .4rem 0 0;
        }

        .mf-err {
            color: #dc2626;
            font-size: .92rem;
            font-weight: 600;
            margin: .4rem 0 0;
        }

        .mf-slug {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-top: .5rem;
            font-size: .95rem;
            color: #64748b;
            flex-wrap: wrap;
        }

        .mf-slug input {
            flex: 1;
            min-width: 160px;
            border: 0;
            background: transparent;
            font-family: var(--font-mono, monospace);
            font-size: .95rem;
            color: var(--ml-green);
            padding: .2rem .4rem;
            border-radius: 6px;
        }

        .mf-slug input:not([readonly]) {
            background: #fff;
            border: 1.5px solid #cfe0d4;
            outline: none;
        }

        .mf-link-btn {
            border: 0;
            background: transparent;
            color: var(--ml-green);
            font-weight: 700;
            font-size: .95rem;
            cursor: pointer;
            padding: 0;
        }

        /* ---------- Editor ---------- */
        .mf-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            padding: .6rem;
            background: #f4faf6;
            border: 1.5px solid #cfe0d4;
            border-bottom: 0;
            border-radius: 12px 12px 0 0;
        }

        .mf-tb {
            border: 1px solid transparent;
            background: transparent;
            color: #2f5a42;
            font-family: inherit;
            font-size: .95rem;
            font-weight: 700;
            padding: .4rem .7rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background .15s, color .15s;
        }

        .mf-tb:hover {
            background: var(--ml-green-soft);
        }

        .mf-tb.on {
            background: var(--ml-green);
            color: #fff;
        }

        .mf-tb.accent {
            background: #fff;
            border-color: #cfe0d4;
        }

        .mf-sep {
            width: 1px;
            background: #cfe0d4;
            margin: .2rem .3rem;
        }

        .mf-editor {
            min-height: 360px;
            padding: 1.2rem 1.4rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 0 0 12px 12px;
            font-size: 1.08rem;
            line-height: 1.8;
            color: #334155;
            outline: none;
            overflow-wrap: anywhere;
        }

        .mf-editor:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        .mf-editor.is-empty::before {
            content: attr(data-placeholder);
            color: #94a3b8;
            pointer-events: none;
            position: absolute;
        }

        .mf-editor h3 {
            font-size: 1.25rem;
            color: #14301f;
            margin: 1.2rem 0 .4rem;
        }

        .mf-editor p {
            margin: 0 0 .9rem;
        }

        .ml-callout {
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-left: 5px solid var(--ml-green);
            border-radius: 12px;
            padding: .8rem 1.1rem;
            margin: 1rem 0;
            color: #1f4d33;
        }

        .ml-callout b {
            color: var(--ml-green);
        }

        .ml-example {
            display: inline-block;
            background: #fffbea;
            border: 1px solid #f6e7a6;
            border-radius: 12px;
            padding: .6rem 1rem;
            margin: .3rem .5rem .3rem 0;
            font-weight: 700;
            color: #14301f;
        }

        .ml-example span {
            display: block;
            font-size: .78rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #8a6d00;
        }

        .ml-numline {
            position: relative;
            margin: 1rem 0;
            background: #f6fbf7;
            border: 1px solid #e0eee4;
            border-radius: 12px;
            user-select: none;
        }

        .ml-numline svg {
            display: block;
            width: 100%;
            height: auto;
        }

        .ml-nl-del {
            position: absolute;
            top: 6px;
            right: 8px;
            border: 0;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            background: #fff;
            color: #94a3b8;
            font-size: 1.1rem;
            line-height: 1;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .12);
        }

        .ml-nl-del:hover {
            color: #dc2626;
        }

        .mf-editor-foot {
            display: flex;
            justify-content: space-between;
            color: #64748b;
            font-size: .92rem;
            margin-top: .6rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* popover garis bilangan */
        .mf-pop {
            display: none;
            gap: 1rem;
            padding: 1rem 1.1rem;
            background: #fff;
            border: 1.5px solid #b5d9c1;
            border-radius: 12px;
            margin: .6rem 0;
            box-shadow: 0 10px 26px rgba(8, 101, 52, .12);
        }

        .mf-pop.open {
            display: block;
            animation: mfUp .3s ease both;
        }

        .mf-pop-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1.6fr;
            gap: .8rem;
        }

        @media (max-width: 640px) {
            .mf-pop-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        .mf-pop .mf-input {
            padding: .55rem .8rem;
            font-size: 1rem;
        }

        .mf-pop-prev {
            margin: .8rem 0;
            min-height: 60px;
        }

        .mf-pop-actions {
            display: flex;
            gap: .6rem;
            justify-content: flex-end;
        }

        /* ---------- Lampiran ---------- */
        .mf-yt {
            display: none;
            align-items: center;
            gap: 1rem;
            margin-top: .8rem;
            padding: .7rem;
            border: 1px solid #cfe8d6;
            border-radius: 12px;
            background: #f6fbf7;
        }

        .mf-yt.show {
            display: flex;
            animation: mfUp .35s ease both;
        }

        .mf-yt-thumb {
            position: relative;
            width: 160px;
            aspect-ratio: 16/9;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: #cfe8d6;
        }

        .mf-yt-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .mf-yt-thumb i {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
        }

        .mf-yt-thumb i::after {
            content: '';
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .92) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='38' height='38' viewBox='0 0 24 24'%3E%3Cpolygon points='9.5 7 17 12 9.5 17' fill='%23086534'/%3E%3C/svg%3E") center/100% no-repeat;
        }

        .mf-yt b {
            display: block;
            color: #14301f;
        }

        .mf-yt small {
            color: #64748b;
        }

        .mf-drop {
            border: 2px dashed #b5d9c1;
            border-radius: 14px;
            padding: 1.6rem 1rem;
            text-align: center;
            background: #fbfdfb;
            cursor: pointer;
            transition: background .2s, border-color .2s, transform .2s;
        }

        .mf-drop:hover,
        .mf-drop.over {
            background: var(--ml-green-light);
            border-color: var(--ml-green);
        }

        .mf-drop.over {
            transform: scale(1.01);
        }

        .mf-drop b {
            display: block;
            color: #14301f;
            font-size: 1.05rem;
        }

        .mf-drop small {
            color: #64748b;
            font-size: .92rem;
        }

        .mf-drop svg {
            color: var(--ml-green);
            margin-bottom: .4rem;
        }

        .mf-file {
            display: none;
            align-items: center;
            gap: 1rem;
            padding: .9rem 1.1rem;
            border: 1px solid #dfe9e2;
            border-radius: 14px;
            background: #fbfdfb;
        }

        .mf-file.show {
            display: flex;
            animation: mfUp .35s ease both;
        }

        .mf-file .badge {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: #ffe9d6;
            color: #c2410c;
            font-weight: 800;
            font-size: .82rem;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .mf-file b {
            display: block;
            color: #14301f;
            word-break: break-word;
        }

        .mf-file small {
            color: #64748b;
        }

        .mf-file .x {
            margin-left: auto;
        }

        /* ---------- Tombol ---------- */
        .mf-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border: 0;
            border-radius: 10px;
            padding: .75rem 1.3rem;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            background: var(--ml-green);
            color: #fff;
            transition: background .2s, transform .15s, box-shadow .2s;
        }

        .mf-btn:hover {
            background: var(--ml-green-hover);
            box-shadow: 0 6px 16px rgba(8, 101, 52, .25);
        }

        .mf-btn:active {
            transform: scale(.97);
        }

        .mf-btn.block {
            width: 100%;
        }

        .mf-btn.ghost {
            background: transparent;
            color: var(--ml-green);
            border: 1.5px solid #b5d9c1;
        }

        .mf-btn.ghost:hover {
            background: var(--ml-green-light);
            box-shadow: none;
        }

        .mf-btn.sm {
            padding: .45rem .9rem;
            font-size: .92rem;
        }

        .mf-btn[disabled] {
            opacity: .7;
            cursor: progress;
        }

        .mf-spin {
            width: 16px;
            height: 16px;
            border: 2.5px solid rgba(255, 255, 255, .4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: mfSpin .7s linear infinite;
            display: none;
        }

        .mf-btn.loading .mf-spin {
            display: inline-block;
        }

        @keyframes mfSpin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ---------- Sidebar ---------- */
        .mf-side {
            position: sticky;
            top: 90px;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .mf-box {
            background: #fff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            padding: 1.2rem;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
        }

        .mf-box h3 {
            margin: 0 0 .9rem;
            font-size: 1.1rem;
            color: #14301f;
        }

        .mf-switch {
            display: flex;
            align-items: center;
            gap: .9rem;
            cursor: pointer;
            user-select: none;
        }

        .mf-switch input {
            position: absolute;
            opacity: 0;
        }

        .mf-track {
            position: relative;
            width: 50px;
            height: 28px;
            border-radius: 999px;
            background: #cbd5e1;
            transition: background .25s;
            flex-shrink: 0;
        }

        .mf-track::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .25);
            transition: transform .25s cubic-bezier(.2, .8, .2, 1);
        }

        .mf-switch input:checked+.mf-track {
            background: var(--ml-green);
        }

        .mf-switch input:checked+.mf-track::after {
            transform: translateX(22px);
        }

        .mf-switch input:focus-visible+.mf-track {
            outline: 3px solid rgba(8, 101, 52, .3);
        }

        .mf-switch b {
            display: block;
            color: #14301f;
        }

        .mf-switch small {
            color: #64748b;
            font-size: .9rem;
        }

        .mf-check {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: .55rem;
        }

        .mf-check li {
            display: flex;
            align-items: center;
            gap: .7rem;
            font-size: 1rem;
            color: #64748b;
            transition: color .2s;
        }

        .mf-check li i {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            transition: background .25s, border-color .25s, transform .25s;
        }

        .mf-check li i svg {
            opacity: 0;
            transform: scale(.4);
            transition: opacity .2s, transform .25s;
        }

        .mf-check li.ok {
            color: #14301f;
            font-weight: 600;
        }

        .mf-check li.ok i {
            background: var(--ml-green);
            border-color: var(--ml-green);
            transform: scale(1.08);
        }

        .mf-check li.ok i svg {
            opacity: 1;
            transform: none;
        }

        .mf-check li small {
            margin-left: auto;
            font-size: .82rem;
            color: #94a3b8;
        }

        .mf-actions {
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }

        @media (prefers-reduced-motion: reduce) {

            .mf-card,
            .mf-pop.open,
            .mf-yt.show,
            .mf-file.show {
                animation: none !important;
            }

            .mf-track::after,
            .mf-check li i {
                transition: none;
            }
        }
    </style>

    <a href="{{ route('materi.index') }}" class="mf-back">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke Daftar Materi
    </a>
    <h1 class="mf-title">{{ $isEdit ? 'Edit Materi' : 'Tambah Materi Baru' }}</h1>
    <p class="mf-sub">Tulis E-Modul, tambahkan video atau berkas pendukung, lalu terbitkan ke siswa.</p>

    @if (session('success'))
        <div class="mf-alert ok">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mf-alert bad">Ada isian yang perlu diperbaiki. Periksa kolom yang ditandai merah di bawah.</div>
    @endif
    @if ($myClasses->isEmpty())
        <div class="mf-alert bad">
            Kamu belum punya kelas. <a href="{{ route('classroom.home') }}" style="color:inherit;font-weight:700">Buat
                kelas dulu</a> sebelum menambah materi.
        </div>
    @endif

    <form id="mf-form" method="POST" action="{{ $formAction }}" enctype="multipart/form-data" novalidate>
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="mf-layout">
            <div class="mf-main">

                {{-- 1. INFORMASI MATERI: class_id, title, slug, description, category, type --}}
                <section class="mf-card" style="--d:0ms">
                    <div class="mf-head">
                        <span class="ic">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                <polyline points="14 3 14 9 20 9"></polyline>
                                <line x1="8" y1="13" x2="16" y2="13"></line>
                                <line x1="8" y1="17" x2="14" y2="17"></line>
                            </svg>
                        </span>
                        <div>
                            <h2>Informasi Materi</h2>
                            <small>Yang tampil di kartu daftar materi</small>
                        </div>
                    </div>
                    <div class="mf-body">
                        <div class="mf-field">
                            <label for="class_id">Kelas <span class="req">*</span></label>
                            <select id="class_id" name="class_id" class="mf-select @error('class_id') invalid @enderror">
                                <option value="">Pilih kelas…</option>
                                @foreach ($myClasses as $c)
                                    <option value="{{ $c->id }}" @selected($selectedClass == $c->id)>
                                        {{ $c->name }}{{ $c->school_year ? ' · ' . $c->school_year : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <p class="mf-err">{{ $message }}</p>
                            @enderror
                            <p class="mf-err" id="err-class" hidden>Pilih kelas untuk materi ini.</p>
                        </div>

                        <div class="mf-field">
                            <label for="title">Judul materi <span class="req">*</span></label>
                            <input id="title" name="title" class="mf-input @error('title') invalid @enderror"
                                maxlength="255" value="{{ $v('title') }}" placeholder="Contoh: Bilangan Bulat">
                            @error('title')
                                <p class="mf-err">{{ $message }}</p>
                            @enderror
                            <p class="mf-err" id="err-title" hidden>Judul materi wajib diisi.</p>
                            <div class="mf-slug">
                                <span>Alamat:</span>
                                <input id="slug" name="slug" value="{{ $v('slug') }}" readonly
                                    placeholder="otomatis-dari-judul" aria-label="Slug">
                                <button type="button" class="mf-link-btn" id="slug-edit">Ubah</button>
                            </div>
                        </div>

                        <div class="mf-field">
                            <label for="description">Ringkasan <em><span id="desc-count">0</span>/300</em></label>
                            <textarea id="description" name="description" class="mf-textarea" maxlength="300"
                                placeholder="Satu atau dua kalimat tentang apa yang akan dipelajari siswa.">{{ $v('description') }}</textarea>
                        </div>

                        <div class="mf-grid2">
                            <div class="mf-field">
                                <label for="category">Kategori / Bab</label>
                                <input id="category" name="category" class="mf-input" list="cat-list" maxlength="255"
                                    value="{{ $v('category') }}" placeholder="Contoh: Bab 1">
                                <datalist id="cat-list">
                                    @foreach (range(1, 8) as $b)
                                        <option value="Bab {{ $b }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="mf-field">
                                <label for="type">Jenis materi</label>
                                @php $type = $v('type', 'modul'); @endphp
                                <select id="type" name="type" class="mf-select">
                                    <option value="modul" @selected($type === 'modul')>Modul (bacaan)</option>
                                    <option value="video" @selected($type === 'video')>Video</option>
                                    <option value="dokumen" @selected($type === 'dokumen')>Dokumen / slide</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- 2. ISI E-MODUL: content --}}
                <section class="mf-card" style="--d:80ms">
                    <div class="mf-head">
                        <span class="ic">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 7v14"></path>
                                <path
                                    d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z">
                                </path>
                            </svg>
                        </span>
                        <div>
                            <h2>Isi E-Modul</h2>
                            <small>Tampil paling atas di halaman belajar siswa</small>
                        </div>
                    </div>
                    <div class="mf-body">
                        <div>
                            <div class="mf-toolbar" id="mf-toolbar" role="toolbar" aria-label="Format teks">
                                <button type="button" class="mf-tb" data-cmd="h3" title="Judul bagian">Judul</button>
                                <button type="button" class="mf-tb" data-cmd="p"
                                    title="Paragraf biasa">Paragraf</button>
                                <span class="mf-sep"></span>
                                <button type="button" class="mf-tb" data-cmd="bold" id="tb-bold"
                                    title="Tebal (Ctrl+B)"><b>B</b></button>
                                <button type="button" class="mf-tb" data-cmd="italic" id="tb-italic"
                                    title="Miring (Ctrl+I)"><i>I</i></button>
                                <span class="mf-sep"></span>
                                <button type="button" class="mf-tb" data-cmd="ul">• Poin</button>
                                <button type="button" class="mf-tb" data-cmd="ol">1. Angka</button>
                                <span class="mf-sep"></span>
                                <button type="button" class="mf-tb accent" data-cmd="callout">Kotak Ingat!</button>
                                <button type="button" class="mf-tb accent" data-cmd="example">Contoh</button>
                                <button type="button" class="mf-tb accent" data-cmd="numline" id="tb-numline">Garis
                                    bilangan</button>
                                <span class="mf-sep"></span>
                                <button type="button" class="mf-tb" data-cmd="undo" title="Urungkan">↶</button>
                                <button type="button" class="mf-tb" data-cmd="redo" title="Ulangi">↷</button>
                            </div>

                            <div class="mf-pop" id="mf-pop">
                                <div class="mf-pop-row">
                                    <div class="mf-field"><label for="nl-min">Dari</label>
                                        <input id="nl-min" type="number" class="mf-input" value="-5"
                                            step="1">
                                    </div>
                                    <div class="mf-field"><label for="nl-max">Sampai</label>
                                        <input id="nl-max" type="number" class="mf-input" value="5"
                                            step="1">
                                    </div>
                                    <div class="mf-field"><label for="nl-pts">Titik yang ditandai</label>
                                        <input id="nl-pts" class="mf-input" value="-3, 5" placeholder="-3, 5">
                                    </div>
                                </div>
                                <div class="mf-pop-prev" id="nl-prev"></div>
                                <p class="mf-err" id="nl-err" hidden></p>
                                <div class="mf-pop-actions">
                                    <button type="button" class="mf-btn ghost sm" id="nl-cancel">Batal</button>
                                    <button type="button" class="mf-btn sm" id="nl-insert">Sisipkan garis
                                        bilangan</button>
                                </div>
                            </div>

                            <div class="mf-editor is-empty" id="mf-editor" contenteditable="true" spellcheck="true"
                                data-placeholder="Mulai menulis materi di sini… Gunakan tombol di atas untuk judul bagian, kotak “Ingat!”, contoh, dan garis bilangan.">
                            </div>

                            <div class="mf-editor-foot">
                                <span><span id="wc">0</span> kata · perkiraan baca <span id="rt">0</span>
                                    menit</span>
                                <span>Garis bilangan dibuat otomatis dari angka yang kamu isi.</span>
                            </div>
                            <textarea name="content" id="content" hidden></textarea>
                            @error('content')
                                <p class="mf-err">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- 3. LAMPIRAN: resource_url (YouTube), file_path + original_filename --}}
                <section class="mf-card" style="--d:160ms">
                    <div class="mf-head">
                        <span class="ic">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48">
                                </path>
                            </svg>
                        </span>
                        <div>
                            <h2>Lampiran</h2>
                            <small>Opsional: video YouTube dan satu berkas pendukung</small>
                        </div>
                    </div>
                    <div class="mf-body">
                        <div class="mf-field">
                            <label for="resource_url">Link video YouTube</label>
                            <input id="resource_url" name="resource_url"
                                class="mf-input @error('resource_url') invalid @enderror" maxlength="255"
                                value="{{ $v('resource_url') }}" placeholder="https://www.youtube.com/watch?v=…">
                            @error('resource_url')
                                <p class="mf-err">{{ $message }}</p>
                            @enderror
                            <p class="mf-err" id="err-yt" hidden>Link YouTube tidak dikenali. Gunakan link watch,
                                youtu.be,
                                atau embed.</p>
                            <div class="mf-yt" id="yt-card">
                                <div class="mf-yt-thumb"><img id="yt-img" alt=""><i></i></div>
                                <div><b>Video terdeteksi</b><small id="yt-id"></small></div>
                            </div>
                        </div>

                        <div>
                            <span class="mf-label">Berkas pendukung <em>PPT, PDF, atau Word · maks. 20 MB</em></span>

                            @if ($hasFile)
                                <div class="mf-file show" id="file-current">
                                    <span
                                        class="badge">{{ strtoupper(pathinfo($material->original_filename ?: $material->file_path, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                                    <div><b>{{ $material->original_filename ?: basename($material->file_path) }}</b>
                                        <small>Berkas saat ini</small>
                                    </div>
                                    <button type="button" class="mf-btn ghost sm x"
                                        id="file-remove-current">Hapus</button>
                                </div>
                                <input type="hidden" name="remove_file" id="remove_file" value="0">
                            @endif

                            <div class="mf-drop" id="drop" tabindex="0" role="button"
                                aria-label="Pilih atau tarik berkas ke sini" {{ $hasFile ? 'hidden' : '' }}>
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <b>Tarik berkas ke sini, atau klik untuk memilih</b>
                                <small>.pdf · .ppt · .pptx · .doc · .docx</small>
                            </div>
                            <input type="file" id="file" name="file" accept=".pdf,.ppt,.pptx,.doc,.docx"
                                hidden>

                            <div class="mf-file" id="file-new">
                                <span class="badge" id="file-badge">PPT</span>
                                <div><b id="file-name"></b><small id="file-size"></small></div>
                                <button type="button" class="mf-btn ghost sm x" id="file-clear">Hapus</button>
                            </div>
                            <p class="mf-err" id="err-file" hidden></p>
                            @error('file')
                                <p class="mf-err">{{ $message }}</p>
                            @enderror
                            <p class="mf-hint">Memilih berkas baru akan menggantikan berkas lama.</p>
                        </div>
                    </div>
                </section>
            </div>

            {{-- SIDEBAR: is_published, duration_minutes, sort_order, kelengkapan, simpan --}}
            <aside class="mf-side">
                <div class="mf-box">
                    <h3>Penerbitan</h3>
                    <label class="mf-switch">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" id="is_published" name="is_published" value="1"
                            @checked($v('is_published', 0))>
                        <span class="mf-track"></span>
                        <span><b id="pub-title">Draf</b><small id="pub-sub">Hanya terlihat olehmu</small></span>
                    </label>
                </div>

                <div class="mf-box">
                    <h3>Pengaturan</h3>
                    <div class="mf-field" style="margin-bottom:1rem">
                        <label for="duration_minutes">Durasi belajar <em>menit</em></label>
                        <input id="duration_minutes" name="duration_minutes" type="number" min="1"
                            max="600" class="mf-input" value="{{ $v('duration_minutes') }}" placeholder="30">
                        <p class="mf-hint" id="dur-hint" hidden>Perkiraan dari isi modul: <b id="dur-val"></b> menit
                            · <button type="button" class="mf-link-btn" id="dur-use">Pakai</button></p>
                    </div>
                    <div class="mf-field">
                        <label for="sort_order">Urutan tampil</label>
                        <input id="sort_order" name="sort_order" type="number" min="0" class="mf-input"
                            value="{{ $v('sort_order', 0) }}">
                        <p class="mf-hint">Angka kecil tampil lebih dulu.</p>
                    </div>
                </div>

                <div class="mf-box">
                    <h3>Kelengkapan</h3>
                    <ul class="mf-check" id="mf-check">
                        <li data-k="title"><i><svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                    stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg></i>Judul diisi</li>
                        <li data-k="class"><i><svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                    stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg></i>Kelas dipilih</li>
                        <li data-k="desc"><i><svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                    stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg></i>Ringkasan ditulis</li>
                        <li data-k="content"><i><svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                    stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg></i>E-Modul ditulis</li>
                        <li data-k="attach"><i><svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                    stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg></i>Lampiran<small>opsional</small></li>
                    </ul>
                </div>

                <div class="mf-actions">
                    <button type="submit" class="mf-btn block" id="mf-save" @disabled($myClasses->isEmpty())>
                        <span class="mf-spin"></span>
                        <span id="save-label">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Materi' }}</span>
                    </button>
                    <a href="{{ route('materi.index') }}" class="mf-btn ghost block">Batal</a>
                </div>
            </aside>
        </div>
    </form>

    <script>
        (function() {
            var $ = function(id) {
                return document.getElementById(id);
            };
            var form = $('mf-form'),
                editor = $('mf-editor'),
                dirty = false,
                submitting = false;
            var INITIAL = @json((string) $v('content'));

            function markDirty() {
                dirty = true;
                refresh();
            }

            /* ================= Garis bilangan ================= */
            function fmt(n) {
                return n < 0 ? '\u2212' + Math.abs(n) : String(n);
            }

            function numlineSVG(min, max, pts) {
                var span = max - min,
                    x0 = 40,
                    step = 520 / span,
                    fs = span > 14 ? 13 : 16;
                var s = '<svg viewBox="0 0 600 96" role="img" aria-label="Garis bilangan dari ' + fmt(min) +
                    ' sampai ' + fmt(max) + '">' +
                    '<line x1="24" y1="52" x2="576" y2="52" stroke="#086534" stroke-width="3" stroke-linecap="round"/>' +
                    '<polygon points="576,44 592,52 576,60" fill="#086534"/><polygon points="24,44 8,52 24,60" fill="#086534"/>';
                for (var i = 0; i <= span; i++) {
                    var n = min + i,
                        x = x0 + i * step,
                        hi = pts.indexOf(n) > -1;
                    s += '<line x1="' + x + '" y1="44" x2="' + x + '" y2="60" stroke="#086534" stroke-width="2"/>' +
                        '<text x="' + x + '" y="82" text-anchor="middle" font-size="' + fs +
                        '" font-weight="700" fill="' +
                        (hi ? '#c2410c' : '#3f6b50') + '">' + fmt(n) + '</text>';
                }
                pts.forEach(function(p) {
                    if (p >= min && p <= max) s += '<circle cx="' + (x0 + (p - min) * step) +
                        '" cy="52" r="8" fill="#f97316"/>';
                });
                return s + '</svg>';
            }

            function blockHTML(min, max, ptsStr) {
                var pts = ptsStr ? ptsStr.split(',').map(Number) : [];
                return '<div class="ml-numline" contenteditable="false" data-min="' + min + '" data-max="' + max +
                    '" data-points="' + ptsStr + '">' + numlineSVG(min, max, pts) +
                    '<button type="button" class="ml-nl-del" aria-label="Hapus garis bilangan">&times;</button></div>';
            }

            // simpan di database sebagai token teks aman; tampil sebagai grafik di editor
            function hydrate(html) {
                return html.replace(
                    /(?:<p>\s*)?\[garis-bilangan min=(-?\d+) max=(-?\d+)(?: titik=([-\d,]*))?\](?:\s*<\/p>)?/g,
                    function(_, a, b, c) {
                        return blockHTML(+a, +b, c || '');
                    });
            }

            function serialize() {
                var clone = editor.cloneNode(true);
                clone.querySelectorAll('.ml-numline').forEach(function(b) {
                    var p = document.createElement('p');
                    p.textContent = '[garis-bilangan min=' + b.getAttribute('data-min') + ' max=' + b
                        .getAttribute('data-max') +
                        (b.getAttribute('data-points') ? ' titik=' + b.getAttribute('data-points') : '') + ']';
                    b.replaceWith(p);
                });
                return clone.innerHTML.trim();
            }

            /* ================= Editor ================= */
            var savedRange = null;

            function saveRange() {
                var sel = window.getSelection();
                if (sel.rangeCount && editor.contains(sel.anchorNode)) savedRange = sel.getRangeAt(0).cloneRange();
            }

            function restoreRange() {
                editor.focus();
                if (savedRange) {
                    var sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(savedRange);
                }
            }

            function exec(cmd, val) {
                editor.focus();
                document.execCommand(cmd, false, val || null);
                markDirty();
            }

            function syncEmpty() {
                var empty = editor.textContent.trim() === '' && !editor.querySelector('.ml-numline');
                editor.classList.toggle('is-empty', empty);
            }

            var actions = {
                h3: function() {
                    exec('formatBlock', 'h3');
                },
                p: function() {
                    exec('formatBlock', 'p');
                },
                bold: function() {
                    exec('bold');
                },
                italic: function() {
                    exec('italic');
                },
                ul: function() {
                    exec('insertUnorderedList');
                },
                ol: function() {
                    exec('insertOrderedList');
                },
                undo: function() {
                    exec('undo');
                },
                redo: function() {
                    exec('redo');
                },
                callout: function() {
                    exec('insertHTML',
                        '<div class="ml-callout"><b>Ingat!</b> Tulis poin penting di sini.</div><p><br></p>'
                        );
                },
                example: function() {
                    exec('insertHTML',
                    '<div class="ml-example"><span>Contoh</span>\u22123 + 5 = 2</div>&nbsp;');
                },
                numline: function() {
                    saveRange();
                    togglePop(true);
                }
            };

            $('mf-toolbar').addEventListener('mousedown', function(e) {
                if (e.target.closest('.mf-tb')) e.preventDefault(); // jaga fokus & seleksi editor
            });
            $('mf-toolbar').addEventListener('click', function(e) {
                var b = e.target.closest('.mf-tb');
                if (b && actions[b.getAttribute('data-cmd')]) actions[b.getAttribute('data-cmd')]();
            });

            document.addEventListener('selectionchange', function() {
                if (!editor.contains(document.getSelection().anchorNode)) return;
                $('tb-bold').classList.toggle('on', document.queryCommandState('bold'));
                $('tb-italic').classList.toggle('on', document.queryCommandState('italic'));
            });

            editor.addEventListener('click', function(e) {
                var del = e.target.closest('.ml-nl-del');
                if (del) {
                    del.closest('.ml-numline').remove();
                    markDirty();
                }
            });
            editor.addEventListener('input', markDirty);
            editor.addEventListener('paste', function(e) {
                e.preventDefault(); // tempel sebagai teks biasa agar bersih
                var t = (e.clipboardData || window.clipboardData).getData('text/plain');
                document.execCommand('insertText', false, t);
            });

            /* ----- popover garis bilangan ----- */
            var pop = $('mf-pop');

            function readNL() {
                var min = parseInt($('nl-min').value, 10),
                    max = parseInt($('nl-max').value, 10);
                var pts = $('nl-pts').value.split(',').map(function(s) {
                        return s.trim();
                    })
                    .filter(function(s) {
                        return s !== '';
                    }).map(Number);
                var err = '';
                if (isNaN(min) || isNaN(max)) err = 'Isi angka awal dan akhir.';
                else if (min >= max) err = 'Angka awal harus lebih kecil dari angka akhir.';
                else if (max - min > 20) err = 'Rentang maksimal 20 angka agar tetap terbaca.';
                else if (pts.some(function(p) {
                        return !Number.isInteger(p) || p < min || p > max;
                    }))
                    err = 'Titik harus bilangan bulat di dalam rentang.';
                return {
                    min: min,
                    max: max,
                    pts: pts,
                    err: err
                };
            }

            function previewNL() {
                var d = readNL(),
                    er = $('nl-err');
                er.hidden = !d.err;
                er.textContent = d.err;
                $('nl-prev').innerHTML = d.err ? '' : numlineSVG(d.min, d.max, d.pts);
                $('nl-insert').disabled = !!d.err;
            }

            function togglePop(open) {
                pop.classList.toggle('open', open);
                $('tb-numline').classList.toggle('on', open);
                if (open) previewNL();
            }
            ['nl-min', 'nl-max', 'nl-pts'].forEach(function(id) {
                $(id).addEventListener('input', previewNL);
            });
            $('nl-cancel').addEventListener('click', function() {
                togglePop(false);
            });
            $('nl-insert').addEventListener('click', function() {
                var d = readNL();
                if (d.err) return;
                restoreRange();
                document.execCommand('insertHTML', false, blockHTML(d.min, d.max, d.pts.join(',')) +
                    '<p><br></p>');
                togglePop(false);
                markDirty();
            });

            /* ================= Slug ================= */
            var title = $('title'),
                slug = $('slug'),
                slugTouched = !!slug.value;

            function slugify(t) {
                return t.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 80);
            }
            title.addEventListener('input', function() {
                if (!slugTouched) slug.value = slugify(title.value);
                markDirty();
            });
            slug.addEventListener('input', function() {
                slugTouched = true;
                slug.value = slugify(slug.value);
            });
            $('slug-edit').addEventListener('click', function() {
                var ro = slug.hasAttribute('readonly');
                if (ro) {
                    slug.removeAttribute('readonly');
                    slug.focus();
                    this.textContent = 'Kunci';
                } else {
                    slug.setAttribute('readonly', '');
                    this.textContent = 'Ubah';
                }
            });

            /* ================= Ringkasan ================= */
            var desc = $('description');
            desc.addEventListener('input', function() {
                $('desc-count').textContent = desc.value.length;
                markDirty();
            });
            $('desc-count').textContent = desc.value.length;

            /* ================= YouTube ================= */
            var yt = $('resource_url');

            function ytId(u) {
                var m = u.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/))([\w-]{11})/);
                return m ? m[1] : null;
            }

            function checkYT() {
                var v = yt.value.trim(),
                    id = ytId(v);
                $('yt-card').classList.toggle('show', !!id);
                $('err-yt').hidden = !v || !!id;
                yt.classList.toggle('invalid', !!v && !id);
                if (id) {
                    $('yt-img').src = 'https://img.youtube.com/vi/' + id + '/hqdefault.jpg';
                    $('yt-id').textContent = 'ID video: ' + id;
                }
                return !v || !!id;
            }
            yt.addEventListener('input', function() {
                checkYT();
                markDirty();
            });

            /* ================= Berkas ================= */
            var drop = $('drop'),
                file = $('file'),
                OK = ['pdf', 'ppt', 'pptx', 'doc', 'docx'],
                MAX = 20 * 1024 * 1024;

            function size(b) {
                return b > 1048576 ? (b / 1048576).toFixed(1).replace('.', ',') + ' MB' : Math.ceil(b / 1024) + ' KB';
            }

            function setFile(f) {
                var er = $('err-file');
                er.hidden = true;
                if (!f) return;
                var ext = f.name.split('.').pop().toLowerCase();
                if (OK.indexOf(ext) === -1) {
                    er.textContent = 'Format .' + ext + ' tidak didukung.';
                    er.hidden = false;
                    file.value = '';
                    return;
                }
                if (f.size > MAX) {
                    er.textContent = 'Ukuran berkas melebihi 20 MB.';
                    er.hidden = false;
                    file.value = '';
                    return;
                }
                var dt = new DataTransfer();
                dt.items.add(f);
                file.files = dt.files;
                $('file-badge').textContent = ext === 'pptx' ? 'PPT' : ext.toUpperCase();
                $('file-name').textContent = f.name;
                $('file-size').textContent = size(f.size) + ' · siap diunggah';
                $('file-new').classList.add('show');
                drop.hidden = true;
                var cur = $('file-current');
                if (cur) cur.style.display = 'none';
                markDirty();
            }
            drop.addEventListener('click', function() {
                file.click();
            });
            drop.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    file.click();
                }
            });
            file.addEventListener('change', function() {
                setFile(file.files[0]);
            });
            ['dragenter', 'dragover'].forEach(function(ev) {
                drop.addEventListener(ev, function(e) {
                    e.preventDefault();
                    drop.classList.add('over');
                });
            });
            ['dragleave', 'drop'].forEach(function(ev) {
                drop.addEventListener(ev, function(e) {
                    e.preventDefault();
                    drop.classList.remove('over');
                });
            });
            drop.addEventListener('drop', function(e) {
                setFile(e.dataTransfer.files[0]);
            });
            $('file-clear').addEventListener('click', function() {
                file.value = '';
                $('file-new').classList.remove('show');
                var cur = $('file-current');
                if (cur && $('remove_file').value !== '1') {
                    cur.style.display = '';
                } else {
                    drop.hidden = false;
                }
                if (!cur) drop.hidden = false;
                markDirty();
            });
            var rm = $('file-remove-current');
            if (rm) rm.addEventListener('click', function() {
                $('remove_file').value = '1';
                $('file-current').style.display = 'none';
                drop.hidden = false;
                markDirty();
            });

            /* ================= Penerbitan ================= */
            var pub = $('is_published');

            function syncPub() {
                $('pub-title').textContent = pub.checked ? 'Terbit' : 'Draf';
                $('pub-sub').textContent = pub.checked ? 'Siswa bisa melihat materi ini' : 'Hanya terlihat olehmu';
            }
            pub.addEventListener('change', function() {
                syncPub();
                markDirty();
            });

            /* ================= Kelengkapan & statistik ================= */
            var dur = $('duration_minutes');

            function refresh() {
                syncEmpty();
                var text = editor.textContent.trim(),
                    words = text ? text.split(/\s+/).length : 0;
                var mins = Math.max(1, Math.round(words / 200));
                $('wc').textContent = words;
                $('rt').textContent = words ? mins : 0;
                $('dur-hint').hidden = !words || !!dur.value;
                $('dur-val').textContent = mins;
                var hasAttach = !!yt.value.trim() || file.files.length > 0 ||
                    ($('file-current') && $('remove_file').value !== '1');
                var state = {
                    title: !!title.value.trim(),
                    class: !!$('class_id').value,
                    desc: !!desc.value.trim(),
                    content: words > 0 || !!editor.querySelector('.ml-numline'),
                    attach: hasAttach
                };
                document.querySelectorAll('#mf-check li').forEach(function(li) {
                    li.classList.toggle('ok', state[li.getAttribute('data-k')]);
                });
            }
            $('dur-use').addEventListener('click', function() {
                dur.value = $('dur-val').textContent;
                refresh();
                markDirty();
            });
            ['class_id', 'duration_minutes', 'sort_order', 'category', 'type'].forEach(function(id) {
                $(id).addEventListener('input', markDirty);
                $(id).addEventListener('change', markDirty);
            });

            /* ================= Simpan ================= */
            form.addEventListener('submit', function(e) {
                var bad = null;
                $('err-title').hidden = !!title.value.trim();
                title.classList.toggle('invalid', !title.value.trim());
                $('err-class').hidden = !!$('class_id').value;
                $('class_id').classList.toggle('invalid', !$('class_id').value);
                if (!title.value.trim()) bad = title;
                else if (!$('class_id').value) bad = $('class_id');
                if (!checkYT()) bad = bad || yt;
                if (bad) {
                    e.preventDefault();
                    bad.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    bad.focus();
                    return;
                }
                $('content').value = serialize();
                submitting = true;
                var btn = $('mf-save');
                btn.classList.add('loading');
                btn.disabled = true;
                $('save-label').textContent = 'Menyimpan…';
            });

            window.addEventListener('beforeunload', function(e) {
                if (dirty && !submitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            /* ================= Muat nilai awal ================= */
            editor.innerHTML = hydrate(INITIAL);
            slugTouched = !!slug.value;
            syncPub();
            checkYT();
            refresh();
            dirty = false;
        })();
    </script>
@endsection

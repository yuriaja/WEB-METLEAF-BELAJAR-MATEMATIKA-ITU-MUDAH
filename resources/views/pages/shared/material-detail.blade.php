@extends('layouts.app')

@section('title', $material->title . ' | MathLeaf')
@section('header_title', 'Materi Pembelajaran')

@section('content')
    @php
        $progressPercent = (int) ($material->progress->first()->progress_percent ?? 0);

        // Khusus tampilan: label badge berkas diambil dari ekstensi nama file
        $fileBadge = strtoupper(pathinfo($material->original_filename ?? '', PATHINFO_EXTENSION)) ?: 'FILE';
        $hasContent = (bool) $material->content;
        $hasAttachment = $material->file_path || $material->resource_url;

        // Ubah link YouTube biasa menjadi link embed (mengambil logika dari video.blade.php dan form)
        $embedUrl = null;
        if (
            $material->resource_url &&
            preg_match(
                '~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/))([\w-]{11})~',
                $material->resource_url,
                $mm,
            )
        ) {
            $embedUrl = 'https://www.youtube.com/embed/' . $mm[1];
        }
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
        .lm-header {
            margin-bottom: 1.75rem;
        }

        .lm-back {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: .9rem;
            transition: color .15s, transform .15s;
        }

        .lm-back:hover {
            color: var(--ml-green);
            transform: translateX(-3px);
        }

        .lm-title {
            font-size: clamp(1.44rem, 2.4vw, 1.84rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .5rem;
            line-height: 1.2;
        }

        .lm-subtitle {
            color: #475569;
            font-size: 0.88rem;
            margin: 0 0 1rem;
            line-height: 1.5;
        }

        .lm-chips {
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
        }

        .lm-chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: var(--ml-green-light);
            color: var(--ml-green);
            border: 1px solid #cfe8d6;
            border-radius: 999px;
            padding: .4rem .9rem;
            font-size: 0.736rem;
            font-weight: 700;
            text-transform: capitalize;
        }

        /* ---------- Alert ---------- */
        .lm-alert {
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

        /* ---------- Layout ---------- */
        .lm-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 2rem;
            align-items: start;
        }

        .lm-main {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
            min-width: 0;
        }

        @media (max-width: 960px) {
            .lm-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .lm-side {
                order: -1;
                position: static !important;
            }
        }

        /* ---------- Kartu section ---------- */
        .lm-card {
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            scroll-margin-top: 90px;
            animation: lmUp .55s cubic-bezier(.2, .8, .2, 1) both;
            animation-delay: var(--d, 0ms);
        }

        .lm-card-head {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: 1.25rem 1.6rem;
            border-bottom: 1px solid #e8f0ea;
        }

        .lm-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .lm-card-head h2 {
            margin: 0;
            font-size: 1.04rem;
            color: #14301f;
            line-height: 1.25;
        }

        .lm-card-head small {
            display: block;
            font-size: 0.76rem;
            color: #64748b;
            font-weight: 500;
            margin-top: 2px;
        }

        .lm-card-body {
            padding: 1.6rem;
        }

        @keyframes lmUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* ---------- Isi materi & Elemen Visual Editor ---------- */
        .lm-modul-text {
            color: #334155;
            font-size: 0.864rem;
            line-height: 1.8;
            overflow-wrap: anywhere;
        }

        .lm-modul-text h3 {
            color: #14301f;
            font-size: 1.1rem;
            margin: 1.4rem 0 .6rem;
        }

        .lm-modul-text h3:first-child {
            margin-top: 0;
        }

        .lm-modul-text p {
            margin: 0 0 .9rem;
        }

        .lm-modul-text ul,
        .lm-modul-text ol {
            margin: 0 0 1rem;
            padding-left: 1.5rem;
        }

        /* Desain Callout & Example yang disisipkan dari form materi */
        .ml-callout {
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-left: 5px solid var(--ml-green);
            border-radius: 12px;
            padding: 1rem 1.2rem;
            margin: 1.2rem 0;
            color: #1f4d33;
            font-size: 0.84rem;
        }

        .ml-callout b {
            display: block;
            color: var(--ml-green);
            margin-bottom: 2px;
        }

        .ml-example {
            display: inline-block;
            background: #fffbea;
            border: 1px solid #f6e7a6;
            border-radius: 12px;
            padding: .9rem 1.1rem;
            margin: .5rem 1rem .5rem 0;
            font-size: 1.05rem;
            font-weight: 700;
            color: #14301f;
        }

        .ml-example span {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            color: #8a6d00;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 2px;
        }

        /* Garis Bilangan */
        .ml-numline {
            width: 100%;
            height: auto;
            background: #f6fbf7;
            border: 1px solid #e0eee4;
            border-radius: 12px;
            margin: 1rem 0;
            overflow: hidden;
        }

        .ml-numline svg {
            display: block;
            width: 100%;
            height: auto;
        }

        /* ---------- Lampiran ---------- */
        .lm-attach {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        .lm-attach-title {
            font-size: 0.704rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #64748b;
            margin: 0;
        }

        /* Video Pemutar */
        .lm-video {
            position: relative;
            aspect-ratio: 16 / 9;
            border-radius: 14px;
            overflow: hidden;
            background: #14301f;
            border: 1px solid #cfe8d6;
            margin-bottom: .8rem;
        }

        .lm-video iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .lm-file {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1rem 1.2rem;
            border: 1px solid #dfe9e2;
            border-radius: 14px;
            background: #fbfdfb;
            transition: border-color .2s, box-shadow .2s, transform .2s;
        }

        .lm-file:hover {
            border-color: var(--ml-green);
            box-shadow: 0 8px 20px rgba(8, 101, 52, .1);
            transform: translateY(-2px);
        }

        .lm-file-badge {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #ffe9d6;
            color: #c2410c;
            font-weight: 800;
            font-size: 0.68rem;
            flex-shrink: 0;
        }

        .lm-file-badge.link {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .lm-file-info {
            min-width: 0;
            flex: 1 1 180px;
        }

        .lm-file-info b {
            display: block;
            font-size: 0.816rem;
            color: #14301f;
            word-break: break-word;
        }

        .lm-file-info small {
            color: #64748b;
            font-size: 0.736rem;
        }

        .lm-file-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-left: auto;
        }

        /* ---------- Tombol ---------- */
        .lm-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border: 0;
            border-radius: 10px;
            padding: .75rem 1.3rem;
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            background: var(--ml-green);
            color: #ffffff;
            transition: background .2s, transform .15s, box-shadow .2s;
        }

        .lm-btn:hover {
            background: var(--ml-green-hover);
            box-shadow: 0 6px 16px rgba(8, 101, 52, .25);
            color: #ffffff;
        }

        .lm-btn:active {
            transform: scale(.97);
        }

        .lm-btn.block {
            width: 100%;
        }

        .lm-btn.outline {
            background: transparent;
            color: var(--ml-green);
            border: 1.5px solid #b5d9c1;
        }

        .lm-btn.outline:hover {
            background: var(--ml-green-light);
            box-shadow: none;
            color: var(--ml-green);
        }

        .lm-btn.soft {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .lm-btn.soft:hover {
            background: var(--ml-green-soft);
            box-shadow: none;
            color: var(--ml-green);
        }

        .lm-btn.sm {
            padding: .5rem .95rem;
            font-size: 0.736rem;
        }

        /* ---------- Sidebar ---------- */
        .lm-side {
            position: sticky;
            top: 90px;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            padding: 1.4rem;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            animation: lmUp .55s cubic-bezier(.2, .8, .2, 1) both;
            animation-delay: 120ms;
        }

        .lm-side h3 {
            margin: 0 0 1rem;
            font-size: 0.92rem;
            color: #14301f;
        }

        .lm-progress-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.784rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: .5rem;
        }

        .lm-progress-row span:last-child {
            color: var(--ml-green);
        }

        .lm-track {
            background: #e3efe6;
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
        }

        .lm-track div {
            height: 100%;
            background: linear-gradient(90deg, #22a35a, #086534);
            border-radius: 999px;
            transition: width 1s cubic-bezier(.2, .8, .2, 1);
            width: 0;
        }

        .lm-toc {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            margin: 1.4rem 0;
        }

        .lm-toc a {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .8rem .9rem;
            border: 1px solid #e3ece6;
            border-radius: 12px;
            color: #334155;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.8rem;
            transition: background .2s, border-color .2s, color .2s, transform .2s;
        }

        .lm-toc a:hover {
            background: #f6fbf7;
            transform: translateX(3px);
        }

        .lm-toc a .n {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #e9f3ec;
            color: var(--ml-green);
            font-size: 0.704rem;
            flex-shrink: 0;
            transition: background .2s, color .2s;
        }

        .lm-toc a small {
            display: block;
            font-size: 0.68rem;
            font-weight: 500;
            color: #64748b;
        }

        .lm-toc a.active {
            background: var(--ml-green-light);
            border-color: var(--ml-green);
            color: var(--ml-green);
        }

        .lm-toc a.active .n {
            background: var(--ml-green);
            color: #ffffff;
        }

        .lm-side-actions {
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }

        .lm-side-actions form {
            margin: 0;
        }

        @media (prefers-reduced-motion: reduce) {

            .lm-card,
            .lm-side,
            .lm-track div {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>

    {{-- HEADER --}}
    <div class="lm-header">
        <a href="{{ route('materi.index') }}" class="lm-back">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Daftar Materi
        </a>

        @if (session('success'))
            <div class="lm-alert" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="lm-alert" role="alert" style="background:#fef2f2; border-color:#fecaca; color:#991b1b;">
                {{ $errors->first() }}</div>
        @endif

        <h1 class="lm-title">{{ $material->title }}</h1>
        @if ($material->description)
            <p class="lm-subtitle">{{ $material->description }}</p>
        @endif

        <div class="lm-chips">
            <span class="lm-chip">{{ $material->type }}</span>
            <span class="lm-chip" style="text-transform:none">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                {{ $material->author->name ?? 'Guru' }}
            </span>
            @if ($isOwner)
                <span class="lm-chip">{{ $material->is_published ? 'Terbit' : 'Draf' }}</span>
            @endif
        </div>
    </div>

    <div class="lm-layout">
        <div class="lm-main">

            {{-- 1. ISI MATERI (Tampil dengan visual HTML lengkap dari form) --}}
            @if ($hasContent)
                <section class="lm-card" id="lm-modul" style="--d: 0ms">
                    <div class="lm-card-head">
                        <span class="lm-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 7v14"></path>
                                <path
                                    d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z">
                                </path>
                            </svg>
                        </span>
                        <div>
                            <h2>Isi Materi / Bacaan</h2>
                            <small>Baca materi ini dengan teliti</small>
                        </div>
                    </div>
                    <div class="lm-card-body">
                        {{-- Menggunakan {!! !!} agar elemen HTML dari editor form teraplikasi --}}
                        <div class="lm-modul-text" id="lm-content-render" aria-label="Isi materi">{!! $material->content !!}
                        </div>
                    </div>
                </section>
            @endif

            {{-- 2. LAMPIRAN (Menampilkan preview iframe YouTube) --}}
            @if ($hasAttachment)
                <section class="lm-card" id="lm-lampiran" style="--d: 90ms">
                    <div class="lm-card-head">
                        <span class="lm-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48">
                                </path>
                            </svg>
                        </span>
                        <div>
                            <h2>Lampiran</h2>
                            <small>Video dan berkas pendukung materi</small>
                        </div>
                    </div>
                    <div class="lm-card-body">
                        <div class="lm-attach">

                            {{-- Preview Pemutar YouTube --}}
                            @if ($material->resource_url)
                                <p class="lm-attach-title">Video Pembelajaran</p>
                                @if ($embedUrl)
                                    <div class="lm-video">
                                        <iframe src="{{ $embedUrl }}" title="Video Materi" allowfullscreen
                                            loading="lazy"></iframe>
                                    </div>
                                @else
                                    <div class="lm-file">
                                        <span class="lm-file-badge link">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71">
                                                </path>
                                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71">
                                                </path>
                                            </svg>
                                        </span>
                                        <div class="lm-file-info">
                                            <b>Tautan materi eksternal</b>
                                            <small>Dibuka di tab baru</small>
                                        </div>
                                        <div class="lm-file-actions">
                                            <a class="lm-btn soft sm" href="{{ $material->resource_url }}" target="_blank"
                                                rel="noopener noreferrer">Buka Tautan</a>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- File Unduhan --}}
                            @if ($material->file_path)
                                <p class="lm-attach-title"
                                    @if ($material->resource_url) style="margin-top:.6rem" @endif>Berkas Materi</p>
                                <div class="lm-file">
                                    <span class="lm-file-badge">{{ $fileBadge }}</span>
                                    <div class="lm-file-info">
                                        <b>{{ $material->original_filename ?: 'File materi' }}</b>
                                        <small>Lampiran materi</small>
                                    </div>
                                    <div class="lm-file-actions">
                                        <a class="lm-btn outline sm" href="{{ route('materi.file', $material) }}"
                                            target="_blank" rel="noopener">Buka</a>
                                        <a class="lm-btn soft sm"
                                            href="{{ route('materi.file', ['material' => $material, 'download' => 1]) }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3">
                                                </line>
                                            </svg>
                                            Unduh
                                        </a>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </section>
            @endif
        </div>

        {{-- SIDEBAR: DAFTAR ISI + PROGRES / AKSI PEMILIK --}}
        <aside class="lm-side">
            @if ($hasContent || $hasAttachment)
                <h3>Isi Halaman</h3>

                @unless ($isOwner)
                    <div class="lm-progress-row">
                        <span>Progres materi</span>
                        <span id="lm-percent">0%</span>
                    </div>
                    <div class="lm-track" role="progressbar" aria-label="Progres belajar" aria-valuemin="0"
                        aria-valuemax="100" aria-valuenow="{{ $progressPercent }}">
                        <div id="lm-bar" data-target="{{ $progressPercent }}"></div>
                    </div>
                @endunless

                @php $tocNo = 0; @endphp
                <nav class="lm-toc" id="lm-toc">
                    @if ($hasContent)
                        <a href="#lm-modul" data-target="lm-modul">
                            <span class="n">{{ ++$tocNo }}</span>
                            <span>Isi Materi<small>Bacaan utama</small></span>
                        </a>
                    @endif
                    @if ($hasAttachment)
                        <a href="#lm-lampiran" data-target="lm-lampiran">
                            <span class="n">{{ ++$tocNo }}</span>
                            <span>Lampiran<small>Video &amp; berkas</small></span>
                        </a>
                    @endif
                </nav>
            @endif

            @unless ($isOwner)
                @if ($progressPercent < 100)
                    <form method="POST" action="{{ route('materi.complete', $material) }}" style="margin-top:1.2rem">
                        @csrf
                        <input type="hidden" name="progress_percent" value="100">
                        <button class="lm-btn block" type="submit">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Tandai Selesai
                        </button>
                    </form>
                @else
                    <button class="lm-btn block done" type="button"
                        style="margin-top:1.2rem; cursor:default; background:var(--ml-green-soft); color:var(--ml-green); box-shadow:none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Selesai dipelajari
                    </button>
                @endif
            @endunless

            @if ($isOwner)
                <div class="lm-side-actions">
                    <a class="lm-btn block" href="{{ route('teacher.materials.edit', $material) }}">Edit Materi</a>
                    <form method="POST" action="{{ route('materi.publish', $material) }}">
                        @csrf
                        @method('PATCH')
                        <button class="lm-btn outline block" type="submit">
                            {{ $material->is_published ? 'Sembunyikan dari siswa' : 'Terbitkan untuk siswa' }}
                        </button>
                    </form>
                </div>
            @endif
        </aside>
    </div>

    <script>
        (function() {
            /* ----- Progres Animasi ----- */
            var bar = document.getElementById('lm-bar');
            var pct = document.getElementById('lm-percent');
            if (bar && pct) {
                var current = 0;

                function setProgress(to) {
                    var from = current;
                    bar.style.width = to + '%';
                    var start = null;

                    function step(ts) {
                        if (start === null) start = ts;
                        var t = Math.min((ts - start) / 900, 1);
                        current = Math.round(from + (to - from) * t);
                        pct.textContent = current + '%';
                        if (t < 1) requestAnimationFrame(step);
                    }
                    requestAnimationFrame(step);
                }
                setTimeout(function() {
                    setProgress(parseInt(bar.getAttribute('data-target'), 10) || 0);
                }, 300);
            }

            /* ----- Render Grafis Garis Bilangan dari Editor ----- */
            var renderDiv = document.getElementById('lm-content-render');
            if (renderDiv) {
                // Logika SVG serupa dengan yang ada di form
                function fmt(n) {
                    return n < 0 ? '\u2212' + Math.abs(n) : String(n);
                }

                function numlineSVG(min, max, pts) {
                    var span = max - min,
                        x0 = 40,
                        step = 520 / span,
                        fs = span > 14 ? 13 : 16;
                    var s =
                        '<svg viewBox="0 0 600 96" role="img" aria-label="Garis bilangan"><line x1="24" y1="52" x2="576" y2="52" stroke="#086534" stroke-width="3" stroke-linecap="round"/><polygon points="576,44 592,52 576,60" fill="#086534"/><polygon points="24,44 8,52 24,60" fill="#086534"/>';
                    for (var i = 0; i <= span; i++) {
                        var n = min + i,
                            x = x0 + i * step,
                            hi = pts.indexOf(n) > -1;
                        s += '<line x1="' + x + '" y1="44" x2="' + x +
                            '" y2="60" stroke="#086534" stroke-width="2"/><text x="' + x +
                            '" y="82" text-anchor="middle" font-size="' + fs + '" font-weight="700" fill="' + (hi ?
                                '#c2410c' : '#3f6b50') + '">' + fmt(n) + '</text>';
                    }
                    pts.forEach(function(p) {
                        if (p >= min && p <= max) s += '<circle cx="' + (x0 + (p - min) * step) +
                            '" cy="52" r="8" fill="#f97316"/>';
                    });
                    return s + '</svg>';
                }

                // Cari shortcode `[garis-bilangan ...]` yang disimpan di database
                renderDiv.innerHTML = renderDiv.innerHTML.replace(
                    /(?:<p>\s*)?\[garis-bilangan min=(-?\d+) max=(-?\d+)(?: titik=([-\d,]*))?\](?:\s*<\/p>)?/g,
                    function(_, minVal, maxVal, ptsStr) {
                        var pts = ptsStr ? ptsStr.split(',').map(Number) : [];
                        return '<div class="ml-numline" aria-hidden="true">' + numlineSVG(+minVal, +maxVal, pts) +
                            '</div>';
                    }
                );
            }

            /* ----- Daftar isi: klik = scroll halus, scroll = tandai bagian aktif ----- */
            var links = Array.prototype.slice.call(document.querySelectorAll('#lm-toc a'));
            if (!links.length) return;

            links[0].classList.add('active');

            links.forEach(function(a) {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    var el = document.getElementById(a.getAttribute('data-target'));
                    if (el) el.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });

            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function(entries) {
                    entries.forEach(function(en) {
                        if (!en.isIntersecting) return;
                        links.forEach(function(a) {
                            a.classList.toggle('active', a.getAttribute('data-target') === en
                                .target.id);
                        });
                    });
                }, {
                    rootMargin: '-25% 0px -60% 0px'
                });

                links.forEach(function(a) {
                    var target = document.getElementById(a.getAttribute('data-target'));
                    if (target) io.observe(target);
                });
            }
        })();
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Ruang Belajar | MathLeaf')
@section('header_title', 'Ruang Belajar')

@section('content')
    @php
        $isTeacher = Auth::check() && Auth::user()->role === 'guru';
        $userName = Auth::check() ? Auth::user()->name : 'Pengguna';

        /*
         * DATA MATERI (sementara dummy).
         * Nanti diganti data dari tabel `materials`:
         *   title -> judul, description -> ringkasan, duration_minutes -> durasi,
         *   content -> isi E-Modul, resource_url -> link video, file_path/original_filename -> lampiran (PPT/PDF)
         * ID materi datang dari tombol "Mulai Belajar": request('materi')
         */
        $materiId = request('materi');
        $m = [
            'title' => 'Bilangan Bulat',
            'category' => 'Bab 1',
            'description' => 'Kenali bilangan positif, negatif, dan nol dalam kehidupan sehari-hari.',
            'duration' => 30,
            'progress' => 40,
            'video_url' => null, // isi link YouTube untuk memunculkan pemutar
            'video_title' => 'Mengenal Bilangan Bulat',
            'video_minutes' => 8,
            'file_name' => 'Bilangan Bulat - Slide Pembelajaran.pptx',
            'file_type' => 'PPT',
            'file_size' => '2,4 MB',
            'file_url' => '#',
        ];

        // Ubah link YouTube biasa menjadi link embed
        $embedUrl = null;
        if (
            $m['video_url'] &&
            preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/))([\w-]{11})~', $m['video_url'], $mm)
        ) {
            $embedUrl = 'https://www.youtube.com/embed/' . $mm[1];
        }

        $hasVideo = (bool) $m['video_url'] || !$isTeacher; // dummy: tampilkan placeholder untuk simulasi
        $hasFile = !empty($m['file_name']);
    @endphp

    <style>
        .app-content {
            font-size: 11.2px;
        }


        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-soft: #dff3e5;
        }

        /* Agar sidebar "Isi Materi" bisa sticky (overflow di layout membuat sticky tidak bekerja) */
        .app-content {
            overflow-x: visible;
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

        .lm-card-head .lm-head-action {
            margin-left: auto;
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

        /* ---------- E-Modul ---------- */
        .lm-modul h3 {
            color: #14301f;
            font-size: 1rem;
            margin: 1.8rem 0 .6rem;
        }

        .lm-modul h3:first-child {
            margin-top: 0;
        }

        .lm-modul p {
            color: #334155;
            font-size: 0.864rem;
            line-height: 1.8;
            margin: 0 0 1rem;
        }

        .lm-callout {
            display: flex;
            gap: .9rem;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-left: 5px solid var(--ml-green);
            border-radius: 12px;
            padding: 1rem 1.2rem;
            margin: 1.2rem 0;
            color: #1f4d33;
            font-size: 0.84rem;
            line-height: 1.6;
        }

        .lm-callout b {
            display: block;
            color: var(--ml-green);
            margin-bottom: 2px;
        }

        .lm-examples {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: .8rem;
            margin: 1rem 0 1.2rem;
        }

        .lm-example {
            background: #fffbea;
            border: 1px solid #f6e7a6;
            border-radius: 12px;
            padding: .9rem 1.1rem;
        }

        .lm-example span {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            color: #8a6d00;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .lm-example strong {
            font-size: 1.12rem;
            color: #14301f;
        }

        .lm-numline {
            width: 100%;
            height: auto;
            background: #f6fbf7;
            border: 1px solid #e0eee4;
            border-radius: 12px;
            margin: .4rem 0 1.2rem;
        }

        /* ---------- Lampiran ---------- */
        .lm-attach {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        .lm-video {
            position: relative;
            aspect-ratio: 16 / 9;
            border-radius: 14px;
            overflow: hidden;
            background: linear-gradient(135deg, #e3f5e9 0%, #c8ebd3 55%, #fdf3c4 130%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            text-decoration: none;
            border: 1px solid #cfe8d6;
        }

        .lm-video iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .lm-video .eyebrow {
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: .1em;
            color: var(--ml-green);
            margin-bottom: .4rem;
        }

        .lm-video .big {
            font-size: clamp(1.44rem, 3.2vw, 2.24rem);
            font-weight: 800;
            color: #0d4a26;
        }

        .lm-video .cap {
            color: #3f6b50;
            font-size: 0.8rem;
            margin: .3rem 0 0;
        }

        .lm-play {
            margin-top: 1rem;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #ffffff;
            color: var(--ml-green);
            display: grid;
            place-items: center;
            box-shadow: 0 8px 22px rgba(8, 101, 52, .28);
            transition: transform .2s ease, box-shadow .2s ease;
            animation: lmPulse 2.2s ease-out infinite;
        }

        .lm-video:hover .lm-play {
            transform: scale(1.08);
            box-shadow: 0 12px 28px rgba(8, 101, 52, .35);
        }

        @keyframes lmPulse {
            0% {
                box-shadow: 0 8px 22px rgba(8, 101, 52, .28), 0 0 0 0 rgba(8, 101, 52, .35);
            }

            70% {
                box-shadow: 0 8px 22px rgba(8, 101, 52, .28), 0 0 0 16px rgba(8, 101, 52, 0);
            }

            100% {
                box-shadow: 0 8px 22px rgba(8, 101, 52, .28), 0 0 0 0 rgba(8, 101, 52, 0);
            }
        }

        .lm-file {
            display: flex;
            align-items: center;
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

        .lm-file b {
            display: block;
            font-size: 0.816rem;
            color: #14301f;
            word-break: break-word;
        }

        .lm-file small {
            color: #64748b;
            font-size: 0.736rem;
        }

        .lm-attach-title {
            font-size: 0.704rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #64748b;
            margin: 0;
        }

        .lm-empty {
            text-align: center;
            color: #64748b;
            padding: 1.5rem;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            font-size: 0.816rem;
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
        }

        .lm-btn.soft {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .lm-btn.soft:hover {
            background: var(--ml-green-soft);
            box-shadow: none;
        }

        .lm-btn.sm {
            padding: .5rem .95rem;
            font-size: 0.736rem;
        }

        .lm-btn.done {
            background: var(--ml-green-soft);
            color: var(--ml-green);
            box-shadow: none;
            cursor: default;
        }

        /* ---------- Diskusi ---------- */
        .lm-form textarea {
            width: 100%;
            min-height: 110px;
            resize: vertical;
            padding: .95rem 1.1rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.84rem;
            line-height: 1.6;
            color: #14301f;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .lm-form textarea:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        .lm-form-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: .8rem;
            flex-wrap: wrap;
        }

        .lm-form-foot small {
            color: #64748b;
            font-size: 0.76rem;
        }

        .lm-posts {
            display: flex;
            flex-direction: column;
            gap: .9rem;
            margin-top: 1.6rem;
        }

        .lm-posts-title {
            font-size: 0.84rem;
            font-weight: 800;
            color: #14301f;
            margin: 1.8rem 0 0;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .lm-posts-title span {
            background: var(--ml-green-light);
            color: var(--ml-green);
            font-size: 0.68rem;
            border-radius: 999px;
            padding: .1rem .6rem;
        }

        .lm-post {
            display: flex;
            gap: .9rem;
            padding: 1rem 1.1rem;
            border: 1px solid #e3ece6;
            border-radius: 14px;
            background: #ffffff;
        }

        .lm-post.teacher {
            background: #f3fbf5;
            border-color: #bfe0c9;
        }

        .lm-post.is-new {
            animation: lmPostIn .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        @keyframes lmPostIn {
            from {
                opacity: 0;
                transform: translateY(-14px) scale(.97);
                background: #fff8cc;
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .lm-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--ml-green-soft);
            color: var(--ml-green);
            display: grid;
            place-items: center;
            font-weight: 800;
            flex-shrink: 0;
        }

        .lm-post.teacher .lm-avatar {
            background: var(--ml-green);
            color: #ffffff;
        }

        .lm-post-meta {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-wrap: wrap;
            margin-bottom: .2rem;
        }

        .lm-post-meta b {
            color: #14301f;
            font-size: 0.8rem;
        }

        .lm-post-meta small {
            color: #94a3b8;
            font-size: 0.704rem;
        }

        .lm-role {
            background: var(--ml-green);
            color: #ffffff;
            font-size: 0.6rem;
            font-weight: 800;
            border-radius: 6px;
            padding: .1rem .5rem;
        }

        .lm-post p {
            margin: 0;
            color: #334155;
            font-size: 0.816rem;
            line-height: 1.6;
            white-space: pre-line;
            overflow-wrap: anywhere;
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
            width: 0;
            background: linear-gradient(90deg, #22a35a, #086534);
            border-radius: 999px;
            transition: width 1s cubic-bezier(.2, .8, .2, 1);
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

        .lm-side .lm-btn+.lm-btn {
            margin-top: .6rem;
        }

        @media (prefers-reduced-motion: reduce) {

            .lm-card,
            .lm-side,
            .lm-play,
            .lm-post.is-new {
                animation: none !important;
            }

            .lm-track div {
                transition: none;
            }
        }
    </style>

    <div class="lm-header">
        <a href="{{ route('materi.index') }}" class="lm-back">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Daftar Materi
        </a>
        <h1 class="lm-title">{{ $m['title'] }}</h1>
        <p class="lm-subtitle">
            {{ $isTeacher
                ? 'Kelola E-Modul, lampiran, dan pantau diskusi siswa untuk materi ini.'
                : 'Tahap 1 · Mengamati dan Menanya. Baca E-Modul, lihat lampiran dari guru, lalu tulis pemahaman atau pertanyaanmu.' }}
        </p>
        <div class="lm-chips">
            <span class="lm-chip">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v5l3 2"></path>
                </svg>
                {{ $m['duration'] }} menit
            </span>
            <span class="lm-chip">{{ $m['category'] }}</span>
            @if ($hasVideo)
                <span class="lm-chip">1 video</span>
            @endif
            @if ($hasFile)
                <span class="lm-chip">1 lampiran</span>
            @endif
        </div>
    </div>

    <div class="lm-layout">
        <div class="lm-main">

            {{-- 1. E-MODUL / BACAAN --}}
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
                        <h2>E-Modul / Bacaan</h2>
                        <small>Baca dulu sebelum menonton atau mengerjakan latihan</small>
                    </div>
                    @if ($isTeacher)
                        <button type="button" class="lm-btn outline sm lm-head-action"
                            onclick="alert('Route Edit E-Modul belum dibuat')">Edit E-Modul</button>
                    @endif
                </div>
                <div class="lm-card-body lm-modul">
                    <h3>A. Mengenal bilangan bulat</h3>
                    <p>Bilangan bulat terdiri dari bilangan negatif, nol, dan bilangan positif. Contohnya −3, −2, −1, 0,
                        1, 2, 3. Pada garis bilangan, semakin ke kanan nilainya semakin besar.</p>

                    <svg class="lm-numline" viewBox="0 0 600 96" role="img"
                        aria-label="Garis bilangan dari −5 sampai 5">
                        <line x1="30" y1="52" x2="570" y2="52" stroke="#086534" stroke-width="3"
                            stroke-linecap="round"></line>
                        <polygon points="570,44 586,52 570,60" fill="#086534"></polygon>
                        <polygon points="30,44 14,52 30,60" fill="#086534"></polygon>
                        @foreach (range(-5, 5) as $i => $n)
                            @php $x = 60 + $i * 48; @endphp
                            <line x1="{{ $x }}" y1="44" x2="{{ $x }}" y2="60"
                                stroke="#086534" stroke-width="2"></line>
                            <text x="{{ $x }}" y="82" text-anchor="middle" font-size="12.8" font-weight="700"
                                fill="{{ in_array($n, [-3, 5]) ? '#c2410c' : '#3f6b50' }}">{{ $n < 0 ? '−' . abs($n) : $n }}</text>
                        @endforeach
                        <circle cx="{{ 60 + 2 * 48 }}" cy="52" r="8" fill="#f97316"></circle>
                        <circle cx="{{ 60 + 10 * 48 }}" cy="52" r="8" fill="#f97316"></circle>
                        <text x="{{ 60 + 2 * 48 }}" y="30" text-anchor="middle" font-size="11.2" font-weight="800"
                            fill="#c2410c">negatif</text>
                        <text x="{{ 60 + 10 * 48 }}" y="30" text-anchor="middle" font-size="11.2" font-weight="800"
                            fill="#c2410c">positif</text>
                    </svg>

                    <div class="lm-callout">
                        <div>
                            <b>Ingat!</b>
                            Bilangan negatif selalu berada di sebelah kiri nol, dan bilangan positif di sebelah kanan nol.
                        </div>
                    </div>

                    <h3>B. Operasi bilangan bulat</h3>
                    <p>Penjumlahan dan pengurangan bisa dibantu dengan garis bilangan. Untuk perkalian dan pembagian, dua
                        bilangan bertanda sama menghasilkan positif, sedangkan tanda berbeda menghasilkan negatif.</p>

                    <div class="lm-examples">
                        <div class="lm-example"><span>Penjumlahan</span><strong>−3 + 5 = 2</strong></div>
                        <div class="lm-example"><span>Pengurangan</span><strong>4 − (−2) = 6</strong></div>
                        <div class="lm-example"><span>Perkalian</span><strong>(−4) × 3 = −12</strong></div>
                    </div>
                </div>
            </section>

            {{-- 2. LAMPIRAN DARI GURU (video / PPT, tampil hanya jika ada) --}}
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
                        <h2>Lampiran dari Guru</h2>
                        <small>Video dan berkas pendukung materi</small>
                    </div>
                    @if ($isTeacher)
                        <button type="button" class="lm-btn outline sm lm-head-action"
                            onclick="alert('Route Unggah Lampiran belum dibuat')">Kelola Lampiran</button>
                    @endif
                </div>
                <div class="lm-card-body">
                    @if ($hasVideo || $hasFile)
                        <div class="lm-attach">
                            @if ($hasVideo)
                                <p class="lm-attach-title">Video pembelajaran</p>
                                @if ($embedUrl)
                                    <div class="lm-video">
                                        <iframe src="{{ $embedUrl }}" title="{{ $m['video_title'] }}"
                                            allowfullscreen loading="lazy"></iframe>
                                    </div>
                                @else
                                    <a class="lm-video" href="{{ $m['video_url'] ?: '#' }}"
                                        {{ $m['video_url'] ? 'target=_blank rel=noopener' : '' }}>
                                        <span class="eyebrow">MATHLEAF · KELAS VII</span>
                                        <span class="big">−3 &lt; 0 &lt; 5</span>
                                        <span class="cap">{{ $m['video_title'] }} · {{ $m['video_minutes'] }}
                                            menit</span>
                                        <span class="lm-play">
                                            <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"
                                                stroke="currentColor" stroke-width="2" stroke-linejoin="round">
                                                <polygon points="7 4 20 12 7 20 7 4"></polygon>
                                            </svg>
                                        </span>
                                    </a>
                                @endif
                            @endif

                            @if ($hasFile)
                                <p class="lm-attach-title" style="margin-top:.4rem">Berkas</p>
                                <div class="lm-file">
                                    <span class="lm-file-badge">{{ $m['file_type'] }}</span>
                                    <div>
                                        <b>{{ $m['file_name'] }}</b>
                                        <small>{{ $m['file_size'] }} · diunggah guru</small>
                                    </div>
                                    <a href="{{ $m['file_url'] }}" class="lm-btn soft sm" style="margin-left:auto">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        Unduh
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="lm-empty">Guru belum menambahkan lampiran untuk materi ini.</div>
                    @endif
                </div>
            </section>

            {{-- 3. SATU KOLOM: PEMAHAMAN / PERTANYAAN --}}
            <section class="lm-card" id="lm-diskusi" style="--d: 180ms">
                <div class="lm-card-head">
                    <span class="lm-card-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                            </path>
                        </svg>
                    </span>
                    <div>
                        <h2>Pemahaman &amp; Pertanyaan</h2>
                        <small>Ceritakan apa yang sudah kamu pahami, atau tanyakan yang masih bingung</small>
                    </div>
                </div>
                <div class="lm-card-body">
                    <form class="lm-form" id="lm-form" action="#" autocomplete="off">
                        <textarea id="lm-text" maxlength="1000"
                            placeholder="{{ $isTeacher ? 'Tulis tanggapan atau penjelasan untuk kelas…' : 'Tulis pemahamanmu atau pertanyaanmu tentang materi ini…' }}"></textarea>
                        <div class="lm-form-foot">
                            <small><span id="lm-count">0</span>/1000</small>
                            <button type="submit" class="lm-btn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                                Kirim
                            </button>
                        </div>
                    </form>

                    <h3 class="lm-posts-title">Diskusi kelas <span id="lm-post-count">3</span></h3>
                    <div class="lm-posts" id="lm-posts">
                        <div class="lm-post teacher">
                            <span class="lm-avatar">G</span>
                            <div>
                                <div class="lm-post-meta"><b>Bu Guru</b><span class="lm-role">Guru</span><small>2
                                        jam lalu</small></div>
                                <p>Selamat datang! Setelah membaca E-Modul, coba tulis dengan kata-katamu sendiri apa itu
                                    bilangan bulat, atau tanyakan bagian yang masih membingungkan.</p>
                            </div>
                        </div>
                        <div class="lm-post">
                            <span class="lm-avatar">R</span>
                            <div>
                                <div class="lm-post-meta"><b>Rani</b><small>1 jam lalu</small></div>
                                <p>Aku paham kalau bilangan negatif letaknya di kiri nol. Tapi kenapa −3 lebih kecil dari
                                    −1 padahal angka 3 lebih besar dari 1?</p>
                            </div>
                        </div>
                        <div class="lm-post">
                            <span class="lm-avatar">D</span>
                            <div>
                                <div class="lm-post-meta"><b>Dimas</b><small>30 menit lalu</small></div>
                                <p>Pemahamanku: bilangan bulat itu semua bilangan tanpa koma, ada negatif, nol, dan
                                    positif. Betul ya, Bu?</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- SIDEBAR: PROGRES + DAFTAR ISI --}}
        <aside class="lm-side">
            <h3>Isi Materi</h3>

            <div class="lm-progress-row">
                <span>{{ $isTeacher ? 'Rata-rata progres kelas' : 'Progres materi' }}</span>
                <span id="lm-percent">0%</span>
            </div>
            <div class="lm-track">
                <div id="lm-bar" data-target="{{ $m['progress'] }}"></div>
            </div>

            <nav class="lm-toc" id="lm-toc">
                <a href="#lm-modul" class="active" data-target="lm-modul">
                    <span class="n">1</span>
                    <span>E-Modul / Bacaan<small>Mulai dari sini</small></span>
                </a>
                <a href="#lm-lampiran" data-target="lm-lampiran">
                    <span class="n">2</span>
                    <span>Lampiran Guru<small>Video &amp; berkas</small></span>
                </a>
                <a href="#lm-diskusi" data-target="lm-diskusi">
                    <span class="n">3</span>
                    <span>Pemahaman &amp; Pertanyaan<small>Tulis &amp; diskusi</small></span>
                </a>
            </nav>

            @if ($isTeacher)
                <button type="button" class="lm-btn block" onclick="alert('Route Detail Progres Siswa belum dibuat')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Cek Detail Progres Siswa
                </button>
            @else
                <button type="button" class="lm-btn block" id="lm-finish">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Tandai Selesai</span>
                </button>
                <a href="{{ url('/latihan') }}" class="lm-btn outline block">
                    Lanjut ke E-LKPD
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            @endif
        </aside>
    </div>

    <script>
        (function() {
            var AUTHOR = @json($userName);
            var IS_TEACHER = @json($isTeacher);

            /* ----- Progres (animasi isi bar + angka) ----- */
            var bar = document.getElementById('lm-bar');
            var pct = document.getElementById('lm-percent');
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

            /* ----- Tandai selesai (simulasi) ----- */
            var finish = document.getElementById('lm-finish');
            if (finish) {
                finish.addEventListener('click', function() {
                    if (finish.classList.contains('done')) return;
                    finish.classList.add('done');
                    finish.querySelector('span').textContent = 'Selesai dipelajari';
                    setProgress(100);
                });
            }

            /* ----- Daftar isi: klik = scroll halus, scroll = tandai bagian aktif ----- */
            var links = Array.prototype.slice.call(document.querySelectorAll('#lm-toc a'));
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
                    io.observe(document.getElementById(a.getAttribute('data-target')));
                });
            }

            /* ----- Satu kolom pemahaman / pertanyaan (simulasi, belum ke database) ----- */
            var form = document.getElementById('lm-form');
            var text = document.getElementById('lm-text');
            var count = document.getElementById('lm-count');
            var posts = document.getElementById('lm-posts');
            var postCount = document.getElementById('lm-post-count');

            text.addEventListener('input', function() {
                count.textContent = text.value.length;
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var value = text.value.trim();
                if (!value) {
                    text.focus();
                    return;
                }

                var post = document.createElement('div');
                post.className = 'lm-post is-new' + (IS_TEACHER ? ' teacher' : '');

                var avatar = document.createElement('span');
                avatar.className = 'lm-avatar';
                avatar.textContent = AUTHOR.charAt(0).toUpperCase();

                var body = document.createElement('div');
                var meta = document.createElement('div');
                meta.className = 'lm-post-meta';
                var name = document.createElement('b');
                name.textContent = AUTHOR;
                meta.appendChild(name);
                if (IS_TEACHER) {
                    var role = document.createElement('span');
                    role.className = 'lm-role';
                    role.textContent = 'Guru';
                    meta.appendChild(role);
                }
                var time = document.createElement('small');
                time.textContent = 'Baru saja';
                meta.appendChild(time);

                var p = document.createElement('p');
                p.textContent = value;

                body.appendChild(meta);
                body.appendChild(p);
                post.appendChild(avatar);
                post.appendChild(body);
                posts.insertBefore(post, posts.firstChild);

                postCount.textContent = posts.children.length;
                text.value = '';
                count.textContent = '0';
            });
        })();
    </script>
@endsection

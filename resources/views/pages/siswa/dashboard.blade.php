@extends('layouts.app')

@section('title', 'Dashboard Siswa | MathLeaf')
@section('header_title', 'Dashboard Siswa')

@section('content')
    @php
        // Ikon SVG yang dipakai di halaman ini (satu tempat, dipanggil lewat {!! $icon['nama'] !!})
        $svg = fn(string $paths, string $class = 'ic') => '<svg class="' .
            $class .
            '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' .
            $paths .
            '</svg>';

        $icon = [
            'arrow' => $svg(
                '<line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline>',
            ),
            'calendar' => $svg(
                '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
            ),
            'sprout' => $svg(
                '<path d="M7 20h10"></path><path d="M10 20c5.5-2.5.8-6.4 3-10"></path><path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z"></path><path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z"></path>',
            ),
            'check' => $svg('<polyline points="20 6 9 17 4 12"></polyline>'),
            'check_circle' => $svg(
                '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>',
            ),
            'alert' => $svg(
                '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>',
            ),
            'star' => $svg(
                '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>',
            ),
            'close' => $svg('<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>'),
        ];
    @endphp

    <style>
        /* ==========================================================
               Dashboard Siswa — satu bahasa desain dengan Materi & Forum
               ========================================================== */
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

        .ic {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
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

        /* ---------- Header halaman ---------- */
        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            margin-bottom: .4rem;
            color: var(--ml-green);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .page-heading h1 {
            margin: 0 0 .4rem;
            color: #14301f;
            font-size: clamp(1.44rem, 2.4vw, 1.84rem);
            font-weight: 800;
            line-height: 1.2;
        }

        .page-heading p {
            margin: 0;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .badge-date {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .5rem 1rem;
            border-radius: 999px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            font-size: 0.76rem;
            font-weight: 800;
        }

        .badge-date .ic {
            width: 15px;
            height: 15px;
        }

        /* ---------- Tombol & tautan ---------- */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            padding: .75rem 1.2rem;
            border: 0;
            border-radius: 10px;
            background: var(--ml-green);
            color: #ffffff;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
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

        .btn-primary .ic {
            width: 16px;
            height: 16px;
        }

        .btn-block {
            width: 100%;
            margin-top: .6rem;
        }

        .link-more {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            color: var(--ml-green);
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .link-more .ic {
            width: 14px;
            height: 14px;
            transition: transform .2s;
        }

        .link-more:hover {
            color: var(--ml-green-hover);
        }

        .link-more:hover .ic {
            transform: translateX(3px);
        }

        /* ---------- Banner + tanaman interaktif ---------- */
        .dashboard-banner {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 220px;
            margin-bottom: 1.5rem;
            padding: 2rem;
            overflow: hidden;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .05);
        }

        .banner-text-content {
            position: relative;
            z-index: 2;
            max-width: 60%;
        }

        .banner-text-content h2 {
            margin: .4rem 0 .9rem;
            color: var(--ml-green-hover);
            font-size: clamp(1.3rem, 2.2vw, 1.6rem);
            font-weight: 800;
            line-height: 1.3;
        }

        .banner-text-content p {
            margin: 0 0 1.5rem;
            color: #334155;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        #dashboardPlantCanvas {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
            width: 40%;
            height: 100%;
            cursor: pointer;
        }

        .notif-badge-overlay {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 2;
            display: none;
            /* ditampilkan (display:flex) oleh script bila ada notifikasi */
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #ef4444;
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 800;
            cursor: pointer;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* ---------- Pop-up aktivitas ---------- */
        .custom-popup-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            background: rgba(20, 48, 31, .55);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s;
        }

        .custom-popup-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .custom-popup-box {
            width: 100%;
            max-width: 440px;
            max-height: 92vh;
            overflow-y: auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 24px 48px rgba(8, 101, 52, .18);
            transform: translateY(20px);
            transition: transform .3s cubic-bezier(.175, .885, .32, 1.275);
        }

        .custom-popup-overlay.active .custom-popup-box {
            transform: translateY(0);
        }

        .popup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.1rem 1.5rem;
            background: var(--ml-green-light);
            border-bottom: 1px solid #dfe9e2;
        }

        .popup-header h3 {
            margin: 0;
            color: #14301f;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .popup-close {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 50%;
            background: #ffffff;
            color: var(--ml-green);
            cursor: pointer;
            transition: background .2s;
        }

        .popup-close:hover {
            background: var(--ml-green-soft);
        }

        .popup-close .ic {
            width: 16px;
            height: 16px;
        }

        .popup-body {
            padding: 1.5rem;
        }

        .popup-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .8rem;
            text-align: center;
        }

        .popup-empty-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--ml-green-soft);
            color: var(--ml-green);
        }

        .popup-empty-icon .ic {
            width: 24px;
            height: 24px;
        }

        .popup-empty p {
            margin: 0;
            color: #475569;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .notif-list-item {
            margin-bottom: .75rem;
            padding: 1rem;
            background: #f6fbf8;
            border: 1px solid #dfe9e2;
            border-left: 4px solid var(--ml-green);
            border-radius: 10px;
        }

        .notif-list-item strong {
            display: block;
            margin-bottom: .2rem;
            color: #14301f;
            font-size: 0.88rem;
        }

        .notif-list-item small {
            color: #64748b;
            font-size: 0.76rem;
        }

        /* ---------- Banner kosong (belum ikut kelas) ---------- */
        .dashboard-empty-banner {
            display: flex;
            flex-direction: column;
            gap: .4rem;
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-left: 5px solid var(--ml-green);
            border-radius: 12px;
            color: #334155;
            font-size: 0.88rem;
            line-height: 1.6;
        }

        .dashboard-empty-banner strong {
            color: var(--ml-green-hover);
        }

        /* ---------- Kartu statistik ---------- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .stat-card {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.4rem;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            transition: transform .25s cubic-bezier(.2, .8, .2, 1), box-shadow .25s, border-color .25s;
            animation: miUp .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        .stat-card:nth-child(2) {
            animation-delay: 60ms;
        }

        .stat-card:nth-child(3) {
            animation-delay: 120ms;
        }

        .stat-card:nth-child(4) {
            animation-delay: 180ms;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: #b5d9c1;
            box-shadow: 0 12px 24px rgba(8, 101, 52, .08);
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .stat-icon.is-blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        .stat-icon.is-red {
            background: #fef2f2;
            color: #dc2626;
        }

        .stat-icon.is-amber {
            background: #fef9c3;
            color: #ca8a04;
        }

        .stat-icon.sm {
            width: 34px;
            height: 34px;
            border-radius: 10px;
        }

        .stat-icon.sm .ic {
            width: 16px;
            height: 16px;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .stat-label {
            margin-bottom: .3rem;
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .stat-value {
            margin-bottom: .3rem;
            color: #14301f;
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-desc {
            color: #64748b;
            font-size: 0.74rem;
            line-height: 1.4;
        }

        /* ---------- Ringkasan cepat ---------- */
        .insight-bar {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .insight-bar a {
            display: flex;
            flex-direction: column;
            gap: .3rem;
            padding: 1rem 1.2rem;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 14px;
            color: var(--ml-green);
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            transition: transform .2s, border-color .2s, box-shadow .2s;
        }

        .insight-bar a:hover {
            transform: translateY(-3px);
            border-color: #b5d9c1;
            box-shadow: 0 10px 20px rgba(8, 101, 52, .08);
        }

        .insight-bar strong {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .insight-bar span {
            color: #475569;
            font-size: 0.82rem;
            font-weight: 600;
        }

        /* ---------- Kolom utama ---------- */
        .dashboard-columns {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.25rem;
            align-items: start;
        }

        .dashboard-columns>* {
            min-width: 0;
        }

        .dash-stack {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .panel {
            padding: 1.4rem;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
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
            margin: 0;
            color: #14301f;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .panel-note {
            margin: 0;
            padding-top: .8rem;
            color: #64748b;
            font-size: 0.84rem;
            line-height: 1.5;
        }

        .panel-empty {
            margin: 0;
            padding: 1.5rem 1rem;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-radius: 12px;
            color: #64748b;
            font-size: 0.86rem;
            line-height: 1.6;
            text-align: center;
        }

        .panel-warning {
            margin: .9rem 0 0;
            padding: .7rem .9rem;
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            border-radius: 0 10px 10px 0;
            color: #b91c1c;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.5;
        }

        /* ---------- Baris-baris daftar ---------- */
        .activity-row {
            display: flex;
            align-items: flex-start;
            gap: .9rem;
            padding: .9rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .activity-row:first-of-type {
            padding-top: 0;
        }

        .activity-row:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .activity-row strong,
        .list-row strong,
        .task-row strong {
            display: block;
            color: #14301f;
            font-size: 0.9rem;
        }

        .activity-row small,
        .list-row small,
        .task-row small {
            display: block;
            margin-top: .25rem;
            color: #64748b;
            font-size: 0.78rem;
        }

        .activity-row a,
        .list-link {
            color: #14301f;
            text-decoration: none;
        }

        .activity-row a:hover strong,
        .list-link:hover strong {
            color: var(--ml-green);
        }

        .material-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: .45rem;
            padding: .75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .material-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .material-row a {
            color: var(--ml-green-hover);
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        .material-row a:hover {
            color: var(--ml-green);
        }

        .material-row span {
            color: #475569;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .progress-track {
            grid-column: span 2;
            height: 8px;
            overflow: hidden;
            background: var(--ml-green-soft);
            border-radius: 999px;
        }

        .progress-track>div {
            height: 100%;
            background: var(--ml-green);
            border-radius: 999px;
            transition: width .6s cubic-bezier(.2, .8, .2, 1);
        }

        .task-row {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: .7rem 0;
            border-bottom: 1px solid #f1f5f9;
            color: inherit;
            text-decoration: none;
        }

        .task-row:hover strong {
            color: var(--ml-green);
        }

        .date-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            background: var(--ml-green-light);
            border: 1px solid #dfe9e2;
            border-radius: 10px;
            color: var(--ml-green-hover);
            font-weight: 800;
            line-height: 1;
        }

        .date-block small {
            margin-top: .15rem;
            color: var(--ml-green);
            font-size: 0.6rem;
            font-weight: 700;
        }

        .list-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .8rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .list-row:last-child,
        .task-row:last-of-type {
            border-bottom: 0;
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 900px) {
            .dashboard-columns {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .banner-text-content {
                max-width: 100%;
            }

            #dashboardPlantCanvas {
                width: 100%;
                opacity: .3;
            }
        }

        @media (max-width: 620px) {
            .insight-bar {
                gap: .5rem;
            }

            .insight-bar a {
                padding: .7rem;
            }

            .insight-bar strong {
                font-size: 1.2rem;
            }

            .insight-bar span {
                font-size: 0.7rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .stat-card,
            .notif-badge-overlay {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow">RUANG BERTUMBUHMU</span>
            <h1>Halo, {{ auth()->user()->name }}! Selamat Datang!</h1>
            <p>
                @if ($activeClass)
                    Ringkasan pembelajaran kelas {{ $activeClass->name }}.
                @else
                    Ringkasan pembelajaran dari kelas yang kamu ikuti.
                @endif
            </p>
        </div>
        <span class="badge-date">{!! $icon['calendar'] !!} {{ now()->translatedFormat('l, d F Y') }}</span>
    </div>

    @php
        $unreadCount = $pendingCount + $remedialQuizzes->count();
        $bannerTitle =
            $unreadCount > 0
                ? "Ada {$unreadCount} hal yang perlu kamu kerjakan."
                : 'Semua tugas dan remedialmu sudah beres!';
        $bannerDesc =
            $unreadCount > 0
                ? 'Cek tugas yang belum dikumpulkan dan kuis yang bisa diremedial. Klik tanaman untuk melihat rinciannya.'
                : 'Lanjutkan belajar dengan membaca materi atau mengerjakan kuis baru.';
    @endphp

    <section class="dashboard-banner">
        <div class="banner-text-content">
            <span class="eyebrow">MATHLEAF · AKTIVITASMU</span>
            <h2>{{ $bannerTitle }}</h2>
            <p>{{ $bannerDesc }}</p>
            <a class="btn-primary" href="{{ $pendingCount ? route('classroom.tasks.index') : route('materi.index') }}">
                {{ $pendingCount ? 'Lihat Tugas' : 'Lanjut Belajar' }} {!! $icon['arrow'] !!}
            </a>
        </div>
        <canvas id="dashboardPlantCanvas" onclick="openInteractivePopup()"
            title="Klik untuk melihat aktivitas belajar"></canvas>
        <div id="notifBadge" class="notif-badge-overlay" onclick="openInteractivePopup()">{{ $unreadCount }}</div>
    </section>

    <div id="interactive-popup" class="custom-popup-overlay" role="dialog" aria-modal="true"
        aria-label="Aktivitas yang perlu dikerjakan">
        <div class="custom-popup-box">
            <div class="popup-header">
                <h3>Aktivitas MathLeaf</h3>
                <button type="button" class="popup-close" onclick="closeInteractivePopup()"
                    aria-label="Tutup">{!! $icon['close'] !!}</button>
            </div>
            <div class="popup-body">
                @if ($unreadCount === 0)
                    <div class="popup-empty">
                        <span class="popup-empty-icon">{!! $icon['sprout'] !!}</span>
                        <p>Belum ada tugas tertunda atau kuis remedial. Terus belajar, satu langkah setiap hari!</p>
                    </div>
                @else
                    @foreach ($pendingTasks->take(4) as $task)
                        <div class="notif-list-item">
                            <strong>Tugas: {{ $task->title }}</strong>
                            <small>{{ $task->due_at ? 'Deadline: ' . $task->due_at->translatedFormat('d M Y H:i') : 'Tanpa deadline' }}</small>
                        </div>
                    @endforeach
                    @foreach ($remedialQuizzes->take(3) as $quiz)
                        <div class="notif-list-item">
                            <strong>Remedial: {{ $quiz->title }}</strong>
                            <small>Sisa kesempatan:
                                {{ max(0, (int) $quiz->max_attempts - $quiz->attempts->count()) }}</small>
                        </div>
                    @endforeach
                    <a class="btn-primary btn-block" href="{{ route('classroom.tasks.index') }}">Buka Daftar Tugas
                        {!! $icon['arrow'] !!}</a>
                @endif
            </div>
        </div>
    </div>

    @if ($classes->isEmpty())
        <div class="dashboard-empty-banner">
            <strong>Belum ada kelas yang diikuti.</strong>
            Gabung kelas menggunakan kode dari guru agar materi, tugas, dan nilai muncul di sini.
            <a class="link-more" href="{{ route('classroom.home') }}">Gabung kelas {!! $icon['arrow'] !!}</a>
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-icon">{!! $icon['sprout'] !!}</span>
            <div class="stat-content">
                <span class="stat-label">Progres Materi</span>
                <span class="stat-value">{{ $materialProgress }}%</span>
                <span class="stat-desc">{{ $materialsCompleted }} dari {{ $materials->count() }} materi selesai</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon is-blue">{!! $icon['check_circle'] !!}</span>
            <div class="stat-content">
                <span class="stat-label">Tugas Dikumpulkan</span>
                <span class="stat-value">{{ $submittedCount }}</span>
                <span class="stat-desc">dari {{ $tasks->count() }} tugas</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon is-red">{!! $icon['alert'] !!}</span>
            <div class="stat-content">
                <span class="stat-label">Tugas Belum Selesai</span>
                <span class="stat-value">{{ $pendingCount }}</span>
                <span class="stat-desc">{{ $overdueCount }} sudah melewati deadline</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon is-amber">{!! $icon['star'] !!}</span>
            <div class="stat-content">
                <span class="stat-label">Rata-rata Nilai</span>
                <span
                    class="stat-value">{{ $averageScore === null ? '—' : number_format($averageScore, 1, ',', '.') }}</span>
                <span class="stat-desc">Nilai kuis terbaru dan tugas dinilai</span>
            </div>
        </div>
    </div>

    <div class="insight-bar">
        <a href="{{ route('siswa.kuis') }}">
            <strong>{{ $availableQuizzes->count() }}</strong><span>Kuis tersedia</span>
        </a>
        <a href="{{ route('siswa.kuis') }}">
            <strong>{{ $remedialQuizzes->count() }}</strong><span>Remedial tersedia</span>
        </a>
        <a href="{{ route('materi.index') }}">
            <strong>{{ $materials->count() }}</strong><span>Materi terbit</span>
        </a>
    </div>

    <div class="dashboard-columns">
        <div class="dash-stack">
            <section class="panel">
                <div class="panel-head">
                    <h2>Aktivitas Belajarmu</h2>
                    <a class="link-more" href="{{ route('siswa.nilai') }}">Lihat nilai {!! $icon['arrow'] !!}</a>
                </div>
                @forelse($recentActivities as $activity)
                    <div class="activity-row">
                        <span class="stat-icon sm">{!! $icon['check'] !!}</span>
                        <div>
                            <a href="{{ $activity->url }}"><strong>{{ $activity->title }}</strong></a>
                            <small>{{ $activity->detail }} · {{ $activity->date?->diffForHumans() ?? '-' }}</small>
                        </div>
                    </div>
                @empty
                    <p class="panel-empty">Belum ada aktivitas belajar. Mulailah dari materi atau tugas kelas.</p>
                @endforelse
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h2>Materi untuk Dilanjutkan</h2>
                    <a class="link-more" href="{{ route('materi.index') }}">Semua materi {!! $icon['arrow'] !!}</a>
                </div>
                @forelse($incompleteMaterials as $material)
                    @php $percent = (int)($progressById[$material->id]->progress_percent ?? 0); @endphp
                    <div class="material-row">
                        <a href="{{ route('materi.show', $material) }}">{{ $material->title }}</a>
                        <span>{{ $percent }}%</span>
                        <div class="progress-track">
                            <div style="width:{{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="panel-empty">Belum ada materi yang perlu dilanjutkan.</p>
                @endforelse
            </section>
        </div>

        <div class="dash-stack">
            <section class="panel">
                <div class="panel-head">
                    <h2>Deadline Tugas Terdekat</h2>
                    <a class="link-more" href="{{ route('classroom.tasks.index') }}">Lihat tugas
                        {!! $icon['arrow'] !!}</a>
                </div>
                @forelse($upcomingTasks as $task)
                    <a class="task-row" href="{{ route('classroom.tasks.index') }}">
                        <div class="date-block">
                            {{ $task->due_at->format('d') }}<small>{{ strtoupper($task->due_at->translatedFormat('M')) }}</small>
                        </div>
                        <div><strong>{{ $task->title }}</strong><small>Deadline
                                {{ $task->due_at->translatedFormat('d M Y, H:i') }}</small></div>
                    </a>
                @empty
                    <p class="panel-empty">Belum ada deadline tugas mendatang.</p>
                @endforelse
                @if ($overdueCount > 0)
                    <p class="panel-warning">{{ $overdueCount }} tugas belum dikumpulkan dan sudah melewati deadline.</p>
                @endif
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h2>Kuis & Remedial</h2>
                    <a class="link-more" href="{{ route('siswa.kuis') }}">Lihat semua {!! $icon['arrow'] !!}</a>
                </div>
                @forelse($availableQuizzes->take(4) as $quiz)
                    <div class="list-row">
                        <div><strong>{{ $quiz->title }}</strong><small>{{ $quiz->attempts->count() > 0 ? 'Remedial' : 'Kuis baru' }}
                                · KKM {{ $quiz->passing_score ?? 70 }}</small></div>
                        <a class="link-more" href="{{ route('classroom.quiz.take', $quiz) }}">Kerjakan
                            {!! $icon['arrow'] !!}</a>
                    </div>
                @empty
                    <p class="panel-empty">Belum ada kuis yang perlu dikerjakan.</p>
                @endforelse
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h2>Diskusi Kelas Terbaru</h2>
                    <a class="link-more" href="{{ route('forum.index') }}">Buka forum {!! $icon['arrow'] !!}</a>
                </div>
                @forelse($recentForumThreads as $thread)
                    <div class="list-row">
                        <div>
                            <a class="list-link"
                                href="{{ route('forum.index', ['class_id' => $thread->class_id], false) }}"><strong>{{ $thread->title }}</strong></a>
                            <small>{{ $thread->schoolClass?->name ?? 'Kelas' }} ·
                                {{ $thread->created_at?->diffForHumans() }}
                                @if ($thread->is_pinned)
                                    · Disematkan
                                @endif
                                @if ($thread->is_locked)
                                    · Dikunci
                                @endif
                            </small>
                        </div>
                    </div>
                @empty
                    <p class="panel-empty">Belum ada diskusi kelas. Kamu bisa memulai pertanyaan baru di forum.</p>
                @endforelse
            </section>
        </div>
    </div>

    <!-- SCRIPTS: Logic Tumbuhan 3D Math Plant Khusus Banner -->
    <script>
        // POP-UP LOGIC
        function openInteractivePopup() {
            document.getElementById('interactive-popup').classList.add('active');
        }

        function closeInteractivePopup() {
            document.getElementById('interactive-popup').classList.remove('active');
        }
        document.getElementById('interactive-popup').addEventListener('click', function(e) {
            if (e.target === this) closeInteractivePopup();
        });

        // CANVAS ANIMATION LOGIC
        const notifCount = {{ $unreadCount }};
        const canvas = document.getElementById('dashboardPlantCanvas');
        const ctx = canvas.getContext('2d');

        // Tampilkan badge angka di canvas jika > 0
        if (notifCount > 0) {
            document.getElementById('notifBadge').style.display = 'flex';
        }

        let w, h;

        function resize() {
            w = canvas.width = canvas.offsetWidth;
            h = canvas.height = canvas.offsetHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        const leaves = [];
        const bubbles = [];
        const mathChars = ['+', '−', '×', '÷', '=', '%', '½'];

        let rootX = w * 0.7; // Akar tanaman berada sedikit ke kanan
        let rootY = h + 20;

        function addLeaf(targetX, targetY) {
            let baseAng = (Math.random() * Math.PI) - (Math.random() * Math.PI);
            leaves.push({
                x: targetX,
                y: targetY,
                baseAngle: baseAng,
                angle: baseAng,
                scale: 0,
                maxScale: Math.random() * 0.5 + 0.5,
                growthRate: 0.05,
                swaySpeed: Math.random() * 0.0015 + 0.001,
                swayOffset: Math.random() * Math.PI * 2,
                swayAmount: Math.random() * 0.15 + 0.05
            });
            spawnBubble(targetX, targetY);
        }

        function spawnBubble(x, y) {
            bubbles.push({
                x: x + (Math.random() * 40 - 20),
                y: y + (Math.random() * 40 - 20),
                char: mathChars[Math.floor(Math.random() * mathChars.length)],
                speedY: -(Math.random() * 1.5 + 0.5),
                speedX: (Math.random() - 0.5) * 1.0,
                opacity: 1,
                size: Math.random() * 10 + 10,
                drift: Math.random() * Math.PI * 2
            });
        }

        function drawLeaf(leaf) {
            ctx.save();
            ctx.translate(leaf.x, leaf.y);
            ctx.rotate(leaf.angle);
            ctx.scale(leaf.scale, leaf.scale);

            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.quadraticCurveTo(35, -35, 70, -10);
            ctx.quadraticCurveTo(35, 25, 0, 0);

            let gradient = ctx.createLinearGradient(0, 0, 70, 0);
            gradient.addColorStop(0, '#34d399');
            gradient.addColorStop(1, '#059669');

            ctx.fillStyle = gradient;
            ctx.fill();

            ctx.strokeStyle = 'rgba(255,255,255,0.4)';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.quadraticCurveTo(35, -10, 65, -10);
            ctx.stroke();

            ctx.restore();
        }

        // Inisialisasi: Jika ada notif, tumbuhkan daun sesuai jumlah notif (max 6)
        if (notifCount > 0) {
            let maxLeaves = Math.min(notifCount, 6);
            for (let i = 0; i < maxLeaves; i++) {
                setTimeout(() => {
                    let ranX = (w * 0.5) + (Math.random() * (w * 0.3));
                    let ranY = (h * 0.6) - (Math.random() * (h * 0.4));
                    addLeaf(ranX, ranY);
                }, i * 300);
            }
        }

        function animate(time) {
            ctx.clearRect(0, 0, w, h);

            if (notifCount > 0) {
                // Gambar Akar
                rootX = w * 0.6;
                ctx.strokeStyle = '#6ee7b7';
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                let rootSway = Math.sin(time * 0.0005) * 10;

                leaves.forEach(leaf => {
                    leaf.angle = leaf.baseAngle + Math.sin(time * leaf.swaySpeed + leaf.swayOffset) * leaf
                        .swayAmount;
                    ctx.beginPath();
                    ctx.moveTo(rootX + rootSway, rootY);
                    ctx.quadraticCurveTo(rootX, leaf.y + 50, leaf.x, leaf.y);
                    ctx.stroke();
                });

                leaves.forEach(leaf => {
                    if (leaf.scale < leaf.maxScale) leaf.scale += leaf.growthRate;
                    drawLeaf(leaf);
                    if (Math.random() < 0.005) spawnBubble(leaf.x, leaf.y);
                });

                for (let i = bubbles.length - 1; i >= 0; i--) {
                    let b = bubbles[i];
                    b.x += b.speedX + Math.sin(time * 0.002 + b.drift) * 0.5;
                    b.y += b.speedY;
                    b.opacity -= 0.008;
                    if (b.opacity <= 0) {
                        bubbles.splice(i, 1);
                        continue;
                    }

                    ctx.save();
                    ctx.globalAlpha = b.opacity;
                    ctx.fillStyle = '#6ee7b7'; // Warna text hijau terang
                    ctx.font = `bold ${b.size}px 'Inter', sans-serif`;
                    ctx.fillText(b.char, b.x, b.y);
                    ctx.restore();
                }
            }
            requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
    </script>
@endsection

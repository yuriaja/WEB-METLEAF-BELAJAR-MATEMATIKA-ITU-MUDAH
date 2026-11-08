@extends('layouts.app')

@section('title', 'Dashboard Guru | MathLeaf')
@section('header_title', 'Dashboard Guru')

@section('content')
    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-border: #bbf7d0;
        }

        /* Dashboard Header Padat */
        .teacher-dashboard-header {
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--ml-green-light) 0%, #ffffff 100%);
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--ml-green-border);
            box-shadow: 0 4px 15px rgba(8, 101, 52, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .teacher-dashboard-header-text h1 {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--ml-green-hover);
            letter-spacing: -0.02em;
            margin: 0 0 0.25rem 0;
        }

        .teacher-dashboard-header-text p {
            color: #334155;
            font-size: 0.9rem;
            margin: 0;
        }

        /* Kartu Statistik Dipadatkan */
        .stats-card-group {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-cell {
            background: #ffffff;
            border: 1px solid var(--ml-green-border);
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            box-shadow: 0 4px 6px rgba(8, 101, 52, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-cell:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(8, 101, 52, 0.08);
        }

        .stat-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-body {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .stat-body .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .stat-body .stat-label {
            font-size: 0.8rem;
            color: #475569;
            font-weight: 600;
            margin-top: 0.25rem;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        /* Layout 2 Kolom Bawah */
        .dashboard-two-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 1024px) {
            .stats-card-group {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dashboard-two-cols {
                grid-template-columns: 1fr;
            }

            .teacher-dashboard-header {
                flex-direction: column;
                text-align: left;
                align-items: flex-start;
                gap: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .stats-card-group {
                grid-template-columns: 1fr;
            }
        }

        .panel-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 3px solid var(--ml-green);
            border-radius: 10px;
            padding: 1.25rem;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.04);
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .panel-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Aksi Cepat Buttons Lebih Rapat */
        .quick-actions-grid {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .action-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: #ffffff;
            border: 1px solid var(--ml-green-border);
            border-left: 3px solid transparent;
            border-radius: 8px;
            text-decoration: none;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .action-item:hover {
            background: var(--ml-green-light);
            border-color: var(--ml-green-border);
            border-left-color: var(--ml-green);
            color: var(--ml-green-hover);
            transform: translateX(4px);
        }

        .action-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .action-icon {
            color: var(--ml-green);
            background: #ffffff;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(8, 101, 52, 0.08);
            transition: all 0.2s ease;
        }

        .action-item:hover .action-icon {
            background: var(--ml-green);
            color: #ffffff;
        }

        .action-arrow {
            color: var(--ml-green);
            font-size: 1rem;
            opacity: 0.5;
            transition: opacity 0.2s ease;
        }

        .action-item:hover .action-arrow {
            opacity: 1;
        }

        /* Log Aktivitas Rapat */
        .activity-feed {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;

            /* Kunci tinggi maksimal persis seukuran 4 tombol Aksi Cepat */
            max-height: 230px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        /* Desain scrollbar tipis agar rapi */
        .activity-feed::-webkit-scrollbar {
            width: 6px;
        }

        .activity-feed::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 8px;
        }

        .activity-feed::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }

        .activity-feed::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .activity-row {
            display: flex;
            gap: 0.85rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 0.85rem;
            align-items: flex-start;
        }

        a.activity-row {
            color: inherit;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color .15s, transform .15s;
        }

        a.activity-row:hover,
        a.activity-row:focus-visible {
            background: var(--ml-green-light);
            transform: translateX(3px);
            outline: none;
        }

        .activity-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .activity-time {
            color: var(--ml-green);
            background: var(--ml-green-light);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 700;
            white-space: nowrap;
            height: fit-content;
        }

        .activity-text {
            color: #475569;
            line-height: 1.4;
        }

        .activity-text strong {
            color: #0f172a;
        }

        /* Upgrade 4: ringkasan berbasis database, mengikuti tema hijau MathLeaf */
        .ml4-teacher-insights {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin: 0 0 1rem;
        }

        .ml4-teacher-insight {
            display: flex;
            flex-direction: column;
            gap: .25rem;
            background: #fff;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 1rem;
            min-width: 0;
        }

        .ml4-teacher-insight span {
            font-size: .77rem;
            color: #475569;
            font-weight: 600;
        }

        .ml4-teacher-insight strong {
            font-size: 1.55rem;
            color: #064d27;
        }

        .ml4-teacher-insight a,
        .ml4-teacher-deadlines a {
            color: #086534;
            text-decoration: none;
            font-size: .75rem;
            font-weight: 700;
        }

        .ml4-teacher-deadlines {
            background: white;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
        }

        .ml4-teacher-deadline-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: .7rem 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: .82rem;
        }

        a.ml4-teacher-deadline-row {
            color: inherit;
            text-decoration: none;
        }

        a.ml4-teacher-deadline-row:hover,
        a.ml4-teacher-deadline-row:focus-visible {
            color: #086534;
            background: #edf8f0;
        }

        .ml4-teacher-deadline-row:last-child {
            border-bottom: 0;
        }

        .ml4-teacher-deadline-row span {
            color: #475569;
            flex-shrink: 0;
        }

        .ml4-empty {
            color: #64748b;
            font-size: .82rem;
            margin: 0;
        }

        @media(max-width:900px) {
            .ml4-teacher-insights {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width:500px) {
            .ml4-teacher-insights {
                grid-template-columns: 1fr;
            }

            .ml4-teacher-deadline-row {
                flex-direction: column;
                gap: .2rem;
            }
        }
    </style>

    <!-- HEADER -->
    <div class="teacher-dashboard-header">
        <div class="teacher-dashboard-header-text">
            <h1>Selamat Datang, {{ auth()->user()->name ?? 'Guru' }}</h1>
            <p>Ringkasan aktivitas pembelajaran dan manajemen kelas hari ini.</p>
        </div>
        <!-- Ikon Daun Halus sebagai aksen visual -->
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)" stroke-width="1.5"
            stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.2;">
            <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"></path>
            <path d="M4 21c0-6 5-11 11-14"></path>
        </svg>
    </div>

    <!-- 4 STATISTIK MELAYANG -->
    <div class="stats-card-group">
        <div class="stat-cell">
            <div class="stat-icon-box">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <div class="stat-body">
                <span class="stat-value">{{ $classes->count() ?? 0 }}</span>
                <span class="stat-label">Total Kelas</span>
            </div>
        </div>

        <div class="stat-cell">
            <div class="stat-icon-box">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-body">
                <span class="stat-value">{{ $uniqueStudentCount }}</span>
                <span class="stat-label">Total Siswa</span>
            </div>
        </div>

        <div class="stat-cell">
            <div class="stat-icon-box">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
            </div>
            <div class="stat-body">
                <span class="stat-value">{{ $pendingTasks ?? 0 }}</span>
                <span class="stat-label">Menunggu Dinilai</span>
            </div>
        </div>

        <div class="stat-cell">
            <div class="stat-icon-box">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </div>
            <div class="stat-body">
                <span class="stat-value">{{ $forumThreadsCount ?? 0 }}</span>
                <span class="stat-label">Topik Diskusi</span>
            </div>
        </div>
    </div>

    <div class="ml4-teacher-deadlines">
        <div class="panel-header">
            <h2 class="panel-title">Batas Waktu Tugas Terdekat</h2>
            <a href="{{ route('classroom.tasks.index') }}">Lihat semua tugas →</a>
        </div>
        @forelse ($upcomingTaskDeadlines as $task)
            <a class="ml4-teacher-deadline-row" href="{{ $task->url }}">
                <strong>{{ $task->title }}</strong>
                <span>{{ $task->due_at->translatedFormat('d M Y, H:i') }}</span>
            </a>
        @empty
            <p class="ml4-empty">Belum ada tugas dengan deadline mendatang di kelasmu.</p>
        @endforelse
    </div>

    <!-- PANEL BAWAH -->
    <div class="dashboard-two-cols">

        <!-- Aksi Cepat -->
        <div class="panel-box">
            <div class="panel-header">
                <h2 class="panel-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                    Aksi Cepat
                </h2>
            </div>
            <div class="quick-actions-grid">
                <a href="{{ route('classroom.home') }}" class="action-item">
                    <div class="action-left">
                        <span class="action-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 7v14" />
                                <path
                                    d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z" />
                            </svg>
                        </span>
                        <span>Kelola Kelas & Siswa</span>
                    </div>
                    <span class="action-arrow">→</span>
                </a>

                <a href="{{ route('teacher.materials.create') }}" class="action-item">
                    <div class="action-left">
                        <span class="action-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="6 3 20 12 6 21 6 3" />
                            </svg>
                        </span>
                        <span>Unggah Materi Pembelajaran</span>
                    </div>
                    <span class="action-arrow">→</span>
                </a>

                <a href="{{ route('teacher.quizzes') }}" class="action-item">
                    <div class="action-left">
                        <span class="action-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="6" />
                                <path d="m8 13-2 8 6-3 6 3-2-8" />
                            </svg>
                        </span>
                        <span>Buat Soal / Kuis Baru</span>
                    </div>
                    <span class="action-arrow">→</span>
                </a>

                <a href="{{ route('teacher.grades') }}" class="action-item">
                    <div class="action-left">
                        <span class="action-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                        </span>
                        <span>Lihat Rekap Nilai Siswa</span>
                    </div>
                    <span class="action-arrow">→</span>
                </a>
            </div>
        </div>

        <!-- Log Aktivitas Siswa -->
        <div class="panel-box">
            <div class="panel-header">
                <h2 class="panel-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    Aktivitas Siswa Terbaru
                </h2>
            </div>
            <div class="activity-feed">
                @forelse ($recentActivities as $activity)
                    <a class="activity-row" href="{{ $activity->url }}">
                        <div class="activity-time">{{ $activity->date?->diffForHumans() ?? '-' }}</div>
                        <div class="activity-text">
                            <strong>{{ $activity->actor }}</strong>
                            {{ $activity->action }} <em>{{ $activity->target }}</em>.
                        </div>
                    </a>
                @empty
                    <div class="activity-row">
                        <div class="activity-text" style="color:#64748b">
                            Belum ada aktivitas siswa di kelasmu. Aktivitas akan muncul setelah siswa mengumpulkan tugas,
                            menyelesaikan kuis, atau membuat dan membalas diskusi.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

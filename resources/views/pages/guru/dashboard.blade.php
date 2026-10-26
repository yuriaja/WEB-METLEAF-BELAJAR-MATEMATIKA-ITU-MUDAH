@extends('layouts.app')

@section('title', 'Dashboard Guru | MathLeaf')
@section('header_title', 'Dashboard Guru')

@section('content')
    <style>
        /* Dashboard Header */
        .teacher-dashboard-header {
            margin-bottom: 2rem;
        }

        .teacher-dashboard-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 0.25rem;
        }

        .teacher-dashboard-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* Modern Connected Stat Card */
        .stats-card-group {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .stat-cell {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-right: 1px solid #f1f5f9;
            transition: background-color 0.15s ease;
        }

        .stat-cell:last-child {
            border-right: none;
        }

        .stat-cell:hover {
            background-color: #f8fafc;
        }

        .stat-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 6px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #334155;
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
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .stat-body .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
            margin-top: 3px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        /* Layout 2 Kolom Bawah */
        .dashboard-two-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .panel-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .panel-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin: 0;
        }

        /* Aksi Cepat Buttons */
        .quick-actions-grid {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .action-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-decoration: none;
            color: #1e293b;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.15s ease;
        }

        .action-item:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            color: #086534;
        }

        .action-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .action-icon {
            color: #64748b;
            display: flex;
        }

        .action-item:hover .action-icon {
            color: #086534;
        }

        .action-arrow {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        /* Log Aktivitas */
        .activity-feed {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .activity-row {
            display: flex;
            gap: 0.85rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }

        .activity-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .activity-time {
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 500;
            min-width: 70px;
            padding-top: 2px;
        }

        .activity-text {
            color: #334155;
            line-height: 1.4;
        }

        .activity-text strong {
            color: #0f172a;
        }
    </style>

    <div class="teacher-dashboard-header">
        <h1>Selamat Datang, {{ auth()->user()->name }}</h1>
        <p>Ringkasan aktivitas pembelajaran dan manajemen kelas hari ini.</p>
    </div>

    <!-- 4 STATISTIK DALAM 1 KARTU TERHUBUNG -->
    <div class="stats-card-group">
        <div class="stat-cell">
            <div class="stat-icon-box">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
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
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-body">
                <span class="stat-value">{{ isset($classes) ? $classes->sum('students_count') : 0 }}</span>
                <span class="stat-label">Total Siswa</span>
            </div>
        </div>

        <div class="stat-cell">
            <div class="stat-icon-box">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
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
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
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

    <!-- PANEL BAWAH -->
    <div class="dashboard-two-cols">
        <!-- Aksi Cepat -->
        <div class="panel-box">
            <div class="panel-header">
                <h2 class="panel-title">Aksi Cepat</h2>
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

                <a href="{{ route('classroom.home') }}" class="action-item">
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
                <h2 class="panel-title">Aktivitas Siswa Terbaru</h2>
            </div>
            <div class="activity-feed">
                @forelse ($recentActivities as $activity)
                    <div class="activity-row">
                        <div class="activity-time">{{ $activity->date?->diffForHumans() ?? '-' }}</div>
                        <div class="activity-text">
                            <strong>{{ $activity->actor }}</strong>
                            {{ $activity->action }} <em>{{ $activity->target }}</em>.
                        </div>
                    </div>
                @empty
                    <div class="activity-row">
                        <div class="activity-text" style="color:#64748b">
                            Belum ada aktivitas siswa di kelasmu. Aktivitas akan muncul setelah siswa mengumpulkan tugas, menyelesaikan kuis, atau membuat diskusi.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

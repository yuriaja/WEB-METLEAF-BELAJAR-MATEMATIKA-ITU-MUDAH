@extends('layouts.app')

@section('title', 'Rekap Nilai Siswa | MathLeaf')
@section('header_title', 'Rekap Nilai')

@section('content')
    <style>
        /* ---------- Variabel Warna Tema MathLeaf ---------- */
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-border: #bbf7d0;
            --ml-green-soft: #dcfce7;
            --ml-text-dark: #0f172a;
            --ml-text-muted: #475569;
        }

        /* Hapus styling body { background-color: #f8fafc; } agar mengikuti layout.app */

        /* ---------- Header Tanpa Kotak (Konsisten dgn Tugas & Kuis) ---------- */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
        }

        .header-text {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 800;
            color: var(--ml-green);
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .header-text h1 {
            font-size: clamp(1.44rem, 2.4vw, 1.84rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .2rem;
            line-height: 1.2;
        }

        .header-text p {
            color: var(--ml-text-muted);
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.5;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        /* ---------- Tombol Dasar ---------- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.75rem 1.2rem;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            transition: background .2s, transform .15s, box-shadow .2s;
            border: 0;
            text-decoration: none;
        }

        .btn:active {
            transform: scale(.97);
        }

        .btn-primary {
            background: var(--ml-green);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--ml-green-hover);
            box-shadow: 0 6px 16px rgba(8, 101, 52, 0.25);
        }

        .btn-outline {
            background-color: transparent;
            border: 1.5px solid #b5d9c1;
            color: var(--ml-green);
        }

        .btn-outline:hover {
            background-color: var(--ml-green-light);
        }

        .btn-sm {
            padding: 0.55rem 0.95rem;
            font-size: 0.8rem;
        }

        /* ---------- Filter Terpadu (Konsisten dgn Tugas & Materi) ---------- */
        .ml-filter {
            display: flex;
            flex-wrap: wrap;
            gap: .8rem;
            padding: 1.1rem;
            margin-bottom: 1.5rem;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            align-items: center;
        }

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
            min-width: 220px;
        }

        .mi-select:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        /* ---------- STATS GRID ---------- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 520px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-card {
            position: relative;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            padding: 1.4rem;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            transition: transform .25s, box-shadow .25s, border-color .25s;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(8, 101, 52, .08);
            border-color: #b5d9c1;
        }

        .stat-icon-wrap {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-label {
            padding-right: 3rem;
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 700;
        }

        .stat-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.8;
            pointer-events: none;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: #14301f;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-desc {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        /* ---------- PANEL UMUM ---------- */
        .panel {
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            padding: 1.8rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
        }

        .panel-head {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e8f0ea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .panel-head h2 {
            font-size: 1.15rem;
            margin: 0;
            color: #14301f;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        /* ---------- GRID SISWA ---------- */
        .student-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .student-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            background: #ffffff;
            border: 1.5px solid #dfe9e2;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .student-card:hover {
            border-color: var(--ml-green-border);
            background: var(--ml-green-light);
            transform: translateX(4px);
            box-shadow: 0 4px 10px rgba(8, 101, 52, 0.05);
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .student-avatar {
            width: 42px;
            height: 42px;
            background: #e0f2fe;
            color: #0284c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
        }

        .student-name {
            font-weight: 700;
            color: #14301f;
            font-size: 0.9rem;
        }

        .student-arrow {
            color: #94a3b8;
            transition: color 0.2s, transform 0.2s;
        }

        .student-card:hover .student-arrow {
            color: var(--ml-green);
            transform: translateX(2px);
        }

        /* ---------- TABEL PENGUMPULAN (Konsisten dgn Tabel di Tugas) ---------- */
        .submission-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .submission-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 10px;
            overflow: hidden;
            min-width: 800px;
        }

        .submission-table th,
        .submission-table td {
            padding: 1rem 1.1rem;
            text-align: left;
            border-bottom: 1px solid #e8f0ea;
            font-size: 0.88rem;
            vertical-align: middle;
        }

        .submission-table th {
            background: #fbfdfb;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
        }

        .submission-table tr:last-child td {
            border-bottom: none;
        }

        .submission-table tr:hover td {
            background: #fbfdfb;
        }

        .submission-name {
            font-weight: 700;
            color: #14301f;
        }

        /* ---------- STATUS BADGE ---------- */
        .status-belum-dinilai {
            color: #dc2626;
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            background: #fef2f2;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            border: 1px dashed #f87171;
            display: inline-block;
        }

        .status-sudah-dinilai {
            color: var(--ml-green);
            font-weight: 800;
            font-size: 1.05rem;
        }

        /* ---------- PAGINATION ---------- */
        .pagination-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e8f0ea;
        }

        .pagination-link {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--ml-green);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .pagination-link:hover {
            background: var(--ml-green-light);
        }

        .pagination-info {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
        }

        /* ---------- TOAST NOTIFICATION ---------- */
        .grades-toast {
            position: fixed;
            top: 84px;
            right: 24px;
            z-index: 9998;
            width: min(380px, calc(100vw - 24px));
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            padding: 1rem 1.1rem;
            border: 1px solid #fde047;
            border-left: 5px solid #eab308;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
            animation: gradesToastIn .45s cubic-bezier(.2, .8, .2, 1) both;
        }

        .grades-toast.out {
            animation: gradesToastOut .3s ease both;
        }

        .grades-toast .toast-icon {
            color: #d97706;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .grades-toast b {
            display: block;
            color: #0f172a;
            font-size: 0.9rem;
            font-weight: 800;
        }

        .grades-toast p {
            margin: 4px 0 0;
            color: #475569;
            font-size: 0.85rem;
            line-height: 1.45;
        }

        .grades-toast a {
            color: var(--ml-green);
            font-weight: 700;
            text-decoration: underline;
        }

        .grades-toast .toast-close {
            margin-left: auto;
            padding: 0 0.2rem;
            border: 0;
            background: transparent;
            color: #94a3b8;
            font-size: 1.2rem;
            line-height: 1;
            cursor: pointer;
        }

        .grades-toast .toast-close:hover {
            color: #ef4444;
        }

        @keyframes gradesToastIn {
            from {
                opacity: 0;
                transform: translateY(-14px) scale(.96);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes gradesToastOut {
            to {
                opacity: 0;
                transform: translateY(-10px) scale(.96);
            }
        }

        @media (max-width: 768px) {
            .grades-toast {
                top: 72px;
                right: 12px;
            }
        }
    </style>

    <!-- HEADER Halaman -->
    <div class="page-header">
        <div class="header-text">
            <span class="eyebrow">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Evaluasi Kelas
            </span>
            <h1>Rekap Nilai Siswa</h1>
            <p>Pantau hasil tugas dan evaluasi kuis siswa dari seluruh kelas yang kamu ajar.</p>
        </div>

        <div class="header-actions">
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline">
                Kembali ke Dashboard
            </a>
            <a href="{{ route('teacher.grades.export', $selectedClassId !== null ? ['class_id' => $selectedClassId] : []) }}"
                class="btn btn-outline">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Export Nilai Excel
            </a>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    @if ($selectedClassId === null && $activeClass)
        <div class="grades-toast" id="grades-class-toast" role="status">
            <svg class="toast-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div>
                <b>Rekap menampilkan semua kelas</b>
                <p>
                    <a href="{{ route('teacher.grades', ['class_id' => $activeClass->id]) }}">
                        Klik di sini untuk hanya menampilkan kelas {{ $activeClass->name }}.
                    </a>
                </p>
            </div>
            <button type="button" class="toast-close" aria-label="Tutup notifikasi">&times;</button>
        </div>
        <script>
            (function() {
                var toast = document.getElementById('grades-class-toast');
                if (!toast) return;
                var timeout;

                function dismiss() {
                    if (!toast || toast.classList.contains('out')) return;
                    toast.classList.add('out');
                    window.setTimeout(function() {
                        if (toast) toast.remove();
                    }, 300);
                }
                toast.querySelector('.toast-close').addEventListener('click', function() {
                    window.clearTimeout(timeout);
                    dismiss();
                });
                timeout = window.setTimeout(dismiss, 7000);
            })();
        </script>
    @endif

    <!-- FILTER KELAS -->
    <form class="ml-filter" action="{{ route('teacher.grades') }}" method="GET">
        <div style="font-weight:700; color: #14301f; font-size: 0.9rem;">Filter Berdasarkan Kelas:</div>
        <select id="filter-class" name="class_id" class="mi-select" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected($selectedClassId === (int) $class->id)>
                    {{ $class->name }}
                </option>
            @endforeach
        </select>
    </form>

    <!-- STATISTIK KESELURUHAN -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap">
                <span class="stat-label">Tugas Dikumpulkan</span>
                <span class="stat-icon" style="background: rgba(224, 242, 254, 0.75); color: #0284c7;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $taskCount }}</div>
            <span class="stat-desc">{{ $taskGradedCount }} tugas sudah dinilai</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap">
                <span class="stat-label">Kuis Dikerjakan</span>
                <span class="stat-icon" style="background: rgba(243, 232, 255, 0.75); color: #9333ea;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="8" r="6"></circle>
                        <path d="m8 13-2 8 6-3 6 3-2-8"></path>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $quizCount }}</div>
            <span class="stat-desc">{{ $quizGradedCount }} kuis sudah dinilai</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap">
                <span class="stat-label">Rata-rata Tugas</span>
                <span class="stat-icon" style="background: rgba(237, 248, 240, 0.8); color: var(--ml-green);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <path d="M9 15l2 2 4-4"></path>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $taskAverage !== null ? number_format($taskAverage, 1) : '—' }}</div>
            <span class="stat-desc">Dari seluruh tugas dinilai</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap">
                <span class="stat-label">Rata-rata Kuis</span>
                <span class="stat-icon" style="background: rgba(254, 249, 195, 0.8); color: #ca8a04;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                        </polygon>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $quizAverage !== null ? number_format($quizAverage, 1) : '—' }}</div>
            <span class="stat-desc">Dari seluruh kuis dinilai</span>
        </div>
    </div>

    <!-- DAFTAR INDIVIDU SISWA -->
    <section class="panel">
        <div class="panel-head">
            <h2>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Detail Nilai Individu Siswa
            </h2>
        </div>

        <div class="student-grid">
            @forelse($students as $student)
                <a href="{{ route('teacher.grades.student', ['student' => $student->id, 'class_id' => $selectedClassId]) }}"
                    class="student-card">
                    <div class="student-info">
                        <div class="student-avatar">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                        <span class="student-name">{{ $student->name }}</span>
                    </div>
                    <svg class="student-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            @empty
                <div
                    style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #64748b; font-size: 0.95rem; border: 2px dashed #cbd5e1; border-radius: 12px;">
                    Belum ada siswa dalam kelas yang dipilih.
                </div>
            @endforelse
        </div>
    </section>

    <!-- TABEL REKAP TUGAS -->
    <section class="panel">
        <div class="panel-head">
            <h2>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Rekap Pengumpulan Tugas
            </h2>
            <a href="{{ route('classroom.tasks.index') }}" class="btn btn-outline btn-sm">Kelola Tugas</a>
        </div>

        <div class="submission-table-wrap">
            <table class="submission-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Judul Tugas</th>
                        <th>Tanggal Submit</th>
                        <th>Status</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taskSubmissions as $submission)
                        <tr>
                            <td>
                                <div class="submission-name">{{ $submission->user?->name ?? 'Akun tidak tersedia' }}</div>
                            </td>
                            <td>{{ $classes->firstWhere('id', $submission->activity?->class_id)?->name ?? 'Kelas tidak tersedia' }}
                            </td>
                            <td>{{ $submission->activity?->title ?? 'Tugas tidak tersedia' }}</td>
                            <td style="color: #64748b;">{{ $submission->submitted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>
                                @if ($submission->score !== null)
                                    <div class="status-sudah-dinilai" style="font-size:0.85rem;">SUDAH DINILAI</div>
                                @else
                                    <div class="status-belum-dinilai">BELUM DINILAI</div>
                                @endif
                            </td>
                            <td>
                                @if ($submission->score !== null)
                                    <strong style="color: var(--ml-green); font-size: 1.05rem;">
                                        {{ number_format($submission->score, 1) }}
                                    </strong>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 2.5rem;">Belum ada
                                pengumpulan tugas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            @if ($taskSubmissions->previousPageUrl())
                <a href="{{ $taskSubmissions->previousPageUrl() }}" class="pagination-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg> Sebelumnya
                </a>
            @else
                <div></div>
            @endif

            <span class="pagination-info">Halaman {{ $taskSubmissions->currentPage() }} dari
                {{ $taskSubmissions->lastPage() }}</span>

            @if ($taskSubmissions->nextPageUrl())
                <a href="{{ $taskSubmissions->nextPageUrl() }}" class="pagination-link">
                    Berikutnya <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            @else
                <div></div>
            @endif
        </div>
    </section>

    <!-- TABEL REKAP KUIS -->
    <section class="panel">
        <div class="panel-head">
            <h2>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="6"></circle>
                    <path d="m8 13-2 8 6-3 6 3-2-8"></path>
                </svg>
                Rekap Pengerjaan Kuis
            </h2>
        </div>

        <div class="submission-table-wrap">
            <table class="submission-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Judul Kuis</th>
                        <th>Tanggal Submit</th>
                        <th>Status</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizAttempts as $attempt)
                        <tr>
                            <td>
                                <div class="submission-name">{{ $attempt->user?->name ?? 'Akun tidak tersedia' }}</div>
                            </td>
                            <td>{{ $classes->firstWhere('id', $attempt->assessment?->class_id)?->name ?? 'Kelas tidak tersedia' }}
                            </td>
                            <td>{{ $attempt->assessment?->title ?? 'Kuis tidak tersedia' }}</td>
                            <td style="color: #64748b;">{{ $attempt->submitted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>
                                @if ($attempt->score !== null)
                                    <div class="status-sudah-dinilai" style="font-size:0.85rem;">SUDAH DINILAI</div>
                                @else
                                    <div class="status-belum-dinilai">BELUM DINILAI</div>
                                @endif
                            </td>
                            <td>
                                @if ($attempt->score !== null)
                                    <strong style="color: var(--ml-green); font-size: 1.05rem;">
                                        {{ number_format($attempt->score, 1) }}
                                    </strong>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 2.5rem;">Belum ada
                                hasil pengerjaan kuis.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            @if ($quizAttempts->previousPageUrl())
                <a href="{{ $quizAttempts->previousPageUrl() }}" class="pagination-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg> Sebelumnya
                </a>
            @else
                <div></div>
            @endif

            <span class="pagination-info">Halaman {{ $quizAttempts->currentPage() }} dari
                {{ $quizAttempts->lastPage() }}</span>

            @if ($quizAttempts->nextPageUrl())
                <a href="{{ $quizAttempts->nextPageUrl() }}" class="pagination-link">
                    Berikutnya <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            @else
                <div></div>
            @endif
        </div>
    </section>
@endsection

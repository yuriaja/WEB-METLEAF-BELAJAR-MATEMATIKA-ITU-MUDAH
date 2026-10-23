@extends('layouts.app')

@section('title', 'Rekap Nilai Siswa | MathLeaf')
@section('header_title', 'Rekap Nilai')

@section('content')
    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-border: #bbf0cb;
        }

        body {
            background-color: #f8fafc;
            font-size: 16px;
        }

        /* PAGE HEADER */
        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-heading h1 {
            font-size: 2.2rem;
            color: #0f172a;
            margin: 0.5rem 0;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .page-heading p {
            color: #475569;
            font-size: 1.1rem;
            margin: 0;
            line-height: 1.6;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: var(--ml-green);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: var(--ml-green-hover);
            box-shadow: 0 4px 12px rgba(8, 101, 52, 0.2);
        }

        .btn-outline-green {
            background: var(--ml-green-light);
            color: var(--ml-green);
            border-color: var(--ml-green-border);
        }

        .btn-outline-green:hover {
            background: #dcfce7;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.03);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(15, 23, 42, 0.05);
        }

        .stat-icon-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-label {
            font-size: 0.95rem;
            color: #64748b;
            font-weight: 600;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-desc {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* PANEL UMUM */
        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.03);
        }

        .panel-head {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .panel-head h2 {
            font-size: 1.25rem;
            margin: 0;
            color: #0f172a;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* FILTER DROPDOWN */
        .filter-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .form-select {
            padding: 0.6rem 1rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            font-size: 0.95rem;
            color: #334155;
            font-weight: 500;
            outline: none;
            min-width: 200px;
            cursor: pointer;
        }

        .form-select:focus {
            border-color: var(--ml-green);
        }

        /* GRID SISWA */
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
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .student-card:hover {
            border-color: var(--ml-green);
            background: var(--ml-green-light);
            transform: translateX(4px);
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            background: #e0f2fe;
            color: #0284c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .student-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 1rem;
        }

        .student-arrow {
            color: #94a3b8;
            transition: color 0.2s;
        }

        .student-card:hover .student-arrow {
            color: var(--ml-green);
        }

        /* CUSTOM TABLE */
        .custom-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .custom-table th {
            background: #f8fafc;
            padding: 1rem 1.25rem;
            text-align: left;
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .custom-table td {
            padding: 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .custom-table tr:hover td {
            background: #f8fafc;
        }

        .custom-table td strong {
            color: #0f172a;
        }

        .badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .badge.green {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge.yellow {
            background: #fef9c3;
            color: #ca8a04;
        }

        /* PAGINATION */
        .pagination-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
        }

        .pagination-link {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--ml-green);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .pagination-link:hover {
            text-decoration: underline;
        }

        .pagination-info {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
        }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow"
                style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #086534; font-size: 0.9rem; letter-spacing: 0.05em; text-transform: uppercase;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                EVALUASI KELAS
            </span>
            <h1>Rekap Nilai Siswa</h1>
            <p>Pantau hasil tugas dan evaluasi kuis siswa dari seluruh kelas yang kamu ajar.</p>
        </div>

        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-green" style="background: transparent;">
                Kembali ke Dashboard
            </a>
            <a href="{{ route('teacher.grades.export', $selectedClassId !== null ? ['class_id' => $selectedClassId] : []) }}"
                class="btn btn-outline-green">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Export Nilai ke Excel
            </a>
        </div>
    </div>

    <!-- STATISTIK KESELURUHAN -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap">
                <span class="stat-label">Tugas Dikumpulkan</span>
                <span class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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
                <span class="stat-icon" style="background: #f3e8ff; color: #9333ea;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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
                <span class="stat-icon" style="background: var(--ml-green-light); color: var(--ml-green);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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
                <span class="stat-icon" style="background: #fef9c3; color: #ca8a04;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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

    <!-- FILTER & DAFTAR SISWA -->
    <section class="panel">
        <div class="panel-head">
            <h2>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Detail Nilai Individu Siswa
            </h2>
            <form method="GET" action="{{ route('teacher.grades') }}" class="filter-form">
                <select name="class_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Kelas --</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected($selectedClassId === (int) $class->id)>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </form>
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
                        stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            @empty
                <div
                    style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #64748b; font-size: 1rem; border: 1px dashed #cbd5e1; border-radius: 8px;">
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
                    stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Rekap Pengumpulan Tugas
            </h2>
            <a href="{{ route('classroom.tasks.index') }}" class="btn btn-outline-green"
                style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                Kelola Tugas
            </a>
        </div>

        <div class="custom-table-wrap">
            <table class="custom-table">
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
                            <td><strong>{{ $submission->user?->name ?? 'Akun tidak tersedia' }}</strong></td>
                            <td>{{ $classes->firstWhere('id', $submission->activity?->class_id)?->name ?? 'Kelas tidak tersedia' }}
                            </td>
                            <td>{{ $submission->activity?->title ?? 'Tugas tidak tersedia' }}</td>
                            <td style="color: #64748b;">{{ $submission->submitted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>
                                @if ($submission->score !== null)
                                    <span class="badge green"><svg width="12" height="12" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg> Sudah Dinilai</span>
                                @else
                                    <span class="badge yellow"><svg width="12" height="12" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg> Menunggu Nilai</span>
                                @endif
                            </td>
                            <td>
                                @if ($submission->score !== null)
                                    <strong
                                        style="color: var(--ml-green); font-size: 1.1rem;">{{ number_format($submission->score, 1) }}</strong>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 2rem;">Belum ada
                                pengumpulan tugas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            @if ($taskSubmissions->previousPageUrl())
                <a href="{{ $taskSubmissions->previousPageUrl() }}" class="pagination-link"><svg width="16"
                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg> Sebelumnya</a>
            @else
                <div></div>
            @endif
            <span class="pagination-info">Halaman {{ $taskSubmissions->currentPage() }} dari
                {{ $taskSubmissions->lastPage() }}</span>
            @if ($taskSubmissions->nextPageUrl())
                <a href="{{ $taskSubmissions->nextPageUrl() }}" class="pagination-link">Berikutnya <svg width="16"
                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg></a>
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
                    stroke-width="2">
                    <circle cx="12" cy="8" r="6"></circle>
                    <path d="m8 13-2 8 6-3 6 3-2-8"></path>
                </svg>
                Rekap Pengerjaan Kuis
            </h2>
        </div>

        <div class="custom-table-wrap">
            <table class="custom-table">
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
                            <td><strong>{{ $attempt->user?->name ?? 'Akun tidak tersedia' }}</strong></td>
                            <td>{{ $classes->firstWhere('id', $attempt->assessment?->class_id)?->name ?? 'Kelas tidak tersedia' }}
                            </td>
                            <td>{{ $attempt->assessment?->title ?? 'Kuis tidak tersedia' }}</td>
                            <td style="color: #64748b;">{{ $attempt->submitted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>
                                @if ($attempt->score !== null)
                                    <span class="badge green"><svg width="12" height="12" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg> Sudah Dinilai</span>
                                @else
                                    <span class="badge yellow"><svg width="12" height="12" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg> Menunggu Nilai</span>
                                @endif
                            </td>
                            <td>
                                @if ($attempt->score !== null)
                                    <strong
                                        style="color: var(--ml-green); font-size: 1.1rem;">{{ number_format($attempt->score, 1) }}</strong>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 2rem;">Belum ada hasil
                                pengerjaan kuis.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            @if ($quizAttempts->previousPageUrl())
                <a href="{{ $quizAttempts->previousPageUrl() }}" class="pagination-link"><svg width="16"
                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg> Sebelumnya</a>
            @else
                <div></div>
            @endif
            <span class="pagination-info">Halaman {{ $quizAttempts->currentPage() }} dari
                {{ $quizAttempts->lastPage() }}</span>
            @if ($quizAttempts->nextPageUrl())
                <a href="{{ $quizAttempts->nextPageUrl() }}" class="pagination-link">Berikutnya <svg width="16"
                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg></a>
            @else
                <div></div>
            @endif
        </div>
    </section>
@endsection

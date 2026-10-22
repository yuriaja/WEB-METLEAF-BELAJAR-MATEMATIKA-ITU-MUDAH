
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Rekap Nilai Guru | MathLeaf</title>

    <link rel="stylesheet"
          href="{{ asset('css/mathleaf.css') }}">

    <style>
        body {
            margin: 0;
            background: #f4f8f3;
            color: #263d30;
            font-family: Arial, sans-serif;
        }

        .grades-wrapper {
            max-width: 1250px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        .grades-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 28px;
        }

        .grades-header h1 {
            font-size: 28px;
            margin: 0 0 8px;
            color: #216c43;
        }

        .grades-header p {
            color: #718075;
            margin: 0;
        }

        .grades-btn {
            display: inline-block;
            padding: 11px 18px;
            background: #26784d;
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }

        .grades-btn:hover {
            background: #185b37;
        }

        .grades-card {
            background: white;
            padding: 24px;
            border: 1px solid #e1eadf;
            border-radius: 18px;
            box-shadow: 0 5px 20px rgba(0,0,0,.03);
            margin-bottom: 22px;
        }

        .grades-card h2 {
            color: #216c43;
            font-size: 19px;
            margin: 0 0 18px;
        }

        .grades-filter {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .grades-filter select {
            padding: 11px 14px;
            min-width: 220px;
            border: 1px solid #cfddcf;
            border-radius: 10px;
            background: white;
        }

        .grades-stats {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(190px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .grades-stat {
            background: white;
            border: 1px solid #e0eadf;
            border-radius: 16px;
            padding: 22px;
        }

        .grades-stat strong {
            display: block;
            font-size: 30px;
            color: #216c43;
            margin: 10px 0;
        }

        .grades-stat small {
            color: #738477;
        }

        .grades-table-wrap {
            overflow-x: auto;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .grades-table th {
            background: #edf6ed;
            color: #27603e;
            text-align: left;
            padding: 14px;
            white-space: nowrap;
        }

        .grades-table td {
            padding: 14px;
            border-bottom: 1px solid #edf0ec;
            vertical-align: top;
        }

        .grades-table tr:hover td {
            background: #fafcf9;
        }

        .grades-badge {
            display: inline-block;
            padding: 6px 10px;
            background: #e5f5e8;
            color: #237c43;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .grades-badge.pending {
            background: #fff1d6;
            color: #93600e;
        }

        .grades-empty {
            padding: 24px;
            color: #718075;
            text-align: center;
        }

        .grades-pages {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            align-items: center;
            margin-top: 18px;
            font-size: 13px;
        }

        .grades-pages a {
            color: #216c43;
            font-weight: 600;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .grades-wrapper {
                padding: 20px 14px;
            }

            .grades-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<main class="grades-wrapper">

    <!-- HEADER -->
    <div class="grades-header">

        <div>
            <h1>📊 Rekap Nilai Siswa</h1>
            <p>
                Pantau hasil tugas dan kuis siswa
                dari kelas yang kamu ajar.
            </p>
        </div>

        <a href="{{ route('teacher.dashboard') }}"
           class="grades-btn">
            ← Dashboard Guru
        </a>

    </div>

    <!-- FILTER KELAS -->
    <section class="grades-card">

        <h2>Filter Berdasarkan Kelas</h2>

        <form method="GET"
              action="{{ route('teacher.grades') }}"
              class="grades-filter">

            <select name="class_id">
                <option value="">Semua Kelas</option>

                @foreach($classes as $class)
                    <option value="{{ $class->id }}"
                        @selected(
                            $selectedClassId === (int) $class->id
                        )>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="grades-btn">
                Tampilkan Nilai
            </button>

            
            <a
                href="{{ route(
                    'teacher.grades.export',
                    $selectedClassId !== null
                        ? ['class_id' => $selectedClassId]
                        : []
                ) }}"
                class="grades-btn"
                style="background:#e8f5eb;color:#216c43;"
            >
                📥 Export Nilai ke Excel
            </a>


        </form>

    </section>

    
        <!-- DAFTAR SISWA -->
        <section class="grades-card">

            <h2>👨‍🎓 Detail Nilai Per Siswa</h2>

            <p style="color:#718075;margin-bottom:20px;">
                Pilih siswa untuk melihat riwayat nilai tugas dan kuis.
            </p>

            <div style="
                display:grid;
                grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
                gap:12px;
            ">

                @forelse($students as $student)

                    <a
                        href="{{ route('teacher.grades.student', [
                            'student' => $student->id,
                            'class_id' => $selectedClassId
                        ]) }}"
                        style="
                            display:flex;
                            align-items:center;
                            justify-content:space-between;
                            gap:12px;
                            padding:16px;
                            border:1px solid #dfebdf;
                            border-radius:12px;
                            color:#216c43;
                            text-decoration:none;
                            background:#f7fbf7;
                        "
                    >
                        <span>
                            👤 {{ $student->name }}
                        </span>

                        <span>→</span>
                    </a>

                @empty

                    <p style="color:#718075;">
                        Belum ada siswa dalam kelas yang dipilih.
                    </p>

                @endforelse

            </div>

        </section>


    <!-- STATISTIK -->
    <div class="grades-stats">

        <div class="grades-stat">
            <small>📤 Tugas Dikumpulkan</small>
            <strong>{{ $taskCount }}</strong>
            <small>
                {{ $taskGradedCount }} sudah dinilai
            </small>
        </div>

        <div class="grades-stat">
            <small>📝 Kuis Dikerjakan</small>
            <strong>{{ $quizCount }}</strong>
            <small>
                {{ $quizGradedCount }} sudah dinilai
            </small>
        </div>

        <div class="grades-stat">
            <small>🏆 Rata-rata Nilai Tugas</small>
            <strong>
                {{ $taskAverage !== null
                    ? number_format($taskAverage, 1)
                    : '—' }}
            </strong>
            <small>Dari tugas yang sudah dinilai</small>
        </div>

        <div class="grades-stat">
            <small>⭐ Rata-rata Nilai Kuis</small>
            <strong>
                {{ $quizAverage !== null
                    ? number_format($quizAverage, 1)
                    : '—' }}
            </strong>
            <small>Dari kuis yang sudah dinilai</small>
        </div>

    </div>

    <!-- REKAP TUGAS -->
    <section class="grades-card">

        <h2>📚 Rekap Nilai Tugas</h2>

        <div class="grades-table-wrap">
            <table class="grades-table">

                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Judul Tugas</th>
                        <th>Nilai</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($taskSubmissions as $submission)
                    <tr>
                        <td>
                            {{ $submission->user?->name ?? 'Akun tidak tersedia' }}
                        </td>

                        <td>
                            {{ $classes->firstWhere(
                                'id',
                                $submission->activity?->class_id
                            )?->name ?? 'Kelas tidak tersedia' }}
                        </td>

                        <td>
                            {{ $submission->activity?->title ?? 'Tugas tidak tersedia' }}
                        </td>

                        <td>
                            {{ $submission->score !== null
                                ? number_format($submission->score, 1)
                                : '—' }}
                        </td>

                        <td>
                            @if($submission->score !== null)
                                <span class="grades-badge">
                                    Sudah Dinilai
                                </span>
                            @else
                                <span class="grades-badge pending">
                                    Menunggu Nilai
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $submission->submitted_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="grades-empty">
                            Belum ada pengumpulan tugas.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

        <div class="grades-pages">
            @if($taskSubmissions->previousPageUrl())
                <a href="{{ $taskSubmissions->previousPageUrl() }}">
                    ← Sebelumnya
                </a>
            @else
                <span></span>
            @endif

            <span>
                Halaman {{ $taskSubmissions->currentPage() }}
                dari {{ $taskSubmissions->lastPage() }}
            </span>

            @if($taskSubmissions->nextPageUrl())
                <a href="{{ $taskSubmissions->nextPageUrl() }}">
                    Berikutnya →
                </a>
            @endif
        </div>

        <a href="{{ route('class-tasks.index') }}"
           class="grades-btn"
           style="margin-top:18px">
            Kelola & Beri Nilai Tugas
        </a>

    </section>

    <!-- REKAP KUIS -->
    <section class="grades-card">

        <h2>📝 Rekap Nilai Kuis</h2>

        <div class="grades-table-wrap">
            <table class="grades-table">

                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Judul Kuis</th>
                        <th>Nilai</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($quizAttempts as $attempt)
                    <tr>
                        <td>
                            {{ $attempt->user?->name ?? 'Akun tidak tersedia' }}
                        </td>

                        <td>
                            {{ $classes->firstWhere(
                                'id',
                                $attempt->assessment?->class_id
                            )?->name ?? 'Kelas tidak tersedia' }}
                        </td>

                        <td>
                            {{ $attempt->assessment?->title ?? 'Kuis tidak tersedia' }}
                        </td>

                        <td>
                            {{ $attempt->score !== null
                                ? number_format($attempt->score, 1)
                                : '—' }}
                        </td>

                        <td>
                            @if($attempt->score !== null)
                                <span class="grades-badge">
                                    Sudah Dinilai
                                </span>
                            @else
                                <span class="grades-badge pending">
                                    Menunggu Nilai
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $attempt->submitted_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="grades-empty">
                            Belum ada hasil pengerjaan kuis.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

        <div class="grades-pages">
            @if($quizAttempts->previousPageUrl())
                <a href="{{ $quizAttempts->previousPageUrl() }}">
                    ← Sebelumnya
                </a>
            @else
                <span></span>
            @endif

            <span>
                Halaman {{ $quizAttempts->currentPage() }}
                dari {{ $quizAttempts->lastPage() }}
            </span>

            @if($quizAttempts->nextPageUrl())
                <a href="{{ $quizAttempts->nextPageUrl() }}">
                    Berikutnya →
                </a>
            @endif
        </div>

    </section>

</main>

</body>
</html>

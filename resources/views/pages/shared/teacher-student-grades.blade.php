
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Detail Nilai Siswa | MathLeaf</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f8f3;
            color: #263d30;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 35px 24px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0 0 8px;
            color: #206c43;
            font-size: 28px;
        }

        h2 {
            color: #206c43;
            font-size: 19px;
            margin: 0 0 20px;
        }

        p, .muted {
            color: #718075;
        }

        .btn {
            display: inline-block;
            border: 0;
            padding: 12px 18px;
            background: #287c50;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }

        .card {
            background: white;
            padding: 25px;
            border: 1px solid #e1eadf;
            border-radius: 18px;
            margin-bottom: 22px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .avatar {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e3f4e8;
            color: #247346;
            border-radius: 50%;
            font-size: 28px;
            font-weight: 700;
        }

        .profile h2 {
            margin: 0 0 6px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat {
            padding: 22px;
            border: 1px solid #e1eadf;
            background: white;
            border-radius: 16px;
        }

        .stat small {
            color: #738477;
        }

        .stat strong {
            display: block;
            color: #216c43;
            font-size: 30px;
            margin: 12px 0;
        }

        select {
            padding: 11px 14px;
            border: 1px solid #d1dfd2;
            border-radius: 10px;
            min-width: 200px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            padding: 14px;
            background: #edf6ed;
            color: #24663d;
            text-align: left;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #edf0ec;
        }

        .badge {
            display: inline-block;
            padding: 7px 10px;
            background: #e4f5e8;
            border-radius: 20px;
            color: #237c43;
            font-size: 12px;
            font-weight: 600;
        }

        .pending {
            background: #fff1d6;
            color: #94620f;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #748377;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            font-size: 13px;
        }

        .pagination a {
            text-decoration: none;
            color: #216c43;
            font-weight: bold;
        }

        @media (max-width: 850px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px 14px;
            }

            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<main class="container">

    <div class="header">
        <div>
            <h1>Detail Nilai Siswa</h1>
            <p>
                Lihat riwayat tugas dan kuis siswa.
            </p>
        </div>

        <a
            href="{{ route('teacher.grades',
                $selectedClassId !== null
                    ? ['class_id' => $selectedClassId]
                    : []
            ) }}"
            class="btn"
        >
            ← Kembali ke Rekap Nilai
        </a>
    </div>

    <!-- PROFIL SISWA -->
    <section class="card">
        <div class="profile">

            <div class="avatar">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>

            <div>
                <h2>{{ $student->name }}</h2>

                <div class="muted">
                    NIS:
                    {{ $student->student_number ?? 'Belum tersedia' }}
                </div>

                <div class="muted" style="margin-top:6px">
                    Kelas:
                    {{ $classes->pluck('name')->join(', ') }}
                </div>
            </div>

        </div>
    </section>

    <!-- FILTER -->
    <section class="card">

        <h2>Filter Kelas</h2>

        <form method="GET"
              action="{{ route('teacher.grades.student', $student->id) }}"
              style="display:flex;gap:12px;flex-wrap:wrap">

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

            <button type="submit" class="btn">
                Tampilkan
            </button>

        </form>

    </section>

    <!-- STATISTIK NILAI -->
    <div class="stats">

        <div class="stat">
            <small>Rata-rata Tugas</small>

            <strong>
                {{ $taskAverage !== null
                    ? number_format($taskAverage, 1)
                    : '—' }}
            </strong>

            <small>Hanya tugas yang sudah dinilai</small>
        </div>

        <div class="stat">
            <small>Rata-rata Kuis</small>

            <strong>
                {{ $quizAverage !== null
                    ? number_format($quizAverage, 1)
                    : '—' }}
            </strong>

            <small>Hanya kuis yang sudah dinilai</small>
        </div>

        <div class="stat">
            <small>Tugas Dikumpulkan</small>

            <strong>{{ $taskCount }}</strong>

            <small>{{ $taskGradedCount }} sudah dinilai</small>
        </div>

        <div class="stat">
            <small>Kuis Dikerjakan</small>

            <strong>{{ $quizCount }}</strong>

            <small>{{ $quizGradedCount }} sudah dinilai</small>
        </div>

    </div>

    <!-- RIWAYAT TUGAS -->
    <section class="card">

        <h2>📚 Riwayat Nilai Tugas</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul Tugas</th>
                        <th>Kelas</th>
                        <th>Nilai</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($taskSubmissions as $submission)
                        <tr>
                            <td>
                                {{ $submission->activity?->title ?? 'Tugas tidak tersedia' }}
                            </td>

                            <td>
                                {{ $classes->firstWhere(
                                    'id',
                                    $submission->activity?->class_id
                                )?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $submission->score !== null
                                    ? number_format($submission->score, 1)
                                    : '—' }}
                            </td>

                            <td>
                                @if($submission->score !== null)
                                    <span class="badge">
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <span class="badge pending">
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
                            <td colspan="5" class="empty">
                                Belum ada riwayat tugas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span>
                @if($taskSubmissions->previousPageUrl())
                    <a href="{{ $taskSubmissions->previousPageUrl() }}">
                        ← Sebelumnya
                    </a>
                @endif
            </span>

            <span>
                Halaman {{ $taskSubmissions->currentPage() }}
                dari {{ $taskSubmissions->lastPage() }}
            </span>

            <span>
                @if($taskSubmissions->nextPageUrl())
                    <a href="{{ $taskSubmissions->nextPageUrl() }}">
                        Berikutnya →
                    </a>
                @endif
            </span>
        </div>

    </section>

    <!-- RIWAYAT KUIS -->
    <section class="card">

        <h2>📝 Riwayat Nilai Kuis</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul Kuis</th>
                        <th>Kelas</th>
                        <th>Nilai</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($quizAttempts as $attempt)
                        <tr>
                            <td>
                                {{ $attempt->assessment?->title ?? 'Kuis tidak tersedia' }}
                            </td>

                            <td>
                                {{ $classes->firstWhere(
                                    'id',
                                    $attempt->assessment?->class_id
                                )?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $attempt->score !== null
                                    ? number_format($attempt->score, 1)
                                    : '—' }}
                            </td>

                            <td>
                                @if($attempt->score !== null)
                                    <span class="badge">
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <span class="badge pending">
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
                            <td colspan="5" class="empty">
                                Belum ada riwayat kuis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span>
                @if($quizAttempts->previousPageUrl())
                    <a href="{{ $quizAttempts->previousPageUrl() }}">
                        ← Sebelumnya
                    </a>
                @endif
            </span>

            <span>
                Halaman {{ $quizAttempts->currentPage() }}
                dari {{ $quizAttempts->lastPage() }}
            </span>

            <span>
                @if($quizAttempts->nextPageUrl())
                    <a href="{{ $quizAttempts->nextPageUrl() }}">
                        Berikutnya →
                    </a>
                @endif
            </span>
        </div>

    </section>

</main>

</body>
</html>

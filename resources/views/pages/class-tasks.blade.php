
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Tugas Kelas | MathLeaf</title>

    <link rel="stylesheet"
          href="{{ asset('css/mathleaf.css') }}">

    <style>
        body {
            background: #f5f8f3;
            font-family: Arial, sans-serif;
            color: #254432;
        }

        .task-container {
            max-width: 1100px;
            margin: 35px auto;
            padding: 20px;
        }

        .task-card {
            background: white;
            padding: 25px;
            margin-bottom: 22px;
            border-radius: 15px;
            border: 1px solid #dce8da;
        }

        .task-card h2 {
            color: #17683b;
            margin-bottom: 15px;
        }

        .task-field {
            margin-bottom: 15px;
        }

        .task-field label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .task-field input,
        .task-field select,
        .task-field textarea {
            display: block;
            width: 100%;
            padding: 11px;
            border: 1px solid #c3d4c5;
            border-radius: 8px;
            box-sizing: border-box;
            font: inherit;
        }

        .task-button {
            display: inline-block;
            background: #197244;
            color: white;
            padding: 11px 18px;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }

        .task-item {
            border: 1px solid #dce8da;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .task-alert {
            padding: 14px;
            background: #e4f5e7;
            border-radius: 9px;
            margin-bottom: 20px;
        }

        .task-error {
            background: #ffe7e7;
        }

        .task-submission {
            padding: 15px;
            margin: 12px 0;
            border-radius: 10px;
            background: #f4f8f3;
        }

        .task-muted {
            color: #64786b;
            font-size: 14px;
        }

        .task-back {
            display: inline-block;
            margin-bottom: 20px;
            color: #17683b;
        }
    </style>
</head>

<body>

<main class="task-container">

    <a href="{{ $teacher ? route('teacher.dashboard') : route('classroom.home') }}"
       class="task-back">
        ← Kembali ke Dashboard
    </a>

    <h1>📝 Tugas Kelas MathLeaf</h1>

    <p>
        {{ $teacher
            ? 'Kelola tugas dan nilai siswa.'
            : 'Lihat dan kerjakan tugas dari guru.' }}
    </p>

    @if(session('success'))
        <div class="task-alert">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="task-alert task-error">
            <strong>Terjadi kesalahan:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- FORM MEMBUAT TUGAS UNTUK GURU --}}

    @if($teacher)

    <section class="task-card">

        <h2>+ Buat Tugas Baru</h2>

        @if($classes->isEmpty())

            <p>
                Belum ada kelas yang kamu ajar.
                Buat kelas terlebih dahulu.
            </p>

        @else

        <form method="POST"
              action="{{ route('class-tasks.store') }}">

            @csrf

            <div class="task-field">
                <label>Pilih Kelas</label>

                <select name="class_id" required>

                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="task-field">
                <label>Judul Tugas</label>

                <input type="text"
                       name="title"
                       value="{{ old('title') }}"
                       placeholder="Contoh: Tugas Bilangan Bulat"
                       required>
            </div>

            <div class="task-field">
                <label>Instruksi Tugas</label>

                <textarea name="instructions"
                          rows="4"
                          required>{{ old('instructions') }}</textarea>
            </div>

            <div class="task-field">
                <label>Tahap FPBL</label>

                <select name="fpbl_stage" required>
                    <option value="1">1 - Mengamati</option>
                    <option value="2">2 - Eksplorasi</option>
                    <option value="3">3 - Investigasi</option>
                    <option value="4">4 - Presentasi</option>
                </select>
            </div>

            <div class="task-field">
                <label>Deadline Pengumpulan</label>

                <input type="datetime-local"
                       name="due_at">
            </div>

            <button type="submit"
                    class="task-button">
                Terbitkan Tugas
            </button>

        </form>

        @endif

    </section>

    @endif


    {{-- DAFTAR TUGAS --}}

    <section class="task-card">

        <h2>
            {{ $teacher
                ? 'Daftar Tugas dan Pengumpulan'
                : 'Tugas yang Harus Dikerjakan' }}
        </h2>

        @forelse($tasks as $task)

            @php
                $mySubmission = $teacher
                    ? null
                    : $task->submissions->first();

                $late = $task->due_at
                    && now()->greaterThan($task->due_at);

                $canSubmit = !$teacher
                    && !$late
                    && (
                        !$mySubmission
                        || $mySubmission->score === null
                    );

                $className = optional(
                    $classes->firstWhere('id', $task->class_id)
                )->name ?? 'Kelas';
            @endphp

            <article class="task-item">

                <p class="task-muted">
                    {{ $className }}
                    • Tahap {{ $task->fpbl_stage }}
                </p>

                <h3>{{ $task->title }}</h3>

                <p style="white-space: pre-line;">{{ $task->instructions }}</p>

                <p>
                    <strong>Deadline:</strong>

                    {{ $task->due_at
                        ? $task->due_at->format('d/m/Y H:i')
                        : 'Tidak dibatasi' }}
                </p>


                {{-- TAMPILAN GURU --}}

                @if($teacher)

                    <h4>
                        Pengumpulan Siswa
                        ({{ $task->submissions->count() }})
                    </h4>

                    @forelse($task->submissions as $submission)

                        <div class="task-submission">

                            <strong>
                                {{ $submission->user->name ?? 'Siswa' }}
                            </strong>

                            <p>
                                {{ $submission->content ?? 'Jawaban berupa file' }}
                            </p>

                            @if($submission->file_path)

                                <a href="{{ route('class-tasks.download', $submission) }}">
                                    📎 Download File Siswa
                                </a>

                            @endif

                            <form method="POST"
                                  action="{{ route('class-tasks.grade', $submission) }}">

                                @csrf
                                @method('PATCH')

                                <div class="task-field">
                                    <label>Nilai (0 - 100)</label>

                                    <input type="number"
                                           name="score"
                                           min="0"
                                           max="100"
                                           step="0.01"
                                           value="{{ $submission->score }}"
                                           required>
                                </div>

                                <div class="task-field">
                                    <label>Umpan Balik Guru</label>

                                    <textarea name="teacher_feedback"
                                              rows="3">{{ $submission->teacher_feedback }}</textarea>
                                </div>

                                <button type="submit"
                                        class="task-button">
                                    Simpan Nilai
                                </button>

                            </form>

                        </div>

                    @empty

                        <p class="task-muted">
                            Belum ada siswa yang mengumpulkan tugas.
                        </p>

                    @endforelse


                {{-- TAMPILAN SISWA --}}

                @else

                    @if($mySubmission)

                        <div class="task-alert">

                            <strong>✅ Tugas Sudah Dikumpulkan</strong>

                            @if($mySubmission->score !== null)

                                <p>
                                    Nilai:
                                    <strong>
                                        {{ $mySubmission->score }}/100
                                    </strong>
                                </p>

                                @if($mySubmission->teacher_feedback)

                                    <p>
                                        Feedback Guru:
                                        {{ $mySubmission->teacher_feedback }}
                                    </p>

                                @endif

                            @else

                                <p>
                                    Menunggu penilaian guru.
                                </p>

                            @endif

                            @if($mySubmission->file_path)
                                <a href="{{ route('class-tasks.download', $mySubmission) }}">
                                    Download file saya
                                </a>
                            @endif

                        </div>

                    @endif


                    @if($canSubmit)

                        <form method="POST"
                              action="{{ route('class-tasks.submit', $task) }}"
                              enctype="multipart/form-data">

                            @csrf

                            <div class="task-field">
                                <label>Jawaban Siswa</label>

                                <textarea name="content"
                                          rows="4"
                                          placeholder="Tulis jawaban di sini...">{{ $mySubmission->content ?? '' }}</textarea>
                            </div>

                            <div class="task-field">
                                <label>Upload File Tugas</label>

                                <input type="file"
                                       name="file"
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">

                                <small>
                                    Maksimal 10 MB.
                                </small>
                            </div>

                            <button type="submit"
                                    class="task-button">
                                {{ $mySubmission
                                    ? 'Perbarui Pengumpulan'
                                    : 'Kumpulkan Tugas' }}
                            </button>

                        </form>

                    @elseif(!$mySubmission)

                        <p class="task-muted">
                            Pengumpulan tugas sudah ditutup.
                        </p>

                    @endif

                @endif

            </article>

        @empty

            <p>Belum ada tugas untuk kelas ini.</p>

        @endforelse

    </section>

</main>

</body>
</html>

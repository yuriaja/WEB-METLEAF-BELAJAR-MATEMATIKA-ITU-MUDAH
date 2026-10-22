@extends('layouts.app')

@section('title', ($assessment->title ?? 'Editor Kuis') . ' | MathLeaf')
@section('header_title', $assessment->title ?? 'Editor Kuis')

@section('content')
    @php
        $isTeacher = Auth::check() && Auth::user()->role === 'guru';
    @endphp

    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-border: #bbf0cb;
        }

        /* Header & General */
        .quiz-page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .quiz-title-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .back-link:hover {
            color: #0f172a;
        }

        .quiz-main-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: var(--ml-green);
            color: #ffffff;
            border-color: var(--ml-green);
        }

        .btn-primary:hover {
            background-color: var(--ml-green-hover);
        }

        .btn-outline-dashed {
            background-color: transparent;
            border: 2px dashed #cbd5e1;
            color: #475569;
            width: 100%;
            justify-content: center;
            padding: 1rem;
        }

        .btn-outline-dashed:hover {
            border-color: var(--ml-green);
            color: var(--ml-green);
        }

        .btn-danger-ghost {
            background: transparent;
            color: #ef4444;
            border: none;
            padding: 0.4rem;
            border-radius: 4px;
        }

        .btn-danger-ghost:hover {
            background: #fef2f2;
        }

        /* Kotak Settings & Soal (Shared) */
        .question-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .question-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 8px 8px 0 0;
        }

        .section-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* Pengaturan Kuis Top Panel */
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .radio-group {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-top: 0.4rem;
        }

        .radio-label-inline {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            font-size: 0.95rem;
            color: #0f172a;
        }

        .radio-label-inline input {
            accent-color: var(--ml-green);
            width: 16px;
            height: 16px;
        }

        /* Help Tooltip Icon */
        .help-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
            margin-left: 0.2rem;
        }

        .help-icon:hover {
            background: #cbd5e1;
            color: #0f172a;
        }

        .help-box {
            display: none;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 0.8rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #475569;
            margin-top: 0.75rem;
            line-height: 1.5;
        }

        .help-box.active {
            display: block;
        }

        /* Teacher Editor Styles */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
            color: #0f172a;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--ml-green);
        }

        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .option-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            background: #f8fafc;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .option-letter {
            background: #e2e8f0;
            color: #475569;
            font-weight: 700;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .option-input {
            border: none;
            background: transparent;
            width: 100%;
            font-size: 0.95rem;
            outline: none;
        }

        .settings-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            padding-top: 1rem;
            border-top: 1px dashed #e2e8f0;
        }

        /* Student Quiz Styles */
        .student-question-text {
            font-size: 1.1rem;
            color: #0f172a;
            line-height: 1.6;
            margin: 0 0 1rem 0;
        }

        .student-options {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            background: #ffffff;
        }

        .radio-label:hover {
            border-color: var(--ml-green);
            background: var(--ml-green-light);
        }

        .radio-label input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--ml-green);
        }

        .radio-text {
            font-size: 1rem;
            color: #334155;
        }

        /* Exam Mode Overlay (Student) */
        #exam-start-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #ffffff;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        #quiz-content-wrapper {
            display: none;
        }

        /* Warning Anti-Curang */
        #cheat-warning {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ef4444;
            color: white;
            padding: 1rem;
            text-align: center;
            font-weight: 700;
            z-index: 10000;
        }
    </style>

    <div class="quiz-page-header">
        <div class="quiz-title-group">
            <a href="{{ $isTeacher ? route('teacher.quizzes') : route('classroom.home') }}" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali
            </a>
            <h1 class="quiz-main-title">{{ $assessment->title ?? 'Editor Soal Baru' }}</h1>
        </div>

        @if ($isTeacher)
            <button type="submit" form="quiz-form" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan Perubahan
            </button>
        @endif
    </div>

    @if (!$isTeacher)
        <div id="cheat-warning">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 5px;">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            PERINGATAN: Anda terdeteksi keluar dari layar penuh atau pindah tab. Tindakan dicatat oleh sistem.
        </div>
    @endif

    <!-- ========================================== -->
    <!-- 1. TAMPILAN GURU (EDITOR KUIS)             -->
    <!-- ========================================== -->
    @if ($isTeacher)
        <form id="quiz-form" method="POST"
            action="{{ route('teacher.quizzes.update', $assessment) }}">
            @csrf
            @method('PUT')

            <!-- PENGATURAN AWAL KUIS -->
            <div class="question-card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <div class="section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path
                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                            </path>
                        </svg>
                        Pengaturan Kuis
                    </div>
                </div>
                <div class="card-body">
                    <div class="settings-grid">
                        <div class="form-group">
                            <label>Judul Kuis</label>
                            <input type="text" name="title" class="form-control" value="{{ $assessment->title ?? '' }}"
                                placeholder="Contoh: Ujian Tengah Semester" required>
                        </div>
                        <div class="form-group">
                            <label>Pilih Kelas</label>
                            <select name="class_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($classes ?? [] as $class)
                                    <option value="{{ $class->id }}"
                                        {{ isset($assessment) && $assessment->class_id == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Batas Waktu (Menit)</label>
                            <input type="number" name="time_limit_minutes" class="form-control"
                                value="{{ $assessment->time_limit_minutes ?? 60 }}" placeholder="Contoh: 60" required>
                        </div>
                        <div class="form-group">
                            <label>Deadline Pengumpulan (Opsional)</label>
                            <input type="datetime-local" name="due_at" class="form-control"
                                value="{{ isset($assessment->due_at) ? \Carbon\Carbon::parse($assessment->due_at)->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>

                    <div class="form-group"
                        style="margin-top: 0.5rem; border-top: 1px dashed #e2e8f0; padding-top: 1.25rem;">
                        <label style="display: flex; align-items: center;">
                            Mode Pengerjaan
                            <span class="help-icon" onclick="toggleHelp()">?</span>
                        </label>
                        <div class="radio-group">
                            <label class="radio-label-inline">
                                <input type="radio" name="type" value="ujian"
                                    {{ isset($assessment) && $assessment->type === 'ujian' ? 'checked' : '' }}> Ujian
                            </label>
                            <label class="radio-label-inline">
                                <input type="radio" name="type" value="latihan"
                                    {{ !isset($assessment) || $assessment->type === 'latihan' ? 'checked' : '' }}> Latihan Biasa
                            </label>
                            <label class="radio-label-inline">
                                <input type="radio" name="type" value="kuis"
                                    {{ isset($assessment) && $assessment->type === 'kuis' ? 'checked' : '' }}> Kuis
                            </label>
                        </div>
                        <div id="help-mode" class="help-box">
                            <strong>Perbedaan Mode:</strong><br>
                            - <strong>Ujian:</strong> Siswa diwajibkan menggunakan Layar Penuh (Fullscreen). Jika keluar
                            dari layar penuh atau pindah tab, sistem akan mencatat aktivitas tersebut sebagai indikasi
                            kecurangan.<br>
                            - <strong>Latihan Biasa:</strong> Siswa dapat mengerjakan secara normal tanpa pengawasan layar
                            penuh.
                        </div>
                    </div>
                </div>
            </div>

            <!-- DAFTAR SOAL EDITOR -->
            <div id="questions-container" class="question-list">
                @forelse($assessment->questions ?? [] as $index => $q)
                    <div class="question-card" data-index="{{ $index }}">
                        <div class="card-header">
                            <div class="section-title">Soal #<span class="q-num-text">{{ $index + 1 }}</span></div>
                            <button type="button" class="btn-danger-ghost" onclick="removeQuestion(this)"
                                title="Hapus Soal">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path
                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Teks Pertanyaan</label>
                                <textarea name="questions[{{ $index }}][text]" class="form-control" style="min-height: 80px;" required>{{ $q->question_text }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Pilihan Jawaban</label>
                                <div class="options-grid">
                                    @foreach (['A', 'B', 'C', 'D'] as $optIdx => $letter)
                                        <div class="option-item">
                                            <span class="option-letter">{{ $letter }}</span>
                                            <input type="text"
                                                name="questions[{{ $index }}][options][{{ $optIdx }}]"
                                                class="option-input" value="{{ $q->options[$optIdx] ?? '' }}" required>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="settings-row">
                                <div class="form-group">
                                    <label>Kunci Jawaban Benar</label>
                                    <select name="questions[{{ $index }}][correct_answer]" class="form-control"
                                        required>
                                        @foreach (['A', 'B', 'C', 'D'] as $optIdx => $letter)
                                            <option value="{{ $optIdx }}"
                                                {{ $q->correct_answer == $optIdx ? 'selected' : '' }}>Opsi
                                                {{ $letter }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Poin Soal</label>
                                    <input type="number" name="questions[{{ $index }}][points]"
                                        class="form-control" value="{{ $q->points ?? 10 }}" min="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Fallback Soal Pertama -->
                    <div class="question-card" data-index="0">
                        <div class="card-header">
                            <div class="section-title">Soal #<span class="q-num-text">1</span></div>
                        </div>
                        <div class="card-body">
                            <div class="form-group"><label>Teks Pertanyaan</label>
                                <textarea name="questions[0][text]" class="form-control" style="min-height: 80px;" required></textarea>
                            </div>
                            <div class="options-grid">
                                <div class="option-item"><span class="option-letter">A</span><input type="text"
                                        name="questions[0][options][0]" class="option-input" required></div>
                                <div class="option-item"><span class="option-letter">B</span><input type="text"
                                        name="questions[0][options][1]" class="option-input" required></div>
                                <div class="option-item"><span class="option-letter">C</span><input type="text"
                                        name="questions[0][options][2]" class="option-input" required></div>
                                <div class="option-item"><span class="option-letter">D</span><input type="text"
                                        name="questions[0][options][3]" class="option-input" required></div>
                            </div>
                            <div class="settings-row">
                                <div class="form-group"><label>Kunci Jawaban Benar</label><select
                                        name="questions[0][correct_answer]" class="form-control">
                                        <option value="0">Opsi A</option>
                                        <option value="1">Opsi B</option>
                                        <option value="2">Opsi C</option>
                                        <option value="3">Opsi D</option>
                                    </select></div>
                                <div class="form-group"><label>Poin Soal</label><input type="number"
                                        name="questions[0][points]" class="form-control" value="10"></div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
            <button type="button" class="btn btn-outline-dashed" onclick="addQuestion()">
                + Tambah Soal Baru
            </button>
        </form>

        <script>
            // Toggle Help Text untuk Mode Pengerjaan
            function toggleHelp() {
                const helpBox = document.getElementById('help-mode');
                helpBox.classList.toggle('active');
            }

            let qIndex = {{ isset($assessment->questions) ? $assessment->questions->count() : 1 }};

            function addQuestion() {
                const container = document.getElementById('questions-container');
                const num = container.children.length + 1;
                const html = `
                <div class="question-card" data-index="${qIndex}">
                    <div class="card-header">
                        <div class="section-title">Soal #<span class="q-num-text">${num}</span></div>
                        <button type="button" class="btn-danger-ghost" onclick="removeQuestion(this)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                    </div>
                    <div class="card-body">
                        <div class="form-group"><label>Teks Pertanyaan</label><textarea name="questions[${qIndex}][text]" class="form-control" style="min-height: 80px;" required></textarea></div>
                        <div class="options-grid">
                            <div class="option-item"><span class="option-letter">A</span><input type="text" name="questions[${qIndex}][options][0]" class="option-input" required></div>
                            <div class="option-item"><span class="option-letter">B</span><input type="text" name="questions[${qIndex}][options][1]" class="option-input" required></div>
                            <div class="option-item"><span class="option-letter">C</span><input type="text" name="questions[${qIndex}][options][2]" class="option-input" required></div>
                            <div class="option-item"><span class="option-letter">D</span><input type="text" name="questions[${qIndex}][options][3]" class="option-input" required></div>
                        </div>
                        <div class="settings-row">
                            <div class="form-group"><label>Kunci Jawaban Benar</label><select name="questions[${qIndex}][correct_answer]" class="form-control"><option value="0">Opsi A</option><option value="1">Opsi B</option><option value="2">Opsi C</option><option value="3">Opsi D</option></select></div>
                            <div class="form-group"><label>Poin Soal</label><input type="number" name="questions[${qIndex}][points]" class="form-control" value="10"></div>
                        </div>
                    </div>
                </div>`;
                container.insertAdjacentHTML('beforeend', html);
                qIndex++;
            }

            function removeQuestion(btn) {
                btn.closest('.question-card').remove();
                updateNums();
            }

            function updateNums() {
                document.querySelectorAll('.q-num-text').forEach((el, i) => el.innerText = i + 1);
            }
        </script>

        <!-- ========================================== -->
        <!-- 2. TAMPILAN SISWA (MENGERJAKAN KUIS)       -->
        <!-- ========================================== -->
    @else
        @if (isset($already) && $already >= $assessment->max_attempts)
            <div
                style="background: #fef2f2; border: 1px solid #fca5a5; padding: 2rem; border-radius: 8px; text-align: center;">
                <h2 style="color: #991b1b; margin-top: 0;">Kesempatan Habis</h2>
                <p style="color: #b91c1c;">Anda telah mencapai batas maksimal pengerjaan untuk kuis ini.</p>
                <a href="{{ route('classroom.home') }}" class="btn btn-outline" style="margin-top: 1rem;">Kembali ke
                    Dashboard</a>
            </div>
        @else
            <!-- Jika Kuis diset sebagai Ujian, tampilkan overlay Fullscreen terlebih dahulu -->
            @if (isset($assessment) && $assessment->type === 'ujian')
                <div id="exam-start-overlay">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1.5rem;">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                    <h2 style="font-size: 2rem; color: #0f172a; margin: 0 0 1rem 0;">Siap Memulai Ujian?</h2>
                    <p style="color: #64748b; font-size: 1.1rem; max-width: 500px; margin-bottom: 2rem;">
                        Pastikan Anda berada di tempat yang kondusif. Sistem akan mengunci tampilan ke Mode Layar Penuh.
                        <strong>Membuka tab baru atau keluar dari layar penuh akan dicatat sebagai indikasi
                            kecurangan.</strong>
                    </p>
                    <button class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;" onclick="startExam()">
                        Mulai Ujian Sekarang
                    </button>
                </div>
            @endif

            <!-- Form Pengerjaan Soal (Disembunyikan jika mode Ujian sampai Fullscreen aktif) -->
            <div id="quiz-content-wrapper"
                style="{{ isset($assessment) && $assessment->type === 'ujian' ? 'display: none;' : '' }}">
                <form method="POST" action="{{ route('classroom.quiz.submit', $assessment) }}">
                    @csrf
                    <div class="question-list">
                        @foreach ($assessment->questions as $index => $q)
                            <div class="question-card">
                                <div class="card-header">
                                    <div class="section-title">Pertanyaan {{ $index + 1 }}</div>
                                    <div style="color: #64748b; font-weight: 600;">{{ $q->points ?? 10 }} Poin</div>
                                </div>
                                <div class="card-body">
                                    <p class="student-question-text">{{ $q->question_text }}</p>
                                    <div class="student-options">
                                        @foreach ($q->options ?? [] as $i => $option)
                                            <label class="radio-label">
                                                <input type="radio" name="answers[{{ $q->id }}]"
                                                    value="{{ $i }}" required>
                                                <span class="option-letter"
                                                    style="background: transparent; border: 1px solid #cbd5e1;">{{ ['A', 'B', 'C', 'D'][$i] ?? $i }}</span>
                                                <span class="radio-text">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                            Selesai & Kirim Jawaban
                        </button>
                    </div>
                </form>
            </div>

            @if (isset($assessment) && $assessment->type === 'ujian')
                <!-- SCRIPT ANTI-CURANG HANYA JIKA MODE UJIAN -->
                <script>
                    const overlay = document.getElementById('exam-start-overlay');
                    const quizContent = document.getElementById('quiz-content-wrapper');
                    const cheatWarning = document.getElementById('cheat-warning');
                    let isExamActive = false;
                    let cheatCount = 0;

                    function startExam() {
                        const elem = document.documentElement;
                        if (elem.requestFullscreen) {
                            elem.requestFullscreen();
                        } else if (elem.webkitRequestFullscreen) {
                            elem.webkitRequestFullscreen();
                        } else if (elem.msRequestFullscreen) {
                            elem.msRequestFullscreen();
                        }

                        overlay.style.display = 'none';
                        quizContent.style.display = 'block';
                        isExamActive = true;

                        const appLayout = document.getElementById('app-layout');
                        if (appLayout) appLayout.classList.add('sidebar-collapsed');
                    }

                    function triggerCheatWarning() {
                        if (!isExamActive) return;
                        cheatCount++;
                        cheatWarning.style.display = 'block';
                        console.warn("Kecurangan terdeteksi! Total: " + cheatCount);
                        setTimeout(() => {
                            cheatWarning.style.display = 'none';
                        }, 4000);
                    }

                    document.addEventListener("fullscreenchange", () => {
                        if (!document.fullscreenElement && isExamActive) {
                            triggerCheatWarning();
                        }
                    });
                    document.addEventListener("visibilitychange", () => {
                        if (document.hidden && isExamActive) {
                            triggerCheatWarning();
                        }
                    });
                </script>
            @endif
        @endif
    @endif

@endsection

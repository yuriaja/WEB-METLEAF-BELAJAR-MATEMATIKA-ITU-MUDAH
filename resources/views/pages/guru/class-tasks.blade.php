@extends('layouts.app')

@section('title', 'Kelola Tugas Kelas | MathLeaf')
@section('header_title', 'Kelola Tugas Kelas')

@section('content')
    <style>
        /* Variabel Warna Tema */
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-border: #bbf0cb;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.02em;
        }

        /* Tombol */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--ml-green);
            color: #ffffff;
            border-color: var(--ml-green);
        }

        .btn-primary:hover {
            background-color: var(--ml-green-hover);
            box-shadow: 0 4px 6px rgba(8, 101, 52, 0.2);
        }

        .btn-outline {
            background-color: #ffffff;
            border-color: #cbd5e1;
            color: #334155;
        }

        .btn-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-danger-outline {
            background-color: #ffffff;
            border-color: #fecaca;
            color: #dc2626;
        }

        .btn-danger-outline:hover {
            background-color: #fef2f2;
            border-color: #f87171;
        }

        /* List Tugas */
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .task-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            overflow: hidden;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            background: #ffffff;
        }

        .task-info-left {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .task-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
        }

        .task-badge {
            background: #f1f5f9;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            color: #334155;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .task-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .task-deadline {
            color: #0f172a;
            font-weight: 600;
        }

        .task-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chevron-icon {
            transition: transform 0.2s ease;
        }

        /* Accordion Detail Tugas (Slide Down) */
        .task-detail {
            border-top: 1px solid #f1f5f9;
            background: #f8fafc;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
            padding: 0 1.5rem;
        }

        .task-detail.open {
            max-height: 3000px;
            padding: 1.5rem;
        }

        .instruction-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            color: #334155;
            line-height: 1.5;
        }

        .instruction-box h4 {
            margin: 0 0 0.5rem 0;
            font-size: 0.95rem;
            color: #0f172a;
        }

        /* Tabel Pengumpulan (Khusus Bapak/Ibu Guru) */
        .submission-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .submission-table th,
        .submission-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .submission-table th {
            background: #f1f5f9;
            font-weight: 600;
            color: #334155;
            font-size: 0.95rem;
        }

        .submission-table tr:last-child td {
            border-bottom: none;
        }

        .submission-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 1.05rem;
        }

        .attachment-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--ml-green);
            text-decoration: none;
            font-weight: 600;
            background: var(--ml-green-light);
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            border: 1px solid var(--ml-green-border);
            margin-top: 0.5rem;
        }

        .attachment-link:hover {
            background: #dcfce7;
        }

        /* Teks Belum Dinilai */
        .status-belum-dinilai {
            color: #dc2626;
            font-weight: 800;
            font-size: 1.05rem;
            text-transform: uppercase;
            background: #fef2f2;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            border: 1px dashed #f87171;
            display: inline-block;
        }

        .status-sudah-dinilai {
            color: var(--ml-green);
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Form Penilaian di dalam Tabel */
        .grading-form {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            min-width: 200px;
        }

        .grading-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
        }

        .grading-input:focus {
            border-color: var(--ml-green);
            outline: none;
        }

        .grading-textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 60px;
        }

        .grading-textarea:focus {
            border-color: var(--ml-green);
            outline: none;
        }

        /* Modal Pop-up */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .modal-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(15px);
            transition: transform 0.2s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-backdrop.active .modal-box {
            transform: translateY(0);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: #ffffff;
            z-index: 10;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #0f172a;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0.25rem;
        }

        .modal-close:hover {
            color: #0f172a;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
            color: #0f172a;
        }

        .form-control:focus {
            border-color: var(--ml-green);
            outline: none;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: #f8fafc;
            position: sticky;
            bottom: 0;
        }
    </style>

    <!-- Alert Messages -->
    @if (session('success'))
        <div
            style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div
            style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <strong style="display: block; margin-bottom: 0.5rem;">Terjadi kesalahan:</strong>
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Halaman -->
    <div class="page-header">
        <h1>Daftar Tugas Kelas</h1>
        <button type="button" class="btn btn-primary" onclick="openModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Buat Tugas Baru
        </button>
    </div>

    <!-- Modal Buat Tugas Baru -->
    <div id="modal-tugas" class="modal-backdrop">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Buat Tugas Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal()">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('class-tasks.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Kelas</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}
                                    {{ $class->school_year ? '(' . $class->school_year . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Judul Tugas</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                            placeholder="Contoh: Latihan Aljabar Bab 1" required>
                    </div>

                    <div class="form-group">
                        <label>Instruksi Tugas</label>
                        <textarea name="instructions" class="form-control" rows="4" placeholder="Tuliskan petunjuk pengerjaan di sini..."
                            required>{{ old('instructions') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Tahap FPBL</label>
                        <select name="fpbl_stage" class="form-control" required>
                            <option value="1">1 - Mengamati</option>
                            <option value="2">2 - Eksplorasi</option>
                            <option value="3">3 - Investigasi</option>
                            <option value="4">4 - Presentasi</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Deadline Pengumpulan (Opsional)</label>
                        <input type="datetime-local" name="due_at" class="form-control" value="{{ old('due_at') }}">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Terbitkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Daftar Tugas -->
    <div class="task-list">
        @forelse($tasks as $task)
            @php
                $className = optional($classes->firstWhere('id', $task->class_id))->name ?? 'Kelas Tidak Diketahui';
            @endphp

            <div class="task-card">
                <!-- Header Tugas -->
                <div class="task-header">
                    <div class="task-info-left">
                        <div class="task-meta">
                            <span class="task-badge">{{ $className }}</span>
                            <span class="task-badge">Tahap FPBL: {{ $task->fpbl_stage }}</span>
                        </div>
                        <h2 class="task-title">{{ $task->title }}</h2>
                        <div class="task-meta" style="margin-top: 0.3rem;">
                            <span>Deadline: <span
                                    class="task-deadline">{{ $task->due_at ? \Carbon\Carbon::parse($task->due_at)->format('d/m/Y H:i') : 'Tidak dibatasi' }}</span></span>
                            <span>•</span>
                            <span>Mengumpulkan: <strong>{{ $task->submissions->count() }} Siswa</strong></span>
                        </div>
                    </div>

                    <div class="task-actions">
                        <!-- Tombol Edit & Hapus (dengan alert dummy jika belum ada route nya) -->
                        <button type="button" class="btn btn-outline" onclick="alert('Route Edit Tugas belum dibuat!')"
                            title="Edit Tugas">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button type="button" class="btn btn-danger-outline"
                            onclick="alert('Route Hapus Tugas belum dibuat!')" title="Hapus Tugas">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                            </svg>
                        </button>

                        <!-- Tombol Lihat Detail (Slide down) -->
                        <button type="button" class="btn btn-primary" onclick="toggleTask({{ $task->id }})"
                            style="margin-left: 0.5rem;">
                            Lihat Detail
                            <svg id="icon-{{ $task->id }}" class="chevron-icon" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Detail Tugas (Slide Down) -->
                <div id="detail-{{ $task->id }}" class="task-detail">

                    <!-- Kotak Instruksi -->
                    <div class="instruction-box">
                        <h4>Instruksi Tugas:</h4>
                        <div style="white-space: pre-line;">{{ $task->instructions }}</div>
                    </div>

                    <h4 style="margin: 0 0 1rem 0; color: #0f172a; font-size: 1.1rem;">Daftar Pengumpulan Siswa</h4>

                    @if ($task->submissions->count() > 0)
                        <table class="submission-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Nama Siswa</th>
                                    <th style="width: 40%;">Komentar & Lampiran</th>
                                    <th style="width: 15%;">Status / Nilai</th>
                                    <th style="width: 20%;">Beri Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($task->submissions as $submission)
                                    <tr>
                                        <td>
                                            <div class="submission-name">
                                                {{ $submission->user->name ?? 'Siswa Tidak Dikenal' }}</div>
                                            <div style="color: #64748b; font-size: 0.85rem; margin-top: 0.2rem;">
                                                Dikumpulkan:
                                                {{ $submission->created_at ? $submission->created_at->format('d/m/Y H:i') : '-' }}
                                            </div>
                                        </td>

                                        <td>
                                            <!-- Komentar Siswa -->
                                            @if ($submission->content)
                                                <div style="margin-bottom: 0.5rem; color: #334155;">
                                                    "{{ $submission->content }}"</div>
                                            @else
                                                <div style="margin-bottom: 0.5rem; color: #94a3b8; font-style: italic;">
                                                    Tidak ada komentar dari siswa.</div>
                                            @endif

                                            <!-- Lampiran File -->
                                            @if ($submission->file_path)
                                                <a href="{{ route('class-tasks.download', $submission) }}"
                                                    class="attachment-link">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48">
                                                        </path>
                                                    </svg>
                                                    Unduh Lampiran Siswa
                                                </a>
                                            @endif
                                        </td>

                                        <td>
                                            <!-- Indikator Nilai -->
                                            @if ($submission->score === null)
                                                <div class="status-belum-dinilai">BELUM DINILAI</div>
                                            @else
                                                <div class="status-sudah-dinilai">{{ $submission->score }} / 100</div>
                                            @endif
                                        </td>

                                        <td>
                                            <!-- Form Beri Nilai -->
                                            <form method="POST" action="{{ route('class-tasks.grade', $submission) }}"
                                                class="grading-form">
                                                @csrf
                                                @method('PATCH')

                                                <input type="number" name="score" class="grading-input"
                                                    min="0" max="100" step="0.01"
                                                    value="{{ $submission->score }}" placeholder="Nilai (0-100)"
                                                    required>

                                                <textarea name="teacher_feedback" class="grading-textarea" placeholder="Catatan/Feedback (Opsional)">{{ $submission->teacher_feedback }}</textarea>

                                                <button type="submit" class="btn btn-primary"
                                                    style="width: 100%; justify-content: center; padding: 0.5rem; font-size: 0.9rem;">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z">
                                                        </path>
                                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                                        <polyline points="7 3 7 8 15 8"></polyline>
                                                    </svg>
                                                    Simpan Nilai
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div
                            style="background: #ffffff; border: 1px dashed #cbd5e1; padding: 2rem; text-align: center; border-radius: 6px; color: #64748b;">
                            Belum ada siswa yang mengumpulkan tugas ini.
                        </div>
                    @endif
                    <br>
                </div>
            </div>
        @empty
            <div
                style="background: #ffffff; border: 1px dashed var(--ml-green-border); border-radius: 8px; padding: 3rem; text-align: center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <h3 style="margin: 0 0 0.5rem 0; color: #0f172a; font-size: 1.2rem;">Belum Ada Tugas</h3>
                <p style="margin: 0; color: #64748b; font-size: 1rem;">Silakan buat tugas pertama Anda menggunakan tombol
                    di atas.</p>
            </div>
        @endforelse
    </div>

    <script>
        // Logika Modal
        const modalTugas = document.getElementById('modal-tugas');

        function openModal() {
            modalTugas.classList.add('active');
        }

        function closeModal() {
            modalTugas.classList.remove('active');
        }

        // Tutup saat klik background
        modalTugas.addEventListener('click', function(e) {
            if (e.target === modalTugas) closeModal();
        });

        // Logika Accordion (Slide Down)
        function toggleTask(id) {
            const detailDiv = document.getElementById('detail-' + id);
            const icon = document.getElementById('icon-' + id);

            if (detailDiv.classList.contains('open')) {
                detailDiv.classList.remove('open');
                icon.style.transform = 'rotate(0deg)';
            } else {
                detailDiv.classList.add('open');
                icon.style.transform = 'rotate(180deg)';
            }
        }
    </script>
@endsection

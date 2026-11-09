@extends('layouts.app')

@section('title', 'Manajemen Kuis & Ujian | MathLeaf')
@section('header_title', 'Manajemen Kuis & Ujian')

@section('content')
    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-border: #bbf7d0;
            --ml-green-soft: #dcfce7;
            --ml-text-dark: #0f172a;
            --ml-text-muted: #475569;
        }

        /* ---------- Header Tanpa Kotak ---------- */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
        }

        .header-text h1 {
            font-size: clamp(1.44rem, 2.4vw, 1.84rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .4rem;
            line-height: 1.2;
        }

        .header-text p {
            color: #475569;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.5;
        }

        /* ---------- Notifikasi ---------- */
        .quiz-notice {
            border-radius: 12px;
            padding: .85rem 1.1rem;
            margin-bottom: 1.25rem;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-left: 5px solid var(--ml-green);
            color: #1f4d33;
            font-size: 0.84rem;
            line-height: 1.6;
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
            color: #ffffff;
        }

        .btn-soft {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .btn-soft:hover {
            background: var(--ml-green-soft);
            box-shadow: none;
            color: var(--ml-green);
        }

        .btn-outline {
            background-color: transparent;
            border: 1.5px solid #b5d9c1;
            color: var(--ml-green);
        }

        .btn-outline:hover {
            background-color: var(--ml-green-light);
            box-shadow: none;
        }

        .btn-danger-outline {
            background-color: transparent;
            border: 1.5px solid #fecaca;
            color: #dc2626;
        }

        .btn-danger-outline:hover {
            background-color: #fef2f2;
        }

        .btn-sm {
            padding: 0.55rem 0.95rem;
            font-size: 0.8rem;
        }

        /* ---------- Filter Terpadu (Pencarian & Tab) ---------- */
        .quiz-filter {
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

        .qf-tabs {
            display: flex;
            gap: 0.4rem;
            background: #f1f5f9;
            padding: 0.3rem;
            border-radius: 10px;
            margin-right: auto;
        }

        .qf-tabs a {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
        }

        .qf-tabs a.selected {
            background: #ffffff;
            color: var(--ml-green);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .qf-tabs a:hover:not(.selected) {
            color: #1e293b;
        }

        .mi-input,
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
        }

        .mi-input:focus,
        .mi-select:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        .quiz-filter .mi-select {
            min-width: 160px;
        }

        @media (max-width: 768px) {
            .qf-tabs {
                width: 100%;
                margin-right: 0;
                justify-content: space-between;
            }

            .qf-tabs a {
                flex: 1;
                text-align: center;
            }

            .quiz-filter .mi-select {
                flex: 1;
            }
        }

        /* ---------- Kartu Kuis (Padat & Rapi) ---------- */
        .quiz-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .quiz-card {
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            overflow: hidden;
            transition: transform .25s, box-shadow .25s, border-color .25s;
        }

        .quiz-card:hover {
            box-shadow: 0 12px 24px rgba(8, 101, 52, .08);
            border-color: #b5d9c1;
        }

        .quiz-header {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 1.5rem;
            align-items: center;
            padding: 1.4rem;
        }

        /* Box Kode Kuis */
        .quiz-code-box {
            background-color: var(--ml-green-light);
            border: 1px solid var(--ml-green-border);
            border-radius: 10px;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            min-width: 120px;
            text-align: center;
        }

        .code-label {
            font-size: 0.65rem;
            font-weight: 800;
            color: var(--ml-green);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .code-value-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .code-text {
            font-family: monospace;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--ml-text-dark);
            letter-spacing: 0.1em;
        }

        .btn-copy {
            background: transparent;
            border: none;
            color: var(--ml-green);
            cursor: pointer;
            padding: 0.2rem;
            display: flex;
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
        }

        .btn-copy:hover {
            background: var(--ml-green);
            color: #ffffff;
        }

        /* Meta & Title */
        .quiz-meta-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .quiz-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .badge {
            background: #f1f5f9;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            color: var(--ml-text-muted);
            border: 1px solid #e2e8f0;
            font-weight: 700;
            font-size: 0.72rem;
            text-transform: capitalize;
        }

        .badge-status {
            background: var(--ml-green-soft);
            color: var(--ml-green);
            border-color: #cfe8d6;
        }

        .quiz-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--ml-text-dark);
            margin: 0;
            line-height: 1.3;
        }

        .ml6-status {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Progres Siswa */
        .quiz-progress-mini {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.3rem;
        }

        .qp-track {
            flex: 1;
            max-width: 150px;
            height: 6px;
            background: #e3efe6;
            border-radius: 999px;
            overflow: hidden;
        }

        .qp-track div {
            height: 100%;
            background: linear-gradient(90deg, #22a35a, #086534);
            border-radius: 999px;
        }

        .qp-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--ml-green);
        }

        /* Aksi Kanan (Horizontal) */
        .quiz-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .quiz-actions form {
            margin: 0;
        }

        .chevron-icon {
            transition: transform 0.3s ease;
        }

        /* ---------- Jadwal (Accordion) ---------- */
        .ml6-schedule {
            margin: 0 1.4rem 1rem;
            background: #fbfdfb;
            border: 1px solid #dfe9e2;
            border-radius: 12px;
            overflow: hidden;
        }

        .ml6-schedule summary {
            cursor: pointer;
            padding: 0.85rem 1.1rem;
            color: var(--ml-green);
            font-weight: 700;
            font-size: 0.85rem;
            outline: none;
            user-select: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--ml-green-light);
            transition: background 0.2s;
        }

        .ml6-schedule summary:hover {
            background: var(--ml-green-soft);
        }

        .ml6-schedule summary::before {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23086534' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E");
            transition: transform 0.3s;
        }

        .ml6-schedule[open] summary::before {
            transform: rotate(90deg);
        }

        .ml6-schedule-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1.1rem;
            align-items: flex-end;
            border-top: 1px solid #dfe9e2;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .ml6-schedule-form label {
            font-weight: 700;
            color: #14301f;
            font-size: 0.8rem;
            display: block;
            margin-bottom: 0.4rem;
        }

        /* ---------- Dropdown Detail (Attempts) ---------- */
        .quiz-detail {
            border-top: 1px solid #e8f0ea;
            background: #ffffff;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s ease;
            padding: 0 1.4rem;
        }

        .quiz-detail.open {
            max-height: 4000px;
            padding: 1.4rem;
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .detail-title {
            margin: 0;
            color: #14301f;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .attempt-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 10px;
            overflow: hidden;
        }

        .attempt-table th,
        .attempt-table td {
            padding: 0.85rem 1.1rem;
            text-align: left;
            border-bottom: 1px solid #e8f0ea;
            font-size: 0.88rem;
        }

        .attempt-table th {
            background: #fbfdfb;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
        }

        .attempt-table tr:last-child td {
            border-bottom: none;
        }

        .student-name {
            font-weight: 700;
            color: #14301f;
        }

        .score-text {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--ml-green);
        }

        /* ---------- Modals (Scrollable & Tidak Mentok) ---------- */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
            backdrop-filter: blur(2px);
            padding: 1.5rem;
            /* Memberi jarak agar tidak mentok tepi layar */
        }

        .modal-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background: #ffffff;
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            /* Membatasi tinggi maksimum */
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transform: translateY(20px) scale(0.95);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            /* Agar footer tetap di bawah, konten scroll */
        }

        .modal-backdrop.active .modal-box {
            transform: translateY(0) scale(1);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e8f0ea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            border-radius: 16px 16px 0 0;
            flex-shrink: 0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 800;
            color: #14301f;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.2rem;
            border-radius: 6px;
            display: flex;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: #f1f5f9;
            color: #ef4444;
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            /* Mengizinkan scroll di dalam modal */
        }

        .quiz-confirm-title {
            font-weight: 800;
            color: #14301f;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            overflow-wrap: anywhere;
        }

        .quiz-archive-hint {
            color: #475569;
            line-height: 1.6;
            margin: 0;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 700;
            color: #14301f;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
            color: #14301f;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: var(--ml-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(8, 101, 52, 0.12);
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #e8f0ea;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: #fbfdfb;
            border-radius: 0 0 16px 16px;
            flex-shrink: 0;
        }

        /* ---------- Empty State ---------- */
        .mi-empty {
            grid-column: 1 / -1;
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 3rem 1.5rem;
            text-align: center;
            color: #64748b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Responsiveness */
        @media(max-width: 900px) {
            .quiz-header {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .quiz-actions {
                justify-content: flex-start;
            }
        }

        @media(max-width: 640px) {
            .quiz-actions .btn {
                flex: 1 1 calc(50% - 0.5rem);
            }

            .ml6-schedule-form {
                flex-direction: column;
                align-items: stretch;
            }

            .attempt-table,
            .attempt-table tbody,
            .attempt-table tr,
            .attempt-table td {
                display: block;
                width: 100%;
            }

            .attempt-table thead {
                display: none;
            }

            .attempt-table tr {
                border: 1px solid #dfe9e2;
                margin-bottom: 1rem;
                border-radius: 10px;
                padding: 0.5rem;
            }

            .attempt-table td {
                border-bottom: none;
                padding: 0.5rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .attempt-table td::before {
                content: attr(data-label);
                font-weight: 800;
                color: #64748b;
                font-size: 0.72rem;
                text-transform: uppercase;
            }
        }
    </style>

    <!-- HEADER (Lepas tanpa kotak, seperti materi) -->
    <div class="page-header">
        <div class="header-text">
            <h1>Manajemen Kuis & Ujian</h1>
            <p>Buat kuis, atur jadwal, dan pantau hasil pengerjaan siswa.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Buat Kuis Baru
        </button>
    </div>

    @if (session('success'))
        <div class="quiz-notice" role="status">{{ session('success') }}</div>
    @endif

    <!-- FILTER & TABS (Digabung menyerupai style materi) -->
    <div class="quiz-filter">
        <div class="qf-tabs">
            <a href="{{ route('teacher.quizzes') }}" class="{{ empty($showArchive) ? 'selected' : '' }}">Aktif & Draft</a>
            <a href="{{ route('teacher.quizzes', ['arsip' => 1]) }}"
                class="{{ !empty($showArchive) ? 'selected' : '' }}">Arsip</a>
        </div>

        <!-- Form filter kelas jika diperlukan di backend -->
        <form method="GET" action="{{ route('teacher.quizzes') }}"
            style="display: flex; flex:1; gap:0.8rem; margin:0; flex-wrap:wrap;">
            @if (!empty($showArchive))
                <input type="hidden" name="arsip" value="1">
            @endif

            <select name="class_id" class="mi-select" aria-label="Pilih kelas" onchange="this.form.submit()">
                <option value="">Semua kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Modal Konfirmasi Hapus/Arsip -->
    <div id="modal-hapus-kuis" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="judul-modal-hapus"
        aria-hidden="true">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="judul-modal-hapus">Hapus Kuis</h3>
                <button type="button" class="modal-close" onclick="tutupModalHapusKuis()" aria-label="Tutup">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="quiz-confirm-title" id="nama-kuis-hapus"></div>
                <p class="quiz-archive-hint" id="pesan-kuis-hapus"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="tutupModalHapusKuis()">Batal</button>
                <button type="button" class="btn btn-danger-outline" id="konfirmasi-hapus-kuis"
                    onclick="konfirmasiHapusKuis()">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Modal Pengaturan Awal Kuis (Scrollable) -->
    <div id="modal-kuis" class="modal-backdrop">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Pengaturan Awal Kuis</h3>
                <button type="button" class="modal-close" onclick="closeModal()" aria-label="Tutup">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('teacher.quizzes.create') }}"
                style="display: flex; flex-direction: column; overflow: hidden; height: 100%;">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul Kuis</label>
                        <input type="text" name="title" class="form-control"
                            placeholder="Contoh: Ujian Tengah Semester" required>
                    </div>
                    <div class="form-group">
                        <label>Pilih Kelas</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @forelse ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @empty
                                <option value="" disabled>Belum ada kelas. Buat kelas terlebih dahulu.</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Penilaian</label>
                        <select name="type" class="form-control" required>
                            <option value="latihan">Latihan Biasa</option>
                            <option value="kuis">Kuis Terjadwal</option>
                            <option value="ujian">Ujian (Mode Fullscreen)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Batas Waktu (Menit)</label>
                        <input type="number" name="time_limit_minutes" class="form-control" placeholder="Contoh: 60"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Waktu Mulai (WIB, Opsional)</label>
                        <input type="datetime-local" name="starts_at" class="form-control">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Deadline Kuis (WIB, Opsional)</label>
                        <input type="datetime-local" name="due_at" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Lanjut Buat Soal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DAFTAR KUIS -->
    <div class="quiz-list">
        @forelse($assessments ?? [] as $quiz)
            <div class="quiz-card">

                <!-- Header Kartu -->
                <div class="quiz-header">
                    <!-- Box Kode -->
                    <div class="quiz-code-box">
                        <span class="code-label">Kode Kuis</span>
                        <div class="code-value-group">
                            <span
                                class="code-text">{{ optional($classes->firstWhere('id', $quiz->class_id))->join_code ?? '—' }}</span>
                            <button type="button" class="btn-copy"
                                onclick="copyCode('{{ optional($classes->firstWhere('id', $quiz->class_id))->join_code ?? '' }}', this)"
                                title="Salin Kode">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2">
                                    </rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Info Utama -->
                    <div class="quiz-meta-group">
                        <div class="quiz-badges">
                            <span
                                class="badge">{{ optional($classes->firstWhere('id', $quiz->class_id))->name ?? 'Semua Kelas' }}</span>
                            <span class="badge">{{ $quiz->type }}</span>
                            <span class="badge">{{ $quiz->time_limit_minutes }} Menit</span>
                            <span class="badge">{{ $quiz->questions_count }} Soal</span>
                            <span
                                class="badge badge-status">{{ $quiz->archived_at ? 'Diarsipkan' : ($quiz->is_published ? ($quiz->starts_at && $quiz->starts_at->isFuture() ? 'Terjadwal' : 'Aktif') : 'Draft') }}</span>
                        </div>
                        <h2 class="quiz-title">{{ $quiz->title }}</h2>

                        <div class="ml6-status">
                            @if ($quiz->starts_at)
                                Mulai: {{ $quiz->starts_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                            @endif
                            @if ($quiz->due_at)
                                · Deadline: {{ $quiz->due_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                            @else
                                · Tanpa Deadline
                            @endif
                        </div>

                        <!-- Mini Progress Kelas -->
                        @php
                            $totalSiswa =
                                optional($classes->firstWhere('id', $quiz->class_id))->students()->count() ?? 0;
                            $sudahSelesai = isset($quiz->attempts)
                                ? $quiz->attempts->where('status', 'completed')->count()
                                : 0;
                            $persenSelesai = $totalSiswa > 0 ? round(($sudahSelesai / $totalSiswa) * 100) : 0;
                        @endphp
                        @if (!$quiz->archived_at && $totalSiswa > 0)
                            <div class="quiz-progress-mini"
                                title="{{ $sudahSelesai }} dari {{ $totalSiswa }} siswa telah menyelesaikan kuis ini">
                                <div class="qp-track">
                                    <div style="width:{{ $persenSelesai }}%"></div>
                                </div>
                                <span class="qp-label">{{ $persenSelesai }}% Selesai</span>
                            </div>
                        @endif
                    </div>

                    <!-- Aksi Tombol -->
                    <div class="quiz-actions">
                        @if (!$quiz->archived_at)
                            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-outline btn-sm"
                                title="Edit Soal & Jawaban">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Edit Kuis
                            </a>
                            <button type="button" class="btn btn-danger-outline btn-sm"
                                onclick="bukaModalHapusKuis(this)" data-form-id="form-hapus-kuis-{{ $quiz->id }}"
                                data-title="{{ $quiz->title }}" data-attempts="{{ $quiz->attempts_count }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path
                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                    </path>
                                </svg>
                                {{ $quiz->attempts_count > 0 ? 'Arsipkan' : 'Hapus' }}
                            </button>
                            <form id="form-hapus-kuis-{{ $quiz->id }}" method="POST"
                                action="{{ route('teacher.quizzes.destroy', $quiz) }}" hidden>
                                @csrf
                                @method('DELETE')
                            </form>
                        @else
                            <form method="POST" action="{{ route('teacher.quizzes.restore', $quiz) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline btn-sm">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                        <path d="M3 3v5h5"></path>
                                    </svg>
                                    Pulihkan
                                </button>
                            </form>
                        @endif

                        <button type="button" class="btn btn-primary btn-sm" onclick="toggleQuiz({{ $quiz->id }})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                            Lihat Hasil
                            <svg id="icon-{{ $quiz->id }}" class="chevron-icon" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Form Ubah Jadwal -->
                <details class="ml6-schedule">
                    <summary>{{ $quiz->archived_at ? 'Jadwalkan Ulang & Buka Kuis' : 'Atur / Perpanjang Jadwal Kuis' }}
                    </summary>
                    <form class="ml6-schedule-form" method="POST"
                        action="{{ route('teacher.quizzes.schedule', $quiz) }}">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label>Mulai (WIB, opsional)</label>
                            <input type="datetime-local" name="starts_at" class="mi-input"
                                value="{{ $quiz->starts_at?->timezone('Asia/Jakarta')->format('Y-m-d\TH:i') }}">
                        </div>
                        <div>
                            <label>Deadline Baru (WIB)</label>
                            <input type="datetime-local" name="due_at" class="mi-input" required
                                value="{{ $quiz->due_at && $quiz->due_at->isFuture() ? $quiz->due_at->timezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <button type="submit" class="btn btn-soft">Simpan Jadwal</button>
                    </form>
                </details>

                <!-- Panel Detail Pengerjaan Siswa (Slide Down) -->
                <div id="detail-{{ $quiz->id }}" class="quiz-detail">
                    <div class="detail-header">
                        <h4 class="detail-title">Daftar Pengerjaan Siswa</h4>
                        @if ($quiz->type === 'ujian')
                            <a href="{{ route('teacher.quizzes.incidents', $quiz) }}" class="btn btn-outline btn-sm">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                                Catatan Pengawasan Ujian
                            </a>
                        @endif
                    </div>

                    @if (isset($quiz->attempts) && $quiz->attempts->count() > 0)
                        <table class="attempt-table">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Percobaan Ke-</th>
                                    <th>Waktu Selesai</th>
                                    <th>Nilai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quiz->attempts as $attempt)
                                    <tr>
                                        <td data-label="Nama Siswa">
                                            <div class="student-name">{{ $attempt->user->name ?? 'Siswa' }}</div>
                                        </td>
                                        <td data-label="Percobaan Ke-">Percobaan {{ $attempt->attempt_number }}</td>
                                        <td data-label="Waktu Selesai">
                                            {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d/m/Y H:i') : 'Sedang Mengerjakan' }}
                                        </td>
                                        <td data-label="Nilai">
                                            <span
                                                class="score-text">{{ $attempt->score !== null ? $attempt->score : '-' }}</span>
                                            / 100
                                        </td>
                                        <td data-label="Aksi">
                                            @if ($attempt->status === 'completed' || $attempt->score !== null)
                                                <a href="{{ route('teacher.quizzes.attempt', $attempt) }}"
                                                    class="btn btn-soft btn-sm">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                        </path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8"
                                                            y2="13"></line>
                                                        <line x1="16" y1="17" x2="8"
                                                            y2="17"></line>
                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                    </svg>
                                                    Lembar Jawaban
                                                </a>
                                            @else
                                                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Belum
                                                    Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div
                            style="background: #fbfdfb; border: 2px dashed #cbd5e1; padding: 2.5rem 1.5rem; text-align: center; border-radius: 12px; color: #64748b;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                style="margin-bottom: 0.75rem; opacity: 0.6;">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <line x1="19" y1="8" x2="19" y2="14"></line>
                                <line x1="22" y1="11" x2="16" y2="11"></line>
                            </svg>
                            <p style="margin:0; font-size:0.95rem;">Belum ada siswa yang mengerjakan kuis ini.</p>
                        </div>
                    @endif
                    <br>
                </div>
            </div>
        @empty
            <div class="mi-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    style="margin-bottom: 1rem; opacity:0.8;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="12" y1="18" x2="12" y2="12"></line>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                </svg>
                <strong style="color: #14301f; font-size: 1.15rem; display:block; margin-bottom:.4rem;">Belum Ada
                    Kuis</strong>
                <p style="margin: 0; font-size: 0.95rem;">Buat kuis pertamamu dan bagikan kodenya kepada siswa untuk mulai
                    belajar.</p>
            </div>
        @endforelse
    </div>

    <script>
        let formHapusKuisId = null;
        const modalHapusKuis = document.getElementById('modal-hapus-kuis');

        function bukaModalHapusKuis(button) {
            formHapusKuisId = button.dataset.formId;
            const adaJawaban = Number(button.dataset.attempts) > 0;
            document.getElementById('judul-modal-hapus').textContent = adaJawaban ? 'Arsipkan Kuis?' : 'Hapus Kuis?';
            document.getElementById('nama-kuis-hapus').textContent = button.dataset.title;
            document.getElementById('pesan-kuis-hapus').textContent = adaJawaban ?
                'Kuis ini sudah memiliki riwayat pengerjaan siswa. Kuis akan dipindahkan ke arsip dan tidak bisa dikerjakan lagi oleh siswa. Nilai serta lembar jawaban yang sudah ada akan tetap tersimpan aman.' :
                'Kuis ini belum dikerjakan siswa. Kuis beserta seluruh soal-soalnya akan dihapus secara permanen. Tindakan ini tidak bisa dibatalkan.';
            document.getElementById('konfirmasi-hapus-kuis').textContent = adaJawaban ? 'Arsipkan Kuis' : 'Hapus Permanen';
            modalHapusKuis.classList.add('active');
        }

        function tutupModalHapusKuis() {
            formHapusKuisId = null;
            modalHapusKuis.classList.remove('active');
        }

        function konfirmasiHapusKuis() {
            const form = formHapusKuisId && document.getElementById(formHapusKuisId);
            if (form) form.requestSubmit();
        }

        modalHapusKuis.addEventListener('click', (e) => {
            if (e.target === modalHapusKuis) tutupModalHapusKuis();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                tutupModalHapusKuis();
                closeModal();
            }
        });

        // Salin kode kelas dari kartu kuis.
        function copyCode(code, btn) {
            if (!code || code === '—') return;
            navigator.clipboard.writeText(code).then(() => {
                const oldHTML = btn.innerHTML;
                btn.innerHTML =
                    `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
                btn.style.color = '#ffffff';
                btn.style.background = 'var(--ml-green)';
                setTimeout(() => {
                    btn.innerHTML = oldHTML;
                    btn.style.color = '';
                    btn.style.background = '';
                }, 1500);
            }).catch(() => {
                alert('Gagal menyalin kode. Silakan salin manual.');
            });
        }

        // Modal Kuis Baru
        const modalKuis = document.getElementById('modal-kuis');

        function openModal() {
            modalKuis.classList.add('active');
        }

        function closeModal() {
            modalKuis.classList.remove('active');
        }

        modalKuis.addEventListener('click', (e) => {
            if (e.target === modalKuis) closeModal();
        });

        // Accordion Lihat Hasil Pengerjaan
        function toggleQuiz(id) {
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

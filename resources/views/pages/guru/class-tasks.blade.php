@extends('layouts.app')

@section('title', 'Kelola Tugas Kelas | MathLeaf')
@section('header_title', 'Kelola Tugas Kelas')

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
            color: var(--ml-text-muted);
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.5;
        }

        /* ---------- Notifikasi Alert ---------- */
        .ml-alert {
            border-radius: 12px;
            padding: .85rem 1.1rem;
            margin-bottom: 1.5rem;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-left: 5px solid var(--ml-green);
            color: #1f4d33;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .ml-alert.error {
            background: #fef2f2;
            border-color: #fca5a5;
            border-left-color: #ef4444;
            color: #991b1b;
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

        /* ---------- Filter Terpadu ---------- */
        .task-filter {
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

        /* ---------- Kartu Tugas ---------- */
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .task-card {
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            overflow: hidden;
            transition: transform .25s, box-shadow .25s, border-color .25s;
        }

        .task-card:hover {
            box-shadow: 0 12px 24px rgba(8, 101, 52, .08);
            border-color: #b5d9c1;
        }

        .task-header {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1.5rem;
            align-items: center;
            padding: 1.4rem;
        }

        .task-info-left {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .task-badges {
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

        .task-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--ml-text-dark);
            margin: 0;
            line-height: 1.3;
        }

        .task-meta-text {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Progres Pengumpulan (Mini Bar) */
        .task-progress-mini {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.3rem;
        }

        .tp-track {
            flex: 1;
            max-width: 150px;
            height: 6px;
            background: #e3efe6;
            border-radius: 999px;
            overflow: hidden;
        }

        .tp-track div {
            height: 100%;
            background: linear-gradient(90deg, #22a35a, #086534);
            border-radius: 999px;
            transition: width 1s ease;
        }

        .tp-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--ml-green);
        }

        /* Aksi Kanan (Horizontal) */
        .task-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .task-actions form {
            margin: 0;
        }

        .chevron-icon {
            transition: transform 0.3s ease;
        }

        /* ---------- Dropdown Detail Tugas ---------- */
        .task-detail {
            border-top: 1px solid #e8f0ea;
            background: #ffffff;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s ease;
            padding: 0 1.4rem;
        }

        .task-detail.open {
            max-height: 4000px;
            padding: 1.4rem;
        }

        .instruction-box {
            background: #fbfdfb;
            border: 1px solid #dfe9e2;
            padding: 1rem 1.2rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            color: var(--ml-text-dark);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .instruction-box h4 {
            margin: 0 0 0.5rem 0;
            font-size: 0.95rem;
            color: #14301f;
            font-weight: 800;
        }

        /* ---------- Tabel Pengumpulan ---------- */
        .submission-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 10px;
            overflow: hidden;
        }

        .submission-table th,
        .submission-table td {
            padding: 0.85rem 1.1rem;
            text-align: left;
            border-bottom: 1px solid #e8f0ea;
            font-size: 0.88rem;
            vertical-align: top;
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

        .submission-name {
            font-weight: 700;
            color: #14301f;
        }

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

        .grading-form {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .grading-input,
        .grading-textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #cfe0d4;
            border-radius: 8px;
            font-family: inherit;
            color: #14301f;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .grading-input:focus,
        .grading-textarea:focus {
            border-color: var(--ml-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(8, 101, 52, 0.12);
        }

        .grading-textarea {
            font-size: 0.85rem;
            resize: vertical;
            min-height: 60px;
        }

        /* ---------- Modals (Scrollable & Tidak Mentok Layar) ---------- */
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
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transform: translateY(20px) scale(0.95);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
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

        /* Modal pratinjau lampiran siswa (Terjaga aman) */
        .mlt-teacher-preview-btn {
            cursor: pointer;
            font: inherit;
            font-size: .9rem;
        }

        .mlt-teacher-preview-btn:focus-visible {
            outline: 3px solid #65bd91;
            outline-offset: 3px;
        }

        .mlt-preview-overlay[hidden] {
            display: none !important;
        }

        .mlt-preview-overlay {
            position: fixed;
            inset: 0;
            z-index: 12000;
            background: rgba(15, 36, 26, .66);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .mlt-preview-dialog {
            width: min(940px, 100%);
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .22);
            overflow: hidden;
        }

        .mlt-preview-head {
            padding: 17px 21px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px solid #dfeae2;
        }

        .mlt-preview-head h3 {
            margin: 0 0 5px;
            font-size: 1.14rem;
            color: #123d27;
            font-weight: 800;
        }

        .mlt-preview-subtitle {
            margin: 0;
            color: #637368;
            font-size: .89rem;
            overflow-wrap: anywhere;
        }

        .mlt-preview-x {
            border: 1px solid #d5e6da;
            background: #fff;
            color: #315541;
            border-radius: 9px;
            cursor: pointer;
            font-size: 1.4rem;
            width: 37px;
            height: 37px;
            flex-shrink: 0;
        }

        .mlt-preview-x:hover {
            background: #eef8f1;
        }

        .mlt-preview-content {
            padding: 16px 20px;
            overflow-y: auto;
            min-height: 0;
        }

        .mlt-preview-comment {
            margin-bottom: 14px;
            padding: 12px 14px;
            background: #f6faf7;
            border: 1px solid #e3eee6;
            border-radius: 10px;
        }

        .mlt-preview-label {
            font-size: .83rem;
            font-weight: 700;
            color: #235239;
            margin-bottom: 6px;
        }

        .mlt-preview-answer {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            font-size: .9rem;
            color: #334155;
            line-height: 1.6;
        }

        .mlt-preview-file-name {
            font-size: .88rem;
            margin: 0 0 10px;
            color: #345c46;
            font-weight: 650;
            overflow-wrap: anywhere;
        }

        .mlt-preview-media {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f1f5f2;
            border: 1px solid #dce6df;
            border-radius: 10px;
            overflow: hidden;
            min-height: 180px;
        }

        .mlt-preview-media iframe {
            border: 0;
            width: 100%;
            height: min(62vh, 660px);
            background: #fff;
        }

        .mlt-preview-media img {
            display: block;
            max-width: 100%;
            max-height: 62vh;
            object-fit: contain;
        }

        .mlt-preview-message {
            padding: 26px 18px;
            color: #516657;
            font-size: .94rem;
            text-align: center;
            line-height: 1.55;
        }

        .mlt-preview-foot {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
            padding: 13px 20px;
            border-top: 1px solid #dfeae2;
        }

        .mlt-preview-zoom-tools {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
            margin: 0 0 10px;
        }

        .mlt-preview-zoom-tools[hidden],
        .mlt-preview-pdf-tip[hidden] {
            display: none !important;
        }

        .mlt-zoom-btn {
            min-width: 38px;
            min-height: 36px;
            padding: 6px 10px;
            cursor: pointer;
            border: 1px solid #cce2d2;
            background: #fff;
            color: #075b33;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.15;
        }

        .mlt-zoom-btn:hover {
            background: #eef8f1;
            border-color: #9cccad;
        }

        .mlt-zoom-btn:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .mlt-zoom-level {
            font-variant-numeric: tabular-nums;
            color: #16402a;
            font-size: .89rem;
            font-weight: 700;
            text-align: center;
            min-width: 53px;
        }

        .mlt-zoom-hint {
            font-size: .78rem;
            color: #617469;
        }

        .mlt-preview-pdf-tip {
            margin: 0 0 10px;
            color: #4b6354;
            font-size: .85rem;
            line-height: 1.5;
        }

        .mlt-preview-pdf-tip a {
            color: #086534;
            font-weight: 700;
            text-underline-offset: 2px;
        }

        .mlt-preview-media.mlt-zoomable {
            height: min(62vh, 660px);
            min-height: 260px;
            position: relative;
            overflow: hidden;
            cursor: grab;
            touch-action: none;
            user-select: none;
        }

        .mlt-preview-media.mlt-zoomable.mlt-dragging {
            cursor: grabbing;
        }

        .mlt-preview-media.mlt-zoomable img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transform-origin: center center;
            will-change: transform;
            pointer-events: none;
            user-select: none;
        }

        /* Responsiveness */
        @media(max-width: 900px) {
            .task-header {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .task-actions {
                justify-content: flex-start;
            }
        }

        @media(max-width: 640px) {
            .task-actions .btn {
                flex: 1 1 calc(50% - 0.5rem);
            }

            .submission-table,
            .submission-table tbody,
            .submission-table tr,
            .submission-table td {
                display: block;
                width: 100%;
            }

            .submission-table thead {
                display: none;
            }

            .submission-table tr {
                border: 1px solid #dfe9e2;
                margin-bottom: 1rem;
                border-radius: 10px;
                padding: 0.5rem;
            }

            .submission-table td {
                border-bottom: none;
                padding: 0.5rem;
            }

            .mlt-preview-overlay {
                padding: 8px;
            }

            .mlt-preview-dialog {
                max-height: 96vh;
            }

            .mlt-preview-head,
            .mlt-preview-content,
            .mlt-preview-foot {
                padding: 12px;
            }

            .mlt-preview-media iframe {
                height: 54vh;
            }
        }
    </style>

    <!-- Header Tanpa Kotak -->
    <div class="page-header">
        <div class="header-text">
            <h1>Daftar Tugas Kelas</h1>
            <p>Berikan instruksi penugasan, pantau pengumpulan, dan nilai jawaban siswa.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Buat Tugas Baru
        </button>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="ml-alert" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="ml-alert error">
            <strong style="display: block; margin-bottom: 0.5rem;">Terjadi kesalahan:</strong>
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter Terpadu -->
    <form class="task-filter" action="{{ route('classroom.tasks.index') }}" method="GET">
        <div style="font-weight:700; color: #14301f; font-size: 0.9rem;">Filter Kelas:</div>
        <select id="filter-class-task" name="class_id" class="mi-select" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected($selectedClassId === (int) $class->id)>
                    {{ $class->name }}
                </option>
            @endforeach
        </select>
    </form>

    <!-- Modal Buat Tugas Baru (Scrollable) -->
    <div id="modal-tugas" class="modal-backdrop">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Buat Tugas Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('classroom.tasks.store') }}" enctype="multipart/form-data"
                style="display: flex; flex-direction: column; overflow: hidden; height: 100%;">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Kelas</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}
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
                        <label>Lampiran Guru (Gambar / PDF / Word / PPT / Video, maksimal 20 MB)</label>
                        <input type="file" name="attachment" class="form-control" accept="image/jpeg,image/png,image/webp,.pdf,.doc,.docx,.ppt,.pptx,.mp4,.webm">
                    </div>
                    <div class="form-group">
                        <label>Link YouTube / Referensi Tugas (Opsional)</label>
                        <input type="url" name="resource_url" class="form-control" value="{{ old('resource_url') }}" placeholder="https://youtu.be/...">
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

                    <div class="form-group" style="margin-bottom: 0;">
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
                        <div class="task-badges">
                            <span class="badge">{{ $className }}</span>
                            <span class="badge badge-status">Tahap FPBL: {{ $task->fpbl_stage }}</span>
                            <span class="badge">{{ $task->submissions->count() }} Terkumpul</span>
                        </div>
                        <h2 class="task-title">{{ $task->title }}</h2>
                        <div class="task-meta-text">
                            @if ($task->due_at)
                                Deadline: {{ \Carbon\Carbon::parse($task->due_at)->format('d/m/Y H:i') }}
                            @else
                                Tanpa Batas Waktu (Deadline)
                            @endif
                        </div>

                        <!-- Progress Bar Pengumpulan Siswa -->
                        @php
                            $totalSiswa =
                                optional($classes->firstWhere('id', $task->class_id))->students()->count() ?? 0;
                            $sudahSelesai = $task->submissions->count();
                            $persenSelesai = $totalSiswa > 0 ? round(($sudahSelesai / $totalSiswa) * 100) : 0;
                        @endphp
                        @if ($totalSiswa > 0)
                            <div class="task-progress-mini"
                                title="{{ $sudahSelesai }} dari {{ $totalSiswa }} siswa telah mengumpulkan tugas ini">
                                <div class="tp-track">
                                    <div style="width:{{ $persenSelesai }}%"></div>
                                </div>
                                <span class="tp-label">{{ $persenSelesai }}% Mengumpulkan</span>
                            </div>
                        @endif
                    </div>

                    <!-- Aksi Tombol -->
                    <div class="task-actions">
                        <a href="{{ route('classroom.tasks.edit', $task) }}" class="btn btn-outline btn-sm"
                            title="Edit Tugas">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Edit
                        </a>

                        @if ($task->submissions->isEmpty())
                            <form action="{{ route('classroom.tasks.destroy', $task) }}" method="POST"
                                onsubmit="return confirm('Hapus tugas ini? Tindakan ini tidak bisa dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger-outline btn-sm" title="Hapus Tugas">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path
                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                        </path>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        @endif

                        <button type="button" class="btn btn-primary btn-sm" onclick="toggleTask({{ $task->id }})"
                            style="margin-left: 0.2rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                            Lihat Jawaban
                            <svg id="icon-{{ $task->id }}" class="chevron-icon" width="16" height="16"
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
                        @if ($task->attachment_path)
                            <p><a href="{{ route('classroom.tasks.attachment', $task) }}" target="_blank" rel="noopener" style="color:#086534;font-weight:700">📎 {{ $task->attachment_name ?? 'Lampiran tugas' }}</a></p>
                        @endif
                        @if ($task->resource_url)
                            <p><a href="{{ $task->resource_url }}" target="_blank" rel="noopener noreferrer" style="color:#086534;font-weight:700">🔗 Buka video / tautan tugas</a></p>
                        @endif
                    </div>

                    <h4 style="margin: 0 0 1rem 0; color: #14301f; font-size: 1.05rem; font-weight:800;">Daftar Pengumpulan
                        Siswa</h4>

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
                                    <tr id="submission-{{ $submission->id }}">
                                        <td>
                                            <div class="submission-name">
                                                {{ $submission->user->name ?? 'Siswa Tidak Dikenal' }}</div>
                                            <div style="color: #64748b; font-size: 0.85rem; margin-top: 0.3rem;">
                                                Dikumpulkan:<br>
                                                {{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y H:i') : '-' }}
                                            </div>
                                        </td>

                                        <td>
                                            <!-- Komentar Siswa -->
                                            @if ($submission->content)
                                                <div
                                                    style="margin-bottom: 0.8rem; color: #334155; line-height: 1.5; font-size: 0.95rem;">
                                                    "{{ $submission->content }}"
                                                </div>
                                            @else
                                                <div
                                                    style="margin-bottom: 0.8rem; color: #94a3b8; font-style: italic; font-size: 0.9rem;">
                                                    Tidak ada komentar dari siswa.
                                                </div>
                                            @endif

                                            <!-- Pratinjau jawaban siswa -->
                                            @php
                                                $lampiranExt = $submission->file_path
                                                    ? strtolower(pathinfo($submission->file_path, PATHINFO_EXTENSION))
                                                    : '';
                                                $lampiranJenis = match ($lampiranExt) {
                                                    'pdf' => 'pdf',
                                                    'jpg', 'jpeg', 'png' => 'image',
                                                    default => 'other',
                                                };
                                                $lampiranNama =
                                                    $submission->original_file_name ?:
                                                    'Lampiran tugas' .
                                                        ($lampiranExt ? ' (' . strtoupper($lampiranExt) . ')' : '');
                                            @endphp
                                            <button type="button" class="btn btn-soft btn-sm mlt-teacher-preview-btn"
                                                data-student="{{ $submission->user->name ?? 'Siswa' }}"
                                                data-title="{{ $task->title }}"
                                                data-file-name="{{ $submission->file_path ? $lampiranNama : '' }}"
                                                data-file-kind="{{ $submission->file_path ? $lampiranJenis : 'none' }}"
                                                data-preview-url="{{ $submission->file_path ? route('classroom.tasks.download', $submission) . '?preview=1' : '' }}"
                                                data-download-url="{{ $submission->file_path ? route('classroom.tasks.download', $submission) : '' }}"
                                                data-comment="{{ $submission->content ?? '' }}"
                                                onclick="mltOpenTeacherPreview(this)">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                                Lihat Lampiran
                                            </button>
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
                                            <form method="POST"
                                                action="{{ route('classroom.tasks.grade', $submission) }}"
                                                class="grading-form">
                                                @csrf
                                                @method('PATCH')

                                                <input type="number" name="score" class="grading-input"
                                                    min="0" max="100" step="0.01"
                                                    value="{{ $submission->score }}" placeholder="Nilai (0-100)"
                                                    required>

                                                <textarea name="teacher_feedback" class="grading-textarea" placeholder="Catatan/Feedback (Opsional)">{{ $submission->teacher_feedback }}</textarea>

                                                <button type="submit" class="btn btn-primary btn-sm"
                                                    style="width: 100%; justify-content: center;">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z">
                                                        </path>
                                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                                        <polyline points="7 3 7 8 15 8"></polyline>
                                                    </svg>
                                                    Simpan
                                                </button>
                                            </form>
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
                            <p style="margin:0; font-size:0.95rem;">Belum ada siswa yang mengumpulkan tugas ini.</p>
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
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <strong style="color: #14301f; font-size: 1.15rem; display:block; margin-bottom:.4rem;">Belum Ada
                    Tugas</strong>
                <p style="margin: 0; font-size: 0.95rem;">Buat penugasan pertama untuk siswa di kelas ini dengan klik
                    tombol di atas.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal hanya meminta file saat guru mengklik Lihat Jawaban. (Logika Aman) -->
    <div id="mlt-teacher-preview" class="mlt-preview-overlay" hidden role="dialog" aria-modal="true"
        aria-labelledby="mlt-preview-title" aria-describedby="mlt-preview-subtitle">
        <div class="mlt-preview-dialog" role="document">
            <div class="mlt-preview-head">
                <div>
                    <h3 id="mlt-preview-title">Lihat Jawaban Siswa</h3>
                    <p class="mlt-preview-subtitle" id="mlt-preview-subtitle"></p>
                </div>
                <button type="button" class="mlt-preview-x" id="mlt-preview-close"
                    aria-label="Tutup pratinjau">&times;</button>
            </div>
            <div class="mlt-preview-content">
                <div class="mlt-preview-comment" id="mlt-preview-comment-wrap" hidden>
                    <div class="mlt-preview-label">Jawaban / komentar siswa</div>
                    <div class="mlt-preview-answer" id="mlt-preview-comment"></div>
                </div>
                <div class="mlt-preview-label">Lampiran tugas</div>
                <p class="mlt-preview-file-name" id="mlt-preview-file-name"></p>
                <div class="mlt-preview-zoom-tools" id="mlt-preview-zoom-tools" hidden
                    aria-label="Perbesar dan perkecil gambar">
                    <button type="button" class="mlt-zoom-btn" id="mlt-zoom-out" aria-label="Perkecil gambar"
                        title="Perkecil">−</button>
                    <output class="mlt-zoom-level" id="mlt-zoom-level" aria-live="polite">100%</output>
                    <button type="button" class="mlt-zoom-btn" id="mlt-zoom-in" aria-label="Perbesar gambar"
                        title="Perbesar">+</button>
                    <button type="button" class="mlt-zoom-btn" id="mlt-zoom-reset"
                        title="Kembali ke ukuran semula">Reset</button>
                    <span class="mlt-zoom-hint">Geser gambar setelah diperbesar • scroll untuk zoom • cubit pada layar
                        sentuh</span>
                </div>
                <p class="mlt-preview-pdf-tip" id="mlt-preview-pdf-tip" hidden>
                    PDF dapat diperbesar memakai tombol zoom pada penampil PDF.
                    <a href="#" id="mlt-preview-open-pdf" target="_blank" rel="noopener noreferrer">Buka PDF lebih
                        lebar</a>
                </p>
                <div class="mlt-preview-media" id="mlt-preview-media"></div>
            </div>
            <div class="mlt-preview-foot">
                <a id="mlt-preview-download" class="btn btn-outline" hidden>Unduh jika diperlukan</a>
                <button type="button" class="btn btn-primary" id="mlt-preview-done">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        // Logika Modal Pratinjau Guru (Tidak diubah fungsinya)
        const mltPreviewDialog = document.getElementById('mlt-teacher-preview');
        const mltPreviewMedia = document.getElementById('mlt-preview-media');
        const mltZoomTools = document.getElementById('mlt-preview-zoom-tools');
        const mltPdfTip = document.getElementById('mlt-preview-pdf-tip');
        const mltPdfOpen = document.getElementById('mlt-preview-open-pdf');
        const mltZoomLevel = document.getElementById('mlt-zoom-level');
        const mltZoomIn = document.getElementById('mlt-zoom-in');
        const mltZoomOut = document.getElementById('mlt-zoom-out');
        let mltPreviousFocus = null;
        let mltZoom = 1,
            mltPanX = 0,
            mltPanY = 0;
        const mltPointers = new Map();
        let mltPinchDistance = 0,
            mltPinchZoom = 1,
            mltDragPoint = null;

        function mltSetZoom(value) {
            mltZoom = Math.max(.5, Math.min(5, Math.round(value * 100) / 100));
            if (mltZoom <= 1) {
                mltPanX = 0;
                mltPanY = 0;
            }
            const img = mltPreviewMedia.querySelector('img');
            if (img) img.style.transform = `translate(${mltPanX}px, ${mltPanY}px) scale(${mltZoom})`;
            mltZoomLevel.value = Math.round(mltZoom * 100) + '%';
            mltZoomIn.disabled = mltZoom >= 5;
            mltZoomOut.disabled = mltZoom <= .5;
        }

        function mltResetZoom() {
            mltPanX = 0;
            mltPanY = 0;
            mltPointers.clear();
            mltPinchDistance = 0;
            mltDragPoint = null;
            mltPreviewMedia.classList.remove('mlt-dragging');
            mltSetZoom(1);
        }
        document.getElementById('mlt-zoom-in').addEventListener('click', () => mltSetZoom(mltZoom + .25));
        document.getElementById('mlt-zoom-out').addEventListener('click', () => mltSetZoom(mltZoom - .25));
        document.getElementById('mlt-zoom-reset').addEventListener('click', mltResetZoom);
        mltPreviewMedia.addEventListener('wheel', e => {
            if (!mltPreviewMedia.classList.contains('mlt-zoomable')) return;
            e.preventDefault();
            mltSetZoom(mltZoom + (e.deltaY < 0 ? .2 : -.2));
        }, {
            passive: false
        });
        mltPreviewMedia.addEventListener('dblclick', e => {
            if (mltPreviewMedia.classList.contains('mlt-zoomable')) mltSetZoom(mltZoom >= 2 ? 1 : 2);
        });
        const mltPointerDistance = () => {
            const pts = Array.from(mltPointers.values());
            return pts.length < 2 ? 0 : Math.hypot(pts[0].x - pts[1].x, pts[0].y - pts[1].y);
        };
        mltPreviewMedia.addEventListener('pointerdown', e => {
            if (!mltPreviewMedia.classList.contains('mlt-zoomable')) return;
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            mltPreviewMedia.setPointerCapture(e.pointerId);
            mltPointers.set(e.pointerId, {
                x: e.clientX,
                y: e.clientY
            });
            if (mltPointers.size >= 2) {
                mltPinchDistance = mltPointerDistance();
                mltPinchZoom = mltZoom;
                mltDragPoint = null;
            } else if (mltZoom > 1) {
                mltDragPoint = {
                    x: e.clientX,
                    y: e.clientY
                };
                mltPreviewMedia.classList.add('mlt-dragging');
            }
        });
        mltPreviewMedia.addEventListener('pointermove', e => {
            if (!mltPointers.has(e.pointerId)) return;
            mltPointers.set(e.pointerId, {
                x: e.clientX,
                y: e.clientY
            });
            if (mltPointers.size >= 2 && mltPinchDistance > 0) {
                mltSetZoom(mltPinchZoom * mltPointerDistance() / mltPinchDistance);
                return;
            }
            if (mltDragPoint && mltZoom > 1) {
                mltPanX += e.clientX - mltDragPoint.x;
                mltPanY += e.clientY - mltDragPoint.y;
                mltDragPoint = {
                    x: e.clientX,
                    y: e.clientY
                };
                mltSetZoom(mltZoom);
            }
        });

        function mltEndPointer(e) {
            mltPointers.delete(e.pointerId);
            mltDragPoint = null;
            mltPinchDistance = 0;
            mltPreviewMedia.classList.remove('mlt-dragging');
            if (mltPointers.size === 1) {
                const remaining = Array.from(mltPointers.values())[0];
                mltDragPoint = mltZoom > 1 ? {
                    x: remaining.x,
                    y: remaining.y
                } : null;
            }
        }
        ['pointerup', 'pointercancel', 'lostpointercapture'].forEach(name =>
            mltPreviewMedia.addEventListener(name, mltEndPointer));

        function mltCloseTeacherPreview() {
            mltPreviewDialog.hidden = true;
            mltResetZoom();
            mltZoomTools.hidden = true;
            mltPdfTip.hidden = true;
            mltPreviewMedia.classList.remove('mlt-zoomable');
            mltPreviewMedia.replaceChildren();
            document.body.style.overflow = mltPreviewDialog.dataset.oldOverflow || '';
            if (mltPreviousFocus && mltPreviousFocus.isConnected) mltPreviousFocus.focus();
            mltPreviousFocus = null;
        }

        function mltOpenTeacherPreview(button) {
            mltPreviousFocus = button;
            const data = button.dataset;
            document.getElementById('mlt-preview-subtitle').textContent =
                `${data.student || 'Siswa'} · ${data.title || 'Tugas Kelas'}`;
            const commentWrap = document.getElementById('mlt-preview-comment-wrap');
            commentWrap.hidden = !(data.comment || '').trim();
            document.getElementById('mlt-preview-comment').textContent = data.comment || '';
            document.getElementById('mlt-preview-file-name').textContent = data.fileName || 'Tidak ada lampiran';
            const downloadLink = document.getElementById('mlt-preview-download');
            downloadLink.hidden = !data.downloadUrl;
            if (data.downloadUrl) downloadLink.href = data.downloadUrl;
            else downloadLink.removeAttribute('href');
            mltResetZoom();
            mltZoomTools.hidden = true;
            mltPdfTip.hidden = true;
            mltPreviewMedia.classList.remove('mlt-zoomable');
            mltPreviewMedia.replaceChildren();

            const message = (text) => {
                const el = document.createElement('p');
                el.className = 'mlt-preview-message';
                el.textContent = text;
                mltPreviewMedia.appendChild(el);
            };
            if (data.fileKind === 'pdf' && data.previewUrl) {
                mltPdfTip.hidden = false;
                mltPdfOpen.href = data.previewUrl;
                const frame = document.createElement('iframe');
                frame.title = `Pratinjau PDF ${data.fileName || 'jawaban siswa'}`;
                frame.setAttribute('loading', 'lazy');
                frame.src = data.previewUrl;
                mltPreviewMedia.appendChild(frame);
            } else if (data.fileKind === 'image' && data.previewUrl) {
                mltZoomTools.hidden = false;
                mltPreviewMedia.classList.add('mlt-zoomable');
                const img = document.createElement('img');
                img.alt = `Lampiran ${data.fileName || 'jawaban siswa'}`;
                img.src = data.previewUrl;
                img.addEventListener('error', () => {
                    mltPreviewMedia.replaceChildren();
                    message('Gambar tidak dapat ditampilkan. Gunakan Unduh jika diperlukan.');
                }, {
                    once: true
                });
                mltPreviewMedia.appendChild(img);
            } else if (data.fileKind === 'none') {
                message('Siswa ini tidak melampirkan file. Jawaban teksnya ditampilkan di atas.');
            } else {
                message(
                    'Pratinjau DOC, DOCX, dan ZIP belum didukung langsung oleh browser. File tetap dapat diunduh jika diperlukan.');
            }
            mltPreviewDialog.dataset.oldOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            mltPreviewDialog.hidden = false;
            document.getElementById('mlt-preview-close').focus();
        }

        document.getElementById('mlt-preview-close').addEventListener('click', mltCloseTeacherPreview);
        document.getElementById('mlt-preview-done').addEventListener('click', mltCloseTeacherPreview);
        mltPreviewDialog.addEventListener('click', (e) => {
            if (e.target === mltPreviewDialog) mltCloseTeacherPreview();
        });
        document.addEventListener('keydown', (e) => {
            if (!mltPreviewDialog.hidden && e.key === 'Escape') mltCloseTeacherPreview();
            if (!mltPreviewDialog.hidden && e.key === 'Tab') {
                const els = Array.from(mltPreviewDialog.querySelectorAll('button:not([disabled]),a[href]'))
                    .filter(el => !el.hidden);
                if (!els.length) return;
                const first = els[0],
                    last = els[els.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });

        // Logika Modal Buat Tugas
        const modalTugas = document.getElementById('modal-tugas');

        function openModal() {
            modalTugas.classList.add('active');
        }

        function closeModal() {
            modalTugas.classList.remove('active');
        }

        modalTugas.addEventListener('click', function(e) {
            if (e.target === modalTugas) closeModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
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

        @if (!empty($selectedTaskId) && !empty($selectedSubmissionId))
            toggleTask({{ $selectedTaskId }});
            window.setTimeout(() => {
                document.getElementById('submission-{{ $selectedSubmissionId }}')
                    ?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
            }, 200);
        @endif
    </script>
@endsection

@extends('layouts.app')
@section('title', 'Tugas Kelas | MathLeaf')
@section('header_title', 'Tugas & Kelompok')

@section('content')
    @php
        // Ikon SVG yang dipakai di halaman ini (dipanggil lewat {!! $icon['nama'] !!})
        $svg = fn(string $paths, string $class = 'ic') => '<svg class="' .
            $class .
            '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
' .
            $paths .
            '</svg>';

        $icon = [
            'leaf' => $svg(
                '<path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"></path><path d="M4 21c0-6 5-11 11-14"></path>',
            ),
            'file' => $svg(
                '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path>',
            ),
            'eye' => $svg(
                '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle>',
            ),
            'download' => $svg('<path d="M12 3v12m-4-4 4 4 4-4M4 17v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"></path>'),
            'pencil' => $svg(
                '<path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>',
            ),
            'chevron' => $svg('<polyline points="6 9 12 15 18 9"></polyline>', 'ic chevron'),
            'list' => $svg(
                '<line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line>',
            ),
            'check_circle' => $svg(
                '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>',
            ),
            'award' => $svg('<circle cx="12" cy="8" r="6"></circle><path d="m8 13-2 8 6-3 6 3-2-8"></path>'),
            'clock' => $svg('<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>'),
            'clipboard' => $svg(
                '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>',
            ),
        ];
    @endphp

    <style>
        .app-content {
            font-size: 11.2px;
        }

        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-soft: #dff3e5;
        }

        .ic {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        /* ---------- Header halaman ---------- */
        .page-heading {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--ml-green);
            font-size: 0.64rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .eyebrow .ic {
            width: 14px;
            height: 14px;
        }

        .page-heading h1 {
            font-size: 1.44rem;
            color: #0f172a;
            margin: 0.5rem 0;
            font-weight: 800;
        }

        .page-heading p {
            color: #64748b;
            font-size: 0.8rem;
            margin: 0;
            line-height: 1.6;
        }

        /* ---------- Alert ---------- */
        .alert {
            padding: 0.9rem 1.1rem;
            margin-bottom: 1.25rem;
            background: var(--ml-green-light);
            border: 1px solid #bbf7d0;
            border-left: 4px solid var(--ml-green);
            border-radius: 8px;
            color: #14532d;
            font-size: 0.76rem;
            line-height: 1.6;
        }

        .alert.error {
            background: #fef2f2;
            border-color: #fecaca;
            border-left-color: #dc2626;
            color: #991b1b;
        }

        .alert ul {
            margin: 0.4rem 0 0;
            padding-left: 1.2rem;
        }

        .alert.no-margin {
            margin: 1.5rem 0 0;
        }

        /* ---------- Statistik ringkas ---------- */




        /* ---------- Ringkasan progres ---------- */
        .summary-panel {
            margin-bottom: 1.5rem;
        }

        .summary-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-bottom: 1rem;
        }

        .summary-head h2 {
            margin: 0;
        }

        .progress-track {
            position: relative;
            height: 10px;
            overflow: hidden;
            background: #e2e8f0;
            border-radius: 999px;
        }

        .progress-fill {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            border-radius: 999px;
            transition: width 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .progress-fill.submitted {
            background: #9fdcb8;
        }

        .progress-fill.graded {
            background: var(--ml-green);
        }

        .summary-legend {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1.2rem;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.9rem 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .summary-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            border-radius: 50%;
        }

        .summary-icon.total {
            background: #e2e8f0;
            color: #475569;
        }

        .summary-icon.submitted {
            background: #cdeedb;
            color: #166534;
        }

        .summary-icon.graded {
            background: var(--ml-green);
            color: #ffffff;
        }

        .summary-item strong {
            display: block;
            color: #0f172a;
            font-size: 1.3rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .summary-item span {
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 600;
        }

        /* ---------- Filter ---------- */
        .filter-panel {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1.2rem 1.5rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.02);
        }

        .filter-panel label {
            display: block;
            margin-bottom: 0.4rem;
            color: #0f172a;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .filter-panel select {
            min-width: 170px;
            padding: 0.65rem 0.8rem;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: #0f172a;
            font-family: inherit;
            font-size: 0.76rem;
            outline: none;
        }

        .filter-panel select:focus,
        .answer-input:focus,
        .task-fields input[type=file]:focus {
            border-color: var(--ml-green);
        }

        /* ---------- Tombol ---------- */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--ml-green);
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.76rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: var(--ml-green-hover);
            color: #ffffff;
        }

        .btn-primary.block {
            width: 100%;
            margin-top: 1rem;
        }

        /* ---------- Kerangka tugas (dua kolom seperti halaman Tugas) ---------- */
        .task-list {
            display: grid;
            gap: 1.5rem;
        }

        .two-columns {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
            gap: 1.5rem;
            align-items: stretch;
        }

        .two-columns > aside {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .two-columns > aside .panel {
            display: flex;
            flex-direction: column;
        }

        .two-columns > aside .panel:first-child {
            flex: 1;
        }

        .two-columns>* {
            min-width: 0;
        }

        @media (max-width: 900px) {
            .two-columns {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        .panel {
            padding: 1.5rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.02);
        }


        .panel h2 {
            margin: 0 0 1.2rem;
            color: #0f172a;
            font-size: 1rem;
            overflow-wrap: anywhere;
        }

        .panel h3 {
            margin: 1.5rem 0 0.8rem;
            color: #0f172a;
            font-size: 0.88rem;
        }

        /* ---------- Badge ---------- */
        .badge-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-bottom: 1rem;
        }

        .badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.8rem;
            background: var(--ml-green-light);
            border-radius: 20px;
            color: var(--ml-green);
            font-size: 0.68rem;
            font-weight: 700;
        }

        .badge.yellow {
            background: #fef9c3;
            color: #ca8a04;
        }

        .badge.red {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge.green {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge.gray {
            background: #f1f5f9;
            color: #475569;
        }

        .badge .ic {
            width: 12px;
            height: 12px;
        }

        /* ---------- Isi tugas ---------- */
        .task-instructions {
            color: #334155;
            font-size: 0.8rem;
            line-height: 1.6;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }








        /* ---------- Pengumpulan ---------- */
        .submission-box {
            margin-top: 1.5rem;
            padding: 1.2rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .submission-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.8rem;
        }

        .submission-head h3 {
            margin: 0;
        }

        .submission-time {
            color: #64748b;
            font-size: 0.72rem;
        }

        .attached-file {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.9rem;
            padding: 0.9rem 1rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .attached-icon {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            gap: 2px;
            width: 42px;
            height: 46px;
            background: var(--ml-green-light);
            border-radius: 8px;
            color: var(--ml-green);
        }

        .attached-icon .ic {
            width: 18px;
            height: 18px;
        }

        .attached-icon span {
            font-size: 0.58rem;
            font-weight: 800;
            letter-spacing: 0.025em;
            line-height: 1;
        }

        .attached-info {
            flex: 1;
            min-width: 150px;
        }

        .attached-name {
            color: #0f172a;
            font-size: 0.8rem;
            font-weight: 700;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .attached-subtext {
            margin-top: 0.25rem;
            color: #64748b;
            font-size: 0.7rem;
        }

        .attached-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }

        .attached-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.55rem 0.9rem;
            background: var(--ml-green-light);
            border-radius: 8px;
            color: var(--ml-green);
            font-size: 0.72rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s;
        }

        .attached-actions a:hover {
            background: var(--ml-green-soft);
        }

        .attached-actions a:focus-visible,
        .edit-panel>summary:focus-visible {
            outline: 2px solid var(--ml-green);
            outline-offset: 2px;
        }

        .attached-actions .ic {
            width: 14px;
            height: 14px;
        }

        .no-file {
            margin: 0;
            padding: 0.8rem 1rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #475569;
            font-size: 0.76rem;
        }

        .submission-answer {
            margin-top: 1rem;
        }

        .submission-answer strong {
            display: block;
            margin-bottom: 0.4rem;
            color: #0f172a;
            font-size: 0.76rem;
        }

        .answer-content {
            padding: 0.8rem 1rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #334155;
            font-size: 0.78rem;
            line-height: 1.6;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        /* ---------- Form kumpul / ubah ---------- */
        .edit-panel {
            margin-top: 1.2rem;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .edit-panel>summary {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1rem;
            color: var(--ml-green);
            font-size: 0.76rem;
            font-weight: 700;
            list-style: none;
            cursor: pointer;
        }

        .edit-panel>summary::-webkit-details-marker {
            display: none;
        }

        .edit-panel>summary:hover {
            background: var(--ml-green-light);
        }

        .edit-panel>summary .chevron {
            margin-left: auto;
            transition: transform 0.2s ease;
        }

        .edit-panel[open]>summary {
            border-bottom: 1px solid #e2e8f0;
        }

        .edit-panel[open]>summary .chevron {
            transform: rotate(180deg);
        }

        .edit-panel>.task-fields {
            padding: 1.2rem;
        }

        .task-fields {
            display: grid;
            gap: 1rem;
        }

        .task-fields.standalone {
            margin-top: 1.5rem;
        }

        .panel h3.first {
            margin-top: 0;
        }

        .task-fields label {
            display: block;
            margin-bottom: 0.4rem;
            color: #0f172a;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .answer-input {
            width: 100%;
            min-height: 80px;
            padding: 0.8rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: #0f172a;
            font-family: inherit;
            font-size: 0.76rem;
            resize: vertical;
            outline: none;
        }

        .task-fields input[type=file] {
            width: 100%;
            padding: 0.5rem;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: #334155;
            font-family: inherit;
            font-size: 0.76rem;
            outline: none;
        }

        .task-fields input[type=file]::file-selector-button {
            margin-right: 0.8rem;
            padding: 0.5rem 0.9rem;
            background: var(--ml-green-light);
            border: 0;
            border-radius: 6px;
            color: var(--ml-green);
            font-family: inherit;
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
        }

        .task-fields input[type=file]::file-selector-button:hover {
            background: var(--ml-green-soft);
        }

        .task-note {
            margin: 0;
            color: #64748b;
            font-size: 0.7rem;
            line-height: 1.5;
        }

        .task-fields .btn-primary {
            width: 100%;
        }

        /* ---------- Panel kanan: status nilai & komentar ---------- */
        .status-box {
            display: flex;
            flex: 1;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            text-align: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .status-box .status-label {
            color: #64748b;
            font-size: 0.76rem;
        }

        .status-box .big-score {
            margin: 1rem 0;
            color: #cbd5e1;
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1;
        }

        .status-box.graded .big-score {
            color: var(--ml-green);
        }

        .reply {
            padding: 1rem;
            background: #ffffff;
            border-left: 4px solid var(--ml-green);
            border-radius: 0 8px 8px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .reply b {
            display: block;
            margin-bottom: 0.3rem;
            color: #0f172a;
            font-size: 0.76rem;
        }

        .reply p {
            margin: 0;
            color: #475569;
            font-size: 0.76rem;
            line-height: 1.6;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        /* ---------- Kosong ---------- */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2.5rem 1.5rem;
            text-align: center;
            background: var(--ml-green-light);
            border: 1px solid #bbf7d0;
            border-radius: 12px;
        }

        .empty-state-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            margin-bottom: 1rem;
            background: var(--ml-green-soft);
            border-radius: 50%;
            color: var(--ml-green);
        }

        .empty-state-icon .ic {
            width: 24px;
            height: 24px;
        }

        .empty-state h2 {
            margin: 0 0 0.5rem;
            color: #0f172a;
            font-size: 1rem;
        }

        .empty-state p {
            max-width: 440px;
            margin: 0 0 1.2rem;
            color: #475569;
            font-size: 0.8rem;
            line-height: 1.6;
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 640px) {
            .summary-legend {
                gap: 0.5rem;
            }

            .summary-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
                padding: 0.7rem;
            }

            .summary-icon {
                width: 30px;
                height: 30px;
            }

            .summary-item strong {
                font-size: 1.15rem;
            }

            .summary-item span {
                font-size: 0.66rem;
            }





            .panel {
                padding: 1.1rem;
            }

            .filter-panel {
                padding: 1rem;
            }

            .filter-panel>div {
                flex: 1 1 130px;
            }

            .filter-panel select {
                width: 100%;
                min-width: 120px;
            }

            .attached-file {
                align-items: flex-start;
            }

            .attached-actions {
                width: 100%;
            }

            .attached-actions a {
                flex: 1;
            }
        }
    </style>

    @if (session('success'))
        <div class="alert" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert error" role="alert"><strong>Periksa kembali:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-heading">
        <div>
            <span class="eyebrow">{!! $icon['leaf'] !!} RUANG BERTUMBUHMU</span>
            <h1>Tugas Kelas Saya</h1>
            <p>Kerjakan tugas sesuai tahap FPBL. Pengumpulan dan nilai tersimpan otomatis.</p>
        </div>
    </div>

    @php
        $totalTasks = $allTasks->count();
        $submittedPct = $totalTasks ? min(100, (int) round(($submittedCount / $totalTasks) * 100)) : 0;
        $gradedPct = $totalTasks ? min(100, (int) round(($gradedCount / $totalTasks) * 100)) : 0;
    @endphp
    <section class="panel summary-panel">
        <div class="summary-head">
            <h2>Progres Tugasmu</h2>
            <span class="badge">{{ $submittedPct }}% terkumpul</span>
        </div>
        <div class="progress-track" role="progressbar" aria-label="Progres pengumpulan tugas" aria-valuemin="0"
            aria-valuemax="100" aria-valuenow="{{ $submittedPct }}">
            <div class="progress-fill submitted" style="width:{{ $submittedPct }}%"></div>
            <div class="progress-fill graded" style="width:{{ $gradedPct }}%"></div>
        </div>
        <div class="summary-legend">
            <div class="summary-item">
                <span class="summary-icon total">{!! $icon['list'] !!}</span>
                <div><strong>{{ $allTasks->count() }}</strong><span>Total tugas</span></div>
            </div>
            <div class="summary-item">
                <span class="summary-icon submitted">{!! $icon['check_circle'] !!}</span>
                <div><strong>{{ $submittedCount }}</strong><span>Sudah dikumpulkan</span></div>
            </div>
            <div class="summary-item">
                <span class="summary-icon graded">{!! $icon['award'] !!}</span>
                <div><strong>{{ $gradedCount }}</strong><span>Sudah dinilai</span></div>
            </div>
        </div>
    </section>

    <form method="GET" action="{{ route('classroom.tasks.index') }}" class="filter-panel">
        <div>
            <label for="mlt-class">Kelas</label>
            <select id="mlt-class" name="class_id">
                <option value="">Semua kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected($selectedClassId === (int) $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="mlt-status">Status</label>
            <select id="mlt-status" name="status">
                <option value="semua" @selected($selectedStatus === 'semua')>Semua tugas</option>
                <option value="belum" @selected($selectedStatus === 'belum')>Belum dikumpulkan</option>
                <option value="dikumpulkan" @selected($selectedStatus === 'dikumpulkan')>Sudah dikumpulkan</option>
                <option value="dinilai" @selected($selectedStatus === 'dinilai')>Sudah dinilai</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Tampilkan</button>
    </form>

    <div class="task-list">
        @forelse($tasks as $task)
            @php
                $submission = $task->submissions->first();
                $closed = $task->due_at && now()->greaterThan($task->due_at);
                $graded = $submission && $submission->score !== null;
                $className = $classes->firstWhere('id', $task->class_id)?->name ?? 'Kelas';

                // Label status (urutan kondisi sama dengan sebelumnya)
                if ($graded) {
                    [$statusLabel, $statusClass] = ['Sudah dinilai', 'green'];
                } elseif ($submission) {
                    [$statusLabel, $statusClass] = ['Sudah dikumpulkan', 'green'];
                } elseif ($closed) {
                    [$statusLabel, $statusClass] = ['Deadline lewat', 'red'];
                } else {
                    [$statusLabel, $statusClass] = ['Belum dikumpulkan', 'yellow'];
                }
            @endphp
            <article class="two-columns">
                <!-- PANEL KIRI: DETAIL TUGAS & PENGUMPULAN -->
                <section class="panel">
                    <div class="badge-group">
                        <div class="badge-row">
                            <span class="badge">{{ $className }}</span>
                            <span class="badge">FPBL Tahap {{ $task->fpbl_stage }} dari 4</span>
                            <span class="badge gray">{!! $icon['clock'] !!}Deadline:
                                {{ $task->due_at?->format('d/m/Y H:i') ?? 'Tidak dibatasi' }}</span>
                        </div>
                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>

                    <h2>{{ $task->title }}</h2>

                    <h3 class="first">Instruksi guru</h3>
                    <div class="task-instructions">{{ $task->instructions }}</div>


                    @if ($submission)
                        <div class="submission-box">
                            <div class="submission-head">
                                <h3>Pengumpulanmu</h3>
                                <span class="submission-time">Dikumpulkan
                                    {{ $submission->submitted_at?->format('d/m/Y H:i') ?? '-' }}</span>
                            </div>
                            @if ($submission->file_path)
                                @php
                                    $fileExt = strtolower(pathinfo($submission->file_path, PATHINFO_EXTENSION));
                                    $fileLabel =
                                        $submission->original_file_name ?:
                                        'Lampiran tersimpan' . ($fileExt ? ' (' . strtoupper($fileExt) . ')' : '');
                                    $fileCanPreview = in_array($fileExt, ['pdf', 'jpg', 'jpeg', 'png'], true);
                                @endphp
                                <div class="attached-file">
                                    <div class="attached-icon" aria-hidden="true">
                                        {!! $icon['file'] !!}
                                        <span>{{ $fileExt ? strtoupper($fileExt) : 'FILE' }}</span>
                                    </div>
                                    <div class="attached-info">
                                        <div class="attached-name">{{ $fileLabel }}</div>
                                        <div class="attached-subtext">File terlampir
                                            @if ($submission->file_size_bytes)
                                                · {{ number_format($submission->file_size_bytes / 1024, 1, ',', '.') }} KB
                                            @endif
                                        </div>
                                    </div>
                                    <div class="attached-actions">
                                        @if ($fileCanPreview)
                                            <a href="{{ route('classroom.tasks.download', $submission) }}?preview=1"
                                                target="_blank" rel="noopener noreferrer"
                                                aria-label="Lihat File {{ $fileLabel }}">{!! $icon['eye'] !!}Lihat
                                                File</a>
                                        @endif
                                        <a href="{{ route('classroom.tasks.download', $submission) }}"
                                            aria-label="Unduh File {{ $fileLabel }}">{!! $icon['download'] !!}Unduh
                                            File</a>
                                    </div>
                                </div>
                            @else
                                <p class="no-file">Tidak ada file lampiran. Jawaban teks sudah tersimpan.</p>
                            @endif
                            @if (filled($submission->content))
                                <div class="submission-answer"><strong>Jawaban yang dikumpulkan</strong>
                                    <div class="answer-content">{{ $submission->content }}</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($graded)
                        {{-- Nilai dan catatan guru tampil di panel Status Penilaian di kanan --}}
                    @elseif($closed)
                        <div class="alert error no-margin">Pengumpulan sudah ditutup.
                            {{ $submission ? 'Jawaban terakhir tetap tersimpan.' : 'Belum ada pengumpulan dari akunmu.' }}
                        </div>
                    @else
                        @if ($submission)
                            <details class="edit-panel" @if ($errors->any()) open @endif>
                                <summary>{!! $icon['pencil'] !!}Ubah jawaban / Ganti file{!! $icon['chevron'] !!}</summary>
                        @endif
                        <form action="{{ route('classroom.tasks.submit', $task) }}" method="POST"
                            enctype="multipart/form-data" class="task-fields {{ $submission ? '' : 'standalone' }}">
                            @csrf
                            <div><label for="mlt-content-{{ $task->id }}">Jawaban atau komentar</label>
                                <textarea id="mlt-content-{{ $task->id }}" name="content" rows="4" maxlength="5000" class="answer-input"
                                    placeholder="Tuliskan jawaban atau penjelasan tugas di sini">{{ old('content', $submission?->content) }}</textarea>
                            </div>
                            <div><label
                                    for="mlt-file-{{ $task->id }}">{{ $submission?->file_path ? 'Ganti file (opsional)' : 'Lampiran (opsional)' }}</label><input
                                    id="mlt-file-{{ $task->id }}" type="file" name="file"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"></div>
                            <p class="task-note">PDF, DOC, DOCX, JPG, PNG atau ZIP · Maksimal 10 MB.
                                {{ $submission?->file_path ? 'File lama tetap tersimpan jika tidak memilih pengganti.' : 'Isi jawaban atau unggah file.' }}
                            </p>
                            <div><button type="submit"
                                    class="btn-primary">{{ $submission ? 'Simpan perubahan' : 'Kumpulkan tugas' }}</button>
                            </div>
                        </form>
                        @if ($submission)
                            </details>
                        @endif
                    @endif
                </section>

                <!-- PANEL KANAN: STATUS NILAI & KOMENTAR GURU -->
                <aside>
                    <section class="panel">
                        <h2>Status Penilaian</h2>
                        <div class="status-box {{ $graded ? 'graded' : '' }}">
                            <strong class="status-label">Nilai:</strong>
                            <div class="big-score">{{ $graded ? $submission->score : '-' }} / 100</div>
                            <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>
                    </section>
                    @if ($graded)
                        <section class="panel">
                            <h2>Komentar Guru</h2>
                            <div class="reply"><b>Catatan guru</b>
                                <p>{{ $submission->teacher_feedback ?: 'Guru belum menambahkan catatan.' }}</p>
                            </div>
                        </section>
                    @endif
                </aside>
            </article>
        @empty
            <div class="empty-state">
                <span class="empty-state-icon">{!! $icon['clipboard'] !!}</span>
                <h2>Belum ada tugas yang cocok</h2>
                <p>Coba ubah filter kelas atau status. Jika belum bergabung kelas, masuk lewat menu Kelas Saya.</p>
                <a class="btn-primary" href="{{ route('classroom.home') }}">Buka Kelas Saya</a>
            </div>
        @endforelse
    </div>
@endsection

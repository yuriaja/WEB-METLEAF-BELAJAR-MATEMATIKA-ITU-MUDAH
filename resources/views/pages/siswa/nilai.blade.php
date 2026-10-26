@extends('layouts.app')

@section('title', 'Rapor Kelas | MathLeaf')
@section('header_title', 'Rapor & Progres')

@section('content')
    @php
        $userName = $user->name;
        $selectedClass = $selectedClassId === null ? null : $classes->firstWhere('id', $selectedClassId);
        $namaKelas = $selectedClass?->name ?? 'Semua Kelas';
    @endphp

    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
        }

        body {
            font-size: 16px;
            background-color: #f8fafc;
        }

        .page-heading {
            margin-bottom: 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .page-heading h1 {
            font-size: 2.2rem;
            color: #0f172a;
            margin: 0.5rem 0;
            font-weight: 900;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .page-heading p {
            color: #475569;
            font-size: 1.1rem;
            margin: 0;
            line-height: 1.6;
        }

        /* TOMBOL GANTI KELAS */
        .btn-switch-class {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-switch-class:hover {
            border-color: var(--ml-green);
            color: var(--ml-green);
            background: #f8fafc;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.8rem;
            display: flex;
            align-items: flex-start;
            gap: 1.2rem;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.03);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(15, 23, 42, 0.05);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.95rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 0.4rem;
            display: flex;
            align-items: baseline;
            gap: 0.3rem;
        }

        .stat-value small {
            font-size: 1rem;
            color: #64748b;
            font-weight: 600;
        }

        .stat-desc {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 500;
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* PANEL TABEL NILAI (FOKUS UTAMA) */
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
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-head h2 {
            font-size: 1.5rem;
            margin: 0;
            color: #0f172a;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .custom-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        .custom-table th {
            background: #f8fafc;
            padding: 1rem 1.2rem;
            text-align: left;
            font-size: 0.95rem;
            color: #64748b;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
        }

        .custom-table td {
            padding: 1.2rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 1rem;
        }

        .custom-table tr:hover td {
            background: #f8fafc;
        }

        .custom-table td b {
            color: #0f172a;
            font-size: 1.1rem;
        }

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }

        .badge.green {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge.blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        /* =========================================
           FLOATING WIDGET (PROGRESS MATERI)
           ========================================= */
        .widget-toggle-btn {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--ml-green);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(8, 101, 52, 0.4);
            z-index: 999;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .widget-toggle-btn:hover {
            transform: translateY(-50%) scale(1.1);
            background: var(--ml-green-hover);
        }

        .widget-toggle-btn.active {
            right: 420px;
        }

        /* Geser menyesuaikan lebar panel */

        .progress-drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.3);
            z-index: 9998;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .progress-drawer-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .progress-drawer {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: #ffffff;
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            transition: right 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
        }

        .progress-drawer.active {
            right: 0;
        }

        .drawer-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .drawer-header h2 {
            font-size: 1.2rem;
            margin: 0;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-close-drawer {
            background: transparent;
            border: none;
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            transition: color 0.2s;
        }

        .btn-close-drawer:hover {
            color: #ef4444;
        }

        .drawer-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        /* HORIZONTAL GROWTH TRACK (MINIMALIS) */
        .growth-list {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .growth-item {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            padding: 1rem;
            border-radius: 12px;
        }

        .growth-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 0.8rem;
        }

        .growth-info strong {
            display: block;
            font-size: 1.05rem;
            color: #0f172a;
            margin-bottom: 0.2rem;
        }

        .growth-info small {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .growth-percentage {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--ml-green);
        }

        .growth-track-bg {
            width: 100%;
            height: 12px;
            background: #f1f5f9;
            border-radius: 20px;
            position: relative;
        }

        .growth-fill {
            height: 100%;
            border-radius: 20px;
            position: relative;
            background: linear-gradient(90deg, #34d399 0%, #059669 100%);
        }

        .growth-leaf-icon {
            position: absolute;
            right: -8px;
            top: -4px;
            width: 20px;
            height: 20px;
            background: white;
            border: 2px solid #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #059669;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3);
        }

        .growth-leaf-icon svg {
            width: 10px;
            height: 10px;
        }

        .growth-fill.empty {
            background: transparent;
        }

        .growth-fill.empty .growth-leaf-icon {
            border-color: #cbd5e1;
            color: #cbd5e1;
            box-shadow: none;
            left: 0;
        }

        @media (max-width: 640px) {
            .progress-drawer {
                width: 100%;
                right: -100%;
            }

            .widget-toggle-btn.active {
                right: 20px;
                opacity: 0;
                pointer-events: none;
            }
        }
    </style>

    <div class="page-heading">
        <div>
            <span style="color:#086534;font-weight:800;font-size:.9rem">RAPOR BELAJAR</span>
            <h1>Rapor {{ $userName }} — {{ $namaKelas }}</h1>
            <p>Nilai kuis, tugas, dan kemajuan materimu dihitung dari aktivitas yang tersimpan.</p>
        </div>
        <form method="GET" action="{{ route('siswa.nilai') }}">
            <label for="ml-class" style="display:block;margin-bottom:7px;color:#475569">Pilih kelas</label>
            <select id="ml-class" name="class_id" class="btn-switch-class" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected($selectedClassId === (int) $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><div class="stat-content">
            <span class="stat-label">Rata-rata Nilai</span>
            <div class="stat-value">{{ $average === null ? '—' : number_format($average, 1) }}</div>
            <span class="stat-desc">Dari kuis dan tugas yang sudah dinilai</span>
        </div></div>
        <div class="stat-card"><div class="stat-content">
            <span class="stat-label">Kuis Selesai</span>
            <div class="stat-value">{{ $attempts->count() }}</div>
            <span class="stat-desc">Total percobaan yang telah dikirim</span>
        </div></div>
        <div class="stat-card"><div class="stat-content">
            <span class="stat-label">Progres Belajar</span>
            <div class="stat-value">{{ $progressAverage }}<small>%</small></div>
            <span class="stat-desc">Rata-rata progres materi tersedia</span>
        </div></div>
        <div class="stat-card"><div class="stat-content">
            <span class="stat-label">Tugas Terkumpul</span>
            <div class="stat-value">{{ $tasksSubmitted }}<small>/ {{ $tasksTotal }}</small></div>
            <span class="stat-desc">Jumlah tugas dari kelas terpilih</span>
        </div></div>
    </div>

    <section class="panel">
        <div class="panel-head"><h2>Riwayat Nilai</h2></div>
        <div class="custom-table-wrap">
            <table class="custom-table">
                <thead><tr><th>Kegiatan</th><th>Tanggal</th><th>Status</th><th>Nilai</th><th>Catatan Guru</th></tr></thead>
                <tbody>
                    @forelse ($history as $entry)
                        <tr>
                            <td><strong>{{ $entry->category }}: {{ $entry->title }}</strong></td>
                            <td>{{ $entry->date?->format('d M Y H:i') ?? '—' }}</td>
                            <td><span class="badge {{ $entry->score === null ? 'blue' : 'green' }}">{{ $entry->score === null ? 'Menunggu Nilai' : 'Sudah Dinilai' }}</span></td>
                            <td><b>{{ $entry->score === null ? '—' : number_format((float) $entry->score, 1) }}</b></td>
                            <td>
                                {{ $entry->feedback ?? '—' }}
                                @if ($entry->result_id)
                                    <a style="display:block;color:#086534;font-weight:600;margin-top:5px" href="{{ route('siswa.quiz.result', $entry->result_id) }}">Lihat hasil kuis</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:32px;color:#64748b">
                            Belum ada hasil kuis atau tugas. Mulai belajar dan kerjakan aktivitas dari kelasmu.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Progres Materi Kelas</h2></div>
        <div class="growth-list">
            @forelse ($materials as $material)
                @php($percent = (int) $progressByMaterial->get($material->id, 0))
                <div class="growth-item">
                    <div class="growth-header">
                        <div class="growth-info"><strong>{{ $material->title }}</strong><small>{{ $percent === 100 ? 'Tuntas' : ($percent > 0 ? 'Sedang dipelajari' : 'Belum dimulai') }}</small></div>
                        <div class="growth-percentage">{{ $percent }}%</div>
                    </div>
                    <div class="growth-track-bg"><div class="growth-fill" style="width:{{ $percent }}%"></div></div>
                </div>
            @empty
                <p style="color:#64748b">Belum ada materi yang diterbitkan untuk kelas ini.</p>
            @endforelse
        </div>
    </section>

@endsection

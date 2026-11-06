@extends('layouts.app')

@section('title', 'Rapor Kelas | MathLeaf')
@section('header_title', 'Rapor & Progres')

@section('content')
    @php
        $userName = $user->name;
        // Asumsi siswa hanya 1 kelas
        $namaKelas = $user->class->name ?? 'Kelas Saya';
    @endphp

    <style>
        /* Mengaktifkan smooth scrolling saat navigasi anchor diklik */
        html {
            scroll-behavior: smooth;
        }

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

        /* ---------- Header ---------- */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1.25rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .header-text {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 800;
            color: var(--ml-green);
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .header-text h1 {
            font-size: clamp(1.44rem, 2.4vw, 1.84rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .2rem;
            line-height: 1.2;
        }

        .header-text p {
            color: var(--ml-text-muted);
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.5;
        }

        /* ---------- STATS GRID (Disesuaikan untuk 5 Kotak) ---------- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 520px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Stat Card diubah menjadi tag <a> untuk efek klik */
        .stat-card {
            position: relative;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            padding: 1.4rem;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            transition: transform .25s, box-shadow .25s, border-color .25s;
            overflow: hidden;
            text-decoration: none;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(8, 101, 52, .08);
            border-color: #b5d9c1;
        }

        .stat-icon-wrap {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-label {
            padding-right: 3rem;
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.8;
            pointer-events: none;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #14301f;
            line-height: 1;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: baseline;
            gap: 0.3rem;
        }

        .stat-value small {
            font-size: 1rem;
            color: #64748b;
            font-weight: 700;
        }

        .stat-desc {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 0.8rem;
        }

        /* Mini Diagram Batang di dalam Kotak Stats */
        .stat-mini-diagram {
            width: 100%;
            height: 6px;
            background-color: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
            margin-top: auto;
        }

        .stat-mini-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        /* ---------- PANEL UMUM ---------- */
        .panel {
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            padding: 1.8rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            scroll-margin-top: 100px;
        }

        .panel-head {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e8f0ea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .panel-head h2 {
            font-size: 1.15rem;
            margin: 0;
            color: #14301f;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        /* ---------- TABEL ---------- */
        .submission-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .submission-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 10px;
            overflow: hidden;
            min-width: 800px;
        }

        .submission-table th,
        .submission-table td {
            padding: 1rem 1.1rem;
            text-align: left;
            border-bottom: 1px solid #e8f0ea;
            font-size: 0.88rem;
            vertical-align: middle;
        }

        .submission-table th {
            background: #fbfdfb;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
        }

        .submission-table tr:hover td {
            background: #fbfdfb;
        }

        .submission-name {
            font-weight: 700;
            color: #14301f;
        }

        /* ---------- STATUS BADGE ---------- */
        .status-belum-dinilai {
            color: #dc2626;
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            background: #fef2f2;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            display: inline-block;
        }

        .status-sudah-dinilai {
            color: var(--ml-green);
            font-weight: 800;
            font-size: 1.05rem;
        }

        /* ---------- PROGRESS MATERI (GROWTH TRACK DRAWER) ---------- */
        .growth-list {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .growth-item {
            background: #ffffff;
            border: 1px solid #dfe9e2;
            padding: 1rem;
            border-radius: 12px;
            transition: border-color .2s;
        }

        .growth-item:hover {
            border-color: var(--ml-green-border);
        }

        .growth-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 0.8rem;
        }

        .growth-info strong {
            display: block;
            font-size: 0.88rem;
            color: #0f172a;
            margin-bottom: 0.2rem;
        }

        .growth-info small {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .growth-percentage {
            font-size: 0.95rem;
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
            transition: width 0.8s ease-out;
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

        /* ---------- WIDGET DRAWER SLIDE ---------- */
        .widget-toggle-btn {
            position: fixed;
            right: 20px;
            bottom: 30px;
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
            transform: translateY(-5px) scale(1.05);
            background: var(--ml-green-hover);
        }

        .widget-toggle-btn.active {
            right: 420px;
            opacity: 0;
            pointer-events: none;
        }

        .progress-drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(2px);
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
            font-size: 1.05rem;
            margin: 0;
            color: #0f172a;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-close-drawer {
            background: #f1f5f9;
            border: none;
            border-radius: 8px;
            width: 32px;
            height: 32px;
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-close-drawer:hover {
            background: #fee2e2;
            color: #ef4444;
        }

        .drawer-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        @media (max-width: 640px) {
            .progress-drawer {
                width: 100%;
                right: -100%;
            }
        }
    </style>

    <!-- HEADER Halaman -->
    <div class="page-header">
        <div class="header-text">
            <span class="eyebrow">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Rapor Belajar
            </span>
            <h1>Rapor {{ $userName }} — {{ $namaKelas }}</h1>
            <p>Nilai kuis, tugas, dan kemajuan materimu dihitung dari aktivitas yang tersimpan.</p>
        </div>
    </div>

    <!-- STATS GRID (5 Kotak, Bisa diklik) -->
    <div class="stats-grid">

        <!-- 1. Peringkat Kelas -->
        <a href="#riwayat-nilai" class="stat-card">
            <div class="stat-icon-wrap">
                <span class="stat-label">Peringkat Kelas</span>
                <span class="stat-icon" style="background: rgba(254, 226, 226, 0.8); color: #ef4444;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                        <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                        <path d="M4 22h16"></path>
                        <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                        <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                        <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $rank ?? 1 }}<small>/ {{ $totalStudents ?? 1 }}</small></div>
            <span class="stat-desc">Berdasarkan rata-rata nilai</span>
            <div class="stat-mini-diagram">
                @php
                    // Logika persentase ranking (agar bar tidak selalu 100% jika peringkatnya rendah)
                    // Jika total siswa = 1, bar akan penuh.
                    $rankPercent =
                        ($totalStudents ?? 1) > 1 ? (1 - (($rank ?? 1) - 1) / (($totalStudents ?? 1) - 1)) * 100 : 100;
                @endphp
                <div class="stat-mini-fill" style="width: {{ $rankPercent }}%; background: #ef4444;"></div>
            </div>
        </a>

        <!-- 2. Rata-rata Nilai -->
        <a href="#riwayat-nilai" class="stat-card">
            <div class="stat-icon-wrap">
                <span class="stat-label">Rata-rata Nilai</span>
                <span class="stat-icon" style="background: rgba(237, 248, 240, 0.8); color: var(--ml-green);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                        </polygon>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $average === null ? '—' : number_format($average, 1) }}</div>
            <span class="stat-desc">Dari kuis & tugas ternilai</span>
            <div class="stat-mini-diagram">
                <div class="stat-mini-fill" style="width: {{ $average ?? 0 }}%; background: var(--ml-green);"></div>
            </div>
        </a>

        <!-- 3. Kuis Selesai -->
        <a href="#riwayat-nilai" class="stat-card">
            <div class="stat-icon-wrap">
                <span class="stat-label">Kuis Selesai</span>
                <span class="stat-icon" style="background: rgba(243, 232, 255, 0.75); color: #9333ea;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="8" r="6"></circle>
                        <path d="m8 13-2 8 6-3 6 3-2-8"></path>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $attempts->count() }}</div>
            <span class="stat-desc">Total percobaan dikirim</span>
            <div class="stat-mini-diagram">
                <div class="stat-mini-fill" style="width: {{ $attempts->count() > 0 ? 100 : 0 }}%; background: #9333ea;">
                </div>
            </div>
        </a>

        <!-- 4. Progres Belajar (Klik membuka drawer, bukan scroll) -->
        <div class="stat-card" onclick="toggleProgressDrawer()">
            <div class="stat-icon-wrap">
                <span class="stat-label">Progres Belajar</span>
                <span class="stat-icon" style="background: rgba(254, 249, 195, 0.8); color: #ca8a04;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M12 20V10"></path>
                        <path d="M18 20V4"></path>
                        <path d="M6 20v-4"></path>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $progressAverage }}<small>%</small></div>
            <span class="stat-desc">Rata-rata progres materi</span>
            <div class="stat-mini-diagram">
                <div class="stat-mini-fill" style="width: {{ $progressAverage }}%; background: #ca8a04;"></div>
            </div>
        </div>

        <!-- 5. Tugas Terkumpul -->
        @php
            $taskPercent = $tasksTotal > 0 ? ($tasksSubmitted / $tasksTotal) * 100 : 0;
        @endphp
        <a href="#riwayat-nilai" class="stat-card">
            <div class="stat-icon-wrap">
                <span class="stat-label">Tugas Terkumpul</span>
                <span class="stat-icon" style="background: rgba(224, 242, 254, 0.75); color: #0284c7;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $tasksSubmitted }}<small>/ {{ $tasksTotal }}</small></div>
            <span class="stat-desc">Dari kelas terpilih</span>
            <div class="stat-mini-diagram">
                <div class="stat-mini-fill" style="width: {{ $taskPercent }}%; background: #0284c7;"></div>
            </div>
        </a>
    </div>

    <!-- PANEL RIWAYAT NILAI (Anchor ID) -->
    <section class="panel" id="riwayat-nilai">
        <div class="panel-head">
            <h2>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Riwayat Nilai
            </h2>
        </div>
        <div class="submission-table-wrap">
            <table class="submission-table">
                <thead>
                    <tr>
                        <th>Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Nilai</th>
                        <th>Catatan Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $entry)
                        <tr>
                            <td>
                                <div class="submission-name">{{ $entry->category }}: {{ $entry->title }}</div>
                            </td>
                            <td style="color: #64748b;">
                                {{ $entry->date?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td>
                                @if ($entry->score === null)
                                    <div class="status-belum-dinilai">MENUNGGU NILAI</div>
                                @else
                                    <div class="status-sudah-dinilai" style="font-size:0.85rem;">SUDAH DINILAI</div>
                                @endif
                            </td>
                            <td>
                                @if ($entry->score === null)
                                    <span style="color: #94a3b8;">—</span>
                                @else
                                    <strong style="color: var(--ml-green); font-size: 1.05rem;">
                                        {{ number_format((float) $entry->score, 1) }}
                                    </strong>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; color: #475569;">{{ $entry->feedback ?? '—' }}</div>
                                @if ($entry->result_id)
                                    <a style="display:inline-flex; align-items:center; gap:4px; color:var(--ml-green); font-weight:700; margin-top:6px; font-size:0.82rem; text-decoration:none;"
                                        href="{{ route('siswa.quiz.result', $entry->result_id) }}">
                                        Lihat hasil
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                            <path d="m12 5 7 7-7 7"></path>
                                        </svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:2.5rem; color:#64748b;">
                                Belum ada hasil kuis atau tugas. Mulai belajar dan kerjakan aktivitas dari kelasmu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- WIDGET TOMBOL MENGAMBANG -->
    <div class="widget-toggle-btn" id="widgetToggleBtn" onclick="toggleProgressDrawer()"
        title="Lihat Diagram Progres Materi">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="20" x2="18" y2="10"></line>
            <line x1="12" y1="20" x2="12" y2="4"></line>
            <line x1="6" y1="20" x2="6" y2="14"></line>
        </svg>
    </div>

    <!-- DRAWER PROGRES MATERI (Diagram Batang) -->
    <div class="progress-drawer-overlay" id="drawerOverlay" onclick="toggleProgressDrawer()"></div>
    <div class="progress-drawer" id="progressDrawer">
        <div class="drawer-header">
            <h2>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="2.5">
                    <path d="M12 20V10"></path>
                    <path d="M18 20V4"></path>
                    <path d="M6 20v-4"></path>
                </svg>
                Diagram Progres Materi
            </h2>
            <button class="btn-close-drawer" onclick="toggleProgressDrawer()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="drawer-content">
            <div class="growth-list">
                @forelse ($materials as $material)
                    @php($percent = (int) $progressByMaterial->get($material->id, 0))
                    <div class="growth-item">
                        <div class="growth-header">
                            <div class="growth-info">
                                <strong>{{ $material->title }}</strong>
                                <small>{{ $percent === 100 ? 'Tuntas' : ($percent > 0 ? 'Sedang dipelajari' : 'Belum dimulai') }}</small>
                            </div>
                            <div class="growth-percentage">{{ $percent }}%</div>
                        </div>
                        <div class="growth-track-bg">
                            <div class="growth-fill {{ $percent === 0 ? 'empty' : '' }}"
                                style="width:{{ $percent }}%">
                                <div class="growth-leaf-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        style="text-align: center; padding: 2rem; color: #64748b; font-size: 0.95rem; border: 2px dashed #cbd5e1; border-radius: 12px;">
                        Belum ada materi yang diterbitkan untuk kelas ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- SCRIPT UNTUK TOGGLE DRAWER -->
    <script>
        function toggleProgressDrawer() {
            document.getElementById('drawerOverlay').classList.toggle('active');
            document.getElementById('progressDrawer').classList.toggle('active');
            document.getElementById('widgetToggleBtn').classList.toggle('active');
        }
    </script>
@endsection

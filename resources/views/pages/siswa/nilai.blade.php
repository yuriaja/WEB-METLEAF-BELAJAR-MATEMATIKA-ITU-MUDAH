@extends('layouts.app')

@section('title', 'Rapor Kelas | MathLeaf')
@section('header_title', 'Rapor & Progres')

@section('content')
    @php
        $userName = Auth::user()->name ?? 'Karin';
        // Dummy kelas, nanti ambil dari relasi database
        $namaKelas = 'VII A';
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
            <span class="eyebrow"
                style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #086534; font-size: 0.9rem; letter-spacing: 0.05em; text-transform: uppercase;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                RAPOR BELAJAR
            </span>
            <h1>
                Rapor {{ $userName }} - {{ $namaKelas }}
                <!-- Tombol Ganti Kelas -->
                <button type="button" class="btn-switch-class"
                    onclick="alert('Pilihan dropdown kelas akan muncul di sini')">
                    Ganti Kelas
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </h1>
            <p>Setiap latihan membawa pemahaman baru. Yuk, lihat hasil kerjamu sejauh ini!</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-icon" style="background: #fef9c3; color: #ca8a04;">
                <!-- Ikon Bintang (SVG) -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polygon
                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                    </polygon>
                </svg>
            </span>
            <div class="stat-content">
                <span class="stat-label">Rata-rata Nilai</span>
                <div class="stat-value">85</div>
                <span class="stat-desc">Kategori: Sangat Baik</span>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                <!-- Ikon Peringkat/Trophy (SVG) -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                    <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                    <path d="M4 22h16"></path>
                    <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                    <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                    <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                </svg>
            </span>
            <div class="stat-content">
                <span class="stat-label">Peringkat Kelas</span>
                <div class="stat-value">5 <small>/ 32</small></div>
                <span class="stat-desc">Pertahankan prestasimu!</span>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-icon" style="background: var(--ml-green-light); color: var(--ml-green);">
                <!-- Ikon Buku (SVG) -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </span>
            <div class="stat-content">
                <span class="stat-label">Progres Belajar</span>
                <div class="stat-value">65<small>%</small></div>
                <span class="stat-desc">Materi berhasil dikuasai</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background: #f3e8ff; color: #9333ea;">
                <!-- Ikon Ceklis Dokumen (SVG) -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <path d="M9 15l2 2 4-4"></path>
                </svg>
            </span>
            <div class="stat-content">
                <span class="stat-label">Tugas Terkumpul</span>
                <div class="stat-value">8 <small>/ 12</small></div>
                <span class="stat-desc">Ayo selesaikan sisanya!</span>
            </div>
        </div>
    </div>

    <section class="panel">
        <div class="panel-head">
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="9" y1="21" x2="9" y2="9"></line>
                </svg>
                Riwayat Penilaian Utama
            </h2>
        </div>

        <div class="custom-table-wrap">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Kegiatan Pembelajaran</th>
                        <th>Waktu Penyelesaian</th>
                        <th>Status</th>
                        <th>Nilai Akhir</th>
                        <th>Catatan & Apresiasi Guru</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong style="display:block; color:#0f172a; margin-bottom:0.2rem;">Kuis: Bilangan
                                Bulat</strong>
                            <small style="color:#64748b;">Evaluasi Tahap 1</small>
                        </td>
                        <td>15 Okt 2026</td>
                        <td>
                            <span class="badge green">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Selesai
                            </span>
                        </td>
                        <td><b>85</b> / 100</td>
                        <td>Pemahaman sudah sangat bagus. Ingat untuk lebih teliti lagi pada pengurangan angka negatif ya.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong style="display:block; color:#0f172a; margin-bottom:0.2rem;">Tugas: E-LKPD Garis
                                Bilangan</strong>
                            <small style="color:#64748b;">Tugas Kelompok</small>
                        </td>
                        <td>12 Okt 2026</td>
                        <td>
                            <span class="badge blue">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                Direview
                            </span>
                        </td>
                        <td><b>90</b> / 100</td>
                        <td>Visualisasi garis bilangan kelompokmu sangat jelas dan kreatif. Pertahankan!</td>
                    </tr>
                    <tr>
                        <td>
                            <strong style="display:block; color:#0f172a; margin-bottom:0.2rem;">Refleksi: Operasi
                                Bilangan</strong>
                            <small style="color:#64748b;">Tugas Individu</small>
                        </td>
                        <td>10 Okt 2026</td>
                        <td>
                            <span class="badge green">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Selesai
                            </span>
                        </td>
                        <td><b>88</b> / 100</td>
                        <td>Pola pikir logismu sangat terlihat. Penjelasanmu sangat runtut dan mudah dipahami.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ==========================================
         FLOATING WIDGET BUTTON
         ========================================== -->
    <div class="widget-toggle-btn" id="widget-btn" onclick="toggleProgressDrawer()" title="Lihat Progres Materi">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20V10"></path>
            <path d="M18 20V4"></path>
            <path d="M6 20v-4"></path>
        </svg>
    </div>

    <!-- ==========================================
         PROGRESS DRAWER (SLIDE-OUT DARI KANAN)
         ========================================== -->
    <div class="progress-drawer-overlay" id="drawer-overlay" onclick="toggleProgressDrawer()"></div>
    <div class="progress-drawer" id="progress-drawer">
        <div class="drawer-header">
            <h2>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                    </path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                Detail Progres Materi
            </h2>
            <button class="btn-close-drawer" onclick="toggleProgressDrawer()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="drawer-content">
            <div class="growth-list">
                <!-- MATERI 1: SELESAI -->
                <div class="growth-item">
                    <div class="growth-header">
                        <div class="growth-info">
                            <strong>Konsep Bilangan Bulat</strong>
                            <small>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                Tuntas
                            </small>
                        </div>
                        <div class="growth-percentage">100%</div>
                    </div>
                    <div class="growth-track-bg">
                        <div class="growth-fill" style="width: 100%;">
                            <div class="growth-leaf-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MATERI 2: BERJALAN -->
                <div class="growth-item">
                    <div class="growth-header">
                        <div class="growth-info">
                            <strong>Penjumlahan & Pengurangan</strong>
                            <small>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                Sedang dipelajari
                            </small>
                        </div>
                        <div class="growth-percentage" style="color: #f59e0b;">65%</div>
                    </div>
                    <div class="growth-track-bg">
                        <div class="growth-fill"
                            style="width: 65%; background: linear-gradient(90deg, #fcd34d 0%, #f59e0b 100%);">
                            <div class="growth-leaf-icon" style="border-color: #f59e0b; color: #f59e0b;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"></path>
                                    <path d="M4 21c0-6 5-11 11-14"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MATERI 3: BELUM DIMULAI -->
                <div class="growth-item">
                    <div class="growth-header">
                        <div class="growth-info">
                            <strong>Perkalian & Pembagian</strong>
                            <small>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2">
                                    </rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                Belum dibuka
                            </small>
                        </div>
                        <div class="growth-percentage" style="color: #94a3b8;">0%</div>
                    </div>
                    <div class="growth-track-bg">
                        <div class="growth-fill empty" style="width: 0%;">
                            <div class="growth-leaf-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2">
                                    </rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MATERI 4: BELUM DIMULAI -->
                <div class="growth-item">
                    <div class="growth-header">
                        <div class="growth-info">
                            <strong>Operasi Pecahan</strong>
                            <small>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2">
                                    </rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                Belum dibuka
                            </small>
                        </div>
                        <div class="growth-percentage" style="color: #94a3b8;">0%</div>
                    </div>
                    <div class="growth-track-bg">
                        <div class="growth-fill empty" style="width: 0%;">
                            <div class="growth-leaf-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2">
                                    </rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Logika untuk menampilkan & menyembunyikan Floating Widget Drawer
        function toggleProgressDrawer() {
            const drawer = document.getElementById('progress-drawer');
            const overlay = document.getElementById('drawer-overlay');
            const btn = document.getElementById('widget-btn');

            drawer.classList.toggle('active');
            overlay.classList.toggle('active');
            btn.classList.toggle('active');
        }
    </script>

@endsection

@extends('layouts.app')

@section('title', 'Dashboard Siswa | MathLeaf')
@section('header_title', 'Dashboard Siswa')

@section('content')
    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
        }

        .page-heading {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-heading h1 {
            font-size: 1.8rem;
            color: #0f172a;
            margin: 0.5rem 0;
        }

        .page-heading p {
            color: #64748b;
            font-size: 1rem;
            margin: 0;
        }

        .badge-date {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--ml-green-light);
            color: var(--ml-green);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        /* BANNER DINAMIS & CANVAS INTERAKTIF */
        .dashboard-banner {
            background: #0f172a;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            position: relative;
            min-height: 220px;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.1);
        }

        .banner-text-content {
            z-index: 2;
            position: relative;
            max-width: 60%;
        }

        .banner-text-content h2 {
            color: white;
            font-size: 1.8rem;
            margin: 0.5rem 0 1rem 0;
            line-height: 1.3;
        }

        .banner-text-content p {
            color: #cbd5e1;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .btn-primary {
            background: var(--ml-green);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.95rem;
            transition: background 0.2s;
            position: relative;
            z-index: 2;
        }

        .btn-primary:hover {
            background: var(--ml-green-hover);
        }

        /* Canvas Tumbuhan 3D */
        #dashboardPlantCanvas {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 40%;
            height: 100%;
            z-index: 1;
            cursor: pointer;
        }

        /* Overlay Notifikasi Angka di atas Tanaman */
        .notif-badge-overlay {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: bold;
            border: 3px solid #0f172a;
            z-index: 2;
            cursor: pointer;
            animation: bounce 2s infinite;
            display: none;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* Pop-up Notifikasi Kustom */
        .custom-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .custom-popup-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .custom-popup-box {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(20px);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
        }

        .custom-popup-overlay.active .custom-popup-box {
            transform: translateY(0);
        }

        .popup-header {
            background: #f8fafc;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .popup-header h3 {
            margin: 0;
            font-size: 1.1rem;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .popup-close {
            background: transparent;
            border: none;
            cursor: pointer;
            color: #64748b;
        }

        .popup-body {
            padding: 1.5rem;
            text-align: center;
        }

        .popup-body p {
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }

        .notif-list-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid var(--ml-green);
            padding: 1rem;
            border-radius: 6px;
            text-align: left;
            margin-bottom: 0.75rem;
        }

        .notif-list-item strong {
            display: block;
            color: #0f172a;
            margin-bottom: 0.2rem;
        }

        .notif-list-item small {
            color: #64748b;
            font-size: 0.8rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        .stat-icon {
            background: var(--ml-green-light);
            color: var(--ml-green);
            width: 40px;
            height: 40px;
            border-radius: 8px;
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
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-desc {
            font-size: 0.75rem;
            color: #94a3b8;
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

            .banner-text-content {
                max-width: 100%;
            }

            #dashboardPlantCanvas {
                opacity: 0.3;
                width: 100%;
            }
        }

        /* Bottom Columns */
        .dashboard-columns {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 900px) {
            .dashboard-columns {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .panel-head h2 {
            font-size: 1.25rem;
            margin: 0;
            color: #0f172a;
        }

        .activity-row {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .activity-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .activity-row strong {
            display: block;
            color: #0f172a;
            margin-bottom: 0.2rem;
            font-size: 0.95rem;
        }

        .activity-row small {
            color: #64748b;
            font-size: 0.85rem;
        }

        .schedule-row {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .date-block {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            width: 45px;
            height: 45px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
        }

        .date-block small {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 0.1rem;
        }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow"
                style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #086534; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"></path>
                    <path d="M4 21c0-6 5-11 11-14"></path>
                </svg>
                RUANG BERTUMBUHMU
            </span>
            <h1>Halo, {{ Auth::user()->name ?? 'Karin' }}! Selamat Datang! 👋</h1>
            <p>Hari yang baik untuk belajar hal baru. Yuk, lanjutkan petualanganmu!</p>
        </div>
        <span class="badge-date">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </span>
    </div>

    @php
        // Variabel ini akan dilempar ke Javascript untuk menentukan jumlah daun 3D
        $unreadCount = 2; // Ubah ke 0 untuk melihat banner tanpa notifikasi/tumbuhan

        $bannerTitle =
            $unreadCount > 0 ? "Ada $unreadCount tugas menantimu." : 'Matahari cerah, hari ini kosong dari tugas.';
        $bannerDesc =
            $unreadCount > 0
                ? 'Ayo selesaikan tugas-tugas di bawah ini agar tumbuhan pemahamanmu makin rimbun! Klik tumbuhan di samping untuk melihat detail.'
                : 'Hebat! Kamu sudah menyelesaikan semua tugas. Lanjutkan petualangan dengan mengulang materi terdahulu atau membaca E-Modul baru.';
    @endphp

    <!-- Banner Utama -->
    <section class="dashboard-banner">
        <div class="banner-text-content">
            <span class="eyebrow"
                style="color: var(--ml-green-border); font-size: 0.8rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">MATHLEAF
                NOTIFICATIONS</span>
            <h2>{{ $bannerTitle }}</h2>
            <p>{{ $bannerDesc }}</p>
            <a class="btn-primary" href="{{ url('/materi') }}">
                Lanjutkan Petualangan
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <!-- Area Canvas Interaktif Tumbuhan -->
        <canvas id="dashboardPlantCanvas" onclick="openInteractivePopup()" title="Klik untuk lihat notifikasi"></canvas>
        <!-- Badge Merah (Muncul jika ada notif > 0) -->
        <div id="notifBadge" class="notif-badge-overlay" onclick="openInteractivePopup()">{{ $unreadCount }}</div>
    </section>

    <!-- Pop-up Overlay -->
    <div id="interactive-popup" class="custom-popup-overlay">
        <div class="custom-popup-box">
            <div class="popup-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    Notifikasi MathLeaf
                </h3>
                <button class="popup-close" onclick="closeInteractivePopup()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="popup-body">
                @if ($unreadCount == 0)
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#f59e0b"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                        <line x1="9" y1="9" x2="9.01" y2="9"></line>
                        <line x1="15" y1="9" x2="15.01" y2="9"></line>
                    </svg>
                    <p><strong>Belum ada tugas atau materi baru!</strong><br>Yuk, jangan tunggu gurumu. Cek lagi pelajaran
                        terdahulu atau eksplorasi materi lain. Terus belajar, satu langkah kecil setiap hari 🌱</p>
                @else
                    <div class="notif-list-item">
                        <strong>Tugas Baru: Pecahan Lanjutan</strong>
                        <small>Diberikan oleh Guru Budi · 1 jam yang lalu</small>
                    </div>
                    <div class="notif-list-item">
                        <strong>Kuis Terjadwal: Bilangan Bulat</strong>
                        <small>Batas waktu: Besok, 23:59</small>
                    </div>
                    <a href="#" class="btn-primary"
                        style="width: 100%; margin-top: 1rem; justify-content: center; background: #0f172a;">Lihat Semua
                        Notifikasi</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistik 4 Kotak -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20v-6M6 20V10M18 20V4"></path>
                </svg></span>
            <div class="stat-content">
                <span class="stat-label">Progres Pembelajaran</span><span class="stat-value">65%</span><span
                    class="stat-desc">Terus bertumbuh, ya!</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background: #e0f2fe; color: #0284c7;"><svg width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg></span>
            <div class="stat-content">
                <span class="stat-label">Tugas Selesai</span><span class="stat-value">8</span><span
                    class="stat-desc">dari 12 tugas belajar</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background: #fef2f2; color: #dc2626;"><svg width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg></span>
            <div class="stat-content">
                <span class="stat-label">Tugas Belum Selesai</span><span class="stat-value">4</span><span
                    class="stat-desc">Yuk, selesaikan bertahap</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background: #fef9c3; color: #ca8a04;"><svg width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polygon
                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                    </polygon>
                </svg></span>
            <div class="stat-content">
                <span class="stat-label">Rata-rata Nilai</span><span class="stat-value">85</span><span
                    class="stat-desc">Pemahamanmu semakin baik</span>
            </div>
        </div>
    </div>

    <div class="dashboard-columns">
        <div>
            <section class="panel">
                <div class="panel-head">
                    <h2>Aktivitas Terbaru</h2>
                    <span
                        style="background: #f1f5f9; padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; color: #475569;">Minggu
                        ini</span>
                </div>
                <div class="activity-row">
                    <span class="stat-icon" style="width:32px; height:32px;"><svg width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg></span>
                    <div><strong>Menyelesaikan kuis Bilangan Bulat · Nilai 85</strong><small>Hari ini, 09.30</small></div>
                </div>
                <div class="activity-row">
                    <span class="stat-icon" style="width:32px; height:32px;"><svg width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                            </path>
                        </svg></span>
                    <div><strong>Mengirim pertanyaan tentang bilangan negatif</strong><small>Kemarin, 14.15</small></div>
                </div>
                <div class="activity-row">
                    <span class="stat-icon" style="width:32px; height:32px;"><svg width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="6 3 20 12 6 21 6 3"></polygon>
                        </svg></span>
                    <div><strong>Menonton Video Operasi Bilangan</strong><small>12 Oktober, 10.00</small></div>
                </div>
            </section>
        </div>

        <div>
            <section class="panel">
                <h2 style="font-size: 1.25rem; margin: 0 0 1.5rem 0; color: #0f172a;">Jadwal Kelas</h2>
                <div class="schedule-row">
                    <div class="date-block">16<small>OKT</small></div>
                    <div>
                        <strong style="display:block; color:#0f172a; font-size:0.95rem; margin-bottom:0.2rem;">Eksplorasi
                            Operasi Bilangan</strong>
                        <small style="color:#64748b; font-size:0.8rem;">Jumat · 08.00 – 09.20</small>
                    </div>
                </div>
                <div class="schedule-row">
                    <div class="date-block">19<small>OKT</small></div>
                    <div>
                        <strong style="display:block; color:#0f172a; font-size:0.95rem; margin-bottom:0.2rem;">Investigasi
                            Kelompok</strong>
                        <small style="color:#64748b; font-size:0.8rem;">Senin · 08.00 – 09.20</small>
                    </div>
                </div>
            </section>

            <section
                style="background: var(--ml-green-light); border: 1px solid var(--ml-green-border); border-radius: 8px; padding: 1.5rem; text-align: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem;">
                    <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"></path>
                    <path d="M4 21c0-6 5-11 11-14"></path>
                </svg>
                <h3 style="margin: 0 0 0.5rem 0; color: #0f172a; font-size: 1.1rem;">Tetap semangat!</h3>
                <p style="color: #334155; font-size: 0.95rem; line-height: 1.5; margin-bottom: 0;">"Hal hebat tumbuh dari
                    langkah kecil yang dilakukan setiap hari."</p>
            </section>
        </div>
    </div>

    <!-- SCRIPTS: Logic Tumbuhan 3D Math Plant Khusus Banner -->
    <script>
        // POP-UP LOGIC
        function openInteractivePopup() {
            document.getElementById('interactive-popup').classList.add('active');
        }

        function closeInteractivePopup() {
            document.getElementById('interactive-popup').classList.remove('active');
        }
        document.getElementById('interactive-popup').addEventListener('click', function(e) {
            if (e.target === this) closeInteractivePopup();
        });

        // CANVAS ANIMATION LOGIC
        const notifCount = {{ $unreadCount }};
        const canvas = document.getElementById('dashboardPlantCanvas');
        const ctx = canvas.getContext('2d');

        // Tampilkan badge angka di canvas jika > 0
        if (notifCount > 0) {
            document.getElementById('notifBadge').style.display = 'flex';
        }

        let w, h;

        function resize() {
            w = canvas.width = canvas.offsetWidth;
            h = canvas.height = canvas.offsetHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        const leaves = [];
        const bubbles = [];
        const mathChars = ['+', '−', '×', '÷', '=', '%', '½'];

        let rootX = w * 0.7; // Akar tanaman berada sedikit ke kanan
        let rootY = h + 20;

        function addLeaf(targetX, targetY) {
            let baseAng = (Math.random() * Math.PI) - (Math.random() * Math.PI);
            leaves.push({
                x: targetX,
                y: targetY,
                baseAngle: baseAng,
                angle: baseAng,
                scale: 0,
                maxScale: Math.random() * 0.5 + 0.5,
                growthRate: 0.05,
                swaySpeed: Math.random() * 0.0015 + 0.001,
                swayOffset: Math.random() * Math.PI * 2,
                swayAmount: Math.random() * 0.15 + 0.05
            });
            spawnBubble(targetX, targetY);
        }

        function spawnBubble(x, y) {
            bubbles.push({
                x: x + (Math.random() * 40 - 20),
                y: y + (Math.random() * 40 - 20),
                char: mathChars[Math.floor(Math.random() * mathChars.length)],
                speedY: -(Math.random() * 1.5 + 0.5),
                speedX: (Math.random() - 0.5) * 1.0,
                opacity: 1,
                size: Math.random() * 10 + 10,
                drift: Math.random() * Math.PI * 2
            });
        }

        function drawLeaf(leaf) {
            ctx.save();
            ctx.translate(leaf.x, leaf.y);
            ctx.rotate(leaf.angle);
            ctx.scale(leaf.scale, leaf.scale);

            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.quadraticCurveTo(35, -35, 70, -10);
            ctx.quadraticCurveTo(35, 25, 0, 0);

            let gradient = ctx.createLinearGradient(0, 0, 70, 0);
            gradient.addColorStop(0, '#34d399');
            gradient.addColorStop(1, '#059669');

            ctx.fillStyle = gradient;
            ctx.fill();

            ctx.strokeStyle = 'rgba(255,255,255,0.4)';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.quadraticCurveTo(35, -10, 65, -10);
            ctx.stroke();

            ctx.restore();
        }

        // Inisialisasi: Jika ada notif, tumbuhkan daun sesuai jumlah notif (max 6)
        if (notifCount > 0) {
            let maxLeaves = Math.min(notifCount, 6);
            for (let i = 0; i < maxLeaves; i++) {
                setTimeout(() => {
                    let ranX = (w * 0.5) + (Math.random() * (w * 0.3));
                    let ranY = (h * 0.6) - (Math.random() * (h * 0.4));
                    addLeaf(ranX, ranY);
                }, i * 300);
            }
        }

        function animate(time) {
            ctx.clearRect(0, 0, w, h);

            if (notifCount > 0) {
                // Gambar Akar
                rootX = w * 0.6;
                ctx.strokeStyle = '#6ee7b7';
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                let rootSway = Math.sin(time * 0.0005) * 10;

                leaves.forEach(leaf => {
                    leaf.angle = leaf.baseAngle + Math.sin(time * leaf.swaySpeed + leaf.swayOffset) * leaf
                        .swayAmount;
                    ctx.beginPath();
                    ctx.moveTo(rootX + rootSway, rootY);
                    ctx.quadraticCurveTo(rootX, leaf.y + 50, leaf.x, leaf.y);
                    ctx.stroke();
                });

                leaves.forEach(leaf => {
                    if (leaf.scale < leaf.maxScale) leaf.scale += leaf.growthRate;
                    drawLeaf(leaf);
                    if (Math.random() < 0.005) spawnBubble(leaf.x, leaf.y);
                });

                for (let i = bubbles.length - 1; i >= 0; i--) {
                    let b = bubbles[i];
                    b.x += b.speedX + Math.sin(time * 0.002 + b.drift) * 0.5;
                    b.y += b.speedY;
                    b.opacity -= 0.008;
                    if (b.opacity <= 0) {
                        bubbles.splice(i, 1);
                        continue;
                    }

                    ctx.save();
                    ctx.globalAlpha = b.opacity;
                    ctx.fillStyle = '#6ee7b7'; // Warna text hijau terang
                    ctx.font = `bold ${b.size}px 'Inter', sans-serif`;
                    ctx.fillText(b.char, b.x, b.y);
                    ctx.restore();
                }
            }
            requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
    </script>
@endsection

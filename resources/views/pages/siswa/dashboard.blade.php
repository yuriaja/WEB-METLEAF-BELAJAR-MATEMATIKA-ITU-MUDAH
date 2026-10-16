<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Dashboard Siswa | MathLeaf</title>
    <meta name="description" content="MathLeaf — Grow Your Math Skills. Pembelajaran Matematika kelas VII SMP." />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="stylesheet" href="{{ asset('css/mathleaf.css') }}" />
    <script defer src="{{ asset('js/mathleaf.js') }}"></script>
</head>

<body data-page="dashboard">
    <div class="app-layout" id="app-layout">
        <aside class="app-sidebar">
            <a href="{{ url('/') }}" class="logo logo-light"><span class="logo-mark"><svg aria-hidden="true"
                        width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                        <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
                        <path d="M4 21c0-6 5-11 11-14" />
                    </svg></span><span>Math<span class="logo-leaf">Leaf</span><small>GROW YOUR MATH
                        SKILLS</small></span></a>
            <div class="sidebar-class">
                <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    style="flex-shrink: 0">
                    <path d="M7 20h10" />
                    <path d="M10 20c5.5-2.5.8-8.3 5-11" />
                    <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
                    <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" />
                </svg><span>Matematika Kelas VII<small>Semester Ganjil · 2026/2027</small></span>
            </div>
            <span class="sidebar-caption">RUANG BELAJAR</span>
            <nav>
                <a href="{{ url('/dashboard') }}" class="selected"><svg aria-hidden="true" width="18" height="18"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" style="flex-shrink: 0">
                        <path d="m2 10 10-5 10 5-10 5-10-5Z" />
                        <path d="M6 12v5c3 2 9 2 12 0v-5M22 10v7" />
                    </svg><span>Dashboard</span></a><a href="{{ url('/materi') }}" class=""><svg
                        aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M12 7v14" />
                        <path
                            d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z" />
                    </svg><span>Materi Pembelajaran</span></a><a href="{{ url('/video') }}" class=""><svg
                        aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <polygon points="6 3 20 12 6 21 6 3" />
                    </svg><span>Video Pembelajaran</span></a><a href="{{ url('/latihan') }}" class=""><svg
                        aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z" />
                        <path d="m5 17 .8 2.2L8 20l-2.2.8L5 23l-.8-2.2L2 20l2.2-.8L5 17Z" />
                        <path d="m19 2 .7 1.3L21 4l-1.3.7L19 6l-.7-1.3L17 4l1.3-.7L19 2Z" />
                    </svg><span>E-LKPD Interaktif</span></a><a href="{{ url('/kuis') }}" class=""><svg
                        aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <circle cx="12" cy="8" r="6" />
                        <path d="m8 13-2 8 6-3 6 3-2-8" />
                    </svg><span>Kuis &amp; Evaluasi</span></a><a href="{{ url('/forum') }}" class=""><svg
                        aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg><span>Forum Diskusi</span></a><a href="{{ url('/tugas') }}" class=""><svg
                        aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M12 7v14" />
                        <path
                            d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z" />
                    </svg><span>Tugas &amp; Kelompok</span></a><a href="{{ url('/nilai') }}" class=""><svg
                        aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <circle cx="12" cy="8" r="6" />
                        <path d="m8 13-2 8 6-3 6 3-2-8" />
                    </svg><span>Nilai &amp; Progres</span></a>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-motivation">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M7 20h10" />
                        <path d="M10 20c5.5-2.5.8-8.3 5-11" />
                        <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
                        <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" />
                    </svg>
                    <p>
                        Setiap langkah kecil membawa perubahan besar. Terus bertumbuh!
                    </p>
                </div>
                <a href="{{ url('/') }}"><svg aria-hidden="true" width="18" height="18"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                        <path d="M5 12h14" />
                        <path d="m12 5 7 7-7 7" />
                    </svg><span>Ke Beranda</span></a><a href="{{ url('/masuk') }}" id="logout-demo"><svg
                        aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg><span>Keluar (demo)</span></a>
            </div>
        </aside>
        <div class="app-main">
            <header class="app-header">
                <div style="display: flex; align-items: center; gap: 12px">
                    <button type="button" class="btn btn-ghost" id="collapse-sidebar" aria-label="Ciutkan sidebar">
                        ☰</button><strong>Dashboard Siswa</strong>
                </div>
                <div style="display: flex; gap: 16px; align-items: center">
                    <button type="button" class="btn btn-ghost" id="notification-button" aria-label="Notifikasi">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z" />
                            <path d="m5 17 .8 2.2L8 20l-2.2.8L5 23l-.8-2.2L2 20l2.2-.8L5 17Z" />
                            <path d="m19 2 .7 1.3L21 4l-1.3.7L19 6l-.7-1.3L17 4l1.3-.7L19 2Z" />
                        </svg></button><span class="header-profile"><span class="avatar"
                            id="avatar-letter">A</span><span data-student-name>Aurora</span></span>
                </div>
                <div class="notification-panel" id="notification-panel" hidden>
                    <strong>Notifikasi MathLeaf</strong>
                    <p>Terus belajar, satu langkah kecil setiap hari 🌱</p>
                </div>
            </header>
            <main class="app-content">
                <div class="page-heading">
                    <div>
                        <span class="eyebrow"><svg aria-hidden="true" width="14" height="14"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                                <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
                                <path d="M4 21c0-6 5-11 11-14" />
                            </svg>
                            RUANG BERTUMBUHMU</span>
                        <h1>
                            Halo, <span data-student-name>Aurora</span>! Selamat Datang! 👋
                        </h1>
                        <p>
                            Hari yang baik untuk belajar hal baru. Yuk, lanjutkan
                            petualanganmu!
                        </p>
                    </div>
                    <span class="badge">🌱 Kamis, 15 Oktober 2026</span>
                </div>
                <section class="dashboard-banner">
                    <div>
                        <span class="eyebrow">GROW YOUR MATH SKILLS</span>
                        <h2>Setiap langkah kecil,<br />membuatmu semakin hebat.</h2>
                        <p>
                            Kamu sudah menyelesaikan
                            <span data-completed-count>3</span> dari 6 aktivitas Bilangan
                            Bulat.
                        </p>
                        <a class="btn btn-primary" href="{{ url('/video') }}">Lanjutkan Belajar
                            <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="flex-shrink: 0">
                                <path d="M5 12h14" />
                                <path d="m12 5 7 7-7 7" />
                            </svg></a>
                    </div>
                    <img src="{{ asset('images/mathleaf-hero.jpg') }}" alt="Teman belajar MathLeaf" />
                </section>
                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="stat-icon"><svg aria-hidden="true" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                                <path d="M12 7v14" />
                                <path
                                    d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z" />
                            </svg></span>
                        <div>
                            <span class="stat-label">Progres Pembelajaran</span><strong
                                data-stat-progress>65%</strong><small>Terus bertumbuh, ya!</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon"><svg aria-hidden="true" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                                <path d="m4 12 5 5L20 6" />
                            </svg></span>
                        <div>
                            <span class="stat-label">Tugas Selesai</span><strong>8</strong><small>dari 12 tugas
                                belajar</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon"><svg aria-hidden="true" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                                <path d="M12 7v14" />
                                <path
                                    d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z" />
                            </svg></span>
                        <div>
                            <span class="stat-label">Tugas Belum Selesai</span><strong>4</strong><small>Yuk, selesaikan
                                bertahap</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon"><svg aria-hidden="true" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                                <circle cx="12" cy="8" r="6" />
                                <path d="m8 13-2 8 6-3 6 3-2-8" />
                            </svg></span>
                        <div>
                            <span class="stat-label">Rata-rata Nilai</span><strong
                                data-stat-score>85</strong><small>Pemahamanmu semakin baik</small>
                        </div>
                    </div>
                </div>
                <div class="dashboard-columns">
                    <div>
                        <section class="panel">
                            <div class="panel-head">
                                <h2>Lanjutkan Belajar</h2>
                                <a href="{{ url('/materi') }}">Semua materi →</a>
                            </div>
                            <div class="continue-row">
                                <div class="continue-art">−3 + 5</div>
                                <div>
                                    <span class="badge">Tahap 1 · Mengamati & Menanya</span>
                                    <h3 style="margin-top: 12px">Mengenal Bilangan Bulat</h3>
                                    <p>Video 3 dari 4 · 8 menit</p>
                                    <a class="btn btn-primary" href="{{ url('/video') }}">Lanjutkan Video
                                        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                                            <polygon points="6 3 20 12 6 21 6 3" />
                                        </svg></a>
                                </div>
                            </div>
                            <div class="progress-label">
                                <span>Penyelesaian materi</span><strong data-progress-label>75%</strong>
                            </div>
                            <div id="dashboard-bar">
                                <div class="progress-track">
                                    <div style="width: 75%"></div>
                                </div>
                            </div>
                        </section>
                        <section class="panel" style="margin-top: 24px">
                            <div class="panel-head">
                                <h2>Aktivitas Terbaru</h2>
                                <span class="badge">Minggu ini</span>
                            </div>
                            <div class="activity-row">
                                <span class="activity-icon"><svg aria-hidden="true" width="18" height="18"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                                        <circle cx="12" cy="8" r="6" />
                                        <path d="m8 13-2 8 6-3 6 3-2-8" />
                                    </svg></span>
                                <div>
                                    <strong>Menyelesaikan kuis Bilangan Bulat · Nilai 85</strong><small>Hari ini,
                                        09.30</small>
                                </div>
                            </div>
                            <div class="activity-row">
                                <span class="activity-icon"><svg aria-hidden="true" width="18" height="18"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                                        <circle cx="12" cy="8" r="6" />
                                        <path d="m8 13-2 8 6-3 6 3-2-8" />
                                    </svg></span>
                                <div>
                                    <strong>Mengirim pertanyaan tentang bilangan negatif</strong><small>Kemarin,
                                        14.15</small>
                                </div>
                            </div>
                            <div class="activity-row">
                                <span class="activity-icon"><svg aria-hidden="true" width="18" height="18"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                                        <circle cx="12" cy="8" r="6" />
                                        <path d="m8 13-2 8 6-3 6 3-2-8" />
                                    </svg></span>
                                <div>
                                    <strong>Mengerjakan E-LKPD garis bilangan</strong><small>12 Oktober, 10.00</small>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div>
                        <section class="panel">
                            <h2>Jadwal Pembelajaran</h2>
                            <div class="schedule-row">
                                <div class="date-block">16<small>OKT</small></div>
                                <div>
                                    <strong>Eksplorasi Operasi Bilangan</strong><small>Jumat · 08.00 – 09.20</small>
                                </div>
                            </div>
                            <div class="schedule-row">
                                <div class="date-block">19<small>OKT</small></div>
                                <div>
                                    <strong>Investigasi Kelompok</strong><small>Senin · 08.00 – 09.20</small>
                                </div>
                            </div>
                            <div class="schedule-row">
                                <div class="date-block">21<small>OKT</small></div>
                                <div>
                                    <strong>Presentasi Kelompok</strong><small>Rabu · 09.30 – 10.50</small>
                                </div>
                            </div>
                        </section>
                        <section class="quote-panel">
                            <h3>🌱 Tetap semangat!</h3>
                            <p>
                                “Hal hebat tumbuh dari langkah kecil yang dilakukan setiap
                                hari.”
                            </p>
                            <small>MathLeaf · Grow Your Math Skills</small>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>

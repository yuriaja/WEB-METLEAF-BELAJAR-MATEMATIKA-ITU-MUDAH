@extends('layouts.app')

@section('title', 'Mengenal Bilangan Bulat | MathLeaf')
@section('header_title', 'Ruang Belajar')

@section('content')
    @php
        $isTeacher = Auth::check() && Auth::user()->role === 'guru';
    @endphp

    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
        }

        .learn-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
            transition: color 0.15s;
        }

        .back-link:hover {
            color: #0f172a;
        }

        .learn-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.4rem 0;
        }

        .learn-subtitle {
            color: #64748b;
            font-size: 0.95rem;
            margin: 0;
        }

        /* Tabs Video vs Modul */
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-tabs button {
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-weight: 600;
            background: transparent;
            border: 1px solid transparent;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-tabs button:hover {
            background: #f1f5f9;
        }

        .filter-tabs button.selected {
            background: #0f172a;
            color: white;
        }

        /* Layout Dua Kolom */
        .content-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 2rem;
        }

        @media (max-width: 900px) {
            .content-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Panel Kiri (Utama) */
        .main-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        .video-stage {
            background: #0f172a;
            aspect-ratio: 16/9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
        }

        .play-btn {
            background: var(--ml-green);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s;
            margin-top: 1rem;
        }

        .play-btn:hover {
            background: var(--ml-green-hover);
        }

        .panel-body {
            padding: 1.5rem;
        }

        .panel-body h2 {
            margin: 0 0 1rem 0;
            font-size: 1.25rem;
            color: #0f172a;
        }

        .panel-body p {
            color: #475569;
            line-height: 1.6;
        }

        /* E-Modul styling */
        .modul-content {
            padding: 1.5rem;
        }

        .modul-content h3 {
            color: #0f172a;
            font-size: 1.1rem;
            margin: 1.5rem 0 0.5rem 0;
        }

        .modul-content p {
            color: #334155;
            line-height: 1.7;
            margin-bottom: 1rem;
        }

        /* Form Pertanyaan */
        .question-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .question-box h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-family: inherit;
            font-size: 0.95rem;
            resize: vertical;
            min-height: 80px;
            outline: none;
        }

        .form-group textarea:focus {
            border-color: var(--ml-green);
        }

        /* Panel Kanan (Sidebar) */
        .side-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            height: max-content;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        .side-panel h3 {
            margin: 0 0 1rem 0;
            font-size: 1.1rem;
            color: #0f172a;
        }

        .lesson-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .lesson-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-decoration: none;
            color: #334155;
            transition: all 0.2s;
            cursor: pointer;
        }

        .lesson-item:hover {
            border-color: #cbd5e1;
            background: #ffffff;
        }

        .lesson-item.active {
            border-color: var(--ml-green);
            background: var(--ml-green-light);
        }

        .lesson-item.active .lesson-title {
            color: var(--ml-green);
            font-weight: 700;
        }

        .lesson-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
            color: #0f172a;
        }

        .lesson-meta {
            font-size: 0.8rem;
            color: #64748b;
        }

        .btn-primary {
            background: var(--ml-green);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            width: 100%;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: var(--ml-green-hover);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }
    </style>

    <div class="learn-header">
        <a href="{{ route('materi.index') ?? url('/materi') }}" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Daftar Materi
        </a>
        <h1 class="learn-title">Bilangan Bulat</h1>
        <p class="learn-subtitle">
            {{ $isTeacher ? 'Kelola konten video dan E-Modul untuk materi ini.' : 'Tahap 1 · Mengamati dan Menanya. Tonton, amati, dan ajukan pertanyaan.' }}
        </p>
    </div>

    <div class="filter-tabs">
        <button type="button" class="selected" id="tab-video" onclick="switchTab('video')">Video Pembelajaran</button>
        <button type="button" id="tab-modul" onclick="switchTab('modul')">E-Modul / Bacaan</button>
    </div>

    <div class="content-layout">
        <!-- Kolom Kiri: Video / Modul & Form -->
        <div>
            <!-- Konten Video -->
            <div class="main-panel" id="panel-video">
                <div class="video-stage">
                    <span
                        style="font-size: 0.85rem; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.5rem; color: #94a3b8;">MATHLEAF
                        · KELAS VII</span>
                    <div style="font-size: 2.5rem; font-weight: 700; color: #ffffff;">−3 < 0 < 5</div>
                            <p style="color: #cbd5e1; font-size: 0.9rem; margin-top: 0.5rem;">Bilangan negatif berada di
                                sebelah kiri nol.</p>
                            <button class="play-btn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="6 3 20 12 6 21 6 3"></polygon>
                                </svg>
                                Putar Pelajaran
                            </button>
                            @if ($isTeacher)
                                <button
                                    style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.2); color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; font-size: 0.8rem; display: flex; align-items: center; gap: 0.4rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Ganti Video
                                </button>
                            @endif
                    </div>
                    <div class="panel-body">
                        <h2>1. Mengenal Bilangan Bulat</h2>
                        <p>Kenali bilangan bulat, posisi pada garis bilangan, serta penerapannya pada suhu dan transaksi
                            sehari-hari. Pelajari bagaimana bilangan negatif dan positif berinteraksi.</p>
                    </div>
                </div>

                <!-- Konten E-Modul (Hidden by default) -->
                <div class="main-panel" id="panel-modul" style="display: none;">
                    <div class="modul-content">
                        <span
                            style="display: inline-block; background: var(--ml-green-light); color: var(--ml-green); padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; margin-bottom: 1rem;">E-Modul
                            Digital · Bab 1</span>
                        <h2 style="font-size: 1.5rem; color: #0f172a; margin: 0 0 1rem 0;">Bilangan Bulat</h2>

                        <h3>A. Mengenal bilangan</h3>
                        <p>Bilangan bulat terdiri dari bilangan negatif, nol, dan bilangan positif. Contoh: −3, −2, −1, 0,
                            1, 2, 3. Pada garis bilangan, semakin ke kanan nilainya semakin besar.</p>

                        <h3>B. Operasi bilangan bulat</h3>
                        <p>Penjumlahan: −3 + 5 = 2. Pengurangan: 4 − (−2) = 6. Perkalian dan pembagian dua bilangan bertanda
                            sama menghasilkan positif; tanda berbeda menghasilkan negatif.</p>

                        @if ($isTeacher)
                            <button class="btn-outline" style="width: auto; margin-top: 1rem;">Edit E-Modul</button>
                        @else
                            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px dashed #e2e8f0;">
                                <p style="font-weight: 600; color: #0f172a; margin-bottom: 1rem;">Sudah paham materinya?
                                    Yuk, uji pemahamanmu!</p>
                                <a href="{{ url('/latihan') }}" class="btn-primary" style="width: max-content;">
                                    Lanjut ke E-LKPD
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Pertanyaan Refleksi -->
                <div class="question-box">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                            </path>
                        </svg>
                        Rasa ingin tahumu berharga
                    </h3>
                    <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 1.5rem;">Tuliskan minimal dua pertanyaan
                        setelah menonton pelajaran.</p>

                    <form action="#">
                        <div class="form-group">
                            <label>Pertanyaan pertama</label>
                            <textarea placeholder="Apa yang ingin kamu pahami lebih jauh?" {{ $isTeacher ? 'readonly' : 'required' }}></textarea>
                        </div>
                        <div class="form-group">
                            <label>Pertanyaan kedua</label>
                            <textarea placeholder="Adakah contoh lain yang membuatmu penasaran?" {{ $isTeacher ? 'readonly' : 'required' }}></textarea>
                        </div>
                        @if (!$isTeacher)
                            <button type="submit" class="btn-primary"
                                style="width: auto; background: #0f172a; border-color: #0f172a;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Tandai Selesai
                            </button>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Daftar Pelajaran & Progress -->
            <aside class="side-panel">
                <h3>Daftar Pelajaran</h3>

                <!-- Progress Bar -->
                <div style="margin-bottom: 1.5rem;">
                    <div
                        style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.4rem;">
                        <span>{{ $isTeacher ? 'Rata-rata Progres Kelas' : 'Progres Materi' }}</span>
                        <span style="color: var(--ml-green);">75%</span>
                    </div>
                    <div style="background: #e2e8f0; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: var(--ml-green); height: 100%; width: 75%;"></div>
                    </div>
                </div>

                @if ($isTeacher)
                    <!-- TOMBOL CEK PROGRES KHUSUS GURU -->
                    <button class="btn-primary" style="margin-bottom: 1.5rem; background: #0f172a;"
                        onclick="alert('Route Detail Progres Siswa belum dibuat')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        Cek Detail Progres Siswa
                    </button>
                @endif

                <div class="lesson-list">
                    <div class="lesson-item active">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 0.2rem;">
                            <polygon points="6 3 20 12 6 21 6 3"></polygon>
                        </svg>
                        <div>
                            <div class="lesson-title">1. Mengenal Bilangan Bulat</div>
                            <div class="lesson-meta">8 menit · Video</div>
                        </div>
                    </div>
                    <div class="lesson-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 0.2rem;">
                            <polygon points="6 3 20 12 6 21 6 3"></polygon>
                        </svg>
                        <div>
                            <div class="lesson-title">2. Membandingkan Bilangan</div>
                            <div class="lesson-meta">7 menit · Video</div>
                        </div>
                    </div>
                    <div class="lesson-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 0.2rem;">
                            <polygon points="6 3 20 12 6 21 6 3"></polygon>
                        </svg>
                        <div>
                            <div class="lesson-title">3. Garis Bilangan</div>
                            <div class="lesson-meta">9 menit · Video</div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-outline" style="margin-top: 1.5rem;" onclick="switchTab('modul')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 7v14"></path>
                        <path
                            d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z">
                        </path>
                    </svg>
                    Buka E-Modul Bacaan
                </button>
            </aside>
        </div>

        <script>
            // Logika sederhana untuk berpindah Tab Video / Modul
            function switchTab(tab) {
                const panelVideo = document.getElementById('panel-video');
                const panelModul = document.getElementById('panel-modul');
                const tabVideo = document.getElementById('tab-video');
                const tabModul = document.getElementById('tab-modul');

                if (tab === 'video') {
                    panelVideo.style.display = 'block';
                    panelModul.style.display = 'none';
                    tabVideo.classList.add('selected');
                    tabModul.classList.remove('selected');
                } else {
                    panelVideo.style.display = 'none';
                    panelModul.style.display = 'block';
                    tabModul.classList.add('selected');
                    tabVideo.classList.remove('selected');
                }
            }
        </script>
    @endsection

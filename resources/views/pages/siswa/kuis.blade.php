@extends('layouts.app')

@section('title', 'Kuis & Evaluasi | MathLeaf')
@section('header_title', 'Kuis & Evaluasi')

@section('content')
    @php
        $userName = Auth::user()->name ?? 'Karin';
    @endphp

    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-border: #bbf0cb;
        }

        body {
            font-size: 16px;
            background-color: #f8fafc;
        }

        .page-heading {
            margin-bottom: 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-heading h1 {
            font-size: 2.2rem;
            color: #0f172a;
            margin: 0.5rem 0;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .page-heading p {
            color: #475569;
            font-size: 1.1rem;
            margin: 0;
            line-height: 1.6;
        }

        /* TAB FILTER */
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: #ffffff;
            padding: 0.5rem;
            border-radius: 10px;
            width: max-content;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        .tab-btn {
            background: transparent;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 1rem;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab-btn.selected {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .badge.green {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .badge.orange {
            background: #ffedd5;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }

        .badge.gray {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .badge.blue {
            background: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }

        /* QUIZ GRID (Modern Cards) */
        .quiz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .quiz-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.03);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .quiz-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(15, 23, 42, 0.06);
            border-color: #cbd5e1;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px dashed #e2e8f0;
            position: relative;
        }

        .card-header h3 {
            margin: 0 0 0.5rem 0;
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.4;
        }

        .quiz-type-icon {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-ujian {
            background: #fee2e2;
            color: #dc2626;
        }

        .icon-latihan {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .icon-kuis {
            background: #e0f2fe;
            color: #0284c7;
        }

        .card-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .quiz-meta {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #475569;
            font-size: 0.95rem;
        }

        .meta-item svg {
            color: #94a3b8;
        }

        .card-footer {
            padding: 1.25rem 1.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            margin-top: auto;
        }

        .btn-action {
            width: 100%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border: none;
        }

        .btn-start {
            background: var(--ml-green);
            color: white;
        }

        .btn-start:hover {
            background: var(--ml-green-hover);
        }

        .btn-view {
            background: #ffffff;
            color: var(--ml-green);
            border: 1px solid var(--ml-green-border);
        }

        .btn-view:hover {
            background: var(--ml-green-light);
        }

        .btn-disabled {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: #0f172a;
            margin: 1rem 0 0.5rem;
        }

        .empty-state p {
            color: #64748b;
            font-size: 1.05rem;
        }

        /* Animasi Tab */
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow"
                style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #086534; font-size: 0.9rem; letter-spacing: 0.05em; text-transform: uppercase;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="6"></circle>
                    <path d="m8 13-2 8 6-3 6 3-2-8"></path>
                </svg>
                RUANG BERTUMBUHMU
            </span>
            <h1>Saatnya Uji Pemahamanmu, {{ $userName }}!</h1>
            <p>Bukan tentang sempurna, tapi tentang sejauh mana kamu sudah bertumbuh.</p>
        </div>
    </div>

    <div class="filter-tabs">
        <button type="button" class="tab-btn selected" id="tab-btn-tersedia" onclick="switchTab('tersedia')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            Kuis Tersedia
            <!-- Misal ada 2 kuis baru (Angka dinamis nantinya) -->
            <span
                style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">2</span>
        </button>
        <button type="button" class="tab-btn" id="tab-btn-selesai" onclick="switchTab('selesai')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
            Riwayat Pengerjaan
        </button>
    </div>

    <!-- TAB 1: KUIS TERSEDIA (BELUM DIKERJAKAN) -->
    <div id="tab-tersedia" class="tab-content active">
        <div class="quiz-grid">

            <!-- CARD DUMMY: MODE UJIAN (Fullscreen) -->
            <div class="quiz-card">
                <div class="card-header">
                    <span class="badge orange" style="margin-bottom: 0.8rem;">Evaluasi Tengah Semester</span>
                    <h3 style="padding-right: 3rem;">Ujian Bilangan Bulat & Pecahan</h3>
                    <div class="quiz-type-icon icon-ujian" title="Mode Ujian (Layar Penuh)">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="3" x2="9" y2="21"></line>
                        </svg>
                    </div>
                </div>
                <div class="card-body">
                    <p style="margin:0; color:#475569; font-size:0.95rem; line-height:1.5;">Ujian komprehensif Bab 1.
                        Membutuhkan konsentrasi penuh.</p>
                    <div class="quiz-meta" style="margin-top: 0.5rem;">
                        <div class="meta-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            Durasi: 60 Menit
                        </div>
                        <div class="meta-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            Jumlah: 20 Soal
                        </div>
                        <div class="meta-item" style="color: #ef4444;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Mode: Ujian Ketat (Fullscreen)
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <!-- LINK MENUJU PREVIEW-KUIS -->
                    <!-- Asumsi rute untuk memulai kuis adalah 'siswa.quizzes.start' dengan ID kuis -->
                    <a href="{{ url('/siswa/kuis/1/kerjakan') }}" class="btn-action btn-start">
                        Mulai Ujian
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- CARD DUMMY: MODE LATIHAN BIASA -->
            <div class="quiz-card">
                <div class="card-header">
                    <span class="badge blue" style="margin-bottom: 0.8rem;">Latihan Harian</span>
                    <h3 style="padding-right: 3rem;">Latihan: Operasi Hitung Campuran</h3>
                    <div class="quiz-type-icon icon-latihan" title="Mode Latihan Santai">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20V10"></path>
                            <path d="M18 20V4"></path>
                            <path d="M6 20v-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="card-body">
                    <p style="margin:0; color:#475569; font-size:0.95rem; line-height:1.5;">Pertajam insting berhitungmu
                        dengan latihan ringan ini.</p>
                    <div class="quiz-meta" style="margin-top: 0.5rem;">
                        <div class="meta-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            Durasi: 30 Menit
                        </div>
                        <div class="meta-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            Jumlah: 10 Soal
                        </div>
                        <div class="meta-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Mode: Latihan Bebas
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <!-- LINK MENUJU PREVIEW-KUIS -->
                    <a href="{{ url('/siswa/kuis/2/kerjakan') }}" class="btn-action btn-start"
                        style="background: #0ea5e9;">
                        Mulai Latihan
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- TAB 2: RIWAYAT PENGERJAAN (KUIS SELESAI) -->
    <div id="tab-selesai" class="tab-content">
        <div class="quiz-grid">

            <!-- CARD DUMMY: KUIS YANG SUDAH DIKERJAKAN -->
            <div class="quiz-card" style="opacity: 0.9;">
                <div class="card-header" style="background: #f8fafc;">
                    <span class="badge green" style="margin-bottom: 0.8rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Selesai Dikerjakan
                    </span>
                    <h3>Kuis: Pengenalan Garis Bilangan</h3>
                </div>
                <div class="card-body">
                    <div style="text-align: center; margin: 1rem 0;">
                        <span
                            style="display: block; color: #64748b; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Skor
                            Akhir</span>
                        <strong style="font-size: 3rem; color: var(--ml-green); line-height: 1;">85</strong>
                        <span style="font-size: 1.2rem; color: #94a3b8;">/ 100</span>
                    </div>
                    <div class="quiz-meta" style="background: #f1f5f9; padding: 1rem; border-radius: 8px;">
                        <div class="meta-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Dikerjakan: 12 Okt 2026, 14:30
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ url('/rapor') }}" class="btn-action btn-view">
                        Lihat Rapor Detail
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Hapus status active/selected dari semua tab
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('selected'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Tambahkan status active ke tab yang diklik
            document.getElementById('tab-btn-' + tabId).classList.add('selected');
            document.getElementById('tab-' + tabId).classList.add('active');
        }
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Materi Pembelajaran | MathLeaf')
@section('header_title', 'Materi Pembelajaran')

@section('content')
    @php
        $isTeacher = Auth::check() && Auth::user()->role === 'guru';
    @endphp

    <style>
        /* Styling tambahan untuk menyesuaikan layout baru tanpa mengubah CSS bawaan */
        .page-heading {
            margin-bottom: 2rem;
        }

        .page-heading h1 {
            font-size: 1.8rem;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .page-heading p {
            color: #64748b;
            font-size: 1rem;
        }

        /* Penyesuaian Filter & Search Bar */
        .filters-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .filter-tabs .btn-ghost {
            white-space: nowrap;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            color: #475569;
            font-weight: 600;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .filter-tabs .btn-ghost:hover {
            background: #f1f5f9;
        }

        .filter-tabs .btn-ghost.selected {
            background: #0f172a;
            color: #ffffff;
        }

        /* Search Bar Besar & Tombol Aksi */
        .search-action-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex: 1;
            max-width: 600px;
            /* Search bar dibuat lebih besar */
        }

        .search-input-large {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem 1.2rem;
            flex: 1;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-input-large:focus-within {
            border-color: #086534;
            box-shadow: 0 0 0 3px rgba(8, 101, 52, 0.1);
        }

        .search-input-large input {
            border: none;
            background: transparent;
            width: 100%;
            font-size: 1rem;
            color: #0f172a;
            outline: none;
        }

        .search-input-large svg {
            color: #64748b;
        }

        .btn-add-material {
            background-color: #086534;
            color: #ffffff;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s;
            white-space: nowrap;
        }

        .btn-add-material:hover {
            background-color: #064d27;
        }

        /* Modifikasi kecil kartu materi untuk tombol guru */
        .material-item {
            position: relative;
        }

        .teacher-card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed #e2e8f0;
        }

        .btn-small-outline {
            flex: 1;
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 0.4rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
        }

        .btn-small-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow"
                style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #086534; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.5rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"></path>
                    <path d="M4 21c0-6 5-11 11-14"></path>
                </svg>
                {{ $isTeacher ? 'RUANG KELOLA GURU' : 'RUANG BERTUMBUHMU' }}
            </span>
            <h1>{{ $isTeacher ? 'Kelola Materi Pembelajaran' : 'Jelajahi Materi Pembelajaran' }}</h1>
            <p>Dari mengenal bilangan sampai memecahkan masalah sehari-hari.</p>
        </div>
    </div>

    <div class="filters-wrapper">
        <!-- Filter Tabs (Ditambah Selesai Dipelajari) -->
        <div class="filter-tabs">
            <button type="button" data-material-tab="Semua Materi" class="btn-ghost selected">Semua Materi</button>
            <button type="button" data-material-tab="Sedang Dipelajari" class="btn-ghost">Sedang Dipelajari</button>
            <button type="button" data-material-tab="Belum Dimulai" class="btn-ghost">Belum Dimulai</button>
            <button type="button" data-material-tab="Selesai Dipelajari" class="btn-ghost">Selesai Dipelajari</button>
        </div>

        <!-- Search Bar & Tombol Tambah Materi -->
        <div class="search-action-group">
            <label class="search-input-large">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input id="material-search" aria-label="Cari materi" placeholder="Cari modul atau video materi..." />
            </label>

            @if ($isTeacher)
                <button type="button" class="btn-add-material" onclick="alert('Route Tambah Materi belum dibuat')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Materi
                </button>
            @endif
        </div>
    </div>

    <div class="material-grid" id="material-grid">

        @php
            // Array warna tema asli untuk dilooping
            $tones = ['tone-mint', 'tone-yellow', 'tone-peach'];

            // Dummy data jika $materials belum dipassing dari controller
            $dummyMaterials = [
                [
                    'title' => 'Bilangan Bulat',
                    'desc' => 'Kenali bilangan positif, negatif, dan nol dalam kehidupan sehari-hari.',
                    'symbol' => '−3 + 5',
                    'progress' => 65,
                ],
                [
                    'title' => 'Penjumlahan & Pengurangan',
                    'desc' => 'Jelajahi operasi bilangan bulat dengan bantuan garis bilangan.',
                    'symbol' => '7 − (−2)',
                    'progress' => 40,
                ],
                [
                    'title' => 'Perkalian & Pembagian',
                    'desc' => 'Temukan pola tanda dan selesaikan operasi dengan percaya diri.',
                    'symbol' => '−4 × 3',
                    'progress' => 0,
                ],
            ];

            $materiList = $materials ?? $dummyMaterials;
        @endphp

        @forelse($materiList as $index => $materi)
            @php
                // Mengambil warna secara bergantian (mint, yellow, peach)
                $colorClass = $tones[$index % count($tones)];
                $title = is_array($materi) ? $materi['title'] : $materi->title;
                $desc = is_array($materi) ? $materi['desc'] : $materi->description;
                $symbol = is_array($materi) ? $materi['symbol'] : 'x + y';
                $progress = is_array($materi) ? $materi['progress'] : $materi->progress_percent ?? 0;
            @endphp

            <div class="material-item">
                <article class="material-card" data-material-title="{{ strtolower($title) }}"
                    data-material-progress="{{ $progress }}">

                    <!-- Gambar Art Berwarna Warni -->
                    <a href="{{ $isTeacher ? '#' : url('/siswa/video') }}" class="material-art {{ $colorClass }}">
                        <span class="art-leaf">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"></path>
                                <path d="M4 21c0-6 5-11 11-14"></path>
                            </svg>
                        </span>
                        <span class="math-symbol">{{ $symbol }}</span>
                        <span class="art-squares">□</span>
                        <span class="material-label">Modul & Video</span>
                    </a>

                    <div class="material-body">
                        <div class="material-meta">
                            <span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="5" width="15" height="14" rx="2"></rect>
                                    <path d="m17 10 5-3v10l-5-3"></path>
                                </svg>
                                4 video
                            </span>
                            <span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 7v5l3 2"></path>
                                </svg>
                                30 menit
                            </span>
                        </div>

                        <h3>{{ $title }}</h3>
                        <p>{{ $desc }}</p>

                        <div class="material-bottom">
                            <span>Kelas VII SMP</span>
                            <a href="{{ $isTeacher ? '#' : url('/siswa/video') }}"
                                style="{{ $isTeacher ? 'pointer-events: none; color: #94a3b8;' : '' }}">
                                {{ $isTeacher ? 'Pratinjau Materi' : 'Mulai Belajar' }}
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M7 17 17 7"></path>
                                    <path d="M7 7h10v10"></path>
                                </svg>
                            </a>
                        </div>

                        @if ($isTeacher)
                            <!-- Tombol Tambahan Khusus Guru (Edit / Hapus) -->
                            <div class="teacher-card-actions">
                                <button type="button" class="btn-small-outline"
                                    onclick="alert('Route Edit belum dibuat')">Edit</button>
                                <button type="button" class="btn-small-outline"
                                    style="color: #ef4444; border-color: #fca5a5;"
                                    onclick="alert('Route Hapus belum dibuat')">Hapus</button>
                            </div>
                        @endif
                    </div>
                </article>

                <!-- Indikator Progres Dinamis (Progres Kelas vs Progres Belajar) -->
                <div class="progress-label">
                    <span>{{ $isTeacher ? 'Progres kelas' : 'Progres belajar' }}</span>
                    <strong>{{ $progress }}%</strong>
                </div>
                <div class="progress-track">
                    <div style="width: {{ $progress }}%"></div>
                </div>
            </div>
        @empty
            <div class="empty-state" id="no-material">
                Materi tidak ditemukan.
            </div>
        @endforelse
    </div>
@endsection

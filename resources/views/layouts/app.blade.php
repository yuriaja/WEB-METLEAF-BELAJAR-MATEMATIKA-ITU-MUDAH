<!doctype html>
<html lang="id">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title', 'MathLeaf')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="stylesheet" href="{{ asset('css/mathleaf.css') }}" />
    <script defer src="{{ asset('js/mathleaf.js') }}"></script>
    <style>
        /* Styling tambahan untuk dropdown di sidebar dan banner notifikasi */
        .sidebar-dropdown-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .sidebar-dropdown-wrapper .icon-left {
            position: absolute;
            left: 12px;
            color: #64748b;
            pointer-events: none;
        }

        .sidebar-dropdown-wrapper .icon-right {
            position: absolute;
            right: 12px;
            color: #64748b;
            pointer-events: none;
        }

        .sidebar-select {
            width: 100%;
            padding: 10px 30px 10px 38px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            font-size: 0.85rem;
            font-weight: 600;
            color: #0f172a;
            cursor: pointer;
            outline: none;
            appearance: none;
            transition: border-color 0.2s;
        }

        .sidebar-select:focus {
            border-color: #086534;
        }

        .alert-no-class {
            background-color: #fef2f2;
            border: 1px solid #f87171;
            color: #991b1b;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 500;
        }
    </style>
</head>

<body data-page="dashboard">
    <div class="app-layout" id="app-layout">

        <!-- MENGAMBIL DATA KELAS UNTUK DROPDOWN SIDEBAR -->
        @php
            $activeClassId = session('kelas_aktif_id');
            $userClasses = collect(); // Default koleksi kosong

            if (Auth::check()) {
                if (Auth::user()->role === 'guru') {
                    $userClasses = \Illuminate\Support\Facades\DB::table('classes')
                        ->where('teacher_id', Auth::id())
                        ->select('id', 'name')
                        ->get();
                } else {
                    $userClasses = \Illuminate\Support\Facades\DB::table('classes')
                        ->join('class_user', 'classes.id', '=', 'class_user.class_id')
                        ->where('class_user.user_id', Auth::id())
                        ->select('classes.id', 'classes.name')
                        ->get();
                }
            }
        @endphp

        <!-- SIDEBAR -->
        <aside class="app-sidebar">
            <a href="{{ url('/') }}" class="logo logo-light">
                <span class="logo-mark">
                    <svg aria-hidden="true" width="23" height="23" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
                        <path d="M4 21c0-6 5-11 11-14" />
                    </svg>
                </span>
                <span>Math<span class="logo-leaf">Leaf</span>
                    <small>
                        @if (Auth::check() && Auth::user()->role === 'guru')
                            RUANG GURU
                        @else
                            GROW YOUR MATH SKILLS
                        @endif
                    </small>
                </span>
            </a>

            <!-- PENGGANTI DIV SIDEBAR CLASS MENJADI DROPDOWN -->
            <div class="sidebar-class" style="padding: 10px 20px;">
                <form action="{{ route('classroom.active') }}" method="POST" style="width: 100%; margin: 0;">
                    @csrf
                    <div class="sidebar-dropdown-wrapper">
                        <!-- Ikon Daun/Kelas Kiri -->
                        <svg class="icon-left" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M7 20h10" />
                            <path d="M10 20c5.5-2.5.8-8.3 5-11" />
                            <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
                            <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" />
                        </svg>

                        <select name="kelas_id" class="sidebar-select" onchange="this.form.submit()">
                            @if ($userClasses->isEmpty())
                                <option value="">Belum Ada Kelas</option>
                            @else
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($userClasses as $kelas)
                                    <option value="{{ $kelas->id }}"
                                        {{ $activeClassId == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        <!-- Ikon Panah Kanan -->
                        <svg class="icon-right" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                </form>
            </div>

            @if (Auth::check() && Auth::user()->role === 'guru')
                <!-- MENU GURU -->
                <span class="sidebar-caption">MANAJEMEN KELAS</span>
                <nav>
                    <a href="{{ route('teacher.dashboard') }}"
                        class="{{ request()->routeIs('teacher.dashboard') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="m2 10 10-5 10 5-10 5-10-5Z" />
                            <path d="M6 12v5c3 2 9 2 12 0v-5M22 10v7" />
                        </svg>
                        <span>Dashboard Guru</span>
                    </a>
                    <a href="{{ route('classroom.home') }}"
                        class="{{ request()->is('kelas') || request()->is('kelas/buat') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M12 7v14" />
                            <path
                                d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z" />
                        </svg>
                        <span>Manajemen Kelas</span>
                    </a>
                    <a href="{{ url('/materi') }}" class="{{ request()->is('materi*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <polygon points="6 3 20 12 6 21 6 3" />
                        </svg>
                        <span>Kelola Materi</span>
                    </a>
                    <a href="{{ route('teacher.quizzes') }}"
                        class="{{ request()->routeIs('teacher.quizzes') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <circle cx="12" cy="8" r="6" />
                            <path d="m8 13-2 8 6-3 6 3-2-8" />
                        </svg>
                        <span>Kelola Kuis</span>
                    </a>
                    <!-- TAMBAHAN: MENU KELOLA TUGAS GURU -->
                    <a href="{{ route('class-tasks.index') }}"
                        class="{{ request()->is('kelas/tugas*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                        <span>Pengumpulan Tugas</span>
                    </a>
                    <a href="{{ route('teacher.grades') }}"
                        class="{{ request()->is('guru/rekap-nilai*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <span>Rekap Nilai Siswa</span>
                    </a>
                    <a href="{{ url('/forum') }}" class="{{ request()->is('forum*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                        </svg>
                        <span>Forum Diskusi</span>
                    </a>
                </nav>
            @else
                <!-- MENU SISWA -->
                <span class="sidebar-caption">RUANG BELAJAR</span>
                <nav>
                    <a href="{{ url('/siswa/dashboard') }}"
                        class="{{ request()->is('siswa/dashboard') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="m2 10 10-5 10 5-10 5-10-5Z" />
                            <path d="M6 12v5c3 2 9 2 12 0v-5M22 10v7" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <!-- TAMBAHAN: Menu Gabung Kelas Untuk Siswa -->
                    <a href="{{ route('classroom.home') }}" class="{{ request()->is('kelas*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="19" y1="8" x2="19" y2="14" />
                            <line x1="22" y1="11" x2="16" y2="11" />
                        </svg>
                        <span>Kelas Saya</span>
                    </a>
                    <a href="{{ url('/materi') }}" class="{{ request()->is('materi*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M12 7v14" />
                            <path
                                d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z" />
                        </svg>
                        <span>Materi Pembelajaran</span>
                    </a>
                    <a href="{{ url('/siswa/video') }}"
                        class="{{ request()->is('siswa/video*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <polygon points="6 3 20 12 6 21 6 3" />
                        </svg>
                        <span>Video Pembelajaran</span>
                    </a>
                    <a href="{{ url('/siswa/latihan') }}"
                        class="{{ request()->is('siswa/latihan*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                        <span>E-LKPD Interaktif</span>
                    </a>
                    <a href="{{ url('/kuis') }}" class="{{ request()->is('kuis*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <circle cx="12" cy="8" r="6" />
                            <path d="m8 13-2 8 6-3 6 3-2-8" />
                        </svg>
                        <span>Kuis & Evaluasi</span>
                    </a>
                    <a href="{{ url('/forum') }}" class="{{ request()->is('forum*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                        </svg>
                        <span>Forum Diskusi</span>
                    </a>
                    <a href="{{ url('/tugas') }}" class="{{ request()->is('tugas*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                        </svg>
                        <span>Tugas & Kelompok</span>
                    </a>
                    <a href="{{ url('/nilai') }}" class="{{ request()->is('nilai*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M2 20h20" />
                            <path d="M5 20v-5" />
                            <path d="M12 20v-9" />
                            <path d="M19 20v-13" />
                        </svg>
                        <span>Nilai & Progres</span>
                    </a>
                </nav>
            @endif

            <div class="sidebar-bottom">
                <div class="sidebar-motivation">
                    <b>Tetap Semangat!</b>
                    <p>Setiap langkah kecil membawa pemahaman besar.</p>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="width: 100%;">
                    @csrf
                    <button type="submit" class="btn btn-ghost"
                        style="color: white; width: 100%; justify-content: flex-start; gap: 10px;">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                        Logout (Keluar)
                    </button>
                </form>
            </div>
        </aside>

        <!-- AREA KONTEN UTAMA & HEADER -->
        <div class="app-main">
            <header class="app-header">
                <div style="display: flex; align-items: center; gap: 12px">
                    <button type="button" class="btn btn-ghost" id="collapse-sidebar" aria-label="Ciutkan sidebar">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <strong>@yield('header_title', 'Dashboard')</strong>
                </div>

                <div style="display: flex; gap: 16px; align-items: center; margin-left: auto;">
                    <!-- Dropdown global dihapus dari sini karena sudah dipindah ke sidebar -->

                    <!-- Lonceng Notifikasi -->
                    <button type="button" class="btn btn-ghost" id="notification-button" aria-label="Notifikasi">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z" />
                            <path d="m5 17 .8 2.2L8 20l-2.2.8L5 23l-.8-2.2L2 20l2.2-.8L5 17Z" />
                            <path d="m19 2 .7 1.3L21 4l-1.3.7L19 6l-.7-1.3L17 4l1.3-.7L19 2Z" />
                        </svg>
                    </button>

                    <span class="header-profile">
                        <span class="avatar" id="avatar-letter">
                            {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'U' }}
                        </span>
                        <span>{{ Auth::check() ? Auth::user()->name : 'User' }}</span>
                    </span>
                </div>

                <div class="notification-panel" id="notification-panel" hidden>
                    <strong>Notifikasi MathLeaf</strong>
                    <p>Terus belajar, satu langkah kecil setiap hari 🌱</p>
                    <!-- Boleh dihapus emoji tanamannya kalau tidak suka -->
                </div>
            </header>

            <main class="app-content">
                <!-- NOTIFIKASI JIKA SISWA BELUM PUNYA KELAS -->
                @if (Auth::check() && Auth::user()->role === 'siswa' && $userClasses->isEmpty())
                    <div class="alert-no-class">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            <strong>Perhatian!</strong> Kamu belum bergabung dengan kelas manapun.
                            Silakan masuk ke halaman <a href="{{ route('classroom.home') }}"
                                style="color: #991b1b; text-decoration: underline;">Kelas Saya</a> dan masukkan kode
                            kelas dari guru kamu.
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

    </div>
</body>

</html>

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
    <script>
        try {
            if (localStorage.getItem('ml-sidebar-collapsed') === '1') {
                document.documentElement.classList.add('sb-collapsed');
            }
        } catch (e) {}
    </script>
    <style>
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

        /* --- LAYOUT SIDEBAR + KONTEN (ANTI-TUMPANG-TINDIH SAAT ZOOM) --- */
        :root {
            --sidebar-w: clamp(210px, 20vw, 250px);
            --sidebar-w-collapsed: 76px;
            /* Ganti jadi 0px kalau ingin sidebar menghilang total saat ditutup */
            --nav-fs: 12px;
            /* Ukuran font menu; dikecilkan otomatis oleh JS jika ada label yang tidak muat */
        }

        html.sb-collapsed {
            --sidebar-w: var(--sidebar-w-collapsed);
        }

        .app-layout {
            display: grid !important;
            /* minmax(0, 1fr) wajib: supaya kolom konten bisa mengecil dan tidak melebar melewati layar */
            grid-template-columns: var(--sidebar-w) minmax(0, 1fr) !important;
            align-items: start;
            min-height: 100vh;
            transition: grid-template-columns .2s ease;
            font-size: 12.8px;
        }

        .app-sidebar {
            /* Sidebar tetap ikut alur grid (bukan fixed/absolute) sehingga tidak menimpa konten */
            position: sticky !important;
            top: 0;
            left: auto !important;
            width: var(--sidebar-w) !important;
            min-width: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            transform: none !important;
            z-index: 60;
            transition: width .2s ease;
        }

        .app-main {
            grid-column: 2;
            min-width: 0 !important;
            width: 100%;
            margin-left: 0 !important;
        }

        .app-content {
            max-width: 100%;
            overflow-x: auto;
            font-size: 11.2px;
        }

        /* --- TIPOGRAFI SIDEBAR: SEMUA SERAGAM --- */
        .app-sidebar nav a,
        .app-sidebar .sidebar-bottom .btn {
            font-size: var(--nav-fs) !important;
            font-weight: 600 !important;
            line-height: 1.25 !important;
        }

        .app-sidebar nav a {
            padding: 0.8rem 1rem !important;
            gap: 12px !important;
        }

        .app-sidebar nav a svg {
            width: 20px !important;
            height: 20px !important;
            flex-shrink: 0;
        }

        /* Label menu selalu 1 baris; kalau tetap tidak muat, JS mengecilkan font semua label sama rata */
        .app-sidebar nav a>span {
            flex: 1 1 auto;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-caption {
            font-size: 0.58rem !important;
            font-weight: 700 !important;
            letter-spacing: .08em;
            margin-top: 0.5rem !important;
            padding-left: 1.25rem;
            white-space: nowrap;
        }

        .app-sidebar .sidebar-class>span {
            font-size: 0.76rem;
            font-weight: 600;
            line-height: 1.25;
            min-width: 0;
        }

        .app-sidebar .sidebar-class small {
            display: block;
            font-size: 0.6rem;
            font-weight: 400;
        }

        .app-sidebar .sidebar-motivation b {
            font-size: 0.72rem;
        }

        .app-sidebar .sidebar-motivation p {
            font-size: 0.64rem;
            margin: 4px 0 0;
        }

        /* --- LOGO: proporsi nama web & tagline --- */
        .app-sidebar {
            container-type: inline-size;
        }

        .app-sidebar .logo>span:not(.logo-mark) {
            font-size: clamp(1rem, 6.5cqw, 1.25rem);
            font-weight: 800;
            line-height: 1.1;
            white-space: nowrap;
            min-width: 0;
        }

        .app-sidebar .logo small.logo-tagline-teacher {
            font-size: clamp(8px, 3.6cqw, 10.5px);
        }

        .app-sidebar .logo small {
            display: block;
            font-size: clamp(7.2px, 3.28cqw, 9.6px);
            font-weight: 600;
            letter-spacing: .05em;
            margin-top: 4px;
            white-space: nowrap;
        }

        .app-sidebar .sidebar-class {
            margin-top: 18px;
        }

        /* --- MODE SIDEBAR DITUTUP (hanya ikon) --- */
        html.sb-collapsed .app-sidebar .sidebar-class-form,
        html.sb-collapsed .app-sidebar .sidebar-caption,
        html.sb-collapsed .app-sidebar .sidebar-motivation,
        html.sb-collapsed .app-sidebar nav a>span,
        html.sb-collapsed .app-sidebar .logo>span:not(.logo-mark) {
            display: none !important;
        }

        html.sb-collapsed .app-sidebar .logo {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
            margin-bottom: 1.5rem;
            /* jarak logo ke ikon Dashboard */
        }

        html.sb-collapsed .app-sidebar nav a {
            justify-content: center !important;
            padding: 0.8rem 0 !important;
            gap: 0 !important;
        }

        html.sb-collapsed .app-sidebar .sidebar-bottom .btn {
            font-size: 0 !important;
            justify-content: center !important;
            gap: 0 !important;
            padding-left: 0;
            padding-right: 0;
        }

        /* Zoom Chrome besar (viewport efektif sempit): sidebar jadi blok di atas, bukan menimpa konten */
        @media (max-width: 768px) {
            .app-layout {
                display: block !important;
            }

            .app-main {
                width: 100% !important;
                display: block !important;
            }

            .app-header {
                position: sticky !important;
                top: 0;
                z-index: 999 !important;
                background: #ffffff;
            }

            .app-sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100vh !important;
                padding-top: 70px !important;
                /* Memberi ruang untuk header di atasnya */
                z-index: 998 !important;
                /* Berada tepat satu lapis di bawah header */
                background-color: #086534 !important;
                transform: translateY(0) !important;
                opacity: 1 !important;
                visibility: visible !important;
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, visibility 0.4s !important;
                display: block !important;
            }

            html.sb-collapsed .app-sidebar {
                transform: translateY(-100%) !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }

            /* Paksa teks dan menu tetap tampil utuh di mobile */
            .app-sidebar nav a>span,
            .app-sidebar .sidebar-caption,
            .app-sidebar .sidebar-motivation,
            .app-sidebar .sidebar-class-form,
            .app-sidebar .logo>span {
                display: block !important;
            }

            .app-sidebar nav a {
                justify-content: flex-start !important;
                padding: 12px 20px !important;
                gap: 12px !important;
            }

            .app-sidebar .sidebar-bottom .btn {
                font-size: var(--nav-fs) !important;
                justify-content: flex-start !important;
                gap: 10px !important;
            }
        }

        .app-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }

        .app-header > div:first-child > strong {
            font-size: 0.8rem;
        }

        .app-content {
            padding-top: 2rem;
            /* Memberikan ruang agar konten tidak tertindih */
        }

        .app-content h1 {
            font-size: 1.6rem !important;
        }

        .app-content h2 {
            font-size: 1.12rem !important;
        }

        .app-content h3 {
            font-size: 1.04rem !important;
        }

        .app-content h4 {
            font-size: 0.88rem !important;
        }

        .app-content .kl-header p,
        .app-content .page-heading .class-info {
            font-size: 0.88rem !important;
        }
        /* MATHLEAF_SIDEBAR_SPACING_FIX_V1
           Samakan posisi konten dengan lebar sidebar yang tampil. */
        @media (min-width: 1101px) {
            .app-layout .app-main { margin-left: 280px; }
            .app-layout .app-content { padding: 32px; }
            .app-layout.sidebar-collapsed .app-sidebar { width: 76px !important; }
            .app-layout.sidebar-collapsed .app-main { margin-left: 76px; }
            .app-layout.sidebar-collapsed .app-sidebar nav a { padding: 12px !important; justify-content: center; }
        }

        @media (min-width: 801px) and (max-width: 1100px) {
            .app-layout .app-sidebar { width: 215px !important; }
            .app-layout .app-main { margin-left: 215px; }
            .app-layout .app-content { padding: 28px; }
            .app-layout.sidebar-collapsed .app-sidebar { width: 76px !important; }
            .app-layout.sidebar-collapsed .app-main { margin-left: 76px; }
            .app-layout.sidebar-collapsed .app-sidebar nav a { padding: 12px !important; justify-content: center; }
        }

        @media (min-width: 561px) and (max-width: 800px) {
            .app-layout .app-sidebar { width: 76px !important; }
            .app-layout .app-main { margin-left: 76px; }
            .app-layout .app-content { padding: 24px 20px; }
            .app-layout .app-sidebar nav a { padding: 12px !important; justify-content: center; }
            .app-layout.sidebar-collapsed .app-sidebar { width: 215px !important; }
            .app-layout.sidebar-collapsed .app-main { margin-left: 76px; }
            .app-layout.sidebar-collapsed .app-sidebar nav a { padding: 12px 14px !important; justify-content: flex-start; }
        }

        @media (max-width: 560px) {
            .app-layout .app-sidebar { width: 62px !important; }
            .app-layout .app-main,
            .app-layout.sidebar-collapsed .app-main { margin-left: 62px; }
            .app-layout .app-content { padding: 20px 16px; }
            .app-layout .app-sidebar nav a { padding: 12px !important; justify-content: center; }
            .app-layout.sidebar-collapsed .app-sidebar { width: 215px !important; }
            .app-layout.sidebar-collapsed .app-sidebar nav a { padding: 12px 14px !important; justify-content: flex-start; }
        }

    </style>
</head>

<body data-page="dashboard">
    <div class="app-layout" id="app-layout">

        <!-- LOGIKA PENGAMBILAN DATA KELAS -->
        @php
            $activeClassId = session('kelas_aktif_id');
            $userClasses = collect();

            if (Auth::check()) {
                if (Auth::user()->role === 'guru') {
                    $userClasses = \Illuminate\Support\Facades\DB::table('classes')
                        ->where('teacher_id', Auth::id())
                        ->select('id', 'name', 'school_year')
                        ->get();
                } else {
                    $userClasses = \Illuminate\Support\Facades\DB::table('classes')
                        ->join('class_user', 'classes.id', '=', 'class_user.class_id')
                        ->join('users', 'classes.teacher_id', '=', 'users.id')
                        ->where('class_user.user_id', Auth::id())
                        ->select('classes.id', 'classes.name', 'classes.school_year', 'users.name as teacher_name')
                        ->get();
                }
            }

            // Penentuan Data Kelas Aktif (Semua Kelas sbg Default)
            $activeClass = $userClasses->firstWhere('id', $activeClassId);
            $showSubtitle = false;

            if ($activeClass) {
                $activeName = $activeClass->name;
                $activeTeacher =
                    Auth::user()->role === 'guru' ? Auth::user()->name : $activeClass->teacher_name ?? 'Guru';
                $activeYear = $activeClass->school_year ?? '-';
                $showSubtitle = true;
            } else {
                $activeName = $userClasses->isEmpty() ? 'Belum Ada Kelas' : 'Semua Kelas';
            }

            $dashboardRoute = url('/');
            if (Auth::check()) {
                $dashboardRoute = Auth::user()->role === 'guru' ? route('teacher.dashboard') : url('/dashboard');
            }
        @endphp

        <aside class="app-sidebar">
            <a href="{{ $dashboardRoute }}" class="logo logo-light">
                <span class="logo-mark">
                    <svg aria-hidden="true" width="23" height="23" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
                        <path d="M4 21c0-6 5-11 11-14" />
                    </svg>
                </span>
                <span>Math<span class="logo-leaf">Leaf</span>
                    <small class="{{ Auth::check() && Auth::user()->role === 'guru' ? 'logo-tagline-teacher' : '' }}">
                        @if (Auth::check() && Auth::user()->role === 'guru')
                            RUANG GURU
                        @else
                            GROW YOUR MATH SKILLS
                        @endif
                    </small>
                </span>
            </a>

            <!-- DROPDOWN KELAS: DESAIN MURNI DARI mathleaf.css -->
            <form action="{{ route('classroom.active') }}" method="POST" class="sidebar-class-form"
                style="margin: 0; padding: 0 20px 0 20px;">
                @csrf
                <div class="sidebar-class" style="position: relative;">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M7 20h10" />
                        <path d="M10 20c5.5-2.5.8-8.3 5-11" />
                        <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
                        <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" />
                    </svg>

                    <span>
                        {{ $activeName }}
                        @if ($showSubtitle && !$userClasses->isEmpty())
                            @if (Auth::user()->role === 'guru')
                                <small>Semester Ganjil · {{ $activeYear }}</small>
                            @else
                                <small>{{ $activeTeacher }} · {{ $activeYear }}</small>
                            @endif
                        @endif
                    </span>

                    @if (!$userClasses->isEmpty())
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="margin-left: auto; opacity: 0.7;">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>

                        <!-- Invisible Select Input untuk Fungsionalitas Dropdown -->
                        <select name="kelas_id" onchange="this.form.submit()"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; appearance: none;">
                            <option value="">Semua Kelas</option>
                            @foreach ($userClasses as $kelas)
                                @php
                                    $namaGuru =
                                        Auth::user()->role === 'guru'
                                            ? Auth::user()->name
                                            : $kelas->teacher_name ?? 'Guru';
                                    $tahunAjar = $kelas->school_year ?? '-';
                                @endphp
                                <option value="{{ $kelas->id }}"
                                    {{ $activeClassId == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->name }}
                                    ({{ Auth::user()->role === 'guru' ? $tahunAjar : $namaGuru . ' - ' . $tahunAjar }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </form>

            @if (Auth::check() && Auth::user()->role === 'guru')
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
                    <a href="{{ route('classroom.tasks.index') }}"
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
                <span class="sidebar-caption">RUANG BELAJAR</span>
                <nav>
                    <a href="{{ url('/dashboard') }}"
                        class="{{ request()->is('dashboard') || request()->is('siswa/dashboard') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="m2 10 10-5 10 5-10 5-10-5Z" />
                            <path d="M6 12v5c3 2 9 2 12 0v-5M22 10v7" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
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
                    <a href="{{ url('/materi') }}"
                        class="{{ request()->is('materi*') || request()->is('video*') || request()->is('latihan*') || request()->is('siswa/video*') || request()->is('siswa/latihan*') ? 'selected' : '' }}">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M12 7v14" />
                            <path
                                d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z" />
                        </svg>
                        <span>Materi Pembelajaran</span>
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
                    <a href="{{ route('classroom.tasks.index') }}" class="{{ request()->is('kelas/tugas*') || request()->is('tugas*') ? 'selected' : '' }}">
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
                    <button type="button" class="btn btn-ghost" id="notification-button" aria-label="Notifikasi">
                        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="flex-shrink: 0">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                            <path d="M10 21h4" />
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
                </div>
            </header>

            <main class="app-content">
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

    <script>
        (function() {
            var root = document.documentElement;
            var btn = document.getElementById('collapse-sidebar');
            var BASE = 12,
                MIN = 9;

            // Samakan ukuran font semua label menu; kecilkan bersama bila ada yang tidak muat 1 baris
            function fitNav() {
                var labels = document.querySelectorAll('.app-sidebar nav a > span');
                if (!labels.length || root.classList.contains('sb-collapsed')) return;
                root.style.setProperty('--nav-fs', BASE + 'px');
                var size = BASE;
                for (var i = 0; i < 8; i++) {
                    var overflow = false;
                    labels.forEach(function(l) {
                        if (l.scrollWidth > l.clientWidth + 1) overflow = true;
                    });
                    if (!overflow || size <= MIN) break;
                    size -= 0.5;
                    root.style.setProperty('--nav-fs', size + 'px');
                }
            }

            function syncButton() {
                if (!btn) return;
                var closed = root.classList.contains('sb-collapsed');
                btn.setAttribute('aria-expanded', closed ? 'false' : 'true');
                btn.setAttribute('aria-label', closed ? 'Buka sidebar' : 'Ciutkan sidebar');
            }

            // Tooltip untuk mode ikon saja
            document.querySelectorAll('.app-sidebar nav a').forEach(function(a) {
                var t = a.querySelector('span');
                if (t) a.setAttribute('title', t.textContent.trim());
            });

            // Tangkap klik tombol burger di fase capture agar handler bawaan mathleaf.js tidak ikut jalan
            document.addEventListener('click', function(e) {
                var hit = e.target.closest && e.target.closest('#collapse-sidebar');
                if (!hit) return;
                e.preventDefault();
                e.stopImmediatePropagation();
                var closed = root.classList.toggle('sb-collapsed');
                try {
                    localStorage.setItem('ml-sidebar-collapsed', closed ? '1' : '0');
                } catch (err) {}
                syncButton();
                setTimeout(fitNav, 220);
            }, true);

            window.addEventListener('resize', fitNav); // zoom Chrome memicu resize
            window.addEventListener('load', fitNav);
            if (document.fonts && document.fonts.ready) document.fonts.ready.then(fitNav);
            syncButton();
            fitNav();
        })();
    </script>
</body>

</html>

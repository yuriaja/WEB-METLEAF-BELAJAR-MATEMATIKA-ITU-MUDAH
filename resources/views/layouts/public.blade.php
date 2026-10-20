<!doctype html>
<html lang="id">
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Belajar matematika bilangan dan pecahan untuk siswa kelas VII SMP bersama MathLeaf.')">
    <title>@yield('title', 'MathLeaf — Grow Your Math Skills')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/mathleaf.css') }}">
    <script defer src="{{ asset('js/mathleaf.js') }}"></script>
</head>
<body>
<header class="public-header">
    <div class="site-container nav-inner">
        <a href="{{ url('/') }}" class="logo" aria-label="MathLeaf — Beranda">
            <span class="logo-mark"><x-icon name="leaf" :size="23" /></span>
            <span>Math<span class="logo-leaf">Leaf</span><small>GROW YOUR MATH SKILLS</small></span>
        </a>
        <nav class="public-nav" id="public-navigation" aria-label="Navigasi utama">
            <a href="{{ url('/') }}" @class(['active' => request()->is('/')])>Beranda</a>
            <a href="{{ url('/tentang') }}" @class(['active' => request()->is('tentang')])>Tentang</a>
            <a href="{{ url('/materi') }}" @class(['active' => request()->is('materi')])>Materi</a>
        </nav>
        <div class="nav-actions">
            <a class="btn btn-ghost login-link" href="{{ url('/masuk') }}">Masuk <x-icon name="arrow-up-right" :size="16" /></a>
            <a class="btn btn-primary nav-cta" href="{{ url('/daftar') }}">Daftar Gratis <x-icon name="arrow-up-right" :size="17" /></a>
            <button class="btn btn-ghost mobile-toggle" data-menu-toggle type="button" aria-expanded="false" aria-controls="public-navigation" aria-label="Buka menu">
                <span class="menu-open-icon"><x-icon name="menu" /></span>
                <span class="menu-close-icon" hidden><x-icon name="x" /></span>
            </button>
        </div>
    </div>
</header>
@yield('content')
<footer class="public-footer">
    <div class="site-container">
        <div class="footer-top">
            <div>
                <a href="{{ url('/') }}" class="logo" aria-label="MathLeaf — Beranda">
                    <span class="logo-mark"><x-icon name="leaf" :size="23" /></span>
                    <span>Math<span class="logo-leaf">Leaf</span><small>GROW YOUR MATH SKILLS</small></span>
                </a>
                <p>Setiap langkah kecil, membawa pemahaman besar.<br>Tumbuhkan kemampuan matematikamu bersama kami.</p>
            </div>
            <div>
                <h4>Jelajahi</h4>
                <a href="{{ url('/materi') }}">Materi Pembelajaran</a>
                <a href="{{ url('/tentang') }}">Tentang MathLeaf</a>
                <a href="{{ url('/forum') }}">Forum Diskusi</a>
            </div>
            <div>
                <h4>Mulai bertumbuh</h4>
                <a href="{{ url('/masuk') }}">Masuk sebagai Siswa</a>
                <a href="{{ url('/guru') }}">Ruang Guru</a>
                <a href="{{ url('/daftar') }}">Daftar Gratis</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2026 MathLeaf. Belajar, bertumbuh, bersama.</span>
            <span>Dibuat dengan semangat belajar <x-icon name="leaf" :size="14" /></span>
        </div>
    </div>
</footer>
</body>
</html>

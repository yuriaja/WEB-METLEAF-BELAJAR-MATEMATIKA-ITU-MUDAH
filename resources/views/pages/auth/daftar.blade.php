<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Daftar | MathLeaf</title>
    <meta name="description" content="MathLeaf — Grow Your Math Skills. Pembelajaran Matematika kelas VII SMP." />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="stylesheet" href="{{ asset('css/mathleaf.css') }}" />
    <script defer src="{{ asset('js/mathleaf.js') }}"></script>
</head>

<body data-page="daftar">
    <header class="public-header">
        <div class="site-container nav-inner">
            <a href="{{ url('/') }}" class="logo"><span class="logo-mark"><svg aria-hidden="true" width="23"
                        height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0">
                        <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
                        <path d="M4 21c0-6 5-11 11-14" />
                    </svg></span><span>Math<span class="logo-leaf">Leaf</span><small>GROW YOUR MATH
                        SKILLS</small></span></a>
            <nav class="public-nav" id="public-navigation">
                <a href="{{ url('/') }}" class="">Beranda</a><a href="{{ url('/tentang') }}"
                    class="">Tentang</a><a href="{{ url('/materi') }}" class="">Materi</a>
            </nav>
            <div class="nav-actions">
                <a class="btn btn-ghost" href="{{ url('/masuk') }}">Masuk
                    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M7 17 17 7" />
                        <path d="M7 7h10v10" />
                    </svg></a><a class="btn btn-primary nav-cta" href="{{ url('/daftar') }}">Daftar Gratis
                    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M7 17 17 7" />
                        <path d="M7 7h10v10" />
                    </svg></a><button class="btn btn-ghost mobile-toggle" id="menu-toggle" aria-label="Buka menu"
                    aria-expanded="false">
                    ☰
                </button>
            </div>
        </div>
    </header>
    <main class="auth-layout">
        <div class="auth-visual">
            <span class="eyebrow">SETIAP HARI ADALAH KESEMPATAN BARU</span>
            <h2>Tumbuhkan rasa ingin tahu.<br />Temukan serunya matematika.</h2>
            <p>Teman belajar bilangan dan operasi bilangan untuk kelas VII SMP.</p>
            <img src="{{ asset('images/mathleaf-hero.jpg') }}" alt="Dua siswa belajar bersama" />
            <p style="margin-top: 20px">
                Belajar mandiri. Berkolaborasi. Bertumbuh bersama.
            </p>
        </div>
        <form class="auth-form" id="auth-form" data-register="true">
            <span class="eyebrow"><svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    style="flex-shrink: 0">
                    <path d="M7 20h10" />
                    <path d="M10 20c5.5-2.5.8-8.3 5-11" />
                    <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
                    <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" />
                </svg>
                SELAMAT DATANG DI MATHLEAF</span>
            <h1>Mulai bertumbuh bersama.</h1>
            <p>Daftar dan mulai petualangan matematikamu.</p>
            <div class="role-tabs">
                <button type="button" class="btn btn-outline selected" data-role="Siswa">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M12 7v14" />
                        <path
                            d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z" />
                    </svg>
                    Siswa</button><button type="button" class="btn btn-outline" data-role="Guru">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    Guru
                </button>
            </div>
            <div class="field">
                <label for="name">Nama lengkap</label><input id="name" name="name"
                    placeholder="Nama lengkapmu" required />
            </div>
            <div class="field">
                <label for="email">Alamat email</label><input id="email" type="email" name="email"
                    placeholder="nama@email.com" required />
            </div>
            <div class="field">
                <label for="password">Kata sandi</label><input id="password" type="password" name="password"
                    placeholder="Minimal 6 karakter" minlength="6" required />
            </div>
            <div class="field" id="class-field">
                <label for="kelas">Kelas</label><select id="kelas">
                    <option>VII A</option>
                    <option>VII B</option>
                    <option>VII C</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit" style="width: 100%">
                Daftar Sekarang
                <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    style="flex-shrink: 0">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                </svg>
            </button>
            <div class="auth-note">
                Mode demo · Gunakan email dan kata sandi contoh. Tidak ada autentikasi
                server/database.
            </div>
            <p style="text-align: center; font-size: 11px">
                Sudah punya akun?
                <a style="color: var(--primary); font-weight: 700" href="{{ url('/masuk') }}">Masuk</a>
            </p>
        </form>
    </main>
    <footer class="public-footer">
        <div class="site-container">
            <div class="footer-top">
                <div>
                    <a href="{{ url('/') }}" class="logo"><span class="logo-mark"><svg aria-hidden="true"
                                width="23" height="23" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="flex-shrink: 0">
                                <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
                                <path d="M4 21c0-6 5-11 11-14" />
                            </svg></span><span>Math<span class="logo-leaf">Leaf</span><small>GROW YOUR MATH
                                SKILLS</small></span></a>
                    <p>
                        Setiap langkah kecil, membawa pemahaman besar.<br />Tumbuhkan
                        kemampuan matematikamu bersama kami.
                    </p>
                </div>
                <div>
                    <h4>Jelajahi</h4>
                    <a href="{{ url('/materi') }}">Materi Pembelajaran</a><a href="{{ url('/tentang') }}">Tentang
                        MathLeaf</a><a href="{{ url('/forum') }}">Forum Diskusi</a>
                </div>
                <div>
                    <h4>Mulai bertumbuh</h4>
                    <a href="{{ url('/masuk') }}">Masuk sebagai Siswa</a><a href="{{ url('/guru') }}">Ruang
                        Guru</a><a href="{{ url('/daftar') }}">Daftar Gratis</a>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© 2026 MathLeaf. Grow Your Math Skills.</span><span>Dirancang dengan
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink: 0">
                        <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
                        <path d="M4 21c0-6 5-11 11-14" />
                    </svg>
                    untuk pembelajar muda.</span>
            </div>
        </div>
    </footer>
</body>

</html>

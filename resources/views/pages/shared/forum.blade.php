
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Forum Diskusi | MathLeaf</title>

    <meta name="description"
          content="Forum Diskusi Matematika MathLeaf">

    <link rel="icon" type="image/svg+xml"
          href="{{ asset('favicon.svg') }}">

    <link rel="stylesheet"
          href="{{ asset('css/mathleaf.css') }}">

    <script defer src="{{ asset('js/mathleaf.js') }}"></script>

    <style>
        /* PERBAIKAN SIDEBAR FORUM - HAKIM */

        body[data-page="forum"] .app-sidebar {
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
        }

        body[data-page="forum"] .forum-navigation a {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
            padding: 11px 12px;
            border-radius: 10px;
            text-decoration: none;
        }

        body[data-page="forum"] .forum-nav-icon {
            display: inline-flex !important;
            width: 23px;
            min-width: 23px;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        body[data-page="forum"] .forum-nav-label {
            font-size: 12px;
            font-weight: 600;
            line-height: 1.5;
        }

        body[data-page="forum"]
        .sidebar-collapsed .forum-nav-label {
            display: none !important;
        }

        body[data-page="forum"]
        .forum-navigation a:focus-visible {
            outline: 2px solid #b9f1cb;
            outline-offset: 2px;
        }

        body[data-page="forum"] .forum-sidebar-button {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 11px 12px;
            border: none;
            background: transparent;
            color: inherit;
            font: inherit;
            text-align: left;
            cursor: pointer;
        }

        @media (max-width: 800px) {
            body[data-page="forum"]
            .sidebar-collapsed .forum-nav-label {
                display: inline !important;
            }
        }
    </style>

</head>

@php
    $currentUser = auth()->user();

    $isTeacher = $currentUser
        && in_array(
            $currentUser->role,
            ['guru', 'admin'],
            true
        );

    $userName = $currentUser?->name ?? 'Pengunjung';
@endphp

<body data-page="forum">

<div class="app-layout" id="app-layout">

    <!-- SIDEBAR -->
    <aside class="app-sidebar">

        <!-- LOGO -->
        <a href="{{ url('/') }}" class="logo logo-light">

            <span class="logo-mark">
                <svg aria-hidden="true"
                     width="23"
                     height="23"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"/>
                    <path d="M4 21c0-6 5-11 11-14"/>

                </svg>
            </span>

            <span>
                Math<span class="logo-leaf">Leaf</span>
                <small>GROW YOUR MATH SKILLS</small>
            </span>

        </a>

        <!-- IDENTITAS SIDEBAR -->
        <div class="sidebar-class">

            <svg aria-hidden="true"
                 width="18"
                 height="18"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">

                <path d="M7 20h10"/>
                <path d="M10 20c5.5-2.5.8-8.3 5-11"/>
                <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6"/>
                <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7"/>

            </svg>

            <span>
                @if($isTeacher)
                    Ruang Guru
                    <small>{{ $userName }} · Pengajar</small>
                @elseif($currentUser)
                    Ruang Siswa
                    <small>{{ $userName }} · Siswa</small>
                @else
                    MathLeaf
                    <small>Silakan masuk</small>
                @endif
            </span>

        </div>


        <!-- MENU GURU -->
        @if($isTeacher)

            <span class="sidebar-caption">
                PENGELOLAAN GURU
            </span>

            <nav class="forum-navigation"
                 aria-label="Navigasi Guru">

                <a href="{{ route('teacher.dashboard') }}">
                    <span class="forum-nav-icon">🏠</span>
                    <span class="forum-nav-label">
                        Dashboard Guru
                    </span>
                </a>

                <a href="{{ route('teacher.dashboard') }}#guru-kelas">
                    <span class="forum-nav-icon">🏫</span>
                    <span class="forum-nav-label">
                        Kelas yang Saya Ajar
                    </span>
                </a>

                <a href="{{ route('teacher.dashboard') }}#guru-materi">
                    <span class="forum-nav-icon">📚</span>
                    <span class="forum-nav-label">
                        Manajemen Materi
                    </span>
                </a>

                <a href="{{ route('teacher.dashboard') }}#guru-soal">
                    <span class="forum-nav-icon">📝</span>
                    <span class="forum-nav-label">
                        Buat Soal / Kuis
                    </span>
                </a>

                @if(\Illuminate\Support\Facades\Route::has('class-tasks.index'))
                    <a href="{{ route('class-tasks.index') }}">
                        <span class="forum-nav-icon">📤</span>
                        <span class="forum-nav-label">
                            Pengumpulan Tugas
                        </span>
                    </a>
                @endif

                <a href="{{ route('teacher.dashboard') }}#guru-daftar-kuis">
                    <span class="forum-nav-icon">📊</span>
                    <span class="forum-nav-label">
                        Daftar Kuis
                    </span>
                </a>

                <a href="{{ url('/forum') }}"
                   class="selected"
                   aria-current="page">
                    <span class="forum-nav-icon">💬</span>
                    <span class="forum-nav-label">
                        Forum Diskusi
                    </span>
                </a>

            </nav>


        <!-- MENU SISWA -->
        @elseif($currentUser)

            <span class="sidebar-caption">
                RUANG BELAJAR
            </span>

            <nav class="forum-navigation"
                 aria-label="Navigasi Siswa">

                <a href="{{ route('classroom.home') }}">
                    <span class="forum-nav-icon">🏫</span>
                    <span class="forum-nav-label">
                        Kelas Saya
                    </span>
                </a>

                <a href="{{ url('/dashboard') }}">
                    <span class="forum-nav-icon">🏠</span>
                    <span class="forum-nav-label">
                        Dashboard Siswa
                    </span>
                </a>

                <a href="{{ url('/materi') }}">
                    <span class="forum-nav-icon">📚</span>
                    <span class="forum-nav-label">
                        Materi Pembelajaran
                    </span>
                </a>

                <a href="{{ url('/video') }}">
                    <span class="forum-nav-icon">🎬</span>
                    <span class="forum-nav-label">
                        Video Pembelajaran
                    </span>
                </a>

                <a href="{{ url('/latihan') }}">
                    <span class="forum-nav-icon">✏️</span>
                    <span class="forum-nav-label">
                        E-LKPD Interaktif
                    </span>
                </a>

                <a href="{{ url('/kuis') }}">
                    <span class="forum-nav-icon">📝</span>
                    <span class="forum-nav-label">
                        Kuis &amp; Evaluasi
                    </span>
                </a>

                <a href="{{ url('/forum') }}"
                   class="selected"
                   aria-current="page">
                    <span class="forum-nav-icon">💬</span>
                    <span class="forum-nav-label">
                        Forum Diskusi
                    </span>
                </a>

                <a href="{{ url('/tugas') }}">
                    <span class="forum-nav-icon">📤</span>
                    <span class="forum-nav-label">
                        Tugas &amp; Kelompok
                    </span>
                </a>

                <a href="{{ url('/nilai') }}">
                    <span class="forum-nav-icon">🏆</span>
                    <span class="forum-nav-label">
                        Nilai &amp; Progres
                    </span>
                </a>

            </nav>

        @else

            <span class="sidebar-caption">AKUN</span>

            <nav class="forum-navigation"
                 aria-label="Navigasi Pengunjung">

                <a href="{{ url('/masuk') }}">
                    <span class="forum-nav-icon">🔑</span>
                    <span class="forum-nav-label">
                        Masuk
                    </span>
                </a>

            </nav>

        @endif


        <!-- BAGIAN BAWAH SIDEBAR -->
        <div class="sidebar-bottom">

            <div class="sidebar-motivation">

                <svg aria-hidden="true"
                     width="18"
                     height="18"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <path d="M7 20h10"/>
                    <path d="M10 20c5.5-2.5.8-8.3 5-11"/>
                    <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6"/>
                    <path d="M14 12c0-4 3-6 7-6"/>

                </svg>

                <p>
                    Setiap langkah kecil membawa
                    perubahan besar. Terus bertumbuh!
                </p>

            </div>

            <a href="{{ url('/') }}">
                <span>🏡</span>
                <span>Ke Beranda</span>
            </a>

            @if($currentUser && \Illuminate\Support\Facades\Route::has('logout'))

                <form action="{{ route('logout') }}"
                      method="POST">
                    @csrf

                    <button type="submit"
                            class="forum-sidebar-button">
                        <span>🚪</span>
                        <span>Keluar</span>
                    </button>
                </form>

            @elseif(!$currentUser)

                <a href="{{ url('/masuk') }}">
                    <span>🔑</span>
                    <span>Masuk</span>
                </a>

            @else

                <a href="{{ url('/masuk') }}">
                    <span>🔑</span>
                    <span>Halaman Masuk</span>
                </a>

            @endif

        </div>

    </aside>


    <!-- KONTEN UTAMA -->
    <div class="app-main">

        <!-- HEADER -->
        <header class="app-header">

            <div style="display:flex;align-items:center;gap:12px">

                <button type="button"
                        class="btn btn-ghost"
                        id="collapse-sidebar"
                        aria-label="Ciutkan sidebar">
                    ☰
                </button>

                <strong>Forum Diskusi</strong>

            </div>

            <div style="display:flex;gap:16px;align-items:center">

                <button type="button"
                        class="btn btn-ghost"
                        id="notification-button"
                        aria-label="Notifikasi">

                    <svg aria-hidden="true"
                         width="18"
                         height="18"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round">

                        <path d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z"/>
                        <path d="m5 17 .8 2.2L8 20l-2.2.8L5 23l-.8-2.2L2 20l2.2-.8L5 17Z"/>
                        <path d="m19 2 .7 1.3L21 4l-1.3.7L19 6l-.7-1.3L17 4l1.3-.7L19 2Z"/>

                    </svg>

                </button>

                <span class="header-profile">

                    <span class="avatar">
                        {{ mb_strtoupper(mb_substr($userName, 0, 1)) }}
                    </span>

                    <span>
                        {{ $userName }}
                    </span>

                </span>

            </div>

            <div class="notification-panel"
                 id="notification-panel"
                 hidden>

                <strong>Notifikasi MathLeaf</strong>

                <p>
                    Terus belajar, satu langkah kecil
                    setiap hari 🌱
                </p>

            </div>

        </header>


        <!-- HALAMAN FORUM -->
        <main class="app-content">

            <div class="page-heading">

                <div>

                    <span class="eyebrow">
                        💬 FORUM PEMBELAJARAN
                    </span>

                    <h1>Ide Hebat Tumbuh Bersama 💬</h1>

                    <p>
                        Bertanya, berbagi pemahaman,
                        dan belajar dari satu sama lain.
                    </p>

                </div>

                <button type="button"
                        class="btn btn-primary"
                        id="new-topic-btn">

                    + Buat Pertanyaan

                </button>

            </div>


            <div class="two-columns">

                <!-- DAFTAR DISKUSI -->
                <section class="panel">

                    <div class="panel-head">

                        <h2>Diskusi Kelas VII A</h2>

                        <span class="badge" id="topic-count">
                            3 topik aktif
                        </span>

                    </div>


                    <div id="forum-topics">

                        <!-- TOPIK 1 -->
                        <article class="thread">

                            <div class="thread-title">

                                <span class="avatar">A</span>

                                <div>
                                    <strong>Alya Putri</strong>
                                    <small>
                                        Siswa · VII A · Hari ini
                                    </small>
                                </div>

                            </div>

                            <h3>
                                Kenapa negatif dikali negatif
                                hasilnya positif?
                            </h3>

                            <p>
                                Aku sudah menonton videonya,
                                tetapi masih ingin tahu contoh
                                dalam kehidupan sehari-hari.
                                Ada yang bisa menjelaskan?
                            </p>

                            <div class="thread-meta">
                                <span class="badge">
                                    Diskusi aktif
                                </span>
                            </div>

                            <div class="reply">

                                <b>Bu Ratna · Guru</b>

                                <p>
                                    Coba amati pola:
                                    −2 × 2 = −4,
                                    −2 × 1 = −2,
                                    −2 × 0 = 0.
                                    Setiap langkah hasil bertambah
                                    2, jadi −2 × (−1) = 2.
                                </p>

                            </div>

                        </article>


                        <!-- TOPIK 2 -->
                        <article class="thread">

                            <div class="thread-title">

                                <span class="avatar">B</span>

                                <div>
                                    <strong>Bima Pratama</strong>
                                    <small>
                                        Siswa · VII A · Hari ini
                                    </small>
                                </div>

                            </div>

                            <h3>
                                Cara mudah menyamakan penyebut pecahan?
                            </h3>

                            <p>
                                Untuk 1/3 + 1/4,
                                bagaimana menemukan penyebut
                                yang sama?
                            </p>

                            <div class="thread-meta">
                                <span class="badge">
                                    Diskusi aktif
                                </span>
                            </div>

                            <div class="reply">

                                <b>Dika · Siswa</b>

                                <p>
                                    Cari KPK dari 3 dan 4,
                                    yaitu 12. Jadi
                                    4/12 + 3/12 = 7/12.
                                </p>

                            </div>

                        </article>


                        <!-- TOPIK 3 -->
                        <article class="thread">

                            <div class="thread-title">

                                <span class="avatar">B</span>

                                <div>
                                    <strong>Bu Ratna</strong>
                                    <small>
                                        Guru Matematika · Hari ini
                                    </small>
                                </div>

                            </div>

                            <h3>
                                Diskusi investigasi:
                                perubahan suhu harian
                            </h3>

                            <p>
                                Bagikan strategi kelompokmu
                                untuk menghitung selisih suhu
                                minimum dan maksimum.
                                Jelaskan alasan setiap langkah.
                            </p>

                            <div class="thread-meta">
                                <span class="badge">
                                    Diskusi aktif
                                </span>
                            </div>

                        </article>

                    </div>


                    <!-- FORM BALAS DISKUSI -->
                    <form id="forum-reply-form"
                          style="margin-top:20px">

                        <label for="forum-reply" class="field">
                            Balas diskusi kelas
                        </label>

                        <textarea
                            id="forum-reply"
                            class="answer-input"
                            required
                            placeholder="Tulis pendapatmu…"
                        ></textarea>

                        <button type="submit"
                                class="btn btn-primary"
                                style="margin-top:10px">

                            ➜ Kirim Balasan

                        </button>

                        <div id="forum-reply-notice"
                             class="inline-notice"
                             hidden>
                        </div>

                    </form>

                </section>


                <!-- INFORMASI FORUM -->
                <aside>

                    <section class="panel">

                        <h2>Ruang Diskusi</h2>

                        <p>
                            Ajukan pertanyaan, berbagi
                            strategi, dan hormati pendapat
                            teman. Gunakan forum untuk
                            mendukung investigasi matematika
                            bersama.
                        </p>

                        <div class="quote-panel">

                            <h3>Tips Diskusi</h3>

                            <p>
                                Jelaskan langkah berpikirmu,
                                bukan hanya hasil akhirnya.
                            </p>

                        </div>

                    </section>

                </aside>

            </div>


            <!-- MODAL BUAT PERTANYAAN -->
            <div class="modal-backdrop"
                 id="forum-modal"
                 hidden>

                <form class="modal"
                      id="forum-new-form">

                    <div class="modal-head">

                        <h2>Buat Pertanyaan</h2>

                        <button type="button"
                                class="btn btn-ghost"
                                data-close-modal>
                            ✕
                        </button>

                    </div>

                    <div class="field">

                        <label for="topic-title">
                            Judul Diskusi
                        </label>

                        <input
                            type="text"
                            id="topic-title"
                            required
                        >

                    </div>

                    <div class="field">

                        <label for="topic-body">
                            Isi Pertanyaan
                        </label>

                        <textarea
                            id="topic-body"
                            required
                        ></textarea>

                    </div>

                    <button type="submit"
                            class="btn btn-primary">
                        Publikasikan (demo)
                    </button>

                </form>

            </div>

        </main>

    </div>

</div>

</body>
</html>

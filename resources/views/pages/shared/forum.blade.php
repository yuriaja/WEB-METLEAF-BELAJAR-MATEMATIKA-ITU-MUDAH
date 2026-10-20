<!doctype html>
<html lang="id">

<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Forum Diskusi | MathLeaf</title>
    <meta name="description" content="MathLeaf — Grow Your Math Skills. Pembelajaran Matematika kelas VII SMP." />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="stylesheet" href="{{ asset('css/mathleaf.css') }}" />
    <script defer src="{{ asset('js/mathleaf.js') }}"></script>
</head>

<body data-page="forum">
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
            <nav><a href="{{ url('/kelas') }}" style="background:rgba(255,255,255,.14);font-weight:700"><span>🏫 Kelas Saya & Konten Guru</span></a>
                <a href="{{ url('/dashboard') }}" class=""><svg aria-hidden="true" width="18" height="18"
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
                    </svg><span>Kuis &amp; Evaluasi</span></a><a href="{{ url('/forum') }}" class="selected"><svg
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
                        ☰</button><strong>Forum Diskusi</strong>
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
                        <h1>Ide Hebat Tumbuh Bersama 💬</h1>
                        <p>
                            Bertanya, berbagi pemahaman, dan belajar dari satu sama lain.
                        </p>
                    </div>
                    <button type="button" class="btn btn-primary" id="new-topic-btn">
                        + Buat Pertanyaan
                    </button>
                </div>
                <div class="two-columns">
                    <section class="panel">
                        <div class="panel-head">
                            <h2>Diskusi Kelas VII A</h2>
                            <span class="badge" id="topic-count">3 topik aktif</span>
                        </div>
                        <div id="forum-topics">
                            <article class="thread">
                                <div class="thread-title">
                                    <span class="avatar">A</span>
                                    <div>
                                        <strong>Alya Putri</strong><small>Siswa · VII A · Hari ini</small>
                                    </div>
                                </div>
                                <h3>Kenapa negatif dikali negatif hasilnya positif?</h3>
                                <p>
                                    Aku sudah menonton videonya, tetapi masih ingin tahu contoh
                                    dalam kehidupan sehari-hari. Ada yang bisa menjelaskan?
                                </p>
                                <div class="thread-meta">
                                    <span class="badge">Diskusi aktif</span>
                                </div>
                                <div class="reply">
                                    <b>Bu Ratna · Guru</b>
                                    <p>
                                        Coba amati pola: −2 × 2 = −4, −2 × 1 = −2, −2 × 0 = 0.
                                        Setiap langkah hasil bertambah 2, jadi −2 × (−1) = 2.
                                    </p>
                                </div>
                            </article>
                            <article class="thread">
                                <div class="thread-title">
                                    <span class="avatar">B</span>
                                    <div>
                                        <strong>Bima Pratama</strong><small>Siswa · VII A · Hari ini</small>
                                    </div>
                                </div>
                                <h3>Cara mudah menyamakan penyebut pecahan?</h3>
                                <p>
                                    Untuk 1/3 + 1/4, bagaimana menemukan penyebut yang sama?
                                </p>
                                <div class="thread-meta">
                                    <span class="badge">Diskusi aktif</span>
                                </div>
                                <div class="reply">
                                    <b>Dika · Siswa</b>
                                    <p>
                                        Cari KPK dari 3 dan 4, yaitu 12. Jadi 4/12 + 3/12 = 7/12.
                                    </p>
                                </div>
                            </article>
                            <article class="thread">
                                <div class="thread-title">
                                    <span class="avatar">B</span>
                                    <div>
                                        <strong>Bu Ratna</strong><small>Guru Matematika · Hari ini</small>
                                    </div>
                                </div>
                                <h3>Diskusi investigasi: perubahan suhu harian</h3>
                                <p>
                                    Bagikan strategi kelompokmu untuk menghitung selisih suhu
                                    minimum dan maksimum. Jelaskan alasan setiap langkah.
                                </p>
                                <div class="thread-meta">
                                    <span class="badge">Diskusi aktif</span>
                                </div>
                            </article>
                        </div>
                        <form id="forum-reply-form" style="margin-top: 20px">
                            <label for="forum-reply" class="field">Balas diskusi kelas</label>
                            <textarea id="forum-reply" class="answer-input" required placeholder="Tulis pendapatmu…"></textarea
                ><button
                  class="btn btn-primary"
                  type="submit"
                  style="margin-top: 10px"
                >
                  <svg
                    aria-hidden="true"
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    style="flex-shrink: 0"
                  >
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                  </svg>
                  Kirim Balasan
                </button>
                <div id="forum-reply-notice" class="inline-notice" hidden></div>
              </form>
            </section>
            <aside>
              <section class="panel">
                <h2>Ruang Diskusi</h2>
                <p>
                  Ajukan pertanyaan, berbagi strategi, dan hormati pendapat
                  teman. Gunakan forum untuk mendukung investigasi matematika
                  bersama.
                </p>
                <div class="quote-panel">
                  <h3>Tips diskusi</h3>
                  <p>
                    Jelaskan langkah berpikirmu, bukan hanya hasil akhirnya.
                  </p>
                </div>
              </section>
            </aside>
          </div>
          <div class="modal-backdrop" id="forum-modal" hidden>
            <form class="modal" id="forum-new-form">
              <div class="modal-head">
                <h2>Buat Pertanyaan</h2>
                <button type="button" class="btn btn-ghost" data-close-modal>
                  ✕
                </button>
              </div>
              <div class="field">
                <label for="topic-title">Judul diskusi</label
                ><input id="topic-title" required />
              </div>
              <div class="field">
                <label for="topic-body">Isi pertanyaan</label
                ><textarea id="topic-body" required></textarea>
                </div>
                <button class="btn btn-primary" type="submit">
                    Publikasikan (demo)
                </button>
                </form>
        </div>
        </main>
    </div>
    </div>
</body>

</html>

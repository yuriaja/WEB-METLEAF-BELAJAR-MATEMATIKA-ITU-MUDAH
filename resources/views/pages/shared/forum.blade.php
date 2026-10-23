@extends('layouts.app')

@section('title', 'Forum Diskusi | MathLeaf')
@section('header_title', 'Forum Diskusi')

@section('content')
    @php
        $isTeacher = Auth::check() && in_array(Auth::user()->role, ['guru', 'admin']);
        $userName = Auth::user()->name ?? 'Karin';
    @endphp

    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
        }

        body {
            font-size: 16px;
        }

        .page-heading {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-heading h1 {
            font-size: 2rem;
            color: #0f172a;
            margin: 0.5rem 0;
            font-weight: 800;
        }

        .page-heading p {
            color: #475569;
            font-size: 1.1rem;
            margin: 0;
            line-height: 1.6;
        }

        .btn-primary {
            background: var(--ml-green);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: var(--ml-green-hover);
        }

        .two-columns {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 900px) {
            .two-columns {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.02);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1rem;
        }

        .panel-head h2 {
            font-size: 1.4rem;
            margin: 0;
            color: #0f172a;
            font-weight: 700;
        }

        .badge {
            background: var(--ml-green-light);
            color: var(--ml-green);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 700;
        }

        /* THREAD / DISKUSI */
        .thread {
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            background: #fafafa;
        }

        .thread-title {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .avatar-large {
            width: 45px;
            height: 45px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .thread-title strong {
            display: block;
            font-size: 1.1rem;
            color: #0f172a;
        }

        .thread-title small {
            color: #64748b;
            font-size: 0.9rem;
        }

        .thread h3 {
            font-size: 1.3rem;
            color: #0f172a;
            margin: 0 0 0.5rem 0;
            line-height: 1.4;
        }

        .thread p {
            font-size: 1.05rem;
            color: #334155;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .reply {
            margin-top: 1rem;
            padding: 1.2rem;
            background: #ffffff;
            border-left: 4px solid var(--ml-green);
            border-radius: 0 8px 8px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .reply b {
            display: block;
            font-size: 1rem;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }

        .reply p {
            font-size: 1rem;
            color: #475569;
            margin: 0;
        }

        .field label {
            display: block;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }

        .answer-input {
            width: 100%;
            padding: 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1.05rem;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
            outline: none;
            transition: border-color 0.2s;
        }

        .answer-input:focus {
            border-color: var(--ml-green);
        }

        /* PANEL KANAN (TOPIK) */
        .side-panel-box {
            margin-bottom: 1.5rem;
        }

        .side-panel-box h3 {
            font-size: 1.2rem;
            color: #0f172a;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .topic-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .topic-item {
            display: block;
            padding: 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            border-radius: 8px;
            text-decoration: none;
            color: #334155;
            transition: all 0.2s;
        }

        .topic-item:hover {
            background: #ffffff;
            border-color: var(--ml-green);
            box-shadow: 0 2px 8px rgba(8, 101, 52, 0.1);
            transform: translateX(-3px);
        }

        .topic-item strong {
            display: block;
            font-size: 1rem;
            color: #0f172a;
            margin-bottom: 0.3rem;
            line-height: 1.3;
        }

        .topic-item small {
            font-size: 0.85rem;
            color: #64748b;
        }

        /* MODAL BUAT PERTANYAAN */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .modal-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            transform: translateY(20px);
            transition: transform 0.3s;
        }

        .modal-backdrop.active .modal {
            transform: translateY(0);
        }

        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-head h2 {
            font-size: 1.4rem;
            margin: 0;
        }

        .modal-head button {
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
        }

        .field input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        /* SLIDE-OUT DRAWER (TOPIC DETAIL) */
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.3);
            z-index: 9998;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .drawer-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .topic-drawer {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 600px;
            height: 100vh;
            background: #ffffff;
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            transition: right 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            /* Efek membal mulus */
            display: flex;
            flex-direction: column;
        }

        .topic-drawer.active {
            right: 0;
        }

        .drawer-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .drawer-header h2 {
            font-size: 1.3rem;
            margin: 0;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-close-drawer {
            background: #e2e8f0;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            transition: background 0.2s;
        }

        .btn-close-drawer:hover {
            background: #cbd5e1;
            color: #0f172a;
        }

        .drawer-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .drawer-footer {
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow"
                style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #086534; font-size: 0.9rem; letter-spacing: 0.05em; text-transform: uppercase;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                    </path>
                </svg>
                FORUM PEMBELAJARAN
            </span>
            <h1>Ide Hebat Tumbuh Bersama</h1>
            <p>Bertanya, berbagi pemahaman, dan belajar dari satu sama lain.</p>
        </div>
        <button type="button" class="btn-primary" onclick="document.getElementById('forum-modal').classList.add('active')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Buat Pertanyaan
        </button>
    </div>

    <div class="two-columns">
        <!-- KOLOM KIRI: DAFTAR DISKUSI -->
        <section class="panel">
            <div class="panel-head">
                <h2>Diskusi Kelas VII A</h2>
                <span class="badge">3 topik aktif</span>
            </div>

            <div id="forum-topics">
                <!-- TOPIK 1 -->
                <article class="thread">
                    <div class="thread-title">
                        <span class="avatar-large">A</span>
                        <div>
                            <strong>Alya Putri</strong>
                            <small>Siswa · VII A · Hari ini</small>
                        </div>
                    </div>
                    <h3>Kenapa negatif dikali negatif hasilnya positif?</h3>
                    <p>Aku sudah menonton videonya, tetapi masih ingin tahu contoh dalam kehidupan sehari-hari. Ada yang
                        bisa menjelaskan?</p>
                    <div class="reply">
                        <b>Bu Ratna · Guru</b>
                        <p>Coba amati pola: −2 × 2 = −4, −2 × 1 = −2, −2 × 0 = 0. Setiap langkah hasil bertambah 2, jadi −2
                            × (−1) = 2.</p>
                    </div>
                </article>

                <!-- TOPIK 2 -->
                <article class="thread">
                    <div class="thread-title">
                        <span class="avatar-large">B</span>
                        <div>
                            <strong>Bima Pratama</strong>
                            <small>Siswa · VII A · Hari ini</small>
                        </div>
                    </div>
                    <h3>Cara mudah menyamakan penyebut pecahan?</h3>
                    <p>Untuk 1/3 + 1/4, bagaimana menemukan penyebut yang sama?</p>
                    <div class="reply">
                        <b>Dika · Siswa</b>
                        <p>Cari KPK dari 3 dan 4, yaitu 12. Jadi 4/12 + 3/12 = 7/12.</p>
                    </div>
                </article>
            </div>

            <form style="margin-top:2rem; padding-top:1rem; border-top:1px dashed #cbd5e1;">
                <div class="field">
                    <label>Kirim Pendapat atau Jawaban Baru</label>
                    <textarea class="answer-input" required placeholder="Tuliskan pemikiran atau jawabanmu di sini..."></textarea>
                </div>
                <button type="submit" class="btn-primary" style="margin-top:1rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Kirim Balasan
                </button>
            </form>
        </section>

        <!-- KOLOM KANAN: TOPIK GURU & TOPIK SAYA -->
        <aside>
            <!-- TOPIK DARI GURU -->
            <section class="panel side-panel-box">
                <h3>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--ml-green);">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                    </svg>
                    Topik dari Guru
                </h3>
                <div class="topic-list">
                    <a class="topic-item"
                        onclick="openDrawer('Diskusi investigasi: perubahan suhu harian', 'Bu Ratna', 'Guru')">
                        <strong>Diskusi investigasi: perubahan suhu harian</strong>
                        <small>Oleh Bu Ratna · 13 Balasan</small>
                    </a>
                    <a class="topic-item" onclick="openDrawer('Penerapan pecahan pada resep masakan', 'Bu Ratna', 'Guru')">
                        <strong>Penerapan pecahan pada resep masakan</strong>
                        <small>Oleh Bu Ratna · 5 Balasan</small>
                    </a>
                </div>
            </section>

            <!-- TOPIK YANG SAYA TANYAKAN -->
            <section class="panel side-panel-box">
                <h3>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #0284c7;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Topik yang Kamu Tanyakan
                </h3>
                @if ($isTeacher)
                    <p style="font-size: 0.95rem; color: #64748b;">Anda adalah Guru. Lihat pertanyaan siswa di panel utama.
                    </p>
                @else
                    <div class="topic-list">
                        <a class="topic-item"
                            onclick="openDrawer('Cara cepat menghitung perkalian besar?', '{{ $userName }}', 'Siswa')">
                            <strong>Cara cepat menghitung perkalian besar?</strong>
                            <small>2 Balasan · Diperbarui kemarin</small>
                        </a>
                    </div>
                @endif
            </section>
        </aside>
    </div>

    <!-- MODAL BUAT PERTANYAAN -->
    <div class="modal-backdrop" id="forum-modal">
        <form class="modal">
            <div class="modal-head">
                <h2>Buat Pertanyaan Baru</h2>
                <button type="button" onclick="document.getElementById('forum-modal').classList.remove('active')">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="field">
                <label>Judul Diskusi</label>
                <input type="text" required placeholder="Contoh: Bingung cara mengubah pecahan ke desimal">
            </div>
            <div class="field">
                <label>Isi Pertanyaan</label>
                <textarea class="answer-input" required placeholder="Tuliskan detail pertanyaanmu di sini..."></textarea>
            </div>
            <button type="submit" class="btn-primary" style="margin-top: 1rem; width: 100%; justify-content: center;">
                Publikasikan Pertanyaan
            </button>
        </form>
    </div>

    <!-- OFF-CANVAS DRAWER (SLIDE KELUAR) -->
    <div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawer()"></div>
    <div class="topic-drawer" id="topic-drawer">
        <div class="drawer-header">
            <h2>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                    </path>
                </svg>
                Detail Diskusi
            </h2>
            <button class="btn-close-drawer" onclick="closeDrawer()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="drawer-content">
            <!-- Isi Dinamis Diskusi -->
            <article class="thread" style="background: transparent; border: none; padding: 0;">
                <div class="thread-title">
                    <span class="avatar-large" id="drawer-avatar">R</span>
                    <div>
                        <strong id="drawer-author">Bu Ratna</strong>
                        <small id="drawer-role">Guru · VII A</small>
                    </div>
                </div>
                <h3 id="drawer-title">Diskusi investigasi: perubahan suhu harian</h3>
                <p>Bagikan strategi kelompokmu untuk menghitung selisih suhu minimum dan maksimum. Jelaskan alasan setiap
                    langkah secara detail ya.</p>

                <div style="margin-top: 2rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #64748b;">Balasan (2)</h4>

                    <div class="reply" style="margin-top: 0; margin-bottom: 1rem; border-left-color: #0284c7;">
                        <b>Bima Pratama · Siswa</b>
                        <p>Kelompok kami menggunakan garis bilangan bu, jadi lebih mudah melihat jarak dari suhu negatif ke
                            positif.</p>
                    </div>

                    <div class="reply" style="margin-top: 0; margin-bottom: 1rem;">
                        <b>Bu Ratna · Guru</b>
                        <p>Bagus sekali Bima! Pendekatan visual memang sangat membantu.</p>
                    </div>
                </div>
            </article>
        </div>

        <div class="drawer-footer">
            <form action="#">
                <textarea class="answer-input" style="min-height: 80px; margin-bottom: 0.5rem;" required
                    placeholder="Balas diskusi ini..."></textarea>
                <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Kirim Balasan
                </button>
            </form>
        </div>
    </div>

    <script>
        // Tutup modal jika area luar diklik
        document.getElementById('forum-modal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('active');
        });

        // Logika Drawer Slide-Out
        function openDrawer(title, author, role) {
            // Manipulasi isi (hanya simulasi sederhana)
            document.getElementById('drawer-title').innerText = title;
            document.getElementById('drawer-author').innerText = author;
            document.getElementById('drawer-role').innerText = role + ' · VII A';
            document.getElementById('drawer-avatar').innerText = author.charAt(0).toUpperCase();

            // Tampilkan
            document.getElementById('drawer-overlay').classList.add('active');
            document.getElementById('topic-drawer').classList.add('active');
        }

        function closeDrawer() {
            document.getElementById('drawer-overlay').classList.remove('active');
            document.getElementById('topic-drawer').classList.remove('active');
        }
    </script>
@endsection

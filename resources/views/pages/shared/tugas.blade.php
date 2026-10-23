@extends('layouts.app')

@section('title', 'Tugas dan Kelompok | MathLeaf')
@section('header_title', 'Tugas & Kelompok')

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

        .page-heading {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-heading h1 {
            font-size: 1.8rem;
            color: #0f172a;
            margin: 0.5rem 0;
            font-weight: 800;
        }

        .page-heading p {
            color: #64748b;
            font-size: 1rem;
            margin: 0;
            line-height: 1.6;
        }

        .two-columns {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            align-items: start;
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

        .panel h2 {
            font-size: 1.25rem;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 1.2rem;
        }

        .panel h3 {
            font-size: 1.1rem;
            color: #0f172a;
            margin: 1.5rem 0 0.8rem 0;
        }

        .panel p {
            font-size: 1rem;
            line-height: 1.6;
            color: #334155;
        }

        /* TAB FILTER */
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            background: #f1f5f9;
            padding: 0.4rem;
            border-radius: 8px;
            width: max-content;
        }

        .tab-btn {
            background: transparent;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.95rem;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab-btn.selected {
            background: #ffffff;
            color: var(--ml-green);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* BADGES */
        .badge-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .badge {
            background: var(--ml-green-light);
            color: var(--ml-green);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .badge.yellow {
            background: #fef9c3;
            color: #ca8a04;
        }

        .badge.red {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge.green {
            background: #dcfce7;
            color: #16a34a;
        }

        /* ANGGOTA KELOMPOK */
        .members-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .member-chip {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
        }

        /* INFO DETAIL KEGIATAN DI KIRI */
        .detail-kegiatan-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.2rem;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .date-block {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            width: 50px;
            height: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            color: #0f172a;
            line-height: 1;
        }

        .date-block small {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 700;
            margin-top: 0.2rem;
        }

        /* UPLOAD ZONE */
        .upload-zone {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .upload-zone:hover {
            border-color: var(--ml-green);
            background: var(--ml-green-light);
        }

        .upload-zone svg {
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .upload-zone b {
            font-size: 1rem;
            color: #0f172a;
        }

        .upload-zone p {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.5rem;
        }

        .upload-zone input {
            display: none;
        }

        .btn-primary {
            background: var(--ml-green);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            width: 100%;
            justify-content: center;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            margin-top: 1rem;
        }

        /* AREA KANAN: STATUS NILAI & DISKUSI */
        .status-box {
            text-align: center;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .status-box .big-score {
            font-size: 3rem;
            font-weight: 800;
            color: #cbd5e1;
            line-height: 1;
            margin: 1rem 0;
        }

        .status-box.graded .big-score {
            color: var(--ml-green);
        }

        /* CHAT SISWA (Obrolan Biasa) */
        .chat-bubble {
            background: #f1f5f9;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .chat-bubble b {
            display: block;
            color: #0f172a;
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .chat-bubble p {
            margin: 0;
            color: #334155;
            font-size: 0.95rem;
        }

        /* FORMAT BALASAN GURU (Style Forum) */
        .reply {
            padding: 1rem;
            background: #ffffff;
            border-left: 4px solid var(--ml-green);
            border-radius: 0 8px 8px 0;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .reply b {
            display: block;
            font-size: 0.95rem;
            color: #0f172a;
            margin-bottom: 0.3rem;
        }

        .reply p {
            font-size: 0.95rem;
            color: #475569;
            margin: 0;
        }

        .answer-input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
            outline: none;
        }

        /* ANIMASI TRANSLISI TAB */
        .fade-panel {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .hidden-panel {
            display: none !important;
            opacity: 0;
            transform: translateY(10px);
        }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow"
                style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #086534; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"></path>
                    <path d="M4 21c0-6 5-11 11-14"></path>
                </svg>
                RUANG BERTUMBUHMU
            </span>
            <h1>Tantangan Nyata, Solusi Bersama</h1>
            <p>Tahap 3 & 4 · Investigasi, presentasi, dan refleksi.</p>
        </div>
    </div>

    <div class="filter-tabs">
        <button type="button" class="tab-btn selected" id="tab-kelompok" onclick="switchTab('kelompok')">Investigasi
            Kelompok</button>
        <button type="button" class="tab-btn" id="tab-individu" onclick="switchTab('individu')">Tugas Individu</button>
    </div>

    <div class="two-columns">
        <!-- PANEL KIRI: DETAIL TUGAS & UPLOAD -->
        <div>
            <!-- KONTEN KELOMPOK -->
            <section class="panel fade-panel" id="content-kelompok">
                <div class="badge-group">
                    <span class="badge">Kelompok 3 · Daun Cerdas</span>
                    <span class="badge yellow">Belum Dikumpulkan</span>
                </div>

                <h2>Menyelidiki Perubahan Suhu</h2>
                <p>
                    Suhu kota A pada pukul 06.00 adalah −3°C, pukul 12.00 adalah 8°C, dan pukul 18.00 adalah 2°C.
                    Buat garis bilangan dan tentukan perubahan suhu pada setiap rentang waktu. Bandingkan dengan
                    kota B yang suhunya 1°C, 10°C, dan −2°C. Kota mana memiliki rentang suhu lebih besar?
                </p>

                <h3>Hasil Investigasi yang Diminta</h3>
                <p>
                    1. Data dan visualisasi garis bilangan.<br>
                    2. Perhitungan lengkap beserta alasannya.<br>
                    3. Kesimpulan dan refleksi proses belajar.
                </p>

                <div class="detail-kegiatan-box">
                    <div class="date-block">19<small>OKT</small></div>
                    <div>
                        <strong style="display:block; color:#0f172a; font-size:1.05rem; margin-bottom:0.2rem;">Batas
                            Pengumpulan</strong>
                        <small style="color:#64748b; font-size:0.9rem;">Senin, 19 Oktober 2026 · 23.59</small>
                        <p style="margin: 0.4rem 0 0 0; font-size: 0.9rem;">Jadwal Presentasi: 21 Okt (5 mnt/kelompok)</p>
                    </div>
                </div>

                <h3 style="margin-top: 1.5rem;">Anggota Kelompok</h3>
                <div class="members-list">
                    <span class="member-chip" style="background: var(--ml-green); color: white;">{{ $userName }}</span>
                    <span class="member-chip">Alya</span>
                    <span class="member-chip">Bima</span>
                    <span class="member-chip">Dika</span>
                </div>

                @if (!$isTeacher)
                    <label class="upload-zone">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 7v14"></path>
                            <path
                                d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z">
                            </path>
                        </svg>
                        <b>Unggah hasil pekerjaan kelompok</b>
                        <p>PDF, dokumen, atau gambar · Maksimal 10 MB</p>
                        <input type="file" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" />
                    </label>
                    <button type="button" class="btn-primary">Kumpulkan Tugas Kelompok</button>
                @endif
            </section>

            <!-- KONTEN INDIVIDU (Hidden by default) -->
            <section class="panel fade-panel hidden-panel" id="content-individu">
                <div class="badge-group">
                    <span class="badge" style="background: #e0f2fe; color: #0284c7;">Tugas Mandiri</span>
                    <span class="badge green">Sudah Dinilai</span>
                </div>

                <h2>Refleksi Operasi Pecahan</h2>
                <p>
                    Buatlah rangkuman singkat (minimal 1 paragraf) menggunakan bahasamu sendiri tentang perbedaan
                    menghitung penjumlahan pecahan dan perkalian pecahan. Berikan 1 contoh soal buatanmu sendiri beserta
                    penyelesaiannya.
                </p>

                <div class="detail-kegiatan-box">
                    <div class="date-block" style="background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0;">
                        12<small>OKT</small></div>
                    <div>
                        <strong style="display:block; color:#0f172a; font-size:1.05rem; margin-bottom:0.2rem;">Tugas Telah
                            Ditutup</strong>
                        <small style="color:#64748b; font-size:0.9rem;">Dikumpulkan: 12 Oktober 2026 · 14.30</small>
                    </div>
                </div>

                @if (!$isTeacher)
                    <div
                        style="margin-top: 1.5rem; padding: 1.2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <h3 style="margin-top:0;">File yang Dikumpulkan:</h3>
                        <p style="margin-bottom:0; font-size: 0.95rem;">📄 <b>Refleksi_{{ $userName }}.pdf</b>
                            (Terkirim)</p>
                    </div>
                @endif
            </section>
        </div>

        <!-- PANEL KANAN: STATUS NILAI & INTERAKSI -->
        <aside>
            <!-- STATUS PENILAIAN -->
            <section class="panel fade-panel" id="panel-nilai">
                <h2>Status Penilaian</h2>

                <!-- Tampilan untuk Tab Kelompok -->
                <div id="status-kelompok" class="status-box">
                    <strong style="color: #64748b; font-size: 0.95rem;">Nilai Kelompok:</strong>
                    <div class="big-score">- / 100</div>
                    <span class="badge yellow" style="font-size: 0.8rem;">Menunggu Pengumpulan</span>
                </div>

                <!-- Tampilan untuk Tab Individu -->
                <div id="status-individu" class="status-box graded hidden-panel">
                    <strong style="color: #64748b; font-size: 0.95rem;">Nilai Akhir:</strong>
                    <div class="big-score">92 / 100</div>
                    <span class="badge green" style="font-size: 0.8rem;">Tuntas</span>
                </div>
            </section>

            <!-- RUANG DISKUSI KELOMPOK (Hanya muncul di tab kelompok) -->
            <section class="panel fade-panel" id="panel-diskusi">
                <h2>Ruang Diskusi Kelompok</h2>

                @if ($isTeacher)
                    <div
                        style="background: #f8fafc; padding: 0.8rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px dashed #cbd5e1;">
                        <p style="margin:0; font-size:0.85rem; color:#475569;">Pantauan Guru: Kelompok Daun Cerdas</p>
                    </div>
                @endif

                <!-- Obrolan Siswa (Chat Bubble biasa) -->
                <div class="chat-bubble">
                    <b>Alya</b>
                    <p>Aku akan mencatat suhu pagi dan malam. Siapa yang membuat garis bilangan?</p>
                </div>
                <div class="chat-bubble">
                    <b>Bima</b>
                    <p>Biar aku yang gambar garis bilangannya, nanti aku foto dan kirim ke sini.</p>
                </div>

                @if ($isTeacher)
                    <!-- Balasan Guru (Style Forum) -->
                    <div class="reply">
                        <b>Bu Ratna · Guru</b>
                        <p>Bagus sekali pembagian tugasnya. Jangan lupa perhatikan tanda negatifnya ya.</p>
                    </div>

                    <form style="margin-top: 1.5rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                        <div class="field">
                            <textarea class="answer-input" required placeholder="Berikan arahan untuk kelompok ini..."></textarea>
                        </div>
                        <button class="btn-primary" type="submit">Kirim Pesan ke Kelompok</button>
                    </form>
                @else
                    <form style="margin-top: 1.5rem;">
                        <div class="field">
                            <textarea class="answer-input" required placeholder="Ketik pesan untuk teman sekelompokmu..."></textarea>
                        </div>
                        <button class="btn-primary" type="submit">Kirim Pesan</button>
                    </form>
                @endif
            </section>

            <!-- FEEDBACK / KOMENTAR (Hanya muncul di tab individu) -->
            <section class="panel fade-panel hidden-panel" id="panel-feedback">
                <h2>Komentar Guru</h2>

                <!-- Komentar Guru (Style Forum) -->
                <div class="reply" style="border-left-color: #ca8a04;">
                    <b>Bu Ratna · Guru</b>
                    <p>Refleksi yang sangat bagus! Penjelasanmu tentang beda penjumlahan dan perkalian sangat mudah
                        dipahami. Contoh soal buatanmu juga kreatif dan menantang.</p>
                </div>

                @if ($isTeacher)
                    <form style="margin-top: 1rem; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                        <textarea class="answer-input" placeholder="Edit atau tambah komentar nilai..."></textarea>
                        <button class="btn-primary" style="margin-top:0.5rem; background: #0f172a;">Simpan
                            Perubahan</button>
                    </form>
                @endif
            </section>
        </aside>
    </div>

    <script>
        function switchTab(tabName) {
            // Update styling tombol tab
            document.getElementById('tab-kelompok').classList.remove('selected');
            document.getElementById('tab-individu').classList.remove('selected');
            document.getElementById('tab-' + tabName).classList.add('selected');

            // Logic Kiri (Konten Tugas)
            const kontenKelompok = document.getElementById('content-kelompok');
            const kontenIndividu = document.getElementById('content-individu');

            // Logic Kanan (Status & Diskusi)
            const statusKelompok = document.getElementById('status-kelompok');
            const statusIndividu = document.getElementById('status-individu');
            const panelDiskusi = document.getElementById('panel-diskusi');
            const panelFeedback = document.getElementById('panel-feedback');

            if (tabName === 'kelompok') {
                kontenIndividu.classList.add('hidden-panel');
                statusIndividu.classList.add('hidden-panel');
                panelFeedback.classList.add('hidden-panel');

                setTimeout(() => {
                    kontenKelompok.classList.remove('hidden-panel');
                    statusKelompok.classList.remove('hidden-panel');
                    panelDiskusi.classList.remove('hidden-panel');
                }, 150);
            } else {
                kontenKelompok.classList.add('hidden-panel');
                statusKelompok.classList.add('hidden-panel');
                panelDiskusi.classList.add('hidden-panel');

                setTimeout(() => {
                    kontenIndividu.classList.remove('hidden-panel');
                    statusIndividu.classList.remove('hidden-panel');
                    panelFeedback.classList.remove('hidden-panel');
                }, 150);
            }
        }
    </script>
@endsection

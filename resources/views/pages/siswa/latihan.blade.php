@extends('layouts.app')

@section('title', 'E-LKPD Interaktif | MathLeaf')
@section('header_title', 'E-LKPD Interaktif')

@section('content')
    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
        }

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

        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        .badge {
            display: inline-block;
            background: var(--ml-green-light);
            color: var(--ml-green);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .panel h2 {
            margin: 0 0 0.5rem 0;
            font-size: 1.25rem;
            color: #0f172a;
        }

        .panel p {
            color: #475569;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        /* Number Line Styling */
        .number-line {
            display: flex;
            justify-content: space-between;
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
        }

        .number-line span {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            color: #475569;
            text-align: center;
        }

        .number-line span.highlight {
            background: var(--ml-green);
            color: white;
        }

        /* Buttons & Inputs */
        .practice-options {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-outline:hover,
        .btn-outline.selected {
            border-color: var(--ml-green);
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .answer-input {
            width: 100%;
            max-width: 300px;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-top: 0.5rem;
            font-size: 1rem;
            outline: none;
        }

        .answer-input:focus {
            border-color: var(--ml-green);
        }

        /* Drag & Sort Elements */
        .drag-zone {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .drag-item {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
        }

        .drag-item button {
            background: #e2e8f0;
            border: none;
            padding: 0.6rem 0.8rem;
            cursor: pointer;
            font-weight: bold;
            color: #475569;
            transition: background 0.2s;
        }

        .drag-item button:hover {
            background: #cbd5e1;
        }

        .drag-item strong {
            padding: 0.5rem 1rem;
            min-width: 40px;
            text-align: center;
            font-size: 1.1rem;
        }

        .btn-primary {
            background: var(--ml-green);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            margin-top: 1rem;
        }

        .btn-primary:hover {
            background: var(--ml-green-hover);
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
                RUANG BERTUMBUHMU
            </span>
            <h1>Eksplorasi, Coba, dan Temukan!</h1>
            <p>Tahap 2 · Eksplorasi Awal. Latihan bilangan melalui masalah di sekitarmu.</p>
        </div>
    </div>

    <form id="lkpd-form" action="#">
        <section class="panel practice-panel">
            <span class="badge">01 · Garis bilangan</span>
            <h2>Bergerak pada garis bilangan</h2>
            <div class="number-line">
                <span>-6</span><span>-5</span><span>-4</span><span class="highlight">-3</span>
                <span>-2</span><span>-1</span><span>0</span><span>1</span><span class="highlight">2</span>
                <span>3</span><span>4</span><span>5</span><span>6</span>
            </div>
            <p>Mulai dari −3, lalu bergerak 5 langkah ke kanan. Di bilangan berapa kamu berhenti?</p>
            <div class="practice-options">
                <button class="btn-outline" data-practice-choice="-8" type="button">-8</button>
                <button class="btn-outline" data-practice-choice="-2" type="button">-2</button>
                <button class="btn-outline" data-practice-choice="2" type="button">2</button>
                <button class="btn-outline" data-practice-choice="8" type="button">8</button>
            </div>
        </section>

        <section class="panel practice-panel">
            <span class="badge">02 · Operasi bilangan & suhu</span>
            <h2>Suhu di pegunungan</h2>
            <p>Suhu mula-mula −4°C dan turun 2°C per jam selama 4 jam. Berapa suhu akhirnya?</p>
            <input type="text" class="answer-input" placeholder="Tulis angka, contoh: -12" required />
        </section>

        <section class="panel practice-panel">
            <span class="badge">03 · Pecahan</span>
            <h2>Berbagi pizza</h2>
            <p>Alya memakan ½ pizza dan Bima ¼ pizza. Berapa bagian yang dimakan bersama?</p>
            <input type="text" class="answer-input" placeholder="Tulis bentuk a/b" required />
        </section>

        <section class="panel practice-panel">
            <span class="badge">04 · Transaksi untung-rugi</span>
            <h2>Toko buku kecil</h2>
            <p>12 buku dibeli Rp8.000 per buku dan dijual Rp10.000 per buku. Berapa keuntungan totalnya?</p>
            <input type="number" class="answer-input" placeholder="Tulis angka tanpa Rp atau titik" required />
        </section>

        <section class="panel practice-panel">
            <span class="badge">05 · Urutkan bilangan</span>
            <h2>Dari yang terkecil ke terbesar</h2>
            <p>Susun kartu bilangan dengan tombol panah.</p>
            <div class="drag-zone" id="sort-numbers">
                <div class="drag-item">
                    <button type="button">‹</button><strong>3</strong><button type="button">›</button>
                </div>
                <div class="drag-item">
                    <button type="button">‹</button><strong>-2</strong><button type="button">›</button>
                </div>
                <div class="drag-item">
                    <button type="button">‹</button><strong>0</strong><button type="button">›</button>
                </div>
                <div class="drag-item">
                    <button type="button">‹</button><strong>-5</strong><button type="button">›</button>
                </div>
                <div class="drag-item">
                    <button type="button">‹</button><strong>1</strong><button type="button">›</button>
                </div>
            </div>
        </section>

        <button class="btn-primary" id="check-practice" type="button"
            onclick="alert('Fungsi periksa jawaban akan diproses oleh JavaScript (mathleaf.js)')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            Periksa Jawaban
        </button>
    </form>

    <script>
        // Script sederhana untuk interaksi tombol pilihan ganda (Soal 01)
        document.querySelectorAll('.practice-options .btn-outline').forEach(button => {
            button.addEventListener('click', function() {
                // Hapus kelas selected dari semua tombol
                let siblings = this.parentElement.querySelectorAll('.btn-outline');
                siblings.forEach(btn => btn.classList.remove('selected'));
                // Tambahkan kelas selected ke tombol yang diklik
                this.classList.add('selected');
            });
        });
    </script>
@endsection

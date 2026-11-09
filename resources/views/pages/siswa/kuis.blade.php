@extends('layouts.app')

@section('title', 'Kuis & Evaluasi | MathLeaf')
@section('header_title', 'Kuis & Evaluasi')

@section('content')
    @php
        $userName = Auth::user()->name ?? 'Karin';

        // Ikon SVG yang dipakai di halaman ini (dipanggil lewat {!! $icon['nama'] !!})
        $svg = fn(string $paths, string $class = 'ic') => '<svg class="' .
            $class .
            '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' .
            $paths .
            '</svg>';

        $icon = [
            'award' => $svg('<circle cx="12" cy="8" r="6"></circle><path d="m8 13-2 8 6-3 6 3-2-8"></path>'),
            'available' => $svg(
                '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>',
            ),
            'history' => $svg(
                '<path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>',
            ),
            'pencil' => $svg(
                '<path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>',
            ),
            'questions' => $svg(
                '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>',
            ),
            'clock' => $svg('<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>'),
            'repeat' => $svg(
                '<polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path>',
            ),
            'target' => $svg(
                '<circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle>',
            ),
            'arrow' => $svg(
                '<line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline>',
            ),
            'check' => $svg('<polyline points="20 6 9 17 4 12"></polyline>'),
            'retry' => $svg(
                '<polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>',
            ),
            'clipboard' => $svg(
                '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>',
            ),
        ];
    @endphp

    <style>
        /* ==========================================================
                   Kuis & Evaluasi — satu bahasa desain dengan Materi & Forum
                   ========================================================== */
        .app-content {
            font-size: 11.2px;
            overflow-x: visible;
        }

        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-soft: #dff3e5;
            --ml-green-border: #bbf0cb;
        }

        .ic {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        /* ---------- Header halaman ---------- */
        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            margin-bottom: .4rem;
            color: var(--ml-green);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .page-heading h1 {
            margin: 0 0 .4rem;
            color: #14301f;
            font-size: clamp(1.44rem, 2.4vw, 1.84rem);
            font-weight: 800;
            line-height: 1.2;
        }

        .page-heading p {
            margin: 0;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* ---------- Tab filter ---------- */
        .filter-tabs {
            display: flex;
            gap: .5rem;
            width: max-content;
            max-width: 100%;
            margin-bottom: 1.5rem;
            padding: .45rem;
            overflow-x: auto;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
        }

        .tab-btn {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .7rem 1.2rem;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #64748b;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            transition: background .2s, color .2s;
        }

        .tab-btn:hover {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .tab-btn.selected {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .tab-count {
            padding: .1rem .55rem;
            border-radius: 999px;
            background: var(--ml-green);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
        }

        /* ---------- Alert ---------- */
        .alert {
            margin-bottom: 1.25rem;
            padding: .85rem 1.1rem;
            border-radius: 12px;
            border-left: 5px solid;
            font-size: 0.88rem;
            line-height: 1.6;
        }

        .alert-error {
            background: #fff7e6;
            border-color: #f59e0b;
            color: #8b5a12;
        }

        .alert-success {
            background: var(--ml-green-light);
            border-color: var(--ml-green);
            color: #1f4d33;
        }

        /* ---------- Badge ---------- */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            margin-bottom: .8rem;
            padding: .25rem .7rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: .03em;
        }

        .badge.green {
            background: var(--ml-green-soft);
            color: var(--ml-green);
        }

        .badge.orange {
            background: #ffedd5;
            color: #c2410c;
        }

        .badge.blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        /* ---------- Kartu kuis ---------- */
        .quiz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.25rem;
        }

        .quiz-card {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #dfe9e2;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            transition: transform .25s cubic-bezier(.2, .8, .2, 1), box-shadow .25s, border-color .25s;
            animation: miUp .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        .quiz-card:nth-child(2) {
            animation-delay: 60ms;
        }

        .quiz-card:nth-child(3) {
            animation-delay: 120ms;
        }

        .quiz-card:nth-child(4) {
            animation-delay: 180ms;
        }

        .quiz-card:nth-child(n+5) {
            animation-delay: 240ms;
        }

        .quiz-card:hover {
            transform: translateY(-4px);
            border-color: #b5d9c1;
            box-shadow: 0 12px 24px rgba(8, 101, 52, .08);
        }

        .card-header {
            position: relative;
            padding: 1.4rem 1.4rem 1.1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-header h3 {
            margin: 0;
            color: #14301f;
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.35;
        }

        .card-header.has-icon h3 {
            padding-right: 3.2rem;
        }

        .quiz-type-icon {
            position: absolute;
            top: 1.4rem;
            right: 1.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 12px;
        }

        .icon-ujian {
            background: #fee2e2;
            color: #dc2626;
        }

        .icon-latihan {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .icon-kuis {
            background: #e0f2fe;
            color: #0284c7;
        }

        .card-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 1rem;
            padding: 1.2rem 1.4rem;
        }

        .quiz-meta {
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: .6rem;
            color: #475569;
            font-size: 0.88rem;
            line-height: 1.4;
        }

        .meta-item .ic {
            width: 16px;
            height: 16px;
            color: var(--ml-green);
        }

        .meta-item.is-pass {
            color: #166534;
            font-weight: 700;
        }

        .meta-item.is-retry {
            color: #9a5316;
            font-weight: 700;
        }

        .meta-item.is-pass .ic,
        .meta-item.is-retry .ic {
            color: inherit;
        }

        /* Nilai di riwayat */
        .score-box {
            margin: .4rem 0;
            text-align: center;
        }

        .score-label {
            display: block;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .score-value {
            color: var(--ml-green);
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .score-max {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .card-footer {
            margin-top: auto;
            padding: 1.1rem 1.4rem;
            background: #f6fbf8;
            border-top: 1px solid #edf2f7;
        }

        /* ---------- Tombol ---------- */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            padding: .8rem 1rem;
            border: 0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: background .2s, box-shadow .2s, transform .15s;
        }

        .btn-action:active {
            transform: scale(.97);
        }

        .btn-action .ic {
            width: 16px;
            height: 16px;
        }

        .btn-start {
            background: var(--ml-green);
            color: #ffffff;
        }

        .btn-start:hover {
            background: var(--ml-green-hover);
            box-shadow: 0 6px 16px rgba(8, 101, 52, .25);
            color: #ffffff;
        }

        .btn-view {
            background: var(--ml-green-light);
            color: var(--ml-green);
        }

        .btn-view:hover {
            background: var(--ml-green-soft);
        }

        /* ---------- Empty state ---------- */
        .empty-state {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 3.5rem 2rem;
            text-align: center;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-radius: 16px;
        }

        .empty-state-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            margin-bottom: 1rem;
            border-radius: 50%;
            background: var(--ml-green-soft);
            color: var(--ml-green);
        }

        .empty-state-icon .ic {
            width: 26px;
            height: 26px;
        }

        .empty-state h3 {
            margin: 0 0 .5rem;
            color: #14301f;
            font-size: 1.25rem;
            font-weight: 800;
        }

        .empty-state p {
            max-width: 460px;
            margin: 0;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .empty-state .btn-action {
            width: auto;
            min-width: 220px;
            margin-top: 1.25rem;
        }

        /* ---------- Animasi tab ---------- */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn .3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes miUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @media (max-width: 640px) {
            .quiz-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .quiz-card,
            .tab-content.active {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow">{!! $icon['award'] !!} RUANG BERTUMBUHMU</span>
            <h1>Saatnya Uji Pemahamanmu, {{ $userName }}!</h1>
            <p>Bukan tentang sempurna, tapi tentang sejauh mana kamu sudah bertumbuh.</p>
        </div>
    </div>

    <div class="filter-tabs">
        <button type="button" class="tab-btn selected" id="tab-btn-tersedia" onclick="switchTab('tersedia')">
            {!! $icon['available'] !!}
            Kuis Tersedia
            <span class="tab-count">{{ $available->count() }}</span>
        </button>
        <button type="button" class="tab-btn" id="tab-btn-selesai" onclick="switchTab('selesai')">
            {!! $icon['history'] !!}
            Riwayat Pengerjaan
        </button>
    </div>

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Kuis benar-benar diambil dari database kelas siswa -->
    <div id="tab-tersedia" class="tab-content active">
        <div class="quiz-grid">
            @forelse ($available as $assessment)
                <div class="quiz-card">
                    <div class="card-header has-icon">
                        <span
                            class="badge {{ $assessment->type === 'ujian' ? 'orange' : ($assessment->type === 'latihan' ? 'green' : 'blue') }}">
                            {{ ucfirst($assessment->type) }}
                        </span>
                        <h3>{{ $assessment->title }}</h3>
                        <div
                            class="quiz-type-icon {{ $assessment->type === 'ujian' ? 'icon-ujian' : ($assessment->type === 'latihan' ? 'icon-latihan' : 'icon-kuis') }}">
                            {!! $icon['pencil'] !!}</div>
                    </div>
                    <div class="card-body">
                        <div class="quiz-meta">
                            <div class="meta-item">{!! $icon['questions'] !!} {{ $assessment->questions_count }} soal pilihan
                                ganda</div>
                            <div class="meta-item">{!! $icon['clock'] !!}
                                {{ $assessment->time_limit_minutes ? $assessment->time_limit_minutes . ' menit (alokasi)' : 'Tanpa alokasi waktu' }}
                            </div>
                            <div class="meta-item">{!! $icon['repeat'] !!} Maksimal
                                {{ max(1, (int) $assessment->max_attempts) }} percobaan</div>
                            <div class="meta-item">{!! $icon['target'] !!} KKM :
                                {{ $assessment->passing_score ?? 70 }}/100</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('classroom.quiz.take', $assessment) }}" class="btn-action btn-start">Mulai
                            {{ ucfirst($assessment->type) }} {!! $icon['arrow'] !!}</a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <span class="empty-state-icon">{!! $icon['clipboard'] !!}</span>
                    <h3>Belum ada kuis yang tersedia</h3>
                    <p>Gabung ke kelas menggunakan kode guru, lalu kuis yang diterbitkan akan muncul di sini.</p>
                    <a href="{{ route('classroom.home') }}" class="btn-action btn-start">Lihat Kelas Saya</a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Riwayat pengerjaan berasal dari tabel assessment_attempts -->
    <div id="tab-selesai" class="tab-content">
        <div class="quiz-grid">
            @forelse ($attempts as $attempt)
                @php $passed = (float) $attempt->score >= (int) ($attempt->assessment?->passing_score ?? 70); @endphp
                <div class="quiz-card">
                    <div class="card-header">
                        <span class="badge green">Selesai dikerjakan</span>
                        <h3>{{ $attempt->assessment?->title ?? 'Kuis' }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="score-box">
                            <span class="score-label">Nilai</span>
                            <strong class="score-value">{{ number_format((float) $attempt->score, 1) }}</strong>
                            <span class="score-max"> / 100</span>
                        </div>
                        <div class="meta-item">{{ $attempt->submitted_at?->format('d M Y H:i') ?? '-' }} · Percobaan
                            {{ $attempt->attempt_number }}</div>
                        <div class="meta-item {{ $passed ? 'is-pass' : 'is-retry' }}">
                            {!! $passed ? $icon['check'] : $icon['retry'] !!}
                            {{ $passed ? 'Tuntas' : 'Belum tuntas, lihat saran remedial' }}
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('siswa.quiz.result', $attempt) }}" class="btn-action btn-view">Lihat Hasil dan
                            Pembahasan</a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <span class="empty-state-icon">{!! $icon['history'] !!}</span>
                    <h3>Belum ada kuis yang dikerjakan</h3>
                    <p>Riwayat dan nilai akan muncul setelah kamu mengirim jawaban kuis.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Hapus status active/selected dari semua tab
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('selected'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Tambahkan status active ke tab yang diklik
            document.getElementById('tab-btn-' + tabId).classList.add('selected');
            document.getElementById('tab-' + tabId).classList.add('active');
        }
    </script>
@endsection

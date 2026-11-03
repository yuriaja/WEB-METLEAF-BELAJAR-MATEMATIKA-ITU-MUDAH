@extends('layouts.app')

@section('title', 'Kuis & Evaluasi | MathLeaf')
@section('header_title', 'Kuis & Evaluasi')

@section('content')
    @php
        $userName = Auth::user()->name;
        $classMap = $classes->keyBy('id');
        $availableAssessments = $assessments->filter(
            fn($assessment) => $assessment->attempts->count() < $assessment->max_attempts,
        );
        $completedAssessments = $assessments->filter(fn($assessment) => $assessment->attempts->isNotEmpty());
        $activeClass = $activeClassId ? $classes->firstWhere('id', $activeClassId) : null;

        // Tampilan per jenis penilaian
        $typeMeta = [
            'latihan' => ['label' => 'Latihan', 'tone' => 0, 'sym' => 'x²'],
            'kuis' => ['label' => 'Kuis', 'tone' => 1, 'sym' => '?'],
            'ujian' => ['label' => 'Ujian', 'tone' => 2, 'sym' => 'A+'],
        ];
    @endphp

    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-soft: #dff3e5;
        }

        /* ---------- Heading ---------- */
        .qz-head {
            margin-bottom: 1.8rem;
        }

        .qz-head h1 {
            font-size: clamp(1.8rem, 3vw, 2.3rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .4rem;
            line-height: 1.2;
        }

        .qz-head p {
            margin: 0;
            color: #475569;
            font-size: 1.1rem;
        }

        .qz-alert {
            padding: .9rem 1.1rem;
            border-radius: 12px;
            margin-bottom: 1.2rem;
            font-size: 1.02rem;
            background: var(--ml-green-light);
            border: 1px solid #bfe0c9;
            color: #1f4d33;
            animation: qzUp .4s ease both;
        }

        @keyframes qzUp {
            from {
                opacity: 0;
                transform: translateY(18px) scale(.97);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* ---------- Tab ---------- */
        .qz-tabs {
            display: inline-flex;
            gap: .35rem;
            padding: 6px;
            margin-bottom: 1.6rem;
            background: #e3f3e7;
            border-radius: 999px;
            max-width: 100%;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .qz-tabs::-webkit-scrollbar {
            display: none;
        }

        .qz-tab {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            white-space: nowrap;
            border: 0;
            background: transparent;
            color: #3f5f4b;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            padding: .65rem 1.2rem;
            border-radius: 999px;
            cursor: pointer;
            transition: background .25s, color .25s, box-shadow .25s, transform .15s;
        }

        .qz-tab:hover {
            background: rgba(8, 101, 52, .1);
            color: var(--ml-green);
        }

        .qz-tab:active {
            transform: scale(.96);
        }

        .qz-tab.selected {
            background: var(--ml-green);
            color: #fff;
            box-shadow: 0 4px 12px rgba(8, 101, 52, .3);
        }

        .qz-tab .cnt {
            min-width: 1.5rem;
            padding: .1rem .5rem;
            border-radius: 999px;
            font-size: .8rem;
            text-align: center;
            background: rgba(8, 101, 52, .12);
            color: var(--ml-green);
            transition: background .25s, color .25s;
        }

        .qz-tab.selected .cnt {
            background: rgba(255, 255, 255, .22);
            color: #fff;
        }

        .qz-pane {
            display: none;
        }

        .qz-pane.active {
            display: block;
        }

        /* ---------- Kartu ---------- */
        .qz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(320px, 100%), 1fr));
            gap: 1.4rem;
            align-items: stretch;
        }

        .qz-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid #dfe9e2;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            transition: transform .25s ease, box-shadow .25s ease;
            animation: qzUp .55s cubic-bezier(.2, .8, .2, 1) both;
            animation-delay: var(--d, 0ms);
        }

        .qz-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .1);
        }

        .qz-cover {
            position: relative;
            padding: 1.2rem 1.4rem 1.3rem;
            min-height: 132px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            overflow: hidden;
        }

        .qz-tone-0 {
            background: linear-gradient(135deg, #e3f5e9, #c8ebd3);
        }

        .qz-tone-1 {
            background: linear-gradient(135deg, #fff6d6, #fde9a0);
        }

        .qz-tone-2 {
            background: linear-gradient(135deg, #ffeadb, #ffd2b0);
        }

        .qz-tone-done {
            background: linear-gradient(135deg, #e9f7ee, #d4efdc);
        }

        .qz-sym {
            position: absolute;
            right: -4px;
            top: 2px;
            font-size: 4.4rem;
            font-weight: 800;
            color: rgba(8, 101, 52, .12);
            transform: rotate(-8deg);
            pointer-events: none;
            user-select: none;
        }

        .qz-chip {
            position: relative;
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(255, 255, 255, .8);
            color: #2f5a42;
            border-radius: 999px;
            padding: .28rem .8rem;
            font-size: .9rem;
            font-weight: 800;
            margin-bottom: .6rem;
        }

        .qz-cover h2 {
            position: relative;
            margin: 0;
            font-size: 1.4rem;
            line-height: 1.25;
            font-weight: 800;
            color: #14301f;
            overflow-wrap: anywhere;
        }

        .qz-class {
            position: relative;
            margin: .35rem 0 0;
            font-size: .98rem;
            color: #3f6b50;
            font-weight: 600;
        }

        .qz-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.2rem 1.4rem;
        }

        .qz-stats {
            display: flex;
            flex-wrap: wrap;
            gap: .55rem;
        }

        .qz-stat {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: var(--ml-green-light);
            color: var(--ml-green);
            border: 1px solid #cfe8d6;
            border-radius: 999px;
            padding: .35rem .85rem;
            font-size: .95rem;
            font-weight: 700;
        }

        .qz-note {
            margin: 0;
            color: #475569;
            font-size: 1rem;
        }

        .qz-note b {
            color: #14301f;
        }

        .qz-foot {
            margin-top: auto;
            padding: 0 1.4rem 1.4rem;
        }

        .qz-btn {
            display: inline-flex;
            width: 100%;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border: 0;
            border-radius: 10px;
            padding: .8rem 1.2rem;
            background: var(--ml-green);
            color: #fff;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            transition: background .2s, transform .15s, box-shadow .2s;
        }

        .qz-btn:hover {
            background: var(--ml-green-hover);
            box-shadow: 0 6px 16px rgba(8, 101, 52, .25);
        }

        .qz-btn:active {
            transform: scale(.97);
        }

        .qz-btn.soft {
            background: var(--ml-green-light);
            color: var(--ml-green);
            border: 1.5px solid #cfe8d6;
        }

        .qz-btn.soft:hover {
            background: var(--ml-green-soft);
            box-shadow: none;
        }

        /* ---------- Cincin nilai ---------- */
        .qz-score {
            display: flex;
            align-items: center;
            gap: 1.1rem;
        }

        .qz-ring {
            position: relative;
            width: 96px;
            height: 96px;
            flex-shrink: 0;
        }

        .qz-ring svg {
            transform: rotate(-90deg);
        }

        .qz-ring .bg {
            stroke: #e3efe6;
        }

        .qz-ring .fg {
            stroke-linecap: round;
            stroke-dasharray: 213.6;
            animation: qzRing 1.1s cubic-bezier(.2, .8, .2, 1) both .2s;
        }

        .qz-ring.good .fg {
            stroke: #22a35a;
        }

        .qz-ring.mid .fg {
            stroke: #f59e0b;
        }

        .qz-ring.low .fg {
            stroke: #ef4444;
        }

        @keyframes qzRing {
            from {
                stroke-dashoffset: 213.6;
            }
        }

        .qz-ring strong {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: #14301f;
        }

        .qz-score small {
            display: block;
            color: #64748b;
            font-size: .85rem;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .qz-score span {
            color: #475569;
            font-size: 1rem;
        }

        /* ---------- Kosong ---------- */
        .qz-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem 1.5rem;
            background: #fff;
            border: 1px dashed #86c79a;
            border-radius: 18px;
            animation: qzUp .5s ease both;
        }

        .qz-empty svg {
            color: var(--ml-green);
            margin-bottom: .8rem;
        }

        .qz-empty h2 {
            margin: 0 0 .4rem;
            font-size: 1.3rem;
            color: #14301f;
        }

        .qz-empty p {
            margin: 0;
            color: #64748b;
            font-size: 1.05rem;
        }

        @media (prefers-reduced-motion: reduce) {

            .qz-card,
            .qz-empty,
            .qz-alert,
            .qz-ring .fg {
                animation: none !important;
            }

            .qz-card {
                transition: none;
            }
        }
    </style>

    <div class="qz-head">
        <h1>Kuis & Evaluasi</h1>
        <p>
            Halo, {{ $userName }}.
            @if ($activeClass)
                Kuis untuk kelas {{ $activeClass->name }}.
            @else
                Kuis dari semua kelas yang kamu ikuti.
            @endif
        </p>
    </div>

    @if (session('success'))
        <div role="status" class="qz-alert">{{ session('success') }}</div>
    @endif

    <div class="qz-tabs" role="tablist" aria-label="Filter kuis">
        <button type="button" class="qz-tab selected" id="tab-btn-tersedia" onclick="switchTab('tersedia')">
            Kuis tersedia <span class="cnt">{{ $availableAssessments->count() }}</span>
        </button>
        <button type="button" class="qz-tab" id="tab-btn-selesai" onclick="switchTab('selesai')">
            Riwayat pengerjaan <span class="cnt">{{ $completedAssessments->count() }}</span>
        </button>
    </div>

    {{-- ================= KUIS TERSEDIA ================= --}}
    <div id="tab-tersedia" class="qz-pane active">
        <div class="qz-grid">
            @forelse ($availableAssessments->values() as $i => $assessment)
                @php
                    $latestAttempt = $assessment->attempts->first();
                    $meta = $typeMeta[$assessment->type] ?? $typeMeta['latihan'];
                    $left = max(0, $assessment->max_attempts - $assessment->attempts->count());
                @endphp
                <article class="qz-card" style="--d: {{ $i * 80 }}ms">
                    <div class="qz-cover qz-tone-{{ $meta['tone'] }}">
                        <span class="qz-sym">{{ $meta['sym'] }}</span>
                        <span class="qz-chip">{{ $meta['label'] }}</span>
                        <h2>{{ $assessment->title }}</h2>
                        <p class="qz-class">{{ $classMap->get($assessment->class_id)?->name ?? 'Semua kelas' }}</p>
                    </div>
                    <div class="qz-body">
                        <div class="qz-stats">
                            <span class="qz-stat">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                                {{ $assessment->questions_count }} soal
                            </span>
                            <span class="qz-stat">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 7v5l3 2"></path>
                                </svg>
                                {{ $assessment->time_limit_minutes ? $assessment->time_limit_minutes . ' menit' : 'Tanpa batas waktu' }}
                            </span>
                        </div>
                        <p class="qz-note">
                            Percobaan tersisa: <b>{{ $left }}</b> dari {{ $assessment->max_attempts }}
                            @if ($latestAttempt)
                                <br>Nilai terakhir: <b>{{ number_format((float) $latestAttempt->score, 1) }}</b>
                            @endif
                        </p>
                    </div>
                    <div class="qz-foot">
                        <a href="{{ route('classroom.quiz.take', $assessment) }}" class="qz-btn">
                            {{ $latestAttempt ? 'Coba Lagi' : 'Mulai ' . $meta['label'] }}
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="qz-empty">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <h2>Belum ada kuis tersedia</h2>
                    <p>Kuis yang diterbitkan guru untuk kelasmu akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ================= RIWAYAT PENGERJAAN ================= --}}
    <div id="tab-selesai" class="qz-pane">
        <div class="qz-grid">
            @forelse ($completedAssessments->values() as $i => $assessment)
                @php
                    $latestAttempt = $assessment->attempts->first();
                    $meta = $typeMeta[$assessment->type] ?? $typeMeta['latihan'];
                    $score = (float) $latestAttempt->score;
                    $pct = max(0, min(100, $score));
                    $offset = round(213.6 * (1 - $pct / 100), 1);
                    $level = $score >= 75 ? 'good' : ($score >= 50 ? 'mid' : 'low');
                @endphp
                <article class="qz-card" style="--d: {{ $i * 80 }}ms">
                    <div class="qz-cover qz-tone-done">
                        <span class="qz-sym">✓</span>
                        <span class="qz-chip">{{ $meta['label'] }} · Selesai</span>
                        <h2>{{ $assessment->title }}</h2>
                        <p class="qz-class">{{ $classMap->get($assessment->class_id)?->name ?? 'Semua kelas' }}</p>
                    </div>
                    <div class="qz-body">
                        <div class="qz-score">
                            <div class="qz-ring {{ $level }}">
                                <svg width="96" height="96" viewBox="0 0 80 80" aria-hidden="true">
                                    <circle class="bg" cx="40" cy="40" r="34" fill="none"
                                        stroke-width="9">
                                    </circle>
                                    <circle class="fg" cx="40" cy="40" r="34" fill="none"
                                        stroke-width="9" style="stroke-dashoffset: {{ $offset }}"></circle>
                                </svg>
                                <strong>{{ number_format($score, 0) }}</strong>
                            </div>
                            <div>
                                <small>NILAI TERAKHIR</small>
                                <span>{{ number_format($score, 1) }} / 100</span>
                            </div>
                        </div>
                        <p class="qz-note">
                            Dikerjakan: <b>{{ $latestAttempt->submitted_at?->format('d M Y, H:i') ?? '—' }}</b><br>
                            Percobaan <b>{{ $latestAttempt->attempt_number }}</b> dari {{ $assessment->max_attempts }}
                        </p>
                    </div>
                    @if ($assessment->attempts->count() < $assessment->max_attempts)
                        <div class="qz-foot">
                            <a href="{{ route('classroom.quiz.take', $assessment) }}" class="qz-btn soft">Coba Lagi</a>
                        </div>
                    @endif
                </article>
            @empty
                <div class="qz-empty">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <h2>Belum ada riwayat pengerjaan</h2>
                    <p>Kuis yang sudah kamu kerjakan akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.qz-tab').forEach(function(button) {
                button.classList.toggle('selected', button.id === 'tab-btn-' + tabId);
            });
            // display none -> block memutar ulang animasi masuk kartu
            document.querySelectorAll('.qz-pane').forEach(function(pane) {
                pane.classList.toggle('active', pane.id === 'tab-' + tabId);
            });
        }
    </script>
@endsection

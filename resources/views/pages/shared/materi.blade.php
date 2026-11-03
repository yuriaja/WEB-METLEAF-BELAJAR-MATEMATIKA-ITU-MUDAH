@extends('layouts.app')

@section('title', 'Materi Pembelajaran | MathLeaf')
@section('header_title', 'Materi Pembelajaran')

@section('content')
    @php
        $isTeacher = Auth::check() && Auth::user()->role === 'guru';

        /*
         * DATA KELAS (sumber yang sama dengan dropdown kelas di sidebar).
         * Variabel di layouts.app tidak bisa dibaca dari section ini, jadi diambil ulang di sini.
         */
        $activeClassId = session('kelas_aktif_id');
        $userClasses = collect();

        if (Auth::check()) {
            $classQuery = \Illuminate\Support\Facades\DB::table('classes')
                ->join('users', 'classes.teacher_id', '=', 'users.id')
                ->select('classes.id', 'classes.name', 'classes.school_year', 'users.name as teacher_name');

            if ($isTeacher) {
                $classQuery->where('classes.teacher_id', Auth::id());
            } else {
                $classQuery
                    ->join('class_user', 'classes.id', '=', 'class_user.class_id')
                    ->where('class_user.user_id', Auth::id());
            }

            $userClasses = $classQuery->get();
        }

        $activeClass = $userClasses->firstWhere('id', $activeClassId);

        // Murid wajib memilih kelas dulu sebelum melihat materi. Guru boleh melihat "Semua Kelas".
        $needsClassPick = !$isTeacher && !$activeClass && $userClasses->isNotEmpty();
        $studentNoClass = !$isTeacher && $userClasses->isEmpty();

        // Teks tahun ajaran (di atas judul). Kosong = baris disembunyikan.
        $yearText = null;
        if ($activeClass) {
            if (!empty($activeClass->school_year)) {
                $yearText = 'Tahun Ajaran ' . $activeClass->school_year;
            }
        } else {
            $years = $userClasses->pluck('school_year')->filter()->unique()->values();
            if ($years->count() === 1) {
                $yearText = 'Tahun Ajaran ' . $years->first();
            } elseif ($years->count() > 1) {
                $yearText = 'Semua Tahun Ajaran';
            }
        }

        // Info kelas (di bawah judul): "Nama Guru - Nama Kelas"
        if ($activeClass) {
            $teacherName = $isTeacher ? Auth::user()->name : $activeClass->teacher_name ?? 'Guru';
            $classInfo = $teacherName . ' - ' . $activeClass->name;
        } elseif ($studentNoClass) {
            $classInfo = 'Kamu belum bergabung ke kelas manapun.';
        } elseif ($userClasses->isEmpty()) {
            $classInfo = 'Belum ada kelas. Buat kelas terlebih dahulu di Manajemen Kelas.';
        } else {
            $classInfo = 'Menampilkan materi dari semua kelas yang kamu ajar.';
        }
    @endphp

    <style>
        /* ---------- Heading ---------- */
        .page-heading {
            margin-bottom: 2rem;
        }

        .page-heading .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 800;
            color: #086534;
            font-size: 0.95rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.6rem;
        }

        .page-heading h1 {
            font-size: clamp(1.8rem, 3vw, 2.4rem);
            color: #0f172a;
            margin: 0 0 0.6rem;
            line-height: 1.2;
        }

        .page-heading .class-info {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: #475569;
            font-size: 1.15rem;
            font-weight: 500;
            margin: 0;
        }

        .page-heading .class-info svg {
            color: #086534;
            flex-shrink: 0;
        }

        /* ---------- Filter & Search ---------- */
        .filters-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .filter-tabs {
            display: inline-flex;
            gap: 0.35rem;
            padding: 6px;
            background: #e3f3e7;
            border-radius: 999px;
            overflow-x: auto;
            max-width: 100%;
            scrollbar-width: none;
        }

        .filter-tabs::-webkit-scrollbar {
            display: none;
        }

        .filter-tabs button[data-filter] {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            white-space: nowrap;
            border: 0;
            background: transparent;
            color: #3f5f4b;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            padding: 0.65rem 1.2rem;
            border-radius: 999px;
            cursor: pointer;
            transition: background .25s ease, color .25s ease, box-shadow .25s ease, transform .15s ease;
        }

        .filter-tabs button[data-filter]:hover {
            background: rgba(8, 101, 52, 0.1);
            color: #086534;
        }

        .filter-tabs button[data-filter]:active {
            transform: scale(0.96);
        }

        .filter-tabs button[data-filter].selected {
            background: #086534;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(8, 101, 52, 0.3);
        }

        .filter-tabs .tab-count {
            min-width: 1.5rem;
            padding: 0.1rem 0.5rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            text-align: center;
            background: rgba(8, 101, 52, 0.12);
            color: #086534;
            transition: background .25s ease, color .25s ease;
        }

        .filter-tabs button[data-filter].selected .tab-count {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
        }

        .search-action-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex: 1;
            max-width: 600px;
        }

        .search-input-large {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
            flex: 1;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-input-large:focus-within {
            border-color: #086534;
            box-shadow: 0 0 0 3px rgba(8, 101, 52, 0.12);
        }

        .search-input-large input {
            border: none;
            background: transparent;
            width: 100%;
            font-size: 1.05rem;
            color: #0f172a;
            outline: none;
        }

        .search-input-large svg {
            color: #64748b;
            flex-shrink: 0;
        }

        .btn-add-material {
            background-color: #086534;
            color: #ffffff;
            border: none;
            padding: 0.8rem 1.3rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s;
            white-space: nowrap;
        }

        .btn-add-material:hover {
            background-color: #064d27;
        }

        /* ---------- Kartu materi: font diperbesar ---------- */
        .material-grid .material-label {
            font-size: 0.8rem;
        }

        .material-grid .material-meta {
            font-size: 0.95rem;
            gap: 1rem;
        }

        .material-grid .material-meta svg {
            width: 16px;
            height: 16px;
        }

        .material-grid .material-body h3 {
            font-size: 1.3rem;
            line-height: 1.3;
            margin: 0.5rem 0 0.4rem;
        }

        .material-grid .material-body p {
            font-size: 1rem;
            line-height: 1.55;
        }

        .material-grid .material-bottom {
            font-size: 0.95rem;
        }

        .material-grid .material-bottom a {
            font-size: 1rem;
            font-weight: 700;
        }

        .material-grid .progress-label {
            font-size: 0.95rem;
        }

        .material-item {
            position: relative;
        }

        .material-item .material-card {
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .material-item:hover .material-card {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.1);
        }

        .teacher-card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed #e2e8f0;
        }

        .btn-small-outline {
            flex: 1;
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 0.5rem;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
        }

        .btn-small-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        /* ---------- Animasi filter ---------- */
        .material-item {
            transition: opacity .18s ease, transform .18s ease;
        }

        .material-item.is-leaving {
            opacity: 0;
            transform: scale(0.94) translateY(6px);
            pointer-events: none;
        }

        .material-item.is-hidden {
            display: none !important;
        }

        @keyframes mlCardIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes mlBarGrow {
            from {
                width: 0;
            }
        }

        .material-item.is-entering {
            animation: mlCardIn .45s cubic-bezier(.2, .8, .2, 1) both;
            animation-delay: var(--d, 0ms);
        }

        .material-item.is-entering .progress-track>div {
            animation: mlBarGrow .9s cubic-bezier(.2, .8, .2, 1) both;
            animation-delay: calc(var(--d, 0ms) + 200ms);
        }

        .empty-state-filter {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem 1rem;
            color: #64748b;
            font-size: 1.1rem;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
        }

        .empty-state-filter a {
            color: #086534;
            font-weight: 700;
        }

        /* ---------- Murid belum memilih kelas: petunjuk ke sidebar ---------- */
        .pick-hint {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.4rem 1.6rem;
            background: #ffffff;
            border: 1px dashed #86c79a;
            border-radius: 14px;
            color: #475569;
            font-size: 1.05rem;
            animation: mlCardIn .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        .pick-hint b {
            display: block;
            color: #0f172a;
            font-size: 1.15rem;
            margin-bottom: 2px;
        }

        .pick-hint .ph-arrow {
            color: #086534;
            flex-shrink: 0;
            animation: mlBobX 1.2s ease-in-out infinite;
        }

        @keyframes mlBobX {

            0%,
            100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(-7px);
            }
        }

        @keyframes mlBobY {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        @keyframes mlPulseRing {
            0% {
                box-shadow: 0 0 0 0 rgba(253, 224, 71, .8);
            }

            70% {
                box-shadow: 0 0 0 14px rgba(253, 224, 71, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(253, 224, 71, 0);
            }
        }

        @keyframes mlNudge {

            0%,
            100% {
                transform: translateX(0);
            }

            20% {
                transform: translateX(-5px);
            }

            40% {
                transform: translateX(5px);
            }

            60% {
                transform: translateX(-3px);
            }

            80% {
                transform: translateX(3px);
            }
        }

        @keyframes mlPopIn {
            from {
                opacity: 0;
                transform: translateX(-12px) scale(.9);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes mlToastIn {
            from {
                opacity: 0;
                transform: translateY(-14px) scale(.96);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes mlToastOut {
            to {
                opacity: 0;
                transform: translateY(-10px) scale(.96);
            }
        }

        /* Sorotan pada dropdown kelas di sidebar */
        .sidebar-class.ml-attention {
            border-radius: 12px;
            outline: 2px solid #fde047;
            outline-offset: 3px;
            animation: mlPulseRing 1.4s ease-out 6, mlNudge .6s ease .2s 1;
        }

        /* Balon penunjuk "Pilih kelas di sini" */
        .ml-callout {
            position: fixed;
            z-index: 9997;
            display: flex;
            align-items: center;
            gap: .5rem;
            background: #fde047;
            color: #3b2f00;
            font-weight: 800;
            font-size: .95rem;
            padding: .6rem .95rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .22);
            cursor: pointer;
            white-space: nowrap;
            animation: mlPopIn .45s cubic-bezier(.2, .8, .2, 1) both, mlBobX 1.2s ease-in-out .5s infinite;
        }

        .ml-callout::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            margin-top: -8px;
            border-style: solid;
            border-width: 8px 8px 8px 0;
            border-color: transparent #fde047 transparent transparent;
        }

        .ml-callout.below {
            animation: mlPopIn .45s cubic-bezier(.2, .8, .2, 1) both, mlBobY 1.2s ease-in-out .5s infinite;
        }

        .ml-callout.below::before {
            left: 22px;
            top: -8px;
            margin-top: 0;
            border-width: 0 8px 8px 8px;
            border-color: transparent transparent #fde047 transparent;
        }

        /* Notifikasi */
        .ml-toast {
            position: fixed;
            top: 84px;
            right: 24px;
            z-index: 9998;
            width: min(380px, calc(100vw - 24px));
            display: flex;
            gap: .8rem;
            align-items: flex-start;
            background: #ffffff;
            border: 1px solid #fde68a;
            border-left: 5px solid #f59e0b;
            border-radius: 12px;
            padding: .95rem 1rem;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .2);
            animation: mlToastIn .45s cubic-bezier(.2, .8, .2, 1) both;
        }

        .ml-toast.out {
            animation: mlToastOut .3s ease both;
        }

        .ml-toast .t-icon {
            color: #d97706;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .ml-toast b {
            display: block;
            font-size: 1.05rem;
            color: #0f172a;
        }

        .ml-toast .t-msg {
            display: block;
            font-size: .95rem;
            color: #475569;
            line-height: 1.45;
            margin-top: 2px;
        }

        .ml-toast .t-close {
            margin-left: auto;
            border: 0;
            background: transparent;
            color: #94a3b8;
            font-size: 1.3rem;
            line-height: 1;
            cursor: pointer;
            padding: 0 .2rem;
        }

        .ml-toast .t-close:hover {
            color: #475569;
        }

        @media (max-width: 768px) {
            .ml-toast {
                right: 12px;
                top: 72px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .material-item,
            .material-item.is-entering,
            .material-item.is-entering .progress-track>div,
            .pick-hint,
            .pick-hint .ph-arrow,
            .ml-callout,
            .ml-toast,
            .sidebar-class.ml-attention {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>

    @if (!$needsClassPick)
        <div class="page-heading">
            <div>
                @if ($yearText)
                    <span class="eyebrow">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        {{ $yearText }}
                    </span>
                @endif
                <h1>{{ $isTeacher ? 'Kelola Materi Pembelajaran' : 'Jelajahi Materi Pembelajaran' }}</h1>
                <p class="class-info">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>{{ $classInfo }}</span>
                </p>
            </div>
        </div>
    @endif

    @if ($needsClassPick)
        {{-- Pilihan kelas hanya di sidebar. Di sini cukup petunjuk, notifikasi, dan sorotan animasi ke dropdown kelas. --}}
        <div class="pick-hint">
            <svg class="ph-arrow" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"></path>
                <path d="m12 19-7-7 7-7"></path>
            </svg>
            <div>
                <b>Pilih kelas dulu</b>
                Buka menu kelas di sidebar, lalu pilih kelasmu.
            </div>
        </div>

        <script>
            (function() {
                var root = document.documentElement;
                var field = document.querySelector('.sidebar-class');
                if (!field) return;
                var select = field.querySelector('select');
                var toast = null,
                    callout = null,
                    timers = [];

                function later(fn, ms) {
                    var t = setTimeout(fn, ms);
                    timers.push(t);
                    return t;
                }

                function closeToast() {
                    if (!toast) return;
                    var t = toast;
                    toast = null;
                    t.classList.add('out');
                    setTimeout(function() {
                        t.remove();
                    }, 300);
                }

                function place() {
                    if (!callout) return;
                    var r = field.getBoundingClientRect();
                    if (window.innerWidth <= 768) {
                        callout.classList.add('below');
                        callout.style.left = (r.left + 16) + 'px';
                        callout.style.top = (r.bottom + 14) + 'px';
                    } else {
                        callout.classList.remove('below');
                        callout.style.left = (r.right + 16) + 'px';
                        callout.style.top = (r.top + r.height / 2 - callout.offsetHeight / 2) + 'px';
                    }
                }

                function dismiss() {
                    timers.forEach(clearTimeout);
                    field.classList.remove('ml-attention');
                    if (callout) {
                        callout.remove();
                        callout = null;
                    }
                    closeToast();
                    window.removeEventListener('resize', place);
                    window.removeEventListener('scroll', place);
                }

                function start() {
                    // Notifikasi
                    toast = document.createElement('div');
                    toast.className = 'ml-toast';
                    toast.setAttribute('role', 'status');
                    toast.innerHTML =
                        '<svg class="t-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>' +
                        '<div><b>Pilih kelas dulu</b><span class="t-msg">Materi ditampilkan sesuai kelas. Pilih kelasmu lewat menu kelas di sidebar.</span></div>' +
                        '<button type="button" class="t-close" aria-label="Tutup">&times;</button>';
                    document.body.appendChild(toast);
                    toast.querySelector('.t-close').addEventListener('click', closeToast);
                    later(closeToast, 7000);

                    // Sorotan pada dropdown kelas + balon penunjuk
                    field.classList.add('ml-attention');
                    callout = document.createElement('div');
                    callout.className = 'ml-callout';
                    callout.textContent = 'Pilih kelas di sini';
                    document.body.appendChild(callout);
                    place();
                    window.addEventListener('resize', place);
                    window.addEventListener('scroll', place, {
                        passive: true
                    });

                    // Klik balon = buka dropdown kelas
                    callout.addEventListener('click', function() {
                        if (select) {
                            select.focus();
                            try {
                                if (select.showPicker) select.showPicker();
                            } catch (e) {}
                        }
                        dismiss();
                    });
                    field.addEventListener('pointerdown', dismiss, {
                        once: true
                    });
                    field.addEventListener('focusin', dismiss, {
                        once: true
                    });
                    later(dismiss, 15000);
                }

                // Jika sidebar sedang diciutkan (mode ikon), buka sementara supaya dropdown kelas terlihat
                var wasCollapsed = root.classList.contains('sb-collapsed');
                if (wasCollapsed) {
                    root.classList.remove('sb-collapsed');
                    setTimeout(function() {
                        window.dispatchEvent(new Event('resize'));
                    }, 260);
                }
                window.scrollTo(0, 0);
                later(start, wasCollapsed ? 350 : 200);
            })();
        </script>
    @elseif ($studentNoClass)
        <div class="empty-state-filter">
            Materi akan muncul setelah kamu bergabung ke kelas.
            <a href="{{ route('classroom.home') }}">Gabung kelas sekarang</a>
        </div>
    @else
        @php
            $tones = ['tone-mint', 'tone-yellow', 'tone-peach'];

            // Dummy data jika $materials belum dikirim dari controller
            $dummyMaterials = [
                [
                    'id' => 1,
                    'title' => 'Bilangan Bulat',
                    'desc' => 'Kenali bilangan positif, negatif, dan nol dalam kehidupan sehari-hari.',
                    'symbol' => '−3 + 5',
                    'progress' => 65,
                ],
                [
                    'id' => 2,
                    'title' => 'Penjumlahan & Pengurangan',
                    'desc' => 'Jelajahi operasi bilangan bulat dengan bantuan garis bilangan.',
                    'symbol' => '7 − (−2)',
                    'progress' => 40,
                ],
                [
                    'id' => 3,
                    'title' => 'Perkalian & Pembagian',
                    'desc' => 'Temukan pola tanda dan selesaikan operasi dengan percaya diri.',
                    'symbol' => '−4 × 3',
                    'progress' => 0,
                ],
                [
                    'id' => 4,
                    'title' => 'Pecahan & Desimal',
                    'desc' => 'Pahami pecahan, desimal, dan cara mengubah satu bentuk ke bentuk lainnya.',
                    'symbol' => '¾ = 0,75',
                    'progress' => 100,
                ],
                [
                    'id' => 5,
                    'title' => 'Bentuk Aljabar Dasar',
                    'desc' => 'Kenalan dengan variabel, koefisien, dan menyederhanakan bentuk aljabar.',
                    'symbol' => '2x + 3',
                    'progress' => 100,
                ],
                [
                    'id' => 6,
                    'title' => 'Perbandingan & Skala',
                    'desc' => 'Gunakan perbandingan untuk membaca peta dan menyelesaikan soal cerita.',
                    'symbol' => '2 : 5',
                    'progress' => 15,
                ],
            ];

            $materiList = $materials ?? $dummyMaterials;

            // Status tiap materi dipakai oleh filter tab
            $statusOf = fn($p) => $p >= 100 ? 'done' : ($p > 0 ? 'ongoing' : 'todo');
            $counts = ['all' => 0, 'ongoing' => 0, 'todo' => 0, 'done' => 0];
            foreach ($materiList as $m) {
                $p = (int) (is_array($m) ? $m['progress'] : $m->progress_percent ?? 0);
                $counts['all']++;
                $counts[$statusOf($p)]++;
            }
        @endphp

        <div class="filters-wrapper">
            <div class="filter-tabs" role="tablist" aria-label="Filter materi">
                <button type="button" data-filter="all" class="selected" aria-pressed="true">
                    Semua Materi <span class="tab-count">{{ $counts['all'] }}</span>
                </button>
                <button type="button" data-filter="ongoing" aria-pressed="false">
                    Sedang Dipelajari <span class="tab-count">{{ $counts['ongoing'] }}</span>
                </button>
                <button type="button" data-filter="todo" aria-pressed="false">
                    Belum Dimulai <span class="tab-count">{{ $counts['todo'] }}</span>
                </button>
                <button type="button" data-filter="done" aria-pressed="false">
                    Selesai Dipelajari <span class="tab-count">{{ $counts['done'] }}</span>
                </button>
            </div>

            <div class="search-action-group">
                <label class="search-input-large">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input id="material-search" aria-label="Cari materi" placeholder="Cari modul atau video materi..." />
                </label>

                @if ($isTeacher)
                    <a href="{{ route('teacher.materials.create') }}" class="btn-add-material">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah Materi
                    </a>
                @endif
            </div>
        </div>

        <div class="material-grid" id="material-grid">
            @foreach ($materiList as $index => $materi)
                @php
                    $colorClass = $tones[$index % count($tones)];
                    $materiId = is_array($materi) ? $materi['id'] ?? $index + 1 : $materi->id;
                    $title = is_array($materi) ? $materi['title'] : $materi->title;
                    $desc = is_array($materi) ? $materi['desc'] : $materi->description;
                    $symbol = is_array($materi) ? $materi['symbol'] : 'x + y';
                    $progress = (int) (is_array($materi) ? $materi['progress'] : $materi->progress_percent ?? 0);

                    // "Mulai Belajar" -> halaman video (route: siswa.video, nama route ada di web.php)
                    $videoUrl = $isTeacher ? '#' : route('siswa.video', ['materi' => $materiId]);
                @endphp

                <div class="material-item" data-status="{{ $statusOf($progress) }}"
                    data-title="{{ mb_strtolower($title) }}">
                    <article class="material-card">

                        <a href="{{ $videoUrl }}" class="material-art {{ $colorClass }}">
                            <span class="art-leaf">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"></path>
                                    <path d="M4 21c0-6 5-11 11-14"></path>
                                </svg>
                            </span>
                            <span class="math-symbol">{{ $symbol }}</span>
                            <span class="art-squares">□</span>
                            <span class="material-label">Modul & Video</span>
                        </a>

                        <div class="material-body">
                            <div class="material-meta">
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="2" y="5" width="15" height="14" rx="2"></rect>
                                        <path d="m17 10 5-3v10l-5-3"></path>
                                    </svg>
                                    4 video
                                </span>
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg>
                                    30 menit
                                </span>
                            </div>

                            <h3>{{ $title }}</h3>
                            <p>{{ $desc }}</p>

                            <div class="material-bottom">
                                <span>Kelas VII SMP</span>
                                <a href="{{ $videoUrl }}"
                                    style="{{ $isTeacher ? 'pointer-events: none; color: #94a3b8;' : '' }}">
                                    {{ $isTeacher ? 'Pratinjau Materi' : 'Mulai Belajar' }}
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M7 17 17 7"></path>
                                        <path d="M7 7h10v10"></path>
                                    </svg>
                                </a>
                            </div>

                            @if ($isTeacher)
                                <div class="teacher-card-actions">
                                    @if (!is_array($materi))
                                        <a href="{{ route('teacher.materials.edit', $materiId) }}"
                                            class="btn-small-outline">Edit</a>
                                    @endif
                                    <button type="button" class="btn-small-outline"
                                        style="color: #ef4444; border-color: #fca5a5;"
                                        onclick="alert('Route Hapus belum dibuat')">Hapus</button>
                                </div>
                            @endif
                        </div>
                    </article>

                    <div class="progress-label">
                        <span>{{ $isTeacher ? 'Progres kelas' : 'Progres belajar' }}</span>
                        <strong>{{ $progress }}%</strong>
                    </div>
                    <div class="progress-track">
                        <div style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @endforeach

            <div class="empty-state-filter" id="filter-empty" hidden>Materi tidak ditemukan.</div>
        </div>

        <script>
            (function() {
                var grid = document.getElementById('material-grid');
                if (!grid) return;

                var items = Array.prototype.slice.call(grid.querySelectorAll('.material-item'));
                var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-filter]'));
                var search = document.getElementById('material-search');
                var empty = document.getElementById('filter-empty');
                var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                var filter = 'all';
                var timer = null;

                var emptyText = {
                    all: 'Belum ada materi.',
                    ongoing: 'Belum ada materi yang sedang dipelajari.',
                    todo: 'Semua materi sudah dimulai. Kerja bagus! 🌱',
                    done: 'Belum ada materi yang selesai dipelajari.'
                };

                function matches(item) {
                    var q = search ? search.value.trim().toLowerCase() : '';
                    var okTab = filter === 'all' || item.getAttribute('data-status') === filter;
                    return okTab && (!q || item.getAttribute('data-title').indexOf(q) !== -1);
                }

                function render(animate) {
                    clearTimeout(timer);
                    var next = items.filter(matches);
                    var leaving = items.filter(function(i) {
                        return next.indexOf(i) === -1 && !i.classList.contains('is-hidden');
                    });

                    function finish() {
                        items.forEach(function(i) {
                            if (next.indexOf(i) === -1) {
                                i.classList.remove('is-leaving', 'is-entering');
                                i.classList.add('is-hidden');
                            }
                        });
                        next.forEach(function(it, n) {
                            it.classList.remove('is-hidden', 'is-leaving', 'is-entering');
                            if (animate && !reduce) {
                                void it.offsetWidth; // restart animasi
                                it.style.setProperty('--d', (n * 70) + 'ms');
                                it.classList.add('is-entering');
                            }
                        });
                        var q = search ? search.value.trim() : '';
                        empty.textContent = q ? 'Materi tidak ditemukan.' : emptyText[filter];
                        empty.hidden = next.length > 0;
                    }

                    if (animate && !reduce && leaving.length) {
                        leaving.forEach(function(i) {
                            i.classList.remove('is-entering');
                            i.classList.add('is-leaving');
                        });
                        timer = setTimeout(finish, 190);
                    } else {
                        finish();
                    }
                }

                tabs.forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        filter = tab.getAttribute('data-filter');
                        tabs.forEach(function(t) {
                            var on = t === tab;
                            t.classList.toggle('selected', on);
                            t.setAttribute('aria-pressed', on ? 'true' : 'false');
                        });
                        render(true);
                    });
                });

                if (search) search.addEventListener('input', function() {
                    render(false);
                });

                render(true); // animasi masuk saat halaman dibuka
            })();
        </script>
    @endif
@endsection

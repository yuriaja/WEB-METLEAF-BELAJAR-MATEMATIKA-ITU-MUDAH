@extends('layouts.app')

@php
    $isTeacher = Auth::check() && Auth::user()->role === 'guru';
@endphp

@section('title', ($isTeacher ? 'Manajemen Kelas' : 'Kelas Saya') . ' | MathLeaf')
@section('header_title', $isTeacher ? 'Manajemen Kelas' : 'Kelas Saya')

@section('content')
    @php
        $DB = \Illuminate\Support\Facades\DB::class;
        $activeId = session('kelas_aktif_id');

        /*
         * Halaman ini mengambil datanya sendiri (guru & siswa), jadi $classes dari controller tidak wajib lagi.
         * Guru  : kelas yang dia ajar.   Siswa : kelas yang dia ikuti (tabel class_user).
         */
        $query = $DB
            ::table('classes')
            ->join('users', 'classes.teacher_id', '=', 'users.id')
            ->select(
                'classes.id',
                'classes.name',
                'classes.school_year',
                'classes.join_code',
                'classes.max_students',
                'users.name as teacher_name',
            )
            ->selectSub(
                $DB::table('class_user')->selectRaw('count(*)')->whereColumn('class_user.class_id', 'classes.id'),
                'students_count',
            )
            ->selectSub(
                $DB
                    ::table('materials')
                    ->selectRaw('count(*)')
                    ->whereColumn('materials.class_id', 'classes.id')
                    ->where('materials.is_published', 1),
                'materials_count',
            )
            ->orderBy('classes.name');

        if ($isTeacher) {
            $query->where('classes.teacher_id', Auth::id());
        } else {
            $query->join('class_user as cu', 'classes.id', '=', 'cu.class_id')->where('cu.user_id', Auth::id());
        }
        $rows = $query->get();

        // Daftar siswa per kelas (hanya dipakai guru)
        $studentsByClass =
            $isTeacher && $rows->isNotEmpty()
                ? $DB
                    ::table('class_user')
                    ->join('users', 'class_user.user_id', '=', 'users.id')
                    ->whereIn('class_user.class_id', $rows->pluck('id'))
                    ->select('class_user.class_id', 'users.id', 'users.name', 'users.email')
                    ->orderBy('users.name')
                    ->get()
                    ->groupBy('class_id')
                : collect();
        $blockedByClass = $isTeacher && $rows->isNotEmpty()
            ? $DB::table('class_kicked_users')
                ->join('users', 'class_kicked_users.user_id', '=', 'users.id')
                ->whereIn('class_kicked_users.class_id', $rows->pluck('id'))
                ->select('class_kicked_users.class_id', 'users.id', 'users.name')
                ->orderBy('users.name')->get()->groupBy('class_id')
            : collect();

        $symbols = ['−3 + 5', 'x²', '½', 'π'];
        $headline = $isTeacher ? 'Daftar Kelas yang Diajar' : 'Kelas yang Kamu Ikuti';
        $subline = $rows->isEmpty()
            ? ($isTeacher
                ? 'Buat kelas pertamamu untuk mulai menambahkan siswa.'
                : 'Masuk kembali dengan kode kelas Matematika dari gurumu.')
            : ($isTeacher
                ? 'Kamu mengajar ' . $rows->count() . ' kelas. Bagikan kode kelas agar siswa bisa bergabung.'
                : 'Kamu mengikuti satu kelas Matematika. Gunakan kode yang sama setiap kali login.');
    @endphp

    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-soft: #dff3e5;
            --ml-green-border: #bbf0cb;
        }

        /* ---------- Header ---------- */
        .kl-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.8rem;
        }

        .kl-header h1 {
            font-size: clamp(1.8rem, 3vw, 2.3rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .4rem;
            line-height: 1.2;
        }

        .kl-header p {
            margin: 0;
            color: #475569;
            font-size: 1.1rem;
        }

        .kl-alert {
            padding: .9rem 1.1rem;
            border-radius: 12px;
            margin-bottom: 1.2rem;
            font-size: 1.02rem;
            animation: klUp .4s ease both;
        }

        .kl-alert.ok {
            background: var(--ml-green-light);
            border: 1px solid #bfe0c9;
            color: #1f4d33;
        }

        .kl-alert.bad {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        @keyframes klUp {
            from {
                opacity: 0;
                transform: translateY(18px) scale(.97);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* ---------- Tombol ---------- */
        .kl-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border: 1.5px solid transparent;
            border-radius: 10px;
            padding: .7rem 1.2rem;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            background: var(--ml-green);
            color: #fff;
            transition: background .2s, transform .15s, box-shadow .2s, border-color .2s;
        }

        .kl-btn:hover {
            background: var(--ml-green-hover);
            box-shadow: 0 6px 16px rgba(8, 101, 52, .25);
        }

        .kl-btn:active {
            transform: scale(.97);
        }

        .kl-btn.soft {
            background: var(--ml-green-light);
            color: var(--ml-green);
            border-color: #cfe8d6;
        }

        .kl-btn.soft:hover {
            background: var(--ml-green-soft);
            box-shadow: none;
        }

        .kl-btn.ghost {
            background: #fff;
            color: #334155;
            border-color: #dbe5de;
        }

        .kl-btn.ghost:hover {
            background: #f6fbf7;
            border-color: #b5d9c1;
            box-shadow: none;
        }

        .kl-btn.block {
            width: 100%;
        }

        .kl-btn.sm {
            padding: .5rem .9rem;
            font-size: .95rem;
        }

        .kl-btn.danger {
            background: transparent;
            color: #ef4444;
            padding: .5rem .6rem;
        }

        .kl-btn.danger:hover {
            background: #fef2f2;
            box-shadow: none;
        }

        .kl-btn[disabled] {
            opacity: .65;
            cursor: progress;
        }

        .kl-spin {
            width: 16px;
            height: 16px;
            border: 2.5px solid rgba(255, 255, 255, .4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: klSpin .7s linear infinite;
            display: none;
        }

        .kl-btn.loading .kl-spin {
            display: inline-block;
        }

        @keyframes klSpin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ---------- Gabung kelas (siswa) ---------- */
        .kl-join {
            display: flex;
            align-items: center;
            gap: 1.4rem;
            flex-wrap: wrap;
            padding: 1.5rem 1.7rem;
            margin-bottom: 1.8rem;
            border-radius: 18px;
            border: 1px solid #cfe8d6;
            background: linear-gradient(135deg, #e9f7ee 0%, #d4efdc 60%, #fdf3c4 140%);
            animation: klUp .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        .kl-join .ic {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: #fff;
            color: var(--ml-green);
            display: grid;
            place-items: center;
            box-shadow: 0 6px 16px rgba(8, 101, 52, .15);
            flex-shrink: 0;
        }

        .kl-join .txt {
            flex: 1 1 220px;
        }

        .kl-join h2 {
            margin: 0 0 .2rem;
            font-size: 1.3rem;
            color: #14301f;
        }

        .kl-join p {
            margin: 0;
            color: #3f6b50;
            font-size: 1.02rem;
        }

        .kl-join form {
            display: flex;
            gap: .7rem;
            flex: 1 1 340px;
            max-width: 480px;
        }

        .kl-code-input {
            flex: 1;
            min-width: 0;
            padding: .8rem 1rem;
            border: 1.5px solid #b5d9c1;
            border-radius: 12px;
            font-family: var(--font-mono, monospace);
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #14301f;
            background: #fff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .kl-code-input::placeholder {
            letter-spacing: .05em;
            font-weight: 500;
            font-size: 1rem;
            color: #94a3b8;
            text-transform: none;
        }

        .kl-code-input:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .15);
        }

        .kl-code-input.shake {
            animation: klShake .4s ease;
            border-color: #dc2626;
        }

        @keyframes klShake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-6px);
            }

            50% {
                transform: translateX(6px);
            }

            75% {
                transform: translateX(-3px);
            }
        }

        .kl-join .err {
            flex-basis: 100%;
            margin: -.4rem 0 0;
            color: #b91c1c;
            font-weight: 600;
            font-size: .98rem;
        }

        /* ---------- Grid kartu kelas ---------- */
        .kl-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.4rem;
            align-items: start;
        }

        .kl-card {
            position: relative;
            background: #fff;
            border: 1px solid #dfe9e2;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            animation: klUp .55s cubic-bezier(.2, .8, .2, 1) both;
            animation-delay: var(--d, 0ms);
        }

        .kl-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .1);
        }

        .kl-card.is-active {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .14), 0 10px 26px rgba(8, 101, 52, .12);
        }

        .kl-cover {
            position: relative;
            padding: 1.4rem 1.5rem 1.3rem;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            overflow: hidden;
        }

        .kl-tone-0 {
            background: linear-gradient(135deg, #e3f5e9, #c8ebd3);
        }

        .kl-tone-1 {
            background: linear-gradient(135deg, #fff6d6, #fde9a0);
        }

        .kl-tone-2 {
            background: linear-gradient(135deg, #ffeadb, #ffd2b0);
        }

        .kl-sym {
            position: absolute;
            right: -6px;
            top: 4px;
            font-size: 4.2rem;
            font-weight: 800;
            color: rgba(8, 101, 52, .12);
            transform: rotate(-8deg);
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
        }

        .kl-titlerow {
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .8rem;
            margin-bottom: .5rem;
        }

        .kl-titlerow h3 {
            margin: 0;
            min-width: 0;
        }

        .kl-active-pill {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: var(--ml-green);
            color: #fff;
            font-size: .82rem;
            font-weight: 800;
            padding: .3rem .7rem;
            border-radius: 999px;
            white-space: nowrap;
            box-shadow: 0 4px 10px rgba(8, 101, 52, .3);
            animation: klPop .4s cubic-bezier(.2, .8, .2, 1) both;
        }

        @keyframes klPop {
            from {
                opacity: 0;
                transform: scale(.7);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .kl-cover h3 {
            position: relative;
            margin: 0;
            font-size: 1.55rem;
            font-weight: 800;
            color: #14301f;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .kl-year {
            position: relative;
            align-self: flex-start;
            background: rgba(255, 255, 255, .75);
            color: #2f5a42;
            border-radius: 999px;
            padding: .25rem .8rem;
            font-size: .92rem;
            font-weight: 700;
        }

        .kl-body {
            padding: 1.2rem 1.5rem 1.4rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .kl-teacher {
            display: flex;
            align-items: center;
            gap: .8rem;
        }

        .kl-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--ml-green-soft);
            color: var(--ml-green);
            display: grid;
            place-items: center;
            font-weight: 800;
            flex-shrink: 0;
        }

        .kl-teacher b {
            display: block;
            color: #14301f;
            font-size: 1.02rem;
        }

        .kl-teacher small {
            color: #64748b;
            font-size: .9rem;
        }

        .kl-stats {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
        }

        .kl-stat {
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

        .kl-actions {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .kl-actions form {
            margin: 0;
            flex: 1 1 auto;
        }

        .kl-actions .kl-delete-form {
            flex: 0 0 auto;
        }

        .kl-actions .kl-btn {
            flex: 1 1 auto;
        }

        .kl-actions .kl-btn.danger {
            flex: 0 0 auto;
        }

        /* Kode kelas (guru) */
        .kl-code {
            position: relative;
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            margin-top: .6rem;
            background: rgba(255, 255, 255, .85);
            border-radius: 12px;
            padding: .45rem .5rem .45rem .9rem;
        }

        .kl-code small {
            display: block;
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .08em;
            color: var(--ml-green);
            text-transform: uppercase;
        }

        .kl-code b {
            font-family: var(--font-mono, monospace);
            font-size: 1.35rem;
            letter-spacing: .1em;
            color: #14301f;
        }

        .kl-copy {
            border: 0;
            background: var(--ml-green-light);
            color: var(--ml-green);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: background .2s, transform .15s;
        }

        .kl-copy:hover {
            background: var(--ml-green-soft);
        }

        .kl-copy:active {
            transform: scale(.92);
        }

        .kl-copy.done {
            background: var(--ml-green);
            color: #fff;
        }

        /* Daftar siswa (accordion halus) */
        .kl-detail {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows .35s cubic-bezier(.2, .8, .2, 1);
            background: #f6fbf7;
            border-top: 1px solid transparent;
        }

        .kl-detail.open {
            grid-template-rows: 1fr;
            border-top-color: #e3ece6;
        }

        .kl-detail>div {
            overflow: hidden;
        }

        .kl-detail-inner {
            padding: 1.2rem 1.5rem 1.4rem;
        }

        .kl-detail h4 {
            margin: 0 0 .9rem;
            font-size: .88rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #64748b;
        }

        .kl-students {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .kl-student {
            display: flex;
            align-items: center;
            gap: .8rem;
            background: #fff;
            border: 1px solid #e3ece6;
            border-radius: 12px;
            padding: .6rem .8rem;
        }

        .kl-student .kl-avatar {
            width: 34px;
            height: 34px;
            font-size: .9rem;
        }

        .kl-student b {
            display: block;
            font-size: 1rem;
            color: #14301f;
        }

        .kl-student small {
            color: #64748b;
            font-size: .88rem;
        }

        .kl-student form {
            margin-left: auto;
            flex: 0 0 auto;
        }

        .kl-empty-mini {
            text-align: center;
            color: #64748b;
            padding: 1rem 0;
            font-size: 1rem;
        }

        /* ---------- State kosong ---------- */
        .kl-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem 1.5rem;
            background: #fff;
            border: 1px dashed #86c79a;
            border-radius: 18px;
            animation: klUp .5s ease both;
        }

        .kl-empty svg {
            color: var(--ml-green);
            margin-bottom: .8rem;
        }

        .kl-empty h3 {
            margin: 0 0 .4rem;
            font-size: 1.3rem;
            color: #14301f;
        }

        .kl-empty p {
            margin: 0 0 1.2rem;
            color: #64748b;
            font-size: 1.05rem;
        }

        /* ---------- Modal (guru) ---------- */
        .kl-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(20, 48, 31, .45);
            backdrop-filter: blur(3px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }

        .kl-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .kl-modal {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .2);
            transform: translateY(14px) scale(.97);
            transition: transform .25s cubic-bezier(.2, .8, .2, 1);
        }

        .kl-backdrop.active .kl-modal {
            transform: none;
        }

        .kl-modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #e8f0ea;
        }

        .kl-modal-head h3 {
            margin: 0;
            font-size: 1.25rem;
            color: #14301f;
        }

        .kl-x {
            border: 0;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            padding: .3rem;
            border-radius: 8px;
            display: flex;
        }

        .kl-x:hover {
            background: #f1f5f9;
            color: #14301f;
        }

        .kl-modal-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        .kl-field label {
            display: block;
            font-size: 1rem;
            font-weight: 700;
            color: #14301f;
            margin-bottom: .45rem;
        }

        .kl-input {
            width: 100%;
            padding: .8rem 1rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1.05rem;
            color: #14301f;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .kl-input:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        .kl-modal-foot {
            display: flex;
            justify-content: flex-end;
            gap: .7rem;
            padding: 1.1rem 1.5rem;
            background: #f6fbf7;
            border-top: 1px solid #e8f0ea;
            border-radius: 0 0 18px 18px;
        }

        .kl-capacity-form { display:flex; align-items:center; gap:.55rem; flex-wrap:wrap; padding:.75rem;
            background:#f1faf3; border:1px solid #d6eadb; border-radius:12px; }
        .kl-capacity-form label { font-weight:800; color:#14532d; font-size:.88rem; }
        .kl-capacity-form input { width:65px; padding:.5rem .55rem; border-radius:9px; border:1px solid #a7d5b5;
            font-size:1rem; font-weight:700; color:#14532d; background:white; }
        .kl-capacity-form button { margin-left:auto; }
        .kl-confirm-danger { background:#dc2626; color:white; border-color:#dc2626; }
        .kl-confirm-danger:hover { background:#b91c1c; box-shadow:0 5px 16px #b91c1c33; }
        .kl-field[hidden] { display:none !important; }

        @media (max-width: 480px) {
            .kl-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .kl-join form {
                flex-direction: column;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .kl-card,
            .kl-join,
            .kl-empty,
            .kl-alert,
            .kl-active-pill {
                animation: none !important;
            }

            .kl-detail,
            .kl-modal,
            .kl-card {
                transition: none !important;
            }
        }
    </style>

    {{-- ================= HEADER ================= --}}
    <div class="kl-header">
        <div>
            <h1>{{ $headline }}</h1>
            <p>{{ $subline }}</p>
        </div>
        @if ($isTeacher)
            <button type="button" class="kl-btn" onclick="openModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Kelas Baru
            </button>
        @endif
    </div>

    @if (session('success'))
        <div class="kl-alert ok">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="kl-alert bad">{{ session('error') }}</div>
    @endif
    @error('confirmation')
        <div class="kl-alert bad">{{ $message }}</div>
    @enderror

    @unless ($isTeacher)
        <section class="kl-join" aria-label="Informasi kelas Matematika">
            <span class="ic" aria-hidden="true">✦</span>
            <div class="txt">
                <h2>Satu akun, satu kelas Matematika</h2>
                <p>Kode kelas dimasukkan setiap kali login. Jika perlu pindah kelas, hubungi guru untuk dikeluarkan dari kelas sebelumnya.</p>
            </div>
        </section>
    @endunless

    {{-- ================= MODAL BUAT KELAS (GURU) ================= --}}
    @if ($isTeacher)
        <div id="modal-tambah" class="kl-backdrop" aria-hidden="true">
            <div class="kl-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                <div class="kl-modal-head">
                    <h3 id="modal-title">Buat Kelas Baru</h3>
                    <button type="button" class="kl-x" onclick="closeModal()" aria-label="Tutup">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('classroom.create') }}" method="POST">
                    @csrf
                    <div class="kl-modal-body">
                        <div class="kl-field">
                            <label for="name">Nama Kelas</label>
                            <input type="text" id="name" name="name" class="kl-input"
                                placeholder="Contoh: VII B Matematika Dasar" required>
                        </div>
                        <div class="kl-field">
                            <label for="school_year">Tahun Ajaran</label>
                            <input type="text" id="school_year" name="school_year" class="kl-input"
                                placeholder="Contoh: 2026/2027">
                        </div>
                        <div class="kl-field">
                            <label for="max_students">Maksimal Siswa</label>
                            <input type="number" id="max_students" name="max_students" class="kl-input"
                                min="1" max="200" value="30" required>
                            <small style="display:block;color:#64748b;margin-top:.4rem">Contoh: VII A Matematika maksimal 30 siswa.</small>
                        </div>
                    </div>
                    <div class="kl-modal-foot">
                        <button type="button" class="kl-btn ghost" onclick="closeModal()">Batal</button>
                        <button type="submit" class="kl-btn">Simpan Kelas</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ================= DAFTAR KELAS ================= --}}
    <div class="kl-grid">
        @forelse ($rows as $i => $class)
            @php
                $isActive = $activeId == $class->id;
                $tone = $i % 3;
            @endphp
            <article class="kl-card {{ $isActive ? 'is-active' : '' }}" style="--d: {{ $i * 80 }}ms">
                <div class="kl-cover kl-tone-{{ $tone }}">
                    <span class="kl-sym">{{ $symbols[$i % count($symbols)] }}</span>
                    <div class="kl-titlerow">
                        <h3>{{ $class->name }}</h3>
                        @if ($isActive)
                            <span class="kl-active-pill">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="3.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Kelas aktif
                            </span>
                        @endif
                    </div>
                    <span
                        class="kl-year">{{ $class->school_year ? 'Tahun Ajaran ' . $class->school_year : 'Tahun ajaran belum diisi' }}</span>

                    @if ($isTeacher)
                        <div class="kl-code">
                            <div>
                                <small>Kode kelas</small>
                                <b>{{ $class->join_code }}</b>
                            </div>
                            <button type="button" class="kl-copy" onclick="copyCode('{{ $class->join_code }}', this)"
                                title="Salin kode" aria-label="Salin kode kelas">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2">
                                    </rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="kl-body">
                    @unless ($isTeacher)
                        <div class="kl-teacher">
                            <span class="kl-avatar">{{ strtoupper(mb_substr($class->teacher_name, 0, 1)) }}</span>
                            <div>
                                <b>{{ $class->teacher_name }}</b>
                                <small>Guru pengampu</small>
                            </div>
                        </div>
                    @endunless

                    <div class="kl-stats">
                        <span class="kl-stat">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            {{ $class->students_count }}/{{ $class->max_students }} siswa
                        </span>
                        <span class="kl-stat" aria-label="Sisa kuota siswa">{{ max(0, $class->max_students - $class->students_count) }} kursi tersisa</span>
                        <span class="kl-stat">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 7v14"></path>
                                <path
                                    d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z">
                                </path>
                            </svg>
                            {{ $class->materials_count }} materi
                        </span>
                    </div>

                    @if ($isTeacher)
                        <form action="{{ route('classroom.capacity.update', $class->id) }}" method="POST"
                            class="kl-capacity-form">
                            @csrf @method('PATCH')
                            <label for="capacity-{{ $class->id }}">Batas siswa</label>
                            <input id="capacity-{{ $class->id }}" type="number" name="max_students"
                                min="{{ max(1, $class->students_count) }}" max="200"
                                value="{{ $class->max_students }}" required aria-label="Maksimal siswa kelas {{ $class->name }}">
                            <button type="submit" class="kl-btn sm soft">Simpan Kuota</button>
                        </form>
                    @endif

                    <div class="kl-actions">
                        @if ($isActive)
                            <a href="{{ route('materi.index') }}" class="kl-btn">
                                Buka Materi
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        @else
                            <form action="{{ route('classroom.active') }}" method="POST">
                                @csrf
                                <input type="hidden" name="kelas_id" value="{{ $class->id }}">
                                <button type="submit" class="kl-btn soft block">Pilih Kelas Ini</button>
                            </form>
                        @endif

                        @if ($isTeacher)
                            <button type="button" class="kl-btn ghost" onclick="toggleDetail({{ $class->id }})"
                                aria-expanded="false" id="btn-{{ $class->id }}">
                                Siswa
                                <svg id="icon-{{ $class->id }}" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" style="transition: transform .3s ease">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <form class="kl-delete-form" action="{{ route('classroom.destroy', $class->id) }}" method="POST"
                                data-class-name="{{ $class->name }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="confirmation" value="">
                                <button type="button" onclick="openClassDelete(this)" class="kl-btn danger" title="Hapus kelas permanen" aria-label="Hapus kelas permanen">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path
                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                    </path>
                                </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if ($isTeacher)
                    <div id="detail-{{ $class->id }}" class="kl-detail">
                        <div>
                            <div class="kl-detail-inner">
                                <h4>Anggota siswa</h4>
                                <div class="kl-students">
                                    @forelse ($studentsByClass[$class->id] ?? [] as $student)
                                        <div class="kl-student">
                                            <span
                                                class="kl-avatar">{{ strtoupper(mb_substr($student->name, 0, 1)) }}</span>
                                            <div>
                                                <b>{{ $student->name }}</b>
                                                <small>{{ $student->email }}</small>
                                            </div>
                                            <form action="{{ route('classroom.students.remove', [$class->id, $student->id]) }}"
                                                method="POST" data-student-name="{{ $student->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="openKickDialog(this)" class="kl-btn danger" title="Keluarkan siswa"
                                                    aria-label="Keluarkan siswa">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="15" y1="9" x2="9" y2="15">
                                                    </line>
                                                    <line x1="9" y1="9" x2="15" y2="15">
                                                    </line>
                                                </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <div class="kl-empty-mini">Belum ada siswa yang bergabung di kelas ini.</div>
                                    @endforelse
                                </div>
                                @if (($blockedByClass[$class->id] ?? collect())->isNotEmpty())
                                    <h4 style="margin-top:1.3rem">Siswa yang pernah dikeluarkan</h4>
                                    <div class="kl-students">
                                        @foreach ($blockedByClass[$class->id] as $blocked)
                                            <div class="kl-student">
                                                <span class="kl-avatar">{{ strtoupper(mb_substr($blocked->name, 0, 1)) }}</span>
                                                <div><b>{{ $blocked->name }}</b><small>Tidak bisa login ke kelas ini</small></div>
                                                <form action="{{ route('classroom.students.allow', [$class->id, $blocked->id]) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="kl-btn sm soft">Izinkan Lagi</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </article>
        @empty
            <div class="kl-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="9" y1="21" x2="9" y2="9"></line>
                </svg>
                @if ($isTeacher)
                    <h3>Belum ada kelas</h3>
                    <p>Buat kelas pertamamu untuk mulai menambahkan siswa.</p>
                    <button type="button" class="kl-btn" onclick="openModal()">Tambah Kelas Baru</button>
                @else
                    <h3>Belum terdaftar di kelas Matematika</h3>
                    <p>Keluar dari akun, lalu login lagi dengan kode kelas 8 karakter dari guru.</p>
                    <a href="{{ route('login') }}" class="kl-btn soft">Lihat halaman login</a>
                @endif
            </div>
        @endforelse
    </div>

    @if ($isTeacher)
        <div id="action-dialog" class="kl-backdrop" aria-hidden="true" onclick="if(event.target===this)closeActionDialog()">
            <div class="kl-modal" role="dialog" aria-modal="true" aria-labelledby="action-dialog-title" aria-describedby="action-dialog-description">
                <div class="kl-modal-head">
                    <h3 id="action-dialog-title">Konfirmasi Tindakan</h3>
                    <button type="button" class="kl-x" onclick="closeActionDialog()" aria-label="Tutup dialog">✕</button>
                </div>
                <div class="kl-modal-body">
                    <p id="action-dialog-description" style="color:#475569;line-height:1.65;margin:0"></p>
                    <div id="action-confirmation-field" class="kl-field" hidden>
                        <label id="action-class-name-label" for="action-class-name-input">Nama kelas</label>
                        <input id="action-class-name-input" type="text" class="kl-input" autocomplete="off">
                    </div>
                    <p id="action-dialog-error" role="alert" style="color:#b91c1c;margin:0;font-weight:700"></p>
                </div>
                <div class="kl-modal-foot">
                    <button type="button" id="action-cancel-button" class="kl-btn ghost" onclick="closeActionDialog()">Batal</button>
                    <button type="button" id="action-confirm-button" class="kl-btn kl-confirm-danger" onclick="confirmClassAction()">Konfirmasi</button>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Modal konfirmasi menggunakan desain MathLeaf, bukan prompt/confirm browser.
        let pendingClassForm = null;
        let pendingMode = null;
        function openKickDialog(button) {
            pendingClassForm = button.closest('form'); pendingMode = 'kick';
            document.getElementById('action-dialog-title').textContent = 'Keluarkan Siswa?';
            document.getElementById('action-dialog-description').textContent =
                'Siswa "' + pendingClassForm.dataset.studentName + '" akan dikeluarkan. Akun dan nilai tetap ada, tetapi dia tidak dapat masuk kembali sampai kamu mengizinkan.';
            document.getElementById('action-confirmation-field').hidden = true;
            document.getElementById('action-confirm-button').textContent = 'Ya, Keluarkan Siswa';
            openActionDialog();
        }
        function openClassDelete(button) {
            pendingClassForm = button.closest('form'); pendingMode = 'delete';
            const name = pendingClassForm.dataset.className;
            document.getElementById('action-dialog-title').textContent = 'Hapus Kelas Permanen?';
            document.getElementById('action-dialog-description').textContent =
                'Kelas "' + name + '" beserta materi, tugas, nilai, dan forum akan dihapus permanen. Akun siswa tidak terhapus.';
            document.getElementById('action-class-name-label').textContent = 'Ketik persis: ' + name;
            document.getElementById('action-class-name-input').value = '';
            document.getElementById('action-confirmation-field').hidden = false;
            document.getElementById('action-confirm-button').textContent = 'Hapus Kelas';
            openActionDialog();
        }
        function openActionDialog() {
            const modal = document.getElementById('action-dialog');
            modal.classList.add('active'); modal.setAttribute('aria-hidden', 'false');
            setTimeout(() => {
                (pendingMode === 'delete' ? document.getElementById('action-class-name-input') : document.getElementById('action-cancel-button')).focus();
            }, 70);
        }
        function closeActionDialog() {
            const modal = document.getElementById('action-dialog');
            if (!modal) return;
            modal.classList.remove('active'); modal.setAttribute('aria-hidden', 'true');
            pendingClassForm = null; pendingMode = null;
            document.getElementById('action-dialog-error').textContent = '';
        }
        function confirmClassAction() {
            if (!pendingClassForm) return;
            if (pendingMode === 'delete') {
                const name = pendingClassForm.dataset.className;
                const typed = document.getElementById('action-class-name-input').value;
                if (typed !== name) {
                    document.getElementById('action-dialog-error').textContent = 'Nama kelas belum sama persis. Penghapusan dibatalkan.';
                    return;
                }
                pendingClassForm.querySelector('input[name="confirmation"]').value = typed;
            }
            document.getElementById('action-confirm-button').disabled = true;
            pendingClassForm.submit();
        }
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeActionDialog();
        });

        /* Salin kode kelas (guru) */
        function copyCode(code, btn) {
            navigator.clipboard.writeText(code).then(function() {
                var old = btn.innerHTML;
                btn.classList.add('done');
                btn.innerHTML =
                    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                setTimeout(function() {
                    btn.classList.remove('done');
                    btn.innerHTML = old;
                }, 1800);
            });
        }

        /* Modal buat kelas (guru) */
        var modal = document.getElementById('modal-tambah');

        function openModal() {
            if (!modal) return;
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            setTimeout(function() {
                var n = document.getElementById('name');
                if (n) n.focus();
            }, 150);
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
        }
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });
        }

        /* Daftar siswa per kelas (guru) */
        function toggleDetail(id) {
            var d = document.getElementById('detail-' + id);
            var icon = document.getElementById('icon-' + id);
            var btn = document.getElementById('btn-' + id);
            var open = d.classList.toggle('open');
            icon.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

    </script>
@endsection

@extends('layouts.app')

@section('title', ($assessment->title ?? 'Editor Kuis') . ' | MathLeaf')
@section('header_title', $assessment->title ?? 'Editor Kuis')

@section('content')
    @php
        $isTeacher = Auth::check() && Auth::user()->role === 'guru';
        $questions = $assessment->questions ?? collect();
        $typeVal = $assessment->type ?? 'latihan';
        $isExam = $typeVal === 'ujian';
        $limit = (int) ($assessment->time_limit_minutes ?? 0);
        $typeLabel = ['latihan' => 'Latihan', 'kuis' => 'Kuis', 'ujian' => 'Ujian'][$typeVal] ?? 'Latihan';

        // Data soal untuk editor guru (dirender oleh JS agar satu template dipakai untuk soal lama & baru)
        $questionData = $questions
            ->map(
                fn($q) => [
                    'text' => $q->question_text,
                    'options' => array_slice(array_pad(array_values((array) ($q->options ?? [])), 4, ''), 0, 4),
                    'correct' => (int) $q->correct_answer,
                    'points' => $q->points ?? 10,
                ],
            )
            ->values();
    @endphp

    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-soft: #dff3e5;
        }

        .app-content {
            overflow-x: visible;
        }

        /* Mode ujian: sembunyikan sidebar & header supaya fokus */
        html.exam-mode .app-sidebar,
        html.exam-mode .app-header {
            display: none !important;
        }

        html.exam-mode .app-layout {
            grid-template-columns: minmax(0, 1fr) !important;
        }

        html.exam-mode .app-main {
            grid-column: 1 !important;
        }

        @keyframes qeUp {
            from {
                opacity: 0;
                transform: translateY(18px) scale(.97);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes qeShake {

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

        /* ---------- Header ---------- */
        .qe-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.6rem;
        }

        .qe-back {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: #64748b;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: .8rem;
            transition: color .15s, transform .15s;
        }

        .qe-back:hover {
            color: var(--ml-green);
            transform: translateX(-3px);
        }

        .qe-title {
            font-size: clamp(1.7rem, 3vw, 2.2rem);
            font-weight: 800;
            color: #14301f;
            margin: 0 0 .6rem;
            line-height: 1.2;
        }

        .qe-chips {
            display: flex;
            flex-wrap: wrap;
            gap: .55rem;
        }

        .qe-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: var(--ml-green-light);
            color: var(--ml-green);
            border: 1px solid #cfe8d6;
            border-radius: 999px;
            padding: .35rem .85rem;
            font-size: .95rem;
            font-weight: 700;
        }

        /* ---------- Layout ---------- */
        .qe-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 1.8rem;
            align-items: start;
        }

        .qe-main {
            display: flex;
            flex-direction: column;
            gap: 1.4rem;
            min-width: 0;
        }

        .qe-side {
            position: sticky;
            top: 90px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background: #fff;
            border: 1px solid #dfe9e2;
            border-radius: 18px;
            padding: 1.3rem;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            animation: qeUp .55s cubic-bezier(.2, .8, .2, 1) both .1s;
        }

        .qe-side h3 {
            margin: 0;
            font-size: 1.1rem;
            color: #14301f;
        }

        @media (max-width: 960px) {
            .qe-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .qe-side {
                position: sticky;
                top: 76px;
                z-index: 30;
                order: -1;
                padding: .9rem 1rem;
            }

            .qe-nav,
            .qe-side h3 {
                display: none;
            }
        }

        /* ---------- Tombol ---------- */
        .qe-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border: 1.5px solid transparent;
            border-radius: 10px;
            padding: .75rem 1.3rem;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            background: var(--ml-green);
            color: #fff;
            transition: background .2s, transform .15s, box-shadow .2s;
        }

        .qe-btn:hover {
            background: var(--ml-green-hover);
            box-shadow: 0 6px 16px rgba(8, 101, 52, .25);
        }

        .qe-btn:active {
            transform: scale(.97);
        }

        .qe-btn.block {
            width: 100%;
        }

        .qe-btn.ghost {
            background: #fff;
            color: #334155;
            border-color: #dbe5de;
        }

        .qe-btn.ghost:hover {
            background: #f6fbf7;
            border-color: #b5d9c1;
            box-shadow: none;
        }

        .qe-btn[disabled] {
            opacity: .65;
            cursor: not-allowed;
            box-shadow: none;
        }

        .qe-btn.loading {
            cursor: progress;
        }

        .qe-spin {
            width: 16px;
            height: 16px;
            border: 2.5px solid rgba(255, 255, 255, .4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: qeSpin .7s linear infinite;
            display: none;
        }

        .qe-btn.loading .qe-spin {
            display: inline-block;
        }

        @keyframes qeSpin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ---------- Kotak & form ---------- */
        .qe-box {
            background: #fff;
            border: 1px solid #dfe9e2;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            animation: qeUp .55s cubic-bezier(.2, .8, .2, 1) both;
        }

        .qe-box-head {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: 1.15rem 1.5rem;
            border-bottom: 1px solid #e8f0ea;
        }

        .qe-box-head .ic {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .qe-box-head h2 {
            margin: 0;
            font-size: 1.2rem;
            color: #14301f;
        }

        .qe-box-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .qe-grid2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 640px) {
            .qe-grid2 {
                grid-template-columns: 1fr;
            }
        }

        .qe-field label,
        .qe-label {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            font-size: 1rem;
            font-weight: 700;
            color: #14301f;
            margin-bottom: .45rem;
        }

        .qe-field label em,
        .qe-label em {
            font-style: normal;
            font-weight: 500;
            font-size: .88rem;
            color: #94a3b8;
        }

        .qe-input,
        .qe-textarea {
            width: 100%;
            padding: .8rem 1rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1.05rem;
            color: #14301f;
            background: #fff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .qe-textarea {
            min-height: 96px;
            resize: vertical;
            line-height: 1.55;
        }

        .qe-input:focus,
        .qe-textarea:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        /* Mode pengerjaan (kartu pilihan) */
        .qe-types {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .7rem;
        }

        @media (max-width: 640px) {
            .qe-types {
                grid-template-columns: 1fr;
            }
        }

        .qe-type {
            position: relative;
            cursor: pointer;
        }

        .qe-type input {
            position: absolute;
            opacity: 0;
        }

        .qe-type span {
            display: block;
            height: 100%;
            padding: .85rem 1rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 12px;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }

        .qe-type b {
            display: block;
            color: #14301f;
            font-size: 1rem;
        }

        .qe-type small {
            display: block;
            margin-top: 2px;
            color: #64748b;
            font-size: .88rem;
            line-height: 1.4;
        }

        .qe-type:hover span {
            background: #f6fbf7;
        }

        .qe-type input:checked+span {
            border-color: var(--ml-green);
            background: var(--ml-green-light);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
        }

        .qe-type input:focus-visible+span {
            outline: 3px solid rgba(8, 101, 52, .35);
        }

        /* ---------- Soal (guru) ---------- */
        .qe-q {
            background: #fff;
            border: 1px solid #dfe9e2;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            scroll-margin-top: 90px;
            transition: opacity .2s ease, transform .2s ease;
            animation: qeUp .5s cubic-bezier(.2, .8, .2, 1) both;
        }

        .qe-q.leaving {
            animation: qeOut .2s ease both;
        }

        @keyframes qeOut {
            to {
                opacity: 0;
                transform: scale(.96) translateY(6px);
            }
        }

        .qe-q-head {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: 1rem 1.4rem;
            border-bottom: 1px solid #e8f0ea;
        }

        .qe-num {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--ml-green);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 800;
        }

        .qe-q-head b {
            font-size: 1.1rem;
            color: #14301f;
        }

        .qe-del {
            margin-left: auto;
            border: 0;
            background: transparent;
            color: #ef4444;
            border-radius: 8px;
            padding: .45rem;
            cursor: pointer;
            display: flex;
            transition: background .2s;
        }

        .qe-del:hover {
            background: #fef2f2;
        }

        .qe-q-body {
            padding: 1.4rem;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .qe-opts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .7rem;
        }

        @media (max-width: 640px) {
            .qe-opts {
                grid-template-columns: 1fr;
            }
        }

        .qe-opt {
            position: relative;
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .5rem .6rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 12px;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }

        .qe-opt:has(input[type=radio]:checked) {
            border-color: var(--ml-green);
            background: var(--ml-green-light);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .1);
        }

        .qe-ltr {
            position: relative;
            cursor: pointer;
            flex-shrink: 0;
        }

        .qe-ltr input {
            position: absolute;
            opacity: 0;
        }

        .qe-ltr span {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1.5px solid #cfe0d4;
            background: #fff;
            color: #3f5f4b;
            display: grid;
            place-items: center;
            font-weight: 800;
            transition: background .2s, color .2s, border-color .2s, transform .15s;
        }

        .qe-ltr:hover span {
            border-color: var(--ml-green);
        }

        .qe-ltr input:checked+span {
            background: var(--ml-green);
            border-color: var(--ml-green);
            color: #fff;
            transform: scale(1.08);
        }

        .qe-ltr input:focus-visible+span {
            outline: 3px solid rgba(8, 101, 52, .35);
        }

        .qe-opt input[type=text] {
            flex: 1;
            min-width: 0;
            border: 0;
            background: transparent;
            font-family: inherit;
            font-size: 1.02rem;
            color: #14301f;
            outline: none;
            padding: .4rem .2rem;
        }

        .qe-key {
            display: none;
            flex-shrink: 0;
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .04em;
            background: var(--ml-green);
            color: #fff;
            border-radius: 999px;
            padding: .15rem .55rem;
        }

        .qe-opt:has(input[type=radio]:checked) .qe-key {
            display: inline-block;
        }

        .qe-q-foot {
            display: flex;
            align-items: center;
            gap: .8rem;
            flex-wrap: wrap;
        }

        .qe-q-foot label {
            font-weight: 700;
            color: #14301f;
        }

        .qe-q-foot input {
            width: 100px;
        }

        .qe-add {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            padding: 1rem;
            border: 2px dashed #b5d9c1;
            border-radius: 16px;
            background: #fbfdfb;
            color: var(--ml-green);
            font-family: inherit;
            font-size: 1.05rem;
            font-weight: 800;
            cursor: pointer;
            transition: background .2s, border-color .2s, transform .15s;
        }

        .qe-add:hover {
            background: var(--ml-green-light);
            border-color: var(--ml-green);
        }

        .qe-add:active {
            transform: scale(.99);
        }

        /* ---------- Panel samping ---------- */
        .qe-sum {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .6rem;
        }

        .qe-sum div {
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            border-radius: 12px;
            padding: .6rem .8rem;
            text-align: center;
        }

        .qe-sum b {
            display: block;
            font-size: 1.5rem;
            color: var(--ml-green);
            line-height: 1.1;
        }

        .qe-sum small {
            color: #64748b;
            font-size: .85rem;
            font-weight: 600;
        }

        .qe-nav {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .qe-pill {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1.5px solid #cfe0d4;
            background: #fff;
            color: #3f5f4b;
            font-family: inherit;
            font-size: .98rem;
            font-weight: 800;
            cursor: pointer;
            transition: background .2s, border-color .2s, color .2s, transform .15s;
        }

        .qe-pill:hover {
            border-color: var(--ml-green);
            transform: translateY(-2px);
        }

        .qe-pill.done {
            background: var(--ml-green);
            border-color: var(--ml-green);
            color: #fff;
        }

        .qe-hint {
            margin: 0;
            color: #64748b;
            font-size: .92rem;
            line-height: 1.5;
        }

        /* ---------- Pengerjaan siswa ---------- */
        .qe-take {
            background: #fff;
            border: 1px solid #dfe9e2;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(8, 101, 52, .04);
            scroll-margin-top: 100px;
            animation: qeUp .5s cubic-bezier(.2, .8, .2, 1) both;
            animation-delay: var(--d, 0ms);
            transition: border-color .25s, box-shadow .25s;
        }

        .qe-take.missing {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .15);
            animation: qeShake .4s ease;
        }

        .qe-take-head {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: 1rem 1.4rem;
            border-bottom: 1px solid #e8f0ea;
        }

        .qe-take-head b {
            color: #14301f;
            font-size: 1.1rem;
        }

        .qe-pts {
            margin-left: auto;
            background: #fff6d6;
            color: #8a6d00;
            border-radius: 999px;
            padding: .25rem .8rem;
            font-size: .9rem;
            font-weight: 800;
        }

        .qe-take-body {
            padding: 1.4rem;
        }

        .qe-qtext {
            margin: 0 0 1.1rem;
            font-size: 1.2rem;
            line-height: 1.6;
            color: #14301f;
            font-weight: 600;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .qe-choices {
            display: flex;
            flex-direction: column;
            gap: .65rem;
        }

        .qe-choice {
            position: relative;
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: .8rem 1rem;
            border: 1.5px solid #cfe0d4;
            border-radius: 14px;
            cursor: pointer;
            transition: border-color .2s, background .2s, box-shadow .2s, transform .15s;
        }

        .qe-choice:hover {
            background: #f6fbf7;
            border-color: #86c79a;
            transform: translateX(3px);
        }

        .qe-choice input {
            position: absolute;
            opacity: 0;
        }

        .qe-choice .l {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1.5px solid #cfe0d4;
            display: grid;
            place-items: center;
            font-weight: 800;
            color: #3f5f4b;
            flex-shrink: 0;
            transition: background .2s, color .2s, border-color .2s;
        }

        .qe-choice .t {
            font-size: 1.08rem;
            color: #334155;
            overflow-wrap: anywhere;
        }

        .qe-choice:has(input:checked) {
            border-color: var(--ml-green);
            background: var(--ml-green-light);
            box-shadow: 0 0 0 3px rgba(8, 101, 52, .12);
            transform: none;
        }

        .qe-choice:has(input:checked) .l {
            background: var(--ml-green);
            border-color: var(--ml-green);
            color: #fff;
        }

        .qe-choice:has(input:checked) .t {
            color: #14301f;
            font-weight: 600;
        }

        .qe-choice:has(input:focus-visible) {
            outline: 3px solid rgba(8, 101, 52, .35);
        }

        .qe-timer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
            padding: .7rem 1rem;
            border-radius: 12px;
            background: var(--ml-green-light);
            border: 1px solid #cfe8d6;
            color: var(--ml-green);
            transition: background .3s, color .3s, border-color .3s;
        }

        .qe-timer .lbl {
            font-size: .88rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .qe-timer b {
            font-size: 1.6rem;
            font-variant-numeric: tabular-nums;
        }

        .qe-timer.warn {
            background: #fef3c7;
            border-color: #fcd34d;
            color: #92400e;
        }

        .qe-timer.danger {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
            animation: qePulse 1s ease-in-out infinite;
        }

        @keyframes qePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.03);
            }
        }

        .qe-progress {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            color: #475569;
            font-size: .98rem;
            margin-bottom: .45rem;
        }

        .qe-progress b {
            color: var(--ml-green);
        }

        .qe-track {
            height: 10px;
            border-radius: 999px;
            background: #e3efe6;
            overflow: hidden;
        }

        .qe-track div {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, #22a35a, #086534);
            border-radius: 999px;
            transition: width .5s cubic-bezier(.2, .8, .2, 1);
        }

        /* ---------- Overlay ujian & peringatan ---------- */
        #exam-start-overlay {
            position: fixed;
            inset: 0;
            z-index: 9000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.2rem;
            background: linear-gradient(135deg, #e9f7ee 0%, #d4efdc 60%, #fdf3c4 140%);
        }

        .qe-exam-card {
            width: 100%;
            max-width: 560px;
            background: #fff;
            border-radius: 22px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 24px 60px rgba(8, 101, 52, .18);
            animation: qeUp .55s cubic-bezier(.2, .8, .2, 1) both;
        }

        .qe-exam-card .ic {
            width: 72px;
            height: 72px;
            margin: 0 auto 1rem;
            border-radius: 20px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            display: grid;
            place-items: center;
        }

        .qe-exam-card h2 {
            margin: 0 0 .5rem;
            font-size: 1.7rem;
            color: #14301f;
        }

        .qe-exam-card p {
            margin: 0 0 1rem;
            color: #475569;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .qe-rules {
            list-style: none;
            margin: 0 0 1.2rem;
            padding: 0;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .qe-rules li {
            display: flex;
            gap: .7rem;
            padding: .65rem .9rem;
            border-radius: 12px;
            background: #fffbea;
            border: 1px solid #f6e7a6;
            color: #5c4a00;
            font-size: 1rem;
        }

        .qe-agree {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            margin-bottom: 1.2rem;
            font-weight: 700;
            color: #14301f;
            cursor: pointer;
        }

        .qe-agree input {
            width: 20px;
            height: 20px;
            accent-color: var(--ml-green);
        }

        #cheat-warning {
            display: none;
            position: fixed;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            max-width: calc(100vw - 24px);
            background: #dc2626;
            color: #fff;
            padding: .85rem 1.2rem;
            border-radius: 14px;
            font-weight: 700;
            box-shadow: 0 12px 30px rgba(220, 38, 38, .4);
            align-items: center;
            gap: .9rem;
            flex-wrap: wrap;
        }

        #cheat-warning.show {
            display: flex;
            animation: qeUp .35s ease both;
        }

        #cheat-warning button {
            border: 0;
            border-radius: 8px;
            background: #fff;
            color: #b91c1c;
            font-family: inherit;
            font-weight: 800;
            padding: .4rem .8rem;
            cursor: pointer;
        }

        .qe-toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9998;
            background: #14301f;
            color: #fff;
            padding: .8rem 1.3rem;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .25);
            animation: qeUp .35s ease both;
        }

        .qe-done {
            text-align: center;
            padding: 2.5rem 1.5rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 18px;
            animation: qeUp .5s ease both;
        }

        .qe-done h2 {
            margin: 0 0 .5rem;
            color: #991b1b;
            font-size: 1.5rem;
        }

        .qe-done p {
            margin: 0 0 1.2rem;
            color: #b91c1c;
            font-size: 1.05rem;
        }

        @media (prefers-reduced-motion: reduce) {

            .qe-box,
            .qe-q,
            .qe-take,
            .qe-side,
            .qe-exam-card,
            .qe-done,
            #cheat-warning.show,
            .qe-toast,
            .qe-timer.danger {
                animation: none !important;
            }

            .qe-q,
            .qe-track div {
                transition: none;
            }
        }
    </style>

    {{-- ================= HEADER ================= --}}
    <div class="qe-head">
        <div>
            <a href="{{ $isTeacher ? route('teacher.quizzes') : route('siswa.kuis') }}" class="qe-back">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali
            </a>
            <h1 class="qe-title">{{ $assessment->title ?? 'Editor Soal Baru' }}</h1>
            @unless ($isTeacher)
                <div class="qe-chips">
                    <span class="qe-chip">{{ $typeLabel }}</span>
                    <span class="qe-chip">{{ $questions->count() }} soal</span>
                    @if ($limit)
                        <span class="qe-chip">{{ $limit }} menit</span>
                    @endif
                </div>
            @endunless
        </div>

        @if ($isTeacher)
            <button type="submit" form="quiz-form" class="qe-btn" id="save-top">
                <span class="qe-spin"></span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span class="lbl">Simpan Perubahan</span>
            </button>
        @endif
    </div>

    @if ($isTeacher)
        {{-- =====================================================
             1. TAMPILAN GURU (EDITOR KUIS)
             Nama field sama seperti sebelumnya, jadi controller tidak perlu diubah.
             ===================================================== --}}
        <form id="quiz-form" method="POST" action="{{ route('teacher.quizzes.update', $assessment) }}">
            @csrf
            @method('PUT')

            <div class="qe-layout">
                <div class="qe-main">

                    {{-- Pengaturan --}}
                    <section class="qe-box">
                        <div class="qe-box-head">
                            <span class="ic">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="4" y1="21" x2="4" y2="14"></line>
                                    <line x1="4" y1="10" x2="4" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12" y2="3"></line>
                                    <line x1="20" y1="21" x2="20" y2="16"></line>
                                    <line x1="20" y1="12" x2="20" y2="3"></line>
                                    <line x1="1" y1="14" x2="7" y2="14"></line>
                                    <line x1="9" y1="8" x2="15" y2="8"></line>
                                    <line x1="17" y1="16" x2="23" y2="16"></line>
                                </svg>
                            </span>
                            <h2>Pengaturan Kuis</h2>
                        </div>
                        <div class="qe-box-body">
                            <div class="qe-grid2">
                                <div class="qe-field">
                                    <label for="f-title">Judul kuis</label>
                                    <input id="f-title" type="text" name="title" class="qe-input"
                                        value="{{ $assessment->title ?? '' }}" placeholder="Contoh: Ujian Tengah Semester"
                                        required>
                                </div>
                                <div class="qe-field">
                                    <label for="f-class">Kelas</label>
                                    <select id="f-class" name="class_id" class="qe-input" required>
                                        <option value="">Pilih kelas…</option>
                                        @foreach ($classes ?? [] as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($assessment) && $assessment->class_id == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="qe-field">
                                    <label for="f-time">Batas waktu <em>menit</em></label>
                                    <input id="f-time" type="number" name="time_limit_minutes" class="qe-input"
                                        min="1" value="{{ $assessment->time_limit_minutes ?? 60 }}" required>
                                </div>
                                <div class="qe-field">
                                    <label for="f-due">Deadline pengumpulan <em>opsional</em></label>
                                    <input id="f-due" type="datetime-local" name="due_at" class="qe-input"
                                        value="{{ isset($assessment->due_at) ? \Carbon\Carbon::parse($assessment->due_at)->format('Y-m-d\TH:i') : '' }}">
                                </div>
                            </div>

                            <div>
                                <span class="qe-label">Mode pengerjaan</span>
                                <div class="qe-types">
                                    <label class="qe-type">
                                        <input type="radio" name="type" value="latihan"
                                            {{ $typeVal === 'latihan' ? 'checked' : '' }}>
                                        <span><b>Latihan biasa</b><small>Dikerjakan normal, tanpa pengawasan layar
                                                penuh.</small></span>
                                    </label>
                                    <label class="qe-type">
                                        <input type="radio" name="type" value="kuis"
                                            {{ $typeVal === 'kuis' ? 'checked' : '' }}>
                                        <span><b>Kuis</b><small>Terjadwal, nilai tercatat untuk rekap.</small></span>
                                    </label>
                                    <label class="qe-type">
                                        <input type="radio" name="type" value="ujian"
                                            {{ $typeVal === 'ujian' ? 'checked' : '' }}>
                                        <span><b>Ujian</b><small>Wajib layar penuh. Keluar layar atau pindah tab dicatat
                                                sebagai indikasi kecurangan.</small></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Daftar soal (dirender oleh JS) --}}
                    <div id="questions" style="display:flex;flex-direction:column;gap:1.4rem"></div>

                    <button type="button" class="qe-add" id="add-q">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah Soal Baru
                    </button>
                </div>

                <aside class="qe-side">
                    <h3>Ringkasan</h3>
                    <div class="qe-sum">
                        <div><b id="sum-q">0</b><small>Soal</small></div>
                        <div><b id="sum-p">0</b><small>Total poin</small></div>
                    </div>
                    <nav class="qe-nav" id="nav" aria-label="Loncat ke soal"></nav>
                    <button type="submit" class="qe-btn block" id="save-side">
                        <span class="qe-spin"></span>
                        <span class="lbl">Simpan Perubahan</span>
                    </button>
                    <p class="qe-hint">Klik huruf (A–D) pada tiap soal untuk menandai kunci jawaban yang benar.</p>
                </aside>
            </div>
        </form>

        {{-- Template satu soal --}}
        <template id="q-tpl">
            <article class="qe-q">
                <div class="qe-q-head">
                    <span class="qe-num">1</span>
                    <b>Soal <span class="n">1</span></b>
                    <button type="button" class="qe-del" title="Hapus soal" aria-label="Hapus soal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="qe-q-body">
                    <div class="qe-field">
                        <label>Teks pertanyaan</label>
                        <textarea class="qe-textarea" data-f="text" required></textarea>
                    </div>
                    <div class="qe-field">
                        <label>Pilihan jawaban <em>klik huruf untuk menandai kunci</em></label>
                        <div class="qe-opts">
                            @foreach (['A', 'B', 'C', 'D'] as $k => $letter)
                                <div class="qe-opt">
                                    <label class="qe-ltr" title="Tandai sebagai kunci jawaban">
                                        <input type="radio" data-f="correct" value="{{ $k }}">
                                        <span>{{ $letter }}</span>
                                    </label>
                                    <input type="text" data-f="opt" data-k="{{ $k }}"
                                        placeholder="Pilihan {{ $letter }}" required>
                                    <span class="qe-key">Kunci</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="qe-q-foot">
                        <label>Poin soal</label>
                        <input type="number" class="qe-input" data-f="points" min="1" required>
                    </div>
                </div>
            </article>
        </template>

        <script>
            (function() {
                var DATA = @json($questionData);
                var list = document.getElementById('questions');
                var tpl = document.getElementById('q-tpl');
                var nav = document.getElementById('nav');
                var uid = 0;

                function $(sel, ctx) {
                    return (ctx || document).querySelector(sel);
                }

                function summary() {
                    var cards = list.children,
                        pts = 0;
                    Array.prototype.forEach.call(cards, function(c) {
                        pts += parseInt($('[data-f=points]', c).value, 10) || 0;
                    });
                    document.getElementById('sum-q').textContent = cards.length;
                    document.getElementById('sum-p').textContent = pts;
                }

                function renumber() {
                    nav.innerHTML = '';
                    Array.prototype.forEach.call(list.children, function(c, idx) {
                        $('.qe-num', c).textContent = idx + 1;
                        $('.n', c).textContent = idx + 1;
                        var b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'qe-pill';
                        b.textContent = idx + 1;
                        b.addEventListener('click', function() {
                            c.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        });
                        nav.appendChild(b);
                    });
                    summary();
                }

                function build(d) {
                    var node = tpl.content.firstElementChild.cloneNode(true);
                    var i = uid++;
                    var t = $('[data-f=text]', node);
                    t.name = 'questions[' + i + '][text]';
                    t.value = d.text || '';
                    node.querySelectorAll('[data-f=opt]').forEach(function(inp) {
                        var k = parseInt(inp.getAttribute('data-k'), 10);
                        inp.name = 'questions[' + i + '][options][' + k + ']';
                        inp.value = (d.options && d.options[k]) || '';
                    });
                    var correct = Math.min(3, Math.max(0, parseInt(d.correct, 10) || 0));
                    node.querySelectorAll('[data-f=correct]').forEach(function(r) {
                        r.name = 'questions[' + i + '][correct_answer]';
                        r.checked = parseInt(r.value, 10) === correct;
                    });
                    var p = $('[data-f=points]', node);
                    p.name = 'questions[' + i + '][points]';
                    p.value = d.points || 10;
                    p.addEventListener('input', summary);

                    $('.qe-del', node).addEventListener('click', function() {
                        if (list.children.length <= 1) {
                            node.style.animation = 'qeShake .4s ease';
                            setTimeout(function() {
                                node.style.animation = '';
                            }, 420);
                            return;
                        }
                        node.classList.add('leaving');
                        setTimeout(function() {
                            node.remove();
                            renumber();
                        }, 200);
                    });
                    list.appendChild(node);
                    return node;
                }

                (DATA.length ? DATA : [{}]).forEach(build);
                renumber();

                document.getElementById('add-q').addEventListener('click', function() {
                    var node = build({
                        options: ['', '', '', ''],
                        correct: 0,
                        points: 10
                    });
                    renumber();
                    node.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    setTimeout(function() {
                        $('[data-f=text]', node).focus();
                    }, 350);
                });

                document.getElementById('quiz-form').addEventListener('submit', function() {
                    ['save-top', 'save-side'].forEach(function(id) {
                        var b = document.getElementById(id);
                        b.classList.add('loading');
                        b.disabled = true;
                        b.querySelector('.lbl').textContent = 'Menyimpan…';
                    });
                });
            })();
        </script>
    @else
        {{-- =====================================================
             2. TAMPILAN SISWA (MENGERJAKAN KUIS)
             ===================================================== --}}
        @if (isset($already) && $already >= $assessment->max_attempts)
            <div class="qe-done">
                <h2>Kesempatan habis</h2>
                <p>Kamu sudah mencapai batas maksimal pengerjaan untuk kuis ini.</p>
                <a href="{{ route('siswa.kuis') }}" class="qe-btn ghost">Kembali ke Daftar Kuis</a>
            </div>
        @else
            <div id="cheat-warning" role="alert">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                    </path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span id="cheat-text">Kamu keluar dari layar penuh atau pindah tab. Aktivitas ini dicatat.</span>
                <button type="button" id="cheat-back">Kembali ke layar penuh</button>
            </div>

            @if ($isExam)
                <div id="exam-start-overlay">
                    <div class="qe-exam-card">
                        <div class="ic">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3">
                                </path>
                            </svg>
                        </div>
                        <h2>Siap memulai ujian?</h2>
                        <p>{{ $assessment->title }} · {{ $questions->count() }}
                            soal{{ $limit ? ' · ' . $limit . ' menit' : '' }}
                        </p>
                        <ul class="qe-rules">
                            <li><span>🖥️</span><span>Layar akan dikunci ke mode layar penuh.</span></li>
                            <li><span>🚫</span><span>Keluar layar penuh atau pindah tab akan dicatat sebagai indikasi
                                    kecurangan.</span></li>
                            @if ($limit)
                                <li><span>⏱️</span><span>Waktu berjalan otomatis dan jawaban terkirim saat waktu
                                        habis.</span></li>
                            @endif
                        </ul>
                        <label class="qe-agree">
                            <input type="checkbox" id="agree"> Saya mengerti dan siap
                        </label>
                        <button type="button" class="qe-btn block" id="start-btn" disabled>Mulai Ujian Sekarang</button>
                    </div>
                </div>
            @endif

            <div id="quiz-content-wrapper" style="{{ $isExam ? 'display:none;' : '' }}">
                <form id="take-form" method="POST" action="{{ route('classroom.quiz.submit', $assessment) }}">
                    @csrf
                    <div class="qe-layout">
                        <div class="qe-main">
                            @foreach ($questions as $index => $q)
                                <article class="qe-take" id="q-{{ $index }}" style="--d: {{ $index * 60 }}ms">
                                    <div class="qe-take-head">
                                        <span class="qe-num">{{ $index + 1 }}</span>
                                        <b>Pertanyaan {{ $index + 1 }}</b>
                                        <span class="qe-pts">{{ $q->points ?? 10 }} poin</span>
                                    </div>
                                    <div class="qe-take-body">
                                        <p class="qe-qtext">{{ $q->question_text }}</p>
                                        <div class="qe-choices">
                                            @foreach ($q->options ?? [] as $i => $option)
                                                <label class="qe-choice">
                                                    <input type="radio" name="answers[{{ $q->id }}]"
                                                        value="{{ $i }}">
                                                    <span class="l">{{ ['A', 'B', 'C', 'D'][$i] ?? $i }}</span>
                                                    <span class="t">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <aside class="qe-side">
                            <h3>Pengerjaan</h3>
                            @if ($limit)
                                <div class="qe-timer" id="timer">
                                    <span class="lbl">Sisa waktu</span>
                                    <b id="timer-text">--:--</b>
                                </div>
                            @endif
                            <div>
                                <div class="qe-progress"><span>Terjawab</span><span><b id="ans">0</b> /
                                        {{ $questions->count() }}</span></div>
                                <div class="qe-track">
                                    <div id="bar"></div>
                                </div>
                            </div>
                            <nav class="qe-nav" aria-label="Loncat ke soal">
                                @foreach ($questions as $index => $q)
                                    <button type="button" class="qe-pill" data-target="q-{{ $index }}"
                                        id="pill-{{ $index }}">{{ $index + 1 }}</button>
                                @endforeach
                            </nav>
                            <button type="submit" class="qe-btn block" id="take-submit">
                                <span class="qe-spin"></span>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                                <span class="lbl">Selesai &amp; Kirim</span>
                            </button>
                        </aside>
                    </div>
                </form>
            </div>

            <script>
                (function() {
                    var IS_EXAM = @json($isExam);
                    var LIMIT = @json($limit); // menit, 0 = tanpa batas
                    var KEY = 'qz-end-' + @json($assessment->id);
                    var TOTAL = {{ $questions->count() }};
                    var form = document.getElementById('take-form');
                    var wrapper = document.getElementById('quiz-content-wrapper');
                    var timerBox = document.getElementById('timer');
                    var timerText = document.getElementById('timer-text');
                    var submitBtn = document.getElementById('take-submit');
                    var warn = document.getElementById('cheat-warning');
                    var examActive = false,
                        cheatCount = 0,
                        submitting = false,
                        warnTimer = null,
                        tickId = null;

                    /* ----- Progres jawaban & navigator ----- */
                    function refresh() {
                        var done = 0;
                        document.querySelectorAll('.qe-take').forEach(function(card, i) {
                            var ok = !!card.querySelector('input:checked');
                            if (ok) done++;
                            var pill = document.getElementById('pill-' + i);
                            if (pill) pill.classList.toggle('done', ok);
                        });
                        document.getElementById('ans').textContent = done;
                        document.getElementById('bar').style.width = (TOTAL ? done / TOTAL * 100 : 0) + '%';
                    }
                    form.addEventListener('change', refresh);
                    document.querySelectorAll('.qe-pill[data-target]').forEach(function(p) {
                        p.addEventListener('click', function() {
                            document.getElementById(p.getAttribute('data-target')).scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        });
                    });
                    refresh();

                    function toast(msg) {
                        var t = document.createElement('div');
                        t.className = 'qe-toast';
                        t.textContent = msg;
                        document.body.appendChild(t);
                        setTimeout(function() {
                            t.remove();
                        }, 3200);
                    }

                    /* ----- Kirim jawaban ----- */
                    function setLoading() {
                        submitting = true;
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                        submitBtn.querySelector('.lbl').textContent = 'Mengirim…';
                    }
                    form.addEventListener('submit', function(e) {
                        var missing = Array.prototype.filter.call(document.querySelectorAll('.qe-take'), function(c) {
                            return !c.querySelector('input:checked');
                        });
                        if (missing.length) {
                            e.preventDefault();
                            var first = missing[0];
                            first.classList.remove('missing');
                            void first.offsetWidth;
                            first.classList.add('missing');
                            first.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            setTimeout(function() {
                                first.classList.remove('missing');
                            }, 1600);
                            toast('Masih ada ' + missing.length + ' soal yang belum dijawab.');
                            return;
                        }
                        sessionStorage.removeItem(KEY);
                        setLoading();
                    });

                    /* ----- Timer (disimpan di sessionStorage agar tidak reset saat refresh) ----- */
                    function startTimer() {
                        if (!LIMIT || !timerBox) return;
                        var end = parseInt(sessionStorage.getItem(KEY) || '0', 10);
                        if (!end || end <= Date.now()) {
                            end = Date.now() + LIMIT * 60000;
                            sessionStorage.setItem(KEY, String(end));
                        }

                        function tick() {
                            var left = Math.max(0, Math.ceil((end - Date.now()) / 1000));
                            var m = Math.floor(left / 60),
                                s = left % 60;
                            timerText.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                            timerBox.classList.toggle('warn', left <= 300 && left > 60);
                            timerBox.classList.toggle('danger', left <= 60);
                            if (left <= 0) {
                                clearInterval(tickId);
                                sessionStorage.removeItem(KEY);
                                toast('Waktu habis. Jawabanmu dikirim otomatis.');
                                setLoading();
                                form.submit(); // melewati validasi: kirim apa adanya
                            }
                        }
                        tick();
                        tickId = setInterval(tick, 500);
                    }

                    window.addEventListener('beforeunload', function(e) {
                        if (!submitting && (examActive || document.querySelector('.qe-take input:checked'))) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });

                    /* ----- Mode ujian ----- */
                    function goFullscreen() {
                        var el = document.documentElement;
                        var fn = el.requestFullscreen || el.webkitRequestFullscreen || el.msRequestFullscreen;
                        if (fn) {
                            try {
                                fn.call(el);
                            } catch (err) {}
                        }
                    }

                    function cheat(reason) {
                        if (!examActive || submitting) return;
                        cheatCount++;
                        document.getElementById('cheat-text').textContent =
                            'Pelanggaran ke-' + cheatCount + ': ' + reason + ' Aktivitas ini dicatat oleh sistem.';
                        warn.classList.add('show');
                        clearTimeout(warnTimer);
                        warnTimer = setTimeout(function() {
                            warn.classList.remove('show');
                        }, 8000);
                    }

                    if (IS_EXAM) {
                        var agree = document.getElementById('agree');
                        var startBtn = document.getElementById('start-btn');
                        agree.addEventListener('change', function() {
                            startBtn.disabled = !agree.checked;
                        });
                        startBtn.addEventListener('click', function() {
                            goFullscreen();
                            document.getElementById('exam-start-overlay').style.display = 'none';
                            wrapper.style.display = 'block';
                            document.documentElement.classList.add('exam-mode');
                            examActive = true;
                            startTimer();
                        });
                        document.getElementById('cheat-back').addEventListener('click', function() {
                            goFullscreen();
                            warn.classList.remove('show');
                        });
                        document.addEventListener('fullscreenchange', function() {
                            if (!document.fullscreenElement) cheat('keluar dari layar penuh.');
                        });
                        document.addEventListener('visibilitychange', function() {
                            if (document.hidden) cheat('pindah tab atau jendela.');
                        });
                    } else {
                        startTimer();
                    }
                })();
            </script>
        @endif
    @endif
@endsection

@extends('layouts.app')

@section('title', 'Lembar Jawaban Kuis | MathLeaf')
@section('header_title', 'Lembar Jawaban Kuis')

@section('content')
    <style>
        .ml-review {
            max-width: 920px;
            margin: 0 auto 50px;
            color: #163f2b
        }

        .ml-review-box {
            padding: 24px;
            background: #fff;
            border: 1px solid #d7e8de;
            border-radius: 16px;
            margin-bottom: 15px
        }

        .ml-review h1 {
            font-size: 26px;
            color: #086534
        }

        .ml-review h2 {
            font-size: 17px;
            line-height: 1.5
        }

        .ml-review p {
            margin: 8px 0;
            color: #52665b
        }

        .ml-review a {
            color: #086534;
            font-weight: 700
        }

        .ml-review-score {
            font-size: 36px;
            color: #086534;
            font-weight: 800
        }
    </style>
    <div class="ml-review">
        <p><a href="{{ route('teacher.quizzes') }}">← Kembali ke daftar kuis</a></p>
        <div class="ml-review-box">
            <h1>{{ $attempt->assessment->title }}</h1>
            <p>Siswa: <strong>{{ $attempt->user?->name ?? 'Siswa' }}</strong> · Percobaan ke-{{ $attempt->attempt_number }}
            </p>
            <p>Diselesaikan: {{ $attempt->submitted_at?->format('d M Y H:i') ?? '-' }}</p>
            <div class="ml-review-score">{{ number_format((float) $attempt->score, 1) }} / 100</div>
            <p
                style="font-weight:750;color:{{ (float) $attempt->score >= ($attempt->assessment->passing_score ?? 70) ? '#166534' : '#ab501d' }}">
                {{ (float) $attempt->score >= ($attempt->assessment->passing_score ?? 70) ? 'Tuntas' : 'Belum tuntas' }} ·
                KKM {{ $attempt->assessment->passing_score ?? 70 }}
            </p>
        </div>
        @foreach ($attempt->answers as $i => $answer)
            @php
                $options = $answer->question?->options ?? [];
                $isShort = $answer->question?->type === 'short_answer';
                $answerIndex = (int) $answer->answer_text;
                $correctIndex = (int) ($answer->question?->correct_answer ?? -1);
                $displayAnswer = $isShort ? $answer->answer_text : (is_array($options) ? ($options[$answerIndex] ?? '-') : '-');
                $displayCorrect = $isShort ? $answer->question?->correct_answer : (is_array($options) ? ($options[$correctIndex] ?? '-') : '-');
            @endphp
            <div class="ml-review-box">
                <h2>{{ $i + 1 }}. {{ $answer->question?->question_text ?? 'Soal sudah tidak tersedia' }}</h2>
                <p>Jawaban siswa: {{ $displayAnswer }}</p>
                <p>Jawaban benar: {{ $displayCorrect }}</p>
                <p style="font-weight:750;color:{{ $answer->is_correct ? '#166534' : '#be123c' }}">
                    {{ $answer->is_correct ? 'Benar' : 'Salah' }} · {{ $answer->points_awarded ?? 0 }} poin</p>
                @if ($answer->feedback)
                    <p>Pembahasan: {{ $answer->feedback }}</p>
                @endif
            </div>
        @endforeach
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Hasil Kuis | MathLeaf')
@section('header_title', 'Hasil Kuis')

@section('content')
<style>
  .ml-result{max-width:900px;margin:0 auto 60px}
  .ml-result-hero{padding:32px 26px;border:1px solid #d9e8df;background:#fff;border-radius:18px;text-align:center;margin-bottom:22px}
  .ml-result-hero h1{font-size:24px;font-weight:750;color:#086534;margin-bottom:8px}
  .ml-result-hero p{color:#64748b}
  .ml-result-score{font-size:56px;font-weight:850;color:#086534;margin:10px 0}
  .ml-result-item{background:#fff;border:1px solid #e0e9e3;border-radius:14px;margin-bottom:12px;padding:21px}
  .ml-result-item h2{font-size:17px;font-weight:700;color:#24372e;line-height:1.5;margin-bottom:10px}
  .ml-result-item p{margin:5px 0;color:#475569}
  .ml-result-correct{color:#166534!important}
  .ml-result-wrong{color:#be123c!important}
  .ml-result-back{background:#086534;color:#fff;padding:12px 22px;border-radius:10px;text-decoration:none;display:inline-block;font-weight:700}
</style>
<div class="ml-result">
  <div class="ml-result-hero">
    <h1>{{ $attempt->assessment?->title ?? 'Hasil Kuis' }}</h1>
    <p>Percobaan ke-{{ $attempt->attempt_number }} · {{ $attempt->submitted_at?->format('d M Y H:i') ?? '-' }}</p>
    <div class="ml-result-score">{{ number_format((float) $attempt->score, 1) }}<span style="font-size:20px;color:#94a3b8"> / 100</span></div>
    @if (session('success')) <p>{{ session('success') }}</p> @endif
    <a class="ml-result-back" href="{{ route('siswa.kuis') }}">Kembali ke Kuis</a>
  </div>
  @foreach ($attempt->answers as $index => $answer)
    <div class="ml-result-item">
      <h2>{{ $index + 1 }}. {{ $answer->question?->question_text ?? 'Soal tidak tersedia' }}</h2>
      @php
        $choices = $answer->question?->options ?? [];
        $selected = is_array($choices) ? ($choices[(int) $answer->answer_text] ?? '-') : '-';
        $correctText = is_array($choices) ? ($choices[(int) $answer->question?->correct_answer] ?? '-') : '-';
      @endphp
      <p>Jawaban kamu: <strong>{{ $selected }}</strong></p>
      <p class="{{ $answer->is_correct ? 'ml-result-correct' : 'ml-result-wrong' }}">
        {{ $answer->is_correct ? 'Benar' : 'Belum tepat' }} · {{ number_format((float) $answer->points_awarded, 0) }} poin
      </p>
      @unless ($answer->is_correct)
        <p>Jawaban yang benar: <strong>{{ $correctText }}</strong></p>
      @endunless
      @if ($answer->feedback)
        <p>Pembahasan: {{ $answer->feedback }}</p>
      @endif
    </div>
  @endforeach
</div>
@endsection

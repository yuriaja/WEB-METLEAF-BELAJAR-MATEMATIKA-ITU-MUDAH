@extends('layouts.app')

@section('title', 'Kerjakan Kuis | MathLeaf')
@section('header_title', 'Kerjakan Kuis')

@section('content')
<style>
  .ml-quiz-wrap{max-width:900px;margin:0 auto 60px;color:#183229}
  .ml-quiz-intro{background:#fff;border:1px solid #dae9df;border-radius:18px;padding:26px;margin-bottom:22px}
  .ml-quiz-intro h1{font-size:28px;font-weight:800;margin:8px 0;color:#076436}
  .ml-quiz-intro p{color:#64748b;margin:0}
  .ml-question{background:white;border:1px solid #e1e9e3;border-radius:16px;padding:24px;margin-bottom:18px}
  .ml-question h2{font-size:17px;font-weight:750;line-height:1.65;margin-bottom:18px}
  .ml-option{display:flex;align-items:center;gap:13px;border:1px solid #dae5de;padding:14px 16px;border-radius:11px;margin-bottom:10px;cursor:pointer;line-height:1.5}
  .ml-option:hover{border-color:#0b8345;background:#effaf2}
  .ml-option:has(input:checked){border-color:#086534;background:#ecf8ef}
  .ml-option input{accent-color:#086534;width:17px;height:17px;flex:none}
  .ml-quiz-actions{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
  .ml-quiz-link{display:inline-flex;padding:13px 22px;border:1px solid #cedbd0;border-radius:11px;text-decoration:none;color:#2e5940;font-weight:650;background:white}
  .ml-quiz-submit{background:#086534;color:white;padding:14px 24px;border:none;border-radius:11px;font-weight:750;cursor:pointer}
  .ml-quiz-submit:hover{background:#064c28}
  .ml-quiz-warning{background:#fff8e7;color:#805b0d;border-radius:10px;padding:13px 16px;margin-bottom:20px}
</style>

<div class="ml-quiz-wrap">
  <div class="ml-quiz-intro">
    <a href="{{ route('siswa.kuis') }}" style="color:#086534;text-decoration:none;font-weight:650">← Kembali ke daftar kuis</a>
    <h1>{{ $assessment->title }}</h1>
    <p>{{ $assessment->questions->count() }} soal · Kesempatan ke-{{ $used + 1 }} dari {{ max(1, (int) $assessment->max_attempts) }}{{ $assessment->time_limit_minutes ? ' · Alokasi waktu '.$assessment->time_limit_minutes.' menit' : '' }}</p>
  </div>

  @if ($errors->any())
    <div class="ml-quiz-warning">{{ $errors->first() }}</div>
  @endif
  <div class="ml-quiz-warning">Pastikan semua soal terjawab sebelum mengirim. Setelah dikirim, jawaban tidak dapat diubah.</div>

  <form method="POST" action="{{ route('classroom.quiz.submit', $assessment) }}" id="mathleaf-quiz-form">
    @csrf
    @foreach ($assessment->questions as $index => $question)
      <section class="ml-question">
        <h2>{{ $index + 1 }}. {{ $question->question_text }}</h2>
        @foreach (is_array($question->options) ? $question->options : [] as $key => $option)
          <label class="ml-option">
            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" required
              @checked((string) old('answers.'.$question->id) === (string) $key)>
            <span><strong>{{ ['A', 'B', 'C', 'D'][$key] ?? ($key + 1) }}.</strong> {{ $option }}</span>
          </label>
        @endforeach
      </section>
    @endforeach
    <div class="ml-quiz-actions">
      <a class="ml-quiz-link" href="{{ route('siswa.kuis') }}">Kembali</a>
      <button class="ml-quiz-submit" type="submit" id="ml-submit">Kirim Jawaban</button>
    </div>
  </form>
</div>
<script>
  document.getElementById('mathleaf-quiz-form')?.addEventListener('submit', function () {
    const button = document.getElementById('ml-submit');
    if (button) { button.disabled = true; button.textContent = 'Mengirim jawaban…'; }
  });
</script>
@endsection

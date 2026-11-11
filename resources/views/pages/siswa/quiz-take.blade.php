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
  .ml-exam-gate{max-width:660px;margin:26px auto;padding:32px 24px;text-align:center;background:white;border:1px solid #cfe6d7;border-radius:22px;box-shadow:0 16px 45px rgba(8,101,52,.09)}
  .ml-exam-gate h2{color:#086534;font-size:1.6rem;margin:0 0 12px}
  .ml-exam-gate p{color:#52695b;line-height:1.7;margin:0 0 18px}
  .ml-exam-gate button{border:0;border-radius:13px;padding:14px 24px;background:#086534;color:#fff;font-weight:750;cursor:pointer}
  .ml-exam-gate button:hover{background:#064c28}
  .ml-exam-error{color:#b42318;font-weight:700;margin:14px 0 0}
  [hidden],.ml-quiz-wrap[hidden],.ml-exam-gate[hidden]{display:none !important}
  .ml-quiz-wrap:fullscreen{width:100%;max-width:none;height:100vh;box-sizing:border-box;margin:0;padding:28px max(20px,calc((100vw - 980px)/2));overflow-y:auto;background:#f5fbf6}
  .ml-quiz-wrap:fullscreen .ml-quiz-link,.ml-quiz-wrap:fullscreen .ml-quiz-intro a{display:none}
  .ml-quiz-wrap:fullscreen .ml-quiz-intro{border-color:#b6dfc3}
  @media(max-width:600px){.ml-exam-gate{margin:10px}.ml-quiz-wrap:fullscreen{padding:16px}}
</style>

@if ($assessment->type === 'ujian')
  <div class="ml-exam-gate" id="ml-exam-start-gate">
    <h2>Mode Ujian Matematika</h2>
    <p>Ujian ini menggunakan layar penuh. Pindah tab atau keluar layar penuh akan tercatat dan memerlukan Anda masuk layar penuh lagi.</p>
    <p>Website tidak dapat memblokir Alt+Tab atau aplikasi lain. Catatan akan ditinjau guru, bukan otomatis dianggap curang.</p>
    <button type="button" id="ml-exam-start">Mulai Ujian dalam Layar Penuh</button>
    <p id="ml-exam-start-error" class="ml-exam-error" role="alert" hidden></p>
  </div>
  <div class="ml-exam-gate" id="ml-exam-resume-gate" hidden>
    <h2>Layar Penuh Terhenti</h2>
    <p>Jawaban yang sudah Anda pilih pada halaman ini masih tersimpan. Aktifkan kembali layar penuh untuk melanjutkan.</p>
    <button type="button" id="ml-exam-resume">Aktifkan Kembali Layar Penuh</button>
    <p id="ml-exam-resume-error" class="ml-exam-error" role="alert" hidden></p>
  </div>
@endif
<div class="ml-quiz-wrap" id="ml-quiz-area" @if($assessment->type === 'ujian') hidden @endif>
  <div class="ml-quiz-intro">
    <a href="{{ route('siswa.kuis') }}" style="color:#086534;text-decoration:none;font-weight:650">← Kembali ke daftar kuis</a>
    <h1>{{ $assessment->title }}</h1>
    <p>{{ $assessment->questions->count() }} soal · Kesempatan ke-{{ $used + 1 }} dari {{ max(1, (int) $assessment->max_attempts) }}{{ $assessment->time_limit_minutes ? ' · Alokasi waktu '.$assessment->time_limit_minutes.' menit' : '' }}</p>
    <p style="margin-top:8px;color:#087344;font-weight:700">Nilai ketuntasan: {{ $assessment->passing_score ?? 70 }}/100. Jika belum tuntas, pelajari kembali materi yang direkomendasikan setelah mengirim jawaban.</p>
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
        @if ($question->attachment_path)
          @php
            $ext = strtolower(pathinfo($question->attachment_path, PATHINFO_EXTENSION));
          @endphp
          @if (in_array($ext, ['jpg','jpeg','png','webp'], true))
            <p><img src="{{ route('classroom.quiz.attachment', $question) }}?preview=1" alt="Gambar pendukung soal {{ $index + 1 }}" style="max-width:100%;height:auto;border-radius:10px"></p>
          @endif
          <p><a href="{{ route('classroom.quiz.attachment', $question) }}" style="color:#086534" target="_blank" rel="noopener">📎 {{ $question->attachment_name ?? 'Unduh lampiran soal' }}</a></p>
        @endif
        @if ($question->resource_url)
          <p><a href="{{ $question->resource_url }}" target="_blank" rel="noopener noreferrer" style="color:#086534">🔗 Buka video / tautan soal</a></p>
        @endif
        @if ($question->type === 'short_answer')
          <input type="text" class="ml-option" style="width:100%;box-sizing:border-box;cursor:text" name="answers[{{ $question->id }}]"
            value="{{ old('answers.'.$question->id) }}" maxlength="1000" placeholder="Tulis jawaban singkat..." required>
        @else
        @foreach (is_array($question->options) ? $question->options : [] as $key => $option)
          <label class="ml-option">
            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" required
              @checked((string) old('answers.'.$question->id) === (string) $key)>
            <span><strong>{{ ['A', 'B', 'C', 'D'][$key] ?? ($key + 1) }}.</strong> {{ $option }}</span>
          </label>
        @endforeach
        @endif
      </section>
    @endforeach
    <div class="ml-quiz-actions">
      <a class="ml-quiz-link" href="{{ route('siswa.kuis') }}">Kembali</a>
      <button class="ml-quiz-submit" type="submit" id="ml-submit">Kirim Jawaban</button>
    </div>
  </form>
</div>
<script>
  const examMode = @json($assessment->type === 'ujian');
  const examArea = document.getElementById('ml-quiz-area');
  let examStarted = false;
  let examSubmitting = false;

  if (examMode) {
    const startGate = document.getElementById('ml-exam-start-gate');
    const resumeGate = document.getElementById('ml-exam-resume-gate');
    const incidentUrl = @json(route('classroom.quiz.incident', $assessment));
    const csrfToken = @json(csrf_token());

    function reportExamIncident(eventType) {
      const body = new FormData();
      body.append('_token', csrfToken);
      body.append('event_type', eventType);
      if (navigator.sendBeacon && navigator.sendBeacon(incidentUrl, body)) return;
      fetch(incidentUrl, { method: 'POST', body, credentials: 'same-origin', keepalive: true })
        .catch(() => {});
    }

    async function activateExam(fromResume) {
      const error = document.getElementById(fromResume ? 'ml-exam-resume-error' : 'ml-exam-start-error');
      error.hidden = true;
      examArea.hidden = false;
      try {
        if (typeof examArea.requestFullscreen !== 'function') {
          throw new Error('Browser ini tidak mendukung layar penuh. Gunakan Chrome atau Edge versi terbaru.');
        }
        // HARUS langsung dipicu klik siswa; jangan dijalankan otomatis saat page load.
        await examArea.requestFullscreen();
        if (document.fullscreenElement !== examArea) {
          throw new Error('Layar penuh tidak berhasil diaktifkan.');
        }
        examStarted = true;
        startGate.hidden = true;
        resumeGate.hidden = true;
      } catch (err) {
        examArea.hidden = true;
        (fromResume ? resumeGate : startGate).hidden = false;
        error.textContent = err.message || 'Gagal mengaktifkan layar penuh. Periksa izin browser.';
        error.hidden = false;
      }
    }

    document.getElementById('ml-exam-start').addEventListener('click', () => activateExam(false));
    document.getElementById('ml-exam-resume').addEventListener('click', () => activateExam(true));

    document.addEventListener('fullscreenchange', () => {
      if (!examStarted || examSubmitting || document.fullscreenElement === examArea) return;
      examArea.hidden = true;
      resumeGate.hidden = false;
      reportExamIncident('fullscreen_exit');
    });

    document.addEventListener('visibilitychange', () => {
      if (examStarted && !examSubmitting && document.hidden) reportExamIncident('tab_hidden');
    });
  }

  document.getElementById('mathleaf-quiz-form')?.addEventListener('submit', function (event) {
    if (examMode && document.fullscreenElement !== examArea) {
      event.preventDefault();
      document.getElementById('ml-exam-resume-gate').hidden = false;
      examArea.hidden = true;
      return;
    }
    examSubmitting = true;
    const button = document.getElementById('ml-submit');
    if (button) { button.disabled = true; button.textContent = 'Mengirim jawaban…'; }
  });
</script>
@endsection

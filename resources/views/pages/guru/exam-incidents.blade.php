@extends('layouts.app')
@section('title', 'Catatan Pengawasan Ujian | MathLeaf')
@section('header_title', 'Catatan Pengawasan Ujian')
@section('content')
<style>
.exam-log-wrap{max-width:1050px;margin:0 auto;color:#183229}
.exam-log-header{background:#fff;border:1px solid #dcece2;border-radius:18px;padding:24px;margin-bottom:20px}
.exam-log-header h1{margin:12px 0 4px;font-size:1.6rem;color:#086534;font-weight:800}
.exam-log-sub{color:#596f62;line-height:1.6}
.exam-log-back{color:#086534;text-decoration:none;font-weight:700}
.exam-log-table-wrap{overflow:auto;background:white;border:1px solid #dcece2;border-radius:16px}
.exam-log-table{border-collapse:collapse;width:100%;min-width:560px}
.exam-log-table th,.exam-log-table td{padding:15px 18px;text-align:left;border-bottom:1px solid #e8eee9}
.exam-log-table th{background:#f1f9f3;color:#315542;font-size:.88rem}
.exam-log-badge{padding:5px 10px;border-radius:20px;font-weight:700;font-size:.83rem;background:#fff2df;color:#99550a}
.exam-log-empty{background:#f4fbf6;border:1px dashed #bfdcca;padding:28px;text-align:center;border-radius:14px}
</style>
<div class="exam-log-wrap">
  <div class="exam-log-header">
    <a class="exam-log-back" href="{{ route('teacher.quizzes') }}">← Kembali ke Manajemen Kuis</a>
    <h1>{{ $assessment->title }}</h1>
    <p class="exam-log-sub">Catatan keluar layar penuh dan berpindah tab dalam mode Ujian.
      Data ini adalah <strong>indikasi aktivitas browser, bukan bukti pasti kecurangan</strong>.
      Gangguan browser atau notifikasi juga bisa menimbulkan catatan.</p>
    <p class="exam-log-sub">Total catatan: <strong>{{ $incidents->total() }}</strong></p>
  </div>
  @if($incidents->isEmpty())
    <div class="exam-log-empty">Belum ada catatan perpindahan tab atau keluar fullscreen pada ujian ini.</div>
  @else
    <div class="exam-log-table-wrap">
      <table class="exam-log-table">
        <thead><tr><th>Nama Siswa</th><th>Aktivitas Terdeteksi</th><th>Waktu (WIB)</th></tr></thead>
        <tbody>
          @foreach($incidents as $incident)
            <tr>
              <td>{{ $incident->student_name }}</td>
              <td><span class="exam-log-badge">{{ $incident->event_type === 'tab_hidden' ? 'Beralih tab / halaman tersembunyi' : 'Keluar layar penuh' }}</span></td>
              <td>{{ \Carbon\Carbon::parse($incident->created_at)->timezone('Asia/Jakarta')->format('d M Y H:i:s') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div style="margin-top:18px">{{ $incidents->links() }}</div>
  @endif
</div>
@endsection

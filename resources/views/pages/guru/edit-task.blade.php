@extends('layouts.app')
@section('title', 'Edit Tugas | MathLeaf')
@section('header_title', 'Edit Tugas')
@section('content')
<style>
.mle-box{max-width:780px;margin:auto;background:#fff;border-radius:16px;border:1px solid #dceae0;padding:26px;box-shadow:0 4px 18px rgba(18,68,44,.04)}.mle-box h1{font-size:1.7rem;color:#0f5e38;margin:0 0 7px}.mle-box p{color:#64748b}.mle-field{margin:18px 0}.mle-field label{display:block;font-weight:750;margin-bottom:7px}.mle-field input,.mle-field select,.mle-field textarea{width:100%;border:1px solid #cbd5e1;border-radius:9px;padding:12px;background:white}.mle-actions{display:flex;gap:11px;flex-wrap:wrap}.mle-btn{background:#087541;color:#fff;text-decoration:none;border:0;border-radius:9px;padding:12px 20px;font-weight:700;cursor:pointer}.mle-btn.alt{background:#e8f3ec;color:#0e6040}.mle-error{background:#fff1f1;padding:14px;border-radius:9px;color:#9d2828}
</style>
<section class="mle-box">
    <h1>Edit Tugas FPBL</h1><p>Ubah instruksi, tahap, kelas atau deadline tugas. Pengumpulan dan nilai siswa tidak akan terhapus.</p>
    @if($activity->submissions()->exists())<p><strong>Catatan:</strong> Sudah ada pengumpulan siswa. Kelas tugas tidak bisa diganti.</p>@endif
    @if($errors->any())<div class="mle-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('classroom.tasks.update', $activity) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="mle-field"><label for="mle-class">Kelas</label><select id="mle-class" name="class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}" @selected((int) old('class_id', $activity->class_id) === (int) $class->id)>{{ $class->name }}</option>@endforeach</select></div>
        <div class="mle-field"><label for="mle-title">Judul tugas</label><input id="mle-title" name="title" type="text" maxlength="255" required value="{{ old('title', $activity->title) }}"></div>
        <div class="mle-field"><label for="mle-instructions">Instruksi</label><textarea id="mle-instructions" name="instructions" rows="8" maxlength="10000" required>{{ old('instructions', $activity->instructions) }}</textarea></div>
        <div class="mle-field"><label for="mle-attachment">Lampiran Guru (Opsional, maksimal 20 MB)</label><input id="mle-attachment" type="file" name="attachment" accept="image/jpeg,image/png,image/webp,.pdf,.doc,.docx,.ppt,.pptx,.mp4,.webm">
            @if ($activity->attachment_path)
                <p><a href="{{ route('classroom.tasks.attachment', $activity) }}" target="_blank" rel="noopener">📎 {{ $activity->attachment_name ?? 'Lampiran tugas' }}</a></p>
                <label><input style="width:auto" type="checkbox" name="remove_attachment" value="1"> Hapus lampiran lama</label>
            @endif
        </div>
        <div class="mle-field"><label for="mle-resource-url">Link YouTube / Referensi (Opsional)</label><input id="mle-resource-url" name="resource_url" type="url" value="{{ old('resource_url', $activity->resource_url) }}" placeholder="https://youtu.be/..."></div>
        <div class="mle-field"><label for="mle-stage">Tahap FPBL</label><select id="mle-stage" name="fpbl_stage" required>@foreach([1=>'Mengamati',2=>'Eksplorasi',3=>'Investigasi',4=>'Presentasi'] as $stage=>$label)<option value="{{ $stage }}" @selected((int) old('fpbl_stage', $activity->fpbl_stage) === $stage)>{{ $stage }} — {{ $label }}</option>@endforeach</select></div>
        <div class="mle-field"><label for="mle-due">Deadline (boleh kosong)</label><input id="mle-due" name="due_at" type="datetime-local" value="{{ old('due_at', $activity->due_at?->format('Y-m-d\\TH:i')) }}"></div>
        <div class="mle-actions"><button type="submit" class="mle-btn">Simpan perubahan</button><a class="mle-btn alt" href="{{ route('classroom.tasks.index') }}">Kembali</a></div>
    </form>
</section>
@endsection

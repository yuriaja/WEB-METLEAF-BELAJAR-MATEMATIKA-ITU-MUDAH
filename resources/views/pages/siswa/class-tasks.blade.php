@extends('layouts.app')
@section('title', 'Tugas Kelas | MathLeaf')
@section('header_title', 'Tugas & Kelompok')

@section('content')
<style>
.mlt-shell{max-width:1150px;margin:auto;color:#213047}.mlt-head h1{font-size:1.8rem;font-weight:800;color:#12442c;margin:0}.mlt-head p{color:#64748b;margin:.4rem 0 1.4rem}
.mlt-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:22px}.mlt-stat{padding:19px;border:1px solid #dbeae0;background:#fff;border-radius:14px}.mlt-stat strong{display:block;font-size:1.85rem;color:#116b3c}.mlt-stat span{color:#64748b;font-size:.9rem}
.mlt-filter{background:#fff;border:1px solid #e2e8f0;padding:15px;border-radius:12px;display:flex;align-items:end;gap:14px;flex-wrap:wrap;margin-bottom:20px}.mlt-filter label{font-weight:700;font-size:.9rem;display:block;margin-bottom:5px}.mlt-filter select{min-width:170px;padding:10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff}.mlt-filter button,.mlt-btn{display:inline-flex;justify-content:center;align-items:center;gap:8px;border:0;border-radius:9px;padding:11px 17px;background:#087541;color:white;font-weight:700;cursor:pointer;text-decoration:none}
.mlt-list{display:grid;gap:18px}.mlt-task{border:1px solid #dbe7de;background:#fff;border-radius:15px;overflow:hidden;box-shadow:0 3px 12px rgba(25,60,33,.035)}.mlt-task-top{padding:20px 22px;border-bottom:1px solid #edf1ec}.mlt-task-top h2{font-weight:800;font-size:1.25rem;color:#173d28;margin:10px 0 8px}.mlt-meta{display:flex;gap:8px;align-items:center;flex-wrap:wrap;font-size:.84rem;color:#526175}.mlt-pill{display:inline-block;padding:5px 11px;border-radius:99px;background:#e9f7ef;color:#11673d;font-weight:750}.mlt-pill.warn{background:#fff0dc;color:#9a530a}.mlt-pill.good{background:#dff8e8;color:#0e6841}.mlt-pill.late{background:#fce8e8;color:#a62a2a}
.mlt-body{padding:20px 22px}.mlt-instructions{white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.7;margin:10px 0 16px;background:#f7faf7;padding:14px;border-radius:10px;border:1px solid #e7eee7}.mlt-fields{display:grid;gap:12px}.mlt-fields label{font-weight:700;display:block;margin-bottom:6px}.mlt-fields textarea,.mlt-fields input[type=file]{width:100%;border:1px solid #cbd5e1;border-radius:9px;padding:11px;background:#fff;color:#162f21}.mlt-note{font-size:.87rem;color:#64748b;margin:7px 0}.mlt-feedback{background:#eaf8f0;border:1px solid #ccecd9;border-radius:10px;padding:14px;margin-top:13px}.mlt-muted{color:#64748b}.mlt-empty{text-align:center;background:white;padding:42px 20px;border:1px dashed #a9c7b8;border-radius:12px}.mlt-alert{padding:14px;border-radius:10px;margin-bottom:18px;background:#ecfdf3;color:#146134;border:1px solid #b6e9c7}.mlt-alert.error{background:#fff1f1;color:#9d2828;border-color:#f5b5b5}.mlt-link{color:#087541;font-weight:700;text-decoration:underline}
@media(max-width:640px){.mlt-stats{grid-template-columns:repeat(3,minmax(0,1fr));gap:7px}.mlt-stat{padding:11px}.mlt-stat strong{font-size:1.3rem}.mlt-stat span{font-size:.73rem}.mlt-task-top,.mlt-body{padding:15px}.mlt-filter select{min-width:120px;width:100%}.mlt-filter>div{flex:1 1 130px}}
</style>
<div class="mlt-shell">
    @if (session('success'))<div class="mlt-alert" role="status">{{ session('success') }}</div>@endif
    @if ($errors->any())
        <div class="mlt-alert error" role="alert"><strong>Periksa kembali:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <header class="mlt-head"><h1>Tugas Kelas Saya</h1><p>Kerjakan tugas sesuai tahap FPBL. Pengumpulan dan nilai tersimpan otomatis.</p></header>
    <div class="mlt-stats">
        <div class="mlt-stat"><strong>{{ $allTasks->count() }}</strong><span>Total tugas</span></div>
        <div class="mlt-stat"><strong>{{ $submittedCount }}</strong><span>Sudah dikumpulkan</span></div>
        <div class="mlt-stat"><strong>{{ $gradedCount }}</strong><span>Sudah dinilai</span></div>
    </div>
    <form method="GET" action="{{ route('classroom.tasks.index') }}" class="mlt-filter">
        <div><label for="mlt-class">Kelas</label><select id="mlt-class" name="class_id"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($selectedClassId === (int) $class->id)>{{ $class->name }}</option>@endforeach</select></div>
        <div><label for="mlt-status">Status</label><select id="mlt-status" name="status"><option value="semua" @selected($selectedStatus==='semua')>Semua tugas</option><option value="belum" @selected($selectedStatus==='belum')>Belum dikumpulkan</option><option value="dikumpulkan" @selected($selectedStatus==='dikumpulkan')>Sudah dikumpulkan</option><option value="dinilai" @selected($selectedStatus==='dinilai')>Sudah dinilai</option></select></div>
        <button type="submit">Tampilkan</button>
    </form>
    <div class="mlt-list">
        @forelse($tasks as $task)
        @php
            $submission = $task->submissions->first();
            $closed = $task->due_at && now()->greaterThan($task->due_at);
            $graded = $submission && $submission->score !== null;
            $className = $classes->firstWhere('id', $task->class_id)?->name ?? 'Kelas';
        @endphp
        <section class="mlt-task">
            <div class="mlt-task-top">
                <div class="mlt-meta"><span class="mlt-pill">{{ $className }}</span><span class="mlt-pill">FPBL Tahap {{ $task->fpbl_stage }}</span>
                @if($graded)<span class="mlt-pill good">Sudah dinilai</span>@elseif($submission)<span class="mlt-pill good">Sudah dikumpulkan</span>@elseif($closed)<span class="mlt-pill late">Deadline lewat</span>@else<span class="mlt-pill warn">Belum dikumpulkan</span>@endif</div>
                <h2>{{ $task->title }}</h2>
                <div class="mlt-meta"><span>Deadline: <strong>{{ $task->due_at?->format('d/m/Y H:i') ?? 'Tidak dibatasi' }}</strong></span><span>•</span><span>Tahap {{ $task->fpbl_stage }} dari 4</span></div>
            </div>
            <div class="mlt-body">
                <strong>Instruksi guru</strong><div class="mlt-instructions">{{ $task->instructions }}</div>
                @if($submission)
                    <p class="mlt-muted">Terakhir dikumpulkan: {{ $submission->submitted_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    @if($submission->file_path)<p>📎 <a class="mlt-link" href="{{ route('classroom.tasks.download', $submission) }}">Unduh file yang kamu kumpulkan</a></p>@endif
                @endif
                @if($graded)
                    <div class="mlt-feedback"><strong>Nilai: {{ $submission->score }} / 100</strong><p style="white-space:pre-wrap;overflow-wrap:anywhere;margin:6px 0 0;">{{ $submission->teacher_feedback ?: 'Guru belum menambahkan catatan.' }}</p></div>
                @elseif($closed)
                    <div class="mlt-alert error" style="margin:0">Pengumpulan sudah ditutup. {{ $submission ? 'Jawaban terakhir tetap tersimpan.' : 'Belum ada pengumpulan dari akunmu.' }}</div>
                @else
                    <form action="{{ route('classroom.tasks.submit', $task) }}" method="POST" enctype="multipart/form-data" class="mlt-fields">
                        @csrf
                        <div><label for="mlt-content-{{ $task->id }}">Jawaban atau komentar</label><textarea id="mlt-content-{{ $task->id }}" name="content" rows="4" maxlength="5000" placeholder="Tuliskan jawaban atau penjelasan tugas di sini">{{ old('content', $submission?->content) }}</textarea></div>
                        <div><label for="mlt-file-{{ $task->id }}">Lampiran (opsional)</label><input id="mlt-file-{{ $task->id }}" type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"></div>
                        <p class="mlt-note">PDF, DOC, DOCX, JPG, PNG atau ZIP • Maksimal 10 MB. {{ $submission?->file_path ? 'File lama tetap tersimpan jika tidak mengunggah pengganti.' : 'Isi jawaban atau unggah file.' }}</p>
                        <div><button type="submit" class="mlt-btn">{{ $submission ? 'Perbarui pengumpulan' : 'Kumpulkan tugas' }}</button></div>
                    </form>
                @endif
            </div>
        </section>
        @empty
            <div class="mlt-empty"><h2>Belum ada tugas yang cocok</h2><p class="mlt-muted">Coba ubah filter kelas atau status. Jika belum bergabung kelas, masuk lewat menu Kelas Saya.</p><a class="mlt-btn" href="{{ route('classroom.home') }}">Buka Kelas Saya</a></div>
        @endforelse
    </div>
</div>
@endsection

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

/* MATHLEAF TUGAS - LAMPIRAN RINGKAS V2 */
.mlt-submission{margin:18px 0 12px}
.mlt-submission-head{display:flex;align-items:baseline;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-bottom:10px}
.mlt-submission-head strong{font-size:.92rem;font-weight:750;color:#244632}
.mlt-submission-time{font-size:.83rem;color:#64748b}
.mlt-attached-file{display:flex;align-items:center;gap:12px;flex-wrap:wrap;padding:12px 14px;border:1px solid #dae7de;border-radius:10px;background:#fafcfb}
.mlt-attached-icon{width:40px;height:43px;flex:0 0 40px;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:2px;background:#eaf5ee;border-radius:8px;color:#0d7545}
.mlt-attached-icon span{font-size:.63rem;font-weight:800;letter-spacing:.025em;line-height:1}
.mlt-attached-icon svg{width:19px;height:19px;display:block}
.mlt-attached-info{flex:1;min-width:150px}
.mlt-attached-name{font-size:.94rem;font-weight:750;color:#193e2b;overflow-wrap:anywhere;line-height:1.35}
.mlt-attached-subtext{font-size:.8rem;color:#64748b;margin-top:4px}
.mlt-attached-actions{display:flex;align-items:center;gap:7px;flex-wrap:wrap}
.mlt-attached-actions a{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:8px 11px;border-radius:8px;border:1px solid #cbded1;color:#0d7040;background:#fff;font-size:.82rem;font-weight:750;text-decoration:none;transition:background .15s ease}
.mlt-attached-actions a:hover{background:#eaf6ed;border-color:#93c8a6}
.mlt-attached-actions a:focus-visible,.mlt-edit-panel>summary:focus-visible{outline:2px solid #16824d;outline-offset:2px}
.mlt-attached-actions a svg{width:15px;height:15px;flex-shrink:0}
.mlt-submission-no-file{margin:0;padding:10px 12px;background:#f8faf8;border:1px solid #e2ebe4;border-radius:9px;font-size:.88rem;color:#526175}
.mlt-submission-answer{margin-top:15px}
.mlt-submission-answer strong{display:block;margin-bottom:7px;color:#244632;font-size:.9rem}
.mlt-submission-answer .mlt-answer-content{font-size:.92rem;line-height:1.6;white-space:pre-wrap;overflow-wrap:anywhere;background:#f7faf7;border:1px solid #e7eee7;border-radius:9px;padding:12px 14px;color:#213047}
.mlt-edit-panel{margin:14px 0 0;border:1px solid #dae7de;border-radius:10px;background:#fff;overflow:hidden}
.mlt-edit-panel>summary{display:flex;align-items:center;gap:7px;cursor:pointer;list-style:none;color:#0b7541;font-weight:750;font-size:.9rem;padding:11px 13px}
.mlt-edit-panel>summary::-webkit-details-marker{display:none}
.mlt-edit-panel>summary:before{content:'✎';font-size:1rem;line-height:1}
.mlt-edit-panel>summary:after{content:'⌄';margin-left:auto;font-size:1.2rem;line-height:.7;transition:transform .2s ease}
.mlt-edit-panel[open]>summary:after{transform:rotate(180deg)}
.mlt-edit-panel>summary:hover{background:#f5faf6}
.mlt-edit-panel[open]>summary{border-bottom:1px solid #e4eee6}
.mlt-edit-panel>.mlt-fields{padding:15px}
@media(max-width:640px){.mlt-attached-file{align-items:flex-start}.mlt-attached-actions{width:100%;padding-left:52px}.mlt-attached-actions a{flex:1}.mlt-submission-head{align-items:flex-start}}
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
                    <div class="mlt-submission">
                        <div class="mlt-submission-head">
                            <strong>Pengumpulanmu</strong>
                            <span class="mlt-submission-time">Dikumpulkan {{ $submission->submitted_at?->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>
                        @if($submission->file_path)
                            @php
                                $fileExt = strtolower(pathinfo($submission->file_path, PATHINFO_EXTENSION));
                                $fileLabel = $submission->original_file_name ?: ('Lampiran tersimpan' . ($fileExt ? ' (' . strtoupper($fileExt) . ')' : ''));
                                $fileCanPreview = in_array($fileExt, ['pdf', 'jpg', 'jpeg', 'png'], true);
                            @endphp
                            <div class="mlt-attached-file">
                                <div class="mlt-attached-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                    <span>{{ $fileExt ? strtoupper($fileExt) : 'FILE' }}</span>
                                </div>
                                <div class="mlt-attached-info">
                                    <div class="mlt-attached-name">{{ $fileLabel }}</div>
                                    <div class="mlt-attached-subtext">File terlampir
                                        @if($submission->file_size_bytes)
                                            · {{ number_format($submission->file_size_bytes / 1024, 1, ',', '.') }} KB
                                        @endif
                                    </div>
                                </div>
                                <div class="mlt-attached-actions">
                                    @if($fileCanPreview)
                                        <a href="{{ route('classroom.tasks.download', $submission) }}?preview=1" target="_blank" rel="noopener noreferrer" aria-label="Lihat File {{ $fileLabel }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>Lihat File</a>
                                    @endif
                                    <a href="{{ route('classroom.tasks.download', $submission) }}" aria-label="Unduh File {{ $fileLabel }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m-4-4 4 4 4-4M4 17v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg>Unduh File</a>
                                </div>
                            </div>
                        @else
                            <p class="mlt-submission-no-file">Tidak ada file lampiran. Jawaban teks sudah tersimpan.</p>
                        @endif
                        @if(filled($submission->content))
                            <div class="mlt-submission-answer"><strong>Jawaban yang dikumpulkan</strong><div class="mlt-answer-content">{{ $submission->content }}</div></div>
                        @endif
                    </div>
                @endif
                @if($graded)
                    <div class="mlt-feedback"><strong>Nilai: {{ $submission->score }} / 100</strong><p style="white-space:pre-wrap;overflow-wrap:anywhere;margin:6px 0 0;">{{ $submission->teacher_feedback ?: 'Guru belum menambahkan catatan.' }}</p></div>
                @elseif($closed)
                    <div class="mlt-alert error" style="margin:0">Pengumpulan sudah ditutup. {{ $submission ? 'Jawaban terakhir tetap tersimpan.' : 'Belum ada pengumpulan dari akunmu.' }}</div>
                @else
                    @if($submission)
                        <details class="mlt-edit-panel" @if($errors->any()) open @endif>
                            <summary>Ubah jawaban / Ganti file</summary>
                    @endif
                    <form action="{{ route('classroom.tasks.submit', $task) }}" method="POST" enctype="multipart/form-data" class="mlt-fields">
                        @csrf
                        <div><label for="mlt-content-{{ $task->id }}">Jawaban atau komentar</label><textarea id="mlt-content-{{ $task->id }}" name="content" rows="4" maxlength="5000" placeholder="Tuliskan jawaban atau penjelasan tugas di sini">{{ old('content', $submission?->content) }}</textarea></div>
                        <div><label for="mlt-file-{{ $task->id }}">{{ $submission?->file_path ? 'Ganti file (opsional)' : 'Lampiran (opsional)' }}</label><input id="mlt-file-{{ $task->id }}" type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"></div>
                        <p class="mlt-note">PDF, DOC, DOCX, JPG, PNG atau ZIP • Maksimal 10 MB. {{ $submission?->file_path ? 'File lama tetap tersimpan jika tidak memilih pengganti.' : 'Isi jawaban atau unggah file.' }}</p>
                        <div><button type="submit" class="mlt-btn">{{ $submission ? 'Simpan perubahan' : 'Kumpulkan tugas' }}</button></div>
                    </form>
                    @if($submission)
                        </details>
                    @endif
                @endif
            </div>
        </section>
        @empty
            <div class="mlt-empty"><h2>Belum ada tugas yang cocok</h2><p class="mlt-muted">Coba ubah filter kelas atau status. Jika belum bergabung kelas, masuk lewat menu Kelas Saya.</p><a class="mlt-btn" href="{{ route('classroom.home') }}">Buka Kelas Saya</a></div>
        @endforelse
    </div>
</div>
@endsection

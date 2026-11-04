@extends('layouts.app')

@section('title', 'Manajemen Kuis & Ujian | MathLeaf')
@section('header_title', 'Manajemen Kuis & Ujian')

@section('content')
    <style>
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-border: #bbf0cb;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--ml-green);
            color: #ffffff;
            border-color: var(--ml-green);
        }

        .btn-primary:hover {
            background-color: var(--ml-green-hover);
            box-shadow: 0 4px 6px rgba(8, 101, 52, 0.2);
        }

        .btn-outline {
            background-color: #ffffff;
            border-color: #cbd5e1;
            color: #334155;
        }

        .btn-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-danger-outline {
            background-color: #ffffff;
            border-color: #fecaca;
            color: #dc2626;
        }

        .btn-danger-outline:hover {
            background-color: #fef2f2;
            border-color: #f87171;
        }

        .quiz-top-links { display:flex; align-items:center; gap:.7rem; flex-wrap:wrap; margin:0 0 1.5rem; }
        .quiz-top-links a { text-decoration:none; border:1px solid #bbf0cb; border-radius:8px; padding:.65rem .95rem; color:#075d30; font-weight:700; }
        .quiz-top-links a.selected { background:#086534; color:white; }
        .quiz-notice { border-radius:10px; padding:.9rem 1.1rem; margin-bottom:1rem; background:#edf8f0; border:1px solid #bbf0cb; color:#086534; }
        .quiz-confirm-title { font-weight:700; color:#0f172a; overflow-wrap:anywhere; }
        .quiz-archive-hint { color:#475569; line-height:1.6; }
        .quiz-card .quiz-actions { flex-wrap:wrap; }
        /* MATHLEAF UPGRADE 6 — Jadwal kuis */
        .ml6-schedule { margin: 0 1.5rem 1rem; padding: 14px 16px; background: #f3fbf6; border: 1px solid #ccebd7; border-radius: 12px; }
        .ml6-schedule summary { cursor: pointer; color: #086534; font-weight: 800; }
        .ml6-schedule form { display:flex; flex-wrap:wrap; gap: 10px; margin-top:14px; align-items:end; }
        .ml6-schedule label { font-weight:700; color:#14532d; font-size:.85rem; display:block; margin-bottom:5px; }
        .ml6-schedule input { border:1px solid #bedfcb; border-radius:9px; padding:10px 12px; color:#1b4330; min-width:190px; max-width:100%; }
        .ml6-schedule button { border:0; border-radius:10px; padding:11px 16px; background:#086534; color:white; font-weight:700; cursor:pointer; }
        .ml6-status { color:#276749; font-size:.83rem; margin-top:7px; display:block; font-weight:650; }
        @media(max-width:640px) { .ml6-schedule form { display:block; } .ml6-schedule form > div { margin-bottom:12px; } .ml6-schedule input { width:100%; } }
        /* Kuis List */
        .quiz-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .quiz-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            overflow: hidden;
        }

        .quiz-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            background: #ffffff;
        }

        .quiz-info-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* Box Kode Kuis Besar */
        .quiz-code-box {
            background-color: var(--ml-green-light);
            border: 1px solid var(--ml-green-border);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            display: flex;
            flex-direction: column;
            min-width: 150px;
        }

        .code-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--ml-green);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.2rem;
        }

        .code-value-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .code-text {
            font-family: monospace;
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.1em;
        }

        .btn-copy {
            background: transparent;
            border: none;
            color: var(--ml-green);
            cursor: pointer;
            padding: 0.2rem;
            display: flex;
            border-radius: 4px;
            transition: background 0.15s;
        }

        .btn-copy:hover {
            background: rgba(8, 101, 52, 0.1);
        }

        .quiz-meta-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .quiz-badges {
            display: flex;
            gap: 0.5rem;
        }

        .badge {
            background: #f1f5f9;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            color: #334155;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-status {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }

        .quiz-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .quiz-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chevron-icon {
            transition: transform 0.2s ease;
        }

        /* Dropdown Detail Kuis (Attempts) */
        .quiz-detail {
            border-top: 1px solid #f1f5f9;
            background: #f8fafc;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
            padding: 0 1.5rem;
        }

        .quiz-detail.open {
            max-height: 4000px;
            padding: 1.5rem;
        }

        /* Tabel Riwayat Pengerjaan (Attempts) */
        .attempt-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .attempt-table th,
        .attempt-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .attempt-table th {
            background: #f1f5f9;
            font-weight: 600;
            color: #334155;
            font-size: 0.95rem;
        }

        .attempt-table tr:last-child td {
            border-bottom: none;
        }

        .student-name {
            font-weight: 600;
            color: #0f172a;
        }

        .score-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--ml-green);
        }

        .link-jawaban {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--ml-green);
            text-decoration: none;
            font-weight: 600;
            background: var(--ml-green-light);
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            border: 1px solid var(--ml-green-border);
        }

        .link-jawaban:hover {
            background: #dcfce7;
        }

        /* Modal Buat Kuis */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .modal-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(15px);
            transition: transform 0.2s ease;
        }

        .modal-backdrop.active .modal-box {
            transform: translateY(0);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #0f172a;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
            color: #0f172a;
        }

        .form-control:focus {
            border-color: var(--ml-green);
            outline: none;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: #f8fafc;
            border-radius: 0 0 8px 8px;
        }
    /* MATHLEAF QUIZ UI V2 */
.quiz-card {
    border: 1px solid #dcebe2;
    border-radius: 18px;
    box-shadow: 0 6px 20px rgba(6,80,43,.06);
    transition: box-shadow .2s ease;
}

.quiz-card:hover {
    box-shadow: 0 12px 28px rgba(6,80,43,.10);
}

.quiz-card .quiz-header {
    display: grid;
    grid-template-columns: minmax(0,1fr) 320px;
    gap: 20px;
    align-items: center;
    padding: 22px 24px;
}

.quiz-card .quiz-info-left {
    min-width: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
}

.quiz-card .quiz-meta-group {
    flex: 1;
    min-width: 210px;
}

.quiz-card .quiz-badges {
    flex-wrap: wrap;
    gap: 6px;
}

.quiz-card .badge {
    border-radius: 9px;
    padding: 5px 9px;
}

.quiz-card .quiz-actions {
    display: grid;
    grid-template-columns: repeat(2,minmax(0,1fr));
    gap: 10px;
    width: 100%;
    align-items: stretch;
}

.quiz-card .quiz-actions > form {
    margin: 0;
    min-width: 0;
}

.quiz-card .quiz-actions > form[hidden] {
    display: none !important;
}

.quiz-card .quiz-actions > form:not([hidden]) {
    grid-column: 1 / -1;
}

.quiz-card .quiz-actions .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    min-height: 46px;
    padding: 10px 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    transition: all .2s ease;
}

.quiz-card .quiz-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(0,0,0,.08);
}

.quiz-card .quiz-actions .btn-outline {
    background: #eff8f2;
    color: #086534;
    border: 1px solid #b9dec7;
}

.quiz-card .quiz-actions .btn-outline:hover {
    background: #dff3e7;
}

.quiz-card .quiz-actions .btn-danger-outline {
    background: #fff2f0;
    color: #bc3833;
    border: 1px solid #f2c0ba;
}

.quiz-card .quiz-actions .btn-danger-outline:hover {
    background: #ffe3df;
}

.quiz-card .quiz-actions > .btn-primary {
    grid-column: 1 / -1;
    background: linear-gradient(135deg,#0fa66a,#087043);
    color: #fff;
    border: 1px solid transparent;
}

.quiz-card .quiz-actions > .btn-primary:hover {
    background: linear-gradient(135deg,#12b775,#075b39);
}

.quiz-card .quiz-actions .btn:focus-visible {
    outline: 3px solid #71d7a0;
    outline-offset: 2px;
}

@media(max-width:1180px) {
    .quiz-card .quiz-header {
        grid-template-columns: minmax(0,1fr);
    }

    .quiz-card .quiz-actions {
        max-width: 420px;
    }
}

@media(max-width:640px) {
    .quiz-card .quiz-header {
        padding: 16px;
    }

    .quiz-card .quiz-meta-group {
        min-width: 0;
    }

    .quiz-card .quiz-actions {
        max-width: none;
    }

    .quiz-card .quiz-actions .btn {
        font-size: 12px;
        padding: 8px;
    }
}
</style>

    <div class="page-header">
        <h1>Manajemen Kuis & Ujian</h1>
        <!-- Tombol ini memicu modal untuk membuat kuis baru sebelum diarahkan ke halaman editor soal -->
        <button type="button" class="btn btn-primary" onclick="openModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Buat Kuis Baru
        </button>
    </div>

    @if(session('success'))
        <div class="quiz-notice" role="status">{{ session('success') }}</div>
    @endif
    <nav class="quiz-top-links" aria-label="Filter kuis">
        <a href="{{ route('teacher.quizzes') }}" class="{{ empty($showArchive) ? 'selected' : '' }}">Kuis Aktif & Draft</a>
        <a href="{{ route('teacher.quizzes', ['arsip' => 1]) }}" class="{{ !empty($showArchive) ? 'selected' : '' }}">Arsip Kuis</a>
    </nav>

    <!-- Modal konfirmasi hapus/arsip, memakai tampilan MathLeaf -->
    <div id="modal-hapus-kuis" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="judul-modal-hapus" aria-hidden="true">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="judul-modal-hapus">Hapus Kuis</h3>
                <button type="button" class="modal-close" onclick="tutupModalHapusKuis()" aria-label="Tutup">✕</button>
            </div>
            <div class="modal-body">
                <div class="quiz-confirm-title" id="nama-kuis-hapus"></div>
                <p class="quiz-archive-hint" id="pesan-kuis-hapus"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="tutupModalHapusKuis()">Batal</button>
                <button type="button" class="btn btn-danger-outline" id="konfirmasi-hapus-kuis" onclick="konfirmasiHapusKuis()">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Modal Info Awal Kuis -->
    <div id="modal-kuis" class="modal-backdrop">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Pengaturan Awal Kuis</h3>
                <button type="button" class="modal-close" onclick="closeModal()">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('teacher.quizzes.create') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul Kuis</label>
                        <input type="text" name="title" class="form-control"
                            placeholder="Contoh: Ujian Tengah Semester" required>
                    </div>
                    <div class="form-group">
                        <label>Pilih Kelas</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @forelse ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @empty
                                <option value="" disabled>Belum ada kelas. Buat kelas terlebih dahulu.</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Penilaian</label>
                        <select name="type" class="form-control" required>
                            <option value="latihan">Latihan Biasa</option>
                            <option value="kuis">Kuis Terjadwal</option>
                            <option value="ujian">Ujian (Mode Fullscreen)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Batas Waktu (Menit)</label>
                        <input type="number" name="time_limit_minutes" class="form-control" placeholder="Contoh: 60"
                            required>
                    </div>
                        <div class="form-group">
                            <label>Waktu Mulai (WIB, Opsional)</label>
                            <input type="datetime-local" name="starts_at" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Deadline Kuis (WIB, Opsional)</label>
                            <input type="datetime-local" name="due_at" class="form-control">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Lanjut Buat Soal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Kuis -->
    <div class="quiz-list">
        @forelse($assessments ?? [] as $quiz)
            <div class="quiz-card">
                <!-- Header Kuis -->
                <div class="quiz-header">
                    <div class="quiz-info-left">
                        <!-- Kode Kuis untuk Siswa Join -->
                        <div class="quiz-code-box">
                            <span class="code-label">Kelas / Kode Gabung</span>
                            <div class="code-value-group">
                                <!-- Asumsi kamu sudah menambahkan kolom quiz_code di database -->
                                <span class="code-text">{{ optional($classes->firstWhere('id', $quiz->class_id))->join_code ?? '—' }}</span>
                                <button type="button" class="btn-copy"
                                    onclick="copyCode('{{ optional($classes->firstWhere('id', $quiz->class_id))->join_code ?? '' }}', this)" title="Salin Kode">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2">
                                        </rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="quiz-meta-group">
                            <div class="quiz-badges">
                                <span
                                    class="badge">{{ optional($classes->firstWhere('id', $quiz->class_id))->name ?? 'Semua Kelas' }}</span>
                                <span class="badge" style="text-transform: capitalize;">{{ $quiz->type }}</span>
                                <span class="badge">{{ $quiz->time_limit_minutes }} Menit</span>
                                <span class="badge">{{ $quiz->questions_count }} soal</span>
                                <span class="badge">KKM {{ $quiz->passing_score ?? 70 }}</span>
                                <span class="badge">{{ $quiz->max_attempts }}x kesempatan</span>
                                <span class="badge badge-status">{{ $quiz->archived_at ? 'Diarsipkan' : ($quiz->is_published ? ($quiz->starts_at && $quiz->starts_at->isFuture() ? 'Terjadwal' : 'Aktif') : 'Draft') }}</span>
                            </div>
                            <h2 class="quiz-title">{{ $quiz->title }}</h2>
                            <div class="ml6-status">
                                @if($quiz->starts_at) Mulai: {{ $quiz->starts_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB @endif
                                @if($quiz->due_at) · Deadline: {{ $quiz->due_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB @else · Tanpa deadline @endif
                            </div>
                        </div>
                    </div>

                    <div class="quiz-actions">
                        @if(!$quiz->archived_at)
                        <!-- Edit hanya tersedia bagi kuis yang belum diarsipkan. -->
                        <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-outline"
                            title="Edit Soal & Jawaban">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Edit Kuis
                        </a>
                        <form id="form-hapus-kuis-{{ $quiz->id }}" method="POST" action="{{ route('teacher.quizzes.destroy', $quiz) }}" hidden>
                            @csrf
                            @method('DELETE')
                        </form>
                        <button type="button" class="btn btn-danger-outline" onclick="bukaModalHapusKuis(this)"
                            data-form-id="form-hapus-kuis-{{ $quiz->id }}"
                            data-title="{{ $quiz->title }}"
                            data-attempts="{{ $quiz->attempts_count }}">
                            {{ $quiz->attempts_count > 0 ? 'Arsipkan' : 'Hapus Kuis' }}
                        </button>
                        @else
                        <form method="POST" action="{{ route('teacher.quizzes.restore', $quiz) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline">Pulihkan</button>
                        </form>
                        @endif

                        <!-- Tombol Lihat Detail (Slide down) -->
                        <button type="button" class="btn btn-primary" onclick="toggleQuiz({{ $quiz->id }})">
                            Lihat Hasil
                            <svg id="icon-{{ $quiz->id }}" class="chevron-icon" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>

                <details class="ml6-schedule">
                    <summary>{{ $quiz->archived_at ? 'Jadwalkan Ulang & Buka Kuis' : 'Atur / Perpanjang Jadwal Kuis' }}</summary>
                    <form method="POST" action="{{ route('teacher.quizzes.schedule', $quiz) }}">
                        @csrf
                        @method('PATCH')
                        <div><label>Mulai (WIB, opsional)</label><input type="datetime-local" name="starts_at" value="{{ $quiz->starts_at?->timezone('Asia/Jakarta')->format('Y-m-d\TH:i') }}"></div>
                        <div><label>Deadline Baru (WIB)</label><input type="datetime-local" name="due_at" required value="{{ $quiz->due_at && $quiz->due_at->isFuture() ? $quiz->due_at->timezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '' }}"></div>
                        <button type="submit">Simpan Jadwal</button>
                    </form>
                </details>

                <!-- Detail Pengerjaan Siswa (Slide Down) -->
                <div id="detail-{{ $quiz->id }}" class="quiz-detail">
                    @if ($quiz->type === 'ujian')
                        <a href="{{ route('teacher.quizzes.incidents', $quiz) }}" class="btn btn-outline" style="margin-bottom:14px;text-decoration:none;">
                            📋 Catatan Pengawasan Ujian
                        </a>
                    @endif
                    <h4 style="margin: 0 0 1rem 0; color: #0f172a; font-size: 1.1rem;">Daftar Pengerjaan Siswa</h4>

                    @if (isset($quiz->attempts) && $quiz->attempts->count() > 0)
                        <table class="attempt-table">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Percobaan Ke-</th>
                                    <th>Waktu Selesai</th>
                                    <th>Nilai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quiz->attempts as $attempt)
                                    <tr>
                                        <td>
                                            <div class="student-name">{{ $attempt->user->name ?? 'Siswa' }}</div>
                                        </td>
                                        <td>Percobaan {{ $attempt->attempt_number }}</td>
                                        <td>{{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d/m/Y H:i') : 'Sedang Mengerjakan' }}
                                        </td>
                                        <td>
                                            <span
                                                class="score-text">{{ $attempt->score !== null ? $attempt->score : '-' }}</span>
                                            / 100
                                        </td>
                                        <td>
                                            <!-- Link menuju halaman Lembar Jawaban Khusus -->
                                            @if ($attempt->status === 'completed' || $attempt->score !== null)
                                                <a href="{{ route('teacher.quizzes.attempt', $attempt) }}"
                                                    class="link-jawaban">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                        </path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8"
                                                            y2="13"></line>
                                                        <line x1="16" y1="17" x2="8"
                                                            y2="17"></line>
                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                    </svg>
                                                    Lihat Lembar Jawaban
                                                </a>
                                            @else
                                                <span style="color: #64748b; font-size: 0.9rem;">Belum Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div
                            style="background: #ffffff; border: 1px dashed #cbd5e1; padding: 2rem; text-align: center; border-radius: 6px; color: #64748b;">
                            Belum ada siswa yang mengerjakan kuis ini.
                        </div>
                    @endif
                    <br>
                </div>
            </div>
        @empty
            <div
                style="background: #ffffff; border: 1px dashed var(--ml-green-border); border-radius: 8px; padding: 3rem; text-align: center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <h3 style="margin: 0 0 0.5rem 0; color: #0f172a; font-size: 1.2rem;">Belum Ada Kuis</h3>
                <p style="margin: 0; color: #64748b; font-size: 1rem;">Buat kuis pertamamu dan bagikan kodenya kepada
                    siswa.</p>
            </div>
        @endforelse
    </div>

    <script>
        let formHapusKuisId = null;
        const modalHapusKuis = document.getElementById('modal-hapus-kuis');
        function bukaModalHapusKuis(button) {
            formHapusKuisId = button.dataset.formId;
            const adaJawaban = Number(button.dataset.attempts) > 0;
            document.getElementById('judul-modal-hapus').textContent = adaJawaban ? 'Arsipkan Kuis?' : 'Hapus Kuis?';
            document.getElementById('nama-kuis-hapus').textContent = button.dataset.title;
            document.getElementById('pesan-kuis-hapus').textContent = adaJawaban
                ? 'Kuis ini sudah memiliki pengerjaan siswa. Kuis akan diarsipkan dan tidak bisa dikerjakan lagi. Nilai serta lembar jawaban siswa tetap tersimpan.'
                : 'Kuis ini belum dikerjakan siswa. Kuis dan soal-soalnya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.';
            document.getElementById('konfirmasi-hapus-kuis').textContent = adaJawaban ? 'Arsipkan Kuis' : 'Hapus Permanen';
            modalHapusKuis.classList.add('active');
            modalHapusKuis.setAttribute('aria-hidden', 'false');
        }
        function tutupModalHapusKuis() {
            formHapusKuisId = null;
            modalHapusKuis.classList.remove('active');
            modalHapusKuis.setAttribute('aria-hidden', 'true');
        }
        function konfirmasiHapusKuis() {
            const form = formHapusKuisId && document.getElementById(formHapusKuisId);
            if (form) form.requestSubmit();
        }
        modalHapusKuis.addEventListener('click', (e) => {
            if (e.target === modalHapusKuis) tutupModalHapusKuis();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') tutupModalHapusKuis();
        });

        // Salin kode kelas dari kartu kuis.
        function copyCode(code, btn) {
            if (!code) return;
            navigator.clipboard.writeText(code).then(() => {
                const old = btn.innerHTML;
                btn.textContent = '✓';
                setTimeout(() => { btn.innerHTML = old; }, 1600);
            }).catch(() => { alert('Gagal menyalin kode. Silakan salin manual.'); });
        }

        // Modal
        const modalKuis = document.getElementById('modal-kuis');

        function openModal() {
            modalKuis.classList.add('active');
        }

        function closeModal() {
            modalKuis.classList.remove('active');
        }
        modalKuis.addEventListener('click', (e) => {
            if (e.target === modalKuis) closeModal();
        });

        // Accordion Lihat Hasil
        function toggleQuiz(id) {
            const detailDiv = document.getElementById('detail-' + id);
            const icon = document.getElementById('icon-' + id);

            if (detailDiv.classList.contains('open')) {
                detailDiv.classList.remove('open');
                icon.style.transform = 'rotate(0deg)';
            } else {
                detailDiv.classList.add('open');
                icon.style.transform = 'rotate(180deg)';
            }
        }
    </script>
@endsection

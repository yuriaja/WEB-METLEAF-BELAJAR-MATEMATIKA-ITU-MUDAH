<!doctype html>
<html lang="id">
  <head>
<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Dashboard Guru | MathLeaf</title>
    <meta
      name="description"
      content="MathLeaf — Grow Your Math Skills. Pembelajaran Matematika kelas VII SMP."
    />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="stylesheet" href="{{ asset('css/mathleaf.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/mathleaf-teacher.css') }}" />
    <script defer src="{{ asset('js/mathleaf.js') }}"></script>
    <script defer src="{{ asset('js/mathleaf-teacher.js') }}"></script>
  </head>
  <body data-page="guru">
    <div class="app-layout" id="app-layout">
      <aside class="app-sidebar">
        <a href="{{ url('/') }}" class="logo logo-light"
          ><span class="logo-mark"
            ><svg
              aria-hidden="true"
              width="23"
              height="23"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
              <path d="M4 21c0-6 5-11 11-14" /></svg></span
          ><span
            >Math<span class="logo-leaf">Leaf</span
            ><small>GROW YOUR MATH SKILLS</small></span
          ></a
        >
        <div class="sidebar-class">
          <svg
            aria-hidden="true"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            style="flex-shrink: 0"
          >
            <path d="M7 20h10" />
            <path d="M10 20c5.5-2.5.8-8.3 5-11" />
            <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
            <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" /></svg
          ><span>Ruang Guru<small>{{ auth()->user()->name }} · Pengajar</small></span>
        </div>
        <span class="sidebar-caption">PENGELOLAAN</span>
        <nav>
          <a href="#guru-kelas" class=""><span>🏫 Kelas yang Saya Ajar</span></a>
          <a href="#guru-soal" class=""><span>📝 Buat Soal / Kuis</span></a>
          <a href="{{ url('/guru') }}" class="selected"
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="m2 10 10-5 10 5-10 5-10-5Z" />
              <path d="M6 12v5c3 2 9 2 12 0v-5M22 10v7" /></svg
            ><span>Dashboard Guru</span></a
          ><a href="#guru-materi" class=""
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M12 7v14" />
              <path
                d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z"
              /></svg
            ><span>Manajemen Materi</span></a
          ><a href="{{ url('/forum') }}" class=""
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path
                d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"
              /></svg
            ><span>Forum Diskusi</span></a
          ><a href="{{ url('/tugas') }}" class=""
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M12 7v14" />
              <path
                d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z"
              /></svg
            ><span>Pengumpulan Tugas</span></a
          ><a href="#guru-soal" class=""
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <circle cx="12" cy="8" r="6" />
              <path d="m8 13-2 8 6-3 6 3-2-8" /></svg
            ><span>Analitik Nilai</span></a
          >
        </nav>
        <div class="sidebar-bottom">
          <div class="sidebar-motivation">
            <svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M7 20h10" />
              <path d="M10 20c5.5-2.5.8-8.3 5-11" />
              <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
              <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" />
            </svg>
            <p>
              Setiap langkah kecil membawa perubahan besar. Terus bertumbuh!
            </p>
          </div>
          <a href="{{ url('/') }}"
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M5 12h14" />
              <path d="m12 5 7 7-7 7" /></svg
            ><span>Ke Beranda</span></a
          ><a href="{{ url('/masuk') }}" id="logout-demo"
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M18 6 6 18M6 6l12 12" /></svg
            ><span>Keluar (demo)</span></a
          >
        </div>
      </aside>
      <div class="app-main">
        <header class="app-header">
          <div style="display: flex; align-items: center; gap: 12px">
            <button
              type="button"
              class="btn btn-ghost"
              id="collapse-sidebar"
              aria-label="Ciutkan sidebar"
            >
              ☰</button
            ><strong>Dashboard Guru</strong>
          </div>
          <div style="display: flex; gap: 16px; align-items: center">
            <button
              type="button"
              class="btn btn-ghost"
              id="notification-button"
              aria-label="Notifikasi"
            >
              <svg
                aria-hidden="true"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                style="flex-shrink: 0"
              >
                <path
                  d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z"
                />
                <path
                  d="m5 17 .8 2.2L8 20l-2.2.8L5 23l-.8-2.2L2 20l2.2-.8L5 17Z"
                />
                <path
                  d="m19 2 .7 1.3L21 4l-1.3.7L19 6l-.7-1.3L17 4l1.3-.7L19 2Z"
                />
              </svg></button
            ><span class="header-profile"
              ><span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span
              ><span>{{ auth()->user()->name }}</span></span
            >
          </div>
          <div class="notification-panel" id="notification-panel" hidden>
            <strong>Notifikasi MathLeaf</strong>
            <p>Terus belajar, satu langkah kecil setiap hari 🌱</p>
          </div>
        </header>
        <main class="app-content teacher-hub">
  <div class="page-heading teacher-page-heading">
    <div>
      <span class="eyebrow">PENGELOLAAN PEMBELAJARAN · KELAS VII</span>
      <h1>Halo, {{ auth()->user()->name }}! 👋</h1>
      <p>Buat kelas, bagikan kode kepada siswa, lalu unggah materi dan soal. Semuanya tersimpan di database MathLeaf.</p>
    </div>
    <div class="teacher-heading-actions">
      <a class="btn btn-outline" href="{{ route('classroom.home') }}">Lihat halaman kelas</a>
      <a class="btn btn-primary" href="#guru-kelas">+ Buat kelas</a>
    </div>
  </div>

  @if(session('success'))
    <div class="teacher-flash teacher-flash-success" role="status">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="teacher-flash teacher-flash-error" role="alert">
      <strong>Ada data yang perlu diperbaiki:</strong>
      <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="stats-grid teacher-stats">
    <article class="stat-card"><span class="teacher-stat-symbol">🏫</span><div><span class="stat-label">Kelas Saya</span><strong>{{ $classes->count() }}</strong><small>Kelas yang dibuat</small></div></article>
    <article class="stat-card"><span class="teacher-stat-symbol">👥</span><div><span class="stat-label">Jumlah Siswa</span><strong>{{ $classes->sum('students_count') }}</strong><small>Anggota semua kelas</small></div></article>
    <article class="stat-card"><span class="teacher-stat-symbol">📚</span><div><span class="stat-label">Materi</span><strong>{{ $materials->count() }}</strong><small>Diunggah oleh guru</small></div></article>
    <article class="stat-card"><span class="teacher-stat-symbol">📝</span><div><span class="stat-label">Kuis / Soal</span><strong>{{ $assessments->count() }}</strong><small>Untuk kelas yang diajar</small></div></article>
  </div>

  <div class="teacher-quick-actions" aria-label="Fitur utama guru">
    <a href="#guru-kelas"><span class="teacher-action-icon">🏫</span><span><strong>Buat Kelas</strong><small>Buat kelas VII A, VII B, dan kode undangan</small></span><span aria-hidden="true">→</span></a>
    <a href="#guru-materi"><span class="teacher-action-icon">📤</span><span><strong>Upload Materi</strong><small>PDF, PPT, foto, video, dan link</small></span><span aria-hidden="true">→</span></a>
    <a href="#guru-soal"><span class="teacher-action-icon">✅</span><span><strong>Buat Soal</strong><small>Kuis pilihan ganda beserta kunci</small></span><span aria-hidden="true">→</span></a>
  </div>

  <div class="teacher-two-cols" id="guru-kelas">
    <section class="panel teacher-section">
      <div class="teacher-section-heading"><div><span class="eyebrow">LANGKAH 1</span><h2>Buat Kelas Baru</h2><p>Setiap kelas mendapat kode unik yang bisa dibagikan kepada siswa.</p></div><span class="teacher-heading-icon">🏫</span></div>
      <form class="teacher-form" action="{{ route('classroom.create') }}" method="POST">
        @csrf
        <label for="class-name">Nama kelas</label>
        <input id="class-name" name="name" type="text" maxlength="100" placeholder="Contoh: VII A atau 7B" value="{{ old('name') }}" required>
        <label for="school-year">Tahun ajaran (opsional)</label>
        <input id="school-year" name="school_year" type="text" maxlength="15" placeholder="2026/2027" value="{{ old('school_year') }}">
        <button type="submit" class="btn btn-primary">+ Buat kelas & kode</button>
      </form>
    </section>
    <section class="panel teacher-section">
      <div class="teacher-section-heading"><div><span class="eyebrow">DAFTAR KELAS</span><h2>Kelas yang Saya Ajar</h2><p>Bagikan kode kelas kepada siswa setelah mereka login.</p></div></div>
      <div class="teacher-classes">
        @forelse($classes as $class)
          <article class="teacher-class-card">
            <div class="teacher-class-top"><div><strong>{{ $class->name }}</strong><small>{{ $class->school_year ?: 'Tahun ajaran belum diisi' }}</small></div><span class="badge">{{ $class->students_count }} siswa</span></div>
            <div class="teacher-code-line"><div><small>KODE KELAS</small><code>{{ $class->join_code }}</code></div><button type="button" class="btn btn-outline teacher-copy" data-code="{{ $class->join_code }}">Salin kode</button></div>
          </article>
        @empty
          <div class="teacher-empty">Belum ada kelas. Buat kelas pertamamu menggunakan formulir di sebelah kiri.</div>
        @endforelse
      </div>
    </section>
  </div>

  <section class="panel teacher-section teacher-wide" id="guru-materi">
    <div class="teacher-section-heading"><div><span class="eyebrow">LANGKAH 2</span><h2>Upload Materi Pembelajaran</h2><p>Semua materi yang dipublikasikan akan terlihat oleh siswa anggota kelas yang dipilih.</p></div><span class="teacher-heading-icon">📤</span></div>
    @if($classes->isEmpty())
      <div class="teacher-empty">Buat kelas dahulu agar kamu bisa mengunggah materi.</div>
    @else
      <form class="teacher-form" method="POST" action="{{ route('classroom.upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="teacher-form-grid">
          <div><label for="material-class">Tampilkan untuk kelas</label><select id="material-class" name="class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}</option>@endforeach</select></div>
          <div><label for="material-type">Jenis materi</label><select id="material-type" name="type" required><option value="modul">Modul / PDF</option><option value="ppt">PowerPoint</option><option value="foto">Foto / Gambar</option><option value="video">Video</option><option value="link">Tautan URL</option></select></div>
        </div>
        <label for="material-title">Judul materi</label><input id="material-title" name="title" maxlength="255" value="{{ old('title') }}" placeholder="Contoh: Operasi Bilangan Bulat" required>
        <label for="material-desc">Deskripsi materi</label><textarea id="material-desc" name="description" rows="3" placeholder="Ringkasan singkat atau petunjuk belajar">{{ old('description') }}</textarea>
        <div class="teacher-form-grid">
          <div><label for="material-file">Unggah file (PDF, PPT/PPTX, JPG/PNG, MP4/WEBM)</label><input id="material-file" name="file" type="file" accept=".pdf,.ppt,.pptx,.jpg,.jpeg,.png,.webp,.mp4,.webm"><small>Maksimal 50 MB. Unggah file atau isi tautan di samping.</small></div>
          <div><label for="material-url">Atau tempel URL video / materi</label><input id="material-url" name="resource_url" type="url" value="{{ old('resource_url') }}" placeholder="https://..."><small>Contoh: YouTube atau tautan bahan belajar.</small></div>
        </div>
        <label class="teacher-check"><input type="checkbox" name="is_published" value="1" checked> Langsung tampilkan kepada siswa</label>
        <button type="submit" class="btn btn-primary">📤 Simpan & upload materi</button>
      </form>
    @endif
  </section>

  <section class="panel teacher-section teacher-wide" id="guru-soal">
    <div class="teacher-section-heading"><div><span class="eyebrow">LANGKAH 3</span><h2>Buat Soal / Kuis Kelas</h2><p>Buat pilihan ganda beserta jawaban benar. Soal langsung tersedia untuk siswa di kelas tersebut.</p></div><span class="teacher-heading-icon">📝</span></div>
    @if($classes->isEmpty())
      <div class="teacher-empty">Buat kelas dahulu agar bisa memberikan soal kepada siswa.</div>
    @else
      <form class="teacher-form" method="POST" action="{{ route('classroom.quiz') }}">
        @csrf
        <div class="teacher-form-grid"><div><label for="quiz-class">Kelas tujuan</label><select id="quiz-class" name="class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select></div><div><label for="quiz-type">Jenis penilaian</label><select id="quiz-type" name="type"><option value="latihan">Latihan</option><option value="kuis">Kuis</option><option value="sumatif">Evaluasi Sumatif</option></select></div></div>
        <label for="quiz-title">Judul soal / kuis</label><input id="quiz-title" name="title" placeholder="Contoh: Kuis Penjumlahan Bilangan Bulat" required>
        <div id="teacher-questions"><fieldset class="teacher-question-card"><legend>Soal 1</legend><label>Pertanyaan</label><textarea name="questions[0][text]" rows="2" required placeholder="Tuliskan pertanyaan..."></textarea><div class="teacher-form-grid">@foreach(['A','B','C','D'] as $i=>$letter)<div><label>Pilihan {{ $letter }}</label><input name="questions[0][options][{{ $i }}]" required placeholder="Jawaban {{ $letter }}"></div>@endforeach</div><label>Kunci jawaban benar</label><select name="questions[0][answer]"><option value="0">A</option><option value="1">B</option><option value="2">C</option><option value="3">D</option></select><label>Pembahasan (opsional)</label><textarea name="questions[0][explanation]" rows="2" placeholder="Alasan jawaban benar"></textarea></fieldset></div>
        <div class="teacher-form-actions"><button id="teacher-add-question" type="button" class="btn btn-outline">+ Tambah soal</button><button type="submit" class="btn btn-primary">Terbitkan kuis untuk siswa</button></div>
      </form>
    @endif
  </section>

  <div class="teacher-two-cols teacher-last">
    <section class="panel teacher-section"><div class="teacher-section-heading"><div><h2>Materi yang Telah Diunggah</h2><p>Daftar materi milikmu berdasarkan kelas.</p></div></div>
      @forelse($materials as $material)
        <div class="teacher-item"><div><strong>{{ $material->title }}</strong><small>{{ $material->type }} · {{ optional($classes->firstWhere('id',$material->class_id))->name ?? 'Tanpa kelas' }}</small><span class="badge">{{ $material->is_published ? 'Terbit' : 'Draf' }}</span></div><div class="teacher-item-actions">@if($material->file_path)<a href="{{ route('classroom.file',$material) }}">Unduh</a>@endif @if($material->resource_url)<a href="{{ $material->resource_url }}" target="_blank" rel="noopener noreferrer">Buka link</a>@endif @if($material->class_id)<form method="POST" action="{{ route('classroom.publish',$material) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-outline">{{ $material->is_published ? 'Jadikan draf' : 'Publikasikan' }}</button></form>@endif</div></div>
      @empty<div class="teacher-empty">Belum ada materi. Upload materi pertamamu menggunakan formulir di atas.</div>@endforelse
    </section>
    <section class="panel teacher-section"><div class="teacher-section-heading"><div><h2>Kuis yang Sudah Dibuat</h2><p>Kuis tersedia untuk anggota kelas yang dituju.</p></div></div>
      @forelse($assessments as $assessment)
        <div class="teacher-item"><div><strong>{{ $assessment->title }}</strong><small>{{ optional($classes->firstWhere('id',$assessment->class_id))->name ?? 'Tanpa kelas' }} · {{ $assessment->questions_count }} soal</small><span class="badge">{{ $assessment->is_published ? 'Terbit' : 'Draf' }}</span></div></div>
      @empty<div class="teacher-empty">Belum ada soal yang dibuat.</div>@endforelse
    </section>
  </div>
  <p class="teacher-footnote">Siswa yang sudah login dapat memasukkan kode kelas lewat halaman <a href="{{ route('classroom.home') }}">Kelas Saya</a>. Materi dan kuis yang dipublikasikan akan muncul sesuai kelasnya.</p>
</main>
      </div>
    </div>
  </body>
</html>

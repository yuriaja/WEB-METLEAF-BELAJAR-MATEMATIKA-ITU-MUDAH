@extends('layouts.app')
@section('title', 'Materi Pembelajaran | MathLeaf')
@section('header_title', 'Materi Pembelajaran')
@section('content')
<link rel="stylesheet" href="{{ asset('css/materials-upgrade.css') }}">
<div class="mlm-page">
    <div class="mlm-heading">
        <div><span class="mlm-eyebrow">MathLeaf · Materi Kelas VII</span><h1>{{ $isTeacher ? 'Kelola Materi Pembelajaran' : 'Ayo Belajar Matematika!' }}</h1>
        <p>{{ $isTeacher ? 'Upload dan publikasikan materi untuk kelas yang kamu ajar.' : 'Materi sesuai kelasmu. Buka untuk belajar dan simpan progres.' }}</p></div>
        @if($isTeacher && $classes->isNotEmpty())<a class="mlm-button" href="#tambah-materi">+ Tambah materi</a>@endif
    </div>
    @if(session('success'))<div class="mlm-alert mlm-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mlm-alert mlm-danger"><strong>Periksa isian:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form method="GET" action="{{ route('materi.index') }}" class="mlm-filters">
        <label><span>Cari materi</span><input type="search" name="q" value="{{ $search }}" placeholder="Bilangan, pecahan, operasi hitung..."></label>
        <label><span>Kelas</span><select name="class_id"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected((string)$selectedClassId === (string)$class->id)>{{ $class->name }}</option>@endforeach</select></label>
        <label><span>Status</span><select name="status">
            <option value="">Semua</option>
            @if($isTeacher)
                <option value="published" @selected(request('status') === 'published')>Dipublikasikan</option>
                <option value="draft" @selected(request('status') === 'draft')>Draf</option>
            @else
                <option value="done" @selected(request('status') === 'done')>Selesai dipelajari</option>
                <option value="undone" @selected(request('status') === 'undone')>Belum selesai</option>
            @endif
        </select></label>
        <button class="mlm-button" type="submit">Terapkan</button>
        <a class="mlm-muted-link" href="{{ route('materi.index') }}">Reset</a>
    </form>

    <div class="mlm-grid">
        @forelse($materials as $material)
            @php $percent = (int) ($material->progress->first()?->progress_percent ?? 0); @endphp
            <article class="mlm-card">
                <div class="mlm-card-top"><span class="mlm-type">{{ strtoupper($material->type) }}</span>
                    @if($isTeacher)<span class="mlm-status {{ $material->is_published ? 'mlm-published' : 'mlm-draft' }}">{{ $material->is_published ? 'Terbit' : 'Draf' }}</span>@endif
                </div>
                <h2><a href="{{ route('materi.show', $material) }}">{{ $material->title }}</a></h2>
                <p>{{ \Illuminate\Support\Str::limit($material->description ?: 'Pelajari materi yang disiapkan oleh gurumu.', 115) }}</p>
                <div class="mlm-meta"><span>Kelas: {{ $classes->firstWhere('id', $material->class_id)?->name ?? 'Kelas' }}</span><span>{{ $material->created_at?->format('d M Y') }}</span></div>
                @unless($isTeacher)
                    <div class="mlm-progress"><div style="width:{{ $percent }}%"></div></div>
                    <small>Progres belajarmu: {{ $percent }}%</small>
                @endunless
                <a class="mlm-button mlm-card-link" href="{{ route('materi.show', $material) }}">{{ $isTeacher ? 'Kelola materi' : ($percent === 100 ? 'Lihat kembali' : 'Mulai belajar') }} →</a>
            </article>
        @empty
            <div class="mlm-empty">{{ $classes->isEmpty() ? ($isTeacher ? 'Buat kelas terlebih dahulu sebelum mengunggah materi.' : 'Kamu belum tergabung di kelas. Masukkan kode kelas dari guru.') : 'Belum ada materi yang sesuai dengan filter ini.' }}
                <p><a href="{{ route('classroom.home') }}">Buka halaman Kelas Saya</a></p>
            </div>
        @endforelse
    </div>
    <div class="mlm-pagination">{{ $materials->links() }}</div>

    @if($isTeacher && $classes->isNotEmpty())
        <section id="tambah-materi" class="mlm-panel">
            <h2>Tambah Materi Baru</h2><p>Materi baru disimpan sebagai draf. Publikasikan dari halaman detail jika sudah siap.</p>
            @include('pages.shared.material-form', ['material' => null])
        </section>
    @endif
</div>
@endsection

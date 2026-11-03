@extends('layouts.app')
@section('title', $material->title . ' | MathLeaf')
@section('header_title', 'Detail Materi')
@section('content')
<link rel="stylesheet" href="{{ asset('css/materials-upgrade.css') }}">
<div class="mlm-page">
    <a href="{{ route('materi.index') }}" class="mlm-muted-link">← Kembali ke Materi</a>
    @if(session('success'))<div class="mlm-alert mlm-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mlm-alert mlm-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="mlm-heading mlm-detail-heading"><div>
        <span class="mlm-eyebrow">{{ strtoupper($material->type) }} · {{ $material->author?->name ?? 'Guru' }}</span>
        <h1>{{ $material->title }}</h1><p>{{ $material->description ?: 'Materi pembelajaran matematika kelas VII.' }}</p>
    </div>@if($isOwner)<span class="mlm-status {{ $material->is_published ? 'mlm-published' : 'mlm-draft' }}">{{ $material->is_published ? 'Dipublikasikan' : 'Draf' }}</span>@endif</div>

    <section class="mlm-panel">
        <h2>Isi Materi</h2>
        @php $ext = strtolower(pathinfo($material->original_filename ?: ($material->file_path ?: ''), PATHINFO_EXTENSION)); @endphp
        @if($material->file_path)
            @if($ext === 'pdf')
                <iframe class="mlm-preview" title="Pratinjau PDF {{ $material->title }}" src="{{ route('materi.file', $material) }}"></iframe>
            @elseif(in_array($ext, ['mp4', 'webm'], true))
                <video class="mlm-video" controls preload="metadata" src="{{ route('materi.file', $material) }}">Browser tidak mendukung pemutaran video.</video>
            @elseif(in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true))
                <img class="mlm-image" src="{{ route('materi.file', $material) }}" alt="{{ $material->title }}">
            @else
                <p>File presentasi atau modul tersedia untuk diunduh.</p>
            @endif
            <p><a class="mlm-button" href="{{ route('materi.file', $material) }}?download=1">Unduh {{ $material->original_filename ?: 'materi' }}</a></p>
        @endif
        @if($material->resource_url)
            <p><a href="{{ $material->resource_url }}" target="_blank" rel="noopener noreferrer" class="mlm-button">Buka tautan materi ↗</a></p>
        @endif
        @if(!$material->file_path && !$material->resource_url)<p>Belum ada berkas atau tautan pada materi ini.</p>@endif
    </section>

    @if(!$isOwner)
        @php $progress = (int) ($material->progress->first()?->progress_percent ?? 0); @endphp
        <section class="mlm-panel"><h2>Progres Belajar</h2>
            <div class="mlm-progress"><div style="width: {{ $progress }}%"></div></div>
            <p>Progres kamu: <strong>{{ $progress }}%</strong></p>
            <form method="POST" action="{{ route('materi.complete', $material) }}" class="mlm-actions">
                @csrf
                @if($progress < 100)<button name="progress_percent" value="50" type="submit" class="mlm-button mlm-outline">Tandai sedang dipelajari (50%)</button>
                <button name="progress_percent" value="100" type="submit" class="mlm-button">Tandai selesai (100%)</button>
                @else
                <button name="progress_percent" value="50" type="submit" class="mlm-button mlm-outline">Ulangi belajar (50%)</button>
                @endif
            </form>
        </section>
    @else
        <section class="mlm-panel"><h2>Publikasi</h2><p>Siswa hanya melihat materi yang sudah dipublikasikan di kelasnya.</p>
            <form method="POST" action="{{ route('materi.publish', $material) }}">@csrf @method('PATCH')
                <button type="submit" class="mlm-button">{{ $material->is_published ? 'Sembunyikan dari siswa' : 'Publikasikan ke siswa' }}</button>
            </form>
        </section>
        <section class="mlm-panel"><h2>Edit Materi</h2>
            @include('pages.shared.material-form', ['material' => $material])
        </section>
        <section class="mlm-panel"><h2>Hapus Materi</h2><p>Materi yang sudah punya progres siswa atau terhubung tugas/kuis tidak bisa dihapus; sembunyikan melalui publikasi.</p>
            <form method="POST" action="{{ route('materi.destroy', $material) }}" onsubmit="return confirm('Hapus materi ini secara permanen?')">@csrf @method('DELETE')
                <button class="mlm-button mlm-danger-button" type="submit">Hapus materi</button>
            </form>
        </section>
    @endif
</div>
@endsection

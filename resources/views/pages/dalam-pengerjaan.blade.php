@extends('layouts.public')
@section('title', $judul . ' | MathLeaf')
@section('content')
<main class="section site-container">
    <div class="section-heading centered">
        <span class="eyebrow"><x-icon name="sprout" :size="14" /> MATHLEAF — TAHAP KONVERSI</span>
        <h1>{{ $judul }}</h1>
        <p>Halaman ini belum dikonversi dari Lovable ke Laravel Blade.<br>Untuk saat ini, kamu bisa melihat beranda dan halaman tentang.</p>
        <p style="margin-top:25px"><a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda <x-icon name="arrow-right" /></a></p>
    </div>
</main>
@endsection

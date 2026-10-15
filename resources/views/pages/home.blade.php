@extends('layouts.public')
@section('title', 'Beranda | MathLeaf')
@section('description', 'Matematika jadi lebih mudah dan menyenangkan. Belajar bilangan dan pecahan bersama MathLeaf.')
@section('content')
<main>
    <section class="landing-hero">
        <img src="{{ asset('images/mathleaf-hero.jpg') }}" width="1536" height="864" alt="Dua pelajar belajar matematika bersama dengan buku dan bentuk bilangan" class="hero-image">
        <div class="site-container hero-inner">
            <div class="hero-copy">
                <span class="hero-pill"><span></span> BELAJAR HARI INI, BERTUMBUH ESOK HARI</span>
                <h1>Matematika Jadi<br>Lebih Mudah dan<br><span>Menyenangkan!</span><span class="headline-leaf"><x-icon name="leaf" :size="30" /></span></h1>
                <p>Belajar bilangan bulat dan pecahan melalui video,<br class="desktop-break"> latihan interaktif, dan tantangan seru.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ url('/daftar') }}">Mulai Belajar <x-icon name="arrow-up-right" /></a>
                    <a class="btn btn-outline" href="{{ url('/materi') }}"><x-icon name="play" :size="16" /> Lihat Materi</a>
                </div>
                <div class="hero-proof">
                    <div class="mini-avatars"><span>A</span><span>R</span><span>D</span><span>N</span></div>
                    <div><div class="stars">★★★★★</div><small>Teman belajar siswa kelas VII SMP</small></div>
                </div>
            </div>
            <div class="hero-floating">
                <span class="floating-icon"><x-icon name="sprout" /></span>
                <span>Langkah kecil, kemajuan besar.<small>Tumbuhkan potensi matematikamu!</small></span>
            </div>
        </div>
    </section>
    <section class="trust-band"><div class="site-container">
        <span><x-icon name="graduation-cap" /> Dirancang untuk Kelas VII SMP</span><i></i>
        <span><x-icon name="book-open" /> Sesuai Kurikulum Merdeka</span><i></i>
        <span><x-icon name="users" /> Belajar Aktif dengan FPBL</span><i></i>
        <span><x-icon name="leaf" /> Bertumbuh Bersama MathLeaf</span>
    </div></section>
    <section class="section benefits-section">
        <div class="site-container">
            <div class="section-heading centered">
                <span class="eyebrow"><x-icon name="leaf" :size="14" /> CARA BARU BELAJAR MATEMATIKA</span>
                <h2>Bukan sekadar belajar.<br><span>Tapi memahami dan bertumbuh.</span></h2>
                <p>Setiap anak punya cara belajar. Temukan cara terbaikmu bersama MathLeaf.</p>
            </div>
            <div class="benefits-grid">
                @foreach ([
                    ['icon' => 'video', 'title' => 'Belajar Sesuai Ritmemu', 'text' => 'Video singkat dan e-modul yang bisa dipelajari kapan saja, di mana saja.', 'tone' => 'mint'],
                    ['icon' => 'sparkles', 'title' => 'Latihan yang Bikin Paham', 'text' => 'Eksplorasi konsep lewat latihan interaktif dan feedback langsung.', 'tone' => 'yellow'],
                    ['icon' => 'users', 'title' => 'Berpikir & Berkolaborasi', 'text' => 'Pecahkan masalah nyata dan bertukar ide bersama teman sekelas.', 'tone' => 'peach'],
                    ['icon' => 'graduation-cap', 'title' => 'Lihat Progresmu Tumbuh', 'text' => 'Pantau setiap pencapaian dan rayakan langkah kecil belajarmu.', 'tone' => 'mint']
                ] as $benefit)
                    <div class="benefit">
                        <span class="benefit-icon tone-{{ $benefit['tone'] }}"><x-icon :name="$benefit['icon']" :size="25" /></span>
                        <h3>{{ $benefit['title'] }}</h3>
                        <p>{{ $benefit['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section class="section materials-section">
        <div class="site-container">
            <div class="section-heading horizontal">
                <div>
                    <span class="eyebrow"><x-icon name="book-open" :size="14" /> MULAI DARI DASAR, MELANGKAH LEBIH JAUH</span>
                    <h2>Petualangan belajarmu<br>dimulai di sini.</h2>
                    <p>Kenali bilangan, kuasai operasi, dan terapkan dalam keseharian.</p>
                </div>
                <a class="btn btn-outline" href="{{ url('/materi') }}">Lihat Semua Materi <x-icon name="arrow-up-right" /></a>
            </div>
            <div class="material-grid">
                @foreach (array_slice(config('mathleaf.materials', []), 0, 3) as $item)
                    <x-material-card :item="$item" />
                @endforeach
            </div>
        </div>
    </section>
    <section class="section fpbl-section">
        <div class="site-container">
            <div class="section-heading centered">
                <span class="eyebrow"><x-icon name="sprout" :size="14" /> BELAJAR DENGAN CARA YANG BERMAKNA</span>
                <h2>Empat langkah menuju <span>“Aku paham!”</span></h2>
                <p>Alur Flipped-Problem Based Learning: belajar mandiri, eksplorasi, dan kolaborasi.</p>
            </div>
            <x-fpbl-stepper />
        </div>
    </section>
    <section class="cta-band"><div class="site-container">
        <div>
            <span class="eyebrow"><x-icon name="leaf" :size="14" /> SATU LANGKAH KECIL UNTUK HARI INI</span>
            <h2>Siap menumbuhkan<br>kemampuan matematikamu?</h2>
            <p>Rasa ingin tahu adalah awal dari petualangan hebat.</p>
        </div>
        <a class="btn btn-primary" href="{{ url('/daftar') }}">Yuk, Mulai Belajar! <x-icon name="arrow-up-right" /></a>
        <x-icon name="sprout" class="cta-sprout" :size="170" />
    </div></section>
</main>
@endsection

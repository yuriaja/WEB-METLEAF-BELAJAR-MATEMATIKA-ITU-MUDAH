<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentang MathLeaf</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f4fbf5;color:#173d28;font-family:system-ui,-apple-system,Segoe UI,sans-serif}
        header{background:white;border-bottom:1px solid #deecdf;padding:22px max(24px,calc((100vw - 1060px)/2))}
        header a{color:#086534;text-decoration:none;font-weight:750}
        main{max-width:1050px;margin:0 auto;padding:55px 24px 80px} .label{color:#15803d;font-size:13px;font-weight:750;letter-spacing:.12em}
        h1{font-size:clamp(2.2rem,5vw,3.5rem);line-height:1.18;margin:14px 0 22px}p{line-height:1.8;color:#475569;font-size:1.05rem}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:17px;margin-top:34px}
        .card{background:white;border:1px solid #deecdf;border-radius:17px;padding:24px}.card h2{font-size:1.1rem;margin-top:0;color:#086534}
        .btn{display:inline-block;background:#086534;color:white;padding:13px 22px;border-radius:10px;text-decoration:none;font-weight:750;margin-top:28px}
    </style>
</head>
<body>
<header><a href="{{ route('home') }}">← MathLeaf — Kembali ke Beranda</a></header>
<main>
    <span class="label">GROW YOUR MATH SKILLS</span>
    <h1>Tentang MathLeaf</h1>
    <p>MathLeaf adalah ruang belajar matematika untuk siswa kelas VII SMP. Guru dapat mengelola kelas, membagikan materi, membuat kuis dan tugas, serta memantau hasil belajar. Siswa bisa belajar sesuai kelasnya, mengerjakan aktivitas, dan berdiskusi dengan guru serta teman satu kelas.</p>
    <div class="grid">
        <section class="card"><h2>Belajar lebih terarah</h2><p>Materi bilangan, latihan, dan kuis disusun untuk membantu siswa memahami konsep matematika secara bertahap.</p></section>
        <section class="card"><h2>Kolaborasi</h2><p>Forum kelas membantu guru dan siswa bertanya, menjawab, serta berbagi pemahaman.</p></section>
        <section class="card"><h2>Progres terpantau</h2><p>Nilai tugas, hasil kuis, dan progres materi dicatat untuk membantu evaluasi pembelajaran.</p></section>
    </div>
    <a class="btn" href="{{ route('login') }}">Masuk ke MathLeaf</a>
</main>
</body>
</html>

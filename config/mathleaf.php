<?php
return [
    'materials' => [
        ['id' => 1, 'title' => 'Bilangan Bulat', 'description' => 'Kenali bilangan positif, negatif, dan nol dalam kehidupan sehari-hari.', 'symbol' => '−3 + 5', 'category' => 'Dasar bilangan', 'lessons' => 4, 'minutes' => 32, 'tone' => 'mint'],
        ['id' => 2, 'title' => 'Penjumlahan & Pengurangan', 'description' => 'Jelajahi operasi bilangan bulat dengan bantuan garis bilangan.', 'symbol' => '7 − (−2)', 'category' => 'Operasi bilangan', 'lessons' => 5, 'minutes' => 40, 'tone' => 'yellow'],
        ['id' => 3, 'title' => 'Perkalian & Pembagian', 'description' => 'Temukan pola tanda dan selesaikan operasi dengan percaya diri.', 'symbol' => '−4 × 3', 'category' => 'Operasi bilangan', 'lessons' => 4, 'minutes' => 35, 'tone' => 'peach'],
        ['id' => 4, 'title' => 'Bilangan Pecahan', 'description' => 'Pahami bagian dari keseluruhan, pecahan senilai, dan perbandingan.', 'symbol' => '½ + ¼', 'category' => 'Dasar pecahan', 'lessons' => 4, 'minutes' => 30, 'tone' => 'mint'],
        ['id' => 5, 'title' => 'Operasi Hitung Pecahan', 'description' => 'Berlatih menjumlahkan, mengurangi, mengalikan, dan membagi pecahan.', 'symbol' => '¾ ÷ ½', 'category' => 'Operasi pecahan', 'lessons' => 5, 'minutes' => 45, 'tone' => 'yellow'],
        ['id' => 6, 'title' => 'Bilangan dalam Kehidupan', 'description' => 'Pecahkan masalah suhu, belanja, dan untung-rugi di sekitarmu.', 'symbol' => '−5°C → 8°C', 'category' => 'Penerapan', 'lessons' => 3, 'minutes' => 25, 'tone' => 'peach'],
    ],
    'steps' => [
        ['title' => 'Mengamati & Menanya', 'text' => 'Tonton video, temukan hal baru, dan tuliskan pertanyaanmu.', 'to' => '/video', 'icon' => 'video'],
        ['title' => 'Eksplorasi Awal', 'text' => 'Baca e-modul dan uji pemahamanmu lewat E-LKPD interaktif.', 'to' => '/latihan', 'icon' => 'book-open'],
        ['title' => 'Investigasi Kelompok', 'text' => 'Berkolaborasi dengan teman untuk memecahkan masalah nyata.', 'to' => '/tugas', 'icon' => 'users'],
        ['title' => 'Presentasi & Evaluasi', 'text' => 'Bagikan hasil, refleksikan proses, dan rayakan perkembanganmu.', 'to' => '/kuis', 'icon' => 'award'],
    ],
];

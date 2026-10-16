<?php

use Illuminate\Support\Facades\Route;

// Hasil konversi MathLeaf: file HTML lengkap disimpan sebagai view Blade.
// CADANGKAN routes/web.php lama sebelum mengganti atau gabungkan route ini.
Route::view('/', 'pages.index');
Route::view('/tentang', 'pages.tentang');

Route::view('/masuk', 'pages.auth.masuk');
Route::view('/daftar', 'pages.auth.daftar');

Route::view('/forum', 'pages.shared.forum');
Route::view('/tugas', 'pages.shared.tugas');
Route::view('/materi', 'pages.shared.materi');

Route::view('/dashboard', 'pages.siswa.dashboard');
Route::view('/video', 'pages.siswa.video');
Route::view('/latihan', 'pages.siswa.latihan');
Route::view('/kuis', 'pages.siswa.kuis');
Route::view('/nilai', 'pages.siswa.nilai');

Route::view('/guru', 'pages.guru');
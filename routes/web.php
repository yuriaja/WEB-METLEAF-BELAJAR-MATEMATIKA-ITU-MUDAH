<?php

use Illuminate\Support\Facades\Route;

// Hasil konversi MathLeaf: file HTML lengkap disimpan sebagai view Blade.
// CADANGKAN routes/web.php lama sebelum mengganti atau gabungkan route ini.
Route::view('/', 'pages.index');
Route::view('/tentang', 'pages.tentang');
Route::view('/masuk', 'pages.masuk');
Route::view('/daftar', 'pages.daftar');
Route::view('/dashboard', 'pages.dashboard');
Route::view('/materi', 'pages.materi');
Route::view('/video', 'pages.video');
Route::view('/latihan', 'pages.latihan');
Route::view('/kuis', 'pages.kuis');
Route::view('/forum', 'pages.forum');
Route::view('/tugas', 'pages.tugas');
Route::view('/nilai', 'pages.nilai');
Route::view('/guru', 'pages.guru');
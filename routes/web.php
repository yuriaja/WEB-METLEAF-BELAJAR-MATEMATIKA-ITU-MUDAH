<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MathLeafController as ML;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ClassTaskController;
use App\Http\Controllers\TeacherGradesController;

/*
|--------------------------------------------------------------------------
| Halaman Publik & Autentikasi
|--------------------------------------------------------------------------
*/
Route::view('/', 'pages.index')->name('home');
Route::view('/tentang', 'pages.tentang')->name('tentang');
Route::view('/masuk', 'pages.auth.masuk')->name('login');
Route::view('/daftar', 'pages.auth.daftar')->name('register');
Route::post('/login', [ML::class, 'login'])->middleware('throttle:10,1')->name('login.post');
Route::post('/logout', [ML::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Halaman Akses Bersama (Siswa & Guru)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Tampilan statis
    Route::view('/materi', 'pages.shared.materi')->name('materi.index');
    Route::view('/forum', 'pages.shared.forum')->name('forum.index');
    Route::view('/tugas', 'pages.shared.tugas')->name('tugas.index');
    
    // Fitur Kelas Bersama
    Route::prefix('kelas')->name('classroom.')->group(function () {
        Route::get('/', [ClassroomController::class, 'home'])->name('home');
        Route::post('/aktif', [ClassroomController::class, 'setActiveClass'])->name('active');
        Route::post('/buat', [ClassroomController::class, 'createClass'])->name('create');
        Route::post('/gabung', [ClassroomController::class, 'joinClass'])->name('join');
        
        // Materi di dalam kelas
        Route::post('/materi', [ClassroomController::class, 'upload'])->name('upload');
        Route::patch('/materi/{material}/publikasi', [ClassroomController::class, 'toggleMaterial'])->name('publish');
        Route::get('/materi/{material}/file', [ClassroomController::class, 'file'])->name('file');
        
        // Kuis di dalam kelas (Akses Siswa & Guru)
        Route::post('/kuis', [ClassroomController::class, 'createQuiz'])->name('quiz');
        Route::get('/kuis/{assessment}', [ClassroomController::class, 'takeQuiz'])->name('quiz.take');
        Route::post('/kuis/{assessment}/jawab', [ClassroomController::class, 'submitQuiz'])->name('quiz.submit');

        // Tugas Kelas (Branch Hakim)
        Route::get('/tugas', [ClassTaskController::class, 'index'])->name('tasks.index');
        Route::post('/tugas', [ClassTaskController::class, 'store'])->name('tasks.store');
        Route::post('/tugas/{activity}/kumpul', [ClassTaskController::class, 'submit'])->name('tasks.submit');
        Route::patch('/tugas/pengumpulan/{submission}/nilai', [ClassTaskController::class, 'grade'])->name('tasks.grade');
        Route::get('/tugas/pengumpulan/{submission}/file', [ClassTaskController::class, 'download'])->name('tasks.download');
    });
});

/*
|--------------------------------------------------------------------------
| Halaman Khusus Siswa
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Rute yang seringkali menyebabkan 404 jika dipanggil dari Blade tetapi file di /siswa tidak ada.
    Route::view('/dashboard', 'pages.siswa.dashboard')->name('siswa.dashboard');
    Route::view('/video', 'pages.siswa.video')->name('siswa.video');
    Route::view('/latihan', 'pages.siswa.latihan')->name('siswa.latihan');
    Route::view('/kuis', 'pages.siswa.kuis')->name('siswa.kuis');
    Route::view('/nilai', 'pages.siswa.nilai')->name('siswa.nilai');
});

/*
|--------------------------------------------------------------------------
| Halaman Khusus Guru
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('guru')->name('teacher.')->group(function () {
    Route::get('/', [ClassroomController::class, 'teacherDashboard'])->name('dashboard');
    
    // Manajemen Kuis Guru
    Route::get('/kuis', [ClassroomController::class, 'teacherQuizzes'])->name('quizzes');
    Route::post('/kuis/buat', [ClassroomController::class, 'startQuiz'])->name('quizzes.create');
    Route::get('/kuis/{assessment}/edit', [ClassroomController::class, 'editQuiz'])->name('quizzes.edit');
    Route::put('/kuis/{assessment}', [ClassroomController::class, 'saveQuiz'])->name('quizzes.update');
    
    // Manajemen Nilai Guru (Branch Hakim)
    Route::get('/rekap-nilai', [TeacherGradesController::class, 'index'])->name('grades');
    Route::get('/rekap-nilai/export', [TeacherGradesController::class, 'export'])->name('grades.export');
    Route::get('/rekap-nilai/siswa/{student}', [TeacherGradesController::class, 'student'])->name('grades.student');
});

/*
|--------------------------------------------------------------------------
| API MathLeaf
|--------------------------------------------------------------------------
*/
Route::prefix('api/mathleaf')->group(function () {
    Route::post('/register', [ML::class, 'register'])->middleware('throttle:10,1');
    Route::post('/login', [ML::class, 'login'])->middleware('throttle:10,1');
    
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [ML::class, 'logout']);
        Route::get('/me', [ML::class, 'me']);
        Route::get('/overview', [ML::class, 'overview']);
        
        Route::get('/materials', [ML::class, 'materials']);
        Route::post('/materials/{material}/progress', [ML::class, 'completeMaterial']);
        
        Route::get('/activities', [ML::class, 'activities']);
        Route::post('/activities', [ML::class, 'storeActivity']);
        
        Route::get('/assessments', [ML::class, 'assessments']);
        Route::get('/assessments/{assessment}/questions', [ML::class, 'questions']);
        Route::post('/assessments/{assessment}/submit', [ML::class, 'submitAssessment']);
        
        Route::get('/submissions', [ML::class, 'submissions']);
        Route::post('/submissions', [ML::class, 'storeSubmission']);
        Route::patch('/submissions/{submission}/grade', [ML::class, 'grade']);
        Route::get('/submissions/{submission}/download', [ML::class, 'download']);
        
        Route::get('/threads', [ML::class, 'threads']);
        Route::post('/threads', [ML::class, 'newThread']);
        Route::post('/threads/{thread}/comments', [ML::class, 'comment']);
        
        Route::get('/teacher/overview', [ML::class, 'teacherOverview']);
        Route::post('/teacher/materials', [ML::class, 'newMaterial']);
        Route::patch('/teacher/materials/{material}', [ML::class, 'publishMaterial']);
    });
});
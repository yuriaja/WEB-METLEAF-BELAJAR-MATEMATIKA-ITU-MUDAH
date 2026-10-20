<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MathLeafController as ML;
Route::view('/', 'pages.index'); Route::view('/tentang','pages.tentang');
Route::view('/masuk','pages.auth.masuk')->name('login'); Route::view('/daftar','pages.auth.daftar');
Route::middleware('auth')->group(function () {
    Route::view('/materi','pages.shared.materi'); Route::view('/forum','pages.shared.forum'); Route::view('/tugas','pages.shared.tugas');
    Route::view('/dashboard','pages.siswa.dashboard'); Route::view('/video','pages.siswa.video'); Route::view('/latihan','pages.siswa.latihan'); Route::view('/kuis','pages.siswa.kuis'); Route::view('/nilai','pages.siswa.nilai'); Route::get('/guru', [\App\Http\Controllers\ClassroomController::class, 'teacherDashboard'])->name('teacher.dashboard');
});
Route::prefix('api/mathleaf')->group(function () {
    Route::post('/register',[ML::class,'register'])->middleware('throttle:10,1');
    Route::post('/login',[ML::class,'login'])->middleware('throttle:10,1');
    Route::middleware('auth')->group(function () {
        Route::post('/logout',[ML::class,'logout']); Route::get('/me',[ML::class,'me']); Route::get('/overview',[ML::class,'overview']);
        Route::get('/materials',[ML::class,'materials']);Route::post('/materials/{material}/progress',[ML::class,'completeMaterial']);
        Route::get('/activities',[ML::class,'activities']); Route::post('/activities',[ML::class,'storeActivity']);
        Route::get('/assessments',[ML::class,'assessments']);Route::get('/assessments/{assessment}/questions',[ML::class,'questions']);Route::post('/assessments/{assessment}/submit',[ML::class,'submitAssessment']);
        Route::get('/submissions',[ML::class,'submissions']);Route::post('/submissions',[ML::class,'storeSubmission']);Route::patch('/submissions/{submission}/grade',[ML::class,'grade']);Route::get('/submissions/{submission}/download',[ML::class,'download']);
        Route::get('/threads',[ML::class,'threads']);Route::post('/threads',[ML::class,'newThread']);Route::post('/threads/{thread}/comments',[ML::class,'comment']);
        Route::get('/teacher/overview',[ML::class,'teacherOverview']);Route::post('/teacher/materials',[ML::class,'newMaterial']);Route::patch('/teacher/materials/{material}',[ML::class,'publishMaterial']);
    });
});
// Kelas guru dan siswa: registrasi tanpa kode, gabung setelah login.
Route::middleware('auth')->group(function () {
 Route::get('/kelas',[\App\Http\Controllers\ClassroomController::class,'home'])->name('classroom.home');
 Route::post('/kelas/buat',[\App\Http\Controllers\ClassroomController::class,'createClass'])->name('classroom.create');
 Route::post('/kelas/gabung',[\App\Http\Controllers\ClassroomController::class,'joinClass'])->name('classroom.join');
 Route::post('/kelas/materi',[\App\Http\Controllers\ClassroomController::class,'upload'])->name('classroom.upload');
 Route::patch('/kelas/materi/{material}/publikasi',[\App\Http\Controllers\ClassroomController::class,'toggleMaterial'])->name('classroom.publish');
 Route::get('/kelas/materi/{material}/file',[\App\Http\Controllers\ClassroomController::class,'file'])->name('classroom.file');
 Route::post('/kelas/kuis',[\App\Http\Controllers\ClassroomController::class,'createQuiz'])->name('classroom.quiz');
 Route::get('/kelas/kuis/{assessment}',[\App\Http\Controllers\ClassroomController::class,'takeQuiz'])->name('classroom.quiz.take');
 Route::post('/kelas/kuis/{assessment}/jawab',[\App\Http\Controllers\ClassroomController::class,'submitQuiz'])->name('classroom.quiz.submit');
});



/*
|--------------------------------------------------------------------------
| Fitur Tugas Kelas - Branch Hakim
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Menampilkan tugas kelas
    Route::get(
        '/kelas/tugas',
        [\App\Http\Controllers\ClassTaskController::class, 'index']
    )->name('class-tasks.index');

    // Guru membuat tugas
    Route::post(
        '/kelas/tugas',
        [\App\Http\Controllers\ClassTaskController::class, 'store']
    )->name('class-tasks.store');

    // Siswa mengumpulkan tugas
    Route::post(
        '/kelas/tugas/{activity}/kumpul',
        [\App\Http\Controllers\ClassTaskController::class, 'submit']
    )->name('class-tasks.submit');

    // Guru memberikan nilai
    Route::patch(
        '/kelas/tugas/pengumpulan/{submission}/nilai',
        [\App\Http\Controllers\ClassTaskController::class, 'grade']
    )->name('class-tasks.grade');

    // Download file
    Route::get(
        '/kelas/tugas/pengumpulan/{submission}/file',
        [\App\Http\Controllers\ClassTaskController::class, 'download']
    )->name('class-tasks.download');

});

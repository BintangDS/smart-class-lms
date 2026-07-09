<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\CourseWebController;

// Rute Publik (Redirect ke login atau dashboard)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Autentikasi Web Session
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rute Terproteksi Auth Web
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Alur Belajar Siswa / Ruang Kelas
    Route::post('/courses/{id}/enroll', [CourseWebController::class, 'enroll'])->name('courses.enroll');
    Route::get('/courses/{id}/classroom', [CourseWebController::class, 'classroom'])->name('classroom');
    Route::post('/lessons/{id}/complete', [CourseWebController::class, 'completeLesson'])->name('lessons.complete');
    
    // Kuis & Tugas
    Route::post('/quizzes/{id}/submit', [CourseWebController::class, 'submitQuiz'])->name('quizzes.submit');
    Route::post('/assignments/{id}/submit', [CourseWebController::class, 'submitAssignment'])->name('assignments.submit');
    
    // Penilaian (Instructor)
    Route::put('/submissions/{id}/grade', [CourseWebController::class, 'gradeSubmission'])->name('submissions.grade');
});

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

    // Manajemen Kursus (Instructor)
    Route::get('/courses/create', [CourseWebController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseWebController::class, 'store'])->name('courses.store');
    Route::get('/courses/{id}/edit', [CourseWebController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [CourseWebController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{id}', [CourseWebController::class, 'destroy'])->name('courses.destroy');

    // Manajemen Modul (Instructor)
    Route::post('/courses/{id}/modules', [CourseWebController::class, 'storeModule'])->name('modules.store');
    Route::delete('/modules/{id}', [CourseWebController::class, 'destroyModule'])->name('modules.destroy');

    // Manajemen Lesson/Materi (Instructor)
    Route::post('/modules/{id}/lessons', [CourseWebController::class, 'storeLesson'])->name('lessons.store');
    Route::delete('/lessons/{id}', [CourseWebController::class, 'destroyLesson'])->name('lessons.destroy');

    // Manajemen Kuis & Ujian (Instructor)
    Route::post('/modules/{moduleId}/quizzes', [CourseWebController::class, 'storeQuiz'])->name('quizzes.store');
    Route::delete('/quizzes/{id}', [CourseWebController::class, 'destroyQuiz'])->name('quizzes.destroy');
    Route::get('/quizzes/{id}/questions', [CourseWebController::class, 'manageQuizQuestions'])->name('quizzes.questions.manage');
    Route::post('/quizzes/{id}/questions', [CourseWebController::class, 'storeQuizQuestion'])->name('quizzes.questions.store');
    Route::delete('/questions/{id}', [CourseWebController::class, 'destroyQuizQuestion'])->name('quizzes.questions.destroy');

    // Manajemen Tugas/Assignment (Instructor)
    Route::post('/modules/{moduleId}/assignments', [CourseWebController::class, 'storeAssignment'])->name('assignments.store');
    Route::delete('/assignments/{id}', [CourseWebController::class, 'destroyAssignment'])->name('assignments.destroy');

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

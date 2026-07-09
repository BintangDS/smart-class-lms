<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\DashboardController;

Route::prefix('v1')->group(function () {
    // Rute Publik Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Rute Publik Kursus & Kategori
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);

    // Rute Publik Sertifikat (Verifikasi)
    Route::get('/certificates/verify/{code}', [CertificateController::class, 'verify']);

    // Rute Terproteksi Auth & Instruktur & Siswa
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::put('/auth/profile', [AuthController::class, 'profile']);

        // CRUD Kursus & Konten (Instructor)
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
        Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
        
        Route::post('/courses/{id}/modules', [ModuleController::class, 'store']);
        Route::post('/modules/{id}/lessons', [LessonController::class, 'store']);
        Route::put('/lessons/{id}', [LessonController::class, 'update']);

        // Pendaftaran & Progres Belajar (Student / Instructor)
        Route::post('/courses/{id}/enroll', [EnrollmentController::class, 'enroll']);
        Route::get('/my-courses', [EnrollmentController::class, 'myCourses']);
        Route::post('/lessons/{id}/complete', [EnrollmentController::class, 'completeLesson']);
        Route::get('/courses/{id}/progress', [EnrollmentController::class, 'progressDetail']);

        // Kuis & Tugas (Student / Instructor)
        Route::post('/modules/{id}/quizzes', [QuizController::class, 'store']);
        Route::get('/quizzes/{id}', [QuizController::class, 'show']);
        Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit']);
        
        Route::post('/modules/{id}/assignments', [AssignmentController::class, 'store']);
        Route::post('/assignments/{id}/submit', [AssignmentController::class, 'submit']);
        Route::put('/submissions/{id}/grade', [AssignmentController::class, 'grade']);

        // Sertifikat (Student)
        Route::get('/certificates', [CertificateController::class, 'index']);

        // Dashboard Stats
        Route::get('/dashboard/instructor', [DashboardController::class, 'instructor']);
        Route::get('/dashboard/admin', [DashboardController::class, 'admin']);
    });
});

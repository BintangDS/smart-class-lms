<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get statistics for the authenticated instructor.
     */
    public function instructor(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'instructor' && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Rute ini hanya untuk Instruktur.',
            ], 403);
        }

        $instructorCourseIds = Course::where('instructor_id', $user->id)->pluck('id');

        $totalCourses = $instructorCourseIds->count();
        $totalStudents = Enrollment::whereIn('course_id', $instructorCourseIds)->count();
        
        $avgProgress = Enrollment::whereIn('course_id', $instructorCourseIds)->avg('progress_percent') ?? 0;
        $avgProgress = round($avgProgress, 1);

        $graduatedStudents = Enrollment::whereIn('course_id', $instructorCourseIds)
            ->whereNotNull('completed_at')
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Statistik dashboard instruktur berhasil dimuat.',
            'data' => [
                'total_courses' => $totalCourses,
                'total_students' => $totalStudents,
                'average_progress' => $avgProgress,
                'graduated_students' => $graduatedStudents,
            ]
        ]);
    }

    /**
     * Get statistics for the authenticated admin.
     */
    public function admin(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Rute ini hanya untuk Administrator.',
            ], 403);
        }

        $totalCourses = Course::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalCertificates = Certificate::count();

        return response()->json([
            'success' => true,
            'message' => 'Statistik dashboard admin berhasil dimuat.',
            'data' => [
                'total_courses' => $totalCourses,
                'total_students' => $totalStudents,
                'total_instructors' => $totalInstructors,
                'total_certificates' => $totalCertificates,
            ]
        ]);
    }
}

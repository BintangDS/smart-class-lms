<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\ProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    protected ProgressService $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Enroll the authenticated student in a course.
     */
    public function enroll(Request $request, int $courseId): JsonResponse
    {
        $user = $request->user();

        // Restrict to student role only (as per PRD)
        if ($user->role !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya siswa yang dapat mendaftar ke kursus.',
            ], 403);
        }

        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        // Restrict to published courses only
        if ($course->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Kursus belum diterbitkan.',
            ], 403);
        }

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di kursus ini.',
            ], 422);
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'enrolled_at' => now(),
            'progress_percent' => 0,
        ]);

        $enrollment->load('course.instructor', 'course.category');

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran kursus berhasil.',
            'data' => new EnrollmentResource($enrollment)
        ], 201);
    }

    /**
     * Display courses enrolled by the authenticated student.
     */
    public function myCourses(Request $request): JsonResponse
    {
        $user = $request->user();

        $enrollments = Enrollment::with(['course.instructor', 'course.category'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar pendaftaran kursus berhasil dimuat.',
            'data' => EnrollmentResource::collection($enrollments)->response()->getData(true)
        ]);
    }

    /**
     * Mark a lesson as completed for the authenticated student.
     */
    public function completeLesson(Request $request, int $lessonId): JsonResponse
    {
        $user = $request->user();
        
        $lesson = Lesson::with('module')->find($lessonId);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson tidak ditemukan.',
            ], 404);
        }

        // Find the student's enrollment for the course containing this lesson
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->module->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus mendaftar ke kursus ini terlebih dahulu.',
            ], 403);
        }

        // Mark completed (firstOrCreate to prevent duplicate entries)
        LessonProgress::firstOrCreate([
            'enrollment_id' => $enrollment->id,
            'lesson_id' => $lesson->id,
        ]);

        // Recalculate progress using ProgressService
        $progressPercent = $this->progressService->updateProgress($enrollment);

        return response()->json([
            'success' => true,
            'message' => 'Lesson berhasil ditandai selesai.',
            'data' => [
                'progress_percent' => $progressPercent,
            ]
        ]);
    }

    /**
     * Get detailed student progress breakdown for a course.
     */
    public function progressDetail(Request $request, int $courseId): JsonResponse
    {
        $user = $request->user();
        $course = Course::with('modules.lessons')->find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        $enrollment = null;

        if ($user->role === 'student') {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki pendaftaran pada kursus ini.',
                ], 403);
            }
        } else {
            // Instructor or Admin
            // Instructor must own the course
            if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke data kursus ini.',
                ], 403);
            }

            // Student ID must be passed to view their progress
            $studentId = $request->query('student_id');
            if (!$studentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID siswa wajib disertakan (student_id).',
                ], 422);
            }

            $enrollment = Enrollment::where('user_id', $studentId)
                ->where('course_id', $courseId)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pendaftaran siswa tidak ditemukan.',
                ], 404);
            }
        }

        // Get completed lessons IDs
        $completedLessonIds = $enrollment->lessonProgress()->pluck('lesson_id')->toArray();

        $modulesData = $course->modules->map(function ($module) use ($completedLessonIds) {
            return [
                'id' => $module->id,
                'title' => $module->title,
                'order' => $module->order,
                'lessons' => $module->lessons->map(function ($lesson) use ($completedLessonIds) {
                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'content_type' => $lesson->content_type,
                        'order' => $lesson->order,
                        'completed' => in_array($lesson->id, $completedLessonIds),
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Detail progres berhasil dimuat.',
            'data' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'progress_percent' => $enrollment->progress_percent,
                'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->toIso8601String() : null,
                'modules' => $modulesData,
            ]
        ]);
    }
}

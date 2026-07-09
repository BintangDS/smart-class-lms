<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ModuleController extends Controller
{
    /**
     * Store a newly created module under a specific course.
     */
    public function store(StoreModuleRequest $request, int $courseId): JsonResponse
    {
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        // Authorize: Only the instructor of the course or an admin can add modules
        Gate::authorize('update', $course);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'order' => $request->order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Modul berhasil ditambahkan.',
            'data' => new ModuleResource($module)
        ], 201);
    }
}

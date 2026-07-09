<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use App\Http\Resources\CourseDetailResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['instructor', 'category']);

        // Default to published courses, unless instructor requests their own list
        if ($request->has('instructor_only') && auth('sanctum')->check()) {
            $query->where('instructor_id', auth('sanctum')->id());
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
        } else {
            $query->where('status', 'published');
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $courses = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kursus berhasil dimuat.',
            'data' => CourseResource::collection($courses)->response()->getData(true)
        ]);
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(StoreCourseRequest $request): JsonResponse
    {
        Gate::authorize('create', Course::class);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course = Course::create([
            'instructor_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'description' => $request->description,
            'thumbnail' => $thumbnailPath,
            'level' => $request->level,
            'status' => $request->status ?? 'draft',
        ]);

        $course->load(['instructor', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Kursus berhasil dibuat.',
            'data' => new CourseResource($course)
        ], 201);
    }

    /**
     * Display the specified course.
     */
    public function show(int $id): JsonResponse
    {
        $course = Course::with(['instructor', 'category', 'modules.lessons'])->find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kursus berhasil dimuat.',
            'data' => new CourseDetailResource($course)
        ]);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(UpdateCourseRequest $request, int $id): JsonResponse
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        Gate::authorize('update', $course);

        // Update basic fields
        if ($request->has('category_id')) {
            $course->category_id = $request->category_id;
        }
        if ($request->has('title')) {
            $course->title = $request->title;
            $course->slug = Str::slug($request->title) . '-' . Str::random(5);
        }
        if ($request->has('description')) {
            $course->description = $request->description;
        }
        if ($request->has('level')) {
            $course->level = $request->level;
        }
        if ($request->has('status')) {
            $course->status = $request->status;
        }

        // Update thumbnail
        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $course->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->save();
        $course->load(['instructor', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Kursus berhasil diperbarui.',
            'data' => new CourseResource($course)
        ]);
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        Gate::authorize('delete', $course);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kursus berhasil dihapus.',
            'data' => null
        ]);
    }
}

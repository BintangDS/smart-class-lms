<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreLessonRequest;
use App\Http\Requests\Api\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Store a newly created lesson under a specific module.
     */
    public function store(StoreLessonRequest $request, int $moduleId): JsonResponse
    {
        $module = Module::with('course')->find($moduleId);

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan.',
            ], 404);
        }

        // Authorize: Only the instructor of the course or an admin can add lessons
        Gate::authorize('update', $module->course);

        $content = $request->content;
        if ($request->content_type !== 'text' && $request->hasFile('content')) {
            $content = $request->file('content')->store('lessons', 'public');
        }

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => $request->title,
            'content_type' => $request->content_type,
            'content' => $content,
            'order' => $request->order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lesson berhasil ditambahkan.',
            'data' => new LessonResource($lesson)
        ], 201);
    }

    /**
     * Update the specified lesson in storage.
     */
    public function update(UpdateLessonRequest $request, int $id): JsonResponse
    {
        $lesson = Lesson::with('module.course')->find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson tidak ditemukan.',
            ], 404);
        }

        // Authorize: Only the instructor of the course or an admin can update lessons
        Gate::authorize('update', $lesson->module->course);

        if ($request->has('title')) {
            $lesson->title = $request->title;
        }

        if ($request->has('order')) {
            $lesson->order = $request->order;
        }

        $contentType = $request->content_type ?? $lesson->content_type;
        $lesson->content_type = $contentType;

        // Update content
        if ($request->has('content')) {
            // Delete old file if it was a file type
            if ($lesson->content_type !== 'text' && $lesson->content && Storage::disk('public')->exists($lesson->content)) {
                Storage::disk('public')->delete($lesson->content);
            }

            if ($contentType !== 'text' && $request->hasFile('content')) {
                $lesson->content = $request->file('content')->store('lessons', 'public');
            } else {
                $lesson->content = $request->content;
            }
        }

        $lesson->save();

        return response()->json([
            'success' => true,
            'message' => 'Lesson berhasil diperbarui.',
            'data' => new LessonResource($lesson)
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAssignmentRequest;
use App\Http\Requests\Api\SubmitAssignmentRequest;
use App\Http\Requests\Api\GradeSubmissionRequest;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\AssignmentSubmissionResource;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Store a newly created assignment under a module.
     */
    public function store(StoreAssignmentRequest $request, int $moduleId): JsonResponse
    {
        $module = Module::with('course')->find($moduleId);

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan.',
            ], 404);
        }

        // Authorize: Only the instructor of the course can add assignments
        Gate::authorize('update', $module->course);

        $assignment = Assignment::create([
            'module_id' => $module->id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'max_score' => $request->max_score,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dibuat.',
            'data' => new AssignmentResource($assignment)
        ], 201);
    }

    /**
     * Submit an assignment (upload file).
     */
    public function submit(SubmitAssignmentRequest $request, int $id): JsonResponse
    {
        $assignment = Assignment::find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak ditemukan.',
            ], 404);
        }

        $user = $request->user();

        // Check if student is enrolled in the course
        $module = Module::find($assignment->module_id);
        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $module->course_id)
            ->exists();

        if (!$enrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus mendaftar ke kursus ini terlebih dahulu.',
            ], 403);
        }

        // Check if deadline has passed
        if (now()->greaterThan($assignment->due_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu pengumpulan tugas sudah terlewati.',
            ], 403);
        }

        // Upload file
        $filePath = $request->file('file')->store('submissions', 'public');

        // Delete old file if updating submission
        $oldSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->first();

        if ($oldSubmission && $oldSubmission->file_path) {
            Storage::disk('public')->delete($oldSubmission->file_path);
        }

        // Save submission
        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $user->id,
            ],
            [
                'file_path' => $filePath,
                'submitted_at' => now(),
                'score' => null,
                'feedback' => null,
                'graded_at' => null,
            ]
        );

        $submission->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dikumpulkan.',
            'data' => new AssignmentSubmissionResource($submission)
        ], 201);
    }

    /**
     * Grade a student's assignment submission (manual grade by Instructor).
     */
    public function grade(GradeSubmissionRequest $request, int $submissionId): JsonResponse
    {
        $submission = AssignmentSubmission::with('assignment.module.course')->find($submissionId);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Kiriman tugas tidak ditemukan.',
            ], 404);
        }

        // Authorize: Only the instructor of the course can grade submissions
        Gate::authorize('update', $submission->assignment->module->course);

        // Validate max score bounds
        $maxScore = $submission->assignment->max_score;
        if ($request->score > $maxScore) {
            return response()->json([
                'success' => false,
                'message' => "Nilai tidak boleh melebihi skor maksimal tugas ({$maxScore}).",
                'errors' => [
                    'score' => ["Nilai tidak boleh melebihi skor maksimal tugas ({$maxScore})."]
                ]
            ], 422);
        }

        $submission->score = $request->score;
        $submission->feedback = $request->feedback;
        $submission->graded_at = now();
        $submission->save();

        $submission->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Penilaian tugas berhasil disimpan.',
            'data' => new AssignmentSubmissionResource($submission)
        ]);
    }
}

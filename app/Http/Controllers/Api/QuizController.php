<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreQuizRequest;
use App\Http\Requests\Api\SubmitQuizRequest;
use App\Http\Resources\QuizAttemptResource;
use App\Http\Resources\QuizResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class QuizController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * Store a newly created quiz under a module.
     */
    public function store(StoreQuizRequest $request, int $moduleId): JsonResponse
    {
        $module = Module::with('course')->find($moduleId);

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan.',
            ], 404);
        }

        // Authorize: Only course creator can add quizzes
        Gate::authorize('update', $module->course);

        $quiz = DB::transaction(function () use ($request, $module) {
            // Create Quiz
            $quiz = Quiz::create([
                'module_id' => $module->id,
                'title' => $request->title,
                'passing_score' => $request->passing_score,
            ]);

            // Create Questions & Options
            foreach ($request->questions as $questionData) {
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question_text'],
                ]);

                foreach ($questionData['options'] as $optionData) {
                    QuizOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                    ]);
                }
            }

            return $quiz;
        });

        $quiz->load('questions.options');

        return response()->json([
            'success' => true,
            'message' => 'Kuis berhasil dibuat.',
            'data' => new QuizResource($quiz)
        ], 201);
    }

    /**
     * Display the specified quiz (hides is_correct for students).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $quiz = Quiz::with('questions.options')->find($id);

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis tidak ditemukan.',
            ], 404);
        }

        // Check enrollment if user is a student
        $user = $request->user();
        if ($user->role === 'student') {
            $module = Module::find($quiz->module_id);
            $enrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $module->course_id)
                ->exists();

            if (!$enrolled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus mendaftar ke kursus ini terlebih dahulu.',
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kuis berhasil dimuat.',
            'data' => new QuizResource($quiz)
        ]);
    }

    /**
     * Submit quiz answers and get score automatically.
     */
    public function submit(SubmitQuizRequest $request, int $id): JsonResponse
    {
        $quiz = Quiz::find($id);

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis tidak ditemukan.',
            ], 404);
        }

        $user = $request->user();

        // Check if student is enrolled
        $module = Module::find($quiz->module_id);
        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $module->course_id)
            ->exists();

        if (!$enrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus mendaftar ke kursus ini terlebih dahulu.',
            ], 403);
        }

        // Process submission via QuizService
        $attempt = $this->quizService->submitAnswers($user, $quiz, $request->answers);
        $attempt->load('quiz');

        return response()->json([
            'success' => true,
            'message' => 'Jawaban kuis berhasil disubmit.',
            'data' => new QuizAttemptResource($attempt)
        ]);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\Category;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\QuizAttempt;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Services\ProgressService;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class CourseWebController extends Controller
{
    protected ProgressService $progressService;
    protected QuizService $quizService;

    public function __construct(ProgressService $progressService, QuizService $quizService)
    {
        $this->progressService = $progressService;
        $this->quizService = $quizService;
    }

    /**
     * Show form to create a new course.
     */
    public function create()
    {
        if (Auth::user()->role !== 'instructor') {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $categories = Category::all();
        return view('course.create', compact('categories'));
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'instructor') {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'status' => 'required|in:draft,published',
        ]);

        Course::create([
            'instructor_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'level' => $request->level,
            'status' => $request->status,
        ]);

        return redirect()->route('dashboard')->with('success', 'Kursus berhasil dibuat!');
    }

    /**
     * Show form to edit an existing course.
     */
    public function edit($id)
    {
        $course = Course::findOrFail($id);

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $categories = Category::all();
        return view('course.edit', compact('course', 'categories'));
    }

    /**
     * Update an existing course.
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'status' => 'required|in:draft,published',
        ]);

        $course->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'level' => $request->level,
            'status' => $request->status,
        ]);

        return redirect()->route('dashboard')->with('success', 'Kursus berhasil diperbarui!');
    }

    /**
     * Delete an existing course.
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $course->delete();

        return redirect()->route('dashboard')->with('success', 'Kursus berhasil dihapus!');
    }

    /**
     * Store a new module under a course.
     */
    public function storeModule(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
        ]);

        Module::create([
            'course_id' => $courseId,
            'title' => $request->title,
            'order' => $request->order,
        ]);

        return back()->with('success', 'Modul berhasil ditambahkan!');
    }

    /**
     * Delete a module.
     */
    public function destroyModule($id)
    {
        $module = Module::findOrFail($id);
        $course = $module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $module->delete();

        return back()->with('success', 'Modul berhasil dihapus!');
    }

    /**
     * Store a new lesson under a module.
     */
    public function storeLesson(Request $request, $moduleId)
    {
        $module = Module::findOrFail($moduleId);
        $course = $module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:text,video,document',
            'content' => 'required|string',
            'order' => 'required|integer|min:1',
        ]);

        Lesson::create([
            'module_id' => $moduleId,
            'title' => $request->title,
            'content_type' => $request->content_type,
            'content' => $request->content,
            'order' => $request->order,
        ]);

        return back()->with('success', 'Materi berhasil ditambahkan!');
    }

    /**
     * Delete a lesson.
     */
    public function destroyLesson($id)
    {
        $lesson = Lesson::findOrFail($id);
        $course = $lesson->module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $lesson->delete();

        return redirect()->route('classroom', $course->id)->with('success', 'Materi berhasil dihapus!');
    }

    /**
     * Enroll in a course (for students).
     */
    public function enroll($courseId)
    {
        $course = Course::findOrFail($courseId);
        $userId = Auth::id();

        if ($course->status !== 'published') {
            return back()->with('error', 'Kursus ini belum dipublikasikan.');
        }

        // Check if already enrolled
        $existing = Enrollment::where('user_id', $userId)->where('course_id', $courseId)->first();
        if ($existing) {
            return redirect()->route('classroom', $courseId)->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        // Enroll student
        Enrollment::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrolled_at' => now(),
            'progress_percent' => 0,
        ]);

        return redirect()->route('classroom', $courseId)->with('success', 'Berhasil mendaftar ke kursus.');
    }

    /**
     * Classroom page for learning materials, quizzes, and assignments.
     */
    public function classroom(Request $request, $courseId)
    {
        $course = Course::with(['instructor', 'category', 'modules.lessons', 'modules.quizzes.questions', 'modules.assignments'])
            ->findOrFail($courseId);

        $userId = Auth::id();
        $userRole = Auth::user()->role;

        // Otorisasi: Siswa harus terdaftar (enroll) atau dia adalah instruktur pembuat kursus, atau admin
        $enrollment = null;
        if ($userRole === 'student') {
            $enrollment = Enrollment::where('user_id', $userId)->where('course_id', $courseId)->first();
            if (!$enrollment) {
                return redirect()->route('dashboard')->with('error', 'Anda harus mendaftar ke kursus ini terlebih dahulu.');
            }
        } elseif ($userRole === 'instructor') {
            if ($course->instructor_id !== $userId) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses ke kursus ini.');
            }
        }

        // Ambil lesson aktif
        $activeLessonId = $request->query('lesson_id');
        $activeLesson = null;
        
        // Cari lesson pertama jika tidak ada lesson_id di request
        $allLessons = $course->modules->flatMap->lessons;
        if ($activeLessonId) {
            $activeLesson = Lesson::where('id', $activeLessonId)->first();
        } else {
            $activeLesson = $allLessons->first();
        }

        // Dapatkan data progres murid
        $completedLessonIds = [];
        if ($enrollment) {
            $completedLessonIds = LessonProgress::where('enrollment_id', $enrollment->id)->pluck('lesson_id')->toArray();
        }

        // Data kuis dan tugas di modul
        $attempts = QuizAttempt::where('user_id', $userId)->with('quiz')->get()->keyBy('quiz_id');
        $submissions = AssignmentSubmission::where('user_id', $userId)->with('assignment')->get()->keyBy('assignment_id');

        return view('course.classroom', compact('course', 'activeLesson', 'enrollment', 'completedLessonIds', 'attempts', 'submissions', 'allLessons'));
    }

    /**
     * Mark a lesson complete.
     */
    public function completeLesson($lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $courseId = $lesson->module->course_id;
        $userId = Auth::id();

        $enrollment = Enrollment::where('user_id', $userId)->where('course_id', $courseId)->firstOrFail();

        // Mark complete
        LessonProgress::firstOrCreate([
            'enrollment_id' => $enrollment->id,
            'lesson_id' => $lessonId,
        ], [
            'completed_at' => now(),
        ]);

        // Update progress & certificate
        $this->progressService->updateProgress($enrollment);

        // Get next lesson to redirect
        $allLessons = $lesson->module->course->modules->flatMap->lessons;
        $currentIndex = $allLessons->pluck('id')->search($lessonId);
        $nextLesson = $allLessons->get($currentIndex + 1);

        if ($nextLesson) {
            return redirect()->route('classroom', ['id' => $courseId, 'lesson_id' => $nextLesson->id])
                ->with('success', 'Materi berhasil diselesaikan!');
        }

        return redirect()->route('classroom', $courseId)->with('success', 'Materi berhasil diselesaikan! Anda telah mencapai akhir materi.');
    }

    /**
     * Submit answers for a quiz.
     */
    public function submitQuiz(Request $request, $quizId)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        $user = Auth::user();

        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.option_id' => 'required|exists:quiz_options,id',
        ]);

        $attempt = $this->quizService->submitAnswers($user, $quiz, $request->answers);

        return redirect()->route('classroom', $quiz->module->course_id)->with(
            'success', 
            "Kuis berhasil dikirim! Nilai Anda: {$attempt->score}. Skor kelulusan: {$quiz->passing_score}."
        );
    }

    /**
     * Submit file for assignment.
     */
    public function submitAssignment(Request $request, $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $user = Auth::user();

        $request->validate([
            'file' => 'required|file|mimes:pdf,zip,doc,docx,jpg,png|max:20480', // max 20MB
        ]);

        // Check enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $assignment->module->course_id)
            ->firstOrFail();

        if (now()->greaterThan($assignment->due_date)) {
            return back()->with('error', 'Batas waktu pengumpulan tugas sudah terlewati.');
        }

        $filePath = $request->file('file')->store('submissions', 'public');

        // Delete old file if exists
        $oldSubmission = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('user_id', $user->id)
            ->first();

        if ($oldSubmission && $oldSubmission->file_path) {
            Storage::disk('public')->delete($oldSubmission->file_path);
        }

        AssignmentSubmission::updateOrCreate([
            'assignment_id' => $assignmentId,
            'user_id' => $user->id,
        ], [
            'file_path' => $filePath,
            'submitted_at' => now(),
            'score' => null,
            'feedback' => null,
            'graded_at' => null,
        ]);

        return redirect()->route('classroom', $assignment->module->course_id)->with('success', 'Tugas berhasil dikumpulkan!');
    }

    /**
     * Grade a student submission (Instructor only).
     */
    public function gradeSubmission(Request $request, $submissionId)
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);
        $course = $submission->assignment->module->course;

        // Authorize instructor
        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $request->validate([
            'score' => 'required|integer|min:0|max:' . $submission->assignment->max_score,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Penilaian tugas berhasil disimpan!');
    }

    /**
     * Store a new quiz.
     */
    public function storeQuiz(Request $request, $moduleId)
    {
        $module = Module::findOrFail($moduleId);
        $course = $module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'passing_score' => 'required|integer|min:0|max:100',
        ]);

        Quiz::create([
            'module_id' => $moduleId,
            'title' => $request->title,
            'passing_score' => $request->passing_score,
        ]);

        return back()->with('success', 'Kuis berhasil dibuat! Silakan kelola soal kuis.');
    }

    /**
     * Delete a quiz.
     */
    public function destroyQuiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        $course = $quiz->module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $quiz->delete();

        return back()->with('success', 'Kuis berhasil dihapus!');
    }

    /**
     * Manage Quiz Questions page.
     */
    public function manageQuizQuestions($quizId)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($quizId);
        $course = $quiz->module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        return view('quiz.manage', compact('quiz', 'course'));
    }

    /**
     * Add question & options to quiz.
     */
    public function storeQuizQuestion(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $course = $quiz->module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|size:4',
            'options.*' => 'required|string|max:255',
            'correct_option' => 'required|integer|min:0|max:3',
        ]);

        $question = QuizQuestion::create([
            'quiz_id' => $quizId,
            'question_text' => $request->question_text,
        ]);

        foreach ($request->options as $index => $optionText) {
            QuizOption::create([
                'question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => $index == $request->correct_option,
            ]);
        }

        return back()->with('success', 'Pertanyaan berhasil ditambahkan!');
    }

    /**
     * Delete a question.
     */
    public function destroyQuizQuestion($id)
    {
        $question = QuizQuestion::findOrFail($id);
        $quiz = $question->quiz;
        $course = $quiz->module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $question->delete();

        return back()->with('success', 'Pertanyaan berhasil dihapus!');
    }

    /**
     * Store a new assignment.
     */
    public function storeAssignment(Request $request, $moduleId)
    {
        $module = Module::findOrFail($moduleId);
        $course = $module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'max_score' => 'required|integer|min:1',
            'due_date' => 'required|date_format:Y-m-d\TH:i',
        ]);

        Assignment::create([
            'module_id' => $moduleId,
            'title' => $request->title,
            'description' => $request->description,
            'max_score' => $request->max_score,
            'due_date' => $request->due_date,
        ]);

        return back()->with('success', 'Tugas berhasil dibuat!');
    }

    /**
     * Delete an assignment.
     */
    public function destroyAssignment($id)
    {
        $assignment = Assignment::findOrFail($id);
        $course = $assignment->module->course;

        if (Auth::user()->role !== 'instructor' || $course->instructor_id !== Auth::id()) {
            abort(403, 'Aksi tidak diotorisasi.');
        }

        $assignment->delete();

        return back()->with('success', 'Tugas berhasil dihapus!');
    }

    /**
     * View Certificate page (Public).
     */
    public function viewCertificate($code)
    {
        $certificate = Certificate::where('certificate_code', $code)
            ->with(['user', 'course.instructor'])
            ->firstOrFail();

        return view('course.certificate', compact('certificate'));
    }
}

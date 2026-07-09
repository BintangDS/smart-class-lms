<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuizAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private User $student;
    private Category $category;
    private Course $course;
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instructor = User::factory()->create(['role' => 'instructor']);
        $this->student = User::factory()->create(['role' => 'student']);

        $this->category = Category::create([
            'name' => 'Design',
            'slug' => 'design',
        ]);

        $this->course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Figma UI/UX',
            'slug' => 'figma-ui-ux',
            'description' => 'Course description',
            'level' => 'beginner',
            'status' => 'published',
        ]);

        $this->module = Module::create([
            'course_id' => $this->course->id,
            'title' => 'Modul Figma 1',
            'order' => 1,
        ]);
    }

    /**
     * Test instructor can create quiz and student can submit it.
     */
    public function test_quiz_lifecycle()
    {
        // 1. Create Quiz (Instructor)
        $responseCreate = $this->actingAs($this->instructor, 'sanctum')
            ->postJson("/api/v1/modules/{$this->module->id}/quizzes", [
                'title' => 'Kuis Figma Dasar',
                'passing_score' => 80,
                'questions' => [
                    [
                        'question_text' => 'Apa singkatan dari UI?',
                        'options' => [
                            ['option_text' => 'User Interface', 'is_correct' => true],
                            ['option_text' => 'User Integration', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'Apa singkatan dari UX?',
                        'options' => [
                            ['option_text' => 'User Experience', 'is_correct' => true],
                            ['option_text' => 'User Xylophone', 'is_correct' => false],
                        ]
                    ]
                ]
            ]);

        $responseCreate->assertStatus(201);
        $quizId = $responseCreate->json('data.id');

        // Enroll student to Figma course
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'progress_percent' => 0,
        ]);

        // 2. View Quiz (Student) - is_correct must be hidden
        $responseView = $this->actingAs($this->student, 'sanctum')
            ->getJson("/api/v1/quizzes/{$quizId}");

        $responseView->assertStatus(200)
            ->assertJsonMissing(['is_correct' => true])
            ->assertJsonMissing(['is_correct' => false]);

        // Get question & options IDs to submit
        $questions = $responseView->json('data.questions');
        $q1Id = $questions[0]['id'];
        $q1OptCorrectId = $questions[0]['options'][0]['id']; // User Interface

        $q2Id = $questions[1]['id'];
        $q2OptIncorrectId = $questions[1]['options'][1]['id']; // User Xylophone (Wrong)

        // 3. Submit Quiz (Student) - 1 correct, 1 wrong = 50% score
        $responseSubmit = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/v1/quizzes/{$quizId}/submit", [
                'answers' => [
                    ['question_id' => $q1Id, 'option_id' => $q1OptCorrectId],
                    ['question_id' => $q2Id, 'option_id' => $q2OptIncorrectId],
                ]
            ]);

        $responseSubmit->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'score' => 50,
                    'passed' => false, // passing_score is 80
                ]
            ]);
    }

    /**
     * Test assignment creation, submission, and grading.
     */
    public function test_assignment_lifecycle()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('jawaban.pdf', 500);

        // 1. Create Assignment (Instructor)
        $responseCreate = $this->actingAs($this->instructor, 'sanctum')
            ->postJson("/api/v1/modules/{$this->module->id}/assignments", [
                'title' => 'Tugas Figma Wireframing',
                'description' => 'Buat wireframe landing page.',
                'due_date' => now()->addDays(7)->toIso8601String(),
                'max_score' => 100,
            ]);

        $responseCreate->assertStatus(201);
        $assignmentId = $responseCreate->json('data.id');

        // Enroll student to course
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'progress_percent' => 0,
        ]);

        // 2. Submit Assignment (Student)
        $responseSubmit = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/v1/assignments/{$assignmentId}/submit", [
                'file' => $file,
            ]);

        $responseSubmit->assertStatus(201);
        $submissionId = $responseSubmit->json('data.id');

        $submission = AssignmentSubmission::find($submissionId);
        Storage::disk('public')->assertExists($submission->file_path);

        // 3. Grade Submission (Instructor)
        $responseGrade = $this->actingAs($this->instructor, 'sanctum')
            ->putJson("/api/v1/submissions/{$submissionId}/grade", [
                'score' => 90,
                'feedback' => 'Hasil wireframe sangat rapi!',
            ]);

        $responseGrade->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Penilaian tugas berhasil disimpan.',
                'data' => [
                    'score' => 90,
                    'feedback' => 'Hasil wireframe sangat rapi!',
                ]
            ]);
    }

    /**
     * Test student cannot submit assignment after deadline.
     */
    public function test_student_cannot_submit_after_deadline()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('jawaban.pdf', 500);

        // Create expired assignment (cannot use StoreAssignmentRequest since validation blocks creation of past deadline, so create via Eloquent)
        $assignment = Assignment::create([
            'module_id' => $this->module->id,
            'title' => 'Tugas Expired',
            'description' => 'Deskripsi',
            'due_date' => now()->subDay(), // 1 day ago
            'max_score' => 100,
        ]);

        // Enroll student
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'progress_percent' => 0,
        ]);

        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/v1/assignments/{$assignment->id}/submit", [
                'file' => $file,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Batas waktu pengumpulan tugas sudah terlewati.',
            ]);
    }
}

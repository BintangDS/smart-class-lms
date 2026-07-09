<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private User $instructor;
    private Category $category;
    private Course $publishedCourse;
    private Course $draftCourse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['role' => 'student']);
        $this->instructor = User::factory()->create(['role' => 'instructor']);
        
        $this->category = Category::create([
            'name' => 'Mobile Dev',
            'slug' => 'mobile-dev',
        ]);

        $this->publishedCourse = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'React Native Pro',
            'slug' => 'react-native-pro',
            'description' => 'Course description',
            'level' => 'intermediate',
            'status' => 'published',
        ]);

        $this->draftCourse = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Flutter Basic',
            'slug' => 'flutter-basic',
            'description' => 'Course description',
            'level' => 'beginner',
            'status' => 'draft',
        ]);
    }

    /**
     * Test student can enroll in a published course.
     */
    public function test_student_can_enroll_in_published_course()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/v1/courses/{$this->publishedCourse->id}/enroll");

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Pendaftaran kursus berhasil.',
            ]);

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $this->publishedCourse->id,
            'progress_percent' => 0,
        ]);
    }

    /**
     * Test student cannot enroll in a draft course.
     */
    public function test_student_cannot_enroll_in_draft_course()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/v1/courses/{$this->draftCourse->id}/enroll");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Kursus belum diterbitkan.',
            ]);
    }

    /**
     * Test duplicate enrollments are prevented.
     */
    public function test_student_cannot_enroll_multiple_times()
    {
        // First enrollment
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->publishedCourse->id,
            'progress_percent' => 0,
        ]);

        // Second enrollment request
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/v1/courses/{$this->publishedCourse->id}/enroll");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Anda sudah terdaftar di kursus ini.',
            ]);
    }

    /**
     * Test complete lesson updates progress percent.
     */
    public function test_complete_lesson_updates_progress()
    {
        // Create modules and lessons
        $module = Module::create([
            'course_id' => $this->publishedCourse->id,
            'title' => 'Modul 1',
            'order' => 1,
        ]);

        $lesson1 = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 1',
            'content_type' => 'text',
            'content' => 'Hello 1',
            'order' => 1,
        ]);

        $lesson2 = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 2',
            'content_type' => 'text',
            'content' => 'Hello 2',
            'order' => 2,
        ]);

        // Enroll student
        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->publishedCourse->id,
            'progress_percent' => 0,
        ]);

        // Complete lesson 1
        $response1 = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/v1/lessons/{$lesson1->id}/complete");

        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'progress_percent' => 50,
                ]
            ]);

        // Complete lesson 2 (100% completion)
        $response2 = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/v1/lessons/{$lesson2->id}/complete");

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'progress_percent' => 100,
                ]
            ]);

        // Verify completed_at is filled in DB
        $enrollment->refresh();
        $this->assertNotNull($enrollment->completed_at);
    }

    /**
     * Test listing my courses.
     */
    public function test_can_view_my_courses()
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->publishedCourse->id,
            'progress_percent' => 30,
        ]);

        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/v1/my-courses');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Daftar pendaftaran kursus berhasil dimuat.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'course',
                            'progress_percent',
                            'enrolled_at',
                        ]
                    ]
                ]
            ]);
    }
}

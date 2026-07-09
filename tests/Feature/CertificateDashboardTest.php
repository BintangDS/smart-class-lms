<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $instructor;
    private User $student;
    private Category $category;
    private Course $course;
    private Module $module;
    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->instructor = User::factory()->create(['role' => 'instructor']);
        $this->student = User::factory()->create(['role' => 'student']);

        $this->category = Category::create([
            'name' => 'Data Science',
            'slug' => 'data-science',
        ]);

        $this->course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Python Basics',
            'slug' => 'python-basics',
            'description' => 'Course description',
            'level' => 'beginner',
            'status' => 'published',
        ]);

        $this->module = Module::create([
            'course_id' => $this->course->id,
            'title' => 'Intro Python',
            'order' => 1,
        ]);

        $this->lesson = Lesson::create([
            'module_id' => $this->module->id,
            'title' => 'Lesson 1: Syntax',
            'content_type' => 'text',
            'content' => 'Print("hello")',
            'order' => 1,
        ]);
    }

    /**
     * Test certificate generation upon 100% course progress.
     */
    public function test_certificate_generated_upon_completion()
    {
        Storage::fake('public');

        // Enroll student
        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'progress_percent' => 0,
        ]);

        // Complete the only lesson to reach 100% progress
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/v1/lessons/{$this->lesson->id}/complete");

        $response->assertStatus(200);

        // Verify Certificate exists in DB
        $this->assertDatabaseHas('certificates', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        $certificate = Certificate::first();
        $this->assertNotNull($certificate->certificate_code);

        // Verify file is saved in public storage
        Storage::disk('public')->assertExists($certificate->file_path);

        // Verify public verification endpoint
        $responseVerify = $this->getJson("/api/v1/certificates/verify/{$certificate->certificate_code}");
        $responseVerify->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'certificate_code' => $certificate->certificate_code,
                    'student_name' => $this->student->name,
                    'course_title' => $this->course->title,
                ]
            ]);

        // Verify student can view certificate list
        $responseList = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/v1/certificates');
        $responseList->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    /**
     * Test instructor and admin dashboard stats.
     */
    public function test_dashboard_statistics()
    {
        // Add 1 student enrollment
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'progress_percent' => 50,
        ]);

        // 1. Instructor Dashboard
        $responseInstructor = $this->actingAs($this->instructor, 'sanctum')
            ->getJson('/api/v1/dashboard/instructor');

        $responseInstructor->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_courses' => 1,
                    'total_students' => 1,
                    'average_progress' => 50.0,
                    'graduated_students' => 0,
                ]
            ]);

        // Student cannot access instructor dashboard
        $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/v1/dashboard/instructor')
            ->assertStatus(403);

        // 2. Admin Dashboard
        $responseAdmin = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/dashboard/admin');

        $responseAdmin->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_courses' => 1,
                    'total_students' => 1, // Student role users
                    'total_instructors' => 1, // Instructor role users
                    'total_certificates' => 0,
                ]
            ]);

        // Instructor cannot access admin dashboard
        $this->actingAs($this->instructor, 'sanctum')
            ->getJson('/api/v1/dashboard/admin')
            ->assertStatus(403);
    }
}

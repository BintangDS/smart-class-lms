<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private User $student;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->instructor = User::factory()->create(['role' => 'instructor']);
        $this->student = User::factory()->create(['role' => 'student']);

        // Create category
        $this->category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
        ]);
    }

    /**
     * Test instructor can create a course.
     */
    public function test_instructor_can_create_course()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('thumbnail.jpg');

        $response = $this->actingAs($this->instructor, 'sanctum')
            ->postJson('/api/v1/courses', [
                'category_id' => $this->category->id,
                'title' => 'Belajar Laravel 11',
                'description' => 'Panduan belajar laravel end-to-end.',
                'thumbnail' => $file,
                'level' => 'beginner',
                'status' => 'published',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Kursus berhasil dibuat.',
            ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Belajar Laravel 11',
            'instructor_id' => $this->instructor->id,
            'level' => 'beginner',
            'status' => 'published',
        ]);

        $course = Course::first();
        Storage::disk('public')->assertExists($course->thumbnail);
    }

    /**
     * Test student cannot create a course.
     */
    public function test_student_cannot_create_course()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson('/api/v1/courses', [
                'category_id' => $this->category->id,
                'title' => 'Siswa Mencoba Membuat Kursus',
                'description' => 'Mencoba membuat.',
                'level' => 'beginner',
                'status' => 'draft',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test course details endpoint.
     */
    public function test_can_view_course_details_with_modules_and_lessons()
    {
        $course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Belajar React Native',
            'slug' => 'belajar-react-native',
            'description' => 'Deskripsi kursus',
            'level' => 'intermediate',
            'status' => 'published',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Modul 1: Intro',
            'order' => 1,
        ]);

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 1: Hello World',
            'content_type' => 'text',
            'content' => 'Ini isi materi teks.',
            'order' => 1,
        ]);

        $response = $this->getJson("/api/v1/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Detail kursus berhasil dimuat.',
                'data' => [
                    'title' => 'Belajar React Native',
                    'modules' => [
                        [
                            'title' => 'Modul 1: Intro',
                            'lessons' => [
                                [
                                    'title' => 'Lesson 1: Hello World',
                                    'content' => 'Ini isi materi teks.',
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /**
     * Test instructor can update their own course.
     */
    public function test_instructor_can_update_own_course()
    {
        $course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Judul Lama',
            'slug' => 'judul-lama',
            'description' => 'Deskripsi lama',
            'level' => 'beginner',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->instructor, 'sanctum')
            ->putJson("/api/v1/courses/{$course->id}", [
                'title' => 'Judul Baru',
                'description' => 'Deskripsi baru',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Kursus berhasil diperbarui.',
            ]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Judul Baru',
            'description' => 'Deskripsi baru',
        ]);
    }

    /**
     * Test instructor cannot update another instructor's course.
     */
    public function test_instructor_cannot_update_other_instructor_course()
    {
        $otherInstructor = User::factory()->create(['role' => 'instructor']);
        $course = Course::create([
            'instructor_id' => $otherInstructor->id,
            'category_id' => $this->category->id,
            'title' => 'Kursus Orang Lain',
            'slug' => 'kursus-orang-lain',
            'description' => 'Mencoba dibajak.',
            'level' => 'beginner',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->instructor, 'sanctum')
            ->putJson("/api/v1/courses/{$course->id}", [
                'title' => 'Mencoba Update Judul',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test instructor can add modules and lessons.
     */
    public function test_instructor_can_add_modules_and_lessons()
    {
        Storage::fake('public');
        $video = UploadedFile::fake()->create('video.mp4', 1024); // 1MB video

        $course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Kursus Web',
            'slug' => 'kursus-web',
            'description' => 'Belajar Web',
            'level' => 'beginner',
            'status' => 'draft',
        ]);

        // Add module
        $responseModule = $this->actingAs($this->instructor, 'sanctum')
            ->postJson("/api/v1/courses/{$course->id}/modules", [
                'title' => 'Modul Ke-1',
                'order' => 1,
            ]);

        $responseModule->assertStatus(201);
        $moduleId = $responseModule->json('data.id');

        // Add lesson
        $responseLesson = $this->actingAs($this->instructor, 'sanctum')
            ->postJson("/api/v1/modules/{$moduleId}/lessons", [
                'title' => 'Lesson Ke-1 (Video)',
                'content_type' => 'video',
                'content' => $video,
                'order' => 1,
            ]);

        $responseLesson->assertStatus(201);
        $this->assertDatabaseHas('lessons', [
            'module_id' => $moduleId,
            'title' => 'Lesson Ke-1 (Video)',
            'content_type' => 'video',
        ]);

        $lesson = Lesson::first();
        Storage::disk('public')->assertExists($lesson->content);
    }
}

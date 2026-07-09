<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test student registration.
     */
    public function test_student_can_register_successfully()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Bintang Siswa',
            'email' => 'bintang@student.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Registrasi berhasil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'avatar',
                        'created_at',
                    ],
                    'access_token',
                    'token_type',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'bintang@student.com',
            'role' => 'student',
        ]);
    }

    /**
     * Test registration validation.
     */
    public function test_registration_validation_fails_for_invalid_data()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test user login.
     */
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'student@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil.',
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'access_token',
                ]
            ]);
    }

    /**
     * Test login fails with wrong credentials.
     */
    public function test_login_fails_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'student@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Kredensial login salah.',
            ]);
    }

    /**
     * Test fetching logged in user profile.
     */
    public function test_user_can_fetch_profile_when_authenticated()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profil berhasil dimuat.',
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ]
            ]);
    }

    /**
     * Test profile fetching fails when unauthenticated.
     */
    public function test_profile_fetching_fails_when_unauthenticated()
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    /**
     * Test logout.
     */
    public function test_user_can_logout_successfully()
    {
        $user = User::factory()->create();

        // Generate token and act as user with token
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout berhasil.',
            ]);
    }

    /**
     * Test profile updates name and password.
     */
    public function test_user_can_update_profile_and_change_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/auth/profile', [
                'name' => 'Bintang Baru',
                'current_password' => 'oldpassword123',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.',
                'data' => [
                    'name' => 'Bintang Baru',
                ]
            ]);

        // Verify name changed in DB
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Bintang Baru',
        ]);

        // Verify password changed
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }
}

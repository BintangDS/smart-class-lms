<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Membuat user admin
        User::factory()->create([
            'name' => 'LMS Admin',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        // Membuat user instruktur
        User::factory()->create([
            'name' => 'LMS Instructor',
            'email' => 'instructor@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'instructor',
        ]);

        // Membuat user siswa
        User::factory()->create([
            'name' => 'LMS Student',
            'email' => 'student@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'student',
        ]);

        // Membuat kategori kursus awal
        \App\Models\Category::create([
            'name' => 'Laravel Development',
            'slug' => 'laravel-development',
        ]);

        \App\Models\Category::create([
            'name' => 'React Native Mobile',
            'slug' => 'react-native-mobile',
        ]);

        \App\Models\Category::create([
            'name' => 'UI/UX Design',
            'slug' => 'ui-ux-design',
        ]);
    }
}

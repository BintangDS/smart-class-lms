<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\Assignment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat akun demo
        $admin = User::factory()->create([
            'name' => 'Bintang Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $instructor = User::factory()->create([
            'name' => 'Kak Bintang (Senior Developer)',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
        ]);

        $student = User::factory()->create([
            'name' => 'LMS Student Demo',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        // 2. Membuat Kategori
        $catLaravel = Category::create([
            'name' => 'Laravel Backend Development',
            'slug' => 'laravel-backend-development',
        ]);

        $catReactNative = Category::create([
            'name' => 'React Native Mobile',
            'slug' => 'react-native-mobile',
        ]);

        $catDesign = Category::create([
            'name' => 'UI/UX Design',
            'slug' => 'ui-ux-design',
        ]);

        // 3. Membuat Kursus 1 (React Native Mobile)
        $courseRN = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $catReactNative->id,
            'title' => 'Pemrograman React Native untuk Pemula',
            'slug' => 'pemrograman-react-native-untuk-pemula',
            'description' => 'Mulai perjalanan Anda menjadi Mobile Developer dengan mempelajari dasar-dasar React Native. Kursus ini mengajarkan pembuatan komponen, styling, navigation, dan integrasi REST API.',
            'level' => 'beginner',
            'status' => 'published',
        ]);

        // Modul 1 untuk Kursus 1
        $moduleRN1 = Module::create([
            'course_id' => $courseRN->id,
            'title' => 'Modul 1: Pengenalan Komponen Dasar & Layout',
            'order' => 1,
        ]);

        // Lesson-lesson Modul 1
        $lessonRN1 = Lesson::create([
            'module_id' => $moduleRN1->id,
            'title' => 'Pengenalan View, Text, dan Image',
            'content_type' => 'text',
            'content' => "Selamat datang di materi pertama! Di React Native, kita tidak menggunakan tag HTML biasa (seperti div, p, img). Sebagai gantinya, kita menggunakan komponen native:\n\n1. <View>: Berperan sebagai wadah/container (seperti <div> di HTML).\n2. <Text>: Berperan untuk membungkus tulisan (seperti <p> atau <span>).\n3. <Image>: Berperan untuk menampilkan gambar.\n\nContoh penggunaan:\n```jsx\nimport { View, Text, Image } from 'react-native';\n\nexport default function App() {\n  return (\n    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>\n      <Text>Halo Dunia!</Text>\n    </View>\n  );\n}\n```",
            'order' => 1,
        ]);

        $lessonRN2 = Lesson::create([
            'module_id' => $moduleRN1->id,
            'title' => 'Memahami Flexbox di React Native',
            'content_type' => 'text',
            'content' => "Layouting di React Native menggunakan Flexbox secara bawaan. Ada beberapa aturan penting:\n\n1. flexDirection: Secara default bernilai 'column' (berbeda dengan CSS biasa yang bernilai 'row').\n2. justifyContent: Mengatur perataan komponen di sumbu utama (flex-start, center, flex-end, space-between, space-around).\n3. alignItems: Mengatur perataan komponen di sumbu silang.\n\nContoh Flexbox layout:\n```jsx\n<View style={{ flex: 1, flexDirection: 'row', justifyContent: 'space-between' }}>\n  <View style={{ width: 50, height: 50, backgroundColor: 'red' }} />\n  <View style={{ width: 50, height: 50, backgroundColor: 'blue' }} />\n</View>\n```",
            'order' => 2,
        ]);

        // Kuis untuk Modul 1
        $quizRN1 = Quiz::create([
            'module_id' => $moduleRN1->id,
            'title' => 'Kuis Komponen & Layouting',
            'passing_score' => 70,
        ]);

        $q1 = QuizQuestion::create([
            'quiz_id' => $quizRN1->id,
            'question_text' => 'Komponen apa yang digunakan untuk menampilkan tulisan di React Native?',
        ]);

        QuizOption::create([
            'question_id' => $q1->id,
            'option_text' => '<View>',
            'is_correct' => false,
        ]);
        QuizOption::create([
            'question_id' => $q1->id,
            'option_text' => '<Text>',
            'is_correct' => true,
        ]);
        QuizOption::create([
            'question_id' => $q1->id,
            'option_text' => '<Label>',
            'is_correct' => false,
        ]);

        $q2 = QuizQuestion::create([
            'quiz_id' => $quizRN1->id,
            'question_text' => 'Apakah nilai default untuk flexDirection di React Native?',
        ]);

        QuizOption::create([
            'question_id' => $q2->id,
            'option_text' => 'row',
            'is_correct' => false,
        ]);
        QuizOption::create([
            'question_id' => $q2->id,
            'option_text' => 'column',
            'is_correct' => true,
        ]);
        QuizOption::create([
            'question_id' => $q2->id,
            'option_text' => 'row-reverse',
            'is_correct' => false,
        ]);

        // Tugas untuk Modul 1
        $assignmentRN1 = Assignment::create([
            'module_id' => $moduleRN1->id,
            'title' => 'Tugas Membuat Layout Profil Sederhana',
            'description' => "Buatlah layout halaman profil pengguna yang terdiri dari:\n1. Foto profil di tengah lingkaran (menggunakan Image & borderRadius).\n2. Nama lengkap (Text tebal).\n3. Deskripsi singkat (bio).\n4. Gunakan Flexbox untuk mengatur tata letaknya agar rapi di emulator mobile.\n\nKumpulkan kode file App.js atau format .zip yang berisi hasil pekerjaan Anda.",
            'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'max_score' => 100,
        ]);

        // 4. Membuat Kursus 2 (Laravel Development)
        $courseLaravel = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $catLaravel->id,
            'title' => 'Advanced Laravel 11 Backend Masterclass',
            'slug' => 'advanced-laravel-11-backend-masterclass',
            'description' => 'Tingkatkan keahlian backend Anda ke level expert dengan mempelajari arsitektur RESTful API, Form Requests, Sanctum Token, Policies, Service Pattern, dan automated testing.',
            'level' => 'advanced',
            'status' => 'published',
        ]);

        // Modul 1 untuk Kursus 2
        $moduleLaravel1 = Module::create([
            'course_id' => $courseLaravel->id,
            'title' => 'Modul 1: API Security & Sanctum Authentication',
            'order' => 1,
        ]);

        // Lesson-lesson Modul 1 Kursus 2
        $lessonL1 = Lesson::create([
            'module_id' => $moduleLaravel1->id,
            'title' => 'Prinsip Kerja Token-Based Authentication',
            'content_type' => 'text',
            'content' => "API bersifat stateless, artinya server tidak mengingat siapa pengirim request lewat session browser biasa. Oleh karena itu, kita menggunakan token token-based authentication.\n\nDengan Laravel Sanctum:\n1. Siswa mengirim kredensial (email & password) ke endpoint login.\n2. Jika benar, server membuat token unik dan menyimpannya di database, lalu mengembalikan token tersebut ke client.\n3. Client (misal React Native) menyimpan token secara aman (Secure Store/AsyncStorage) dan mengirimkannya pada Header request berikutnya:\n`Authorization: Bearer {token}`",
            'order' => 1,
        ]);
    }
}

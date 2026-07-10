@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white">Halo, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-slate-400 mt-1">Selamat datang kembali. Mari lanjutkan belajarmu hari ini.</p>
        </div>
    </div>

    <!-- Enrolled Courses -->
    <div class="space-y-4">
        <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
            <span class="inline-block h-2 w-2 rounded-full bg-indigo-500"></span>
            Kelas yang Saya Ikuti
        </h2>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($myEnrollments as $enrollment)
            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 flex flex-col justify-between hover:border-indigo-500/30 transition duration-200">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-indigo-400 uppercase tracking-wider">
                            {{ $enrollment->course->category->name ?? 'Kategori' }}
                        </span>
                        <span class="text-xs text-slate-400 capitalize">
                            Level: {{ $enrollment->course->level }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-white leading-tight mb-2">{{ $enrollment->course->title }}</h3>
                    <p class="text-xs text-slate-400 mb-4 font-medium">Instruktur: {{ $enrollment->course->instructor->name ?? 'Instructor' }}</p>
                </div>
                
                <!-- Progress Bar -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-medium">
                        <span class="text-slate-400">Progres Belajar</span>
                        <span class="text-indigo-400 font-bold">{{ $enrollment->progress_percent }}%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-slate-850 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-violet-600 rounded-full transition-all duration-300" style="width: {{ $enrollment->progress_percent }}%"></div>
                    </div>
                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                        @if($enrollment->progress_percent >= 100)
                            <span class="inline-flex items-center gap-1 rounded bg-emerald-500/10 px-1.5 py-0.5 text-[10px] font-bold text-emerald-400 border border-emerald-500/20">
                                LULUS
                            </span>
                        @else
                            <span class="text-[10px] text-slate-500 font-medium">Aktif</span>
                        @endif
                        <a href="{{ route('classroom', $enrollment->course_id) }}" class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs px-4 py-2 transition duration-200">
                            Masuk Kelas
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full rounded-2xl border border-slate-800/80 bg-slate-900/30 p-8 text-center text-slate-500">
                Kamu belum mengikuti kelas mana pun. Silakan jelajahi katalog di bawah!
            </div>
            @endforelse
        </div>
    </div>

    <!-- Certificates -->
    @if($myCertificates->count() > 0)
    <div class="space-y-4">
        <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
            Sertifikat Kelulusan Saya
        </h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($myCertificates as $cert)
            <div class="rounded-xl border border-emerald-500/10 bg-emerald-950/10 p-4 flex items-center justify-between border-dashed hover:border-emerald-500/30 transition duration-200">
                <div class="space-y-1">
                    <span class="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20">{{ $cert->certificate_code }}</span>
                    <h4 class="text-sm font-bold text-white">{{ $cert->course->title }}</h4>
                    <p class="text-slate-400 text-xs">Selesai: {{ $cert->issued_at->toDateString() }}</p>
                </div>
                <a href="{{ route('certificates.view', $cert->certificate_code) }}" target="_blank" class="rounded-lg bg-slate-900 hover:bg-slate-850 p-2 border border-slate-800 text-emerald-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Catalog Courses -->
    <div class="space-y-6 pt-6 border-t border-slate-900">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <span class="inline-block h-2 w-2 rounded-full bg-violet-500"></span>
                Jelajahi Katalog Kursus
            </h2>
            
            <!-- Filters -->
            <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap gap-2">
                <input type="text" name="search" placeholder="Cari judul kelas..." value="{{ request('search') }}"
                    class="rounded-lg border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs text-slate-100 placeholder-slate-500 outline-none focus:border-indigo-500">
                <select name="category_id" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs text-slate-100 outline-none focus:border-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs px-3 py-1.5 border border-slate-700">Filter</button>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($catalogCourses as $course)
            <div class="rounded-2xl border border-slate-850 bg-slate-900/30 p-6 flex flex-col justify-between hover:border-slate-800 transition duration-200">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-slate-400 bg-slate-850 px-2 py-0.5 rounded">
                            {{ $course->category->name ?? 'Kategori' }}
                        </span>
                        <span class="text-xs text-slate-400 capitalize">
                            Level: {{ $course->level }}
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2 leading-snug">{{ $course->title }}</h3>
                    <p class="text-xs text-slate-400 line-clamp-2 mb-4">{{ $course->description }}</p>
                </div>
                <div class="pt-4 border-t border-slate-900 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-slate-500">Instruktur</span>
                        <span class="text-xs font-semibold text-slate-300">{{ $course->instructor->name ?? 'Instructor' }}</span>
                    </div>
                    <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-lg bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700 text-white font-semibold text-xs px-4 py-2 transition duration-200 shadow-md shadow-indigo-500/10">
                            Daftar Kelas
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-full py-8 text-center text-slate-500">
                Tidak ada kursus lain yang ditemukan dalam katalog saat ini.
            </div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $catalogCourses->links() }}
        </div>
    </div>
</div>
@endsection

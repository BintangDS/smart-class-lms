@extends('layouts.app')

@section('title', 'Instructor Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight text-white">Dashboard Instruktur</h1>
        <p class="text-slate-400 mt-1">Buat materi kelas, kelola tugas, dan beri penilaian pada karya siswa.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 backdrop-blur">
            <span class="text-sm font-semibold text-slate-400">Kursus Yang Diajar</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-extrabold text-white">{{ $totalCourses }}</span>
                <span class="text-xs text-indigo-400 font-medium">Kelas Aktif</span>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 backdrop-blur">
            <span class="text-sm font-semibold text-slate-400">Siswa Terdaftar</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-extrabold text-white">{{ $totalStudents }}</span>
                <span class="text-xs text-indigo-400 font-medium">Siswa Terhitung</span>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 backdrop-blur">
            <span class="text-sm font-semibold text-slate-400">Butuh Penilaian</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-extrabold text-amber-400">{{ $submissionsNeedGrading->count() }}</span>
                <span class="text-xs text-amber-500 font-medium">Tugas Murid</span>
            </div>
        </div>
    </div>

    <!-- Kelas Saya -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/20 p-6 backdrop-blur">
        <h3 class="text-lg font-bold text-white mb-4">Kelas Yang Anda Ajar</h3>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($courses as $course)
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 flex flex-col justify-between hover:border-indigo-500/40 transition duration-200">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-indigo-400 uppercase tracking-wider">{{ $course->category->name ?? 'Kategori' }}</span>
                        <span class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase {{ $course->status === 'published' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                            {{ $course->status }}
                        </span>
                    </div>
                    <h4 class="text-base font-bold text-white leading-tight mb-2">{{ $course->title }}</h4>
                    <p class="text-xs text-slate-400 line-clamp-2 mb-4">{{ $course->description }}</p>
                </div>
                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-medium">👥 {{ $course->enrollments_count }} Murid</span>
                    <a href="{{ route('classroom', $course->id) }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition">
                        Masuk Ruang Kelas &rarr;
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-8 text-center text-slate-500">
                Belum ada kelas yang Anda ajar. Silakan buat melalui API atau seed data.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Submissions Penilaian -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/20 p-6 backdrop-blur">
        <h3 class="text-lg font-bold text-white mb-4">Tugas Siswa Menunggu Penilaian</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/80 text-xs uppercase text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">Kelas / Tugas</th>
                        <th class="px-4 py-3">File Jawaban</th>
                        <th class="px-4 py-3">Nilai (Maks)</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($submissionsNeedGrading as $sub)
                    <tr class="hover:bg-slate-900/30 transition">
                        <td class="px-4 py-4">
                            <span class="font-medium text-white block">{{ $sub->user->name }}</span>
                            <span class="text-xs text-slate-400">{{ $sub->user->email }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-slate-400 block text-xs">{{ $sub->assignment->module->course->title }}</span>
                            <span class="font-medium text-slate-200">{{ $sub->assignment->title }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 underline">
                                Lihat Berkas &rarr;
                            </a>
                        </td>
                        <td class="px-4 py-4 text-xs font-semibold text-slate-400">
                            / {{ $sub->assignment->max_score }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            <!-- Inline Form Grade -->
                            <form action="{{ route('submissions.grade', $sub->id) }}" method="POST" class="flex items-center justify-end gap-2">
                                @csrf
                                @method('PUT')
                                <input type="number" name="score" min="0" max="{{ $sub->assignment->max_score }}" required placeholder="Skor"
                                    class="w-16 rounded-lg border border-slate-800 bg-slate-950 px-2 py-1 text-center text-sm text-slate-100 outline-none focus:border-indigo-500">
                                <input type="text" name="feedback" placeholder="Feedback singkat"
                                    class="w-40 rounded-lg border border-slate-800 bg-slate-950 px-2 py-1 text-xs text-slate-100 outline-none focus:border-indigo-500">
                                <button type="submit" class="rounded bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs px-2.5 py-1.5 transition">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500">Semua tugas murid telah dinilai! Kerja bagus.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

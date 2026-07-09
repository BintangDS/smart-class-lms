@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight text-white">Dashboard Administrator</h1>
        <p class="text-slate-400 mt-1">Kelola pengguna platform, pantau aktivitas kursus, dan lihat statistik global.</p>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- Stat 1 -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 backdrop-blur">
            <span class="text-sm font-semibold text-slate-400">Total Pengguna</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-extrabold text-white">{{ $totalUsers }}</span>
                <span class="text-xs text-indigo-400 font-medium">Orang</span>
            </div>
        </div>
        <!-- Stat 2 -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 backdrop-blur">
            <span class="text-sm font-semibold text-slate-400">Total Kursus</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-extrabold text-white">{{ $totalCourses }}</span>
                <span class="text-xs text-indigo-400 font-medium">Kelas</span>
            </div>
        </div>
        <!-- Stat 3 -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6 backdrop-blur">
            <span class="text-sm font-semibold text-slate-400">Rerata Penyelesaian Kelas</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-extrabold text-white">{{ number_format($avgCompletion, 1) }}%</span>
                <span class="text-xs text-emerald-400 font-medium">Completion Rate</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Users Management Table -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/20 p-6 backdrop-blur">
            <h3 class="text-lg font-bold text-white mb-4">Pengguna Baru</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/80 text-xs uppercase text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Peran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-900/30 transition">
                            <td class="px-4 py-3.5 font-medium text-white">{{ $user->name }}</td>
                            <td class="px-4 py-3.5 text-slate-400">{{ $user->email }}</td>
                            <td class="px-4 py-3.5">
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold capitalize border 
                                    {{ $user->role === 'admin' ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : 
                                       ($user->role === 'instructor' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 
                                       'bg-indigo-500/10 text-indigo-400 border-indigo-500/20') }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Courses Management Table -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/20 p-6 backdrop-blur">
            <h3 class="text-lg font-bold text-white mb-4">Daftar Kursus</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/80 text-xs uppercase text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Judul Kelas</th>
                            <th class="px-4 py-3">Instruktur</th>
                            <th class="px-4 py-3">Level</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($courses as $course)
                        <tr class="hover:bg-slate-900/30 transition">
                            <td class="px-4 py-3.5 font-medium text-white">{{ $course->title }}</td>
                            <td class="px-4 py-3.5 text-slate-400">{{ $course->instructor->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3.5 capitalize text-xs text-indigo-400 font-semibold">{{ $course->level }}</td>
                            <td class="px-4 py-3.5">
                                <span class="rounded px-2 py-0.5 text-xs font-bold uppercase 
                                    {{ $course->status === 'published' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                                    {{ $course->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

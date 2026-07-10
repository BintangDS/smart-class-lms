@extends('layouts.app')

@section('title', 'Buat Kursus Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="rounded-lg bg-slate-900 hover:bg-slate-800 p-2 border border-slate-800 text-slate-400 hover:text-white transition text-xs">
            &larr; Batal
        </a>
        <h1 class="text-2xl font-extrabold text-white">Buat Kursus Baru</h1>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-8 shadow-2xl backdrop-blur-xl">
        <form action="{{ route('courses.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-slate-300">Judul Kursus</label>
                <input type="text" id="title" name="title" required value="{{ old('title') }}" placeholder="Contoh: Belajar Laravel 11 untuk Pemula"
                    class="mt-1 block w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-slate-100 placeholder-slate-500 shadow-inner outline-none transition duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm">
                @error('title')
                    <p class="mt-2 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-slate-300">Kategori</label>
                <select id="category_id" name="category_id" required
                    class="mt-1 block w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-slate-100 placeholder-slate-500 shadow-inner outline-none transition duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-2 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Level -->
            <div>
                <label for="level" class="block text-sm font-medium text-slate-300">Tingkat Kesulitan (Level)</label>
                <select id="level" name="level" required
                    class="mt-1 block w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-slate-100 placeholder-slate-500 shadow-inner outline-none transition duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm">
                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Pemula (Beginner)</option>
                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Menengah (Intermediate)</option>
                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Mahir (Advanced)</option>
                </select>
                @error('level')
                    <p class="mt-2 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-300">Deskripsi Lengkap Kursus</label>
                <textarea id="description" name="description" rows="5" required placeholder="Tulis deskripsi detail materi kelas di sini..."
                    class="mt-1 block w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-slate-100 placeholder-slate-500 shadow-inner outline-none transition duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-2 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-slate-300">Status Publikasi</label>
                <select id="status" name="status" required
                    class="mt-1 block w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-slate-100 placeholder-slate-500 shadow-inner outline-none transition duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm">
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Sembunyikan dari Katalog)</option>
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Tampilkan di Katalog Siswa)</option>
                </select>
                @error('status')
                    <p class="mt-2 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 px-4 py-3.5 text-sm font-semibold text-white transition-all duration-200 hover:from-indigo-600 hover:to-violet-700 hover:shadow-lg hover:shadow-indigo-500/20 active:scale-98">
                Simpan & Terbitkan Kelas
            </button>
        </form>
    </div>
</div>
@endsection

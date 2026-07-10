@extends('layouts.app')

@section('title', 'Kelola Pertanyaan Kuis')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('classroom', $course->id) }}" class="rounded-lg bg-slate-900 hover:bg-slate-800 p-2 border border-slate-800 text-slate-400 hover:text-white transition text-xs">
                &larr; Kembali ke Kelas
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-white">Kelola Pertanyaan Kuis</h1>
                <p class="text-slate-400 text-xs mt-0.5">Kuis: {{ $quiz->title }} | Passing Score: {{ $quiz->passing_score }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-emerald-500/10 bg-emerald-500/5 p-4 text-xs font-semibold text-emerald-400">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <!-- List Pertanyaan (7 cols) -->
        <div class="lg:col-span-7 space-y-4">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="inline-block h-2 w-2 rounded-full bg-indigo-500"></span>
                Daftar Pertanyaan Saat Ini ({{ $quiz->questions->count() }})
            </h2>

            <div class="space-y-4">
                @forelse($quiz->questions as $index => $question)
                <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-5 space-y-3 relative">
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-xs font-semibold text-slate-500 font-mono">Soal #{{ $index + 1 }}</span>
                        <form action="{{ route('quizzes.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 px-2 py-0.5 text-[10px] font-semibold border border-rose-500/20 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                    <p class="text-sm font-semibold text-white leading-relaxed">{!! nl2br(e($question->question_text)) !!}</p>
                    
                    <div class="grid grid-cols-1 gap-2 pt-2 border-t border-slate-900/60">
                        @foreach($question->options as $optIndex => $option)
                        <div class="flex items-center gap-2 rounded-lg border p-2 text-xs transition
                            {{ $option->is_correct ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400 font-medium' : 'border-slate-800 bg-slate-950/40 text-slate-400' }}">
                            <span class="font-bold font-mono">
                                {{ chr(65 + $optIndex) }}.
                            </span>
                            <span>{{ $option->option_text }}</span>
                            @if($option->is_correct)
                                <span class="ml-auto text-[9px] font-bold tracking-wider text-emerald-400">BENAR</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="rounded-xl border border-slate-800/80 bg-slate-900/30 p-8 text-center text-slate-500 text-xs">
                    Belum ada pertanyaan di kuis ini. Silakan buat menggunakan form di samping.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Form Tambah Pertanyaan (5 cols) -->
        <div class="lg:col-span-5">
            <div class="sticky top-6 rounded-xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl backdrop-blur-md space-y-4">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-violet-500"></span>
                    Tambah Pertanyaan Baru
                </h2>

                <form action="{{ route('quizzes.questions.store', $quiz->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Soal -->
                    <div>
                        <label for="question_text" class="block text-xs font-semibold text-slate-400 mb-1">Teks Pertanyaan</label>
                        <textarea id="question_text" name="question_text" rows="3" required placeholder="Tulis soal kuis di sini..."
                            class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-slate-100 placeholder-slate-600 outline-none focus:border-indigo-500 text-xs"></textarea>
                    </div>

                    <!-- Opsi Jawaban -->
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-400">Pilihan Jawaban & Kunci Jawaban</label>
                        
                        <!-- Pilihan A -->
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" value="0" required checked class="text-indigo-600 focus:ring-indigo-500 bg-slate-950 border-slate-800">
                            <span class="text-xs font-bold text-slate-400 font-mono">A</span>
                            <input type="text" name="options[]" required placeholder="Jawaban A..."
                                class="flex-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-1.5 text-slate-100 placeholder-slate-600 outline-none focus:border-indigo-500 text-xs">
                        </div>

                        <!-- Pilihan B -->
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" value="1" required class="text-indigo-600 focus:ring-indigo-500 bg-slate-950 border-slate-800">
                            <span class="text-xs font-bold text-slate-400 font-mono">B</span>
                            <input type="text" name="options[]" required placeholder="Jawaban B..."
                                class="flex-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-1.5 text-slate-100 placeholder-slate-600 outline-none focus:border-indigo-500 text-xs">
                        </div>

                        <!-- Pilihan C -->
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" value="2" required class="text-indigo-600 focus:ring-indigo-500 bg-slate-950 border-slate-800">
                            <span class="text-xs font-bold text-slate-400 font-mono">C</span>
                            <input type="text" name="options[]" required placeholder="Jawaban C..."
                                class="flex-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-1.5 text-slate-100 placeholder-slate-600 outline-none focus:border-indigo-500 text-xs">
                        </div>

                        <!-- Pilihan D -->
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" value="3" required class="text-indigo-600 focus:ring-indigo-500 bg-slate-950 border-slate-800">
                            <span class="text-xs font-bold text-slate-400 font-mono">D</span>
                            <input type="text" name="options[]" required placeholder="Jawaban D..."
                                class="flex-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-1.5 text-slate-100 placeholder-slate-600 outline-none focus:border-indigo-500 text-xs">
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 px-4 py-2.5 text-xs font-semibold text-white transition-all hover:from-indigo-600 hover:to-violet-700">
                        Tambah Pertanyaan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

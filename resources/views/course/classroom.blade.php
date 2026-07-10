@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="space-y-6">
    <!-- Header Back button and course title -->
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="rounded-lg bg-slate-900 hover:bg-slate-800 p-2 border border-slate-800 text-slate-400 hover:text-white transition">
            &larr; Kembali ke Dashboard
        </a>
        <div>
            <span class="text-xs text-indigo-400 font-semibold uppercase">{{ $course->category->name }}</span>
            <h1 class="text-2xl font-extrabold text-white leading-tight">{{ $course->title }}</h1>
        </div>
    </div>

    <!-- Main Classroom Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <!-- Sidebar Navigation (4 cols) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-4 backdrop-blur space-y-4">
                <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">Kurikulum Kelas</h3>
                
                <div class="space-y-3">
                    @forelse($course->modules as $module)
                    <div class="rounded-xl border border-slate-800/80 bg-slate-950/40 p-3 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-300">{{ $module->title }}</h4>
                            @if(Auth::user()->role === 'instructor' && $course->instructor_id === Auth::id())
                            <form action="{{ route('modules.destroy', $module->id) }}" method="POST" onsubmit="return confirm('Hapus modul beserta seluruh materinya?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[9px] text-rose-400 hover:text-rose-300 transition">Hapus</button>
                            </form>
                            @endif
                        </div>
                        
                        <div class="space-y-1">
                            @forelse($module->lessons as $les)
                            @php
                                $isCompleted = in_class_complete($les->id, $completedLessonIds);
                                $isActive = $activeLesson && $activeLesson->id === $les->id;
                            @endphp
                            <div class="flex items-center justify-between rounded-lg p-1.5 text-xs transition duration-155 
                                      {{ $isActive ? 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' : 'hover:bg-slate-900/60 text-slate-400 hover:text-slate-200' }}">
                                <a href="{{ route('classroom', ['courseId' => $course->id, 'lesson_id' => $les->id]) }}" class="truncate flex-1 pr-2">
                                    📖 {{ $les->title }}
                                </a>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($isCompleted)
                                        <span class="rounded bg-emerald-500/10 px-1.5 py-0.5 text-[9px] font-bold text-emerald-400">SELESAI</span>
                                    @endif
                                    @if(Auth::user()->role === 'instructor' && $course->instructor_id === Auth::id())
                                    <form action="{{ route('lessons.destroy', $les->id) }}" method="POST" onsubmit="return confirm('Hapus materi ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[9px] text-rose-500/60 hover:text-rose-400 font-bold px-1">&times;</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-[10px] text-slate-600 pl-2">Materi belum tersedia</p>
                            @endforelse
                        </div>

                        <!-- Form Tambah Lesson (Instructor Only) -->
                        @if(Auth::user()->role === 'instructor' && $course->instructor_id === Auth::id())
                        <form action="{{ route('lessons.store', $module->id) }}" method="POST" class="mt-2 pt-2 border-t border-slate-900 space-y-1.5">
                            @csrf
                            <input type="text" name="title" required placeholder="Judul materi baru..." class="w-full rounded bg-slate-950 px-2 py-1 text-[10px] text-slate-100 placeholder-slate-600 outline-none border border-slate-900 focus:border-indigo-500">
                            <input type="hidden" name="content_type" value="text">
                            <input type="hidden" name="order" value="{{ $module->lessons->count() + 1 }}">
                            <textarea name="content" required placeholder="Isi materi..." rows="2" class="w-full rounded bg-slate-950 px-2 py-1 text-[10px] text-slate-100 placeholder-slate-600 outline-none border border-slate-900 focus:border-indigo-500"></textarea>
                            <button type="submit" class="w-full rounded bg-indigo-600/80 hover:bg-indigo-600 text-white text-[9px] font-semibold py-1 transition">+ Tambah Materi</button>
                        </form>
                        @endif

                        <!-- Quiz / Assignment info inside module -->
                        @if($module->quizzes->count() > 0 || $module->assignments->count() > 0)
                        <div class="mt-2 pt-2 border-t border-slate-900 space-y-1">
                            @foreach($module->quizzes as $q)
                            @php $att = $attempts->get($q->id); @endphp
                            <div class="flex items-center justify-between text-[10px] text-slate-400 px-2 py-1 bg-slate-900/20 rounded">
                                <span>📝 Kuis: {{ $q->title }}</span>
                                @if($att)
                                    <span class="font-bold text-indigo-400">Skor: {{ $att->score }}</span>
                                @else
                                    <span class="text-amber-500">Belum Ujian</span>
                                @endif
                            </div>
                            @endforeach

                            @foreach($module->assignments as $a)
                            @php $sub = $submissions->get($a->id); @endphp
                            <div class="flex items-center justify-between text-[10px] text-slate-400 px-2 py-1 bg-slate-900/20 rounded mt-1">
                                <span>📁 Tugas: {{ $a->title }}</span>
                                @if($sub)
                                    @if($sub->score !== null)
                                        <span class="font-bold text-emerald-400">Nilai: {{ $sub->score }}</span>
                                    @else
                                        <span class="text-indigo-400">Dikumpul</span>
                                    @endif
                                @else
                                    <span class="text-amber-500">Belum Kumpul</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @empty
                    <p class="text-xs text-slate-500">Modul belum dibuat.</p>
                    @endforelse
                </div>

                <!-- Form Tambah Modul (Instructor Only) -->
                @if(Auth::user()->role === 'instructor' && $course->instructor_id === Auth::id())
                <form action="{{ route('modules.store', $course->id) }}" method="POST" class="mt-4 pt-3 border-t border-slate-800 space-y-2">
                    @csrf
                    <input type="text" name="title" required placeholder="Judul modul baru..." class="w-full rounded-xl bg-slate-950 px-3 py-2 text-xs text-slate-100 placeholder-slate-500 outline-none border border-slate-900 focus:border-indigo-500">
                    <input type="hidden" name="order" value="{{ $course->modules->count() + 1 }}">
                    <button type="submit" class="w-full rounded-xl bg-slate-850 hover:bg-slate-800 text-indigo-400 text-xs font-semibold py-2 transition border border-indigo-500/10">+ Tambah Modul Baru</button>
                </form>
                @endif
            </div>
        </div>

        <!-- Material and Interactions (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            @if($activeLesson)
            <!-- Active Lesson Details -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/30 p-6 backdrop-blur space-y-4">
                <div class="flex items-center justify-between">
                    <span class="rounded bg-indigo-500/10 px-2.5 py-1 text-xs font-semibold text-indigo-400 border border-indigo-500/20 capitalize">
                        Materi: {{ $activeLesson->content_type }}
                    </span>
                    @if($enrollment && in_array($activeLesson->id, $completedLessonIds))
                        <span class="text-xs text-emerald-400 font-semibold flex items-center gap-1">
                            ✓ Anda sudah menyelesaikan materi ini
                        </span>
                    @endif
                </div>
                
                <h2 class="text-xl font-bold text-white">{{ $activeLesson->title }}</h2>
                
                <!-- Content box -->
                <div class="prose prose-invert max-w-none text-slate-300 bg-slate-950/40 rounded-xl p-5 border border-slate-900 leading-relaxed text-sm">
                    {!! nl2br(e($activeLesson->content)) !!}
                </div>

                <!-- Complete Button -->
                @if($enrollment && !in_array($activeLesson->id, $completedLessonIds))
                <form action="{{ route('lessons.complete', $activeLesson->id) }}" method="POST" class="pt-2">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm px-6 py-3 transition duration-200 shadow-lg shadow-emerald-600/10">
                        Tandai Selesai & Lanjutkan &rarr;
                    </button>
                </form>
                @endif
            </div>

            <!-- Quiz & Assignment Interactions for current Active Lesson's Module -->
            @php
                $activeModule = $activeLesson->module;
            @endphp

            @if($activeModule)
                <!-- Quizzes in current Module -->
                @foreach($activeModule->quizzes as $quiz)
                <div class="rounded-2xl border border-slate-800 bg-slate-900/30 p-6 backdrop-blur space-y-4">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>📝</span> Kuis Modul: {{ $quiz->title }}
                    </h3>
                    
                    @php $attempt = $attempts->get($quiz->id); @endphp
                    @if($attempt)
                        <div class="rounded-xl bg-slate-950/40 p-4 border border-slate-900 text-sm space-y-1">
                            <p class="text-slate-300">Anda telah menyelesaikan kuis ini.</p>
                            <p class="text-slate-400">Nilai Anda: <span class="font-bold text-indigo-400">{{ $attempt->score }}</span> (Kriteria Kelulusan: {{ $quiz->passing_score }})</p>
                            <p class="text-xs text-slate-500">Dikirim pada: {{ $attempt->submitted_at->toDateTimeString() }}</p>
                        </div>
                    @elseif($enrollment)
                        <!-- Quiz taking form -->
                        <form action="{{ route('quizzes.submit', $quiz->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <p class="text-xs text-slate-400">Pilih satu jawaban yang paling tepat.</p>
                            
                            @foreach($quiz->questions as $index => $question)
                            <div class="p-4 rounded-xl bg-slate-950/20 border border-slate-900 space-y-2">
                                <p class="text-sm font-semibold text-slate-200">{{ $index + 1 }}. {{ $question->question_text }}</p>
                                <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">
                                
                                <div class="space-y-2">
                                    @foreach($question->options as $option)
                                    <label class="flex items-center gap-3 rounded-lg border border-slate-850 bg-slate-950/40 p-3 text-xs text-slate-300 cursor-pointer hover:border-slate-800 transition">
                                        <input type="radio" name="answers[{{ $index }}][option_id]" value="{{ $option->id }}" required
                                            class="text-indigo-600 focus:ring-indigo-500/20">
                                        <span>{{ $option->option_text }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach

                            <button type="submit" class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs px-4 py-2.5 transition">
                                Kirim Jawaban Kuis
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-slate-500">Instruktur dapat melihat kuis di ruang kelas.</p>
                    @endif
                </div>
                @endforeach

                <!-- Assignments in current Module -->
                @foreach($activeModule->assignments as $assignment)
                <div class="rounded-2xl border border-slate-800 bg-slate-900/30 p-6 backdrop-blur space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <span>📁</span> Tugas Modul: {{ $assignment->title }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">Batas Waktu: {{ $assignment->due_date }} | Skor Maksimal: {{ $assignment->max_score }}</p>
                        </div>
                    </div>

                    <div class="text-xs text-slate-300 bg-slate-950/40 border border-slate-900 rounded-lg p-3">
                        {!! nl2br(e($assignment->description)) !!}
                    </div>

                    @php $submission = $submissions->get($assignment->id); @endphp
                    @if($submission)
                        <div class="rounded-xl bg-slate-950/40 p-4 border border-slate-900 text-xs space-y-2">
                            <p class="font-medium text-emerald-400">✓ Anda telah mengumpulkan tugas ini.</p>
                            <p class="text-slate-400">Berkas dikirim: 
                                <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 underline font-semibold">
                                    Unduh File Jawaban
                                </a>
                            </p>
                            
                            @if($submission->score !== null)
                                <div class="pt-2 border-t border-slate-900">
                                    <p class="text-slate-200">Nilai: <span class="font-bold text-emerald-400">{{ $submission->score }}</span> / {{ $assignment->max_score }}</p>
                                    @if($submission->feedback)
                                        <p class="text-slate-400 mt-1 italic bg-slate-900/30 p-2 rounded border border-slate-850">Feedback: "{{ $submission->feedback }}"</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-amber-500 italic">Menunggu penilaian dari instruktur.</p>
                            @endif
                        </div>
                    @elseif($enrollment)
                        @if(now()->greaterThan($assignment->due_date))
                            <p class="text-xs text-rose-400">Batas waktu pengerjaan tugas ini sudah habis.</p>
                        @else
                            <form action="{{ route('assignments.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 mb-2">Unggah Jawaban Tugas (PDF, ZIP, DOCX, JPG, PNG maks 20MB)</label>
                                    <input type="file" name="file" required
                                        class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-indigo-400 hover:file:bg-slate-750">
                                </div>
                                <button type="submit" class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs px-4 py-2 transition">
                                    Kumpulkan Tugas
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
                @endforeach
            @endif

            @else
            <div class="rounded-2xl border border-slate-800 bg-slate-900/20 p-8 text-center text-slate-500">
                Belum ada materi pelajaran dalam kursus ini.
            </div>
            @endif
        </div>
    </div>
</div>

@php
    function in_class_complete($lesId, $completedIds) {
        return in_array($lesId, $completedIds);
    }
@endphp
@endsection

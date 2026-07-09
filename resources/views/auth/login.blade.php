<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fun Teacher Private LMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Glowing background elements -->
    <div class="absolute top-1/4 left-1/4 -z-10 h-72 w-72 rounded-full bg-indigo-600/10 blur-3xl"></div>
    <div class="absolute bottom-1/4 right-1/4 -z-10 h-96 w-96 rounded-full bg-violet-600/10 blur-3xl"></div>

    <div class="w-full max-w-md space-y-8">
        <!-- Logo & Header -->
        <div class="text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-600 shadow-xl shadow-indigo-500/20">
                <span class="text-2xl font-bold text-white">F</span>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Fun Teacher LMS
            </h2>
            <p class="mt-2 text-sm text-slate-400">
                Portal LMS Portofolio Laravel & API
            </p>
        </div>

        <!-- Glassmorphism Login Card -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-8 shadow-2xl backdrop-blur-xl">
            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300">Alamat Email</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="block w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-slate-100 placeholder-slate-500 shadow-inner outline-none transition duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm">
                    </div>
                    @error('email')
                        <p class="mt-2 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300">Kata Sandi</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="block w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-slate-100 placeholder-slate-500 shadow-inner outline-none transition duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm">
                    </div>
                    @error('password')
                        <p class="mt-2 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-slate-800 bg-slate-950 text-indigo-600 focus:ring-indigo-500/20">
                        <label for="remember" class="ml-2 block text-sm text-slate-400">Ingat Saya</label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative flex w-full justify-center rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 px-4 py-3 text-sm font-semibold text-white transition-all duration-200 hover:from-indigo-600 hover:to-violet-700 hover:shadow-lg hover:shadow-indigo-500/20 active:scale-98">
                        Masuk Ke Dashboard
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Demo Credentials Box -->
        <div class="rounded-xl border border-indigo-500/10 bg-indigo-950/20 p-5 backdrop-blur-md">
            <h4 class="text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-3 flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Akun Pengujian Demo (Seeder)
            </h4>
            <div class="space-y-2 text-xs text-slate-400">
                <div class="flex justify-between items-center bg-slate-950/40 p-2 rounded-lg border border-slate-900">
                    <div>
                        <span class="font-medium text-slate-200 block">Siswa (Student)</span>
                        <span>student@example.com</span>
                    </div>
                    <button onclick="fillForm('student@example.com')" class="text-indigo-400 hover:text-indigo-300 font-semibold px-2 py-1 rounded hover:bg-indigo-500/10 transition">Gunakan</button>
                </div>
                <div class="flex justify-between items-center bg-slate-950/40 p-2 rounded-lg border border-slate-900">
                    <div>
                        <span class="font-medium text-slate-200 block">Instruktur (Instructor)</span>
                        <span>instructor@example.com</span>
                    </div>
                    <button onclick="fillForm('instructor@example.com')" class="text-indigo-400 hover:text-indigo-300 font-semibold px-2 py-1 rounded hover:bg-indigo-500/10 transition">Gunakan</button>
                </div>
                <div class="flex justify-between items-center bg-slate-950/40 p-2 rounded-lg border border-slate-900">
                    <div>
                        <span class="font-medium text-slate-200 block">Admin (Administrator)</span>
                        <span>admin@example.com</span>
                    </div>
                    <button onclick="fillForm('admin@example.com')" class="text-indigo-400 hover:text-indigo-300 font-semibold px-2 py-1 rounded hover:bg-indigo-500/10 transition">Gunakan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillForm(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>

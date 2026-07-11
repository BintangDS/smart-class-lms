<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <title>Login - Smart Class LMS</title>
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
    <!-- Theme Toggle Button (Top Right) -->
    <div class="absolute top-6 right-6 z-50">
        <button id="theme-toggle" type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-800/60 hover:text-slate-100 focus:outline-none transition duration-200 border border-transparent hover:border-slate-700">
            <!-- Dark Icon (Moon) -->
            <svg id="theme-toggle-dark-icon" class="hidden h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            <!-- Light Icon (Sun) -->
            <svg id="theme-toggle-light-icon" class="hidden h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>

    <!-- Floating Orbs Background -->
    <div class="orbs-wrapper">
        <div class="orb orb-indigo"></div>
        <div class="orb orb-violet"></div>
        <div class="orb orb-emerald"></div>
    </div>

    <div class="w-full max-w-md space-y-8">
        <!-- Logo & Header -->
        <div class="text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-600 shadow-xl shadow-indigo-500/20">
                <span class="text-2xl font-bold text-white">S</span>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Smart Class LMS
            </h2>
            <p class="mt-2 text-sm text-slate-400">
                Portal LMS Portofolio Laravel & API
            </p>
        </div>

        <!-- Glassmorphism Login Card -->
        <div class="rounded-2xl glass-card p-8 shadow-2xl">
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
        <div class="rounded-xl glass-credentials p-5">
            <h4 class="text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-3 flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Akun Pengujian Demo (Seeder)
            </h4>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center glass-row p-3 rounded-xl">
                    <div>
                        <span class="font-medium text-slate-200 block">Siswa (Student)</span>
                        <span class="text-slate-400">student@example.com</span>
                    </div>
                    <button onclick="fillForm('student@example.com')" class="text-indigo-400 hover:text-indigo-300 font-semibold px-2 py-1 rounded hover:bg-indigo-500/10 transition">Gunakan</button>
                </div>
                <div class="flex justify-between items-center glass-row p-3 rounded-xl">
                    <div>
                        <span class="font-medium text-slate-200 block">Instruktur (Instructor)</span>
                        <span class="text-slate-400">instructor@example.com</span>
                    </div>
                    <button onclick="fillForm('instructor@example.com')" class="text-indigo-400 hover:text-indigo-300 font-semibold px-2 py-1 rounded hover:bg-indigo-500/10 transition">Gunakan</button>
                </div>
                <div class="flex justify-between items-center glass-row p-3 rounded-xl">
                    <div>
                        <span class="font-medium text-slate-200 block">Admin (Administrator)</span>
                        <span class="text-slate-400">admin@example.com</span>
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

        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Show proper icon based on theme state
        if (document.documentElement.classList.contains('dark')) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        const themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            // Toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // If set via local storage previously
            if (localStorage.getItem('theme')) {
                if (localStorage.getItem('theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            } else {
                // If not set previously, check document class
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>

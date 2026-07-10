<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan - {{ $certificate->certificate_code }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Montserrat:wght@400;600;800&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #0b1329;
        }
        .certificate-font-title {
            font-family: 'Playfair Display', serif;
        }
        .certificate-font-signature {
            font-family: 'Alex Brush', cursive;
        }
        
        /* CSS Cetak Khusus */
        @media print {
            body {
                background-color: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                background-color: #ffffff !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                height: 100vh !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8">

    <!-- Tombol Aksi (Disembunyikan saat cetak) -->
    <div class="no-print mb-6 flex gap-4">
        <button onclick="window.print()" class="rounded-xl bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 text-slate-950 font-bold text-sm px-6 py-3 transition shadow-lg shadow-amber-500/20 active:scale-95 flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak / Simpan PDF
        </button>
        <a href="/" class="rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-semibold text-sm px-6 py-3 transition border border-slate-800 flex items-center gap-2">
            Kembali ke Dashboard &rarr;
        </a>
    </div>

    <!-- Container Sertifikat (A4 Landscape aspect ratio: ~1.41) -->
    <div class="print-container w-full max-w-5xl aspect-[1.41] rounded-2xl border-[16px] border-double border-amber-600 bg-white p-8 sm:p-16 flex flex-col justify-between shadow-2xl relative overflow-hidden">
        
        <!-- Watermark / Background Ornament -->
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-4 border-amber-500/5 pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full border-4 border-amber-500/5 pointer-events-none"></div>

        <!-- Header -->
        <div class="text-center space-y-2">
            <span class="text-xs font-bold tracking-[0.2em] text-slate-400 uppercase">Certificate of Completion</span>
            <h1 class="certificate-font-title text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-wide">SERTIFIKAT KELULUSAN</h1>
            <div class="h-1 w-24 bg-gradient-to-r from-amber-500 to-yellow-600 mx-auto rounded"></div>
        </div>

        <!-- Body / Content -->
        <div class="text-center space-y-6 my-6">
            <p class="text-xs sm:text-sm text-slate-500 italic">Sertifikat ini diberikan secara terhormat kepada:</p>
            
            <h2 class="certificate-font-signature text-5xl sm:text-7xl text-amber-600 font-normal py-2 leading-none">
                {{ $certificate->user->name }}
            </h2>
            
            <p class="text-xs sm:text-sm text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Atas dedikasi, minat belajar yang tinggi, serta keberhasilan menyelesaikan seluruh kurikulum pembelajaran pada modul kelas tingkat lanjut:
            </p>
            
            <h3 class="certificate-font-title text-xl sm:text-2xl font-bold text-slate-850">
                "{{ $certificate->course->title }}"
            </h3>
        </div>

        <!-- Footer -->
        <div class="grid grid-cols-3 items-end pt-6 border-t border-slate-100 text-center text-xs">
            <!-- Verification -->
            <div class="space-y-1 text-left">
                <span class="text-[9px] font-bold text-slate-400 block uppercase">Verifikasi Keaslian</span>
                <span class="font-mono text-slate-600 font-bold block">{{ $certificate->certificate_code }}</span>
                <span class="text-[9px] text-slate-400 block">Diterbitkan: {{ $certificate->issued_at->toDateString() }}</span>
            </div>

            <!-- Emblem/Seal -->
            <div class="flex justify-center">
                <div class="w-16 h-16 rounded-full border-4 border-amber-500 flex items-center justify-center bg-gradient-to-br from-amber-400 to-yellow-600 shadow-md transform rotate-12">
                    <span class="text-[9px] font-extrabold text-slate-900 tracking-wider">OFFICIAL SEAL</span>
                </div>
            </div>

            <!-- Signature -->
            <div class="space-y-1 text-right">
                <span class="certificate-font-signature text-3xl text-slate-800 block font-normal leading-none pr-4">BintangDS</span>
                <div class="h-0.5 w-32 bg-slate-300 ml-auto rounded"></div>
                <span class="text-[9px] font-bold text-slate-700 block uppercase">{{ $certificate->course->instructor->name ?? 'Instruktur Utama' }}</span>
                <span class="text-[8px] text-slate-400 block">Smart Class LMS</span>
            </div>
        </div>
    </div>

</body>
</html>

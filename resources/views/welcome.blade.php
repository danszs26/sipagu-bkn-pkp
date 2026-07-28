<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Sistem Keuangan') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
            .font-display { font-family: 'Newsreader', serif; }
            .font-mono-label { font-family: 'IBM Plex Mono', monospace; }
        </style>
    </head>
    <!-- <body class="antialiased" style="background: #FFFFFF;">
        <div class="min-h-screen flex flex-col items-center px-6 py-12">
            <div class="flex-1 flex flex-col items-center justify-center w-full">

                <img src="{{ asset('images/logo-bkn.png') }}" alt="Logo BKN" class="w-20 h-20 sm:w-24 sm:h-24 object-contain mb-5">

                <p class="font-mono-label text-[10px] sm:text-xs tracking-widest uppercase mb-3" style="color: #C08A2E;">
                    UPT BKN Pangkalpinang
                </p>

                <h1 class="font-display text-2xl sm:text-4xl text-center leading-tight max-w-lg mb-3" style="color: #16213A;">
                    Aplikasi Kerja UPT BKN Pangkalpinang
                </h1>

                <p class="text-xs sm:text-sm text-gray-500 text-center max-w-md mb-10 leading-relaxed">
                    Pilih aplikasi yang ingin Anda akses.
                </p>

                <div class="flex justify-center w-full">
                    <div class="w-full max-w-xs">

                                    {{-- Kartu: Pagu Anggaran (AKTIF) --}}
                                    <a href="{{ route('login') }}"
                                    class="flex items-center sm:flex-col gap-4 sm:gap-0 text-left sm:text-center bg-white border border-gray-200 rounded-xl p-4 sm:p-6 hover:border-gray-300 transition-colors">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0 rounded-full flex items-center justify-center sm:mb-3.5" style="background: #FBF3E5;">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" style="color: #C08A2E;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <rect x="2.25" y="6.75" width="19.5" height="10.5" rx="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="12" cy="12" r="2.25" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0 sm:flex-none sm:w-full">
                                            <p class="text-sm font-medium mb-0.5 sm:mb-1" style="color: #16213A;">Pagu Anggaran</p>
                                            <p class="font-mono-label text-[10px] tracking-wide uppercase mb-0 sm:mb-4" style="color: #639922;">Aktif</p>
                                        </div>
                                        <span class="hidden sm:inline-flex w-full justify-center py-2.5 rounded-md text-sm font-medium text-white" style="background: #16325C;">
                                            Masuk
                                        </span>
                                        <span class="sm:hidden flex-shrink-0 px-3 py-1.5 rounded-md text-xs font-medium text-white" style="background: #16325C;">
                                            Masuk
                                        </span>
                                    </a>
                </div>
            </div>
                <!-- 
                                    {{-- Kartu: IKM (SEGERA HADIR) --}}
                                    <div class="flex items-center sm:flex-col gap-4 sm:gap-0 text-left sm:text-center bg-white border border-gray-200 rounded-xl p-4 sm:p-6 opacity-90">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0 rounded-full flex items-center justify-center sm:mb-3.5" style="background: #F1EFE8;">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" style="color: #888780;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0 sm:flex-none sm:w-full">
                                            <p class="text-sm font-medium mb-0.5 sm:mb-1" style="color: #16213A;">IKM</p>
                                            <p class="font-mono-label text-[10px] tracking-wide uppercase mb-0 sm:mb-4" style="color: #888780;">Segera hadir</p>
                                        </div>
                                        <span class="hidden sm:inline-flex w-full justify-center py-2.5 rounded-md text-sm font-medium border border-gray-200" style="background: #F7F6F2; color: #A9A79D;">
                                            Segera hadir
                                        </span>
                                        <span class="sm:hidden flex-shrink-0 px-3 py-1.5 rounded-md text-xs font-medium border border-gray-200" style="background: #F7F6F2; color: #A9A79D;">
                                            Segera hadir
                                        </span>
                                    </div>

                </div>
            </div> -->

            <!-- <p class="font-mono-label text-[10px] text-gray-400 mt-auto pt-10">
                &copy; {{ date('Y') }} UPT BKN Pangkalpinang
            </p>
        </div>
    </body> -->
    <body class="antialiased" style="background: #FFFFFF;">
    <div class="min-h-screen flex flex-col items-center justify-center px-6 py-12">

        <img src="{{ asset('images/logo-bkn.png') }}" alt="Logo BKN" class="w-20 h-20 sm:w-24 sm:h-24 object-contain mb-5">

        <p class="font-mono-label text-[10px] sm:text-xs tracking-widest uppercase mb-3" style="color: #C08A2E;">
            UPT BKN Pangkalpinang
        </p>

        <h1 class="font-display text-2xl sm:text-4xl text-center leading-tight max-w-lg mb-3" style="color: #16213A;">
            Aplikasi Kerja UPT BKN Pangkalpinang
        </h1>

        <p class="text-xs sm:text-sm text-gray-500 text-center max-w-md mb-10 leading-relaxed">
            Pilih aplikasi yang ingin Anda akses.
        </p>

        <div class="w-full max-w-xs">
            <a href="{{ route('login') }}"
               class="flex items-center sm:flex-col gap-4 sm:gap-0 text-left sm:text-center bg-white border border-gray-200 rounded-xl p-4 sm:p-6 hover:border-gray-300 transition-colors">
                <div class="w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0 rounded-full flex items-center justify-center sm:mb-3.5" style="background: #FBF3E5;">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" style="color: #C08A2E;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <rect x="2.25" y="6.75" width="19.5" height="10.5" rx="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="2.25" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 sm:flex-none sm:w-full">
                    <p class="text-sm font-medium mb-0.5 sm:mb-1" style="color: #16213A;">Pagu Anggaran</p>
                    <p class="font-mono-label text-[10px] tracking-wide uppercase mb-0 sm:mb-4" style="color: #639922;">Aktif</p>
                </div>
                <span class="hidden sm:inline-flex w-full justify-center py-2.5 rounded-md text-sm font-medium text-white" style="background: #16325C;">
                    Masuk
                </span>
                <span class="sm:hidden flex-shrink-0 px-3 py-1.5 rounded-md text-xs font-medium text-white" style="background: #16325C;">
                    Masuk
                </span>
            </a>
        </div>

        <p class="font-mono-label text-[10px] text-gray-400 mt-10">
            &copy; {{ date('Y') }} UPT BKN Pangkalpinang
        </p>
    </div>
</body>
</html> 

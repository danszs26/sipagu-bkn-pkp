<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Sistem') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-bkn.png') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
            .font-display { font-family: 'Newsreader', serif; }
            .font-display-alt { font-family: 'Fraunces', serif; font-style: italic; }
            .font-mono-label { font-family: 'IBM Plex Mono', monospace; }
        </style>
    </head>
    <body class="antialiased">
        @php
            $eyebrow = 'UPT BKN Pangkalpinang';
            $headline = 'Sistem Pencatatan Keuangan & Pagu Anggaran';
            $tagline = 'Satu pintu untuk mencatat, menelusuri, dan mempertanggungjawabkan setiap rupiah yang keluar dari pagu anggaran yang berjalan.';
            $headlineFontClass = 'font-display';

            $rows = [ ['01','Belanja Keperluan Perkantoran', 68], ['02','Belanja Langganan Daya & Jasa', 92], ['03','Pengelolaan Arsip & Persuratan', 40], ['04','Perawatan Gedung Kantor', 55] ];
        @endphp

        <div class="min-h-screen flex flex-col lg:flex-row">

            {{-- Panel kiri: identitas & elemen ledger --}}
            <div class="relative lg:w-[44%] overflow-hidden border-b lg:border-b-0 lg:border-r border-gray-200"
                 style="background: #FDFDFC;">

                <div class="relative z-10 flex flex-col justify-between h-full px-8 py-10 sm:px-12 sm:py-14 min-h-[280px] lg:min-h-screen">

                    <div>
                        <div class="flex items-center gap-4 mb-8">
                            <img src="{{ asset('images/logo-bkn.png') }}" alt="Logo BKN" class="w-12 h-12 object-contain">
                            <span class="font-mono-label text-xs tracking-widest uppercase" style="color: #C08A2E;">
                                {{ $eyebrow }}
                            </span>
                        </div>

                        <h1 class="{{ $headlineFontClass }} text-3xl sm:text-4xl leading-tight mb-4 max-w-sm" style="color: #16213A;">
                            {{ $headline }}
                        </h1>
                        <p class="text-sm text-gray-500 max-w-xs leading-relaxed hidden sm:block">
                            {{ $tagline }}
                        </p>
                    </div>

                    <div class="hidden lg:block mt-12">
                        <div class="space-y-4">
                            @foreach ($rows as [$no, $label, $width])
                                <div>
                                    <div class="flex justify-between items-baseline mb-1.5">
                                        <span class="font-mono-label text-[11px] text-gray-400">{{ $no }} — {{ $label }}</span>
                                    </div>
                                    <div class="h-px w-full bg-gray-200 relative overflow-hidden">
                                        <div class="h-px absolute inset-y-0 left-0" style="width: {{ $width }}%; background: #C08A2E;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <p class="font-mono-label text-[11px] text-gray-400 mt-10 hidden lg:block">
                        &copy; {{ date('Y') }} UPT BKN Pangkalpinang
                    </p>
                </div>
            </div>

            {{-- Panel kanan: konten (form login/register/dst) --}}
            <div class="flex-1 flex items-center justify-center px-6 py-12 sm:px-12" style="background: #FFFFFF;">
                <div class="w-full max-w-sm">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

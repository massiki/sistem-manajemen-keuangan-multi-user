<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-dvh lg:grid lg:grid-cols-2">

            <!-- Brand panel (desktop) -->
            <aside class="pointer-events-none relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-600 p-12 text-white">
                <div class="pointer-events-none absolute -top-32 -right-32 h-96 w-96 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -bottom-40 -left-24 h-[28rem] w-[28rem] rounded-full bg-white/10"></div>

                <div class="relative flex items-center gap-3">
                    <x-application-logo class="h-11 w-11 fill-current text-white" />
                    <div>
                        <p class="text-lg font-bold leading-tight">{{ config('app.name') }}</p>
                        <p class="text-sm text-emerald-100">Digital Buku Kas</p>
                    </div>
                </div>

                <div class="relative mt-12">
                    <h1 class="text-3xl font-bold leading-tight">
                        Kelola keuangan Anda dengan mudah dan aman.
                    </h1>
                    <p class="mt-4 max-w-md leading-relaxed text-emerald-100/90">
                        Catat uang masuk dan uang keluar, biarkan sistem menghitung saldo Anda, dan pantau kondisi keuangan dari satu tempat.
                    </p>

                    <ul class="mt-10 space-y-4 text-emerald-50/90">
                        <li class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Catat uang masuk &amp; keluar dengan cepat
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Saldo dihitung otomatis dan akurat
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Data keuangan Anda aman dan pribadi
                        </li>
                    </ul>
                </div>

                <p class="relative text-sm text-emerald-100/80">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Dikembangkan di Indonesia.
                </p>
            </aside>

            <!-- Form -->
            <main class="flex min-h-dvh items-center justify-center px-4 py-10 sm:px-6 lg:px-12">
                <div class="w-full max-w-md">
                    <!-- Mobile brand -->
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <x-application-logo class="h-10 w-10 fill-current text-emerald-600" />
                        <div>
                            <p class="text-base font-bold leading-tight">{{ config('app.name') }}</p>
                            <p class="text-sm text-gray-500">Digital Buku Kas</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
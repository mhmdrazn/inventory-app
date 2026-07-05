<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Warehaus') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <script>
            (function () {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (!stored && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-foreground">
        {{-- Theme toggle top-right --}}
        <div class="absolute right-4 top-4 sm:right-6 sm:top-6 z-20">
            <button
                type="button"
                x-data="{
                    dark: document.documentElement.classList.contains('dark'),
                    toggle() {
                        this.dark = !this.dark;
                        document.documentElement.classList.toggle('dark', this.dark);
                        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                    }
                }"
                @click="toggle()"
                :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
                class="inline-flex items-center justify-center h-9 w-9 rounded-md border border-input bg-card hover:bg-accent transition-colors"
            >
                <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m8.485-8.485H21M3 12h1.515m14.02-6.02l-1.06 1.06M6.525 17.475l-1.06 1.06m0-13.06l1.06 1.06m10.95 10.95l1.06 1.06M12 7.5A4.5 4.5 0 1 1 12 16.5a4.5 4.5 0 0 1 0-9z" /></svg>
                <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" /></svg>
            </button>
        </div>

        <div class="grid min-h-screen lg:grid-cols-[1.1fr_1fr]">
            {{-- Left: brand column --}}
            <div class="relative hidden lg:flex flex-col justify-between p-12 overflow-hidden text-white"
                 style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #0b1a3a 100%);">

                {{-- Aurora blobs --}}
                <div class="pointer-events-none absolute -top-32 -left-24 h-96 w-96 rounded-full opacity-40 blur-3xl"
                     style="background: radial-gradient(circle, #3b82f6 0%, transparent 70%);"></div>
                <div class="pointer-events-none absolute -bottom-24 -right-16 h-[28rem] w-[28rem] rounded-full opacity-30 blur-3xl"
                     style="background: radial-gradient(circle, #8b5cf6 0%, transparent 70%);"></div>
                <div class="pointer-events-none absolute top-1/3 right-1/4 h-64 w-64 rounded-full opacity-20 blur-3xl"
                     style="background: radial-gradient(circle, #06b6d4 0%, transparent 70%);"></div>

                {{-- Subtle grid overlay --}}
                <div class="pointer-events-none absolute inset-0 opacity-[0.07]"
                     style="background-image: linear-gradient(rgba(255,255,255,0.6) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.6) 1px, transparent 1px); background-size: 44px 44px;"></div>

                {{-- Top: logo --}}
                <div class="relative flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 backdrop-blur ring-1 ring-white/20">
                        <img src="{{ asset('img/logo.png') }}" alt="Warehaus" class="h-6 w-6">
                    </div>
                    <span class="font-bold text-lg tracking-tight">Warehaus</span>
                </div>

                {{-- Middle: headline + feature list --}}
                <div class="relative space-y-8 max-w-md">
                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium ring-1 ring-white/15 backdrop-blur">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Sistem Manajemen Inventaris
                        </span>
                        <h2 class="text-3xl font-bold leading-tight tracking-tight sm:text-4xl">
                            Kelola inventaris dan peminjaman dengan lebih rapi.
                        </h2>
                        <p class="text-white/70 leading-relaxed">
                            Pantau stok, catat peminjaman, dan hasilkan laporan realtime dalam satu tempat.
                        </p>
                    </div>

                    <ul class="space-y-3.5">
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-md bg-white/10 ring-1 ring-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-medium">Pencatatan stok terpusat</p>
                                <p class="text-xs text-white/60">Setiap produk, lokasi, dan kondisi terlacak.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-md bg-white/10 ring-1 ring-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-medium">Peminjaman aman dari race condition</p>
                                <p class="text-xs text-white/60">Validasi stok terkunci dalam transaksi database.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-md bg-white/10 ring-1 ring-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-medium">Laporan PDF dan Excel siap unduh</p>
                                <p class="text-xs text-white/60">Ekspor riwayat inventaris dan peminjaman kapan saja.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Bottom: footer --}}
                <div class="relative flex items-center justify-between text-xs text-white/50">
                    <span>&copy; {{ date('Y') }} Warehaus</span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Data Anda aman
                    </span>
                </div>
            </div>

            {{-- Right: form column --}}
            <div class="flex flex-col items-center justify-center p-6 sm:p-10">
                {{-- Mobile brand --}}
                <div class="lg:hidden mb-8 flex items-center gap-2">
                    <img src="{{ asset('img/logo.png') }}" alt="Warehaus" class="h-9 w-9">
                    <span class="font-bold text-lg tracking-tight">Warehaus</span>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

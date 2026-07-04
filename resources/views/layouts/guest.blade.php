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
        <div class="absolute right-4 top-4 sm:right-6 sm:top-6">
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

        <div class="grid min-h-screen lg:grid-cols-2">
            {{-- Left: brand column --}}
            <div class="relative hidden lg:flex flex-col justify-between p-10 overflow-hidden text-white" style="background: linear-gradient(135deg, #ff0021 0%, #b3001c 45%, #0a1f44 100%);">
                {{-- Decorative pattern --}}
                <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 25% 20%, rgba(255,255,255,0.2) 0%, transparent 40%), radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 40%);"></div>

                <div class="relative flex items-center gap-2">
                    <img src="{{ asset('img/logo.png') }}" alt="Warehaus" class="h-9 w-9">
                    <span class="font-bold text-lg tracking-tight">Warehaus</span>
                </div>

                <div class="relative space-y-6 max-w-md">
                    <blockquote class="text-2xl font-semibold leading-snug tracking-tight">
                        Kelola inventaris dan peminjaman barang perusahaan dengan lebih terorganisir dan efisien.
                    </blockquote>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-white/15 backdrop-blur flex items-center justify-center text-sm font-semibold">PT</div>
                        <div class="text-sm">
                            <p class="font-medium">PT Telkomsel</p>
                            <p class="text-white/70">Sistem Manajemen Inventaris</p>
                        </div>
                    </div>
                </div>

                <div class="relative text-xs text-white/60">
                    &copy; {{ date('Y') }} PT Telkomsel · Prototype
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

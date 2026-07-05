<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Warehaus &mdash; Sistem Manajemen Inventaris</title>
    <meta name="description" content="Warehaus adalah sistem manajemen inventaris berbasis web untuk mencatat barang, mengelola peminjaman, dan membuat laporan dengan mudah.">

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

    {{-- Top Navigation --}}
    <header class="sticky top-0 z-40 border-b bg-background/80 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">
            <a href="/" class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="Warehaus" class="h-8 w-8">
                <span class="font-bold text-lg tracking-tight">Warehaus</span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm text-muted-foreground">
                <a href="#features" class="hover:text-foreground transition-colors">Fitur</a>
                <a href="#howitworks" class="hover:text-foreground transition-colors">Cara Kerja</a>
                <a href="#about" class="hover:text-foreground transition-colors">Tentang</a>
            </nav>

            <div class="flex items-center gap-2">
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
                    class="inline-flex items-center justify-center h-9 w-9 rounded-md border border-input hover:bg-accent transition-colors"
                    aria-label="Ganti tema"
                >
                    <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m8.485-8.485H21M3 12h1.515m14.02-6.02l-1.06 1.06M6.525 17.475l-1.06 1.06m0-13.06l1.06 1.06m10.95 10.95l1.06 1.06M12 7.5A4.5 4.5 0 1 1 12 16.5a4.5 4.5 0 0 1 0-9z" /></svg>
                    <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" /></svg>
                </button>

                @auth
                    <x-ui.button :href="url('/dashboard')">Ke Dashboard</x-ui.button>
                @else
                    <x-ui.button variant="ghost" :href="route('login')">Masuk</x-ui.button>
                    @if (Route::has('register'))
                        <x-ui.button :href="route('register')">Daftar</x-ui.button>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        {{-- Decorative gradient blob --}}
        <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #ff0021 0%, transparent 70%);"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-24 h-96 w-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #0a1f44 0%, transparent 70%);"></div>

        <div class="relative mx-auto max-w-7xl px-6 py-20 lg:py-28">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                {{-- Left: copy --}}
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 rounded-full border bg-card px-3 py-1 text-xs font-medium">
                        <span class="inline-flex h-1.5 w-1.5 rounded-full bg-primary"></span>
                        Prototype Warehaus
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.1]">
                        Manajemen inventaris,
                        <span class="text-primary">tanpa ribet.</span>
                    </h1>

                    <p class="text-lg text-muted-foreground max-w-lg">
                        Warehaus membantu tim Anda mencatat barang, mengelola peminjaman, dan membuat laporan &mdash; semuanya dalam satu tempat yang rapi.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        @auth
                            <x-ui.button size="lg" :href="url('/dashboard')">
                                Buka Dashboard
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </x-ui.button>
                        @else
                            <x-ui.button size="lg" :href="route('login')">
                                Mulai Sekarang
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </x-ui.button>
                            <x-ui.button variant="outline" size="lg" href="#features">Pelajari Fitur</x-ui.button>
                        @endauth
                    </div>

                    <div class="flex items-center gap-6 pt-4 text-sm text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            <span>3 role akses</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            <span>Export PDF & Excel</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            <span>Dark mode</span>
                        </div>
                    </div>
                </div>

                {{-- Right: mock dashboard card --}}
                <div class="relative">
                    <div class="absolute -inset-4 rounded-2xl bg-gradient-to-br from-primary/20 to-transparent blur-2xl"></div>
                    <div class="relative rounded-xl border bg-card shadow-2xl overflow-hidden">
                        {{-- Fake browser chrome --}}
                        <div class="flex items-center gap-1.5 border-b px-4 py-2.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 text-xs text-muted-foreground font-mono">warehaus.app/dashboard</span>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-muted-foreground">Ringkasan</p>
                                    <p class="text-lg font-bold">Dashboard</p>
                                </div>
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-semibold">W</div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div class="rounded-lg border p-3">
                                    <p class="text-[10px] text-muted-foreground uppercase tracking-wider">Total</p>
                                    <p class="mt-1 text-lg font-bold">248</p>
                                </div>
                                <div class="rounded-lg border p-3">
                                    <p class="text-[10px] text-muted-foreground uppercase tracking-wider">Dipinjam</p>
                                    <p class="mt-1 text-lg font-bold text-amber-500">32</p>
                                </div>
                                <div class="rounded-lg border p-3">
                                    <p class="text-[10px] text-muted-foreground uppercase tracking-wider">Tersedia</p>
                                    <p class="mt-1 text-lg font-bold text-emerald-500">216</p>
                                </div>
                            </div>

                            {{-- Fake bar chart --}}
                            <div class="rounded-lg border p-4">
                                <p class="text-xs font-medium mb-3">Tren Peminjaman</p>
                                <div class="flex items-end justify-between gap-1.5 h-24">
                                    @foreach([30, 45, 20, 70, 55, 80, 35, 60, 90, 50, 75, 65] as $h)
                                        <div class="flex-1 rounded-t" style="height: {{ $h }}%; background: linear-gradient(to top, #ff0021, #ff4d6d);"></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="border-t bg-muted/30 py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="max-w-2xl mx-auto text-center space-y-3">
                <p class="text-sm font-semibold uppercase tracking-wider text-primary">Fitur Utama</p>
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight">Semua yang Anda butuhkan untuk mengelola inventaris</h2>
                <p class="text-muted-foreground">Fitur end-to-end untuk menjaga inventaris tetap rapi dan akuntabel.</p>
            </div>

            <div class="mt-14 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @php
                    $features = [
                        [
                            'title' => 'Manajemen Barang',
                            'desc' => 'CRUD barang lengkap dengan kategori, stok, lokasi, dan foto. Kode barang dibuat otomatis.',
                            'color' => 'bg-primary/10 text-primary',
                            'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
                        ],
                        [
                            'title' => 'Peminjaman & Pengembalian',
                            'desc' => 'Catat peminjaman, tandai jatuh tempo, dan proses pengembalian dengan mudah.',
                            'color' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                            'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664',
                        ],
                        [
                            'title' => 'Dashboard & Laporan',
                            'desc' => 'Ringkasan real-time, tren peminjaman 12 bulan, laporan Excel & PDF.',
                            'color' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                            'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
                        ],
                        [
                            'title' => 'Role & Otorisasi',
                            'desc' => 'Admin, Staff, Manager — akses fitur diatur sesuai tanggung jawab.',
                            'color' => 'bg-violet-500/10 text-violet-600 dark:text-violet-400',
                            'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0z',
                        ],
                        [
                            'title' => 'Alert Stok Menipis',
                            'desc' => 'Notifikasi visual saat stok barang di bawah batas aman.',
                            'color' => 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400',
                            'icon' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z',
                        ],
                        [
                            'title' => 'Dark Mode',
                            'desc' => 'UI mendukung tema terang & gelap. Preferensi tersimpan otomatis.',
                            'color' => 'bg-navy/10 text-navy dark:text-blue-300',
                            'icon' => 'M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z',
                        ],
                    ];
                @endphp
                @foreach($features as $feature)
                    <div class="group rounded-xl border bg-card p-6 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg {{ $feature['color'] }} transition-transform group-hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}" /></svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-lg">{{ $feature['title'] }}</h3>
                        <p class="mt-1 text-sm text-muted-foreground">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="howitworks" class="py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="max-w-2xl mx-auto text-center space-y-3">
                <p class="text-sm font-semibold uppercase tracking-wider text-primary">Cara Kerja</p>
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight">Tiga langkah, siap pakai</h2>
            </div>

            <div class="mt-14 grid grid-cols-1 gap-8 md:grid-cols-3">
                @foreach([
                    ['num' => '01', 'title' => 'Daftarkan Barang', 'desc' => 'Tambahkan barang ke inventaris dengan kategori, stok, dan foto.'],
                    ['num' => '02', 'title' => 'Kelola Peminjaman', 'desc' => 'Catat siapa meminjam apa dan kapan jatuh tempo.'],
                    ['num' => '03', 'title' => 'Pantau & Laporkan', 'desc' => 'Lihat ringkasan di dashboard dan ekspor laporan.'],
                ] as $step)
                    <div class="text-center space-y-3">
                        <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full border-2 border-primary text-primary font-bold text-lg">{{ $step['num'] }}</div>
                        <h3 class="font-semibold text-lg">{{ $step['title'] }}</h3>
                        <p class="text-sm text-muted-foreground max-w-xs mx-auto">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section id="about" class="border-t">
        <div class="mx-auto max-w-5xl px-6 py-20">
            <div class="relative overflow-hidden rounded-2xl p-10 text-center text-white" style="background: linear-gradient(135deg, #ff0021 0%, #b3001c 45%, #0a1f44 100%);">
                <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 25% 20%, rgba(255,255,255,0.2) 0%, transparent 40%), radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 40%);"></div>
                <div class="relative space-y-4">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight">Siap merapikan inventaris?</h2>
                    <p class="text-white/80 max-w-xl mx-auto">Warehaus adalah prototype sistem manajemen inventaris berbasis web untuk mengelola stok, peminjaman, dan pelaporan barang perusahaan.</p>
                    <div class="flex flex-wrap items-center justify-center gap-3 pt-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex h-11 items-center gap-2 rounded-md bg-white px-6 text-sm font-semibold text-primary hover:bg-white/90 transition-colors">
                                Buka Dashboard
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex h-11 items-center gap-2 rounded-md bg-white px-6 text-sm font-semibold text-primary hover:bg-white/90 transition-colors">
                                Masuk Sekarang
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex h-11 items-center rounded-md border border-white/40 bg-white/10 backdrop-blur px-6 text-sm font-semibold text-white hover:bg-white/20 transition-colors">Buat Akun</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t py-8">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 sm:flex-row">
            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <img src="{{ asset('img/logo.png') }}" alt="Warehaus" class="h-6 w-6">
                <span>&copy; {{ date('Y') }} Warehaus</span>
            </div>
            <div class="flex items-center gap-6 text-sm text-muted-foreground">
                <a href="#features" class="hover:text-foreground transition-colors">Fitur</a>
                <a href="#howitworks" class="hover:text-foreground transition-colors">Cara Kerja</a>
                @guest
                    <a href="{{ route('login') }}" class="hover:text-foreground transition-colors">Masuk</a>
                @endguest
            </div>
        </div>
    </footer>
</body>
</html>

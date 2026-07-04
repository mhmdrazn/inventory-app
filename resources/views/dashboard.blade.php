<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        {{-- Page title --}}
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold tracking-tight">Selamat datang kembali, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-muted-foreground">Ringkasan inventaris dan aktivitas peminjaman.</p>
        </div>

        {{-- Shortcut actions --}}
        @if(auth()->user()->hasRole('admin', 'staff'))
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <a href="{{ route('products.index', ['create' => 1]) }}" class="group flex items-center gap-3 rounded-lg border bg-card p-4 shadow-sm transition-all hover:border-violet-500/40 hover:shadow-md">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:text-violet-400 transition-transform group-hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">Tambah Barang</p>
                        <p class="text-xs text-muted-foreground">Daftarkan barang baru ke inventaris</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                </a>

                <a href="{{ route('borrowings.index', ['create' => 1]) }}" class="group flex items-center gap-3 rounded-lg border bg-card p-4 shadow-sm transition-all hover:border-amber-500/40 hover:shadow-md">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 transition-transform group-hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12z" /></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">Tambah Peminjaman</p>
                        <p class="text-xs text-muted-foreground">Catat peminjaman barang baru</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                </a>

                <a href="{{ route('borrowings.index', ['status' => 'dipinjam']) }}" class="group flex items-center gap-3 rounded-lg border bg-card p-4 shadow-sm transition-all hover:border-emerald-500/40 hover:shadow-md">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 transition-transform group-hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">Pengembalian Peminjaman</p>
                        <p class="text-xs text-muted-foreground">Proses barang yang dikembalikan</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                </a>
            </div>
        @endif

        {{-- Low stock alert --}}
        @if($lowStockProducts->count() > 0)
            <div x-data="{ show: true }" x-show="show" x-cloak>
                <x-ui.alert variant="warning">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                            <div>
                                <p class="font-semibold">Peringatan Stok Menipis</p>
                                <p class="mt-0.5 opacity-90">Ada <strong>{{ $lowStockProducts->count() }}</strong> barang dengan stok ≤ 5. Segera lakukan pengecekan atau pengadaan ulang.</p>
                            </div>
                        </div>
                        <button @click="show = false" type="button" class="shrink-0 opacity-70 hover:opacity-100" aria-label="Tutup">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </x-ui.alert>
            </div>
        @endif

        {{-- KPI cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            @php
                $stats = [
                    ['label' => 'Total Barang', 'value' => number_format($totalStock), 'sub' => 'unit dalam inventaris', 'color' => 'violet', 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'],
                    ['label' => 'Sedang Dipinjam', 'value' => number_format($borrowedCount), 'sub' => 'unit dipinjam', 'color' => 'amber', 'icon' => 'M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z'],
                    ['label' => 'Tersedia', 'value' => number_format($availableStock), 'sub' => 'unit tersedia', 'color' => 'emerald', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
                    ['label' => 'Peminjaman Overdue', 'value' => number_format($overdueBorrowings->count()), 'sub' => 'melewati jatuh tempo', 'color' => 'destructive', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ['label' => 'Total Kategori', 'value' => $totalCategories, 'sub' => 'kategori barang', 'color' => 'blue', 'icon' => 'M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25z'],
                ];
                $colorMap = [
                    'violet' => 'bg-violet-500/10 text-violet-600 dark:text-violet-400',
                    'amber' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                    'emerald' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                    'blue' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
                    'destructive' => 'bg-destructive/10 text-destructive',
                ];
            @endphp
            @foreach($stats as $stat)
                <x-ui.card>
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <p class="text-sm text-muted-foreground">{{ $stat['label'] }}</p>
                            <p class="text-3xl font-bold tracking-tight">{{ $stat['value'] }}</p>
                            <p class="text-xs text-muted-foreground">{{ $stat['sub'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $colorMap[$stat['color']] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" /></svg>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        {{-- Chart --}}
        <x-ui.card>
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold">Tren Peminjaman</h3>
                    <p class="text-xs text-muted-foreground">12 bulan terakhir</p>
                </div>
            </div>
            <canvas id="borrowingChart" height="80"></canvas>
        </x-ui.card>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            {{-- Recent borrowings --}}
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="font-semibold">Peminjaman Terbaru</h3>
                    <p class="text-xs text-muted-foreground">5 aktivitas terakhir</p>
                </div>
                @if($recentBorrowings->count() > 0)
                    <div class="overflow-x-auto -mx-2">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b text-xs text-muted-foreground">
                                    <th class="px-2 py-2 text-left font-medium">Peminjam</th>
                                    <th class="px-2 py-2 text-left font-medium">Tanggal</th>
                                    <th class="px-2 py-2 text-left font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @foreach($recentBorrowings as $b)
                                    <tr class="border-b last:border-0">
                                        <td class="px-2 py-2.5">{{ $b->borrower_name }}</td>
                                        <td class="px-2 py-2.5 text-muted-foreground">{{ $b->borrowed_at->format('d/m/Y') }}</td>
                                        <td class="px-2 py-2.5">
                                            @if($b->status === 'dipinjam')
                                                <x-ui.badge variant="info">Dipinjam</x-ui.badge>
                                            @elseif($b->status === 'dikembalikan')
                                                <x-ui.badge variant="success">Dikembalikan</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="destructive">Terlambat</x-ui.badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="py-8 text-center text-sm text-muted-foreground">Belum ada data peminjaman.</p>
                @endif
            </x-ui.card>

            {{-- Low stock --}}
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="font-semibold">Stok Menipis</h3>
                    <p class="text-xs text-muted-foreground">Barang dengan stok ≤ 5</p>
                </div>
                @if($lowStockProducts->count() > 0)
                    <div class="overflow-x-auto -mx-2">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b text-xs text-muted-foreground">
                                    <th class="px-2 py-2 text-left font-medium">Barang</th>
                                    <th class="px-2 py-2 text-left font-medium">Kategori</th>
                                    <th class="px-2 py-2 text-left font-medium">Stok</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @foreach($lowStockProducts as $p)
                                    <tr class="border-b last:border-0">
                                        <td class="px-2 py-2.5">{{ $p->name }}</td>
                                        <td class="px-2 py-2.5 text-muted-foreground">{{ $p->category->name }}</td>
                                        <td class="px-2 py-2.5">
                                            @if($p->stock === 0)
                                                <x-ui.badge variant="destructive">{{ $p->stock }}</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="warning">{{ $p->stock }}</x-ui.badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="py-8 text-center text-sm text-muted-foreground">Semua stok tercukupi.</p>
                @endif
            </x-ui.card>
        </div>

        {{-- Overdue --}}
        @if($overdueBorrowings->count() > 0)
            <x-ui.card class="border-destructive/30">
                <div class="mb-4 flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-destructive/10 text-destructive">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold">Peminjaman Terlambat</h3>
                        <p class="text-xs text-muted-foreground">{{ $overdueBorrowings->count() }} peminjaman melewati jatuh tempo</p>
                    </div>
                </div>
                <div class="overflow-x-auto -mx-2">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b text-xs text-muted-foreground">
                                <th class="px-2 py-2 text-left font-medium">Peminjam</th>
                                <th class="px-2 py-2 text-left font-medium">Jatuh Tempo</th>
                                <th class="px-2 py-2 text-left font-medium">Keterlambatan</th>
                                <th class="px-2 py-2 text-left font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($overdueBorrowings as $ob)
                                <tr class="border-b last:border-0">
                                    <td class="px-2 py-2.5">{{ $ob->borrower_name }}</td>
                                    <td class="px-2 py-2.5 text-muted-foreground">{{ $ob->due_at->format('d/m/Y') }}</td>
                                    <td class="px-2 py-2.5 text-destructive font-medium">{{ (int) $ob->due_at->diffInDays(now()) }} hari</td>
                                    <td class="px-2 py-2.5">
                                        @if(auth()->user()->hasRole('admin', 'staff'))
                                            <a href="{{ route('borrowings.show', $ob) }}" class="text-primary hover:underline">Detail</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const ctx = document.getElementById('borrowingChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Jumlah Peminjaman',
                        data: @json($chartData),
                        backgroundColor: isDark ? 'rgba(255, 51, 87, 0.5)' : 'rgba(255, 0, 33, 0.4)',
                        borderColor: isDark ? 'rgba(255, 51, 87, 1)' : 'rgba(255, 0, 33, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: isDark ? '#a1a1aa' : '#71717a' },
                            grid: { color: isDark ? '#27272a' : '#e4e4e7' },
                        },
                        x: {
                            ticks: { color: isDark ? '#a1a1aa' : '#71717a' },
                            grid: { display: false },
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

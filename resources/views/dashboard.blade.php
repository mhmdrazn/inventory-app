<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($lowStockProducts->count() > 0)
                <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-lg border-l-4 border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">Peringatan Stok Menipis</h3>
                                <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">
                                    Ada <strong>{{ $lowStockProducts->count() }}</strong> barang dengan stok ≤ 5. Segera lakukan pengecekan atau pengadaan ulang.
                                </p>
                            </div>
                        </div>
                        <button @click="show = false" type="button" class="text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-300 focus:outline-none" aria-label="Tutup">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Barang</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalStock) }}</div>
                    <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">unit dalam inventaris</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sedang Dipinjam</div>
                    <div class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($borrowedCount) }}</div>
                    <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">unit dipinjam</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Tersedia</div>
                    <div class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($availableStock) }}</div>
                    <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">unit tersedia</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kategori</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalCategories }}</div>
                    <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">kategori barang</div>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tren Peminjaman (12 Bulan Terakhir)</h3>
                    <canvas id="borrowingChart" height="100"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Peminjaman Terbaru -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Peminjaman Terbaru</h3>
                        @if($recentBorrowings->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Peminjam</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tanggal</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($recentBorrowings as $b)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $b->borrower_name }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $b->borrowed_at->format('d/m/Y') }}</td>
                                                <td class="px-4 py-2 text-sm">
                                                    @if($b->status === 'dipinjam')
                                                        <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-2 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Dipinjam</span>
                                                    @elseif($b->status === 'dikembalikan')
                                                        <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2 py-0.5 text-xs font-medium text-green-800 dark:text-green-400">Dikembalikan</span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">Terlambat</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data peminjaman.</p>
                        @endif
                    </div>
                </div>

                <!-- Stok Menipis -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Stok Menipis (≤ 5)</h3>
                        @if($lowStockProducts->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Barang</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Kategori</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($lowStockProducts as $p)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $p->name }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $p->category->name }}</td>
                                                <td class="px-4 py-2 text-sm">
                                                    @if($p->stock === 0)
                                                        <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">{{ $p->stock }}</span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-400">{{ $p->stock }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Semua stok tercukupi.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Overdue -->
            @if($overdueBorrowings->count() > 0)
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-red-600 dark:text-red-400 mb-4">⚠️ Peminjaman Terlambat</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Peminjam</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Jatuh Tempo</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Keterlambatan</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($overdueBorrowings as $ob)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $ob->borrower_name }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $ob->due_at->format('d/m/Y') }}</td>
                                            <td class="px-4 py-2 text-sm text-red-600 dark:text-red-400">{{ $ob->due_at->diffInDays(now()) }} hari</td>
                                            <td class="px-4 py-2 text-sm">
                                                @if(auth()->user()->hasRole('admin', 'staff'))
                                                    <a href="{{ route('borrowings.show', $ob) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Detail</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('borrowingChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Jumlah Peminjaman',
                        data: @json($chartData),
                        backgroundColor: 'rgba(99, 102, 241, 0.5)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
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

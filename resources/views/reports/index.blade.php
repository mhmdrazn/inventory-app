<x-app-layout>
    <x-slot name="header">Laporan</x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="{ tab: 'barang' }">
        {{-- Header (no export here) --}}
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Laporan</h1>
            <p class="text-sm text-muted-foreground">Ringkasan inventaris dan riwayat peminjaman.</p>
        </div>

        {{-- Filter --}}
        <x-ui.card>
            <h3 class="mb-3 font-semibold text-sm">Filter Laporan</h3>
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <div class="space-y-1.5">
                    <x-ui.label for="date_from" value="Dari Tanggal" />
                    <x-ui.input id="date_from" name="date_from" type="date" :value="$filters['date_from']" />
                </div>
                <div class="space-y-1.5">
                    <x-ui.label for="date_to" value="Sampai Tanggal" />
                    <x-ui.input id="date_to" name="date_to" type="date" :value="$filters['date_to']" />
                </div>
                <div class="space-y-1.5">
                    <x-ui.label for="status" value="Status" />
                    <x-ui.select id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="dipinjam" {{ $filters['status'] === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="dikembalikan" {{ $filters['status'] === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="terlambat" {{ $filters['status'] === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </x-ui.select>
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit" class="flex-1 w-full">Cari</x-ui.button>
                    <x-ui.button variant="outline" :href="route('reports.index')" class="flex-1 w-full">Reset</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- Tabs + export toolbar --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div role="tablist" class="inline-flex rounded-md border bg-card p-0.5">
                <button
                    type="button"
                    role="tab"
                    :aria-selected="tab === 'barang'"
                    @click="tab = 'barang'"
                    :class="tab === 'barang' ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                    class="inline-flex h-9 items-center gap-2 rounded-[6px] px-4 text-sm font-medium transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
                    Daftar Barang
                    <span class="rounded bg-background/30 px-1.5 py-0.5 text-[10px] font-semibold">{{ $products->count() }}</span>
                </button>
                <button
                    type="button"
                    role="tab"
                    :aria-selected="tab === 'peminjaman'"
                    @click="tab = 'peminjaman'"
                    :class="tab === 'peminjaman' ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                    class="inline-flex h-9 items-center gap-2 rounded-[6px] px-4 text-sm font-medium transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                    Riwayat Peminjaman
                    <span class="rounded bg-background/30 px-1.5 py-0.5 text-[10px] font-semibold">{{ $borrowings->total() }}</span>
                </button>
            </div>

            {{-- Export buttons --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export.pdf', request()->query()) }}"
                    class="inline-flex h-9 items-center gap-2 rounded-md border border-red-500/30 bg-red-500/10 px-3 text-sm font-medium text-red-700 dark:text-red-400 hover:bg-red-500/20 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z" /></svg>
                    Export PDF
                </a>
                <a href="{{ route('reports.export.excel', request()->query()) }}"
                    class="inline-flex h-9 items-center gap-2 rounded-md border border-emerald-500/30 bg-emerald-500/10 px-3 text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:bg-emerald-500/20 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" /></svg>
                    Export Excel
                </a>
            </div>
        </div>

        {{-- Daftar Barang tab --}}
        <div x-show="tab === 'barang'" x-cloak>
            <x-ui.card :padded="false">
                <div class="p-6 pb-3">
                    <h3 class="font-semibold">Daftar Barang</h3>
                    <p class="text-xs text-muted-foreground">Total {{ $products->count() }} barang di inventaris.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-y bg-muted/40 text-xs text-muted-foreground">
                                <th class="px-6 py-2.5 text-left font-medium">Kode</th>
                                <th class="px-6 py-2.5 text-left font-medium">Nama</th>
                                <th class="px-6 py-2.5 text-left font-medium">Kategori</th>
                                <th class="px-6 py-2.5 text-left font-medium">Stok</th>
                                <th class="px-6 py-2.5 text-left font-medium">Kondisi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($products as $product)
                                <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-6 py-3 font-mono text-xs">{{ $product->code }}</td>
                                    <td class="px-6 py-3 font-medium">{{ $product->name }}</td>
                                    <td class="px-6 py-3 text-muted-foreground">{{ $product->category?->name ?? '-' }}</td>
                                    <td class="px-6 py-3">{{ number_format($product->stock) }}</td>
                                    <td class="px-6 py-3">
                                        @if($product->condition === 'baik')
                                            <x-ui.badge variant="success">Baik</x-ui.badge>
                                        @elseif($product->condition === 'rusak_ringan')
                                            <x-ui.badge variant="warning">Rusak Ringan</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="destructive">Rusak Berat</x-ui.badge>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-muted-foreground">Belum ada data barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>

        {{-- Riwayat Peminjaman tab --}}
        <div x-show="tab === 'peminjaman'" x-cloak>
            <x-ui.card :padded="false">
                <div class="p-6 pb-3">
                    <h3 class="font-semibold">Riwayat Peminjaman</h3>
                    <p class="text-xs text-muted-foreground">Total {{ $borrowings->total() }} transaksi pada rentang filter.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-y bg-muted/40 text-xs text-muted-foreground">
                                <th class="px-6 py-2.5 text-left font-medium">Peminjam</th>
                                <th class="px-6 py-2.5 text-left font-medium">Tgl Pinjam</th>
                                <th class="px-6 py-2.5 text-left font-medium">Jatuh Tempo</th>
                                <th class="px-6 py-2.5 text-left font-medium">Status</th>
                                <th class="px-6 py-2.5 text-right font-medium">Total Item</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($borrowings as $borrowing)
                                <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-6 py-3 font-medium">{{ $borrowing->borrower_name }}</td>
                                    <td class="px-6 py-3 text-muted-foreground">{{ $borrowing->borrowed_at->format('d M Y') }}</td>
                                    <td class="px-6 py-3 text-muted-foreground">{{ $borrowing->due_at->format('d M Y') }}</td>
                                    <td class="px-6 py-3">
                                        @if($borrowing->status === 'dipinjam')
                                            <x-ui.badge variant="info">Dipinjam</x-ui.badge>
                                        @elseif($borrowing->status === 'dikembalikan')
                                            <x-ui.badge variant="success">Dikembalikan</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="destructive">Terlambat</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium">{{ $borrowing->borrowingDetails->sum('quantity') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-muted-foreground">Tidak ada peminjaman pada rentang filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($borrowings->hasPages())
                    <div class="border-t p-4">
                        {{ $borrowings->links() }}
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>

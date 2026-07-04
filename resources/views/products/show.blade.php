<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('products.index') }}" class="hover:text-foreground transition-colors">Barang</a>
        <span class="mx-1.5 text-border">/</span>
        <span class="text-foreground">Detail</span>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        {{-- Header --}}
        <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">{{ $product->name }}</h1>
                <p class="text-sm text-muted-foreground font-mono">{{ $product->code }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="outline" :href="route('products.index')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    Kembali
                </x-ui.button>
                @if(auth()->user()->hasRole('admin', 'staff'))
                    <x-ui.button :href="route('products.edit', $product)">Edit</x-ui.button>
                @endif
            </div>
        </div>

        {{-- Detail grid --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Image --}}
            <x-ui.card :padded="false" class="overflow-hidden lg:col-span-1">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 bg-muted flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                    </div>
                @endif
                <div class="p-4 space-y-3">
                    <div>
                        <p class="text-xs text-muted-foreground uppercase tracking-wider">Kondisi</p>
                        <div class="mt-1">
                            @if($product->condition === 'baik')
                                <x-ui.badge variant="success">Baik</x-ui.badge>
                            @elseif($product->condition === 'rusak_ringan')
                                <x-ui.badge variant="warning">Rusak Ringan</x-ui.badge>
                            @else
                                <x-ui.badge variant="destructive">Rusak Berat</x-ui.badge>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground uppercase tracking-wider">Stok Tersedia</p>
                        <div class="mt-1">
                            @if($product->stock === 0)
                                <x-ui.badge variant="destructive">{{ $product->stock }} unit</x-ui.badge>
                            @elseif($product->stock <= 5)
                                <x-ui.badge variant="warning">{{ $product->stock }} unit</x-ui.badge>
                            @else
                                <x-ui.badge variant="success">{{ $product->stock }} unit</x-ui.badge>
                            @endif
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Info --}}
            <x-ui.card class="lg:col-span-2">
                <h3 class="font-semibold mb-4">Informasi Barang</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs text-muted-foreground uppercase tracking-wider">Kode</dt>
                        <dd class="mt-1 font-mono text-sm">{{ $product->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground uppercase tracking-wider">Nama</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $product->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground uppercase tracking-wider">Kategori</dt>
                        <dd class="mt-1 text-sm">{{ $product->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground uppercase tracking-wider">Lokasi</dt>
                        <dd class="mt-1 text-sm">{{ $product->location ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-muted-foreground uppercase tracking-wider">Deskripsi</dt>
                        <dd class="mt-1 text-sm text-muted-foreground">{{ $product->description ?? '-' }}</dd>
                    </div>
                </dl>
            </x-ui.card>
        </div>

        {{-- Riwayat peminjaman --}}
        @php
            $history = $product->borrowingDetails->sortByDesc('created_at');
            $activeCount = $history->filter(fn($d) => $d->borrowing->status === 'dipinjam')->count();
            $totalBorrowed = $history->sum('quantity');
        @endphp
        <x-ui.card>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-semibold">Riwayat Peminjaman</h3>
                    <p class="text-xs text-muted-foreground">Detail peminjaman barang ini sepanjang waktu.</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <div class="rounded-md border px-3 py-1.5">
                        <span class="text-muted-foreground text-xs">Sedang dipinjam</span>
                        <span class="ml-2 font-semibold">{{ $activeCount }}</span>
                    </div>
                    <div class="rounded-md border px-3 py-1.5">
                        <span class="text-muted-foreground text-xs">Total unit dipinjam</span>
                        <span class="ml-2 font-semibold">{{ $totalBorrowed }}</span>
                    </div>
                </div>
            </div>

            @if($history->count() > 0)
                <div class="overflow-x-auto -mx-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-y bg-muted/40 text-xs text-muted-foreground">
                                <th class="px-6 py-2.5 text-left font-medium">Peminjam</th>
                                <th class="px-6 py-2.5 text-left font-medium">Jumlah</th>
                                <th class="px-6 py-2.5 text-left font-medium">Tanggal Pinjam</th>
                                <th class="px-6 py-2.5 text-left font-medium">Jatuh Tempo</th>
                                <th class="px-6 py-2.5 text-left font-medium">Status</th>
                                <th class="px-6 py-2.5 text-right font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($history as $detail)
                                <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-6 py-3 font-medium">{{ $detail->borrowing->borrower_name }}</td>
                                    <td class="px-6 py-3 text-muted-foreground">{{ $detail->quantity }} unit</td>
                                    <td class="px-6 py-3 text-muted-foreground">{{ $detail->borrowing->borrowed_at->format('d M Y') }}</td>
                                    <td class="px-6 py-3 text-muted-foreground">{{ $detail->borrowing->due_at->format('d M Y') }}</td>
                                    <td class="px-6 py-3">
                                        @if($detail->borrowing->status === 'dipinjam')
                                            <x-ui.badge variant="info">Dipinjam</x-ui.badge>
                                        @elseif($detail->borrowing->status === 'dikembalikan')
                                            <x-ui.badge variant="success">Dikembalikan</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="destructive">Terlambat</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @if(auth()->user()->hasRole('admin', 'staff'))
                                            <a href="{{ route('borrowings.show', $detail->borrowing) }}" class="text-sm text-primary hover:underline">Lihat</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center">
                    <p class="text-sm text-muted-foreground">Belum ada riwayat peminjaman untuk barang ini.</p>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>

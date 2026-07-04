<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('borrowings.index') }}" class="hover:text-foreground transition-colors">Peminjaman</a>
        <span class="mx-1.5 text-border">/</span>
        <span class="text-foreground">Detail</span>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Detail Peminjaman</h1>
                <p class="text-sm text-muted-foreground">{{ $borrowing->borrower_name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="outline" :href="route('borrowings.index')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    Kembali
                </x-ui.button>
                @if($borrowing->status === 'dipinjam' && auth()->user()->hasRole('admin', 'staff'))
                    <div x-data="{ showReturnModal: false }">
                        <x-ui.button @click="showReturnModal = true">Proses Pengembalian</x-ui.button>
                        <div x-show="showReturnModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                            <div @click="showReturnModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
                            <div class="relative flex min-h-screen items-center justify-center p-4">
                                <div @click.stop class="relative w-full max-w-md rounded-lg border bg-card p-6 shadow-lg">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                        </div>
                                        <div class="text-left">
                                            <h3 class="text-lg font-semibold">Konfirmasi Pengembalian</h3>
                                            <p class="mt-1 text-sm text-muted-foreground">Kembalikan seluruh barang dari peminjaman <strong class="text-foreground">{{ $borrowing->borrower_name }}</strong>? Stok akan otomatis dikembalikan.</p>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-2">
                                        <x-ui.button variant="outline" @click="showReturnModal = false">Batal</x-ui.button>
                                        <form method="POST" action="{{ route('borrowings.return', $borrowing) }}">
                                            @csrf
                                            @method('PATCH')
                                            <x-ui.button type="submit">Ya, Kembalikan</x-ui.button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if(session('success'))
            <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
        @endif
        @if(session('error'))
            <x-ui.alert variant="destructive">{{ session('error') }}</x-ui.alert>
        @endif

        <x-ui.card>
            <h3 class="font-semibold mb-4">Informasi Peminjaman</h3>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs text-muted-foreground uppercase tracking-wider">Peminjam</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $borrowing->borrower_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground uppercase tracking-wider">Diinput Oleh</dt>
                    <dd class="mt-1 text-sm">{{ $borrowing->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground uppercase tracking-wider">Status</dt>
                    <dd class="mt-1 flex items-center gap-1.5">
                        @if($borrowing->status === 'dipinjam')
                            <x-ui.badge variant="info">Dipinjam</x-ui.badge>
                            @if($borrowing->isOverdue())
                                <x-ui.badge variant="destructive">Terlambat</x-ui.badge>
                            @endif
                        @elseif($borrowing->status === 'dikembalikan')
                            <x-ui.badge variant="success">Dikembalikan</x-ui.badge>
                        @else
                            <x-ui.badge variant="destructive">Terlambat</x-ui.badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground uppercase tracking-wider">Tanggal Pinjam</dt>
                    <dd class="mt-1 text-sm">{{ $borrowing->borrowed_at->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted-foreground uppercase tracking-wider">Jatuh Tempo</dt>
                    <dd class="mt-1 text-sm">{{ $borrowing->due_at->format('d M Y') }}</dd>
                </div>
                @if($borrowing->returned_at)
                    <div>
                        <dt class="text-xs text-muted-foreground uppercase tracking-wider">Tanggal Kembali</dt>
                        <dd class="mt-1 text-sm">{{ $borrowing->returned_at->format('d M Y') }}</dd>
                    </div>
                @endif
                @if($borrowing->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-muted-foreground uppercase tracking-wider">Catatan</dt>
                        <dd class="mt-1 text-sm text-muted-foreground">{{ $borrowing->notes }}</dd>
                    </div>
                @endif
            </dl>
        </x-ui.card>

        <x-ui.card :padded="false">
            <div class="p-6 pb-4">
                <h3 class="font-semibold">Daftar Barang Dipinjam</h3>
                <p class="text-xs text-muted-foreground">{{ $borrowing->borrowingDetails->count() }} jenis barang, total {{ $borrowing->borrowingDetails->sum('quantity') }} unit</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y bg-muted/40 text-xs text-muted-foreground">
                            <th class="px-6 py-2.5 text-left font-medium">Kode</th>
                            <th class="px-6 py-2.5 text-left font-medium">Nama Barang</th>
                            <th class="px-6 py-2.5 text-right font-medium">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($borrowing->borrowingDetails as $detail)
                            <tr class="border-b last:border-0">
                                <td class="px-6 py-3 font-mono text-xs">{{ $detail->product->code }}</td>
                                <td class="px-6 py-3">{{ $detail->product->name }}</td>
                                <td class="px-6 py-3 text-right font-medium">{{ $detail->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>

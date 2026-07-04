<x-app-layout>
    <x-slot name="header">Peminjaman</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        {{-- Page header --}}
        <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Daftar Peminjaman</h1>
                <p class="text-sm text-muted-foreground">Kelola transaksi peminjaman barang.</p>
            </div>
            @if(auth()->user()->hasRole('admin', 'staff'))
                <x-ui.button @click="$dispatch('open-dialog', 'create-borrowing')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Peminjaman Baru
                </x-ui.button>
            @endif
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
        @endif
        @if(session('error'))
            <x-ui.alert variant="destructive">{{ session('error') }}</x-ui.alert>
        @endif

        {{-- Filter --}}
        <x-ui.card>
            <form method="GET" action="{{ route('borrowings.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-5">
                <div class="space-y-1.5">
                    <x-ui.label for="search" value="Cari" />
                    <x-ui.input id="search" name="search" placeholder="Nama peminjam..." :value="request('search')" />
                </div>
                <div class="space-y-1.5">
                    <x-ui.label for="status" value="Status" />
                    <x-ui.select id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </x-ui.select>
                </div>
                <div class="space-y-1.5">
                    <x-ui.label for="date_from" value="Dari" />
                    <x-ui.input id="date_from" name="date_from" type="date" :value="request('date_from')" />
                </div>
                <div class="space-y-1.5">
                    <x-ui.label for="date_to" value="Sampai" />
                    <x-ui.input id="date_to" name="date_to" type="date" :value="request('date_to')" />
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit" class="flex-1 w-full">Cari</x-ui.button>
                    <x-ui.button variant="outline" :href="route('borrowings.index')" class="flex-1 w-full">Reset</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- Table --}}
        <x-ui.card :padded="false">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b bg-muted/40 text-xs text-muted-foreground">
                            <th class="px-6 py-3 text-left font-medium">Peminjam</th>
                            <th class="px-6 py-3 text-left font-medium">Item</th>
                            <th class="px-6 py-3 text-left font-medium">Tanggal Pinjam</th>
                            <th class="px-6 py-3 text-left font-medium">Jatuh Tempo</th>
                            <th class="px-6 py-3 text-left font-medium">Status</th>
                            <th class="px-6 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($borrowings as $borrowing)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $borrowing->borrower_name }}</td>
                                <td class="px-6 py-3 text-muted-foreground">{{ $borrowing->borrowingDetails->sum('quantity') }} barang</td>
                                <td class="px-6 py-3 text-muted-foreground">{{ $borrowing->borrowed_at->format('d M Y') }}</td>
                                <td class="px-6 py-3 text-muted-foreground">
                                    {{ $borrowing->due_at->format('d M Y') }}
                                    @if($borrowing->isOverdue())
                                        <span class="ml-1 text-xs text-destructive">(terlambat)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if($borrowing->status === 'dipinjam')
                                        <x-ui.badge variant="info">Dipinjam</x-ui.badge>
                                    @elseif($borrowing->status === 'dikembalikan')
                                        <x-ui.badge variant="success">Dikembalikan</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="destructive">Terlambat</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button variant="outline" size="sm" :href="route('borrowings.show', $borrowing)">Detail</x-ui.button>
                                        @if($borrowing->status === 'dipinjam' && auth()->user()->hasRole('admin', 'staff'))
                                            <div x-data="{ showReturnModal: false }" class="inline-flex">
                                                <button type="button" @click="showReturnModal = true" class="inline-flex h-8 items-center px-3 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 ring-1 ring-inset ring-emerald-500/20 hover:bg-emerald-500/20 transition-colors">Kembalikan</button>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-muted-foreground">Belum ada data peminjaman.</td>
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

    {{-- Create Borrowing Dialog --}}
    @if(auth()->user()->hasRole('admin', 'staff'))
        <x-ui.dialog
            name="create-borrowing"
            title="Peminjaman Baru"
            description="Isi detail peminjaman dan barang yang akan dipinjam."
            maxWidth="3xl"
        >
            <form
                method="POST"
                action="{{ route('borrowings.store') }}"
                x-data="{
                    items: [{ product_id: '', quantity: 1 }],
                    products: {{ Js::from($availableProducts->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'code' => $p->code, 'stock' => $p->stock])) }},
                    addItem() { this.items.push({ product_id: '', quantity: 1 }); },
                    removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); },
                    getMaxStock(productId) {
                        const p = this.products.find(x => x.id == productId);
                        return p ? p.stock : 1;
                    }
                }"
            >
                @csrf
                <div class="space-y-5">
                    <div class="space-y-1.5">
                        <x-ui.label for="borrower_name" value="Nama Peminjam" />
                        <x-ui.input id="borrower_name" name="borrower_name" :value="old('borrower_name')" required />
                        <x-input-error :messages="$errors->get('borrower_name')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <x-ui.label for="borrowed_at" value="Tanggal Pinjam" />
                            <x-ui.input id="borrowed_at" name="borrowed_at" type="date" :value="old('borrowed_at', now()->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('borrowed_at')" />
                        </div>
                        <div class="space-y-1.5">
                            <x-ui.label for="due_at" value="Jatuh Tempo" />
                            <x-ui.input id="due_at" name="due_at" type="date" :value="old('due_at')" required />
                            <x-input-error :messages="$errors->get('due_at')" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <x-ui.label for="notes" value="Catatan (opsional)" />
                        <x-ui.textarea id="notes" name="notes" rows="2">{{ old('notes') }}</x-ui.textarea>
                        <x-input-error :messages="$errors->get('notes')" />
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <x-ui.label value="Daftar Barang" />
                            <x-ui.button type="button" variant="outline" size="sm" @click="addItem()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                Tambah
                            </x-ui.button>
                        </div>
                        <x-input-error class="mb-2" :messages="$errors->get('items')" />

                        <div class="space-y-2">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-start gap-2 rounded-md border p-2.5">
                                    <div class="flex-1">
                                        <x-ui.select ::name="'items[' + index + '][product_id]'" x-model="item.product_id" required>
                                            <option value="">Pilih Barang</option>
                                            <template x-for="product in products" :key="product.id">
                                                <option :value="product.id" x-text="product.code + ' — ' + product.name + ' (stok: ' + product.stock + ')'"></option>
                                            </template>
                                        </x-ui.select>
                                    </div>
                                    <div class="w-24">
                                        <x-ui.input type="number" ::name="'items[' + index + '][quantity]'" x-model="item.quantity" min="1" ::max="getMaxStock(item.product_id)" placeholder="Qty" required />
                                    </div>
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-md text-destructive hover:bg-destructive/10 transition-colors" aria-label="Hapus item">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t">
                        <x-ui.button type="button" variant="outline" @click="$dispatch('close-dialog', 'create-borrowing')">Batal</x-ui.button>
                        <x-ui.button type="submit">Simpan Peminjaman</x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.dialog>

        @if($errors->any() || request('create'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('open-dialog', { detail: 'create-borrowing' }));
                });
            </script>
        @endif
    @endif
</x-app-layout>

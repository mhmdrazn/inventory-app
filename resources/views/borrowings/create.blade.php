<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Peminjaman Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('borrowings.store') }}" x-data="{
                        items: [{ product_id: '', quantity: 1 }],
                        products: {{ Js::from($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'code' => $p->code, 'stock' => $p->stock])) }},
                        addItem() {
                            this.items.push({ product_id: '', quantity: 1 });
                        },
                        removeItem(index) {
                            if (this.items.length > 1) {
                                this.items.splice(index, 1);
                            }
                        },
                        getMaxStock(productId) {
                            const product = this.products.find(p => p.id == productId);
                            return product ? product.stock : 1;
                        }
                    }">
                        @csrf

                        <div class="space-y-6">
                            <!-- Nama Peminjam -->
                            <div>
                                <x-input-label for="borrower_name" value="Nama Peminjam" />
                                <x-text-input id="borrower_name" name="borrower_name" type="text" class="mt-1 block w-full" :value="old('borrower_name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('borrower_name')" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Tanggal Pinjam -->
                                <div>
                                    <x-input-label for="borrowed_at" value="Tanggal Pinjam" />
                                    <x-text-input id="borrowed_at" name="borrowed_at" type="date" class="mt-1 block w-full" :value="old('borrowed_at', now()->format('Y-m-d'))" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('borrowed_at')" />
                                </div>

                                <!-- Jatuh Tempo -->
                                <div>
                                    <x-input-label for="due_at" value="Tanggal Jatuh Tempo" />
                                    <x-text-input id="due_at" name="due_at" type="date" class="mt-1 block w-full" :value="old('due_at')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('due_at')" />
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <x-input-label for="notes" value="Catatan (opsional)" />
                                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                            </div>

                            <!-- Daftar Barang -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <x-input-label value="Daftar Barang" />
                                    <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                        + Tambah Barang
                                    </button>
                                </div>

                                <x-input-error class="mb-2" :messages="$errors->get('items')" />

                                <div class="space-y-3">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="flex-1">
                                                <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm" required>
                                                    <option value="">Pilih Barang</option>
                                                    <template x-for="product in products" :key="product.id">
                                                        <option :value="product.id" x-text="product.code + ' - ' + product.name + ' (stok: ' + product.stock + ')'"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="w-24">
                                                <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity" min="1" :max="getMaxStock(item.product_id)" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm" placeholder="Qty" required />
                                            </div>
                                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="mt-1 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>Simpan Peminjaman</x-primary-button>
                                <a href="{{ route('borrowings.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

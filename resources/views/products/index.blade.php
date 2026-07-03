<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Daftar Barang
            </h2>
            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition ease-in-out duration-150">
                + Tambah Barang
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                    <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                    <p class="text-sm text-red-700 dark:text-red-400">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="search" value="Cari" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" placeholder="Nama atau kode barang..." :value="request('search')" />
                        </div>
                        <div>
                            <x-input-label for="category" value="Kategori" />
                            <select id="category" name="category" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="condition" value="Kondisi" />
                            <select id="condition" name="condition" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Semua Kondisi</option>
                                <option value="baik" {{ request('condition') === 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="rusak_ringan" {{ request('condition') === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="rusak_berat" {{ request('condition') === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <x-primary-button>Filter</x-primary-button>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kondisi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">{{ $product->code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('products.show', $product) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ $product->name }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($product->stock === 0)
                                                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">{{ $product->stock }}</span>
                                            @elseif($product->stock <= 5)
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-400">{{ $product->stock }}</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-400">{{ $product->stock }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->location ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($product->condition === 'baik')
                                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-400">Baik</span>
                                            @elseif($product->condition === 'rusak_ringan')
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-400">Rusak Ringan</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">Rusak Berat</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Edit</a>
                                                <div x-data="{ showDeleteModal: false }">
                                                    <button @click="showDeleteModal = true" type="button" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Hapus</button>
                                                    <!-- Delete Modal -->
                                                    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                        <div class="flex min-h-screen items-center justify-center p-4">
                                                            <div x-show="showDeleteModal" @click="showDeleteModal = false" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75 transition-opacity"></div>
                                                            <div x-show="showDeleteModal" class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                                                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Konfirmasi Hapus</h3>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Apakah Anda yakin ingin menghapus barang <strong>{{ $product->name }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                                                                <div class="flex justify-end gap-3">
                                                                    <x-secondary-button @click="showDeleteModal = false">Batal</x-secondary-button>
                                                                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <x-danger-button type="submit">Hapus</x-danger-button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data barang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

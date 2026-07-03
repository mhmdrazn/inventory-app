<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Barang
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Info Barang -->
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Barang</p>
                                <p class="text-lg font-mono text-gray-900 dark:text-gray-100">{{ $product->code }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Barang</p>
                                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ $product->category->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stok</p>
                                @if($product->stock === 0)
                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">{{ $product->stock }}</span>
                                @elseif($product->stock <= 5)
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-400">{{ $product->stock }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-400">{{ $product->stock }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ $product->location ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kondisi</p>
                                @if($product->condition === 'baik')
                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-400">Baik</span>
                                @elseif($product->condition === 'rusak_ringan')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-400">Rusak Ringan</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">Rusak Berat</span>
                                @endif
                            </div>
                        </div>

                        <!-- Gambar -->
                        <div>
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded-lg">
                            @else
                                <div class="w-full h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <p class="text-gray-400 dark:text-gray-500">Tidak ada gambar</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition ease-in-out duration-150">Edit</a>
                    </div>
                </div>
            </div>

            <!-- Riwayat Peminjaman -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Riwayat Peminjaman</h3>
                    @if($product->borrowingDetails->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Peminjam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Pinjam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($product->borrowingDetails->sortByDesc('created_at') as $detail)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $detail->borrowing->borrower_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $detail->quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $detail->borrowing->borrowed_at->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($detail->borrowing->status === 'dipinjam')
                                                    <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Dipinjam</span>
                                                @elseif($detail->borrowing->status === 'dikembalikan')
                                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-400">Dikembalikan</span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">Terlambat</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat peminjaman untuk barang ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

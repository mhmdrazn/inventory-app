<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Daftar Kategori
            </h2>
            <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition ease-in-out duration-150">
                + Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($categories as $category)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:text-gray-300">{{ $category->products_count }} produk</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Edit</a>
                                                <div x-data="{ showDeleteModal: false }">
                                                    <button @click="showDeleteModal = true" type="button" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Hapus</button>
                                                    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                        <div class="flex min-h-screen items-center justify-center p-4">
                                                            <div x-show="showDeleteModal" @click="showDeleteModal = false" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75 transition-opacity"></div>
                                                            <div x-show="showDeleteModal" class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                                                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Konfirmasi Hapus</h3>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Apakah Anda yakin ingin menghapus kategori <strong>{{ $category->name }}</strong>?</p>
                                                                @if($category->products_count > 0)
                                                                    <div class="mb-4 rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-3">
                                                                        <p class="text-sm text-yellow-700 dark:text-yellow-400">⚠️ Kategori ini memiliki {{ $category->products_count }} produk dan tidak dapat dihapus.</p>
                                                                    </div>
                                                                @endif
                                                                <div class="flex justify-end gap-3">
                                                                    <x-secondary-button @click="showDeleteModal = false">Batal</x-secondary-button>
                                                                    @if($category->products_count === 0)
                                                                        <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <x-danger-button type="submit">Hapus</x-danger-button>
                                                                        </form>
                                                                    @endif
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
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada kategori.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

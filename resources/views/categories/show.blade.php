<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Detail Kategori</h2>
            <a href="{{ route('categories.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Kategori</p>
                            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $category->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Produk</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $category->products_count }} produk</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Barang
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Kode Barang -->
                            <div>
                                <x-input-label for="code" value="Kode Barang" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $product->code)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('code')" />
                            </div>

                            <!-- Nama Barang -->
                            <div>
                                <x-input-label for="name" value="Nama Barang" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- Kategori -->
                            <div>
                                <x-input-label for="category_id" value="Kategori" />
                                <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>

                            <!-- Stok -->
                            <div>
                                <x-input-label for="stock" value="Stok" />
                                <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full" :value="old('stock', $product->stock)" min="0" required />
                                <x-input-error class="mt-2" :messages="$errors->get('stock')" />
                            </div>

                            <!-- Lokasi -->
                            <div>
                                <x-input-label for="location" value="Lokasi Penyimpanan" />
                                <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location', $product->location)" />
                                <x-input-error class="mt-2" :messages="$errors->get('location')" />
                            </div>

                            <!-- Kondisi -->
                            <div>
                                <x-input-label for="condition" value="Kondisi" />
                                <select id="condition" name="condition" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="baik" {{ old('condition', $product->condition) === 'baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="rusak_ringan" {{ old('condition', $product->condition) === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="rusak_berat" {{ old('condition', $product->condition) === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('condition')" />
                            </div>

                            <!-- Gambar -->
                            <div>
                                <x-input-label for="image" value="Gambar (opsional)" />
                                @if($product->image)
                                    <div class="mt-2 mb-2">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-md">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gambar saat ini</p>
                                    </div>
                                @endif
                                <input id="image" name="image" type="file" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-600" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG. Maksimal 2MB.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('image')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>Perbarui</x-primary-button>
                                <a href="{{ route('products.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">Barang</x-slot>

    <div
        class="mx-auto max-w-7xl space-y-6"
        x-data="{
            view: localStorage.getItem('products.view') || 'list',
            setView(v) { this.view = v; localStorage.setItem('products.view', v); }
        }"
    >
        {{-- Page header --}}
        <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Daftar Barang</h1>
                <p class="text-sm text-muted-foreground">Kelola inventaris barang perusahaan.</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- View toggle --}}
                <div class="inline-flex rounded-md border bg-card p-0.5">
                    <button type="button" @click="setView('list')" :class="view === 'list' ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'" class="inline-flex h-8 items-center gap-1.5 rounded-[6px] px-2.5 text-xs font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        List
                    </button>
                    <button type="button" @click="setView('grid')" :class="view === 'grid' ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'" class="inline-flex h-8 items-center gap-1.5 rounded-[6px] px-2.5 text-xs font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                        Grid
                    </button>
                </div>

                @if(auth()->user()->hasRole('admin', 'staff'))
                    <x-ui.button @click="$dispatch('open-dialog', 'create-product')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah Barang
                    </x-ui.button>
                @endif
            </div>
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
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <div class="space-y-1.5">
                    <x-ui.label for="search" value="Cari" />
                    <x-ui.input id="search" name="search" placeholder="Nama atau kode barang..." :value="request('search')" />
                </div>
                <div class="space-y-1.5">
                    <x-ui.label for="category" value="Kategori" />
                    <x-ui.select id="category" name="category">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
                <div class="space-y-1.5">
                    <x-ui.label for="condition" value="Kondisi" />
                    <x-ui.select id="condition" name="condition">
                        <option value="">Semua Kondisi</option>
                        <option value="baik" {{ request('condition') === 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak_ringan" {{ request('condition') === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="rusak_berat" {{ request('condition') === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </x-ui.select>
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit" class="flex-1 w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        Cari
                    </x-ui.button>
                    <x-ui.button variant="outline" :href="route('products.index')" class="flex-1 w-full">Reset</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- LIST view --}}
        <div x-show="view === 'list'" x-cloak>
            <x-ui.card :padded="false">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b bg-muted/40 text-xs text-muted-foreground">
                                <th class="px-6 py-3 text-left font-medium">Kode</th>
                                <th class="px-6 py-3 text-left font-medium">Nama Barang</th>
                                <th class="px-6 py-3 text-left font-medium">Kategori</th>
                                <th class="px-6 py-3 text-left font-medium">Stok</th>
                                <th class="px-6 py-3 text-left font-medium">Lokasi</th>
                                <th class="px-6 py-3 text-left font-medium">Kondisi</th>
                                <th class="px-6 py-3 text-right font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($products as $product)
                                <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-6 py-3 font-mono text-xs">{{ $product->code }}</td>
                                    <td class="px-6 py-3 font-medium">
                                        <a href="{{ route('products.show', $product) }}" class="hover:text-foreground/70 transition-colors">{{ $product->name }}</a>
                                    </td>
                                    <td class="px-6 py-3 text-muted-foreground">{{ $product->category->name }}</td>
                                    <td class="px-6 py-3">
                                        @if($product->stock === 0)
                                            <x-ui.badge variant="destructive">{{ $product->stock }}</x-ui.badge>
                                        @elseif($product->stock <= 5)
                                            <x-ui.badge variant="warning">{{ $product->stock }}</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="success">{{ $product->stock }}</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-muted-foreground">{{ $product->location ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        @if($product->condition === 'baik')
                                            <x-ui.badge variant="success">Baik</x-ui.badge>
                                        @elseif($product->condition === 'rusak_ringan')
                                            <x-ui.badge variant="warning">Rusak Ringan</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="destructive">Rusak Berat</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <x-ui.button variant="outline" size="sm" :href="route('products.show', $product)">Detail</x-ui.button>
                                            @if(auth()->user()->hasRole('admin', 'staff'))
                                                <x-ui.button
                                                    variant="outline"
                                                    size="sm"
                                                    @click="$dispatch('open-dialog', 'edit-product-{{ $product->id }}')"
                                                >
                                                    Edit
                                                </x-ui.button>
                                                <div x-data="{ showDeleteModal: false }" class="inline-flex">
                                                    <x-ui.button variant="soft-destructive" size="sm" @click="showDeleteModal = true">Hapus</x-ui.button>
                                                    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                        <div @click="showDeleteModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
                                                        <div class="relative flex min-h-screen items-center justify-center p-4">
                                                            <div @click.stop class="relative w-full max-w-md rounded-lg border bg-card p-6 shadow-lg">
                                                                <h3 class="text-lg font-semibold">Konfirmasi Hapus</h3>
                                                                <p class="mt-2 text-sm text-muted-foreground">Yakin ingin menghapus <strong class="text-foreground">{{ $product->name }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                                                                <div class="mt-6 flex justify-end gap-2">
                                                                    <x-ui.button variant="outline" @click="showDeleteModal = false">Batal</x-ui.button>
                                                                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <x-ui.button variant="destructive" type="submit">Hapus</x-ui.button>
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

                                @if(auth()->user()->hasRole('admin', 'staff'))
                                    {{-- Per-row Edit Product Dialog --}}
                                    <x-ui.dialog
                                        name="edit-product-{{ $product->id }}"
                                        title="Edit Barang"
                                        description="Perbarui detail barang."
                                        maxWidth="2xl"
                                    >
                                        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-5">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div class="space-y-1.5">
                                                    <x-ui.label for="edit-code-{{ $product->id }}" value="Kode Barang" />
                                                    <x-ui.input id="edit-code-{{ $product->id }}" name="code" :value="old('code', $product->code)" required />
                                                    <x-input-error :messages="$errors->get('code')" />
                                                </div>
                                                <div class="space-y-1.5">
                                                    <x-ui.label for="edit-name-{{ $product->id }}" value="Nama Barang" />
                                                    <x-ui.input id="edit-name-{{ $product->id }}" name="name" :value="old('name', $product->name)" required />
                                                    <x-input-error :messages="$errors->get('name')" />
                                                </div>
                                                <div class="space-y-1.5">
                                                    <x-ui.label for="edit-category-{{ $product->id }}" value="Kategori" />
                                                    <x-ui.select id="edit-category-{{ $product->id }}" name="category_id" required>
                                                        <option value="">Pilih Kategori</option>
                                                        @foreach($categories as $c)
                                                            <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                        @endforeach
                                                    </x-ui.select>
                                                    <x-input-error :messages="$errors->get('category_id')" />
                                                </div>
                                                <div class="space-y-1.5">
                                                    <x-ui.label for="edit-stock-{{ $product->id }}" value="Stok" />
                                                    <x-ui.input id="edit-stock-{{ $product->id }}" name="stock" type="number" min="0" :value="old('stock', $product->stock)" required />
                                                    <x-input-error :messages="$errors->get('stock')" />
                                                </div>
                                                <div class="space-y-1.5 sm:col-span-2">
                                                    <x-ui.label for="edit-location-{{ $product->id }}" value="Lokasi Penyimpanan" />
                                                    <x-ui.input id="edit-location-{{ $product->id }}" name="location" :value="old('location', $product->location)" placeholder="contoh: Gudang IT Lt. 2" />
                                                    <x-input-error :messages="$errors->get('location')" />
                                                </div>
                                                <div class="space-y-1.5">
                                                    <x-ui.label for="edit-condition-{{ $product->id }}" value="Kondisi" />
                                                    <x-ui.select id="edit-condition-{{ $product->id }}" name="condition" required>
                                                        <option value="baik" {{ old('condition', $product->condition) === 'baik' ? 'selected' : '' }}>Baik</option>
                                                        <option value="rusak_ringan" {{ old('condition', $product->condition) === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                        <option value="rusak_berat" {{ old('condition', $product->condition) === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                                    </x-ui.select>
                                                    <x-input-error :messages="$errors->get('condition')" />
                                                </div>
                                                <div class="space-y-1.5">
                                                    <x-ui.label for="edit-image-{{ $product->id }}" value="Gambar (opsional)" />
                                                    <input id="edit-image-{{ $product->id }}" name="image" type="file" accept="image/jpeg,image/png" class="flex h-9 w-full rounded-md border border-input bg-background text-sm file:mr-3 file:h-full file:border-0 file:border-r file:bg-muted file:px-3 file:text-xs file:font-medium file:text-foreground">
                                                    <p class="text-xs text-muted-foreground">JPG/PNG, maks. 2MB. Kosongkan untuk mempertahankan gambar saat ini.</p>
                                                    <x-input-error :messages="$errors->get('image')" />
                                                </div>
                                                @if($product->image)
                                                    <div class="sm:col-span-2 flex items-center gap-3 rounded-md border bg-muted/40 p-3">
                                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="h-14 w-14 rounded-md object-cover">
                                                        <p class="text-xs text-muted-foreground">Gambar saat ini &mdash; akan dipertahankan jika kolom gambar dikosongkan.</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-end gap-2 border-t pt-4">
                                                <x-ui.button type="button" variant="outline" @click="$dispatch('close-dialog', 'edit-product-{{ $product->id }}')">Batal</x-ui.button>
                                                <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                                            </div>
                                        </form>
                                    </x-ui.dialog>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-muted-foreground">Belum ada data barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($products->hasPages())
                    <div class="border-t p-4">
                        {{ $products->links() }}
                    </div>
                @endif
            </x-ui.card>
        </div>

        {{-- GRID view --}}
        <div x-show="view === 'grid'" x-cloak>
            @if($products->count() > 0)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($products as $product)
                        <a href="{{ route('products.show', $product) }}" class="group flex flex-col rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden transition-all hover:border-muted-foreground/40 hover:shadow-md">
                            <div class="block bg-muted aspect-video overflow-hidden">
                                @if($product->image)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col flex-1 p-4 space-y-3">
                                <div class="space-y-1">
                                    <p class="font-mono text-[10px] uppercase tracking-wider text-muted-foreground">{{ $product->code }}</p>
                                    <p class="font-semibold leading-tight group-hover:text-foreground/80 transition-colors line-clamp-2">{{ $product->name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $product->category->name }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @if($product->stock === 0)
                                        <x-ui.badge variant="destructive">Stok {{ $product->stock }}</x-ui.badge>
                                    @elseif($product->stock <= 5)
                                        <x-ui.badge variant="warning">Stok {{ $product->stock }}</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="success">Stok {{ $product->stock }}</x-ui.badge>
                                    @endif

                                    @if($product->condition === 'baik')
                                        <x-ui.badge variant="secondary">Baik</x-ui.badge>
                                    @elseif($product->condition === 'rusak_ringan')
                                        <x-ui.badge variant="warning">Rusak Ringan</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="destructive">Rusak Berat</x-ui.badge>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                @if($products->hasPages())
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <x-ui.card>
                    <p class="py-12 text-center text-sm text-muted-foreground">Belum ada data barang.</p>
                </x-ui.card>
            @endif
        </div>
    </div>

    {{-- Create Product Dialog --}}
    @if(auth()->user()->hasRole('admin', 'staff'))
        <x-ui.dialog
            name="create-product"
            title="Tambah Barang"
            description="Lengkapi detail barang baru untuk ditambahkan ke inventaris."
            maxWidth="2xl"
        >
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <x-ui.label for="p-code" value="Kode Barang" />
                        <x-ui.input id="p-code" value="Otomatis dibuat" readonly class="cursor-not-allowed bg-muted text-muted-foreground" />
                        <p class="text-xs text-muted-foreground">Kode dihasilkan otomatis berdasarkan kategori.</p>
                    </div>
                    <div class="space-y-1.5">
                        <x-ui.label for="p-name" value="Nama Barang" />
                        <x-ui.input id="p-name" name="name" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>
                    <div class="space-y-1.5">
                        <x-ui.label for="p-category" value="Kategori" />
                        <x-ui.select id="p-category" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('category_id')" />
                    </div>
                    <div class="space-y-1.5">
                        <x-ui.label for="p-stock" value="Stok" />
                        <x-ui.input id="p-stock" name="stock" type="number" :value="old('stock', 0)" min="0" required />
                        <x-input-error :messages="$errors->get('stock')" />
                    </div>
                    <div class="space-y-1.5 sm:col-span-2">
                        <x-ui.label for="p-location" value="Lokasi Penyimpanan" />
                        <x-ui.input id="p-location" name="location" :value="old('location')" placeholder="contoh: Gudang IT Lt. 2" />
                        <x-input-error :messages="$errors->get('location')" />
                    </div>
                    <div class="space-y-1.5">
                        <x-ui.label for="p-condition" value="Kondisi" />
                        <x-ui.select id="p-condition" name="condition" required>
                            <option value="baik" {{ old('condition', 'baik') === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ old('condition') === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak_berat" {{ old('condition') === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('condition')" />
                    </div>
                    <div class="space-y-1.5">
                        <x-ui.label for="p-image" value="Gambar (opsional)" />
                        <input id="p-image" name="image" type="file" accept="image/jpeg,image/png" class="flex h-9 w-full rounded-md border border-input bg-background text-sm file:mr-3 file:h-full file:border-0 file:border-r file:bg-muted file:px-3 file:text-xs file:font-medium file:text-foreground">
                        <p class="text-xs text-muted-foreground">JPG/PNG, maks. 2MB</p>
                        <x-input-error :messages="$errors->get('image')" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t pt-4">
                    <x-ui.button type="button" variant="outline" @click="$dispatch('close-dialog', 'create-product')">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan Barang</x-ui.button>
                </div>
            </form>
        </x-ui.dialog>

        @if($errors->any() || request('create'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('open-dialog', { detail: 'create-product' }));
                });
            </script>
        @endif

        @if(request('edit'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('open-dialog', { detail: 'edit-product-{{ (int) request('edit') }}' }));
                });
            </script>
        @endif
    @endif
</x-app-layout>

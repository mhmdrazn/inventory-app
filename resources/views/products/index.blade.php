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
                    <x-ui.button type="submit">Cari</x-ui.button>
                    <x-ui.button variant="outline" :href="route('products.index')">Reset</x-ui.button>
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
                                        <a href="{{ route('products.show', $product) }}" class="hover:text-primary transition-colors">{{ $product->name }}</a>
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
                                                <x-ui.button variant="outline" size="sm" :href="route('products.edit', $product)">Edit</x-ui.button>
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
                        <x-ui.card :padded="false" class="overflow-hidden flex flex-col">
                            <a href="{{ route('products.show', $product) }}" class="block bg-muted aspect-video overflow-hidden">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                                    </div>
                                @endif
                            </a>
                            <div class="flex flex-col flex-1 p-4 space-y-3">
                                <div class="space-y-1">
                                    <p class="font-mono text-[10px] uppercase tracking-wider text-muted-foreground">{{ $product->code }}</p>
                                    <a href="{{ route('products.show', $product) }}" class="font-semibold leading-tight hover:text-primary transition-colors line-clamp-2">{{ $product->name }}</a>
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
                                <div class="flex-1"></div>
                                <div class="flex items-center gap-1.5 pt-1 border-t">
                                    <x-ui.button variant="ghost" size="sm" :href="route('products.show', $product)" class="flex-1">Detail</x-ui.button>
                                    @if(auth()->user()->hasRole('admin', 'staff'))
                                        <x-ui.button variant="outline" size="sm" :href="route('products.edit', $product)">Edit</x-ui.button>
                                        <div x-data="{ showDeleteModal: false }">
                                            <x-ui.button variant="soft-destructive" size="sm" @click="showDeleteModal = true">Hapus</x-ui.button>
                                            <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                <div @click="showDeleteModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
                                                <div class="relative flex min-h-screen items-center justify-center p-4">
                                                    <div @click.stop class="relative w-full max-w-md rounded-lg border bg-card p-6 shadow-lg">
                                                        <h3 class="text-lg font-semibold">Konfirmasi Hapus</h3>
                                                        <p class="mt-2 text-sm text-muted-foreground">Yakin ingin menghapus <strong class="text-foreground">{{ $product->name }}</strong>?</p>
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
                            </div>
                        </x-ui.card>
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
                        <x-ui.input id="p-code" name="code" :value="old('code')" required placeholder="contoh: INV-ELK-001" />
                        <x-input-error :messages="$errors->get('code')" />
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
    @endif
</x-app-layout>

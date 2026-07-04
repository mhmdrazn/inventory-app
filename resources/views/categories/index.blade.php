<x-app-layout>
    <x-slot name="header">Kategori</x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Daftar Kategori</h1>
                <p class="text-sm text-muted-foreground">Kelola kategori barang.</p>
            </div>
            <x-ui.button :href="route('categories.create')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Kategori
            </x-ui.button>
        </div>

        @if(session('success'))
            <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
        @endif
        @if(session('error'))
            <x-ui.alert variant="destructive">{{ session('error') }}</x-ui.alert>
        @endif

        <x-ui.card :padded="false">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b bg-muted/40 text-xs text-muted-foreground">
                            <th class="px-6 py-3 text-left font-medium">Nama Kategori</th>
                            <th class="px-6 py-3 text-left font-medium">Jumlah Produk</th>
                            <th class="px-6 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($categories as $category)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $category->name }}</td>
                                <td class="px-6 py-3">
                                    <x-ui.badge variant="secondary">{{ $category->products_count }} produk</x-ui.badge>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button variant="outline" size="sm" :href="route('categories.edit', $category)">Edit</x-ui.button>
                                        <div x-data="{ showDeleteModal: false }" class="inline-flex">
                                            <x-ui.button variant="soft-destructive" size="sm" @click="showDeleteModal = true">Hapus</x-ui.button>
                                            <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                <div @click="showDeleteModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
                                                <div class="relative flex min-h-screen items-center justify-center p-4">
                                                    <div @click.stop class="relative w-full max-w-md rounded-lg border bg-card p-6 shadow-lg">
                                                        <h3 class="text-lg font-semibold">Konfirmasi Hapus</h3>
                                                        <p class="mt-2 text-sm text-muted-foreground">Yakin ingin menghapus kategori <strong class="text-foreground">{{ $category->name }}</strong>?</p>
                                                        @if($category->products_count > 0)
                                                            <div class="mt-4">
                                                                <x-ui.alert variant="warning">
                                                                    Kategori ini memiliki {{ $category->products_count }} produk dan tidak dapat dihapus.
                                                                </x-ui.alert>
                                                            </div>
                                                        @endif
                                                        <div class="mt-6 flex justify-end gap-2">
                                                            <x-ui.button variant="outline" @click="showDeleteModal = false">Batal</x-ui.button>
                                                            @if($category->products_count === 0)
                                                                <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <x-ui.button variant="destructive" type="submit">Hapus</x-ui.button>
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
                                <td colspan="3" class="px-6 py-12 text-center text-sm text-muted-foreground">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>

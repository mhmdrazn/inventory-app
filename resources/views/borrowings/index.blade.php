<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Daftar Peminjaman</h2>
            <a href="{{ route('borrowings.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition ease-in-out duration-150">
                + Peminjaman Baru
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
                    <form method="GET" action="{{ route('borrowings.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                        <div>
                            <x-input-label for="search" value="Cari Peminjam" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" placeholder="Nama peminjam..." :value="request('search')" />
                        </div>
                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="date_from" value="Dari Tanggal" />
                            <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="request('date_from')" />
                        </div>
                        <div>
                            <x-input-label for="date_to" value="Sampai Tanggal" />
                            <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" :value="request('date_to')" />
                        </div>
                        <div class="flex items-end gap-2">
                            <x-primary-button>Filter</x-primary-button>
                            <a href="{{ route('borrowings.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">Reset</a>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Peminjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Pinjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jatuh Tempo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($borrowings as $borrowing)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrowing->borrower_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $borrowing->borrowingDetails->sum('quantity') }} barang</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $borrowing->borrowed_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $borrowing->due_at->format('d/m/Y') }}
                                            @if($borrowing->isOverdue())
                                                <span class="text-red-500 text-xs">(terlambat)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($borrowing->status === 'dipinjam')
                                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Dipinjam</span>
                                            @elseif($borrowing->status === 'dikembalikan')
                                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-400">Dikembalikan</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">Terlambat</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('borrowings.show', $borrowing) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Detail</a>
                                                @if($borrowing->status === 'dipinjam')
                                                    <form method="POST" action="{{ route('borrowings.return', $borrowing) }}" onsubmit="return confirm('Konfirmasi pengembalian barang?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">Kembalikan</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data peminjaman.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $borrowings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

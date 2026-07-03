<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Detail Peminjaman</h2>
            <a href="{{ route('borrowings.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">&larr; Kembali</a>
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

            <!-- Info Peminjaman -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Peminjam</p>
                                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $borrowing->borrower_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Diinput Oleh</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ $borrowing->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                                @if($borrowing->status === 'dipinjam')
                                    <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Dipinjam</span>
                                    @if($borrowing->isOverdue())
                                        <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-400 ml-1">Terlambat</span>
                                    @endif
                                @elseif($borrowing->status === 'dikembalikan')
                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-400">Dikembalikan</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-400">Terlambat</span>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pinjam</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ $borrowing->borrowed_at->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jatuh Tempo</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ $borrowing->due_at->format('d/m/Y') }}</p>
                            </div>
                            @if($borrowing->returned_at)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Kembali</p>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $borrowing->returned_at->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($borrowing->notes)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan</p>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $borrowing->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($borrowing->status === 'dipinjam')
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ route('borrowings.return', $borrowing) }}" onsubmit="return confirm('Konfirmasi pengembalian semua barang?')">
                                @csrf
                                @method('PATCH')
                                <x-primary-button>Proses Pengembalian</x-primary-button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Daftar Barang -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daftar Barang Dipinjam</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($borrowing->borrowingDetails as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">{{ $detail->product->code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $detail->product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $detail->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

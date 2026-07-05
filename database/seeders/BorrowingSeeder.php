<?php

namespace Database\Seeders;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BorrowingSeeder extends Seeder
{
    /**
     * Seed borrowings with realistic historical data spanning the last 12 months.
     */
    public function run(): void
    {
        $staff = User::whereHas('role', fn ($q) => $q->where('name', Role::STAFF))->first();
        $admin = User::whereHas('role', fn ($q) => $q->where('name', Role::ADMIN))->first();

        if (! $staff || ! $admin) {
            $this->command->warn('Skipping BorrowingSeeder: staff or admin user not found.');

            return;
        }

        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('Skipping BorrowingSeeder: no products found.');

            return;
        }

        $borrowers = [
            'Budi Santoso', 'Siti Rahayu', 'Ahmad Fauzi', 'Dewi Kartika',
            'Rizky Pratama', 'Nur Hidayah', 'Fajar Setiawan', 'Rina Marlina',
            'Hendra Wijaya', 'Lina Kusuma', 'Doni Saputra', 'Maya Anggraeni',
        ];

        $notes = [
            'Untuk kegiatan meeting rutin divisi.',
            'Keperluan presentasi klien.',
            'Perbaikan ruang kerja lantai 3.',
            'Event gathering tahunan.',
            'Kebutuhan operasional harian.',
            'Training karyawan baru.',
            'Pemeliharaan jaringan kantor.',
            'Audit inventaris kuartal.',
            null,
            null,
        ];

        $records = [
            // 11 months ago
            ['months_ago' => 11, 'duration_days' => 7, 'status' => 'dikembalikan', 'borrower' => 0, 'product_indices' => [0], 'quantities' => [2]],
            ['months_ago' => 11, 'duration_days' => 14, 'status' => 'dikembalikan', 'borrower' => 1, 'product_indices' => [10, 11], 'quantities' => [5, 3]],

            // 10 months ago
            ['months_ago' => 10, 'duration_days' => 5, 'status' => 'dikembalikan', 'borrower' => 2, 'product_indices' => [3], 'quantities' => [1]],

            // 9 months ago
            ['months_ago' => 9, 'duration_days' => 10, 'status' => 'dikembalikan', 'borrower' => 3, 'product_indices' => [6, 7], 'quantities' => [1, 2]],
            ['months_ago' => 9, 'duration_days' => 7, 'status' => 'dikembalikan', 'borrower' => 4, 'product_indices' => [13], 'quantities' => [1]],

            // 8 months ago
            ['months_ago' => 8, 'duration_days' => 14, 'status' => 'dikembalikan', 'borrower' => 5, 'product_indices' => [0, 1], 'quantities' => [1, 2]],
            ['months_ago' => 8, 'duration_days' => 3, 'status' => 'dikembalikan', 'borrower' => 6, 'product_indices' => [12], 'quantities' => [3]],

            // 7 months ago
            ['months_ago' => 7, 'duration_days' => 7, 'status' => 'dikembalikan', 'borrower' => 7, 'product_indices' => [2, 11], 'quantities' => [1, 2]],
            ['months_ago' => 7, 'duration_days' => 21, 'status' => 'dikembalikan', 'borrower' => 8, 'product_indices' => [16], 'quantities' => [1]],

            // 6 months ago
            ['months_ago' => 6, 'duration_days' => 5, 'status' => 'dikembalikan', 'borrower' => 9, 'product_indices' => [4, 5], 'quantities' => [1, 1]],
            ['months_ago' => 6, 'duration_days' => 14, 'status' => 'dikembalikan', 'borrower' => 0, 'product_indices' => [8], 'quantities' => [1]],
            ['months_ago' => 6, 'duration_days' => 7, 'status' => 'dikembalikan', 'borrower' => 10, 'product_indices' => [14], 'quantities' => [2]],

            // 5 months ago
            ['months_ago' => 5, 'duration_days' => 10, 'status' => 'dikembalikan', 'borrower' => 1, 'product_indices' => [0, 3], 'quantities' => [1, 1]],
            ['months_ago' => 5, 'duration_days' => 7, 'status' => 'dikembalikan', 'borrower' => 11, 'product_indices' => [9], 'quantities' => [2]],

            // 4 months ago
            ['months_ago' => 4, 'duration_days' => 14, 'status' => 'dikembalikan', 'borrower' => 2, 'product_indices' => [1, 5, 10], 'quantities' => [1, 1, 10]],
            ['months_ago' => 4, 'duration_days' => 5, 'status' => 'dikembalikan', 'borrower' => 3, 'product_indices' => [17], 'quantities' => [1]],

            // 3 months ago
            ['months_ago' => 3, 'duration_days' => 7, 'status' => 'dikembalikan', 'borrower' => 4, 'product_indices' => [6, 7], 'quantities' => [2, 3]],
            ['months_ago' => 3, 'duration_days' => 14, 'status' => 'dikembalikan', 'borrower' => 5, 'product_indices' => [0, 2], 'quantities' => [1, 1]],
            ['months_ago' => 3, 'duration_days' => 10, 'status' => 'dikembalikan', 'borrower' => 6, 'product_indices' => [15], 'quantities' => [1]],

            // 2 months ago
            ['months_ago' => 2, 'duration_days' => 7, 'status' => 'dikembalikan', 'borrower' => 7, 'product_indices' => [3, 4], 'quantities' => [1, 2]],
            ['months_ago' => 2, 'duration_days' => 14, 'status' => 'dikembalikan', 'borrower' => 8, 'product_indices' => [10, 12], 'quantities' => [5, 2]],

            // 1 month ago
            ['months_ago' => 1, 'duration_days' => 10, 'status' => 'dikembalikan', 'borrower' => 9, 'product_indices' => [0, 1], 'quantities' => [1, 1]],
            ['months_ago' => 1, 'duration_days' => 7, 'status' => 'dikembalikan', 'borrower' => 10, 'product_indices' => [16, 17], 'quantities' => [1, 1]],
            ['months_ago' => 1, 'duration_days' => 5, 'status' => 'dikembalikan', 'borrower' => 11, 'product_indices' => [8, 9], 'quantities' => [1, 1]],

            // Current month — active borrowings (stock is reduced)
            ['months_ago' => 0, 'duration_days' => 14, 'status' => 'dipinjam', 'borrower' => 0, 'product_indices' => [0, 5], 'quantities' => [2, 1]],
            ['months_ago' => 0, 'duration_days' => 7, 'status' => 'dipinjam', 'borrower' => 2, 'product_indices' => [3], 'quantities' => [1]],

            // Overdue — borrowed 2 months ago, still not returned
            ['months_ago' => 2, 'duration_days' => 14, 'status' => 'terlambat', 'borrower' => 4, 'product_indices' => [13, 14], 'quantities' => [1, 1]],
        ];

        $productList = $products->values();

        foreach ($records as $index => $record) {
            $borrowedAt = Carbon::now()->subMonths($record['months_ago'])->subDays($index % 15);
            $dueAt = $borrowedAt->copy()->addDays($record['duration_days']);

            $returnedAt = null;
            if ($record['status'] === 'dikembalikan') {
                $returnedAt = $dueAt->copy()->subDays($index % 3);
            }

            $borrowing = Borrowing::create([
                'user_id' => $index % 2 === 0 ? $staff->id : $admin->id,
                'approved_by' => $admin->id,
                'borrower_name' => $borrowers[$record['borrower']],
                'status' => $record['status'],
                'borrowed_at' => $borrowedAt,
                'due_at' => $dueAt,
                'returned_at' => $returnedAt,
                'notes' => $notes[$index % count($notes)],
            ]);

            foreach ($record['product_indices'] as $i => $productIndex) {
                $product = $productList[$productIndex % $productList->count()];
                $qty = $record['quantities'][$i];

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'condition_on_return' => $record['status'] === 'dikembalikan' ? 'baik' : null,
                ]);

                if ($record['status'] !== 'dikembalikan') {
                    $product->decrement('stock', $qty);
                }
            }
        }
    }
}

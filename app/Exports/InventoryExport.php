<?php

namespace App\Exports;

use App\Models\Borrowing;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        public ?Carbon $dateFrom = null,
        public ?Carbon $dateTo = null,
        public ?string $status = null,
    ) {}

    /**
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new InventarisSheet,
            new PeminjamanSheet($this->dateFrom, $this->dateTo, $this->status),
        ];
    }
}

class InventarisSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function collection(): Collection
    {
        return Product::with('category')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'code' => $product->code,
                'name' => $product->name,
                'category' => $product->category?->name ?? '-',
                'stock' => $product->stock,
                'location' => $product->location ?? '-',
                'condition' => str_replace('_', ' ', $product->condition),
            ])
            ->values();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Kode', 'Nama Barang', 'Kategori', 'Stok', 'Lokasi', 'Kondisi'];
    }

    public function title(): string
    {
        return 'Inventaris';
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1C1917'],
                ],
            ],
        ];
    }
}

class PeminjamanSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        public ?Carbon $dateFrom = null,
        public ?Carbon $dateTo = null,
        public ?string $status = null,
    ) {}

    public function collection(): Collection
    {
        return Borrowing::with(['user', 'borrowingDetails.product'])
            ->when($this->dateFrom, fn ($q) => $q->where('borrowed_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('borrowed_at', '<=', $this->dateTo))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest('borrowed_at')
            ->get()
            ->map(fn (Borrowing $borrowing) => [
                'borrower' => $borrowing->borrower_name,
                'input_by' => $borrowing->user?->name ?? '-',
                'borrowed_at' => $borrowing->borrowed_at?->format('Y-m-d'),
                'due_at' => $borrowing->due_at?->format('Y-m-d'),
                'returned_at' => $borrowing->returned_at?->format('Y-m-d') ?? '-',
                'status' => ucfirst($borrowing->status),
                'items' => $borrowing->borrowingDetails
                    ->map(fn ($detail) => ($detail->product->name ?? '-').' ('.$detail->quantity.')')
                    ->implode(', '),
                'total_qty' => $borrowing->borrowingDetails->sum('quantity'),
            ])
            ->values();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Peminjam',
            'Diinput Oleh',
            'Tanggal Pinjam',
            'Jatuh Tempo',
            'Dikembalikan',
            'Status',
            'Barang Dipinjam',
            'Total Qty',
        ];
    }

    public function title(): string
    {
        return 'Peminjaman';
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1C1917'],
                ],
            ],
        ];
    }
}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris PT Telkomsel</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #0c0a09;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #E8232C;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
            color: #0c0a09;
        }
        .header .meta {
            font-size: 10px;
            color: #78716c;
        }
        .section {
            margin-bottom: 22px;
        }
        .section h2 {
            font-size: 13px;
            margin: 0 0 8px 0;
            padding: 6px 8px;
            background: #fafaf9;
            border-left: 3px solid #E8232C;
            color: #0c0a09;
        }
        .filter-note {
            font-size: 10px;
            color: #78716c;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        thead th {
            background: #1c1917;
            color: #ffffff;
            text-align: left;
            padding: 6px 8px;
            font-weight: 600;
            border: 1px solid #1c1917;
        }
        tbody td {
            padding: 5px 8px;
            border: 1px solid #e8e6e5;
            color: #0c0a09;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td {
            background: #fafaf9;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-baik { background: #dcfce7; color: #166534; }
        .badge-rusak_ringan { background: #fef9c3; color: #854d0e; }
        .badge-rusak_berat { background: #fee2e2; color: #991b1b; }
        .badge-dipinjam { background: #dbeafe; color: #1e40af; }
        .badge-dikembalikan { background: #dcfce7; color: #166534; }
        .badge-terlambat { background: #fee2e2; color: #991b1b; }
        .text-right { text-align: right; }
        .text-muted { color: #78716c; }
        .empty {
            padding: 12px;
            text-align: center;
            color: #78716c;
            border: 1px dashed #e8e6e5;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #78716c;
            border-top: 1px solid #e8e6e5;
            padding-top: 6px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Inventaris PT Telkomsel</h1>
        <div class="meta">
            Digenerate pada: {{ $generatedAt->translatedFormat('d F Y H:i') }} WIB
        </div>
    </div>

    <div class="section">
        <h2>Daftar Barang ({{ $products->count() }})</h2>
        @if($products->isEmpty())
            <div class="empty">Belum ada data barang.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 12%;">Kode</th>
                        <th style="width: 32%;">Nama Barang</th>
                        <th style="width: 18%;">Kategori</th>
                        <th style="width: 8%;" class="text-right">Stok</th>
                        <th style="width: 14%;">Lokasi</th>
                        <th style="width: 12%;">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                            <td class="text-right">{{ number_format($product->stock) }}</td>
                            <td>{{ $product->location ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $product->condition }}">
                                    {{ str_replace('_', ' ', $product->condition) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <h2>Riwayat Peminjaman ({{ $borrowings->count() }})</h2>
        @if($filters['date_from'] || $filters['date_to'] || $filters['status'])
            <div class="filter-note">
                Filter:
                @if($filters['date_from'])
                    Dari {{ $filters['date_from']->format('d/m/Y') }}
                @endif
                @if($filters['date_to'])
                    &mdash; Sampai {{ $filters['date_to']->format('d/m/Y') }}
                @endif
                @if($filters['status'])
                    &mdash; Status: {{ ucfirst($filters['status']) }}
                @endif
            </div>
        @endif

        @if($borrowings->isEmpty())
            <div class="empty">Tidak ada peminjaman pada rentang filter.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 18%;">Peminjam</th>
                        <th style="width: 11%;">Tgl Pinjam</th>
                        <th style="width: 11%;">Jatuh Tempo</th>
                        <th style="width: 11%;">Dikembalikan</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 33%;">Barang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($borrowings as $index => $borrowing)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $borrowing->borrower_name }}</td>
                            <td>{{ $borrowing->borrowed_at?->format('d/m/Y') }}</td>
                            <td>{{ $borrowing->due_at?->format('d/m/Y') }}</td>
                            <td>{{ $borrowing->returned_at?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $borrowing->status }}">
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                            </td>
                            <td>
                                @foreach($borrowing->borrowingDetails as $detail)
                                    <div>
                                        {{ $detail->product->name ?? '-' }} ({{ $detail->quantity }})
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="footer">
        Digenerate oleh {{ $generatedBy }} pada {{ $generatedAt->translatedFormat('d F Y H:i') }} WIB
    </div>
</body>
</html>

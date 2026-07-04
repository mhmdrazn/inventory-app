<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the products table with realistic Telkomsel office inventory.
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');

        $products = [
            // Elektronik (ELK)
            ['code' => 'INV-ELK-001', 'name' => 'Laptop Dell Latitude 5540', 'category' => 'Elektronik', 'stock' => 10, 'location' => 'Gudang IT Lt. 2', 'condition' => 'baik', 'image_keyword' => 'laptop,dell'],
            ['code' => 'INV-ELK-002', 'name' => 'Monitor LG 24"', 'category' => 'Elektronik', 'stock' => 15, 'location' => 'Gudang IT Lt. 2', 'condition' => 'baik', 'image_keyword' => 'monitor,display'],
            ['code' => 'INV-ELK-003', 'name' => 'Printer HP LaserJet Pro', 'category' => 'Elektronik', 'stock' => 8, 'location' => 'Ruang Admin Lt. 1', 'condition' => 'rusak_ringan', 'image_keyword' => 'printer,office'],
            ['code' => 'INV-ELK-004', 'name' => 'Proyektor Epson EB-X51', 'category' => 'Elektronik', 'stock' => 4, 'location' => 'Ruang Meeting Lt. 3', 'condition' => 'baik', 'image_keyword' => 'projector,presentation'],
            ['code' => 'INV-ELK-005', 'name' => 'UPS APC 1100VA', 'category' => 'Elektronik', 'stock' => 12, 'location' => 'Gudang IT Lt. 2', 'condition' => 'baik', 'image_keyword' => 'ups,battery'],
            ['code' => 'INV-ELK-006', 'name' => 'Webcam Logitech C920', 'category' => 'Elektronik', 'stock' => 6, 'location' => 'Gudang IT Lt. 2', 'condition' => 'baik', 'image_keyword' => 'webcam,camera'],

            // Furniture (FRN)
            ['code' => 'INV-FRN-001', 'name' => 'Meja Kerja Ergonomis', 'category' => 'Furniture', 'stock' => 20, 'location' => 'Gudang Umum Lt. 1', 'condition' => 'baik', 'image_keyword' => 'office,desk'],
            ['code' => 'INV-FRN-002', 'name' => 'Kursi Kantor Ergonomis', 'category' => 'Furniture', 'stock' => 25, 'location' => 'Gudang Umum Lt. 1', 'condition' => 'baik', 'image_keyword' => 'office,chair'],
            ['code' => 'INV-FRN-003', 'name' => 'Lemari Arsip Besi 4 Laci', 'category' => 'Furniture', 'stock' => 10, 'location' => 'Gudang Umum Lt. 1', 'condition' => 'rusak_ringan', 'image_keyword' => 'filing,cabinet'],
            ['code' => 'INV-FRN-004', 'name' => 'Whiteboard Magnetic 120x90', 'category' => 'Furniture', 'stock' => 8, 'location' => 'Ruang Meeting Lt. 3', 'condition' => 'baik', 'image_keyword' => 'whiteboard,office'],

            // ATK (ATK)
            ['code' => 'INV-ATK-001', 'name' => 'Kertas HVS A4 80gsm (rim)', 'category' => 'ATK (Alat Tulis Kantor)', 'stock' => 50, 'location' => 'Gudang ATK Lt. 1', 'condition' => 'baik', 'image_keyword' => 'paper,stack'],
            ['code' => 'INV-ATK-002', 'name' => 'Tinta Printer HP 680 Black', 'category' => 'ATK (Alat Tulis Kantor)', 'stock' => 20, 'location' => 'Gudang ATK Lt. 1', 'condition' => 'baik', 'image_keyword' => 'ink,cartridge'],
            ['code' => 'INV-ATK-003', 'name' => 'Stapler Kangaro HD-10D', 'category' => 'ATK (Alat Tulis Kantor)', 'stock' => 15, 'location' => 'Gudang ATK Lt. 1', 'condition' => 'baik', 'image_keyword' => 'stapler,stationery'],

            // Perangkat Jaringan (PJR)
            ['code' => 'INV-PJR-001', 'name' => 'Router Cisco ISR 4321', 'category' => 'Perangkat Jaringan', 'stock' => 5, 'location' => 'Ruang Server Lt. 2', 'condition' => 'baik', 'image_keyword' => 'router,network'],
            ['code' => 'INV-PJR-002', 'name' => 'Switch Cisco Catalyst 2960', 'category' => 'Perangkat Jaringan', 'stock' => 8, 'location' => 'Ruang Server Lt. 2', 'condition' => 'baik', 'image_keyword' => 'network,switch,server'],
            ['code' => 'INV-PJR-003', 'name' => 'Access Point Ubiquiti UniFi 6', 'category' => 'Perangkat Jaringan', 'stock' => 10, 'location' => 'Ruang Server Lt. 2', 'condition' => 'rusak_berat', 'image_keyword' => 'wifi,router,access-point'],

            // Kendaraan (KDR)
            ['code' => 'INV-KDR-001', 'name' => 'Mobil Operasional Avanza', 'category' => 'Kendaraan', 'stock' => 3, 'location' => 'Parkir Basement', 'condition' => 'baik', 'image_keyword' => 'car,mpv,minivan'],
            ['code' => 'INV-KDR-002', 'name' => 'Motor Honda Beat (Kurir)', 'category' => 'Kendaraan', 'stock' => 5, 'location' => 'Parkir Basement', 'condition' => 'baik', 'image_keyword' => 'scooter,motorcycle'],
        ];

        foreach ($products as $index => $product) {
            $seed = crc32($product['code']);
            $imageUrl = "https://loremflickr.com/600/400/{$product['image_keyword']}?lock={$seed}";

            Product::updateOrCreate(
                ['code' => $product['code']],
                [
                    'name' => $product['name'],
                    'category_id' => $categories[$product['category']]->id,
                    'stock' => $product['stock'],
                    'location' => $product['location'],
                    'condition' => $product['condition'],
                    'image' => $imageUrl,
                ],
            );
        }
    }
}

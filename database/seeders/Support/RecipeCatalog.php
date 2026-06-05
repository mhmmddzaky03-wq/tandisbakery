<?php

namespace Database\Seeders\Support;

/**
 * Master data resep & harga bahan baku realistis (pasar Indonesia 2025–2026).
 * Sumber acuan: pengeluaran Juni 2025 Tandi's Bakery + harga pasar umum.
 */
final class RecipeCatalog
{
    /** @return array<int, array{id: string, nama: string, kategori: string, satuan: string, harga: int, min: float}> */
    public static function rawMaterials(): array
    {
        return [
            ['id' => 'SBB001', 'nama' => 'Tepung Terigu Protein Tinggi', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 14_500, 'min' => 8],
            ['id' => 'SBB002', 'nama' => 'Tepung Terigu Protein Sedang', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 12_800, 'min' => 5],
            ['id' => 'SBB003', 'nama' => 'Tepung Terigu Protein Rendah', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 13_200, 'min' => 3],
            ['id' => 'SBB004', 'nama' => 'Gula Pasir Murni', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 14_500, 'min' => 5],
            ['id' => 'SBB005', 'nama' => 'Gula Halus', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 16_500, 'min' => 2],
            ['id' => 'SBB006', 'nama' => 'Susu Skim Bubuk', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 82_000, 'min' => 1],
            ['id' => 'SBB007', 'nama' => 'Ragi Instan Bubuk', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 42_000, 'min' => 0.5],
            ['id' => 'SBB008', 'nama' => 'Garam Halus', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 6_000, 'min' => 1],
            ['id' => 'SBB009', 'nama' => 'Susu UHT Full Cream', 'kategori' => 'basah', 'satuan' => 'L', 'harga' => 17_690, 'min' => 5],
            ['id' => 'SBB010', 'nama' => 'Telur Ayam Utuh', 'kategori' => 'basah', 'satuan' => 'pcs', 'harga' => 1_600, 'min' => 60],
            ['id' => 'SBB011', 'nama' => 'Kuning Telur Ayam', 'kategori' => 'basah', 'satuan' => 'pcs', 'harga' => 900, 'min' => 20],
            ['id' => 'SBB012', 'nama' => 'Air Bersih', 'kategori' => 'basah', 'satuan' => 'L', 'harga' => 0, 'min' => 0],
            ['id' => 'SBB013', 'nama' => 'Mentega Unsalted', 'kategori' => 'padat', 'satuan' => 'kg', 'harga' => 92_000, 'min' => 2],
            ['id' => 'SBB014', 'nama' => 'Isian Cokelat Bake-Stable', 'kategori' => 'padat', 'satuan' => 'kg', 'harga' => 115_000, 'min' => 1],
            ['id' => 'SBB015', 'nama' => 'Tepung Maizena', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 17_000, 'min' => 2],
            ['id' => 'SBB016', 'nama' => 'Vanili Bubuk', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 380_000, 'min' => 0.1],
            ['id' => 'SBB017', 'nama' => 'Dark Cooking Chocolate (DCC)', 'kategori' => 'padat', 'satuan' => 'kg', 'harga' => 293_900, 'min' => 1],
            ['id' => 'SBB018', 'nama' => 'Margarin Berkualitas Tinggi', 'kategori' => 'padat', 'satuan' => 'kg', 'harga' => 32_000, 'min' => 3],
            ['id' => 'SBB019', 'nama' => 'Pisang Uli / Raja', 'kategori' => 'basah', 'satuan' => 'pcs', 'harga' => 3_500, 'min' => 15],
            ['id' => 'SBB020', 'nama' => 'Minyak Goreng', 'kategori' => 'basah', 'satuan' => 'L', 'harga' => 17_500, 'min' => 5],
            ['id' => 'SBB021', 'nama' => 'Cokelat Bubuk Pekat', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 82_000, 'min' => 1],
            ['id' => 'SBB022', 'nama' => 'Susu Bubuk Full Cream', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 88_000, 'min' => 1],
            ['id' => 'SBB023', 'nama' => 'Baking Powder', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 42_000, 'min' => 0.5],
            ['id' => 'SBB024', 'nama' => 'Pasta Cokelat Cair', 'kategori' => 'basah', 'satuan' => 'kg', 'harga' => 125_000, 'min' => 0.5],
            ['id' => 'SBB025', 'nama' => 'Sirup Ceri Hitam', 'kategori' => 'basah', 'satuan' => 'L', 'harga' => 35_000, 'min' => 0.5],
            ['id' => 'SBB026', 'nama' => 'Whipping Cream Dairy', 'kategori' => 'basah', 'satuan' => 'L', 'harga' => 48_000, 'min' => 2],
            ['id' => 'SBB027', 'nama' => 'Cake Emulsifier (SP/TBM)', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 38_000, 'min' => 0.5],
            ['id' => 'SBB028', 'nama' => 'Ceri Hitam Kaleng', 'kategori' => 'padat', 'satuan' => 'pcs', 'harga' => 42_000, 'min' => 2],
            ['id' => 'SBB029', 'nama' => 'Wincheez Custom (B) 8 x 2 kg', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 54_323, 'min' => 8],
            ['id' => 'SBB030', 'nama' => 'Kismis Hitam 1kg USA Premium', 'kategori' => 'kering', 'satuan' => 'kg', 'harga' => 50_000, 'min' => 2],
        ];
    }

    /** @return array<int, array{id: string, nama: string, satuan: string, min: float}> */
    public static function bahanDasar(): array
    {
        return [
            ['id' => 'BD001', 'nama' => 'Adonan Roti Manis', 'satuan' => 'gram', 'min' => 500],
            ['id' => 'BD002', 'nama' => 'Adonan Kulit Éclair', 'satuan' => 'gram', 'min' => 300],
            ['id' => 'BD003', 'nama' => 'Vla Cokelat Éclair', 'satuan' => 'ml', 'min' => 200],
            ['id' => 'BD004', 'nama' => 'Adonan Kulit Kue Sus', 'satuan' => 'gram', 'min' => 300],
            ['id' => 'BD005', 'nama' => 'Vla Vanilla Kue Sus', 'satuan' => 'ml', 'min' => 200],
            ['id' => 'BD006', 'nama' => 'Adonan Kulit Pisang Molen', 'satuan' => 'gram', 'min' => 300],
        ];
    }

    /**
     * BOM bahan dasar: bahan baku per 1 batch standar.
     *
     * @return array<string, array{output: float, output_unit: string, lines: array<int, array{id: string, qty: float, unit: string}>}>
     */
    public static function bahanDasarBom(): array
    {
        return [
            'BD001' => [
                'output' => 800,
                'output_unit' => 'gram',
                'lines' => [
                    ['id' => 'SBB001', 'qty' => 0.2, 'unit' => 'kg'],
                    ['id' => 'SBB002', 'qty' => 0.05, 'unit' => 'kg'],
                    ['id' => 'SBB004', 'qty' => 0.05, 'unit' => 'kg'],
                    ['id' => 'SBB006', 'qty' => 0.015, 'unit' => 'kg'],
                    ['id' => 'SBB007', 'qty' => 0.006, 'unit' => 'kg'],
                    ['id' => 'SBB008', 'qty' => 0.002, 'unit' => 'kg'],
                    ['id' => 'SBB009', 'qty' => 0.12, 'unit' => 'L'],
                    ['id' => 'SBB011', 'qty' => 2, 'unit' => 'pcs'],
                    ['id' => 'SBB012', 'qty' => 0.02, 'unit' => 'L'],
                    ['id' => 'SBB013', 'qty' => 0.04, 'unit' => 'kg'],
                ],
            ],
            'BD002' => [
                'output' => 900,
                'output_unit' => 'gram',
                'lines' => [
                    ['id' => 'SBB001', 'qty' => 0.15, 'unit' => 'kg'],
                    ['id' => 'SBB016', 'qty' => 0.001, 'unit' => 'kg'],
                    ['id' => 'SBB008', 'qty' => 0.001, 'unit' => 'kg'],
                    ['id' => 'SBB012', 'qty' => 0.25, 'unit' => 'L'],
                    ['id' => 'SBB013', 'qty' => 0.1, 'unit' => 'kg'],
                    ['id' => 'SBB010', 'qty' => 4, 'unit' => 'pcs'],
                ],
            ],
            'BD003' => [
                'output' => 450,
                'output_unit' => 'ml',
                'lines' => [
                    ['id' => 'SBB004', 'qty' => 0.05, 'unit' => 'kg'],
                    ['id' => 'SBB015', 'qty' => 0.025, 'unit' => 'kg'],
                    ['id' => 'SBB009', 'qty' => 0.35, 'unit' => 'L'],
                    ['id' => 'SBB011', 'qty' => 2, 'unit' => 'pcs'],
                    ['id' => 'SBB013', 'qty' => 0.03, 'unit' => 'kg'],
                    ['id' => 'SBB016', 'qty' => 0.001, 'unit' => 'kg'],
                ],
            ],
            'BD004' => [
                'output' => 700,
                'output_unit' => 'gram',
                'lines' => [
                    ['id' => 'SBB001', 'qty' => 0.125, 'unit' => 'kg'],
                    ['id' => 'SBB016', 'qty' => 0.002, 'unit' => 'kg'],
                    ['id' => 'SBB008', 'qty' => 0.001, 'unit' => 'kg'],
                    ['id' => 'SBB012', 'qty' => 0.2, 'unit' => 'L'],
                    ['id' => 'SBB018', 'qty' => 0.1, 'unit' => 'kg'],
                    ['id' => 'SBB010', 'qty' => 3, 'unit' => 'pcs'],
                ],
            ],
            'BD005' => [
                'output' => 450,
                'output_unit' => 'ml',
                'lines' => [
                    ['id' => 'SBB004', 'qty' => 0.06, 'unit' => 'kg'],
                    ['id' => 'SBB015', 'qty' => 0.03, 'unit' => 'kg'],
                    ['id' => 'SBB016', 'qty' => 0.002, 'unit' => 'kg'],
                    ['id' => 'SBB009', 'qty' => 0.35, 'unit' => 'L'],
                    ['id' => 'SBB011', 'qty' => 2, 'unit' => 'pcs'],
                    ['id' => 'SBB013', 'qty' => 0.03, 'unit' => 'kg'],
                ],
            ],
            'BD006' => [
                'output' => 450,
                'output_unit' => 'gram',
                'lines' => [
                    ['id' => 'SBB002', 'qty' => 0.25, 'unit' => 'kg'],
                    ['id' => 'SBB005', 'qty' => 0.05, 'unit' => 'kg'],
                    ['id' => 'SBB015', 'qty' => 0.015, 'unit' => 'kg'],
                    ['id' => 'SBB016', 'qty' => 0.002, 'unit' => 'kg'],
                    ['id' => 'SBB008', 'qty' => 0.001, 'unit' => 'kg'],
                    ['id' => 'SBB012', 'qty' => 0.06, 'unit' => 'L'],
                    ['id' => 'SBB018', 'qty' => 0.05, 'unit' => 'kg'],
                    ['id' => 'SBB013', 'qty' => 0.015, 'unit' => 'kg'],
                ],
            ],
        ];
    }

    /**
     * Produk jadi: pemakaian bahan dasar + bahan baku langsung per batch produksi.
     *
     * @return array<int, array{
     *   product_id: string,
     *   nama: string,
     *   harga: int,
     *   batch_qty: int,
     *   batch_unit: string,
     *   bahan_dasar: array<int, array{bahan_dasar_id: string, qty: float, unit: string}>,
     *   raw_materials: array<int, array{id: string, qty: float, unit: string}>
     * }>
     */
    public static function products(): array
    {
        return [
            [
                'product_id' => 'P001',
                'nama' => 'Roti Manis Rasa Cokelat',
                'harga' => 8_500,
                'batch_qty' => 10,
                'batch_unit' => 'pcs',
                'bahan_dasar' => [
                    ['bahan_dasar_id' => 'BD001', 'qty' => 800, 'unit' => 'gram'],
                ],
                'raw_materials' => [
                    ['id' => 'SBB014', 'qty' => 0.3, 'unit' => 'kg'],
                ],
            ],
            [
                'product_id' => 'P002',
                'nama' => 'Éclair Cokelat',
                'harga' => 12_000,
                'batch_qty' => 15,
                'batch_unit' => 'pcs',
                'bahan_dasar' => [
                    ['bahan_dasar_id' => 'BD002', 'qty' => 900, 'unit' => 'gram'],
                    ['bahan_dasar_id' => 'BD003', 'qty' => 450, 'unit' => 'ml'],
                ],
                'raw_materials' => [
                    ['id' => 'SBB017', 'qty' => 0.15, 'unit' => 'kg'],
                ],
            ],
            [
                'product_id' => 'P003',
                'nama' => 'Kue Sus Vla Vanilla',
                'harga' => 6_500,
                'batch_qty' => 20,
                'batch_unit' => 'pcs',
                'bahan_dasar' => [
                    ['bahan_dasar_id' => 'BD004', 'qty' => 700, 'unit' => 'gram'],
                    ['bahan_dasar_id' => 'BD005', 'qty' => 450, 'unit' => 'ml'],
                ],
                'raw_materials' => [],
            ],
            [
                'product_id' => 'P004',
                'nama' => 'Pisang Molen',
                'harga' => 5_000,
                'batch_qty' => 12,
                'batch_unit' => 'pcs',
                'bahan_dasar' => [
                    ['bahan_dasar_id' => 'BD006', 'qty' => 450, 'unit' => 'gram'],
                ],
                'raw_materials' => [
                    ['id' => 'SBB019', 'qty' => 6, 'unit' => 'pcs'],
                    ['id' => 'SBB020', 'qty' => 1.5, 'unit' => 'L'],
                ],
            ],
            [
                'product_id' => 'P005',
                'nama' => 'Black Forest Cake',
                'harga' => 185_000,
                'batch_qty' => 1,
                'batch_unit' => 'loyang',
                'bahan_dasar' => [],
                'raw_materials' => [
                    ['id' => 'SBB003', 'qty' => 0.1, 'unit' => 'kg'],
                    ['id' => 'SBB021', 'qty' => 0.04, 'unit' => 'kg'],
                    ['id' => 'SBB015', 'qty' => 0.015, 'unit' => 'kg'],
                    ['id' => 'SBB004', 'qty' => 0.15, 'unit' => 'kg'],
                    ['id' => 'SBB022', 'qty' => 0.02, 'unit' => 'kg'],
                    ['id' => 'SBB023', 'qty' => 0.02, 'unit' => 'kg'],
                    ['id' => 'SBB010', 'qty' => 8, 'unit' => 'pcs'],
                    ['id' => 'SBB024', 'qty' => 0.01, 'unit' => 'kg'],
                    ['id' => 'SBB025', 'qty' => 0.05, 'unit' => 'L'],
                    ['id' => 'SBB026', 'qty' => 0.5, 'unit' => 'L'],
                    ['id' => 'SBB013', 'qty' => 0.1, 'unit' => 'kg'],
                    ['id' => 'SBB027', 'qty' => 0.015, 'unit' => 'kg'],
                    ['id' => 'SBB028', 'qty' => 1, 'unit' => 'pcs'],
                    ['id' => 'SBB017', 'qty' => 0.2, 'unit' => 'kg'],
                ],
            ],
        ];
    }

    /** @return array<int, array{date: string, total: int, qty: int, metode: string}> */
    public static function june2025DailySales(): array
    {
        $known = [
            '2025-06-01' => 1_822_000,
            '2025-06-02' => 1_690_600,
            '2025-06-03' => 1_142_000,
            '2025-06-04' => 1_808_500,
            '2025-06-05' => 4_122_450,
            '2025-06-06' => 1_306_000,
            '2025-06-07' => 1_680_500,
            '2025-06-08' => 1_899_500,
            '2025-06-09' => 2_077_000,
            '2025-06-10' => 1_511_500,
            '2025-06-11' => 2_450_000,
            '2025-06-12' => 2_680_000,
            '2025-06-13' => 2_890_000,
            '2025-06-14' => 3_120_000,
            '2025-06-15' => 2_750_000,
            '2025-06-16' => 2_980_000,
            '2025-06-17' => 3_450_000,
            '2025-06-18' => 3_680_000,
            '2025-06-19' => 3_250_000,
            '2025-06-20' => 3_890_000,
            '2025-06-21' => 4_120_000,
            '2025-06-22' => 3_560_000,
            '2025-06-23' => 3_780_000,
            '2025-06-24' => 4_050_000,
            '2025-06-25' => 3_920_000,
            '2025-06-26' => 3_650_000,
            '2025-06-27' => 3_480_000,
            '2025-06-28' => 3_890_000,
            '2025-06-29' => 4_250_000,
            '2025-06-30' => 4_680_000,
        ];

        $rows = [];
        $metodes = ['Cash', 'Transfer', 'Mix'];
        $i = 0;

        foreach ($known as $date => $total) {
            $rows[] = [
                'date' => $date,
                'total' => $total,
                'qty' => max(12, (int) round($total / 95_000)),
                'metode' => $metodes[$i % 3],
            ];
            $i++;
        }

        return $rows;
    }

    /** @return array<int, array{material_id: string, tanggal: string, qty: float, harga: int, catatan: string}> */
    public static function june2025Restocks(): array
    {
        return [
            ['material_id' => 'SBB029', 'tanggal' => '2025-06-03', 'qty' => 16, 'harga' => 54_323, 'catatan' => 'PT Mitra Handal Sejahtera'],
            ['material_id' => 'SBB029', 'tanggal' => '2025-06-17', 'qty' => 16, 'harga' => 54_323, 'catatan' => 'PT Mitra Handal Sejahtera'],
            ['material_id' => 'SBB029', 'tanggal' => '2025-06-21', 'qty' => 16, 'harga' => 54_323, 'catatan' => 'PT Mitra Handal Sejahtera'],
            ['material_id' => 'SBB010', 'tanggal' => '2025-06-20', 'qty' => 255, 'harga' => 1_600, 'catatan' => 'Pak Dwi — Telur Ayam (~14 kg)'],
            ['material_id' => 'SBB010', 'tanggal' => '2025-06-01', 'qty' => 270, 'harga' => 1_600, 'catatan' => 'Pak Dwi — Telur Ayam (~14,9 kg)'],
            ['material_id' => 'SBB010', 'tanggal' => '2025-06-05', 'qty' => 268, 'harga' => 1_600, 'catatan' => 'Pak Dwi — Telur Ayam (~14,8 kg)'],
            ['material_id' => 'SBB017', 'tanggal' => '2025-06-18', 'qty' => 1, 'harga' => 293_900, 'catatan' => 'PT Sukanda Djaya — Chocolate Compound Dark Diamond'],
            ['material_id' => 'SBB017', 'tanggal' => '2025-05-30', 'qty' => 2, 'harga' => 293_900, 'catatan' => 'PT Sukanda Djaya — Chocolate Compound'],
            ['material_id' => 'SBB009', 'tanggal' => '2025-06-18', 'qty' => 12, 'harga' => 17_690, 'catatan' => 'PT Sukanda Djaya — UHT Milk Full Cream'],
            ['material_id' => 'SBB001', 'tanggal' => '2025-06-04', 'qty' => 25, 'harga' => 14_500, 'catatan' => 'Kencana Makmur — Tepung & bahan kering'],
            ['material_id' => 'SBB001', 'tanggal' => '2025-06-13', 'qty' => 30, 'harga' => 14_500, 'catatan' => 'Kencana Makmur'],
            ['material_id' => 'SBB001', 'tanggal' => '2025-06-17', 'qty' => 40, 'harga' => 14_500, 'catatan' => 'Kencana Makmur'],
            ['material_id' => 'SBB004', 'tanggal' => '2025-06-08', 'qty' => 20, 'harga' => 14_500, 'catatan' => 'Kencana Makmur — Gula pasir'],
            ['material_id' => 'SBB013', 'tanggal' => '2025-06-11', 'qty' => 8, 'harga' => 92_000, 'catatan' => 'Tbk Gracia — Mentega'],
            ['material_id' => 'SBB018', 'tanggal' => '2025-06-11', 'qty' => 10, 'harga' => 32_000, 'catatan' => 'Yoeks — Margarin'],
            ['material_id' => 'SBB018', 'tanggal' => '2025-06-04', 'qty' => 15, 'harga' => 32_000, 'catatan' => 'Yoeks — Margarin & bahan bakery'],
            ['material_id' => 'SBB030', 'tanggal' => '2025-06-13', 'qty' => 1, 'harga' => 50_000, 'catatan' => 'Online — Kismis Hitam'],
            ['material_id' => 'SBB002', 'tanggal' => '2025-06-02', 'qty' => 15, 'harga' => 12_800, 'catatan' => 'Kencana Makmur — Tepung sedang'],
            ['material_id' => 'SBB015', 'tanggal' => '2025-06-12', 'qty' => 5, 'harga' => 17_000, 'catatan' => 'Tbk Gracia — Maizena'],
            ['material_id' => 'SBB019', 'tanggal' => '2025-06-06', 'qty' => 30, 'harga' => 3_500, 'catatan' => 'Pasar Tradisional — Pisang'],
            ['material_id' => 'SBB020', 'tanggal' => '2025-06-30', 'qty' => 20, 'harga' => 17_500, 'catatan' => 'Pertamina — Minyak goreng'],
            ['material_id' => 'SBB026', 'tanggal' => '2025-06-24', 'qty' => 10, 'harga' => 48_000, 'catatan' => 'Yoeks — Whipping cream'],
            ['material_id' => 'SBB021', 'tanggal' => '2025-06-18', 'qty' => 3, 'harga' => 82_000, 'catatan' => 'PT Sukanda Djaya — Cokelat bubuk'],
            ['material_id' => 'SBB003', 'tanggal' => '2025-06-14', 'qty' => 10, 'harga' => 13_200, 'catatan' => 'Kencana Makmur — Tepung rendah'],
            ['material_id' => 'SBB007', 'tanggal' => '2025-06-09', 'qty' => 2, 'harga' => 42_000, 'catatan' => 'Yoeks — Ragi instan'],
            ['material_id' => 'SBB006', 'tanggal' => '2025-06-16', 'qty' => 3, 'harga' => 82_000, 'catatan' => 'Kencana Makmur — Susu skim bubuk'],
        ];
    }
}

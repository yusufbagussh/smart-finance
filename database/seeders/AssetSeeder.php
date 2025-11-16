<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- Jangan lupa import DB
use App\Models\Asset; // <-- Import Model Asset

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Aset Reksadana Saham
        Asset::create([
            'asset_type' => 'mutual_fund',
            'code' => 'BDS',
            'name' => 'Batavia Dana Saham',
            'issuer' => 'Batavia Prosperindo Aset Manajemen',
            'price_unit' => 'unit',
            'current_price' => 1750.50 // Harga NAV terkini (contoh)
        ]);

        // 2. Aset Reksadana Pasar Uang
        Asset::create([
            'asset_type' => 'mutual_fund',
            'code' => 'SPU',
            'name' => 'Sucorinvest Pasar Uang',
            'issuer' => 'Sucorinvest Asset Management',
            'price_unit' => 'unit',
            'current_price' => 1200.00 // Harga NAV terkini (contoh)
        ]);

        // 3. Aset Emas
        Asset::create([
            'asset_type' => 'gold',
            'code' => 'ANTAM01',
            'name' => 'Emas Batangan Antam',
            'issuer' => 'PT Antam Tbk',
            'price_unit' => 'gram',
            'current_price' => 1350000.00 // Harga per gram terkini (contoh)
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\Portfolio;
use App\Models\Asset; // Kita akan butuh ini
use Illuminate\Support\Facades\DB;

class PortfolioService
{
    /**
     * Menghitung ringkasan detail dari sebuah portofolio.
     */
    public function calculatePortfolioSummary(Portfolio $portfolio)
    {
        // 1. Ambil semua transaksi, dikelompokkan per aset
        $transactionsByAsset = $portfolio->investmentTransactions()
            ->with('asset') // Load relasi aset
            ->orderBy('transaction_date', 'asc')
            ->get()
            ->groupBy('asset_id');

        $assetsSummary = [];
        $totalPortfolioValue = 0;
        $totalPortfolioCost = 0;

        foreach ($transactionsByAsset as $assetId => $transactions) {
            $totalQuantity = 0;
            $totalCostBasis = 0; // Total modal yang dikeluarkan untuk unit yang dimiliki
            $realizedPnL = 0;

            // Ambil data aset (seperti nama, tipe, dll)
            $asset = $transactions->first()->asset;

            // Variabel untuk menghitung Harga Beli Rata-rata (Average Cost)
            $weightedSum = 0;

            foreach ($transactions as $tx) {
                if ($tx->transaction_type === 'buy') {
                    $totalQuantity += $tx->quantity;
                    $weightedSum += $tx->total_amount; // Gunakan total_amount (sudah termasuk fee)
                } else { // 'sell'
                    // Hitung harga beli rata-rata SAAT INI sebelum menjual
                    $avgBuyPrice = ($totalQuantity > 0) ? ($weightedSum / $totalQuantity) : 0;

                    // Kurangi kuantitas
                    $totalQuantity -= $tx->quantity;

                    // Hitung modal dari unit yang dijual
                    $costOfSoldUnits = $avgBuyPrice * $tx->quantity;

                    // Update total modal
                    $weightedSum -= $costOfSoldUnits;

                    // Hitung Keuntungan/Kerugian yang Direalisasi
                    $realizedPnL += ($tx->total_amount - $costOfSoldUnits);
                }
            }

            // Setelah loop: Hitung nilai aset saat ini
            $averageBuyPrice = ($totalQuantity > 0) ? ($weightedSum / $totalQuantity) : 0;
            $currentCostBasis = $averageBuyPrice * $totalQuantity;

            // [!! PENTING !!]
            // Kita butuh harga pasar saat ini. Untuk sekarang, kita mock/simulasikan.
            // Nanti, ini harus diganti dengan panggilan API.
            // $currentPrice = $this->getMockCurrentPrice($asset);
            $currentPrice = $asset->current_price; // <-- GANTI DENGAN INI

            $currentValue = $totalQuantity * $currentPrice;
            $unrealizedPnL = $currentValue - $currentCostBasis;

            // Kumpulkan data per aset
            $assetsSummary[] = [
                'asset_id' => $asset->id,
                'asset_name' => $asset->name,
                'asset_code' => $asset->code,
                'price_unit' => $asset->price_unit,
                'total_quantity' => $totalQuantity,
                'average_buy_price' => $averageBuyPrice,
                'current_price' => $currentPrice,
                'current_value' => $currentValue,
                'unrealized_pnl' => $unrealizedPnL,
                'realized_pnl' => $realizedPnL,
            ];

            // Tambahkan ke total portofolio
            $totalPortfolioValue += $currentValue;
            $totalPortfolioCost += $currentCostBasis;
        }

        $totalUnrealizedPnL = $totalPortfolioValue - $totalPortfolioCost;

        // Kembalikan data dalam format yang rapi
        return [
            'assets' => $assetsSummary,
            'total_value' => $totalPortfolioValue,
            'total_cost_basis' => $totalPortfolioCost,
            'total_unrealized_pnl' => $totalUnrealizedPnL,
            // Anda juga bisa menambahkan total realized P&L dari semua aset
        ];
    }

    /**
     * FUNGSI SIMULASI: Mengambil harga pasar saat ini.
     * Ganti fungsi ini dengan panggilan API ke layanan data pasar.
     */
    private function getMockCurrentPrice(Asset $asset)
    {
        // Simulasi: harga emas naik jadi 1.100.000, reksadana naik jadi 1.650
        if ($asset->asset_type === 'gold') {
            return 1100000.00; // Harga per gram
        } else {
            return 1650.00; // NAV per unit
        }
    }
}

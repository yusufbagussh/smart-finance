<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Services\PortfolioService; // Import service kita
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use OpenAI\Laravel\Facades\OpenAI;

class PortfolioController extends Controller
{
    protected $portfolioService;

    // Inject service melalui constructor
    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    /**
     * Menampilkan daftar semua portofolio milik user.
     */
    public function index()
    {
        $portfolios = Auth::user()->portfolios;
        return view('portfolios.index', compact('portfolios'));
    }

    /**
     * Menampilkan dashboard/ringkasan detail dari satu portofolio.
     */
    // public function show(Portfolio $portfolio)
    // {
    //     $user = auth()->user();

    //     // 1. Kalkulasi Service (Sudah ada)
    //     $summary = $this->portfolioService->calculatePortfolioSummary($portfolio);

    //     // 2. Riwayat Transaksi (Sudah ada)
    //     $transactions = $portfolio->investmentTransactions()
    //         ->with('asset')
    //         ->orderBy('transaction_date', 'desc')
    //         ->get();

    //     // --- 3. LOGIKA ANALISIS AI DENGAN CACHE ---
    //     $cacheKey = "portfolio_analysis_user_{$user->id}_portfolio_{$portfolio->id}";

    //     $geminiAnalysisText = Cache::remember($cacheKey, now()->addHours(24), function () use ($user, $portfolio, $summary) {

    //         // A. Ambil Data Cash Flow Tambahan
    //         $currentYear = now()->year;
    //         $currentMonthNum = now()->month;

    //         $monthlyEarnedIncome = $user->transactions()
    //             ->income()
    //             ->whereYear('date', $currentYear)
    //             ->whereMonth('date', $currentMonthNum)
    //             ->whereNull('investment_transaction_id')
    //             ->sum('amount');

    //         $monthlySpending = $user->transactions()->expense()
    //             ->whereYear('date', $currentYear)->whereMonth('date', $currentMonthNum)
    //             ->whereNull('investment_transaction_id')->sum('amount');

    //         $netCashFlow = $monthlyEarnedIncome - $monthlySpending;

    //         // B. Buat Query Pencarian Google Dinamis
    //         $assetNamesForSearch = [];
    //         if (!empty($summary['assets'])) {
    //             foreach ($summary['assets'] as $asset) {
    //                 if ($asset['total_quantity'] > 0) {
    //                     $assetNamesForSearch[] = $asset['asset_name'];
    //                 }
    //             }
    //         }
    //         $assetQuery = empty($assetNamesForSearch) ? "investasi di Indonesia" : implode(' dan ', array_unique($assetNamesForSearch));
    //         $searchQuery1 = "analisis pasar {$assetQuery} terkini";
    //         $searchQuery2 = "tren inflasi dan suku bunga Indonesia " . $currentYear;
    //         $fullQuery = $searchQuery1 . " | " . $searchQuery2;

    //         // --- C. PANGGIL GOOGLE SEARCH API ---
    //         $searchResults = "Tidak ada data pasar eksternal yang ditemukan."; // Default
    //         $googleApiKey = env('GOOGLE_SEARCH_API_KEY');
    //         $searchEngineId = env('GOOGLE_SEARCH_ENGINE_ID');

    //         if ($googleApiKey && $searchEngineId) {
    //             try {
    //                 $response = Http::get('https://www.googleapis.com/customsearch/v1', [
    //                     'key' => $googleApiKey,
    //                     'cx' => $searchEngineId,
    //                     'q' => $fullQuery,
    //                     'num' => 3 // Ambil 3 cuplikan (snippet) berita teratas
    //                 ]);

    //                 if ($response->successful() && isset($response->json()['items'])) {
    //                     $items = $response->json()['items'];
    //                     // Ambil cuplikan berita dan gabungkan
    //                     $snippets = array_map(function ($item) {
    //                         // Bersihkan snippet dari newline/tab yang berlebihan
    //                         $cleanSnippet = preg_replace('/\s+/', ' ', $item['snippet']);
    //                         return "- " . trim($cleanSnippet);
    //                     }, $items);
    //                     $searchResults = "Konteks pasar terkini berdasarkan pencarian:\n" . implode("\n", $snippets);
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::error('Gagal memanggil Google Search API: ' . $e->getMessage());
    //                 $searchResults = "Gagal memuat data pasar eksternal.";
    //             }
    //         } else {
    //             Log::warning('Google Search API Key/CX belum diatur. Analisis AI akan tanpa data pasar.');
    //             $searchResults = "Data pasar eksternal dinonaktifkan (API Key belum diatur).";
    //         }
    //         // D. Buat Prompt AI
    //         $prompt = "[PERAN]\n" .
    //             "Anda adalah 'Smart Financial Co-Pilot', seorang analis keuangan dan investasi yang sangat cerdas, suportif, dan independen. Tugas Anda adalah memberikan analisis HOLISTIK (menyeluruh) atas portofolio pengguna, membandingkannya dengan profil risiko PRIBADI mereka dan kondisi pasar TERKINI. Gunakan bahasa yang profesional namun memotivasi. Fokus pada PRINSIP dan STRATEGI, bukan nasihat 'beli' atau 'jual' aset spesifik.\n\n" .

    //             "[DATA PROFIL PENGGUNA (STATIS)]\n" .
    //             "- Profil Risiko: " . ($portfolio->risk_profile ?? 'Belum diatur') . "\n" .
    //             "- Tujuan Utama Portofolio: " . ($portfolio->goal ?? 'Belum diatur') . "\n" .
    //             "- Horizon Waktu: " . ($portfolio->time_horizon ? $portfolio->time_horizon . ' tahun' : 'N/A') . "\n" .
    //             "- Toleransi Risiko (Catatan Pribadi): " . ($portfolio->risk_tolerance_notes ?? 'N/A') . "\n" .
    //             "- Rencana Investasi ke Depan: " . ($portfolio->future_plans ?? 'N/A') . "\n\n" .

    //             "[DATA KEUANGAN SAAT INI (DINAMIS)]\n" .
    //             "- Total Pemasukan Bulanan: Rp " . number_format($monthlyEarnedIncome, 0, ',', '.') . "\n" .
    //             "- Total Pengeluaran Konsumtif Bulanan: Rp " . number_format($monthlySpending, 0, ',', '.') . "\n" .
    //             "- Sisa Arus Kas (Cash Flow) Bulanan: Rp " . number_format($netCashFlow, 0, ',', '.') . "\n\n" .

    //             "[DATA PORTOFOLIO SAAT INI (DINAMIS)]\n" .
    //             "- Nama Portofolio: " . $portfolio->name . "\n" .
    //             "- Total Nilai Portofolio: Rp " . number_format($summary['total_value'], 0, ',', '.') . "\n" .
    //             "- Total Modal (Cost): Rp " . number_format($summary['total_cost_basis'], 0, ',', '.') . "\n" .
    //             "- Total Keuntungan (Unrealized): Rp " . number_format($summary['total_unrealized_pnl'], 0, ',', '.') . "\n\n" .

    //             "[ALOKASI ASET (DINAMIS)]\n";

    //         if (empty($assetNamesForSearch)) {
    //             $prompt .= "- Portofolio ini masih kosong.\n";
    //         } else {
    //             foreach ($summary['assets'] as $asset) {
    //                 if ($asset['total_quantity'] > 0) {
    //                     $percentage = ($summary['total_value'] > 0) ? ($asset['current_value'] / $summary['total_value']) * 100 : 0;
    //                     $prompt .= "- Aset: " . $asset['asset_name'] . "\n" .
    //                         "  - Nilai Saat Ini: Rp " . number_format($asset['current_value'], 0, ',', '.') . " (" . number_format($percentage, 1) . "% dari portofolio)\n";
    //                 }
    //             }
    //         }

    //         // Memasukkan hasil Google Search NYATA ke prompt
    //         $prompt .= "\n[DATA PASAR EKSTERNAL (Hasil Google Search)]\n" .
    //             $searchResults . "\n\n" .

    //             "---
    //         [TUGAS ANDA]
    //         Berdasarkan SEMUA data di atas (termasuk data pasar eksternal), berikan analisis Anda dalam poin-poin (gunakan markdown):

    //         1.  **Kesesuaian (Alignment) Portofolio:** Apakah alokasi aset saat ini SESUAI dengan Profil Risiko dan Horizon Waktu pengguna? Jelaskan jika ada ketidaksesuaian.
    //         2.  **Analisis Risiko & Skenario Pasar:** Berdasarkan data pasar eksternal, apa risiko utama untuk aset terbesar pengguna? Jelaskan 1 skenario (misal: 'Jika inflasi global naik...').
    //         3.  **Komentar atas Rencana Pengguna:** Pengguna berencana untuk '" . ($portfolio->future_plans ?? 'N/A') . "'. Apakah langkah ini sejalan dengan tujuan mereka? Berikan satu prinsip (alternatif aman) yang bisa dipertimbangkan.
    //         4.  **Wawasan Strategis:** Berikan SATU prinsip strategi diversifikasi atau rebalancing yang relevan untuk membantu pengguna berpikir lebih matang.
    //         ";

    //         // E. Panggil Gemini
    //         try {
    //             if (!env('GEMINI_API_KEY')) {
    //                 Log::warning('GEMINI_API_KEY not set for portfolio analysis.');
    //                 return "Analisis AI tidak tersedia (API Key belum diatur).";
    //             }

    //             // Gunakan model Flash untuk respons cepat
    //             $responseGemini = Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent($prompt);

    //             return $responseGemini->text() ?? "Gagal memuat analisis AI. Coba beberapa saat lagi.";
    //         } catch (\Exception $e) {
    //             Log::error('Gemini API connection failed: ' . $e->getMessage());
    //             return "Analisis AI tidak dapat terhubung. Coba beberapa saat lagi.";
    //         }
    //     });

    //     // 4. Lempar data ke view
    //     return view('portfolios.show', [
    //         'portfolio' => $portfolio,
    //         'summary' => $summary['assets'],
    //         'totals' => $summary, // Kirim semua data total
    //         'transactions' => $transactions,
    //         'geminiAnalysisText' => $geminiAnalysisText,
    //     ]);
    // }

    public function show(Portfolio $portfolio)
    {
        $user = auth()->user();

        // 1. Kalkulasi Service (Sudah ada)
        $summary = $this->portfolioService->calculatePortfolioSummary($portfolio);

        // 2. Riwayat Transaksi (Sudah ada)
        $transactions = $portfolio->investmentTransactions()
            ->with('asset')
            ->orderBy('transaction_date', 'desc')
            ->get();

        // --- 3. LOGIKA ANALISIS AI DENGAN CACHE ---
        $cacheKey = "portfolio_analysis_user_{$user->id}_portfolio_{$portfolio->id}";

        $geminiAnalysisText = Cache::remember($cacheKey, now()->addHours(24), function () use ($user, $portfolio, $summary) {

            // A. Ambil Data Cash Flow Tambahan
            $currentYear = now()->year;
            $currentMonthNum = now()->month;

            $monthlyEarnedIncome = $user->transactions()
                ->income()
                ->whereYear('date', $currentYear)
                ->whereMonth('date', $currentMonthNum)
                ->whereNull('investment_transaction_id')
                ->sum('amount');

            $monthlySpending = $user->transactions()->expense()
                ->whereYear('date', $currentYear)->whereMonth('date', $currentMonthNum)
                ->whereNull('investment_transaction_id')->sum('amount');

            $netCashFlow = $monthlyEarnedIncome - $monthlySpending;

            // B. Buat Query Pencarian Google Dinamis
            $assetNamesForSearch = [];
            if (!empty($summary['assets'])) {
                foreach ($summary['assets'] as $asset) {
                    if ($asset['total_quantity'] > 0) {
                        $assetNamesForSearch[] = $asset['asset_name'];
                    }
                }
            }
            $assetQuery = empty($assetNamesForSearch) ? "investasi di Indonesia" : implode(' dan ', array_unique($assetNamesForSearch));
            // Kueri yang lebih detail dan berlapis
            // 1. Analisis Pasar (Spesifik Aset)
            $searchQuery1 = "analisis pasar {$assetQuery} {$currentYear}";
            // 2. Makroekonomi (Global & Lokal)
            $searchQuery2 = "tren inflasi dan suku bunga global dan Indonesia {$currentYear}";
            // 3. Geopolitik & Kebijakan Global (Sesuai permintaan Anda)
            $searchQuery3 = "dampak geopolitik dan kebijakan global terhadap {$assetQuery}";
            // 4. Kebijakan Lokal (Sesuai permintaan Anda)
            $searchQuery4 = "kebijakan ekonomi Indonesia {$currentYear} yang mempengaruhi investasi";

            // Gabungkan semua kueri untuk satu panggilan Google Search
            $fullQuery = implode(" | ", [$searchQuery1, $searchQuery2, $searchQuery3, $searchQuery4]);
            // --- AKHIR BLOK ENHANCEMENT ---

            // --- C. PANGGIL GOOGLE SEARCH API ---
            $searchResults = "Tidak ada data pasar eksternal yang ditemukan."; // Default
            $googleApiKey = env('GOOGLE_SEARCH_API_KEY');
            $searchEngineId = env('GOOGLE_SEARCH_ENGINE_ID');

            if ($googleApiKey && $searchEngineId) {
                try {
                    $response = Http::get('https://www.googleapis.com/customsearch/v1', [
                        'key' => $googleApiKey,
                        'cx' => $searchEngineId,
                        'q' => $fullQuery,
                        'num' => 3 // Ambil 3 cuplikan (snippet) berita teratas
                    ]);

                    if ($response->successful() && isset($response->json()['items'])) {
                        $items = $response->json()['items'];
                        // Ambil cuplikan berita dan gabungkan
                        $snippets = array_map(function ($item) {
                            // Bersihkan snippet dari newline/tab yang berlebihan
                            $cleanSnippet = preg_replace('/\s+/', ' ', $item['snippet']);
                            return "- " . trim($cleanSnippet);
                        }, $items);
                        $searchResults = "Konteks pasar terkini berdasarkan pencarian:\n" . implode("\n", $snippets);
                    }
                } catch (\Exception $e) {
                    Log::error('Gagal memanggil Google Search API: ' . $e->getMessage());
                    $searchResults = "Gagal memuat data pasar eksternal.";
                }
            } else {
                Log::warning('Google Search API Key/CX belum diatur. Analisis AI akan tanpa data pasar.');
                $searchResults = "Data pasar eksternal dinonaktifkan (API Key belum diatur).";
            }
            // D. Buat Prompt AI
            $prompt = "[PERAN]\n" .
                "Anda adalah 'Smart Financial Co-Pilot', seorang analis keuangan dan investasi yang sangat cerdas, suportif, dan independen. Tugas Anda adalah memberikan analisis HOLISTIK (menyeluruh) atas portofolio pengguna, membandingkannya dengan profil risiko PRIBADI mereka dan kondisi pasar TERKINI. Gunakan bahasa yang profesional namun memotivasi. Fokus pada PRINSIP dan STRATEGI, bukan nasihat 'beli' atau 'jual' aset spesifik.\n\n" .

                "[DATA PROFIL PENGGUNA (STATIS)]\n" .
                "- Profil Risiko: " . ($portfolio->risk_profile ?? 'Belum diatur') . "\n" .
                "- Tujuan Utama Portofolio: " . ($portfolio->goal ?? 'Belum diatur') . "\n" .
                "- Horizon Waktu: " . ($portfolio->time_horizon ? $portfolio->time_horizon . ' tahun' : 'N/A') . "\n" .
                "- Toleransi Risiko (Catatan Pribadi): " . ($portfolio->risk_tolerance_notes ?? 'N/A') . "\n" .
                "- Rencana Investasi ke Depan: " . ($portfolio->future_plans ?? 'N/A') . "\n\n" .

                "[DATA KEUANGAN SAAT INI (DINAMIS)]\n" .
                "- Total Pemasukan Bulanan: Rp " . number_format($monthlyEarnedIncome, 0, ',', '.') . "\n" .
                "- Total Pengeluaran Konsumtif Bulanan: Rp " . number_format($monthlySpending, 0, ',', '.') . "\n" .
                "- Sisa Arus Kas (Cash Flow) Bulanan: Rp " . number_format($netCashFlow, 0, ',', '.') . "\n\n" .

                "[DATA PORTOFOLIO SAAT INI (DINAMIS)]\n" .
                "- Nama Portofolio: " . $portfolio->name . "\n" .
                "- Total Nilai Portofolio: Rp " . number_format($summary['total_value'], 0, ',', '.') . "\n" .
                "- Total Modal (Cost): Rp " . number_format($summary['total_cost_basis'], 0, ',', '.') . "\n" .
                "- Total Keuntungan (Unrealized): Rp " . number_format($summary['total_unrealized_pnl'], 0, ',', '.') . "\n\n" .

                "[ALOKASI ASET (DINAMIS)]\n";

            if (empty($assetNamesForSearch)) {
                $prompt .= "- Portofolio ini masih kosong.\n";
            } else {
                foreach ($summary['assets'] as $asset) {
                    if ($asset['total_quantity'] > 0) {
                        $percentage = ($summary['total_value'] > 0) ? ($asset['current_value'] / $summary['total_value']) * 100 : 0;
                        $prompt .= "- Aset: " . $asset['asset_name'] . "\n" .
                            "  - Nilai Saat Ini: Rp " . number_format($asset['current_value'], 0, ',', '.') . " (" . number_format($percentage, 1) . "% dari portofolio)\n";
                    }
                }
            }

            // Memasukkan hasil Google Search NYATA ke prompt
            $prompt .= "\n[DATA PASAR EKSTERNAL (Hasil Google Search)]\n" .
                $searchResults . "\n\n" .

                $prompt .= "---
            [CONTOH JAWABAN IDEAL]
            (Anda HARUS mengikuti format markdown, gaya bahasa, dan struktur 4 poin ini. Jangan tambahkan poin ke-5.)

            **1. Kesesuaian (Alignment) Portofolio:**
            Alokasi aset Anda saat ini [Sebutkan temuan utama, misal: sangat terkonsentrasi di Emas] terlihat [Beri penilaian, misal: kurang sejalan/cukup sejalan/sangat sejalan] dengan profil risiko '[Profil Risiko Pengguna]' dan horizon waktu [Horizon Waktu Pengguna] tahun Anda. Portofolio dengan profil tersebut biasanya [Berikan 1 kalimat prinsip, misal: memiliki porsi lebih besar di aset pertumbuhan].

            **2. Analisis Risiko & Skenario Pasar:**
            Berdasarkan data pasar terkini [Sebutkan 1 temuan pasar dari Google, misal: tentang inflasi yang melandai], risiko utama pada aset terbesar Anda ([Aset Terbesar Pengguna]) adalah [Sebutkan risiko, misal: potensi harganya stagnan].
            * **Skenario:** Jika [Kondisi pasar dari Google, misal: suku bunga tetap tinggi], investor mungkin akan beralih dari [Aset Pengguna] ke aset lain yang memberi imbal hasil. Ini bisa [Dampak ke aset pengguna, misal: menekan harga emas] dalam jangka pendek.

            **3. Komentar atas Rencana Pengguna:**
            Rencana Anda untuk [Sebutkan rencana pengguna, misal: 'mulai masuk saham individu'] adalah langkah yang [Beri penilaian, misal: sejalan/agresif] dengan profil '[Profil Risiko Pengguna]' Anda. Namun, [Berikan 1 catatan penting, misal: ini membutuhkan riset mendalam]. Sebagai alternatif yang lebih aman, Anda bisa mempertimbangkan [Berikan 1 prinsip alternatif, misal: menambah porsi di Reksadana Indeks] untuk mendapatkan eksposur ke pasar dengan risiko lebih terdiversifikasi.

            **4. Wawasan Strategis:**
            Prinsip terpenting untuk Anda saat ini adalah **[Sebutkan 1 PRINSIP, misal: Rebalancing (Penyeimbangan Kembali) atau Dollar-Cost Averaging (DCA)]**. Anda bisa [Berikan 1 kalimat penjelasan praktis, misal: menetapkan alokasi target (60% Emas, 40% Reksadana) dan menyeimbangkannya secara berkala] untuk memastikan profil risiko Anda tetap terjaga.
            ---
            ";

            "---
            [TUGAS ANDA]
            Berdasarkan SEMUA data di atas (termasuk data pasar eksternal), berikan analisis Anda.
            **Anda WAJIB mengikuti format, struktur, dan gaya bahasa dari [CONTOH JAWABAN IDEAL] di atas.**
            ";

            // E. Panggil Gemini
            try {
                if (!env('GEMINI_API_KEY')) {
                    Log::warning('GEMINI_API_KEY not set for portfolio analysis.');
                    return "Analisis AI tidak tersedia (API Key belum diatur).";
                }

                // Gunakan model Flash untuk respons cepat
                $responseGemini = Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent($prompt);

                return $responseGemini->text() ?? "Gagal memuat analisis AI. Coba beberapa saat lagi.";
            } catch (\Exception $e) {
                Log::error('Gemini API connection failed: ' . $e->getMessage());
                return "Analisis AI tidak dapat terhubung. Coba beberapa saat lagi.";
            }
        });

        // 4. Lempar data ke view
        return view('portfolios.show', [
            'portfolio' => $portfolio,
            'summary' => $summary['assets'],
            'totals' => $summary, // Kirim semua data total
            'transactions' => $transactions,
            'geminiAnalysisText' => $geminiAnalysisText,
        ]);
    }

    /**
     * Menampilkan form untuk membuat portofolio baru.
     */
    public function create()
    {
        return view('portfolios.create');
    }

    /**
     * Menyimpan portofolio baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'risk_profile' => ['nullable', Rule::in(['conservative', 'moderate', 'aggressive'])],
            'goal' => 'nullable|string|max:255',
            'time_horizon' => 'nullable|integer|min:0|max:100', // max 100 tahun
            'future_plans' => 'nullable|string',
            'risk_tolerance_notes' => 'nullable|string',
        ]);

        // Langsung asosiasikan dengan user yang sedang login
        $request->user()->portfolios()->create($validated);

        return redirect()->route('portfolios.index')
            ->with('success', 'Portofolio baru berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit portofolio.
     */
    public function edit(Portfolio $portfolio)
    {
        // Pastikan user hanya bisa mengedit miliknya
        // $this->authorize('update', $portfolio); // (Asumsi Anda punya Policy)
        // Atau: if ($portfolio->user_id !== auth()->id()) { abort(403); }

        return view('portfolios.edit', compact('portfolio'));
    }

    /**
     * Memperbarui data portofolio di database.
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        // Pastikan user hanya bisa mengupdate miliknya
        // $this->authorize('update', $portfolio);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'risk_profile' => ['nullable', Rule::in(['conservative', 'moderate', 'aggressive'])],
            'goal' => 'nullable|string|max:255',
            'time_horizon' => 'nullable|integer|min:0|max:100',
            'future_plans' => 'nullable|string',
            'risk_tolerance_notes' => 'nullable|string',
        ]);

        $portfolio->update($validated);

        return redirect()->route('portfolios.index')
            ->with('success', 'Portofolio berhasil diperbarui.');
    }

    /**
     * Menghapus portofolio dari database.
     */
    public function destroy(Portfolio $portfolio)
    {
        // Pastikan user hanya bisa menghapus miliknya
        // $this->authorize('delete', $portfolio);

        // Hapus portofolio.
        // Transaksi di dalamnya akan ikut terhapus karena
        // kita set 'onDelete('cascade')' di migrasi.
        $portfolio->delete();

        return redirect()->route('portfolios.index')
            ->with('success', 'Portofolio berhasil dihapus.');
    }
}

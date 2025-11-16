<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MachineLearningMonitoringController extends Controller
{
    private ?string $mlBaseUrl = null;

    public function __construct()
    {
        $this->mlBaseUrl = env('ML_BASE_URL', 'http://localhost:5000');
    }

    public function index()
    {
        // ... (Kode status API, DB, Disk, User Stats) ...

        // 1. Cek Status API AI
        $apiStatus = 'OFFLINE';
        $apiResponseTime = null;
        try {
            $start = microtime(true);
            $response = Http::timeout(2)->get($this->mlBaseUrl . 'health');
            $apiResponseTime = round((microtime(true) - $start) * 1000);
            if ($response->successful()) {
                $apiStatus = 'ONLINE';
            }
        } catch (\Exception $e) {
            $apiStatus = 'OFFLINE';
        }

        // 2. Cek Status Database
        $dbStatus = 'TERHUBUNG';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'GAGAL';
        }

        // 3. Statistik Server (Disk Space)
        $diskTotal = disk_total_space('/');
        $diskFree = disk_free_space('/');
        $diskUsedPercent = 100 - ($diskFree / $diskTotal) * 100;

        // 4. Statistik Aplikasi
        $userCount = User::count();
        $transactionCount = Transaction::count();
        $budgetCount = Budget::count();
        $failedJobs = DB::table('failed_jobs')->count();


        // --- !! 5. METRIK KINERJA MODEL AI !! ---

        // Fungsi helper untuk menghitung rata-rata dari array
        $getAverage = function ($key) {
            $data = Cache::get($key, []);
            if (empty($data)) return 0;
            return round(array_sum($data) / count($data));
        };

        // Metrik Klasifikasi
        $classifyErrors = Cache::get('ml_classify_errors', 0);
        $classifyAvgLatency = $getAverage('classify_latency'); // Rata-rata dalam ms
        $classifyAvgConfidenceCat = $getAverage('classify_confidence_cat') * 100; // Dalam %
        $classifyAvgConfidenceType = $getAverage('classify_confidence_type') * 100; // Dalam %

        // Metrik Prediksi
        $predictErrors = Cache::get('ml_predict_errors', 0);
        $predictAvgLatency = $getAverage('predict_latency');

        // Metrik Rekomendasi
        $recommendPythonErrors = Cache::get('ml_recommend_python_errors', 0);
        $recommendPythonAvgLatency = $getAverage('recommend_python_latency');
        $recommendGeminiErrors = Cache::get('ml_recommend_gemini_errors', 0);
        $recommendGeminiAvgLatency = $getAverage('recommend_gemini_latency');

        // --- !! AKHIR METRIK KINERJA MODEL AI !! ---


        return view('admin.monitoring.index', compact(
            'apiStatus',
            'apiResponseTime',
            'dbStatus',
            'diskUsedPercent',
            'userCount',
            'transactionCount',
            'budgetCount',
            'failedJobs',
            // Kirim data baru ke view
            'classifyErrors',
            'classifyAvgLatency',
            'classifyAvgConfidenceCat',
            'classifyAvgConfidenceType',
            'predictErrors',
            'predictAvgLatency',
            'recommendPythonErrors',
            'recommendPythonAvgLatency',
            'recommendGeminiErrors',
            'recommendGeminiAvgLatency'
        ));
    }
}

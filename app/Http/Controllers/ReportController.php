<?php
// app/Http/Controllers/ReportController.php
namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Exports\FinancialReportExport;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show report options page
     */
    public function index()
    {
        $categories = Category::all();
        return view('reports.index', compact('categories'));
    }

    /**
     * Export transactions to Excel
     */
    public function exportTransactionsExcel(Request $request)
    {
        $filters = $request->only(['type', 'category_id', 'date_from', 'date_to']);

        $filename = 'transactions_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new TransactionsExport(auth()->id(), $filters),
            $filename
        );
    }

    /**
     * Export financial report to Excel (multiple sheets)
     */
    public function exportFinancialReportExcel(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $validated['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $validated['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        $filename = 'financial_report_' . $startDate . '_to_' . $endDate . '.xlsx';

        return Excel::download(
            new FinancialReportExport(auth()->id(), $startDate, $endDate),
            $filename
        );
    }

    /**
     * Export transactions to PDF
     */
    public function exportTransactionsPdf(Request $request)
    {
        $filters = $request->only(['type', 'category_id', 'date_from', 'date_to']);

        $query = auth()->user()->transactions()->with('category')->orderBy('date', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $data = [
            'transactions' => $transactions,
            'user' => auth()->user(),
            'filters' => $filters,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'generatedAt' => now()->format('d M Y H:i:s')
        ];

        $pdf = Pdf::loadView('reports.transactions-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'transactions_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export financial report to PDF
     */
    public function exportFinancialReportPdf(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        // dd($validated);
        $startDate = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : now()->startOfMonth();
        $endDate = isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : now()->endOfMonth();

        $user = auth()->user();

        // Get transactions
        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Calculate summary
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        // Category breakdown
        $categoryBreakdown = $transactions->groupBy('category_id')->map(function ($items) {
            return [
                'category' => $items->first()->category,
                'income' => $items->where('type', 'income')->sum('amount'),
                'expense' => $items->where('type', 'expense')->sum('amount'),
                'count' => $items->count()
            ];
        })->sortByDesc(function ($item) {
            return $item['income'] + $item['expense'];
        });

        $data = [
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'transactions' => $transactions,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'categoryBreakdown' => $categoryBreakdown,
            'generatedAt' => now()->format('d M Y H:i:s')
        ];

        $pdf = Pdf::loadView('reports.financial-report-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'financial_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Preview PDF before download
     */
    // public function previewTransactionsPdf(Request $request)
    // {
    //     $filters = $request->only(['type', 'category_id', 'date_from', 'date_to']);

    //     $query = auth()->user()->transactions()->with('category')->orderBy('date', 'desc');

    //     // Apply filters
    //     if ($request->filled('type')) {
    //         $query->where('type', $request->type);
    //     }

    //     if ($request->filled('category_id')) {
    //         $query->where('category_id', $request->category_id);
    //     }

    //     if ($request->filled('date_from')) {
    //         $query->whereDate('date', '>=', $request->date_from);
    //     }

    //     if ($request->filled('date_to')) {
    //         $query->whereDate('date', '<=', $request->date_to);
    //     }

    //     $transactions = $query->get();

    //     $totalIncome = $transactions->where('type', 'income')->sum('amount');
    //     $totalExpense = $transactions->where('type', 'expense')->sum('amount');
    //     $balance = $totalIncome - $totalExpense;

    //     $data = [
    //         'transactions' => $transactions,
    //         'user' => auth()->user(),
    //         'filters' => $filters,
    //         'totalIncome' => $totalIncome,
    //         'totalExpense' => $totalExpense,
    //         'balance' => $balance,
    //         'generatedAt' => now()->format('d M Y H:i:s')
    //     ];

    //     $pdf = Pdf::loadView('reports.transactions-pdf', $data);
    //     $pdf->setPaper('a4', 'portrait');

    //     // Stream instead of download for preview
    //     return $pdf->stream('transactions_preview.pdf');
    // }

    /**
     * Export financial report to PDF (ENHANCED VERSION dengan grafik)
     */
    public function exportFinancialReportPdfEnhanced(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'template' => 'nullable|in:simple,enhanced', // Pilihan template
        ]);

        $startDate = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : now()->startOfMonth();
        $endDate = isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : now()->endOfMonth();
        $template = $validated['template'] ?? 'enhanced'; // Default ke enhanced

        $user = auth()->user();

        // Get transactions
        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Calculate summary
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        // Category breakdown
        $categoryBreakdown = $transactions->groupBy('category_id')->map(function ($items) {
            return [
                'category' => $items->first()->category,
                'income' => $items->where('type', 'income')->sum('amount'),
                'expense' => $items->where('type', 'expense')->sum('amount'),
                'count' => $items->count()
            ];
        })->sortByDesc(function ($item) {
            return $item['income'] + $item['expense'];
        });

        // Daily average
        $daysCount = max($startDate->diffInDays($endDate) + 1, 1);
        $avgDaily = $expense / $daysCount;

        // Largest transaction
        $largestExpense = $transactions->where('type', 'expense')->max('amount') ?? 0;

        // Savings rate
        $savingsRate = $income > 0 ? ($balance / $income) * 100 : 0;

        $data = [
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'transactions' => $transactions,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'categoryBreakdown' => $categoryBreakdown,
            'avgDaily' => $avgDaily,
            'largestExpense' => $largestExpense,
            'savingsRate' => $savingsRate,
            'daysCount' => $daysCount,
            'generatedAt' => now()->format('d M Y H:i:s')
        ];

        // Pilih template berdasarkan request
        $viewName = $template === 'enhanced'
            ? 'reports.financial-report-pdf-enhanced'
            : 'reports.financial-report-pdf';

        $pdf = Pdf::loadView($viewName, $data);
        $pdf->setPaper('a4', 'portrait');

        // Set options untuk PDF
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        $filename = 'financial_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export transactions to PDF (ENHANCED VERSION)
     */
    public function exportTransactionsPdfEnhanced(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|in:income,expense',
            'category_id' => 'nullable|exists:categories,category_id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'template' => 'nullable|in:simple,enhanced',
        ]);

        $filters = $request->only(['type', 'category_id', 'date_from', 'date_to']);
        $template = $validated['template'] ?? 'simple';

        $query = auth()->user()->transactions()->with('category')->orderBy('date', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Data untuk template simple
        $data = [
            'transactions' => $transactions,
            'user' => auth()->user(),
            'filters' => $filters,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'generatedAt' => now()->format('d M Y H:i:s')
        ];

        // Jika enhanced, tambahkan data tambahan
        if ($template === 'enhanced') {
            $categoryBreakdown = $transactions->groupBy('category_id')->map(function ($items) {
                return [
                    'category' => $items->first()->category,
                    'income' => $items->where('type', 'income')->sum('amount'),
                    'expense' => $items->where('type', 'expense')->sum('amount'),
                    'count' => $items->count()
                ];
            })->sortByDesc(function ($item) {
                return $item['income'] + $item['expense'];
            });

            $avgTransaction = $transactions->where('type', 'expense')->count() > 0
                ? $totalExpense / $transactions->where('type', 'expense')->count()
                : 0;

            $data['categoryBreakdown'] = $categoryBreakdown;
            $data['avgTransaction'] = $avgTransaction;
        }

        // Pilih view berdasarkan template
        $viewName = $template === 'enhanced'
            ? 'reports.transactions-pdf-enhanced'
            : 'reports.transactions-pdf';

        $pdf = Pdf::loadView($viewName, $data);
        $pdf->setPaper('a4', 'portrait');

        // Set options untuk PDF
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        $filename = 'transactions_' . ($template === 'enhanced' ? 'enhanced_' : '') . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Preview transactions PDF before download
     */
    public function previewTransactionsPdf(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|in:income,expense',
            'category_id' => 'nullable|exists:categories,category_id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'template' => 'nullable|in:simple,enhanced',
        ]);

        $filters = $request->only(['type', 'category_id', 'date_from', 'date_to']);
        $template = $validated['template'] ?? 'simple';

        $query = auth()->user()->transactions()->with('category')->orderBy('date', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Data untuk template simple
        $data = [
            'transactions' => $transactions,
            'user' => auth()->user(),
            'filters' => $filters,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'generatedAt' => now()->format('d M Y H:i:s')
        ];

        // Jika enhanced, tambahkan data tambahan
        if ($template === 'enhanced') {
            $categoryBreakdown = $transactions->groupBy('category_id')->map(function ($items) {
                return [
                    'category' => $items->first()->category,
                    'income' => $items->where('type', 'income')->sum('amount'),
                    'expense' => $items->where('type', 'expense')->sum('amount'),
                    'count' => $items->count()
                ];
            })->sortByDesc(function ($item) {
                return $item['income'] + $item['expense'];
            });

            $avgTransaction = $transactions->where('type', 'expense')->count() > 0
                ? $totalExpense / $transactions->where('type', 'expense')->count()
                : 0;

            $data['categoryBreakdown'] = $categoryBreakdown;
            $data['avgTransaction'] = $avgTransaction;
        }

        // Pilih view berdasarkan template
        $viewName = $template === 'enhanced'
            ? 'reports.transactions-pdf-enhanced'
            : 'reports.transactions-pdf';

        $pdf = Pdf::loadView($viewName, $data);
        $pdf->setPaper('a4', 'portrait');

        // Set options untuk PDF
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        // Stream instead of download for preview
        return $pdf->stream('transactions_preview_' . $template . '.pdf');
    }

    /**
     * Preview financial report PDF before download
     */
    public function previewFinancialReportPdf(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'template' => 'nullable|in:simple,enhanced',
        ]);

        $startDate = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : now()->startOfMonth();
        $endDate = isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : now()->endOfMonth();
        $template = $validated['template'] ?? 'enhanced'; // Default ke enhanced untuk financial

        $user = auth()->user();

        // Get transactions
        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Calculate summary
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        // Category breakdown
        $categoryBreakdown = $transactions->groupBy('category_id')->map(function ($items) {
            return [
                'category' => $items->first()->category,
                'income' => $items->where('type', 'income')->sum('amount'),
                'expense' => $items->where('type', 'expense')->sum('amount'),
                'count' => $items->count()
            ];
        })->sortByDesc(function ($item) {
            return $item['income'] + $item['expense'];
        });

        $data = [
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'transactions' => $transactions,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'categoryBreakdown' => $categoryBreakdown,
            'generatedAt' => now()->format('d M Y H:i:s')
        ];

        // Jika enhanced, tambahkan data statistik tambahan
        if ($template === 'enhanced') {
            $daysCount = max($startDate->diffInDays($endDate) + 1, 1);
            $avgDaily = $expense / $daysCount;
            $largestExpense = $transactions->where('type', 'expense')->max('amount') ?? 0;
            $savingsRate = $income > 0 ? ($balance / $income) * 100 : 0;

            $data['avgDaily'] = $avgDaily;
            $data['largestExpense'] = $largestExpense;
            $data['savingsRate'] = $savingsRate;
            $data['daysCount'] = $daysCount;
        }

        // Pilih template berdasarkan request
        $viewName = $template === 'enhanced'
            ? 'reports.financial-report-pdf-enhanced'
            : 'reports.financial-report-pdf';

        $pdf = Pdf::loadView($viewName, $data);
        $pdf->setPaper('a4', 'portrait');

        // Set options untuk PDF
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        // Stream instead of download for preview
        return $pdf->stream('financial_report_preview_' . $template . '.pdf');
    }
}

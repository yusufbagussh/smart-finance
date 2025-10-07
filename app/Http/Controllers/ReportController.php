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
    public function previewTransactionsPdf(Request $request)
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

        // Stream instead of download for preview
        return $pdf->stream('transactions_preview.pdf');
    }
}

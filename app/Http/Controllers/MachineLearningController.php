<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MachineLearningController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Mock classification accuracy
        $classificationStats = [
            'total_transactions' => $user->transactions()->count(),
            'auto_classified' => rand(60, 85),
            'accuracy_rate' => rand(85, 95),
        ];

        // Mock prediction data
        $predictions = $this->getMockPredictions();

        // Mock recommendations
        $recommendations = $this->getMockRecommendations();

        return view('ml.index', compact('classificationStats', 'predictions', 'recommendations'));
    }

    public function classifyTransaction(Request $request)
    {
        $description = $request->input('description', '');

        // Mock auto-classification logic
        $mockClassifications = [
            'gaji' => 'Salary',
            'grabfood' => 'Food & Dining',
            'gofood' => 'Food & Dining',
            'mcdonald' => 'Food & Dining',
            'kfc' => 'Food & Dining',
            'grab' => 'Transportation',
            'gojek' => 'Transportation',
            'uber' => 'Transportation',
            'spotify' => 'Entertainment',
            'netflix' => 'Entertainment',
            'youtube' => 'Entertainment',
            'tokopedia' => 'Shopping',
            'shopee' => 'Shopping',
            'lazada' => 'Shopping',
            'indomaret' => 'Shopping',
            'alfamart' => 'Shopping',
            'hospital' => 'Health & Medical',
            'doctor' => 'Health & Medical',
            'pharmacy' => 'Health & Medical',
            'listrik' => 'Bills & Utilities',
            'pdam' => 'Bills & Utilities',
            'telkom' => 'Bills & Utilities',
        ];

        $suggestedCategory = 'Other Expense';
        $confidence = 0.5;

        foreach ($mockClassifications as $keyword => $categoryName) {
            if (stripos($description, $keyword) !== false) {
                $suggestedCategory = $categoryName;
                $confidence = rand(80, 95) / 100;
                break;
            }
        }

        $category = Category::where('name', $suggestedCategory)->first();

        return response()->json([
            'suggested_category' => $category ? $category->category_id : null,
            'category_name' => $suggestedCategory,
            'confidence' => $confidence,
            'explanation' => "Based on keywords in the description, this transaction is likely related to {$suggestedCategory}."
        ]);
    }

    public function predictions()
    {
        $predictions = $this->getMockPredictions();
        return view('ml.predictions', compact('predictions'));
    }

    public function recommendations()
    {
        $recommendations = $this->getMockRecommendations();
        return view('ml.recommendations', compact('recommendations'));
    }

    private function getMockPredictions()
    {
        $user = auth()->user();
        $currentMonth = now();

        // Calculate average monthly expense for last 3 months
        $averageExpense = 0;
        for ($i = 1; $i <= 3; $i++) {
            $month = $currentMonth->copy()->subMonths($i)->format('Y-m');
            $monthExpense = $user->transactions()
                ->expense()
                ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$month])
                ->sum('amount');
            $averageExpense += $monthExpense;
        }
        $averageExpense = $averageExpense / 3;

        // Mock predictions for next 3 months
        $predictions = [];
        for ($i = 1; $i <= 3; $i++) {
            $predictedMonth = $currentMonth->copy()->addMonths($i);

            // Add some randomness to make it realistic
            $variance = rand(-20, 20) / 100; // ±20%
            $predictedAmount = $averageExpense * (1 + $variance);

            $predictions[] = [
                'month' => $predictedMonth->format('M Y'),
                'predicted_expense' => $predictedAmount,
                'confidence' => rand(75, 90),
                'trend' => $variance > 0 ? 'increase' : 'decrease',
            ];
        }

        return $predictions;
    }

    private function getMockRecommendations()
    {
        $user = auth()->user();

        // Get current month expenses by category
        $currentMonth = now()->format('Y-m');
        $categoryExpenses = $user->transactions()
            ->expense()
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $recommendations = [];

        // Mock recommendations based on spending patterns
        foreach ($categoryExpenses as $expense) {
            if ($expense->total > 1000000) { // If spending > 1M IDR
                $recommendations[] = [
                    'type' => 'reduce_spending',
                    'category' => $expense->category->name,
                    'current_amount' => $expense->total,
                    'suggested_amount' => $expense->total * 0.8,
                    'potential_savings' => $expense->total * 0.2,
                    'tips' => $this->getTipsForCategory($expense->category->name),
                    'priority' => $expense->total > 2000000 ? 'high' : 'medium',
                ];
            }
        }

        // Add general recommendations
        $recommendations[] = [
            'type' => 'emergency_fund',
            'category' => 'Savings',
            'current_amount' => 0,
            'suggested_amount' => $user->totalIncome() * 0.1,
            'potential_savings' => $user->totalIncome() * 0.1,
            'tips' => ['Set up automatic savings', 'Aim for 3-6 months of expenses', 'Use high-yield savings account'],
            'priority' => 'high',
        ];

        return $recommendations;
    }

    private function getTipsForCategory($categoryName)
    {
        $tips = [
            'Food & Dining' => [
                'Cook more meals at home',
                'Use food delivery promo codes',
                'Buy groceries in bulk',
                'Plan your meals weekly'
            ],
            'Transportation' => [
                'Use public transportation',
                'Carpool with colleagues',
                'Walk or bike for short distances',
                'Combine multiple errands in one trip'
            ],
            'Shopping' => [
                'Make a shopping list before going out',
                'Wait 24 hours before buying non-essentials',
                'Compare prices online',
                'Buy during sales or discount periods'
            ],
            'Entertainment' => [
                'Cancel unused subscriptions',
                'Look for free entertainment options',
                'Share family plans with friends',
                'Set a monthly entertainment budget'
            ],
        ];

        return $tips[$categoryName] ?? ['Review your spending in this category', 'Set a monthly budget limit', 'Track expenses regularly'];
    }
}

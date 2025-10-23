{{-- resources/views/reports/financial-report-pdf-enhanced.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        @page {
            margin: 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #8B5CF6;
            padding-bottom: 8px;
        }

        .header h1 {
            margin: 0 0 5px 0;
            color: #8B5CF6;
            font-size: 20px;
        }

        .header .subtitle {
            margin: 3px 0;
            color: #666;
            font-size: 9px;
        }

        .info-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .info-bar table {
            width: 100%;
        }

        .info-bar td {
            padding: 3px 5px;
            font-size: 9px;
        }

        /* Summary Cards Grid */
        .summary-container {
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-row {
            width: 100%;
            margin-bottom: 10px;
        }

        .summary-card {
            width: 32%;
            display: inline-block;
            padding: 12px;
            text-align: center;
            border: 2px solid;
            background-color: #F9FAFB;
            border-radius: 8px;
            margin-right: 1%;
            vertical-align: top;
        }

        .summary-card:last-child {
            margin-right: 0;
        }

        .summary-card.income {
            border-color: #10B981;
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
        }

        .summary-card.expense {
            border-color: #EF4444;
            background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
        }

        .summary-card.balance {
            border-color: #8B5CF6;
            background: linear-gradient(135deg, #EDE9FE 0%, #DDD6FE 100%);
        }

        .summary-card .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .summary-card .amount {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }

        .summary-card.income .amount {
            color: #059669;
        }

        .summary-card.expense .amount {
            color: #DC2626;
        }

        .summary-card.balance .amount {
            color: #7C3AED;
        }

        .summary-card .sub-info {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }

        /* Charts Section */
        .chart-section {
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .chart-container {
            width: 48%;
            display: inline-block;
            margin-right: 3%;
            vertical-align: top;
            page-break-inside: avoid;
        }

        .chart-container:nth-child(2) {
            margin-right: 0;
        }

        .chart-container h3 {
            color: #8B5CF6;
            font-size: 12px;
            margin: 0 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #8B5CF6;
        }

        .chart-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Section Headers */
        h2 {
            color: #8B5CF6;
            font-size: 14px;
            margin: 20px 0 10px 0;
            border-bottom: 2px solid #8B5CF6;
            padding-bottom: 5px;
            page-break-after: avoid;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto;
        }

        table.data-table th {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }

        table.data-table td {
            padding: 6px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 9px;
        }

        table.data-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        table.data-table tr:hover {
            background-color: #F3F4F6;
        }

        /* Category Stats */
        .category-stats {
            margin: 15px 0;
        }

        .category-item {
            padding: 8px;
            margin-bottom: 5px;
            border-left: 4px solid #8B5CF6;
            background: #F9FAFB;
            border-radius: 0 5px 5px 0;
        }

        .category-item .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .category-name {
            font-weight: bold;
            font-size: 10px;
            color: #333;
        }

        .category-amounts {
            font-size: 9px;
        }

        .category-amounts .income-amt {
            color: #10B981;
            margin-right: 10px;
        }

        .category-amounts .expense-amt {
            color: #EF4444;
        }

        .progress-bar {
            height: 6px;
            background: #E5E7EB;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 5px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10B981 0%, #059669 100%);
        }

        /* Insights Box */
        .insights-box {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            border-left: 4px solid #F59E0B;
            padding: 12px;
            margin: 15px 0;
            border-radius: 0 8px 8px 0;
        }

        .insights-box h3 {
            color: #92400E;
            font-size: 11px;
            margin: 0 0 8px 0;
            border: none;
            padding: 0;
        }

        .insights-box ul {
            margin: 0;
            padding-left: 15px;
        }

        .insights-box li {
            color: #78350F;
            font-size: 9px;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        /* Stats Grid */
        .stats-grid {
            width: 100%;
            margin: 15px 0;
        }

        .stat-item {
            width: 23%;
            display: inline-block;
            padding: 10px;
            margin-right: 2%;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            text-align: center;
            vertical-align: top;
        }

        .stat-item:last-child {
            margin-right: 0;
        }

        .stat-item .stat-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-item .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #8B5CF6;
        }

        .stat-item .stat-icon {
            font-size: 16px;
            margin-bottom: 5px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            color: #666;
            font-size: 8px;
        }

        .footer .page-number {
            margin-top: 5px;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(139, 92, 246, 0.05);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <!-- Watermark -->
    <div class="watermark">CONFIDENTIAL</div>

    <!-- Page 1: Summary & Overview -->
    <div class="header">
        <h1>📊 COMPREHENSIVE FINANCIAL REPORT</h1>
        <p class="subtitle">{{ $user->name }} ({{ $user->email }})</p>
        <p class="subtitle">Report Period: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
    </div>

    <div class="info-bar">
        <table>
            <tr>
                <td><strong>Generated:</strong> {{ $generatedAt }}</td>
                <td><strong>Total Transactions:</strong> {{ $transactions->count() }}</td>
                <td><strong>Report Duration:</strong> {{ $startDate->diffInDays($endDate) + 1 }} days</td>
            </tr>
        </table>
    </div>

    <!-- Summary Cards -->
    <div class="summary-container">
        <div class="summary-card income">
            <div class="label">💰 Total Income</div>
            <div class="amount">Rp {{ number_format($income, 0, ',', '.') }}</div>
            <div class="sub-info">
                {{ $transactions->where('type', 'income')->count() }} transactions
            </div>
        </div>
        <div class="summary-card expense">
            <div class="label">💸 Total Expense</div>
            <div class="amount">Rp {{ number_format($expense, 0, ',', '.') }}</div>
            <div class="sub-info">
                {{ $transactions->where('type', 'expense')->count() }} transactions
            </div>
        </div>
        <div class="summary-card balance">
            <div class="label">📈 Net Balance</div>
            <div class="amount">Rp {{ number_format($balance, 0, ',', '.') }}</div>
            <div class="sub-info">
                @if ($balance >= 0)
                    ✅ Positive balance
                @else
                    ⚠️ Deficit
                @endif
            </div>
        </div>
    </div>

    <!-- Key Statistics -->
    <h2>📊 Key Statistics</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-icon">📅</div>
            <div class="stat-label">Average Daily</div>
            <div class="stat-value">Rp
                {{ number_format($expense / max($startDate->diffInDays($endDate) + 1, 1), 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">💳</div>
            <div class="stat-label">Avg Transaction</div>
            <div class="stat-value">Rp
                {{ number_format($transactions->where('type', 'expense')->count() > 0 ? $expense / $transactions->where('type', 'expense')->count() : 0, 0, ',', '.') }}
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">🏆</div>
            <div class="stat-label">Largest Expense</div>
            <div class="stat-value">Rp
                {{ number_format($transactions->where('type', 'expense')->max('amount') ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">📊</div>
            <div class="stat-label">Savings Rate</div>
            <div class="stat-value">{{ $income > 0 ? number_format(($balance / $income) * 100, 1) : 0 }}%</div>
        </div>
    </div>

    <!-- Charts Placeholder -->
    <div class="chart-section">
        <div class="chart-container">
            <h3>📈 Income vs Expense Trend</h3>
            <div class="chart-placeholder">
                <div>
                    <strong>Chart View</strong><br>
                    Income: Rp {{ number_format($income, 0, ',', '.') }}<br>
                    Expense: Rp {{ number_format($expense, 0, ',', '.') }}<br><br>
                    <em>For interactive charts, view in web dashboard</em>
                </div>
            </div>
        </div>
        <div class="chart-container">
            <h3>🥧 Category Distribution</h3>
            <div class="chart-placeholder">
                <div>
                    <strong>Top Categories</strong><br>
                    @foreach ($categoryBreakdown->take(3) as $item)
                        {{ $item['category']->icon }} {{ $item['category']->name }}:
                        Rp {{ number_format($item['expense'], 0, ',', '.') }}<br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Insights -->
    <div class="insights-box">
        <h3>💡 Financial Insights & Recommendations</h3>
        <ul>
            @if ($balance >= 0)
                <li>✅ <strong>Positive Balance:</strong> You maintained a healthy surplus of Rp
                    {{ number_format($balance, 0, ',', '.') }} during this period.</li>
            @else
                <li>⚠️ <strong>Deficit Alert:</strong> Your expenses exceeded income by Rp
                    {{ number_format(abs($balance), 0, ',', '.') }}. Consider budget review.</li>
            @endif

            @php
                $savingsRate = $income > 0 ? ($balance / $income) * 100 : 0;
            @endphp
            @if ($savingsRate >= 20)
                <li>🎯 <strong>Excellent Savings:</strong> You're saving {{ number_format($savingsRate, 1) }}% of your
                    income. Keep it up!</li>
            @elseif($savingsRate >= 10)
                <li>👍 <strong>Good Savings:</strong> You're saving {{ number_format($savingsRate, 1) }}% of your
                    income. Try to reach 20% target.</li>
            @else
                <li>📉 <strong>Low Savings:</strong> Current savings rate is {{ number_format($savingsRate, 1) }}%. Aim
                    for at least 10-20% of income.</li>
            @endif

            @php
                $topCategory = $categoryBreakdown
                    ->sortByDesc(function ($item) {
                        return $item['expense'];
                    })
                    ->first();
            @endphp
            @if ($topCategory && $topCategory['expense'] > 0)
                <li>🔍 <strong>Top Spending Category:</strong> {{ $topCategory['category']->name }} accounts for Rp
                    {{ number_format($topCategory['expense'], 0, ',', '.') }}. Review if this aligns with your
                    priorities.</li>
            @endif

            <li>📊 <strong>Transaction Pattern:</strong> You made {{ $transactions->count() }} transactions, averaging
                {{ number_format($transactions->count() / max($startDate->diffInDays($endDate) + 1, 1), 1) }} per day.
            </li>
        </ul>
    </div>

    <div class="page-break"></div>

    <!-- Page 2: Category Breakdown -->
    <h2>📁 Category Breakdown Analysis</h2>

    <div class="category-stats">
        @php
            $maxAmount = $categoryBreakdown->max(function ($item) {
                return $item['income'] + $item['expense'];
            });
        @endphp

        @foreach ($categoryBreakdown as $item)
            <div class="category-item">
                <div class="category-header">
                    <span class="category-name">
                        {{ $item['category']->icon }} {{ $item['category']->name }}
                    </span>
                    <span class="category-amounts">
                        @if ($item['income'] > 0)
                            <span class="income-amt">+Rp {{ number_format($item['income'], 0, ',', '.') }}</span>
                        @endif
                        @if ($item['expense'] > 0)
                            <span class="expense-amt">-Rp {{ number_format($item['expense'], 0, ',', '.') }}</span>
                        @endif
                    </span>
                </div>
                <div style="font-size: 8px; color: #666; margin-bottom: 3px;">
                    {{ $item['count'] }} transactions
                    @if ($expense > 0 && $item['expense'] > 0)
                        • {{ number_format(($item['expense'] / $expense) * 100, 1) }}% of total expenses
                    @endif
                </div>
                @if ($maxAmount > 0)
                    <div class="progress-bar">
                        <div class="progress-fill"
                            style="width: {{ (($item['income'] + $item['expense']) / $maxAmount) * 100 }}%"></div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="page-break"></div>

    <!-- Page 3: Transaction Details -->
    <h2>📋 Detailed Transaction List</h2>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Date</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 18%;">Category</th>
                <th style="width: 40%;">Description</th>
                <th style="width: 20%; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date->format('d/m/Y') }}</td>
                    <td
                        style="color: {{ $transaction->type === 'income' ? '#10B981' : '#EF4444' }}; font-weight: bold;">
                        {{ $transaction->type === 'income' ? '↗️ Income' : '↘️ Expense' }}
                    </td>
                    <td>{{ $transaction->category->icon }} {{ $transaction->category->name }}</td>
                    <td>{{ Str::limit($transaction->description, 60) }}</td>
                    <td class="text-right"
                        style="font-weight: bold; color: {{ $transaction->type === 'income' ? '#10B981' : '#EF4444' }};">
                        {{ $transaction->type === 'income' ? '+' : '-' }}Rp
                        {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #F3F4F6; font-weight: bold;">
                <td colspan="4" class="text-right" style="padding: 10px;">TOTAL BALANCE:</td>
                <td class="text-right"
                    style="padding: 10px; color: {{ $balance >= 0 ? '#10B981' : '#EF4444' }}; font-size: 11px;">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Finance Tracker</strong> - Personal Financial Management System</p>
        <p>This is a confidential financial report. Please keep it secure.</p>
        <p>Generated on {{ $generatedAt }} | Report ID: FIN-{{ $user->id }}-{{ now()->format('YmdHis') }}</p>
        <p class="page-number">© {{ date('Y') }} Finance Tracker. All rights reserved.</p>
    </div>
</body>

</html>

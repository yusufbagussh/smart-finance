<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Transaction Report - Enhanced</title>
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
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 8px;
        }

        .header h1 {
            margin: 0 0 5px 0;
            color: #4F46E5;
            font-size: 20px;
        }

        .header .subtitle {
            margin: 3px 0;
            color: #666;
            font-size: 9px;
        }

        .info-bar {
            background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
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

        /* Summary Cards */
        .summary-container {
            width: 100%;
            margin-bottom: 15px;
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
            border-color: #4F46E5;
            background: linear-gradient(135deg, #E0E7FF 0%, #C7D2FE 100%);
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
            color: #4338CA;
        }

        .summary-card .sub-info {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
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
            color: #4F46E5;
        }

        .stat-item .stat-icon {
            font-size: 16px;
            margin-bottom: 5px;
        }

        /* Category Stats */
        .category-stats {
            margin: 15px 0;
        }

        .category-item {
            padding: 8px;
            margin-bottom: 5px;
            border-left: 4px solid #4F46E5;
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
            background: linear-gradient(90deg, #4F46E5 0%, #6366F1 100%);
        }

        /* Filter Info Box */
        .filter-box {
            background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
            border-left: 4px solid #3B82F6;
            padding: 12px;
            margin: 15px 0;
            border-radius: 0 8px 8px 0;
        }

        .filter-box h3 {
            color: #1E3A8A;
            font-size: 11px;
            margin: 0 0 8px 0;
        }

        .filter-box .filter-item {
            font-size: 9px;
            color: #1E40AF;
            margin-bottom: 3px;
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

        /* Section Headers */
        h2 {
            color: #4F46E5;
            font-size: 14px;
            margin: 20px 0 10px 0;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 5px;
            page-break-after: avoid;
        }

        /* Tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto;
        }

        table.data-table th {
            background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
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

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            color: #666;
            font-size: 8px;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(79, 70, 229, 0.05);
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

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- Watermark -->
    <div class="watermark">CONFIDENTIAL</div>

    <!-- Header -->
    <div class="header">
        <h1>💰 COMPREHENSIVE TRANSACTION REPORT</h1>
        <p class="subtitle">{{ $user->name }} ({{ $user->email }})</p>
        <p class="subtitle">Generated on {{ $generatedAt }}</p>
    </div>

    <!-- Info Bar -->
    <div class="info-bar">
        <table>
            <tr>
                <td><strong>Total Transactions:</strong> {{ $transactions->count() }}</td>
                <td><strong>Report Type:</strong>
                    @if (isset($filters['type']))
                        {{ ucfirst($filters['type']) }} Only
                    @else
                        All Transactions
                    @endif
                </td>
                @if (isset($filters['date_from']) || isset($filters['date_to']))
                    <td><strong>Period:</strong> {{ $filters['date_from'] ?? 'Start' }} to
                        {{ $filters['date_to'] ?? 'Now' }}</td>
                @endif
            </tr>
        </table>
    </div>

    <!-- Summary Cards -->
    <div class="summary-container">
        <div class="summary-card income">
            <div class="label">💰 Total Income</div>
            <div class="amount">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            <div class="sub-info">
                {{ $transactions->where('type', 'income')->count() }} transactions
            </div>
        </div>
        <div class="summary-card expense">
            <div class="label">💸 Total Expense</div>
            <div class="amount">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
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
            <div class="stat-icon">💳</div>
            <div class="stat-label">Avg Transaction</div>
            <div class="stat-value">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">🆙</div>
            <div class="stat-label">Largest Income</div>
            <div class="stat-value">Rp
                {{ number_format($transactions->where('type', 'income')->max('amount') ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">🔻</div>
            <div class="stat-label">Largest Expense</div>
            <div class="stat-value">Rp
                {{ number_format($transactions->where('type', 'expense')->max('amount') ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">📊</div>
            <div class="stat-label">Categories Used</div>
            <div class="stat-value">{{ $categoryBreakdown->count() }}</div>
        </div>
    </div>

    <!-- Active Filters -->
    @if (isset($filters['type']) ||
            isset($filters['category_id']) ||
            isset($filters['date_from']) ||
            isset($filters['date_to']))
        <div class="filter-box">
            <h3>🔍 Active Filters</h3>
            @if (isset($filters['type']))
                <div class="filter-item">• <strong>Transaction Type:</strong> {{ ucfirst($filters['type']) }}</div>
            @endif
            @if (isset($filters['category_id']))
                @php
                    $filteredCategory = $transactions->first()?->category;
                @endphp
                @if ($filteredCategory)
                    <div class="filter-item">• <strong>Category:</strong> {{ $filteredCategory->icon }}
                        {{ $filteredCategory->name }}</div>
                @endif
            @endif
            @if (isset($filters['date_from']))
                <div class="filter-item">• <strong>From Date:</strong> {{ $filters['date_from'] }}</div>
            @endif
            @if (isset($filters['date_to']))
                <div class="filter-item">• <strong>To Date:</strong> {{ $filters['date_to'] }}</div>
            @endif
        </div>
    @endif

    <!-- Transaction Insights -->
    <div class="insights-box">
        <h3>💡 Transaction Insights</h3>
        <ul>
            @if ($balance >= 0)
                <li>✅ <strong>Positive Balance:</strong> Your income exceeded expenses by Rp
                    {{ number_format($balance, 0, ',', '.') }}.</li>
            @else
                <li>⚠️ <strong>Deficit Alert:</strong> Your expenses exceeded income by Rp
                    {{ number_format(abs($balance), 0, ',', '.') }}.</li>
            @endif

            @if ($transactions->count() > 0)
                <li>📊 <strong>Transaction Volume:</strong> You made {{ $transactions->count() }} transactions during
                    this period.</li>
            @endif

            @if ($avgTransaction > 0)
                <li>💵 <strong>Average Spending:</strong> Your average expense per transaction is Rp
                    {{ number_format($avgTransaction, 0, ',', '.') }}.</li>
            @endif

            @php
                $topCategory = $categoryBreakdown
                    ->sortByDesc(function ($item) {
                        return $item['expense'];
                    })
                    ->first();
            @endphp
            @if ($topCategory && $topCategory['expense'] > 0)
                <li>🏆 <strong>Top Category:</strong> Most expenses in {{ $topCategory['category']->name }} (Rp
                    {{ number_format($topCategory['expense'], 0, ',', '.') }}).</li>
            @endif
        </ul>
    </div>

    <!-- Category Breakdown -->
    @if ($categoryBreakdown->count() > 0)
        <h2>📂 Category Breakdown</h2>
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
    @endif

    <div class="page-break"></div>

    <!-- Transaction Details -->
    <h2>📋 Detailed Transaction List</h2>

    @if ($transactions->count() > 0)
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
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <div style="font-size: 48px; margin-bottom: 10px;">📭</div>
            <p style="font-size: 12px; font-weight: bold;">No transactions found</p>
            <p style="font-size: 10px;">Try adjusting your filters to see more results.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Finance Tracker</strong> - Personal Financial Management System</p>
        <p>This is a confidential transaction report. Please keep it secure.</p>
        <p>Generated on {{ $generatedAt }} | Report ID: TRX-{{ $user->id }}-{{ now()->format('YmdHis') }}</p>
        <p>© {{ date('Y') }} Finance Tracker. All rights reserved.</p>
    </div>
</body>

</html>

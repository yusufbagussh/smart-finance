{{-- resources/views/reports/financial-report-pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #8B5CF6;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #8B5CF6;
            font-size: 22px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 10px;
        }

        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 2px solid #E5E7EB;
            background-color: #F9FAFB;
        }

        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        .summary-card .amount {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }

        .income-card {
            border-color: #10B981;
        }

        .expense-card {
            border-color: #EF4444;
        }

        .balance-card {
            border-color: #8B5CF6;
        }

        .income-amount {
            color: #10B981;
        }

        .expense-amount {
            color: #EF4444;
        }

        .balance-amount {
            color: #8B5CF6;
        }

        h2 {
            color: #8B5CF6;
            font-size: 16px;
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 2px solid #8B5CF6;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #8B5CF6;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #666;
            font-size: 9px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>📊 Financial Report</h1>
        <p>{{ $user->name }} - {{ $user->email }}</p>
        <p>Period: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card income-card">
            <h3>Total Income</h3>
            <div class="amount income-amount">Rp {{ number_format($income, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card expense-card">
            <h3>Total Expense</h3>
            <div class="amount expense-amount">Rp {{ number_format($expense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card balance-card">
            <h3>Net Balance</h3>
            <div class="amount balance-amount">Rp {{ number_format($balance, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Category Breakdown -->
    <h2>Category Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Income</th>
                <th class="text-right">Expense</th>
                <th class="text-right">Transaction Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categoryBreakdown as $item)
                <tr>
                    <td>{{ $item['category']->name }}</td>
                    <td class="text-right" style="color: #10B981;">
                        Rp {{ number_format($item['income'], 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="color: #EF4444;">
                        Rp {{ number_format($item['expense'], 0, ',', '.') }}
                    </td>
                    <td class="text-right">{{ $item['count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Transaction List -->
    <h2>Transaction List</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date->format('d/m/Y') }}</td>
                    <td style="color: {{ $transaction->type === 'income' ? '#10B981' : '#EF4444' }};">
                        {{ ucfirst($transaction->type) }}
                    </td>
                    <td>{{ $transaction->category->name }}</td>
                    <td>{{ Str::limit($transaction->description, 50) }}</td>
                    <td class="text-right"
                        style="font-weight: bold; color: {{ $transaction->type === 'income' ? '#10B981' : '#EF4444' }};">
                        {{ $transaction->type === 'income' ? '+' : '-' }}Rp
                        {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ $generatedAt }} by Finance Tracker</p>
        <p>© {{ date('Y') }} Finance Tracker - Confidential Financial Report</p>
    </div>
</body>

</html>

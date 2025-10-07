{{-- resources/views/reports/transactions-pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Transaction Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #4F46E5;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .info-box {
            background-color: #F3F4F6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .info-box table {
            width: 100%;
        }

        .info-box td {
            padding: 5px;
        }

        .summary-box {
            background-color: #EEF2FF;
            padding: 15px;
            border-left: 4px solid #4F46E5;
            margin-bottom: 20px;
        }

        .summary-box table {
            width: 100%;
        }

        .summary-box td {
            padding: 8px;
            font-size: 14px;
        }

        .summary-box .label {
            font-weight: bold;
            width: 40%;
        }

        .income {
            color: #10B981;
            font-weight: bold;
        }

        .expense {
            color: #EF4444;
            font-weight: bold;
        }

        .balance {
            color: #4F46E5;
            font-weight: bold;
            font-size: 16px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.data-table th {
            background-color: #4F46E5;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }

        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
        }

        table.data-table tr:hover {
            background-color: #F9FAFB;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>💰 Transaction Report</h1>
        <p>{{ $user->name }} - {{ $user->email }}</p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td><strong>Generated:</strong></td>
                <td>{{ $generatedAt }}</td>
                <td><strong>Total Transactions:</strong></td>
                <td>{{ $transactions->count() }}</td>
            </tr>
            @if (isset($filters['date_from']) || isset($filters['date_to']))
                <tr>
                    <td><strong>Period:</strong></td>
                    <td colspan="3">
                        {{ $filters['date_from'] ?? 'All time' }} to {{ $filters['date_to'] ?? 'Now' }}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td class="label">Total Income:</td>
                <td class="text-right income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Expense:</td>
                <td class="text-right expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 2px solid #4F46E5;">
                <td class="label">Balance:</td>
                <td class="text-right balance">Rp {{ number_format($balance, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <h3 style="color: #4F46E5; margin-top: 30px;">Transaction Details</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Description</th>
                <th class="text-right">Amount (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date->format('d M Y') }}</td>
                    <td>
                        @if ($transaction->type === 'income')
                            <span style="color: #10B981;">Income</span>
                        @else
                            <span style="color: #EF4444;">Expense</span>
                        @endif
                    </td>
                    <td>{{ $transaction->category->name }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td class="text-right"
                        style="font-weight: bold; color: {{ $transaction->type === 'income' ? '#10B981' : '#EF4444' }};">
                        {{ $transaction->type === 'income' ? '+' : '-' }}Rp
                        {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #666;">
                        No transactions found for the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated by Finance Tracker on {{ $generatedAt }}</p>
        <p>© {{ date('Y') }} Finance Tracker - All rights reserved</p>
    </div>
</body>

</html>

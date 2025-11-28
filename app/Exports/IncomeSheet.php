<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IncomeSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $userId;
    protected $startDate;
    protected $endDate;

    public function __construct($userId, $startDate, $endDate)
    {
        $this->userId = $userId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Transaction::with('category')
            ->where('user_id', $this->userId)
            ->where('type', 'income')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Date', 'Category', 'Description', 'Amount (IDR)'];
    }

    public function map($transaction): array
    {
        return [
            $transaction->date->format('Y-m-d'),
            $transaction->category->name ?? 'Uncategorized',
            $transaction->description,
            $transaction->amount
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10B981']
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Income';
    }
}

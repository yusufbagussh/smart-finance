<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SummarySheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
        $user = User::find($this->userId);

        $income = $user->transactions()
            ->where('type', 'income')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->sum('amount');

        $expense = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->sum('amount');

        $count = $user->transactions()
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->count();

        return collect([
            ['metric' => 'Total Income', 'value' => $income],
            ['metric' => 'Total Expense', 'value' => $expense],
            ['metric' => 'Net Balance', 'value' => $income - $expense],
            ['metric' => 'Transaction Count', 'value' => $count]
        ]);
    }

    public function headings(): array
    {
        return ['Financial Summary', 'Amount (IDR)'];
    }

    public function map($row): array
    {
        return [$row['metric'], $row['value']];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10B981']
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
}

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
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $userId;
    protected $filters;

    public function __construct($userId, $filters = [])
    {
        $this->userId = $userId;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Transaction::with('category')
            ->where('user_id', $this->userId)
            ->orderBy('date', 'desc');

        // Apply filters
        if (isset($this->filters['type']) && !empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        if (isset($this->filters['category_id']) && !empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (isset($this->filters['date_from']) && !empty($this->filters['date_from'])) {
            $query->whereDate('date', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to']) && !empty($this->filters['date_to'])) {
            $query->whereDate('date', '<=', $this->filters['date_to']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Category',
            'Description',
            'Amount (IDR)',
            'Created At'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->date->format('Y-m-d'),
            ucfirst($transaction->type),
            $transaction->category->name ?? 'Uncategorized',
            $transaction->description,
            $transaction->type === 'income' ? $transaction->amount : -$transaction->amount,
            $transaction->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function title(): string
    {
        return 'Transactions';
    }
}

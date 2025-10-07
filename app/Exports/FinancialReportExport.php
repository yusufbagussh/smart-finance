<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FinancialReportExport implements WithMultipleSheets
{
    protected $userId;
    protected $startDate;
    protected $endDate;

    public function __construct($userId, $startDate = null, $endDate = null)
    {
        $this->userId = $userId;
        $this->startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? now()->endOfMonth()->format('Y-m-d');
    }

    public function sheets(): array
    {
        return [
            new SummarySheet($this->userId, $this->startDate, $this->endDate),
            new IncomeSheet($this->userId, $this->startDate, $this->endDate),
            new ExpenseSheet($this->userId, $this->startDate, $this->endDate),
            new CategoryBreakdownSheet($this->userId, $this->startDate, $this->endDate),
        ];
    }
}

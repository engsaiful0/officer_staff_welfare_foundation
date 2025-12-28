<?php

namespace App\Exports;

use App\Models\Investment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvestmentLedgerExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $ledgerData;
    protected $investment;
    protected $totalProduct;
    protected $totalBalance;

    public function __construct($ledgerData, $investment, $totalProduct, $totalBalance)
    {
        $this->ledgerData = collect($ledgerData);
        $this->investment = $investment;
        $this->totalProduct = $totalProduct;
        $this->totalBalance = $totalBalance;
    }

    public function collection()
    {
        return $this->ledgerData;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Ending date',
            'Particulars',
            'Dr',
            'Cr',
            'Balance',
            'Days',
            'Product'
        ];
    }

    public function map($row): array
    {
        return [
            $row['date'],
            $row['ending_date'],
            $row['particulars'],
            number_format($row['debit'], 2),
            number_format($row['credit'], 2),
            number_format($row['balance'], 2),
            $row['days'],
            number_format($row['product'], 2),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}






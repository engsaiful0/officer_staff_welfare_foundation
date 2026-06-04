<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MemberDeductionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        protected $deductions,
        protected $resolveAccountNumber
    ) {}

    public function collection()
    {
        return $this->deductions;
    }

    public function headings(): array
    {
        return [
            'Member',
            'Member ID',
            'Account Number',
            'Mobile',
            'Designation',
            'Period',
            'Deposit',
            'Investment',
            'Qard',
            'Profit',
            'Compensation',
            'Total Amount',
            'Deduction Date',
            'Recorded By',
            'Remarks',
        ];
    }

    public function map($deduction): array
    {
        $member = $deduction->member;
        $accountNumber = ($this->resolveAccountNumber)($member);

        return [
            $member?->name ?? '—',
            $member?->member_unique_id ?? '—',
            $accountNumber,
            $member?->mobile ?? '—',
            $member?->designation?->designation_name ?? '—',
            date('F', mktime(0, 0, 0, (int) $deduction->month, 1)).' '.$deduction->year,
            (float) $deduction->monthly_deposit_amount,
            (float) $deduction->monthly_investment_amount,
            (float) $deduction->monthly_qard_amount,
            (float) $deduction->profit_on_deposit_amount,
            (float) $deduction->compensation_on_investment_amount,
            (float) $deduction->total_amount,
            $deduction->deduction_date?->format('Y-m-d') ?? '—',
            $deduction->user?->name ?? '—',
            $deduction->remarks ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

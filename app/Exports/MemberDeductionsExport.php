<?php

namespace App\Exports;

use App\Models\Member;
use App\Models\MemberDeduction;

class MemberDeductionsExport
{
    public function __construct(
        protected $deductions,
        protected \Closure $resolveAccountNumber
    ) {}

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

    /**
     * @return array<int, array<int, mixed>>
     */
    public function rows(): array
    {
        $rows = [];
        foreach ($this->deductions as $deduction) {
            $rows[] = $this->mapRow($deduction);
        }

        return $rows;
    }

    /**
     * @return array<int, mixed>
     */
    protected function mapRow(MemberDeduction $deduction): array
    {
        /** @var Member|null $member */
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
}

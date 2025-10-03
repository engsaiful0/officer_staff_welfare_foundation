<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $members;

    public function __construct($members)
    {
        $this->members = $members;
    }

    public function collection()
    {
        return $this->members;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Father Name',
            'Email',
            'Mobile',
            'NID Number',
            'Unique ID',
            'Member Unique ID',
            'Designation',
            'Branch',
            'Religion',
            'Date of Join',
            'Present Address',
            'Permanent Address',
            'Introducer',
            'Nominee Name',
            'Nominee Relation',
            'Nominee Phone',
            'Created At'
        ];
    }

    public function map($member): array
    {
        return [
            $member->id,
            $member->name,
            $member->father_name,
            $member->email,
            $member->mobile,
            $member->nid_number,
            $member->unique_id,
            $member->member_unique_id,
            $member->designation ? $member->designation->designation_name : 'N/A',
            $member->branch ? $member->branch->branch_name : 'N/A',
            $member->religion ? $member->religion->religion_name : 'N/A',
            $member->date_of_join ? $member->date_of_join->format('Y-m-d') : 'N/A',
            $member->present_address,
            $member->permanent_address,
            $member->introducer ? $member->introducer->name : 'N/A',
            $member->nominee_name ?: 'N/A',
            $member->nomineeRelation ? $member->nomineeRelation->relation_name : 'N/A',
            $member->nominee_phone ?: 'N/A',
            $member->created_at ? $member->created_at->format('Y-m-d H:i:s') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

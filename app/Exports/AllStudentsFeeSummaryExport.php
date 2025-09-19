<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllStudentsFeeSummaryExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $feeSummaries;
    protected $stats;
    protected $academicYear;

    public function __construct($feeSummaries, $stats, $academicYear)
    {
        $this->feeSummaries = $feeSummaries;
        $this->stats = $stats;
        $this->academicYear = $academicYear;
    }

    public function array(): array
    {
        $data = [];
        
        // Summary Statistics
        $data[] = ['FEE COLLECTION SUMMARY'];
        $data[] = [];
        $data[] = ['Total Students', $this->stats['total_students']];
        $data[] = ['Students with Complete Fees', $this->stats['students_with_complete_fees']];
        $data[] = ['Students with Partial Fees', $this->stats['students_with_partial_fees']];
        $data[] = ['Students with No Fees', $this->stats['students_with_no_fees']];
        $data[] = ['Total Expected Fees', number_format($this->stats['total_expected_fees'], 2)];
        $data[] = ['Total Collected Fees', number_format($this->stats['total_collected_fees'], 2)];
        $data[] = ['Total Due Fees', number_format($this->stats['total_due_fees'], 2)];
        $data[] = ['Collection Percentage', $this->stats['collection_percentage'] . '%'];
        $data[] = [];
        
        // Academic Year
        if ($this->academicYear) {
            $data[] = ['Academic Year', $this->academicYear->academic_year_name];
            $data[] = [];
        }
        
        // Students Details
        $data[] = ['STUDENTS FEE DETAILS'];
        $data[] = [
            'Student ID',
            'Student Name',
            'Technology',
            'Semester Fees Paid',
            'Monthly Fees Paid',
            'Total Paid',
            'Total Due',
            'Completion %',
            'Status'
        ];
        
        foreach ($this->feeSummaries as $summary) {
            $student = $summary->student;
            $completionPercentage = $summary->total_fees > 0 ? 
                round(($summary->total_paid / $summary->total_fees) * 100, 2) : 0;
            
            $data[] = [
                $student->student_unique_id ?? 'N/A',
                $student->full_name_in_english_block_letter,
                $student->technology->technology_name ?? 'N/A',
                $summary->semesters_completed . '/8',
                $summary->months_completed . '/48',
                number_format($summary->total_paid, 2),
                number_format($summary->total_due, 2),
                $completionPercentage . '%',
                $summary->all_fees_paid ? 'Complete' : 'Incomplete'
            ];
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            6 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
            10 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
        ];
    }

    public function title(): string
    {
        return 'All Students Fee Summary';
    }
}

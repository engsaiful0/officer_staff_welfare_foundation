@extends('layouts/layoutMaster')

@section('title', 'Student Details')
<style>
    @media print {
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* Hide navigation, buttons, etc. */
        .no-print,
        nav,
        .btn,
        footer {
            display: none !important;
        }

        /* Header row layout */
        .header {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: nowrap;
        }

        .header .col-md-2,
        .header .col-md-8 {
            float: none !important;
            /* Remove Bootstrap float */
            width: auto !important;
            /* Let flex handle width */
            text-align: center;
            display: inline-block;
            vertical-align: top;
        }

        .header .col-md-2:first-child {
            text-align: left;
        }

        .header .col-md-2:last-child {
            text-align: right;
        }



        /* Table print formatting */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        table th,
        table td {
            border: 1px solid #000 !important;
            padding: 5px !important;
            font-size: 12px !important;
        }

        /* Avoid page break inside table rows */
        tr {
            page-break-inside: avoid;
        }

        /* Optional: adjust headings */
        h1,
        h2,
        h3,
        p {
            margin: 0;
            padding: 0;
        }

        .logo-div {
            text-align: left;
            width: 20%;
            float: left;

        }

        .name-div {
            text-align: center;
            width: 60%;
            float: left;
        }

        .picture-div {
            width: 20%;
            float: right;
        }
    }
</style>

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Student Details</h5>
            <div>
                {{-- Print Button --}}
                <button class="btn btn-primary me-2" onclick="printStudentDetails()">
                    <i class="fas fa-print"></i> Print
                </button>


            </div>
        </div>

        <div class="card-body" id="student-details">
            {{-- Header Row --}}
            <div class="row mb-3 header">
                <div class="col-md-2 logo-div">
                    <img style="width: 70%; height:130px;"
                        src="{{ optional($appSetting)->logo ? asset('assets/img/branding/' . $appSetting->logo) : asset('storage/default.png') }}"
                        alt="Logo" class="img-fluid">
                </div>
                <div class="col-md-8 text-center name-div">
                    <h1>বাংলাদেশ কারিগরি শিক্ষা বোর্ড, ঢাকা</h1>
                    <h2>{{ optional($appSetting)->app_name ?? 'My Application' }}</h2>
                    <p>ভর্তি ফরম</p>
                </div>
                <div class="col-md-2 picture-div">
                    <img style="width: 70%; height:130px;"
                        src="{{ $student->picture ? asset('assets/images/students/' . $student->picture) : asset('storage/default.png') }}"
                        alt="Student Picture" class="img-fluid">
                </div>
            </div>

            {{-- Student Details Table --}}
            <div class="row mt-3">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped table-hover" style="width: 100%">
                        <tr>
                            <th>Serial</th>
                            <td>{{ $student->serial ?? ($nextSerial ?? '-') }}</td>
                            <th>Name (Bangla)</th>
                            <td>{{ $student->full_name_in_banglai ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Name (English)</th>
                            <td>{{ $student->full_name_in_english_block_letter ?? '-' }}</td>
                            <th>Father's Name (Bangla)</th>
                            <td>{{ $student->father_name_in_banglai ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Father's Name (English)</th>
                            <td>{{ $student->father_name_in_english_block_letter ?? '-' }}</td>
                            <th>Mother's Name (Bangla)</th>
                            <td>{{ $student->mother_name_in_banglai ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Mother's Name (English)</th>
                            <td>{{ $student->mother_name_in_english_block_letter ?? '-' }}</td>
                            <th>Guardian's Name</th>
                            <td>{{ $student->guardian_name_absence_of_father ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $student->all_communication_number ?? '-' }}</td>
                            <th>Present Address</th>
                            <td>{{ $student->present_address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Permanent Address</th>
                            <td>{{ $student->permanent_address ?? '-' }}</td>
                            <th>Date of Birth</th>
                            <td>{{ $student->date_of_birth ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td>{{ $student->gender ?? '-' }}</td>
                            <th>SSC Institute Name</th>
                            <td>{{ $student->ssc_or_equivalent_institute_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>SSC Institute Address</th>
                            <td>{{ $student->ssc_or_equivalent_institute_address ?? '-' }}</td>
                            <th>SSC Number Potro</th>
                            <td>{{ $student->ssc_or_equivalent_number_potro ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>SSC Roll Number</th>
                            <td>{{ $student->ssc_or_equivalent_roll_number ?? '-' }}</td>
                            <th>SSC Registration Number</th>
                            <td>{{ $student->ssc_or_equivalent_registration_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>SSC Session</th>
                            <td>{{ $student->ssc_or_equivalent_session ?? '-' }}</td>
                            <th>SSC Passing Year</th>
                            <td>{{ $student->ssc_or_equivalent_passing_year ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>SSC GPA</th>
                            <td>{{ $student->ssc_or_equivalent_gpa ?? '-' }}</td>
                            <th>Nationality</th>
                            <td>{{ optional($student->nationality)->nationality_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Religion</th>
                            <td>{{ optional($student->religion)->religion_name ?? '-' }}</td>
                            <th>Board</th>
                            <td>{{ optional($student->board)->board_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Technology</th>
                            <td>{{ optional($student->technology)->technology_name ?? '-' }}</td>
                            <th>Academic Year</th>
                            <td>{{ optional($student->academic_year)->academic_year_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Shift</th>
                            <td>{{ optional($student->shift)->shift_name ?? '-' }}</td>
                            <th>Semester</th>
                            <td>{{ optional($student->semester)->semester_name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Print Script --}}
    <script>
        function printStudentDetails() {
            var printContents = document.getElementById('student-details').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
@endsection

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Details PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 100px;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .student-table th,
        .student-table td {
            border: 1px solid #000;
            padding: 6px 10px;
        }
        .student-table th {
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="row mb-3">
            <div class="col-md-2">
                <img style="width: 70%; height:130px;"
                     src="{{ optional($appSetting)->logo ? asset('assets/img/branding/' . $appSetting->logo) : asset('storage/default.png') }}"
                     alt="Logo" class="img-fluid">
            </div>
            <div class="col-md-8 text-center">
                <h1>বাংলাদেশ কারিগরি শিক্ষা বোর্ড, ঢাকা</h1>
                <h2>{{ optional($appSetting)->app_name ?? 'My Application' }}</h2>
                <p>ভর্তি ফরম</p>
            </div>
            <div class="col-md-2">
                <img style="width: 70%; height:130px;"
                     src="{{ $student->picture ? asset('storage/pictures/' . $student->picture) : asset('storage/default.png') }}"
                     alt="Student Picture" class="img-fluid">
            </div>
        </div>

    {{-- Student Table --}}
    <table class="student-table">
        <tr>
            <th>Serial</th>
            <td>{{ $student->serial ?? '-' }}</td>
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

</body>
</html>

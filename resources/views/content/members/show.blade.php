@extends('layouts/layoutMaster')

@section('title', 'View Member')

@section('page-script')
<script>
    function printMember() {
        window.print();
    }

</script>
@endsection

@section('content')
<div class="card" id="member-detail-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="card-title mb-0">Member Details</h5>
        <div class="d-flex gap-2 no-print">
            <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">
                <i class="bx bx-edit me-1"></i>Edit
            </a>
            <a href="{{ route('members.view-member') }}" class="btn btn-outline-secondary">
                <i class="bx bx-list-ul me-1"></i>Back to List
            </a>
            <button type="button" class="btn btn-success" onclick="printMember()">
                <i class="bx bx-printer me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="8">Personal Information</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td  class="fw-bold">Name</td>
                            <td>{{ $member->name ?? '—' }}</td>
                            <td class="fw-bold">Father Name</td>
                            <td >{{ $member->father_name ?? '—' }}</td>
                            <td class="fw-bold">Mother Name</td>
                            <td>{{ $member->mother_name ?? '—' }}</td>
                            <td class="fw-bold">Spouse Name</td>
                            <td>{{ $member->spouse_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Mobile</td>
                            <td>{{ $member->mobile ?? '—' }}</td>
                            <td class="fw-bold">Email</td>
                            <td>{{ $member->email ?? '—' }}</td>
                            <td class="fw-bold">NID Number</td>
                            <td>{{ $member->nid_number ?? '—' }}</td>
                            <td class="fw-bold">Picture</td>
                            <td></td>{{ $member->picture ?? '—' }}</td>

                        </tr>
                        <tr>
                            <td class="fw-bold">Present Address</td>
                            <td>{{ $member->present_address ?? '—' }}</td>
                            <td class="fw-bold">Permanent Address</td>
                            <td>{{ $member->permanent_address ?? '—' }}</td>
                            <td class="fw-bold">Designation</td>
                            <td>{{ $member->designation?->designation_name ?? '—' }}</td>
                            <td class="fw-bold">Date of Birth</td>
                            <td>{{ $member->date_of_birth ?? '—' }}</td>

                        </tr>
                        <tr>
                            <td class="fw-bold">Date of Join</td>
                            <td>{{ $member->date_of_join ?? '—' }}</td>
                            <td class="fw-bold">Branch</td>
                            <td>{{ $member->branch?->branch_name ?? '—' }}</td>
                            <td class="fw-bold">Religion</td>
                            <td>{{ $member->religion?->religion_name ?? '—' }}</td>
                            <td class="fw-bold">Religion</td>
                            <td>{{ $member->religion?->religion_name ?? '—' }}</td>
                       
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-hover mt-3">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="8">Professional Information</th>
                        </tr>
                    </thead>
                    <tr>
                        <td class="fw-bold">Designation</td>
                        <td>{{ $member->designation?->designation_name ?? '—' }}</td>
                        <td class="fw-bold">Date of Join in IBBL</td>
                        <td>{{ $member->date_of_join ?? '—' }}</td>
                        <td class="fw-bold">Branch</td>
                        <td>{{ $member->branch?->branch_name ?? '—' }}</td>
                        <td class="fw-bold">Employees ID</td>
                        <td>{{ $member->employees_id ?? '—' }}</td>
                    </tr>
                    <tr>   
                        <td class="fw-bold">Member ID</td>
                        <td>{{ $member->member_unique_id ?? '—' }}</td>
                        <td class="fw-bold">Account Opening Date</td>
                        <td>{{ $member->account_opening_date ?? '—' }}</td>
                        <td class="fw-bold">Account Opening Date</td>
                        <td>{{ $member->account_opening_date ?? '—' }}</td>
                        <td class="fw-bold">Deposit Account Number</td>
                        <td>{{ $member->diposit_account_number ?? '—' }}</td>

                    </tr>
                </table>
                <table class="table table-bordered table-hover mt-3">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="8">Nominee Information</th>
                        </tr>
                    </thead>
                    <tr>
                        <td class="fw-bold">Nominee Name</td>
                        <td>{{ $member->nominee_name ?? '—' }}</td>
                        <td class="fw-bold">Nominee Father Name</td>
                        <td>{{ $member->nominee_father_name ?? '—' }}</td>
                        <td class="fw-bold">Nominee Mother Name</td>
                        <td>{{ $member->nominee_mother_name ?? '—' }}</td>
                        <td class="fw-bold">Nominee Spouse Name</td>
                        <td>{{ $member->nominee_spouse_name ?? '—' }}</td>
                        <td class="fw-bold">Nominee Relation</td>
                        <td>{{ $member->nomineeRelation?->relation_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Nominee Phone</td>
                        <td>{{ $member->nominee_phone ?? '—' }}</td>
                        <td class="fw-bold">Nominee NID Number</td>
                        <td>{{ $member->nominee_nid_number ?? '—' }}</td>
                        <td class="fw-bold">Nominee Present Address</td>
                        <td>{{ $member->nominee_present_address ?? '—' }}</td>
                        <td class="fw-bold">Nominee Permanent Address</td>
                        <td>{{ $member->nominee_permanent_address ?? '—' }}</td>
                        <td>Picture</td>
                        <td>{{ $member->nominee_picture ?? '—' }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style media="print">
        @media print {

            .no-print,
            nav,
            aside,
            .navbar,
            .footer,
            .layout-menu,
            .layout-overlay,
            .btn,
            a[href]:not(.print-link) {
                display: none !important;
            }

            body {
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .card-header {
                border-bottom: 1px solid #ddd !important;
            }
        }

    </style>
    @endsection

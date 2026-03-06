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
            <!-- Personal Information -->
            <div class="col-12">
                <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">Personal Information</h6>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Name</label>
                <p class="mb-0 fw-medium">{{ $member->name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Father Name</label>
                <p class="mb-0">{{ $member->father_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Mother Name</label>
                <p class="mb-0">{{ $member->mother_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Spouse Name</label>
                <p class="mb-0">{{ $member->spouse_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Mobile</label>
                <p class="mb-0">{{ $member->mobile ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Email</label>
                <p class="mb-0">{{ $member->email ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">NID Number</label>
                <p class="mb-0">{{ $member->nid_number ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Picture</label>
                <div class="mb-0">
                    @if($member->picture)
                        <img src="{{ asset('storage/' . $member->picture) }}" alt="{{ $member->name }}" class="img-thumbnail" style="max-width: 100px;">
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Deposit Account Number</label>
                <p class="mb-0">{{ $member->diposit_account_number ?? '—' }}</p>
            </div>

            <!-- Professional Information -->
            <div class="col-12 mt-4">
                <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">Professional Information</h6>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Designation</label>
                <p class="mb-0">{{ $member->designation?->designation_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Date of Birth</label>
                <p class="mb-0">{{ is_object($member->date_of_birth) && method_exists($member->date_of_birth, 'format') ? $member->date_of_birth->format('Y-m-d') : ($member->date_of_birth ?? '—') }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Date of Join</label>
                <p class="mb-0">{{ is_object($member->date_of_join) && method_exists($member->date_of_join, 'format') ? $member->date_of_join->format('Y-m-d') : ($member->date_of_join ?? '—') }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Branch</label>
                <p class="mb-0">{{ $member->branch?->branch_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Religion</label>
                <p class="mb-0">{{ $member->religion?->religion_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Employees ID</label>
                <p class="mb-0">{{ $member->employees_id ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Member ID</label>
                <p class="mb-0">{{ $member->member_unique_id ?? '—' }}</p>
            </div>

            <!-- Address Information -->
            <div class="col-12 mt-4">
                <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">Address Information</h6>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted small">Present Address</label>
                <p class="mb-0">{{ $member->present_address ?? '—' }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted small">Permanent Address</label>
                <p class="mb-0">{{ $member->permanent_address ?? '—' }}</p>
            </div>

            <!-- Nominee Information -->
            <div class="col-12 mt-4">
                <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">Nominee Information</h6>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Nominee Name</label>
                <p class="mb-0">{{ $member->nominee_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Nominee Father Name</label>
                <p class="mb-0">{{ $member->nominee_father_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Nominee Mother Name</label>
                <p class="mb-0">{{ $member->nominee_mother_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Nominee Spouse Name</label>
                <p class="mb-0">{{ $member->nominee_spouse_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Nominee Relation</label>
                <p class="mb-0">{{ $member->nomineeRelation?->relation_name ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Nominee Phone</label>
                <p class="mb-0">{{ $member->nominee_phone ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Nominee NID Number</label>
                <p class="mb-0">{{ $member->nominee_nid_number ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Nominee Present Address</label>
                <p class="mb-0">{{ $member->nominee_present_address ?? '—' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-muted small">Nominee Permanent Address</label>
                <p class="mb-0">{{ $member->nominee_permanent_address ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>

<style media="print">
    @media print {
        .no-print, nav, aside, .navbar, .footer, .layout-menu, .layout-overlay, .btn, a[href]:not(.print-link) { display: none !important; }
        body { padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .card-header { border-bottom: 1px solid #ddd !important; }
    }
</style>
@endsection

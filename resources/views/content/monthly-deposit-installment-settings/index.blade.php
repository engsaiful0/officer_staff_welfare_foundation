@extends('layouts/layoutMaster')

@section('title', 'Deposit Installment Setup')

@section('page-script')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.monthlyDepositInstallmentSettingsUrls = {
        show: '{{ url("app/members/monthly-deposit-installment-settings") }}',
        update: '{{ url("app/members/monthly-deposit-installment-settings") }}',
        destroy: '{{ url("app/members/monthly-deposit-installment-settings") }}'
    };
</script>
<script src="{{ asset('assets/js/monthly-deposit-installment-settings.js') }}?v={{ time() }}"></script>
@endsection

@section('content')
@php
    $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];
@endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="card-title mb-0">Deposit Installment Setup</h5>
        <a href="{{ route('members.monthly-deposit-installment-settings.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>Create
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('members.monthly-deposit-installment-settings.index') }}" class="row g-2 mb-3">
            <div class="col-md-4">
                <label class="form-label">Member</label>
                <select class="form-select" name="member_id">
                    <option value="">All Members</option>
                    @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->unique_id ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Member</th>
                        <th>Installment Amount</th>
                        <th>Date</th>
                     
                        <th>User</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($installments as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->member ? $row->member->name : '—' }} <span class="text-muted">({{ $row->member ? $row->member->unique_id : '—' }})</span></td>
                        <td>{{ number_format((float)$row->installment_amount, 2) }}</td>
                       
                        <td>{{ $row->date ? (is_object($row->date) && method_exists($row->date, 'format') ? $row->date->format('M d, Y') : $row->date) : '—' }}</td>
                        <td>{{ $row->user ? $row->user->name : '—' }}</td>
                        <td>
                            <a href="javascript:;" class="btn btn-sm btn-icon view-record" data-id="{{ $row->id }}" title="View"><i class="ti ti-eye"></i></a>
                            <a href="javascript:;" class="btn btn-sm btn-icon edit-record" data-id="{{ $row->id }}" title="Edit"><i class="ti ti-pencil"></i></a>
                            <a href="javascript:;" class="btn btn-sm btn-icon text-danger delete-record" data-id="{{ $row->id }}" title="Delete"><i class="ti ti-trash"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No deposit installments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($installments->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $installments->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: View -->
<div class="modal fade" id="view-installment-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Deposit Installment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="view-installment-body">
                <div id="view-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 mb-0">Loading...</p>
                </div>
                <div id="view-content" class="d-none"></div>
            </div>
        </div>
    </div>
</div>

<!-- Offcanvas: Edit -->
<div class="offcanvas offcanvas-end" id="edit-installment-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Update Deposit Installment</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <div id="edit-loading" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 mb-0">Loading...</p>
        </div>
        <form id="form-edit-installment" class="pt-0 row g-2 d-none">
            <input type="hidden" id="edit_id" name="id">
            <div class="col-sm-12">
                <label class="form-label" for="edit_member_id">Member <span class="text-danger">*</span></label>
                <select class="form-select select2" id="edit_member_id" name="member_id" required>
                    <option value="">Select Member</option>
                    @foreach($members as $m)
                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unique_id ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="edit_installment_amount">Installment Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" id="edit_installment_amount" name="installment_amount" class="form-control" required>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="edit_date">Date <span class="text-danger">*</span></label>
                <input type="date" id="edit_date" name="date" class="form-control" required>
            </div>
            
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" id="edit-submit-btn">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="edit-spinner" role="status"></span>
                    <span id="edit-submit-text">Update</span>
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

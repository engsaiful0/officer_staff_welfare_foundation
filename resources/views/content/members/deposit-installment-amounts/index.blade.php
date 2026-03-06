@extends('layouts/layoutMaster')

@section('title', 'Deposit Installment Amount')

@section('page-script')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.depositInstallmentUrls = {
        getData: '{{ url("app/members/deposit-installment-amounts/get-data") }}',
        lastAmount: '{{ url("app/members/deposit-installment-amounts/last-amount") }}',
        store: '{{ url("app/members/deposit-installment-amounts") }}',
        destroy: '{{ url("app/members/deposit-installment-amounts") }}'
    };
</script>
<script src="{{ asset('assets/js/deposit-installment-amounts.js') }}?v={{ time() }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Member</th>
                    <th>Installment Amount</th>
                    <th>Date</th>
                    <th>Recorded By</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Offcanvas: Add new installment -->
<div class="offcanvas offcanvas-end" id="add-new-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Add Deposit Installment</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <p class="text-muted small">Member always pays the last installment amount — it will be pre-filled when you select a member.</p>
        <form id="form-add-installment" class="pt-0 row g-2">
            <div class="col-sm-12">
                <label class="form-label" for="member_id">Member <span class="text-danger">*</span></label>
                <select class="form-select select2" id="member_id" name="member_id" required>
                    <option value="">Select Member</option>
                    @foreach($members as $m)
                    <option value="{{ $m->id }}" data-name="{{ $m->name }}">{{ $m->name }} ({{ $m->unique_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="installment_amount">Installment Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" id="installment_amount" name="installment_amount" class="form-control" placeholder="0.00" required>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                <input type="date" id="date" name="date" class="form-control" required>
            </div>
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="submit-spinner" role="status"></span>
                    <span id="submit-text">Save</span>
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

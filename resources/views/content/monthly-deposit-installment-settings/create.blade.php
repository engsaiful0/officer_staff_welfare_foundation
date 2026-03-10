@extends('layouts/layoutMaster')

@section('title', 'Add Deposit Installment')

@section('page-script')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.depositInstallmentCreate = {
        storeUrl: '{{ route("members.monthly-deposit-installment-settings.store") }}',
        indexUrl: '{{ route("members.monthly-deposit-installment-settings.index") }}',
        lastAmountUrl: '{{ url("app/members/monthly-deposit-installment-settings/last-amount") }}',
        members: @json($membersJson ?? [])
    };
</script>
<script src="{{ asset('assets/js/deposit-installment-create.js') }}?v={{ time() }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Add Deposit Installment</h5>
        <a href="{{ route('members.monthly-deposit-installment-settings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Back to List
        </a>
    </div>
    <div class="card-body">
        <form id="form-deposit-installment-create" class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="member_id">Member <span class="text-danger">*</span></label>
                <select class="form-select select2" id="member_id" name="member_id" required>
                    <option value="">Select Member</option>
                    @if(isset($members) && count($members) > 0)
                    @foreach($members as $m)
                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unique_id ?? '' }})</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="installment_amount">Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" id="installment_amount" name="installment_amount" class="form-control" placeholder="0.00" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                <input type="date" id="date" name="date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="month">Month</label>
                <select class="form-select" id="month" name="month">
                    <option value="">—</option>
                    @foreach([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $num => $label)
                    <option value="{{ $num }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="year">Year</label>
                <input type="number" id="year" name="year" class="form-control" placeholder="e.g. 2026" min="2000" max="2100">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="submit-spinner" role="status"></span>
                    <span id="submit-text">Save Deposit Installment</span>
                </button>
                <a href="{{ route('members.monthly-deposit-installment-settings.index') }}" class="btn btn-label-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

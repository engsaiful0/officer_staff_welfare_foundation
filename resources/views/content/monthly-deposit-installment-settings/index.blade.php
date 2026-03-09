@extends('layouts/layoutMaster')

@section('title', 'Deposit Installment Setup')

@section('page-script')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.monthlyDepositInstallmentSettingsUrls = {
        getData: '{{ url("app/members/monthly-deposit-installment-settings/get-data") }}',
        lastAmount: '{{ url("app/members/monthly-deposit-installment-settings/last-amount") }}',
        store: '{{ url("app/members/monthly-deposit-installment-settings") }}',
        show: '{{ url("app/members/monthly-deposit-installment-settings") }}',
        update: '{{ url("app/members/monthly-deposit-installment-settings") }}',
        destroy: '{{ url("app/members/monthly-deposit-installment-settings") }}',
        getMembers: '{{ route("members.monthly-deposit-installment-settings.get-members") }}'
    };
    window.membersFromServer = @json($membersJson ?? []);
</script>
<script src="{{ asset('assets/js/monthly-deposit-installment-settings.js') }}?v={{ time() }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Month</th>
                    <th>Year</th>
                    <th>User</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Offcanvas: Create -->
<div class="offcanvas offcanvas-end" id="add-new-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Create Deposit Installment</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form id="form-create-installment" class="pt-0 row g-2">
            <div class="col-sm-12">
                <label class="form-label" for="member_id">Member <span class="text-danger">*</span></label>
                <select class="form-select select2" id="member_id" name="member_id" required>
                    <option value="">Select Member</option>
                    @if(isset($members) && count($members) > 0)
                    @foreach($members as $m)
                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unique_id ?? '' }})</option>
                    @endforeach
                    @endif
                </select>
                <div id="member_id_loading" class="form-text text-muted d-none">Loading members...</div>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="installment_amount">Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" id="installment_amount" name="installment_amount" class="form-control" placeholder="0.00" required>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                <input type="date" id="date" name="date" class="form-control" required>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="month">Month</label>
                <select class="form-select" id="month" name="month">
                    <option value="">—</option>
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'] as $num => $label)
                    <option value="{{ $num }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="year">Year</label>
                <input type="number" id="year" name="year" class="form-control" placeholder="e.g. 2026" min="2000" max="2100">
            </div>
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" id="create-submit-btn">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="create-spinner" role="status"></span>
                    <span id="create-submit-text">Create</span>
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
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
                    @if(isset($members) && count($members) > 0)
                    @foreach($members as $m)
                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unique_id ?? '' }})</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="edit_installment_amount">Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" id="edit_installment_amount" name="installment_amount" class="form-control" required>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="edit_date">Date <span class="text-danger">*</span></label>
                <input type="date" id="edit_date" name="date" class="form-control" required>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="edit_month">Month</label>
                <select class="form-select" id="edit_month" name="month">
                    <option value="">—</option>
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'] as $num => $label)
                    <option value="{{ $num }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="edit_year">Year</label>
                <input type="number" id="edit_year" name="year" class="form-control" min="2000" max="2100">
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

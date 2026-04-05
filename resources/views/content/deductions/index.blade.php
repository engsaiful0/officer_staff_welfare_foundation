@extends('layouts/contentNavbarLayout')

@section('title', 'Deductions')

@section('content')
@php
  $canManageDeductions = auth()->check() && auth()->user()->hasPermissionTo('add-deduction');
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
  @permission('add-deduction')
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-primary">
        <div class="card-header">
          <h5 class="card-title mb-0">Generate monthly deductions (all active members)</h5>
          <p class="small text-muted mb-0 mt-1">Creates or updates one deduction row per member who has an active deposit, investment, or qard. Only month and year are required.</p>
        </div>
        <div class="card-body">
          <form id="generateMonthlyForm" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-3">
              <label for="gen_month" class="form-label">Month <span class="text-danger">*</span></label>
              <select class="form-select" id="gen_month" name="month" required>
                @for($m = 1; $m <= 12; $m++)
                  <option value="{{ $m }}" {{ (int) date('n') === $m ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                  </option>
                @endfor
              </select>
            </div>
            <div class="col-md-2">
              <label for="gen_year" class="form-label">Year <span class="text-danger">*</span></label>
              @php $cy = (int) date('Y'); @endphp
              <select class="form-select" id="gen_year" name="year" required>
                @for($y = $cy - 5; $y <= $cy + 2; $y++)
                  <option value="{{ $y }}" {{ $cy === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
            </div>
            <div class="col-md-3">
              <label for="gen_deduction_date" class="form-label">Deduction date</label>
              <input type="date" class="form-control" id="gen_deduction_date" name="deduction_date"
                     value="{{ date('Y-m-01') }}">
              <div class="form-text">Defaults to the 1st of the selected month if left empty at submit.</div>
            </div>
            <div class="col-md-4">
              <label for="gen_remarks" class="form-label">Remarks (all rows)</label>
              <input type="text" class="form-control" id="gen_remarks" name="remarks" maxlength="5000" placeholder="Optional">
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary" id="generateBtn">
                <span class="spinner-border spinner-border-sm me-2 d-none" id="generateSpinner" role="status"></span>
                <i class="bx bx-list-plus me-1" id="generateIcon"></i>
                <span id="generateText">Generate list</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endpermission

  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
          <h5 class="card-title mb-0">Member deductions</h5>
          @permission('add-deduction')
          <a href="{{ route('deductions.add-deduction') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus me-1"></i> Add deduction
          </a>
          @endpermission
        </div>
        <div class="card-body">
          <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
              <label for="member_id" class="form-label">Member</label>
              <select class="select2 form-select" id="member_id" name="member_id">
                <option value="">All</option>
                @foreach($members as $m)
                  <option value="{{ $m->id }}" {{ (string) request('member_id') === (string) $m->id ? 'selected' : '' }}>
                    {{ $m->name }} ({{ $m->unique_id }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label for="month" class="form-label">Month</label>
              <select class="form-select" id="month" name="month">
                <option value="">All</option>
                @for($m = 1; $m <= 12; $m++)
                  <option value="{{ $m }}" {{ (string) request('month') === (string) $m ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                  </option>
                @endfor
              </select>
            </div>
            <div class="col-md-2">
              <label for="year" class="form-label">Year</label>
              <input type="number" class="form-control" id="year" name="year" min="2000" max="2100"
                     value="{{ request('year') }}" placeholder="Year">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
              <button type="submit" class="btn btn-primary">Filter</button>
              <a href="{{ route(request()->route()->getName()) }}" class="btn btn-outline-secondary">Clear</a>
            </div>
          </form>

          @if($deductions->count() > 0)
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Member</th>
                    <th>Period</th>
                    <th class="text-end">Deposit</th>
                    <th class="text-end">Investment</th>
                    <th class="text-end">Qard</th>
                    <th class="text-end">Profit</th>
                    <th class="text-end">Compensation</th>
                    <th class="text-end">Total</th>
                    <th>Date</th>
                    <th>Recorded by</th>
                    @permission('add-deduction')
                    <th class="text-end" style="width: 120px;">Actions</th>
                    @endpermission
                  </tr>
                </thead>
                <tbody>
                  @foreach($deductions as $d)
                    <tr data-deduction-row="{{ $d->id }}">
                      <td>
                        @if($d->member)
                          <span class="fw-semibold">{{ $d->member->name }}</span>
                          <span class="text-muted small">({{ $d->member->unique_id }})</span>
                        @else
                          —
                        @endif
                      </td>
                      <td>{{ date('F', mktime(0, 0, 0, $d->month, 1)) }} {{ $d->year }}</td>
                      <td class="text-end">{{ number_format($d->monthly_deposit_amount, 2) }}</td>
                      <td class="text-end">{{ number_format($d->monthly_investment_amount, 2) }}</td>
                      <td class="text-end">{{ number_format($d->monthly_qard_amount, 2) }}</td>
                      <td class="text-end">{{ number_format($d->profit_on_deposit_amount, 2) }}</td>
                      <td class="text-end">{{ number_format($d->compensation_on_investment_amount, 2) }}</td>
                      <td class="text-end fw-semibold">{{ number_format($d->total_amount, 2) }}</td>
                      <td>{{ $d->deduction_date?->format('Y-m-d') }}</td>
                      <td>{{ $d->user?->name ?? '—' }}</td>
                      @permission('add-deduction')
                      <td class="text-end text-nowrap">
                        <a href="{{ route('deductions.edit', $d) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                          <i class="bx bx-edit-alt"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-deduction" title="Delete"
                                data-url="{{ route('deductions.destroy', $d) }}">
                          <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                          <i class="bx bx-trash delete-icon"></i>
                        </button>
                      </td>
                      @endpermission
                    </tr>
                    @if($d->remarks)
                      <tr class="table-light">
                        <td colspan="{{ $canManageDeductions ? 11 : 10 }}" class="small text-muted">
                          <strong>Remarks:</strong> {{ $d->remarks }}
                        </td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="mt-3">
              {{ $deductions->links() }}
            </div>
          @else
            <p class="text-muted mb-0">No deductions found.</p>
            @permission('add-deduction')
            <a href="{{ route('deductions.add-deduction') }}" class="btn btn-sm btn-primary mt-2">Add first deduction</a>
            @endpermission
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
window.deductionGenerateUrl = @json(route('deductions.generate-monthly'));
window.deductionListBaseUrl = @json(route('deductions.monthly-deduction-list'));
</script>
<script>
jQuery(document).ready(function($) {
  if ($.fn.select2) {
    $('.select2').select2({ width: '100%' });
  }

  const csrf = $('meta[name="csrf-token"]').attr('content');

  $('#generateMonthlyForm').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    const btn = $('#generateBtn');
    const sp = $('#generateSpinner');
    const icon = $('#generateIcon');
    const txt = $('#generateText');
    if (btn.prop('disabled')) return;

    const month = $('#gen_month').val();
    const year = $('#gen_year').val();
    const deductionDateVal = $('#gen_deduction_date').val();

    btn.prop('disabled', true);
    sp.removeClass('d-none');
    icon.addClass('d-none');
    txt.text('Generating…');

    $.ajax({
      url: window.deductionGenerateUrl,
      type: 'POST',
      data: {
        _token: csrf,
        month: month,
        year: year,
        deduction_date: deductionDateVal || null,
        remarks: $('#gen_remarks').val() || null
      },
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (res.success) {
          if (typeof toastr !== 'undefined') toastr.success(res.message || 'Done');
          else alert(res.message || 'Done');
          const q = '?month=' + encodeURIComponent(month) + '&year=' + encodeURIComponent(year);
          window.location.href = window.deductionListBaseUrl + q;
        } else {
          if (typeof toastr !== 'undefined') toastr.error(res.message || 'Failed');
          else alert(res.message || 'Failed');
        }
      },
      error: function(xhr) {
        const j = xhr.responseJSON || {};
        const msg = j.message || 'Generation failed.';
        if (typeof toastr !== 'undefined') toastr.error(msg);
        else alert(msg);
      },
      complete: function() {
        btn.prop('disabled', false);
        sp.addClass('d-none');
        icon.removeClass('d-none');
        txt.text('Generate list');
      }
    });
  });

  $(document).on('click', '.btn-delete-deduction', function() {
    const btn = $(this);
    if (!confirm('Delete this deduction?')) return;
    const url = btn.data('url');
    const sp = btn.find('.spinner-border');
    const ic = btn.find('.delete-icon');
    btn.prop('disabled', true);
    sp.removeClass('d-none');
    ic.addClass('d-none');

    $.ajax({
      url: url,
      type: 'POST',
      data: { _token: csrf, _method: 'DELETE' },
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (res.success) {
          if (typeof toastr !== 'undefined') toastr.success(res.message || 'Deleted');
          const row = btn.closest('tr');
          if (row.next('tr.table-light').length) {
            row.next('tr.table-light').remove();
          }
          row.remove();
        }
      },
      error: function(xhr) {
        const j = xhr.responseJSON || {};
        if (typeof toastr !== 'undefined') toastr.error(j.message || 'Delete failed');
        else alert(j.message || 'Delete failed');
        btn.prop('disabled', false);
        sp.addClass('d-none');
        ic.removeClass('d-none');
      }
    });
  });
});
</script>
@endsection

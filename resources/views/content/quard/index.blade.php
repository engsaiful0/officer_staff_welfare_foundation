@extends('layouts/contentNavbarLayout')

@section('title', 'Quards - List')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Quards</h5>
    <a href="{{ route('quards.add-quard') }}" class="btn btn-primary">
      <i class="bx bx-plus me-1"></i> Add Quard
    </a>
  </div>

  <div class="card-body">
    <div class="row mb-4">
      <div class="col-md-4">
        <label for="member_filter" class="form-label">Member</label>
        <select class="form-select select2" id="member_filter">
          <option value="">All Members</option>
          @foreach($members as $m)
            <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->unique_id }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label for="status_filter" class="form-label">Status</label>
        <select class="form-select" id="status_filter">
          <option value="">All</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="matured" {{ request('status') === 'matured' ? 'selected' : '' }}>Matured</option>
          <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
      </div>
      <div class="col-md-5 d-flex align-items-end">
        <a href="{{ route('quards.view-quards') }}" class="btn btn-secondary">Clear Filters</a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered" id="quardsTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Member</th>
          
            <th>Quard Amount</th>
            <th>Period (Years)</th>
            <th>Installment Number</th>
    
            <th>Charge Amount</th>
            <th>Total Payable Amount</th>
            <th>Installment Amount</th>
            <th>Start Date</th>
            <th>Maturity Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($quards as $q)
            <tr>
              <td>{{ $quards->firstItem() + $loop->index }}</td>
              <td>{{ $q->member?->name }} ({{ $q->member?->unique_id }})</td>
              
              <td>{{ number_format((float) $q->quard_amount, 2) }}</td>
              <td>{{ $q->period_in_years }}</td>
              <td>{{ $q->installment_number }}</td>
              
              <td>{{ number_format((float) $q->charge_amount, 2) }}</td>
              <td>{{ number_format((float) $q->total_payable_amount, 2) }}</td>
              <td>{{ number_format((float) $q->installment_amount, 2) }}</td>
              <td>{{ $q->start_date ? $q->start_date->format('Y-m-d') : '' }}</td>
              <td>{{ $q->maturity_date ? $q->maturity_date->format('Y-m-d') : '' }}</td>
              <td>{{ ucfirst($q->status) }}</td>
              <td>
                <a href="{{ route('quards.show', $q) }}" class="btn btn-sm btn-outline-primary">
                  <i class="bx bx-show"></i> View
                </a>
                <a href="{{ route('quards.edit', $q) }}" class="btn btn-sm btn-outline-primary">
                  <i class="bx bx-edit-alt"></i> Edit
                </a>
                <button type="button"
                        class="btn btn-sm btn-outline-danger delete-quard"
                        data-id="{{ $q->id }}"
                        data-url="{{ route('quards.destroy', $q->id) }}">
                  <span class="delete-text"><i class="bx bx-trash"></i> Delete</span>
                  <span class="spinner-border spinner-border-sm d-none delete-spinner" role="status" aria-hidden="true"></span>
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center">
      {{ $quards->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  function applyFilters() {
    const params = new URLSearchParams();
    if ($('#member_filter').val()) params.append('member_id', $('#member_filter').val());
    if ($('#status_filter').val()) params.append('status', $('#status_filter').val());
    window.location.href = '{{ route("quards.view-quards") }}' + (params.toString() ? ('?' + params.toString()) : '');
  }

  $('#member_filter, #status_filter').on('change', applyFilters);

  $(document).on('click', '.delete-quard', function(e) {
    e.preventDefault();
    const btn = $(this);
    const deleteUrl = btn.data('url');
    const spinner = btn.find('.delete-spinner');
    const textSpan = btn.find('.delete-text');

    const doDelete = function() {
      if (btn.prop('disabled')) return;
      btn.prop('disabled', true);
      textSpan.addClass('d-none');
      spinner.removeClass('d-none');
      $.ajax({
        url: deleteUrl,
        type: 'POST',
        data: { _method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content') },
        dataType: 'json',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        success: function(resp) {
          if (resp.success) {
            if (typeof toastr !== 'undefined') toastr.success(resp.message || 'Quard deleted successfully');
            else alert(resp.message || 'Quard deleted successfully');
            setTimeout(function(){ location.reload(); }, 600);
          } else {
            if (typeof toastr !== 'undefined') toastr.error(resp.message || 'Failed to delete quard');
            else alert(resp.message || 'Failed to delete quard');
          }
        },
        error: function(xhr) {
          const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error deleting quard';
          if (typeof toastr !== 'undefined') toastr.error(msg);
          else alert(msg);
        },
        complete: function() {
          btn.prop('disabled', false);
          spinner.addClass('d-none');
          textSpan.removeClass('d-none');
        }
      });
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Are you sure?',
        text: 'This quard will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) doDelete();
      });
    } else {
      if (confirm('Are you sure you want to delete this quard?')) doDelete();
    }
  });
});
</script>
@endpush

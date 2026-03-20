@extends('layouts/contentNavbarLayout')

@section('title', 'Quard Payments - List')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Quard Payments</h5>
    <a href="{{ route('quard-payment.add-quard-payment') }}" class="btn btn-primary">
      <i class="bx bx-plus me-1"></i> Add Quard Payment
    </a>
  </div>

  <div class="card-body">
    <div class="row mb-4">
      <div class="col-md-4">
        <label for="member_filter" class="form-label">Member</label>
        <select class="form-select select2" id="member_filter">
          <option value="">All Members</option>
          @foreach($members as $m)
            <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>
              {{ $m->name }} ({{ $m->unique_id }})
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-8 d-flex align-items-end justify-content-end">
        <a href="{{ route('quard-payment.view-quard-payment') }}" class="btn btn-secondary">Clear Filters</a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered" id="quardPaymentsTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Member</th>
            <th>Payment Amount</th>
            <th>Payment Date</th>
            <th>Notes</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($payments as $p)
            <tr>
              <td>{{ $payments->firstItem() + $loop->index }}</td>
              <td>{{ $p->member?->name }} ({{ $p->member?->unique_id }})</td>
              <td>{{ number_format((float) $p->payment_amount, 2) }}</td>
              <td>{{ $p->payment_date ? $p->payment_date->format('Y-m-d') : '' }}</td>
              <td>{{ $p->notes }}</td>
              <td>
                <button type="button"
                        class="btn btn-sm btn-outline-danger delete-quard-payment"
                        data-url="{{ route('quard-payment.destroy', $p->id) }}">
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
      {{ $payments->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#member_filter').on('change', function() {
    const params = new URLSearchParams();
    if ($(this).val()) params.append('member_id', $(this).val());
    window.location.href = '{{ route("quard-payment.view-quard-payment") }}' + (params.toString() ? ('?' + params.toString()) : '');
  });

  $(document).on('click', '.delete-quard-payment', function(e) {
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
            if (typeof toastr !== 'undefined') toastr.success(resp.message || 'Quard payment deleted successfully');
            else alert(resp.message || 'Quard payment deleted successfully');
            setTimeout(function() { location.reload(); }, 700);
          } else {
            if (typeof toastr !== 'undefined') toastr.error(resp.message || 'Failed to delete quard payment');
            else alert(resp.message || 'Failed to delete quard payment');
          }
        },
        error: function(xhr) {
          const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error deleting quard payment';
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
        text: 'This quard payment will be permanently deleted.',
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
      if (confirm('Are you sure you want to delete this quard payment?')) doDelete();
    }
  });
});
</script>
@endpush

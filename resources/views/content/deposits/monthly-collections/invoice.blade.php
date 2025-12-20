@extends('layouts/contentNavbarLayout')

@section('title', 'Monthly Deposit Collection Invoice')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success!</strong> {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Monthly Deposit Collection Invoice</h5>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-primary" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Print
          </button>
          <a href="{{ route('deposits.monthly-collections.export', ['collection_id' => $collection->id, 'type' => 'pdf']) }}" class="btn btn-outline-danger" target="_blank">
            <i class="bx bx-file-blank me-1"></i> Download PDF
          </a>
          <a href="{{ route('deposits.monthly-collections.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back to List
          </a>
        </div>
      </div>
      
      <div class="card-body">
        <div class="invoice-container" style="max-width: 800px; margin: 0 auto; padding: 30px; border: 2px solid #ddd; background: white;">
          <!-- Header -->
          <div class="text-center mb-4 pb-3 border-bottom">
            <h2 class="mb-2">Monthly Deposit Collection Invoice</h2>
            <p class="mb-0 text-muted">Collection Number: <strong>{{ $collection->collection_number }}</strong></p>
          </div>

          <!-- Invoice Details -->
          <div class="row mb-4">
            <div class="col-md-6">
              <h6 class="text-muted mb-3">Collection Information</h6>
              <table class="table table-borderless table-sm">
                <tr>
                  <td width="40%" class="text-muted"><strong>Collection Date:</strong></td>
                  <td>{{ $collection->collection_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                  <td class="text-muted"><strong>Month:</strong></td>
                  <td>{{ $collection->month ?? 'N/A' }}</td>
                </tr>
                <tr>
                  <td class="text-muted"><strong>Collection Number:</strong></td>
                  <td><strong>{{ $collection->collection_number }}</strong></td>
                </tr>
              </table>
            </div>
            <div class="col-md-6">
              <h6 class="text-muted mb-3">Member Information</h6>
              <table class="table table-borderless table-sm">
                <tr>
                  <td width="40%" class="text-muted"><strong>Member Name:</strong></td>
                  <td>{{ $collection->member->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                  <td class="text-muted"><strong>Member ID:</strong></td>
                  <td>{{ $collection->member->unique_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                  <td class="text-muted"><strong>Account Number:</strong></td>
                  <td><strong>{{ $collection->deposit->deposit_account_number ?? 'N/A' }}</strong></td>
                </tr>
              </table>
            </div>
          </div>

          <!-- Amount Section -->
          <div class="text-center mb-4 p-4 bg-light rounded">
            <h6 class="text-muted mb-2">Collection Amount</h6>
            <h1 class="text-success mb-0">৳{{ number_format($collection->amount, 2) }}</h1>
            @php
              use App\Helpers\NumberToWordsHelper;
              $amountInWords = NumberToWordsHelper::convert($collection->amount, 'Taka', 'Paisa');
            @endphp
            <small class="text-muted">({{ $amountInWords }})</small>
          </div>

          <!-- Description -->
          @if($collection->description)
          <div class="mb-4">
            <h6 class="text-muted mb-2">Description</h6>
            <p class="mb-0">{{ $collection->description }}</p>
          </div>
          @endif

          <!-- Footer -->
          <div class="mt-5 pt-4 border-top">
            <div class="row">
              <div class="col-md-6">
                <p class="mb-1"><strong>Collected By:</strong></p>
                <p class="mb-0">{{ $collection->createdBy->name ?? 'N/A' }}</p>
              </div>
              <div class="col-md-6 text-end">
                <p class="mb-1"><strong>Generated On:</strong></p>
                <p class="mb-0">{{ date('M d, Y h:i A') }}</p>
              </div>
            </div>
          </div>

          <!-- Signature Section -->
          <div class="mt-5 pt-4">
            <div class="row">
              <div class="col-md-6 text-center">
                <div class="border-top pt-3" style="min-height: 80px;">
                  <p class="mb-0"><strong>Member Signature</strong></p>
                </div>
              </div>
              <div class="col-md-6 text-center">
                <div class="border-top pt-3" style="min-height: 80px;">
                  <p class="mb-0"><strong>Authorized Signature</strong></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
@media print {
  .card-header, .btn, .no-print, .alert {
    display: none !important;
  }
  .invoice-container {
    border: none !important;
    padding: 0 !important;
  }
  body {
    background: white !important;
  }
}
</style>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Show toast message from session flash if available
  @if(session('success'))
    if (typeof toastr !== 'undefined') {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        timeOut: 5000,
        extendedTimeOut: 1000,
        positionClass: 'toast-top-right',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
      };
      toastr.success('{{ session('success') }}');
    }
  @endif
});
</script>
@endsection


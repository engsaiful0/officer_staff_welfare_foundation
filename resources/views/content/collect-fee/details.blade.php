@extends('layouts/layoutMaster')

@section('title', 'Fee Collection Details')

@section('page-style')
<style>
  .invoice-header {
    margin-bottom: 2rem;
  }
  .invoice-header .logo {
    display: flex;
    align-items: center;
  }
  .invoice-header .logo img {
    max-height: 80px;
  }
  .invoice-header .logo-text {
    margin-left: 1rem;
    font-size: 1.5rem;
    font-weight: bold;
  }
  .student-info {
    margin-bottom: 2rem;
  }
  .fee-details {
    margin-top: 2rem;
  }
</style>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Fee Collection Details</h5>
      <button class="btn btn-primary" onclick="window.print()">
        <i class="ti ti-printer"></i> Print
      </button>
    </div>
  </div>
  <div class="card-body">
    <div class="invoice-header text-center">
      <div class="logo">
        @if($app_setting && $app_setting->logo)
        <img src="{{ asset('profile_pictures/'.$app_setting->logo) }}" alt="Logo">
        @endif
        <div class="logo-text">
          <h2 class="mb-0">{{ $app_setting->name ?? 'Institute Name' }}</h2>
          <p class="mb-0">{{ $app_setting->address ?? 'Institute Address' }}</p>
        </div>
      </div>
      <hr>
    </div>

    <div class="student-info">
      <h6>Student Information</h6>
      <div class="row">
        <div class="col-md-6">
          <p><strong>Student Name:</strong> {{ $feeCollect->student->full_name_in_english_block_letter ?? '' }}</p>
          <p><strong>Student ID:</strong> {{ $feeCollect->student->id ?? '' }}</p>
          <p><strong>Email:</strong> {{ $feeCollect->student->email_address ?? '' }}</p>
          <p><strong>Phone:</strong> {{ $feeCollect->student->mobile_no ?? '' }}</p>
        </div>
        <div class="col-md-6">
          <p><strong>Academic Year:</strong> {{ $feeCollect->academic_year->academic_year_name ?? '' }}</p>
          <p><strong>Year:</strong> {{ $feeCollect->year ?? 'N/A' }}</p>
          <p><strong>Semester:</strong> {{ $feeCollect->semester->semester_name ?? '' }}</p>
          <p><strong>Date of Collection:</strong> {{ $feeCollect->date }}</p>
        </div>
      </div>
    </div>

    <div class="fee-details">
      <h6>Fee Details</h6>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Fee Head</th>
              <th class="text-end">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($fee_heads as $fee_head)
            <tr>
              <td>{{ $fee_head->name }}</td>
              <td class="text-end">{{ number_format($fee_head->amount, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th class="text-end">Total Amount:</th>
              <th class="text-end">{{ number_format($feeCollect->total_amount, 2) }}</th>
            </tr>
            @if($feeCollect->discount)
            <tr>
              <th class="text-end">Discount:</th>
              <th class="text-end">{{ number_format($feeCollect->discount, 2) }}</th>
            </tr>
            @endif
            <tr>
              <th class="text-end">Net Payable:</th>
              <th class="text-end">{{ number_format($feeCollect->net_payable, 2) }}</th>
            </tr>
            <tr>
              <th class="text-end">Paid Via:</th>
              <th class="text-end">{{ $feeCollect->payment_method->name ?? '' }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

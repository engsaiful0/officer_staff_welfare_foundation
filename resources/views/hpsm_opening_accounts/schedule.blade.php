@extends('layouts/contentNavbarLayout')

@section('title', 'HPSM schedule — '.$account->account_no)

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 no-print">
    <h5 class="mb-0">Reducing schedule — {{ $account->account_no }}</h5>
    <div class="d-flex gap-2">
      <a href="{{ route('hpsm-opening-accounts.show', $account) }}" class="btn btn-sm btn-outline-secondary">Back</a>
      <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">Print</button>
      <a href="{{ route('hpsm-opening-accounts.print-schedule', $account) }}" class="btn btn-sm btn-outline-dark" target="_blank">Printer view</a>
    </div>
  </div>
  <div class="card-body">
    <p class="small text-muted">{{ $account->member?->name }} · Rate {{ number_format($account->annual_profit_rate, 2) }}% yearly · Fixed monthly principal {{ number_format($account->monthly_principal, 2) }}</p>
    <div class="table-responsive">
      <table class="table table-bordered table-sm">
        <thead class="table-light">
          <tr>
            <th>#</th><th>Date</th>
            <th class="text-end">Opening princ.</th><th class="text-end">Principal</th>
            <th class="text-end">Pre-rent</th><th class="text-end">Rent</th>
            <th class="text-end">Installment</th><th class="text-end">Closing princ.</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($account->installments as $row)
            <tr>
              <td>{{ $row->installment_no }}</td>
              <td>{{ $row->installment_date?->format('Y-m-d') }}</td>
              <td class="text-end">{{ number_format($row->opening_principal, 2) }}</td>
              <td class="text-end">{{ number_format($row->principal_amount, 2) }}</td>
              <td class="text-end">{{ number_format($row->pre_rent_amount, 2) }}</td>
              <td class="text-end">{{ number_format($row->rent_amount, 2) }}</td>
              <td class="text-end">{{ number_format($row->total_installment, 2) }}</td>
              <td class="text-end">{{ number_format($row->closing_principal, 2) }}</td>
              <td>{{ $row->payment_status }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('page-style')
<style>@media print{.no-print{display:none!important}}</style>
@endsection

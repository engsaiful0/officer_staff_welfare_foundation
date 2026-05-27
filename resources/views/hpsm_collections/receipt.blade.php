<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <title>Receipt {{ $collection->id }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <style>@media print{.no-print{display:none!important}}</style>
</head>
<body class="p-4">
<div class="no-print mb-3">
  <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Print</button>
  <a href="{{ route('hpsm-collections.show', $collection) }}" class="btn btn-outline-secondary btn-sm">Detail</a>
</div>

<div class="border p-4" style="max-width:560px;margin:0 auto;">
  <h4 class="text-center">Collection receipt</h4>
  <p class="text-center small mb-4">HPSM reducing — {{ config('app.name') }}</p>
  <dl class="row small mb-0">
    <dt class="col-5">Receipt #</dt><dd class="col-7">{{ $collection->id }}</dd>
    <dt class="col-5">Date</dt><dd class="col-7">{{ $collection->collection_date?->format('d M Y') }}</dd>
    <dt class="col-5">Account</dt><dd class="col-7">{{ $collection->openingAccount?->account_no }}</dd>
    <dt class="col-5">Member</dt><dd class="col-7">{{ $collection->openingAccount?->member?->name }}</dd>
    <dt class="col-5">From inst.</dt><dd class="col-7">#{{ $collection->installment?->installment_no ?? '—' }}</dd>
    <dt class="col-5 mt-2">Total</dt><dd class="col-7 mt-2 fw-bold">{{ number_format($collection->total_collected, 2) }}</dd>
    <dt class="col-5">Principal</dt><dd class="col-7">{{ number_format($collection->principal_collected, 2) }}</dd>
    <dt class="col-5">Pre-rent</dt><dd class="col-7">{{ number_format($collection->pre_rent_collected, 2) }}</dd>
    <dt class="col-5">Rent</dt><dd class="col-7">{{ number_format($collection->rent_collected, 2) }}</dd>
    <dt class="col-5">Method</dt><dd class="col-7">{{ $collection->payment_method ?: '—' }}</dd>
    <dt class="col-5">Txn</dt><dd class="col-7">{{ $collection->transaction_no ?: '—' }}</dd>
  </dl>
  @if($collection->remarks)
    <p class="small mt-3 mb-0"><strong>Note:</strong> {{ $collection->remarks }}</p>
  @endif
</div>
</body>
</html>

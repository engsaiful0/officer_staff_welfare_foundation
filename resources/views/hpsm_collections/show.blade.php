@extends('layouts/contentNavbarLayout')

@section('title', 'Collection '.$collection->id)

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h5 class="mb-0">Collection #{{ $collection->id }}</h5>
    <a href="{{ route('hpsm-collections.receipt', $collection) }}" class="btn btn-sm btn-primary">Print receipt</a>
  </div>
  <div class="card-body">
    <dl class="row small">
      <dt class="col-sm-4">Account</dt><dd class="col-sm-8">{{ $collection->openingAccount?->account_no }}</dd>
      <dt class="col-sm-4">Member</dt><dd class="col-sm-8">{{ $collection->openingAccount?->member?->name }}</dd>
      <dt class="col-sm-4">Date</dt><dd class="col-sm-8">{{ $collection->collection_date?->format('Y-m-d') }}</dd>
      <dt class="col-sm-4">Starting installment</dt><dd class="col-sm-8">#{{ $collection->installment?->installment_no ?? '—' }}</dd>
      <dt class="col-sm-4">Total</dt><dd class="col-sm-8">{{ number_format($collection->total_collected, 2) }}</dd>
      <dt class="col-sm-4">Principal</dt><dd class="col-sm-8">{{ number_format($collection->principal_collected, 2) }}</dd>
      <dt class="col-sm-4">Pre-rent</dt><dd class="col-sm-8">{{ number_format($collection->pre_rent_collected, 2) }}</dd>
      <dt class="col-sm-4">Rent</dt><dd class="col-sm-8">{{ number_format($collection->rent_collected, 2) }}</dd>
      <dt class="col-sm-4">Payment method</dt><dd class="col-sm-8">{{ $collection->payment_method ?: '—' }}</dd>
      <dt class="col-sm-4">Transaction no.</dt><dd class="col-sm-8">{{ $collection->transaction_no ?: '—' }}</dd>
      <dt class="col-sm-4">Remarks</dt><dd class="col-sm-8">{{ $collection->remarks ?: '—' }}</dd>
    </dl>
    <a href="{{ route('hpsm-opening-accounts.show', $collection->openingAccount) }}" class="btn btn-outline-secondary btn-sm">Open account</a>
  </div>
</div>
@endsection

@extends('layouts/contentNavbarLayout')

@section('title', 'Receive HPSM collection')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between flex-wrap gap-2">
    <h5 class="mb-0">Receive collection</h5>
    <a href="{{ route('hpsm-collections.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
  </div>
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="get" action="{{ route('hpsm-collections.create') }}" class="row g-2 mb-4">
      <div class="col-md-8">
        <label class="form-label">Account</label>
        <select name="hpsm_opening_account_id" class="form-select select2" onchange="this.form.submit()">
          <option value="">Select account</option>
          @foreach($accounts as $a)
            <option value="{{ $a->id }}" @selected($selectedAccount && $selectedAccount->id === $a->id)>
              {{ $a->account_no }} — {{ $a->member?->name }} ({{ $a->member?->unique_id }})
            </option>
          @endforeach
        </select>
      </div>
    </form>

    @if($selectedAccount)
      <div class="alert alert-secondary small mb-3">
        Outstanding principal: <strong>{{ number_format($selectedAccount->current_outstanding_principal, 2) }}</strong>
        · Allocation order: pre-rent → rent → principal (spills to next installments if overpaid on current row).
      </div>
      <form method="post" action="{{ route('hpsm-collections.store') }}">
        @csrf
        <input type="hidden" name="hpsm_opening_account_id" value="{{ $selectedAccount->id }}">

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Installment <span class="text-danger">*</span></label>
            <select name="hpsm_installment_id" class="form-select" required>
              <option value="">Select</option>
              @foreach($payableInstallments as $i)
                <option value="{{ $i->id }}" @selected((string) old('hpsm_installment_id') === (string) $i->id)>
                  #{{ $i->installment_no }} — due {{ number_format((float) $i->totalDue(), 2) }}
                  ({{ $i->payment_status }})
                  · {{ $i->installment_date?->format('Y-m-d') }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Collection date <span class="text-danger">*</span></label>
            <input type="date" name="collection_date" class="form-control" required value="{{ old('collection_date', now()->format('Y-m-d')) }}">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Total collected <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">৳</span>
              <input type="number" step="0.01" min="0.01" name="total_collected" class="form-control" required value="{{ old('total_collected') }}">
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Payment method</label>
            <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method') }}">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Transaction no.</label>
            <input type="text" name="transaction_no" class="form-control" value="{{ old('transaction_no') }}">
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
          </div>
        </div>

        @if($payableInstallments->isEmpty())
          <p class="text-muted">Nothing due on this account (or all installments marked paid).</p>
        @else
          <button type="submit" class="btn btn-primary">Save collection</button>
        @endif
      </form>
    @else
      <p class="text-muted">Choose an active account to show payable installments.</p>
    @endif
  </div>
</div>
@endsection

@extends('layouts/contentNavbarLayout')

@section('title', 'HPSM ledger — '.$account->account_no)

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h5 class="mb-0">Account ledger</h5>
      <small>{{ $account->account_no }} — {{ $account->member?->name }}</small>
    </div>
    <a href="{{ route('hpsm-opening-accounts.show', $account) }}" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Date</th>
            <th>Installment</th>
            <th class="text-end">Total</th>
            <th class="text-end">Principal</th>
            <th class="text-end">Pre-rent</th>
            <th class="text-end">Rent</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($collections as $c)
            <tr>
              <td>{{ $c->collection_date?->format('Y-m-d') }}</td>
              <td>#{{ $c->installment?->installment_no ?? '—' }}</td>
              <td class="text-end">{{ number_format($c->total_collected, 2) }}</td>
              <td class="text-end">{{ number_format($c->principal_collected, 2) }}</td>
              <td class="text-end">{{ number_format($c->pre_rent_collected, 2) }}</td>
              <td class="text-end">{{ number_format($c->rent_collected, 2) }}</td>
              <td><a href="{{ route('hpsm-collections.receipt', $c) }}">Receipt</a></td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No collections.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $collections->links() }}
  </div>
</div>
@endsection

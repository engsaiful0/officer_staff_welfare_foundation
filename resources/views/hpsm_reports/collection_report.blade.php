@extends('layouts/contentNavbarLayout')

@section('title', 'HPSM collection report')

@section('content')
<div class="card">
  <div class="card-header"><h5 class="mb-0">Collection report</h5></div>
  <div class="card-body">
    <form class="row g-2 mb-3" method="get">
      <div class="col-md-4">
        <label class="form-label small">Account</label>
        <select name="hpsm_opening_account_id" class="form-select form-select-sm">
          <option value="">All</option>
          @foreach($accounts as $a)
            <option value="{{ $a->id }}" @selected(request('hpsm_opening_account_id') == $a->id)>{{ $a->account_no }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small">From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
      </div>
      <div class="col-md-2">
        <label class="form-label small">To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
      </div>
      <div class="col-md-4 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="{{ route('hpsm-reports.collection') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Date</th><th>Account</th><th>Member</th>
            <th class="text-end">Total</th><th class="text-end">Princ.</th><th class="text-end">Rent</th><th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($collections as $c)
            <tr>
              <td>{{ $c->collection_date?->format('Y-m-d') }}</td>
              <td>{{ $c->openingAccount?->account_no }}</td>
              <td>{{ $c->openingAccount?->member?->name }}</td>
              <td class="text-end">{{ number_format($c->total_collected, 2) }}</td>
              <td class="text-end">{{ number_format($c->principal_collected, 2) }}</td>
              <td class="text-end">{{ number_format((float) $c->pre_rent_collected + (float) $c->rent_collected, 2) }}</td>
              <td><a href="{{ route('hpsm-collections.receipt', $c) }}">Receipt</a></td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $collections->links() }}
  </div>
</div>
@endsection

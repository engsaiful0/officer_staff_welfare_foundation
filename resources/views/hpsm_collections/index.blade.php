@extends('layouts/contentNavbarLayout')

@section('title', 'HPSM collections')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between flex-wrap gap-2">
    <h5 class="mb-0">Collections</h5>
    <a href="{{ route('hpsm-collections.create') }}" class="btn btn-sm btn-primary">New collection</a>
  </div>
  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('success') }}</div>
    @endif
    <form class="row g-2 mb-3" method="get">
      <div class="col-md-4">
        <label class="form-label small">Account</label>
        <select name="hpsm_opening_account_id" class="form-select form-select-sm">
          <option value="">All</option>
          @foreach($accounts as $a)
            <option value="{{ $a->id }}" @selected(request('hpsm_opening_account_id') == $a->id)>{{ $a->account_no }} — {{ $a->member?->name }}</option>
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
        <button class="btn btn-sm btn-primary" type="submit">Filter</button>
        <a href="{{ route('hpsm-collections.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Date</th><th>Account</th><th>Member</th><th class="text-end">Total</th><th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($collections as $c)
            <tr>
              <td>{{ $c->collection_date?->format('Y-m-d') }}</td>
              <td>{{ $c->openingAccount?->account_no }}</td>
              <td>{{ $c->openingAccount?->member?->name }}</td>
              <td class="text-end">{{ number_format($c->total_collected, 2) }}</td>
              <td>
                <a href="{{ route('hpsm-collections.show', $c) }}" class="me-2">View</a>
                <a href="{{ route('hpsm-collections.receipt', $c) }}">Receipt</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{ $collections->links() }}
  </div>
</div>
@endsection

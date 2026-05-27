@extends('layouts/contentNavbarLayout')

@section('title', 'HPSM due report')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between flex-wrap gap-2">
    <h5 class="mb-0">Due report — installments pending / partial</h5>
    <a href="{{ route('hpsm-opening-accounts.index') }}" class="btn btn-sm btn-outline-secondary">Accounts</a>
  </div>
  <div class="card-body">
    <form class="row g-2 mb-3" method="get">
      <div class="col-md-4">
        <label class="form-label small">Member</label>
        <select name="member_id" class="form-select form-select-sm select2">
          <option value="">All</option>
          @foreach($members as $m)
            <option value="{{ $m->id }}" @selected(request('member_id') == $m->id)>{{ $m->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small">Account no. contains</label>
        <input type="text" name="account_no" value="{{ request('account_no') }}" class="form-control form-control-sm" placeholder="HPSM-">
      </div>
      <div class="col-md-4 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="{{ route('hpsm-reports.due') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Account</th><th>Member</th><th>#</th><th>Date</th>
            <th class="text-end">Due</th><th>Status</th><th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $r)
            <tr>
              <td>{{ $r->openingAccount?->account_no }}</td>
              <td>{{ $r->openingAccount?->member?->name }}</td>
              <td>{{ $r->installment_no }}</td>
              <td>{{ $r->installment_date?->format('Y-m-d') }}</td>
              <td class="text-end">{{ number_format($r->due_amount, 2) }}</td>
              <td>{{ $r->payment_status }}</td>
              <td>
                <a href="{{ route('hpsm-opening-accounts.show', $r->openingAccount) }}">Account</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No rows.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $rows->links() }}
  </div>
</div>
@endsection

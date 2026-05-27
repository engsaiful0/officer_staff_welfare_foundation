@extends('layouts/contentNavbarLayout')

@section('title', 'HPSM Opening Accounts')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between flex-wrap gap-2 align-items-center">
    <div>
      <h5 class="mb-0">HPSM Opening Accounts</h5>
      <small class="text-muted">Anuity → reducing conversion balances</small>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('hpsm-reports.due') }}" class="btn btn-outline-secondary btn-sm">Due report</a>
      <a href="{{ route('hpsm-reports.collection') }}" class="btn btn-outline-secondary btn-sm">Collection report</a>
      <a href="{{ route('hpsm-reports.member-statement') }}" class="btn btn-outline-secondary btn-sm">Member statement</a>
      <a href="{{ route('hpsm-opening-accounts.create') }}" class="btn btn-primary btn-sm">Open account</a>
    </div>
  </div>
  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('error') }}
      </div>
    @endif

    <form class="row g-2 mb-4" method="get" action="{{ route('hpsm-opening-accounts.index') }}">
      <div class="col-md-4">
        <label class="form-label small">Member</label>
        <select name="member_id" class="form-select form-select-sm select2">
          <option value="">All</option>
          @foreach($members as $m)
            <option value="{{ $m->id }}" @selected(request('member_id') == $m->id)>{{ $m->name }} ({{ $m->unique_id }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="active" @selected(request('status')==='active')>Active</option>
          <option value="completed" @selected(request('status')==='completed')>Completed</option>
          <option value="closed" @selected(request('status')==='closed')>Closed</option>
        </select>
      </div>
      <div class="col-md-5 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        <a href="{{ route('hpsm-opening-accounts.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Account</th>
            <th>Member</th>
            <th class="text-end">Principal</th>
            <th class="text-end">Outstanding</th>
            <th class="text-end">Opening total</th>
            <th>Opened</th>
            <th>Status</th>
            <th class="no-print"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($accounts as $a)
            <tr>
              <td><a href="{{ route('hpsm-opening-accounts.show', $a) }}">{{ $a->account_no }}</a></td>
              <td>{{ $a->member?->name }} <small class="text-muted">{{ $a->member?->unique_id }}</small></td>
              <td class="text-end">{{ number_format($a->balance_principal, 2) }}</td>
              <td class="text-end">{{ number_format($a->current_outstanding_principal, 2) }}</td>
              <td class="text-end">{{ number_format($a->total_opening_balance, 2) }}</td>
              <td>{{ $a->opening_date?->format('Y-m-d') }}</td>
              <td><span class="badge bg-label-{{ $a->status === 'active' ? 'success' : 'secondary' }}">{{ $a->status }}</span></td>
              <td class="text-nowrap">
                <a class="btn btn-icon btn-sm" href="{{ route('hpsm-opening-accounts.show', $a) }}" title="View"><i class="bx bx-show"></i></a>
                <a class="btn btn-icon btn-sm" href="{{ route('hpsm-opening-accounts.schedule', $a) }}" title="Schedule"><i class="bx bx-calendar"></i></a>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No accounts found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $accounts->links() }}
  </div>
</div>
@endsection

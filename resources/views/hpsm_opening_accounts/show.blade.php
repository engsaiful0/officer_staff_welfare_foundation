@extends('layouts/contentNavbarLayout')

@section('title', 'HPSM — '.$account->account_no)

@section('content')
<div class="row g-3">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h5 class="mb-0">{{ $account->account_no }}</h5>
          <small class="text-muted">{{ $account->member?->name }} — {{ $account->member?->unique_id }}</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('hpsm-opening-accounts.index') }}" class="btn btn-outline-secondary btn-sm">List</a>
          <a href="{{ route('hpsm-opening-accounts.schedule', $account) }}" class="btn btn-outline-primary btn-sm">Schedule</a>
          <a href="{{ route('hpsm-opening-accounts.ledger', $account) }}" class="btn btn-outline-primary btn-sm">Ledger</a>
          <a href="{{ route('hpsm-opening-accounts.print-schedule', $account) }}" class="btn btn-outline-dark btn-sm" target="_blank">Print schedule</a>
          <a href="{{ route('hpsm-collections.create', ['hpsm_opening_account_id' => $account->id]) }}" class="btn btn-primary btn-sm">Receive collection</a>
          <a href="{{ route('hpsm-opening-accounts.edit', $account) }}" class="btn btn-secondary btn-sm">Edit</a>
          <form action="{{ route('hpsm-opening-accounts.destroy', $account) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this account?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
          </form>
        </div>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('error') }}</div>
        @endif

        <div class="row small">
          <div class="col-md-3 mb-2"><span class="text-muted">Status</span><br><span class="badge bg-label-{{ $account->status === 'active' ? 'success' : 'secondary' }}">{{ $account->status }}</span></div>
          <div class="col-md-3 mb-2"><span class="text-muted">Opening date</span><br>{{ $account->opening_date?->format('Y-m-d') }}</div>
          <div class="col-md-3 mb-2"><span class="text-muted">Duration (months)</span><br>{{ $account->remaining_duration_months }}</div>
          <div class="col-md-3 mb-2"><span class="text-muted">Annual rate %</span><br>{{ number_format($account->annual_profit_rate, 2) }}</div>
          <div class="col-md-3 mb-2"><span class="text-muted">Balance principal</span><br>{{ number_format($account->balance_principal, 2) }}</div>
          <div class="col-md-3 mb-2"><span class="text-muted">Outstanding principal</span><br><strong>{{ number_format($account->current_outstanding_principal, 2) }}</strong></div>
          <div class="col-md-3 mb-2"><span class="text-muted">Monthly principal</span><br>{{ number_format($account->monthly_principal, 2) }}</div>
          <div class="col-md-3 mb-2"><span class="text-muted">Total opening balance</span><br>{{ number_format($account->total_opening_balance, 2) }}</div>
          <div class="col-12 mb-2"><span class="text-muted">Remarks</span><br>{{ $account->remarks ?: '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header"><h6 class="mb-0">Installments (summary)</h6></div>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead><tr>
            <th>#</th><th>Date</th><th class="text-end">Total</th><th class="text-end">Due</th><th>Status</th>
          </tr></thead>
          <tbody>
            @foreach($account->installments as $i)
              <tr>
                <td>{{ $i->installment_no }}</td>
                <td>{{ $i->installment_date?->format('Y-m-d') }}</td>
                <td class="text-end">{{ number_format($i->total_installment, 2) }}</td>
                <td class="text-end">{{ number_format($i->due_amount, 2) }}</td>
                <td><span class="badge bg-label-{{ $i->payment_status === 'paid' ? 'success' : ($i->payment_status === 'partial' ? 'warning' : 'secondary') }}">{{ $i->payment_status }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if($account->collections->isNotEmpty())
  <div class="col-12">
    <div class="card">
      <div class="card-header"><h6 class="mb-0">Recent collections</h6></div>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead><tr>
            <th>Date</th><th class="text-end">Total</th><th class="text-end">Principal</th><th class="text-end">Pre-rent</th><th class="text-end">Rent</th><th></th>
          </tr></thead>
          <tbody>
            @foreach($account->collections as $c)
              <tr>
                <td>{{ $c->collection_date?->format('Y-m-d') }}</td>
                <td class="text-end">{{ number_format($c->total_collected, 2) }}</td>
                <td class="text-end">{{ number_format($c->principal_collected, 2) }}</td>
                <td class="text-end">{{ number_format($c->pre_rent_collected, 2) }}</td>
                <td class="text-end">{{ number_format($c->rent_collected, 2) }}</td>
                <td><a href="{{ route('hpsm-collections.receipt', $c) }}">Receipt</a></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection

@extends('layouts/contentNavbarLayout')

@section('title', 'HPSM member statement')

@section('content')
<div class="card mb-4">
  <div class="card-header"><h5 class="mb-0">Member-wise statement</h5></div>
  <div class="card-body">
    <form class="row g-2 align-items-end" method="get">
      <div class="col-md-8">
        <label class="form-label">Member</label>
        <select name="member_id" class="form-select select2">
          <option value="">Choose member…</option>
          @foreach($members as $m)
            <option value="{{ $m->id }}" @selected(request('member_id') == $m->id)>{{ $m->name }} ({{ $m->unique_id }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary w-100">Load</button>
      </div>
    </form>
  </div>
</div>

@if($member)
  <div class="card mb-4">
    <div class="card-body">
      <h6>{{ $member->name }}</h6>
      <small class="text-muted">{{ $member->unique_id }}</small>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header"><strong>Opening accounts</strong></div>
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th>Account</th><th>Open</th><th class="text-end">Principal</th><th class="text-end">Outstanding</th><th>Status</th><th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($accounts as $a)
            <tr>
              <td>{{ $a->account_no }}</td>
              <td>{{ $a->opening_date?->format('Y-m-d') }}</td>
              <td class="text-end">{{ number_format($a->balance_principal, 2) }}</td>
              <td class="text-end">{{ number_format($a->current_outstanding_principal, 2) }}</td>
              <td>{{ $a->status }}</td>
              <td><a href="{{ route('hpsm-opening-accounts.show', $a) }}">View</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header"><strong>Installment snapshot</strong></div>
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead><tr><th>Account</th><th>#</th><th>Date</th><th class="text-end">Due</th><th>Status</th></tr></thead>
        <tbody>
          @foreach($accounts as $a)
            @foreach($a->installments as $i)
              @if($i->payment_status !== 'paid')
                <tr>
                  <td>{{ $a->account_no }}</td>
                  <td>{{ $i->installment_no }}</td>
                  <td>{{ $i->installment_date?->format('Y-m-d') }}</td>
                  <td class="text-end">{{ number_format($i->due_amount, 2) }}</td>
                  <td>{{ $i->payment_status }}</td>
                </tr>
              @endif
            @endforeach
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><strong>Collection history</strong></div>
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead><tr><th>Date</th><th>Account</th><th class="text-end">Total</th><th></th></tr></thead>
        <tbody>
          @forelse($timeline as $c)
            <tr>
              <td>{{ $c->collection_date?->format('Y-m-d') }}</td>
              <td>{{ $c->openingAccount?->account_no }}</td>
              <td class="text-end">{{ number_format($c->total_collected, 2) }}</td>
              <td><a href="{{ route('hpsm-collections.receipt', $c) }}">Receipt</a></td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-muted text-center">No collections.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endif
@endsection

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Schedule {{ $account->account_no }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <style>
    body { padding: 1rem; font-size: 12px; }
    @media print { .no-print { display: none !important; } button { display: none !important; } }
  </style>
</head>
<body>
  <div class="no-print mb-3">
    <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Print</button>
    <a href="{{ route('hpsm-opening-accounts.schedule', $account) }}" class="btn btn-outline-secondary btn-sm">Screen view</a>
  </div>
  <h4>HPSM reducing schedule — {{ $account->account_no }}</h4>
  <p>{{ $account->member?->name }} ({{ $account->member?->unique_id }}) · Opened {{ $account->opening_date?->format('Y-m-d') }}
    · {{ $account->remaining_duration_months }} months · {{ number_format($account->annual_profit_rate, 2) }}% p.a.</p>
  <table class="table table-bordered table-sm">
    <thead>
      <tr>
        <th>#</th><th>Date</th>
        <th class="text-end">Opening</th><th class="text-end">Principal</th><th class="text-end">Pre-rent</th><th class="text-end">Rent</th><th class="text-end">Total</th><th class="text-end">Closing</th>
      </tr>
    </thead>
    <tbody>
      @foreach($account->installments as $row)
        <tr>
          <td>{{ $row->installment_no }}</td>
          <td>{{ $row->installment_date?->format('Y-m-d') }}</td>
          <td class="text-end">{{ number_format($row->opening_principal, 2) }}</td>
          <td class="text-end">{{ number_format($row->principal_amount, 2) }}</td>
          <td class="text-end">{{ number_format($row->pre_rent_amount, 2) }}</td>
          <td class="text-end">{{ number_format($row->rent_amount, 2) }}</td>
          <td class="text-end">{{ number_format($row->total_installment, 2) }}</td>
          <td class="text-end">{{ number_format($row->closing_principal, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

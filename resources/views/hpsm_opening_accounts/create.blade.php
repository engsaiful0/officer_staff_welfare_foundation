@extends('layouts/contentNavbarLayout')

@section('title', 'HPSM — Open account')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h5 class="mb-0">Open HPSM reducing account</h5>
    <a href="{{ route('hpsm-opening-accounts.index') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
  </div>
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form method="post" action="{{ route('hpsm-opening-accounts.store') }}">
      @csrf
      @include('hpsm_opening_accounts._form', ['account' => null, 'members' => $members, 'mayReschedule' => true])
      <button type="submit" class="btn btn-primary">Save &amp; generate schedule</button>
    </form>
  </div>
</div>
@endsection

@section('page-script')
<script>
(function(){
  function n(v){ var x = parseFloat(v); return isFinite(x) ? x : 0; }
  function fmt(x){ return (Math.round(x * 100) / 100).toFixed(2); }
  function recalc(){
    var bp = n(document.getElementById('balance_principal').value);
    var mos = parseInt(document.getElementById('remaining_duration_months').value, 10) || 1;
    var rate = n(document.getElementById('annual_profit_rate').value);
    var pre = n(document.getElementById('balance_pre_rent').value);
    var cur = n(document.getElementById('current_rent').value);
    var mp = mos ? bp / mos : 0;
    var estRent = bp * rate / 12 / 100;
    var estFirst = mp + estRent;
    var totalOpen = bp + pre + cur;
    document.getElementById('lbl_monthly_principal').textContent = fmt(mp);
    document.getElementById('lbl_est_rent').textContent = fmt(estRent);
    document.getElementById('lbl_est_first').textContent = fmt(estFirst);
    document.getElementById('lbl_total_opening').textContent = fmt(totalOpen);
  }
  ['balance_principal','balance_pre_rent','current_rent','annual_profit_rate','remaining_duration_months'].forEach(function(id){
    var el = document.getElementById(id); if(el) el.addEventListener('input', recalc);
  });
  recalc();
})();
</script>
@endsection

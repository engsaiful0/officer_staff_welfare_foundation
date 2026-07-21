@php($a = $account ?? null)
@php($mayReschedule = $mayReschedule ?? true)

<div class="row">
  @if(!$mayReschedule)
    <div class="col-12 mb-3">
      <div class="alert alert-info mb-0">
        Collections exist for this account. Schedule-driving fields cannot be edited.
      </div>
    </div>
  @endif

  <div class="col-md-3 mb-3">
    <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
    <select class="select2 form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required @disabled(!$mayReschedule)>
      <option value="">Select member</option>
      @foreach($members as $m)
        <option value="{{ $m->id }}" @selected((string) old('member_id', $a?->member_id) === (string) $m->id)>
          {{ $m->name }} ({{ $m->unique_id }})
        </option>
      @endforeach
    </select>
    @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3 mb-3">
    <label for="opening_date" class="form-label">Opening / conversion date <span class="text-danger">*</span></label>
    <input type="date" class="form-control @error('opening_date') is-invalid @enderror" id="opening_date" name="opening_date"
      value="{{ old('opening_date', optional($a?->opening_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required @disabled(!$mayReschedule) />
    @error('opening_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3 mb-3">
    <label for="remaining_duration_months" class="form-label">Remaining duration (months) <span class="text-danger">*</span></label>
    <input type="number" min="1" step="1" class="form-control @error('remaining_duration_months') is-invalid @enderror"
      id="remaining_duration_months" name="remaining_duration_months"
      value="{{ old('remaining_duration_months', $a?->remaining_duration_months ?? '') }}" required @readonly(!$mayReschedule) />
    @error('remaining_duration_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3 mb-3">
    <label for="balance_principal" class="form-label">Balance principal <span class="text-danger">*</span></label>
    <div class="input-group">
      <span class="input-group-text">৳</span>
      <input type="number" step="0.01" min="0" class="form-control @error('balance_principal') is-invalid @enderror" id="balance_principal"
        name="balance_principal" value="{{ old('balance_principal', $a?->balance_principal ?? '') }}" required @readonly(!$mayReschedule) />
    </div>
    @error('balance_principal')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3 mb-3">
    <label for="balance_pre_rent" class="form-label">Balance pre rent / outstanding rent <span class="text-danger">*</span></label>
    <div class="input-group">
      <span class="input-group-text">৳</span>
      <input type="number" step="0.01" min="0" class="form-control @error('balance_pre_rent') is-invalid @enderror" id="balance_pre_rent"
        name="balance_pre_rent" value="{{ old('balance_pre_rent', $a?->balance_pre_rent ?? '') }}" required @readonly(!$mayReschedule) />
    </div>
    @error('balance_pre_rent')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3 mb-3">
    <label for="current_rent" class="form-label">Current rent <span class="text-danger">*</span></label>
    <div class="input-group">
      <span class="input-group-text">৳</span>
      <input type="number" step="0.01" min="0" class="form-control @error('current_rent') is-invalid @enderror" id="current_rent"
        name="current_rent" value="{{ old('current_rent', $a?->current_rent ?? '') }}" required @readonly(!$mayReschedule) />
    </div>
    @error('current_rent')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3 mb-3">
    <label for="annual_profit_rate" class="form-label">Annual profit rate (%) <span class="text-danger">*</span></label>
    <input type="number" step="0.01" min="0" class="form-control @error('annual_profit_rate') is-invalid @enderror" id="annual_profit_rate"
      name="annual_profit_rate" value="{{ old('annual_profit_rate', $a?->annual_profit_rate ?? '') }}" required @readonly(!$mayReschedule) />
    @error('annual_profit_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-8 mb-3">
    <label class="form-label small text-muted">Live estimates (opening balance / fixed monthly principal)</label>
    <div class="rounded border px-3 py-2 bg-label-secondary bg-opacity-10">
      <div class="row small">
        <div class="col-6 col-md-3">Monthly principal: <strong id="lbl_monthly_principal">—</strong></div>
        <div class="col-6 col-md-3">Est. monthly rent (on principal): <strong id="lbl_est_rent">—</strong></div>
        <div class="col-6 col-md-3">Est. 1st installment (excl. pre-rent): <strong id="lbl_est_first">—</strong></div>
        <div class="col-6 col-md-3">Total opening balance: <strong id="lbl_total_opening">—</strong></div>
      </div>
    </div>
  </div>

  <div class="col-12 mb-3">
    <label for="remarks" class="form-label">Remarks</label>
    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="2">{{ old('remarks', $a?->remarks ?? '') }}</textarea>
    @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

@extends('layouts/contentNavbarLayout')

@section('title', 'Monthly Fee Settings')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ asset('assets/js/fee-settings.js') }}"></script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Fee Management /</span> Monthly Fee Settings
</h4>

<div class="row">
  <!-- Fee Settings Form -->
  <div class="col-xl-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Configure Monthly Fee Settings</h5>
        <small class="text-muted">Set payment deadline and fine amounts</small>
      </div>
      <div class="card-body">
        <form id="feeSettingsForm">
          @csrf
          
          <!-- Basic Fee Settings -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="monthlyFeeAmount">Monthly Fee Amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control" id="monthlyFeeAmount" name="monthly_fee_amount" 
                       value="{{ $feeSettings->monthly_fee_amount ?? 0 }}" 
                       placeholder="0.00" step="0.01" min="0" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="paymentDeadlineDay">Payment Deadline (Day of Month) <span class="text-danger">*</span></label>
              <select class="form-select" id="paymentDeadlineDay" name="payment_deadline_day" required>
                @for($day = 1; $day <= 31; $day++)
                  <option value="{{ $day }}" 
                    {{ ($feeSettings->payment_deadline_day ?? 10) == $day ? 'selected' : '' }}>
                    {{ $day }}{{ $day == 1 ? 'st' : ($day == 2 ? 'nd' : ($day == 3 ? 'rd' : 'th')) }}
                  </option>
                @endfor
              </select>
              <div class="form-text">Students must pay by this day each month</div>
            </div>
          </div>

          <!-- Fine Settings -->
          <div class="row mb-3">
            <div class="col-12">
              <label class="form-label">Fine Type <span class="text-danger">*</span></label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="fine_type" id="fineTypeFixed" value="fixed" 
                       {{ (!($feeSettings->is_percentage_fine ?? false)) ? 'checked' : '' }}>
                <label class="form-check-label" for="fineTypeFixed">
                  Fixed Amount per Day
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="fine_type" id="fineTypePercentage" value="percentage"
                       {{ ($feeSettings->is_percentage_fine ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="fineTypePercentage">
                  Percentage of Fee Amount
                </label>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6" id="fixedFineSection">
              <label class="form-label" for="fineAmountPerDay">Fine Amount per Day</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control" id="fineAmountPerDay" name="fine_amount_per_day" 
                       value="{{ $feeSettings->fine_amount_per_day ?? 0 }}" 
                       placeholder="0.00" step="0.01" min="0">
              </div>
            </div>
            <div class="col-md-6" id="percentageFineSection" style="display: none;">
              <label class="form-label" for="finePercentage">Fine Percentage per Day</label>
              <div class="input-group">
                <input type="number" class="form-control" id="finePercentage" name="fine_percentage" 
                       value="{{ $feeSettings->fine_percentage ?? 0 }}" 
                       placeholder="0.00" step="0.01" min="0" max="100">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="maximumFineAmount">Maximum Fine Amount (Optional)</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control" id="maximumFineAmount" name="maximum_fine_amount" 
                       value="{{ $feeSettings->maximum_fine_amount ?? '' }}" 
                       placeholder="No limit" step="0.01" min="0">
              </div>
              <div class="form-text">Leave empty for no limit</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="gracePeriodDays">Grace Period (Days)</label>
              <input type="number" class="form-control" id="gracePeriodDays" name="grace_period_days" 
                     value="{{ $feeSettings->grace_period_days ?? 0 }}" 
                     placeholder="0" min="0" max="365">
              <div class="form-text">No fine for this many days after deadline</div>
            </div>
          </div>

          <!-- Notes -->
          <div class="mb-3">
            <label class="form-label" for="notes">Notes (Optional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" 
                      placeholder="Any additional notes about fee settings">{{ $feeSettings->notes ?? '' }}</textarea>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i class="ti ti-device-floppy me-2"></i>Save Settings
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Current Settings Summary -->
  <div class="col-xl-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Current Settings Summary</h5>
      </div>
      <div class="card-body">
        <div id="settingsSummary">
          @if($feeSettings->id ?? false)
            <div class="d-flex justify-content-between mb-2">
              <span>Monthly Fee:</span>
              <strong>৳{{ number_format($feeSettings->monthly_fee_amount, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Payment Deadline:</span>
              <strong>{{ $feeSettings->payment_deadline_day }}{{ $feeSettings->payment_deadline_day == 1 ? 'st' : ($feeSettings->payment_deadline_day == 2 ? 'nd' : ($feeSettings->payment_deadline_day == 3 ? 'rd' : 'th')) }} of each month</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Fine Type:</span>
              <strong>{{ $feeSettings->is_percentage_fine ? 'Percentage' : 'Fixed Amount' }}</strong>
            </div>
            @if($feeSettings->is_percentage_fine)
              <div class="d-flex justify-content-between mb-2">
                <span>Fine Rate:</span>
                <strong>{{ $feeSettings->fine_percentage }}% per day</strong>
              </div>
            @else
              <div class="d-flex justify-content-between mb-2">
                <span>Fine Amount:</span>
                <strong>৳{{ number_format($feeSettings->fine_amount_per_day, 2) }} per day</strong>
              </div>
            @endif
            @if($feeSettings->maximum_fine_amount)
              <div class="d-flex justify-content-between mb-2">
                <span>Maximum Fine:</span>
                <strong>৳{{ number_format($feeSettings->maximum_fine_amount, 2) }}</strong>
              </div>
            @endif
            <div class="d-flex justify-content-between mb-2">
              <span>Grace Period:</span>
              <strong>{{ $feeSettings->grace_period_days }} days</strong>
            </div>
          @else
            <div class="text-center text-muted">
              <i class="ti ti-settings ti-sm mb-2"></i>
              <p>No settings configured yet. Please configure the monthly fee settings.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mt-4">
      <div class="card-header">
        <h5 class="mb-0">Quick Actions</h5>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
          <button type="button" class="btn btn-info" id="generateCurrentMonthBtn">
            <i class="ti ti-plus me-2"></i>Generate Current Month Payments
          </button>
          <button type="button" class="btn btn-warning" id="updateOverdueBtn">
            <i class="ti ti-refresh me-2"></i>Update Overdue Status
          </button>
          <button type="button" class="btn btn-primary" id="updateFeeAmountsBtn">
            <i class="ti ti-currency-taka me-2"></i>Update Fee Amounts
          </button>
          <a href="{{ route('fee-management.monthly-report') }}" class="btn btn-success">
            <i class="ti ti-report me-2"></i>View Monthly Report
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Generate Payments Modal -->
<div class="modal fade" id="generatePaymentsModal" tabindex="-1" aria-labelledby="generatePaymentsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="generatePaymentsModalLabel">Generate Monthly Payments</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="generatePaymentsForm">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="generateMonth">Month</label>
              <select class="form-select" id="generateMonth" name="month" required>
                @for($month = 1; $month <= 12; $month++)
                  <option value="{{ $month }}" {{ date('n') == $month ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                  </option>
                @endfor
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="generateYear">Year</label>
              <input type="number" class="form-control" id="generateYear" name="year" 
                     value="{{ date('Y') }}" min="2020" max="2030" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="generateAcademicYear">Academic Year (Optional)</label>
            <select class="form-select" id="generateAcademicYear" name="academic_year_id">
              <option value="">Current Academic Year</option>
              <!-- Will be populated by JavaScript -->
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmGenerateBtn">Generate Payments</button>
      </div>
    </div>
  </div>
</div>

@endsection

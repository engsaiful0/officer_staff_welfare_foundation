@extends('layouts/contentNavbarLayout')

@section('title', 'Deposit Details')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success!</strong> {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
  <!-- Deposit Information -->
  <div class="col-md-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Deposit Details</h5>
        <div class="d-flex gap-2">
          <a href="{{ route('deposits.edit', $deposit) }}" class="btn btn-outline-primary btn-sm">
            <i class="bx bx-edit me-1"></i> Edit
          </a>
          <a href="{{ route('deposits.ledger.index', $deposit) }}" class="btn btn-outline-info btn-sm">
            <i class="bx bx-list-ul me-1"></i> View Ledger
          </a>
          <a href="{{ route('deposits.view-deposits') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back
          </a>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-bordered table-sm table-hover">
              <tr>
                <td width="40%"><strong>Account Number:</strong></td>
                <td>{{ $deposit->deposit_account_number ?: ($deposit->account_number ?: 'N/A') }}</td>
              </tr>
              <tr>
                <td><strong>Deposit ID:</strong></td>
                <td>#{{ $deposit->id }}</td>
              </tr>
              <tr>
                <td><strong>Member:</strong></td>
                <td>{{ $deposit->member->name }} <small class="text-muted">({{ $deposit->member->unique_id ?? $deposit->member->member_unique_id ?? 'N/A' }})</small></td>
              </tr>
              <tr>
                <td><strong>Deposit Type:</strong></td>
                <td>
                  <span class="badge bg-label-{{ $deposit->deposit_type === 'savings' ? 'info' : ($deposit->deposit_type === 'fixed' ? 'warning' : 'success') }}">
                    {{ ucfirst($deposit->deposit_type) }}
                  </span>
                </td>
              </tr>
              <tr>
                <td><strong>Monthly Deposit Amount:</strong></td>
                <td>৳{{ number_format($deposit->monthly_deposit_amount ?? 0, 2) }}</td>
              </tr>
              <tr>
                <td><strong>Current Balance:</strong></td>
                <td><span class="fw-semibold text-success">৳{{ number_format($deposit->current_balance, 2) }}</span></td>
              </tr>
              <tr>
                <td><strong>Interest Rate:</strong></td>
                <td>{{ $deposit->rate_percentage }}% per annum</td>
              </tr>
              <tr>
                <td><strong>Status:</strong></td>
                <td>
                  <span class="badge bg-label-{{ $deposit->status === 'active' ? 'success' : ($deposit->status === 'matured' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($deposit->status) }}
                  </span>
                </td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-bordered table-sm table-hover">
              <tr>
                <td width="40%"><strong>Start Date:</strong></td>
                <td>{{ $deposit->start_date->format('M d, Y') }}</td>
              </tr>
              <tr>
                <td><strong>Maturity Date:</strong></td>
                <td>
                  {{ $deposit->maturity_date ? $deposit->maturity_date->format('M d, Y') : 'N/A' }}
                  @if($deposit->maturity_date && $deposit->maturity_date < now())
                    <span class="badge bg-label-danger ms-2">Overdue</span>
                  @elseif($deposit->maturity_date && $deposit->maturity_date <= now()->addDays(30))
                    <span class="badge bg-label-warning ms-2">Due Soon</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td><strong>Deposit Day:</strong></td>
                <td>{{ $deposit->deposit_day_of_month ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td><strong>Created At:</strong></td>
                <td>{{ $deposit->created_at->format('M d, Y h:i A') }}</td>
              </tr>
              <tr>
                <td><strong>Last Updated:</strong></td>
                <td>{{ $deposit->updated_at->format('M d, Y h:i A') }}</td>
              </tr>
              @if($deposit->notes)
              <tr>
                <td><strong>Notes:</strong></td>
                <td>{{ $deposit->notes }}</td>
              </tr>
              @endif
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Sidebar: Summary & Quick Actions -->
  <div class="col-md-4">
    <!-- Summary Card -->
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="mb-0">Summary</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>Total Interest Accrued</span>
          <span class="fw-semibold text-success">৳{{ number_format($deposit->total_interest_accrued, 2) }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>Total Withdrawals</span>
          <span class="fw-semibold text-danger">৳{{ number_format($deposit->total_withdrawals, 2) }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>Total Deposits</span>
          <span class="fw-semibold text-info">৳{{ number_format($deposit->total_deposits, 2) }}</span>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center">
          <span>Net Gain/Loss</span>
          <span class="fw-semibold {{ ($deposit->total_interest_accrued - $deposit->total_withdrawals) >= 0 ? 'text-success' : 'text-danger' }}">
            ৳{{ number_format($deposit->total_interest_accrued - $deposit->total_withdrawals, 2) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Quick Actions</h5>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#makeDepositModal">
            <i class="bx bx-money me-1"></i> Make Deposit
          </button>

          <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#withdrawModal">
            <i class="bx bx-minus me-1"></i> Withdraw
          </button>

          @if($deposit->hasInterestRate())
          <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#accrueModal">
            <i class="bx bx-calculator me-1"></i> Accrue Interest
          </button>
          @endif

          <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
            <i class="bx bx-edit-alt me-1"></i> Adjustment
          </button>

          @if($deposit->status === 'active')
          <button type="button" class="btn btn-danger" onclick="closeDeposit({{ $deposit->id }})">
            <i class="bx bx-x-circle me-1"></i> Close Deposit
          </button>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Ledger Entries -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Transactions</h5>
        <a href="{{ route('deposits.ledger.index', $deposit) }}" class="btn btn-outline-primary btn-sm">
          <i class="bx bx-list-ul me-1"></i> View All
        </a>
      </div>

      <div class="card-body">
        @if($ledgerEntries->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Balance After</th>
                <th>Description</th>
                <th>Created By</th>
              </tr>
            </thead>
            <tbody>
              @foreach($ledgerEntries as $entry)
              <tr>
                <td>{{ $entry->entry_date->format('M d, Y') }}</td>
                <td>
                  <span class="badge bg-label-{{ $entry->type === 'deposit' ? 'success' : ($entry->type === 'withdrawal' ? 'danger' : 'info') }}">
                    {{ ucfirst($entry->type) }}
                  </span>
                </td>
                <td class="{{ $entry->type === 'withdrawal' ? 'text-danger' : 'text-success' }}">
                  {{ $entry->type === 'withdrawal' ? '-' : '+' }}৳{{ number_format($entry->amount, 2) }}
                </td>
                <td>৳{{ number_format($entry->balance_after, 2) }}</td>
                <td>{{ $entry->description }}</td>
                <td>{{ $entry->createdBy->name ?? 'System' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
          {{ $ledgerEntries->links() }}
        </div>
        @else
        <div class="text-center py-4">
          <i class="bx bx-info-circle fs-1 text-muted mb-2"></i>
          <p class="text-muted">No transactions found for this deposit.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modals Section -->
<!-- Make Deposit Modal -->
<div class="modal fade" id="makeDepositModal" tabindex="-1" aria-labelledby="makeDepositModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="makeDepositModalLabel">
          <i class="bx bx-money me-2"></i>Make Deposit
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="depositForm" action="{{ route('deposits.ledger.deposit', $deposit) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="alert alert-info">
            <strong>Account Number:</strong> {{ $deposit->deposit_account_number ?: ($deposit->account_number ?: 'N/A') }}<br>
            <strong>Member:</strong> {{ $deposit->member->name }}<br>
            <strong>Current Balance:</strong> ৳{{ number_format($deposit->current_balance, 2) }}
          </div>

          <div class="mb-3">
            <label for="deposit_amount" class="form-label">Deposit Amount <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">৳</span>
              <input type="number"
                class="form-control @error('amount') is-invalid @enderror"
                id="deposit_amount"
                name="amount"
                value="{{ old('amount', $deposit->monthly_deposit_amount) }}"
                step="0.01"
                min="0.01"
                required>
            </div>
            @error('amount')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            @if($deposit->monthly_deposit_amount)
            <div class="form-text">Monthly deposit amount: ৳{{ number_format($deposit->monthly_deposit_amount, 2) }}</div>
            @endif
          </div>

          <div class="mb-3">
            <label for="entry_date" class="form-label">Deposit Date <span class="text-danger">*</span></label>
            <input type="date"
              class="form-control @error('entry_date') is-invalid @enderror"
              id="entry_date"
              name="entry_date"
              value="{{ old('entry_date', date('Y-m-d')) }}"
              required>
            @error('entry_date')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
              id="description"
              name="description"
              rows="2"
              placeholder="Monthly deposit for {{ date('F Y') }}">{{ old('description', 'Monthly deposit - ' . date('F Y')) }}</textarea>
            @error('description')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" id="submitDepositBtn">
            <i class="bx bx-check me-1"></i> Process Deposit
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Withdraw Modal (Placeholder - to be implemented) -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="withdrawModalLabel">
          <i class="bx bx-minus me-2"></i>Withdraw
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">Withdrawal functionality will be implemented here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Accrue Interest Modal (Placeholder - to be implemented) -->
<div class="modal fade" id="accrueModal" tabindex="-1" aria-labelledby="accrueModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accrueModalLabel">
          <i class="bx bx-calculator me-2"></i>Accrue Interest
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">Interest accrual functionality will be implemented here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Adjustment Modal (Placeholder - to be implemented) -->
<div class="modal fade" id="adjustmentModal" tabindex="-1" aria-labelledby="adjustmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adjustmentModalLabel">
          <i class="bx bx-edit-alt me-2"></i>Adjustment
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">Adjustment functionality will be implemented here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Show toast message from session flash if available
  @if(session('success'))
    if (typeof toastr !== 'undefined') {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        timeOut: 3000,
        extendedTimeOut: 1000,
        positionClass: 'toast-top-right',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
      };
      toastr.success('{{ session('success') }}');
    }
  @endif
  
  @if(session('error'))
    if (typeof toastr !== 'undefined') {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        timeOut: 5000,
        extendedTimeOut: 1000,
        positionClass: 'toast-top-right'
      };
      toastr.error('{{ session('error') }}');
    }
  @endif
  
  const depositForm = document.getElementById('depositForm');
  const monthlyAmount = {{ $deposit->monthly_deposit_amount ?? 0 }};

  // Auto-fill monthly amount if available
  if (monthlyAmount > 0 && depositForm) {
    const amountInput = document.getElementById('deposit_amount');
    if (amountInput && (!amountInput.value || amountInput.value == 0)) {
      amountInput.value = monthlyAmount;
    }
  }

  // Handle form submission with AJAX
  if (depositForm) {
    depositForm.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      const submitBtn = document.getElementById('submitDepositBtn');
      const originalText = submitBtn.innerHTML;

      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

      fetch(this.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show success message
            if (typeof toastr !== 'undefined') {
              toastr.success(data.message || 'Deposit recorded successfully');
            } else {
              alert(data.message || 'Deposit recorded successfully');
            }

            // Close modal and reload page
            const modalElement = document.getElementById('makeDepositModal');
            if (modalElement) {
              const modal = bootstrap.Modal.getInstance(modalElement);
              if (modal) modal.hide();
            }

            // Reload page after 1 second
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          } else {
            // Show error
            if (data.errors) {
              let errorMsg = 'Please fix the following errors:\n';
              for (let field in data.errors) {
                errorMsg += data.errors[field][0] + '\n';
              }
              alert(errorMsg);
            } else {
              alert(data.message || 'Failed to record deposit');
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while processing the deposit');
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        });
    });
  }
});

function closeDeposit(depositId) {
  if (confirm('Are you sure you want to close this deposit? This action cannot be undone.')) {
    $.ajax({
      url: `/app/deposits/${depositId}/close`,
      type: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if (response.success) {
          if (typeof toastr !== 'undefined') {
            toastr.success(response.message || 'Deposit closed successfully');
          } else {
            alert(response.message || 'Deposit closed successfully');
          }
          setTimeout(() => {
            location.reload();
          }, 1000);
        }
      },
      error: function(xhr) {
        const errorMessage = xhr.responseJSON?.message || 'Error closing deposit';
        if (typeof toastr !== 'undefined') {
          toastr.error(errorMessage);
        } else {
          alert('Error closing deposit: ' + errorMessage);
        }
      }
    });
  }
}
</script>
@endpush

@extends('layouts/contentNavbarLayout')

@section('title', 'Deposit Details')

@section('content')
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
        </div>
      </div>
      
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">Deposit ID</label>
              <p class="form-control-plaintext">#{{ $deposit->id }}</p>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">Member</label>
              <p class="form-control-plaintext">
                {{ $deposit->member->name }}<br>
                <small class="text-muted">{{ $deposit->member->member_unique_id }}</small>
              </p>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">Product Name</label>
              <p class="form-control-plaintext">{{ $deposit->product_name ?: 'N/A' }}</p>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">Deposit Type</label>
              <p class="form-control-plaintext">
                <span class="badge bg-label-{{ $deposit->deposit_type === 'savings' ? 'info' : ($deposit->deposit_type === 'fixed' ? 'warning' : 'success') }}">
                  {{ ucfirst($deposit->deposit_type) }}
                </span>
              </p>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">Deposit Amount</label>
              <p class="form-control-plaintext">৳{{ number_format($deposit->deposit_amount, 2) }}</p>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">Current Balance</label>
              <p class="form-control-plaintext fw-bold text-success">৳{{ number_format($deposit->current_balance, 2) }}</p>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">Interest Rate</label>
              <p class="form-control-plaintext">{{ $deposit->rate_percentage }}% {{ $deposit->deposit_type === 'savings' ? 'per annum' : 'per annum' }}</p>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">Status</label>
              <p class="form-control-plaintext">
                <span class="badge bg-label-{{ $deposit->status === 'active' ? 'success' : ($deposit->status === 'matured' ? 'warning' : 'secondary') }}">
                  {{ ucfirst($deposit->status) }}
                </span>
              </p>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">Start Date</label>
              <p class="form-control-plaintext">{{ $deposit->start_date->format('M d, Y') }}</p>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-semibold">Maturity Date</label>
              <p class="form-control-plaintext">
                {{ $deposit->maturity_date ? $deposit->maturity_date->format('M d, Y') : 'N/A' }}
                @if($deposit->maturity_date && $deposit->maturity_date < now())
                  <span class="badge bg-label-danger ms-2">Overdue</span>
                @elseif($deposit->maturity_date && $deposit->maturity_date <= now()->addDays(30))
                  <span class="badge bg-label-warning ms-2">Due Soon</span>
                @endif
              </p>
            </div>
          </div>
        </div>
        
        @if($deposit->notes)
          <div class="mb-3">
            <label class="form-label fw-semibold">Notes</label>
            <p class="form-control-plaintext">{{ $deposit->notes }}</p>
          </div>
        @endif
      </div>
    </div>
  </div>
  
  <!-- Summary Statistics -->
  <div class="col-md-4">
    <div class="card">
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
    
    <!-- Quick Actions -->
    <div class="card mt-3">
      <div class="card-header">
        <h5 class="mb-0">Quick Actions</h5>
      </div>
      
      <div class="card-body">
        <div class="d-grid gap-2">
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDepositModal">
            <i class="bx bx-plus me-1"></i> Add Deposit
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
          View All
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
          
          <div class="d-flex justify-content-center">
            {{ $ledgerEntries->links() }}
          </div>
        @else
          <div class="text-center py-4">
            <p class="text-muted">No transactions found for this deposit.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modals -->
@include('deposits.modals.add-deposit')
@include('deposits.modals.withdraw')
@include('deposits.modals.accrue')
@include('deposits.modals.adjustment')
@endsection

@push('scripts')
<script>
function closeDeposit(depositId) {
  if (confirm('Are you sure you want to close this deposit? This action cannot be undone.')) {
    $.ajax({
      url: `/deposits/${depositId}/close`,
      type: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        location.reload();
      },
      error: function(xhr) {
        alert('Error closing deposit: ' + xhr.responseJSON.message);
      }
    });
  }
}
</script>
@endpush

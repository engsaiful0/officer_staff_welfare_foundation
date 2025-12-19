@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Investment Collection')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-edit-alt me-2"></i>Edit Investment Collection
                    </h5>
                    <a href="{{ route('investments.collection.show', $installment) }}" class="btn btn-light btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back to View
                    </a>
                </div>
                <div class="card-body">
                    <form id="editForm" action="{{ route('investments.collection.update', $installment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Column: Collection Info -->
                            <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="bx bx-info-circle me-2"></i>Collection Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Receipt Number:</strong></label>
                                            <input type="text" class="form-control bg-light" value="{{ $installment->receipt_number }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Installment Number:</strong></label>
                                            <input type="text" class="form-control bg-light" value="#{{ $installment->installment_number }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="paid_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                            <input type="date" 
                                                   class="form-control @error('paid_date') is-invalid @enderror" 
                                                   id="paid_date" 
                                                   name="paid_date" 
                                                   value="{{ old('paid_date', $installment->paid_date->format('Y-m-d')) }}" 
                                                   required>
                                            @error('paid_date')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Amount Details -->
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="bx bx-calculator me-2"></i>Payment Amount</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td class="text-muted">Principal:</td>
                                                <td class="text-end">${{ number_format($installment->principal_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Interest (Rent):</td>
                                                <td class="text-end">${{ number_format($installment->rent, 2) }}</td>
                                            </tr>
                                            @if($installment->fine_amount > 0)
                                            <tr>
                                                <td class="text-muted">Late Fee:</td>
                                                <td class="text-end text-danger">${{ number_format($installment->fine_amount, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="border-top">
                                                <td class="text-muted"><strong>Gross Amount:</strong></td>
                                                <td class="text-end"><strong>${{ number_format($installment->total_amount, 2) }}</strong></td>
                                            </tr>
                                        </table>
                                        <div class="mb-3 mt-3">
                                            <label for="discount_amount" class="form-label">Discount Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" 
                                                       class="form-control @error('discount_amount') is-invalid @enderror" 
                                                       id="discount_amount" 
                                                       name="discount_amount" 
                                                       value="{{ old('discount_amount', $installment->discount_amount ?? 0) }}" 
                                                       step="0.01" 
                                                       min="0" 
                                                       max="{{ $installment->total_amount }}">
                                            </div>
                                            <small class="text-muted">Enter discount amount if applicable</small>
                                            @error('discount_amount')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="alert alert-info">
                                            <strong>Net Amount:</strong> 
                                            <span id="net_amount_display">${{ number_format($installment->total_amount - ($installment->discount_amount ?? 0), 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Payment Method & Notes -->
                            <div class="col-lg-6">
                                <!-- Payment Method -->
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="bx bx-credit-card me-2"></i>Payment Method</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="payment_method_id" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                            <select class="form-select @error('payment_method_id') is-invalid @enderror" 
                                                    id="payment_method_id" 
                                                    name="payment_method_id" 
                                                    required>
                                                <option value="">-- Select Payment Method --</option>
                                                @foreach($paymentMethods as $method)
                                                    <option value="{{ $method->id }}" 
                                                            {{ old('payment_method_id', $installment->payment_method_id) == $method->id ? 'selected' : '' }}>
                                                        {{ $method->payment_method_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('payment_method_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div id="payment_method_fields">
                                            <div class="mb-3" id="bank_name_field" style="display: {{ old('bank_name', $installment->bank_name) ? 'block' : 'none' }};">
                                                <label for="bank_name" class="form-label">Bank Name</label>
                                                <input type="text" 
                                                       class="form-control @error('bank_name') is-invalid @enderror" 
                                                       id="bank_name" 
                                                       name="bank_name" 
                                                       value="{{ old('bank_name', $installment->bank_name) }}" 
                                                       placeholder="Enter bank name">
                                                @error('bank_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3" id="check_number_field" style="display: {{ old('check_number', $installment->check_number) ? 'block' : 'none' }};">
                                                <label for="check_number" class="form-label">Check Number</label>
                                                <input type="text" 
                                                       class="form-control @error('check_number') is-invalid @enderror" 
                                                       id="check_number" 
                                                       name="check_number" 
                                                       value="{{ old('check_number', $installment->check_number) }}" 
                                                       placeholder="Enter check number">
                                                @error('check_number')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="transaction_reference" class="form-label">Transaction Reference</label>
                                            <input type="text" 
                                                   class="form-control @error('transaction_reference') is-invalid @enderror" 
                                                   id="transaction_reference" 
                                                   name="transaction_reference" 
                                                   value="{{ old('transaction_reference', $installment->transaction_reference) }}" 
                                                   placeholder="e.g., TXN-123456789">
                                            <small class="text-muted">Optional: Bank transaction ID, reference number, etc.</small>
                                            @error('transaction_reference')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Notes -->
                                <div class="card mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0"><i class="bx bx-note me-2"></i>Additional Notes</h6>
                                    </div>
                                    <div class="card-body">
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" 
                                                  name="notes" 
                                                  rows="4" 
                                                  placeholder="Enter any additional notes or remarks...">{{ old('notes', $installment->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Member Info (Read-only) -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bx bx-user me-2"></i>Member Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td class="text-muted">Member:</td>
                                                <td><strong>{{ $installment->investment->member->name }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Account #:</td>
                                                <td>{{ $installment->investment->account->account_number ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('investments.collection.show', $installment) }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update Collection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountInput = document.getElementById('discount_amount');
    const netAmountDisplay = document.getElementById('net_amount_display');
    const paymentMethodSelect = document.getElementById('payment_method_id');
    const bankNameField = document.getElementById('bank_name_field');
    const checkNumberField = document.getElementById('check_number_field');
    const grossAmount = {{ $installment->total_amount }};

    // Calculate net amount when discount changes
    discountInput.addEventListener('input', function() {
        const discount = parseFloat(this.value) || 0;
        const netAmount = Math.max(0, grossAmount - discount);
        netAmountDisplay.textContent = '$' + netAmount.toFixed(2);
    });

    // Show/hide payment method fields
    paymentMethodSelect.addEventListener('change', function() {
        const selectedMethod = this.options[this.selectedIndex].text.toLowerCase();
        
        if (selectedMethod.includes('check') || selectedMethod.includes('cheque')) {
            bankNameField.style.display = 'block';
            checkNumberField.style.display = 'block';
            document.getElementById('bank_name').required = true;
            document.getElementById('check_number').required = true;
        } else if (selectedMethod.includes('bank') || selectedMethod.includes('transfer')) {
            bankNameField.style.display = 'block';
            checkNumberField.style.display = 'none';
            document.getElementById('bank_name').required = true;
            document.getElementById('check_number').required = false;
        } else {
            bankNameField.style.display = 'none';
            checkNumberField.style.display = 'none';
            document.getElementById('bank_name').required = false;
            document.getElementById('check_number').required = false;
        }
    });

    // Trigger on page load if payment method is already selected
    if (paymentMethodSelect.value) {
        paymentMethodSelect.dispatchEvent(new Event('change'));
    }

    // Form submission
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'Collection updated successfully');
                setTimeout(() => {
                    window.location.href = '{{ route("investments.collection.show", $installment) }}';
                }, 1500);
            } else {
                toastr.error(data.message || 'Failed to update collection');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred while updating the collection');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>
@endsection


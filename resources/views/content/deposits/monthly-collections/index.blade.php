@extends('layouts/contentNavbarLayout')

@section('title', 'Monthly Deposit Collections')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Monthly Deposit Collections</h5>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-primary export-btn" data-type="print">
            <i class="bx bx-printer me-1"></i> Print
          </button>
          <button type="button" class="btn btn-outline-success export-btn" data-type="excel">
            <i class="bx bx-file me-1"></i> Excel
          </button>
          <button type="button" class="btn btn-outline-danger export-btn" data-type="pdf">
            <i class="bx bx-file-blank me-1"></i> PDF
          </button>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#collectionModal" onclick="openCreateModal()">
            <i class="bx bx-plus me-1"></i> Add Collection
          </button>
        </div>
      </div>

      <div class="card-body">
        <!-- Filters -->
        <form id="filterForm" class="mb-4">
          <div class="row g-3">
            <div class="col-md-2">
              <label for="deposit_filter" class="form-label">Deposit Account</label>
              <select class="form-select" id="deposit_filter" name="deposit_id">
                <option value="">All Accounts</option>
                @foreach($deposits as $deposit)
                  <option value="{{ $deposit->id }}">
                    {{ $deposit->deposit_account_number }} - {{ $deposit->member->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label for="member_filter" class="form-label">Member</label>
              <select class="form-select" id="member_filter" name="member_id">
                <option value="">All Members</option>
                @foreach($members as $member)
                  <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->unique_id }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label for="collection_number_filter" class="form-label">Collection #</label>
              <input type="text" class="form-control" id="collection_number_filter" name="collection_number" placeholder="Search...">
            </div>
            <div class="col-md-2">
              <label for="date_from_filter" class="form-label">From Date</label>
              <input type="date" class="form-control" id="date_from_filter" name="date_from">
            </div>
            <div class="col-md-2">
              <label for="date_to_filter" class="form-label">To Date</label>
              <input type="date" class="form-control" id="date_to_filter" name="date_to">
            </div>
            <div class="col-md-2">
              <label for="month_filter" class="form-label">Month</label>
              <input type="text" class="form-control" id="month_filter" name="month" placeholder="e.g., January 2024">
            </div>
            <div class="col-md-12">
              <div class="input-group">
                <input type="text" class="form-control" id="search_filter" name="search" placeholder="Search by collection number, account number, member name...">
                <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                  <i class="bx bx-refresh me-1"></i> Reset
                </button>
              </div>
            </div>
          </div>
        </form>

        <!-- Summary Card -->
        <div class="row mb-3" id="summaryCard" style="display: none;">
          <div class="col-md-12">
            <div class="card bg-label-info">
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-md-4">
                    <h6 class="mb-0">Total Collections</h6>
                    <h4 class="mb-0" id="totalCollections">0</h4>
                  </div>
                  <div class="col-md-4">
                    <h6 class="mb-0">Total Amount</h6>
                    <h4 class="mb-0" id="totalAmount">৳0.00</h4>
                  </div>
                  <div class="col-md-4">
                    <h6 class="mb-0">Current Page Total</h6>
                    <h4 class="mb-0" id="pageTotal">৳0.00</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Collections Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Date</th>
                <th>Collection #</th>
                <th>Account #</th>
                <th>Member</th>
                <th>Amount</th>
                <th>Month</th>
                <th>Description</th>
                <th>Collected By</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="collectionsTableBody">
              <tr>
                <td colspan="9" class="text-center">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div id="paginationLinks" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="collectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Add Monthly Deposit Collection</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="collectionForm">
        <div class="modal-body">
          <input type="hidden" id="collection_id" name="id">
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="deposit_id" class="form-label">Deposit Account <span class="text-danger">*</span></label>
              <select class="form-select" id="deposit_id" name="deposit_id" required>
                <option value="">Select Deposit Account</option>
              </select>
              <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="collection_date" class="form-label">Collection Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="collection_date" name="collection_date" value="{{ date('Y-m-d') }}" required>
              <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required>
              </div>
              <div class="form-text" id="monthlyAmountHint"></div>
              <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="month" class="form-label">Month</label>
              <input type="text" class="form-control" id="month" name="month" placeholder="e.g., January 2024">
              <div class="form-text">Leave empty to auto-generate from date</div>
              <div class="invalid-feedback"></div>
            </div>
            <div class="col-12 mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3" placeholder="Additional notes..."></textarea>
              <div class="invalid-feedback"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner"></span>
            <span id="submitText">Save Collection</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
let isEditMode = false;
let currentCollectionId = null;

document.addEventListener('DOMContentLoaded', function() {
  const filterForm = document.getElementById('filterForm');
  const resetBtn = document.getElementById('resetFilters');
  const collectionModal = new bootstrap.Modal(document.getElementById('collectionModal'));
  
  // Initial load
  fetchCollections();

  // Filter events
  filterForm.querySelectorAll('input, select').forEach(element => {
    element.addEventListener('change', () => fetchCollections(1));
    element.addEventListener('input', debounce(() => fetchCollections(1), 500));
  });

  // Reset filters
  resetBtn.addEventListener('click', () => {
    filterForm.reset();
    fetchCollections(1);
  });

  // Export buttons
  document.querySelectorAll('.export-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const type = this.dataset.type;
      const formData = new FormData(filterForm);
      const params = new URLSearchParams(formData);
      params.append('type', type);
      window.open('{{ route("deposits.monthly-collections.export") }}?' + params.toString(), '_blank');
    });
  });

  // Form submission
  document.getElementById('collectionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    saveCollection();
  });

  // Deposit selection change
  document.getElementById('deposit_id').addEventListener('change', function() {
    const depositId = this.value;
    if (depositId) {
      const option = this.options[this.selectedIndex];
      const monthlyAmount = option.dataset.monthlyAmount;
      if (monthlyAmount) {
        document.getElementById('amount').value = monthlyAmount;
        document.getElementById('monthlyAmountHint').textContent = `Monthly deposit amount: ৳${parseFloat(monthlyAmount).toFixed(2)}`;
      }
    }
  });

  // Date change - auto-generate month
  document.getElementById('collection_date').addEventListener('change', function() {
    if (!document.getElementById('month').value) {
      const date = new Date(this.value);
      const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      document.getElementById('month').value = monthNames[date.getMonth()] + ' ' + date.getFullYear();
    }
  });

  // Pagination click handling
  document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
      e.preventDefault();
      const url = new URL(e.target.closest('a').href);
      const page = url.searchParams.get('page');
      fetchCollections(page);
    }
  });
});

function fetchCollections(page = 1) {
  const formData = new FormData(document.getElementById('filterForm'));
  formData.append('page', page);
  const tbody = document.getElementById('collectionsTableBody');
  
  // Show loading spinner
  tbody.innerHTML = '<tr><td colspan="9" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
  
  fetch('{{ route("deposits.monthly-collections.index") }}?' + new URLSearchParams(formData), {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => {
    if (!response.ok) {
      return response.json().then(err => Promise.reject(err));
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      renderTable(data.data);
      document.getElementById('paginationLinks').innerHTML = data.pagination || '';
      
      // Update summary
      if (data.summary) {
        document.getElementById('summaryCard').style.display = 'block';
        document.getElementById('totalCollections').textContent = data.summary.total_collections || 0;
        document.getElementById('totalAmount').textContent = '৳' + parseFloat(data.summary.total_amount || 0).toFixed(2);
        document.getElementById('pageTotal').textContent = '৳' + parseFloat(data.summary.current_page_total || 0).toFixed(2);
      }
    } else {
      tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">' + (data.message || 'Failed to load collections') + '</td></tr>';
      if (typeof toastr !== 'undefined') {
        toastr.error(data.message || 'Failed to load collections');
      }
    }
  })
  .catch(error => {
    console.error('Error:', error);
    const errorMessage = error.message || 'Failed to load collections. Please check your connection and try again.';
    tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">' + errorMessage + '</td></tr>';
    if (typeof toastr !== 'undefined') {
      toastr.error(errorMessage);
    }
  });
}

function renderTable(collections) {
  const tbody = document.getElementById('collectionsTableBody');
  
  if (collections.length === 0) {
    tbody.innerHTML = '<tr><td colspan="9" class="text-center">No collections found</td></tr>';
    return;
  }
  
  tbody.innerHTML = collections.map(collection => `
    <tr>
      <td>${new Date(collection.collection_date).toLocaleDateString()}</td>
      <td><strong>${collection.collection_number}</strong></td>
      <td>${collection.deposit?.deposit_account_number || 'N/A'}</td>
      <td>${collection.member?.name || 'N/A'}<br><small class="text-muted">${collection.member?.unique_id || ''}</small></td>
      <td><strong class="text-success">৳${parseFloat(collection.amount).toFixed(2)}</strong></td>
      <td>${collection.month || 'N/A'}</td>
      <td>${collection.description || '-'}</td>
      <td>${collection.created_by?.name || 'N/A'}</td>
      <td>
        <div class="dropdown">
          <button type="button" class="btn btn-sm btn-text-primary dropdown-toggle" data-bs-toggle="dropdown">
            <span style="font-size: 1.25rem; font-weight: 600; letter-spacing: 2px;">⋯</span>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="viewCollection(${collection.id})"><i class="bx bx-show me-2"></i> View</a></li>
            <li><a class="dropdown-item" href="#" onclick="openEditModal(${collection.id})"><i class="bx bx-edit me-2"></i> Edit</a></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="deleteCollection(${collection.id})"><i class="bx bx-trash me-2"></i> Delete</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" onclick="exportCollection(${collection.id}, 'print')"><i class="bx bx-printer me-2"></i> Print</a></li>
            <li><a class="dropdown-item" href="#" onclick="exportCollection(${collection.id}, 'pdf')"><i class="bx bx-file-blank me-2"></i> PDF</a></li>
          </ul>
        </div>
      </td>
    </tr>
  `).join('');
}

function openCreateModal() {
  isEditMode = false;
  currentCollectionId = null;
  document.getElementById('modalTitle').textContent = 'Add Monthly Deposit Collection';
  document.getElementById('submitText').textContent = 'Save Collection';
  document.getElementById('collectionForm').reset();
  document.getElementById('collection_id').value = '';
  document.getElementById('collection_date').value = '{{ date('Y-m-d') }}';
  document.getElementById('monthlyAmountHint').textContent = '';
  
  // Load deposits
  fetch('{{ route("deposits.monthly-collections.create") }}', {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const select = document.getElementById('deposit_id');
      select.innerHTML = '<option value="">Select Deposit Account</option>' +
        data.data.deposits.map(deposit => 
          `<option value="${deposit.id}" data-monthly-amount="${deposit.monthly_deposit_amount}">
            ${deposit.deposit_account_number} - ${deposit.member_name} (${deposit.member_id})
          </option>`
        ).join('');
    }
  });
}

function openEditModal(id) {
  isEditMode = true;
  currentCollectionId = id;
  document.getElementById('modalTitle').textContent = 'Edit Monthly Deposit Collection';
  document.getElementById('submitText').textContent = 'Update Collection';
  
  fetch(`{{ route("deposits.monthly-collections.index") }}/${id}/edit`, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const collection = data.data.collection;
      document.getElementById('collection_id').value = collection.id;
      document.getElementById('collection_date').value = collection.collection_date;
      document.getElementById('amount').value = collection.amount;
      document.getElementById('month').value = collection.month || '';
      document.getElementById('description').value = collection.description || '';
      
      // Load deposits
      const select = document.getElementById('deposit_id');
      select.innerHTML = '<option value="">Select Deposit Account</option>' +
        data.data.deposits.map(deposit => 
          `<option value="${deposit.id}" data-monthly-amount="${deposit.monthly_deposit_amount}" ${deposit.id == collection.deposit_id ? 'selected' : ''}>
            ${deposit.deposit_account_number} - ${deposit.member_name} (${deposit.member_id})
          </option>`
        ).join('');
      
      new bootstrap.Modal(document.getElementById('collectionModal')).show();
    }
  });
}

function saveCollection() {
  const form = document.getElementById('collectionForm');
  const formData = new FormData(form);
  const submitBtn = document.getElementById('submitBtn');
  const submitSpinner = document.getElementById('submitSpinner');
  const submitText = document.getElementById('submitText');
  
  submitBtn.disabled = true;
  submitSpinner.classList.remove('d-none');
  
  const url = isEditMode 
    ? `{{ route("deposits.monthly-collections.index") }}/${currentCollectionId}`
    : '{{ route("deposits.monthly-collections.store") }}';
  const method = isEditMode ? 'PUT' : 'POST';
  
  formData.append('_method', method);
  formData.append('_token', '{{ csrf_token() }}');
  
  fetch(url, {
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
      toastr.success(data.message || 'Collection saved successfully');
      bootstrap.Modal.getInstance(document.getElementById('collectionModal')).hide();
      fetchCollections();
    } else {
      if (data.errors) {
        Object.keys(data.errors).forEach(field => {
          const input = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
          if (input) {
            input.classList.add('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
              feedback.textContent = data.errors[field][0];
            }
          }
        });
      }
      toastr.error(data.message || 'Failed to save collection');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    toastr.error('An error occurred while saving the collection');
  })
  .finally(() => {
    submitBtn.disabled = false;
    submitSpinner.classList.add('d-none');
  });
}

function deleteCollection(id) {
  if (!confirm('Are you sure you want to delete this collection?')) {
    return;
  }
  
  fetch(`{{ route("deposits.monthly-collections.index") }}/${id}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      toastr.success(data.message || 'Collection deleted successfully');
      fetchCollections();
    } else {
      toastr.error(data.message || 'Failed to delete collection');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    toastr.error('An error occurred while deleting the collection');
  });
}

function viewCollection(id) {
  fetch(`{{ route("deposits.monthly-collections.index") }}/${id}`, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const collection = data.data;
      alert(`Collection Details:\n\nCollection #: ${collection.collection_number}\nDate: ${collection.collection_date}\nAmount: ৳${collection.amount}\nMember: ${collection.member?.name}\nAccount: ${collection.deposit?.deposit_account_number}`);
    }
  });
}

function exportCollection(id, type) {
  window.open(`{{ route("deposits.monthly-collections.export") }}?collection_id=${id}&type=${type}`, '_blank');
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}
</script>
@endsection



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
          <a href="{{ route('deposits.monthly-collections.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Add Collection
          </a>
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

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const filterForm = document.getElementById('filterForm');
  const resetBtn = document.getElementById('resetFilters');
  
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
  
  tbody.innerHTML = collections.map(collection => {
    const editUrl = '{{ route("deposits.monthly-collections.index") }}/' + collection.id + '/edit';
    return `
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
            <li><a class="dropdown-item" href="#" onclick="editCollection(${collection.id})"><i class="bx bx-edit me-2"></i> Edit</a></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="deleteCollection(${collection.id})"><i class="bx bx-trash me-2"></i> Delete</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" onclick="exportCollection(${collection.id}, 'print')"><i class="bx bx-printer me-2"></i> Print</a></li>
            <li><a class="dropdown-item" href="#" onclick="exportCollection(${collection.id}, 'pdf')"><i class="bx bx-file-blank me-2"></i> PDF</a></li>
          </ul>
        </div>
      </td>
    </tr>
  `;
  }).join('');
}

function editCollection(id) {
  window.location.href = `{{ route("deposits.monthly-collections.index") }}/${id}/edit`;
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



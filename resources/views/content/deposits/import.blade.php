@extends('layouts/contentNavbarLayout')

@section('title', 'Import Deposits')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Import Deposit Data</h5>
      </div>
      
      <div class="card-body">
        <form action="{{ route('deposits.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
          @csrf
          
          <div class="mb-4">
            <label for="file" class="form-label">Select Excel File <span class="text-danger">*</span></label>
            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                   id="file" name="file" accept=".xlsx,.xls,.csv" required>
            @error('file')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
              Supported formats: .xlsx, .xls, .csv (Max size: 10MB)
            </div>
          </div>
          
          <div class="alert alert-info">
            <h6 class="alert-heading">Import Instructions</h6>
            <ul class="mb-0">
              <li>Download the template file to see the required format</li>
              <li>Ensure member names or IDs match existing records</li>
              <li>Date formats should be YYYY-MM-DD or Excel serial dates</li>
              <li>Amounts should be numeric values only</li>
              <li>Interest rates should be in percentage (e.g., 8 for 8%)</li>
            </ul>
          </div>
          
          <div class="d-flex justify-content-between">
            <a href="{{ route('deposits.import.template') }}" class="btn btn-outline-secondary">
              <i class="bx bx-download me-1"></i> Download Template
            </a>
            
            <button type="submit" class="btn btn-primary" id="importBtn">
              <i class="bx bx-upload me-1"></i> Import Data
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Template Format</h5>
      </div>
      
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Column</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Member ID</td>
                <td>Member unique ID</td>
              </tr>
              <tr>
                <td>Member Name</td>
                <td>Full name</td>
              </tr>
              <tr>
                <td>Deposit Amount</td>
                <td>Initial amount</td>
              </tr>
              <tr>
                <td>Product Name</td>
                <td>Product description</td>
              </tr>
              <tr>
                <td>Start Date</td>
                <td>YYYY-MM-DD</td>
              </tr>
              <tr>
                <td>Maturity Date</td>
                <td>YYYY-MM-DD</td>
              </tr>
              <tr>
                <td>Deposit Type</td>
                <td>savings/fixed/recurring</td>
              </tr>
              <tr>
                <td>Interest Rate</td>
                <td>Percentage (e.g., 8)</td>
              </tr>
              <tr>
                <td>Withdrawal</td>
                <td>Withdrawal amount</td>
              </tr>
              <tr>
                <td>Interest</td>
                <td>Interest amount</td>
              </tr>
              <tr>
                <td>Balance</td>
                <td>Current balance</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Import History -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Import History</h5>
        <a href="{{ route('deposits.import.history') }}" class="btn btn-outline-primary btn-sm">
          View All
        </a>
      </div>
      
      <div class="card-body">
        @if($imports->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>File Name</th>
                  <th>Imported By</th>
                  <th>Imported At</th>
                  <th>Rows Imported</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($imports as $import)
                  <tr>
                    <td>{{ $import->filename }}</td>
                    <td>{{ $import->importedBy->name ?? 'System' }}</td>
                    <td>{{ $import->imported_at->format('M d, Y H:i') }}</td>
                    <td>{{ $import->rows_imported }}</td>
                    <td>
                      @if($import->has_errors)
                        <span class="badge bg-label-warning">
                          <i class="bx bx-error me-1"></i> {{ $import->error_count }} errors
                        </span>
                      @else
                        <span class="badge bg-label-success">
                          <i class="bx bx-check me-1"></i> Success
                        </span>
                      @endif
                    </td>
                    <td>
                      <a href="{{ route('deposits.import.show', $import) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-show me-1"></i> View Details
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          
          <div class="d-flex justify-content-center">
            {{ $imports->links() }}
          </div>
        @else
          <div class="text-center py-4">
            <p class="text-muted">No import history found.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#importForm').on('submit', function(e) {
    const fileInput = $('#file')[0];
    const file = fileInput.files[0];
    
    if (!file) {
      e.preventDefault();
      alert('Please select a file to import.');
      return false;
    }
    
    // Check file size (10MB limit)
    if (file.size > 10 * 1024 * 1024) {
      e.preventDefault();
      alert('File size must be less than 10MB.');
      return false;
    }
    
    // Check file type
    const allowedTypes = [
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
      'application/vnd.ms-excel', // .xls
      'text/csv' // .csv
    ];
    
    if (!allowedTypes.includes(file.type)) {
      e.preventDefault();
      alert('Please select a valid Excel or CSV file.');
      return false;
    }
    
    // Show loading state
    $('#importBtn').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Importing...');
  });
  
  // File input change handler
  $('#file').on('change', function() {
    const file = this.files[0];
    if (file) {
      const fileSize = (file.size / 1024 / 1024).toFixed(2);
      console.log('Selected file:', file.name, 'Size:', fileSize + 'MB');
    }
  });
});
</script>
@endpush

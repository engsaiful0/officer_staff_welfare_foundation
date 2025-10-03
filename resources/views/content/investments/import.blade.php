@extends('layouts.contentNavbarLayout')

@section('title', 'Import Investments')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Import Investment Data</h5>
                    <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Back to Investments
                    </a>
                </div>
                <div class="card-body">
                    <!-- Import Form -->
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('investments.import.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="import_type" class="form-label">Import Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('import_type') is-invalid @enderror" id="import_type" name="import_type" required>
                                        <option value="">Select Import Type</option>
                                        <option value="investments" {{ old('import_type') == 'investments' ? 'selected' : '' }}>Investments</option>
                                        <option value="ledger_entries" {{ old('import_type') == 'ledger_entries' ? 'selected' : '' }}>Ledger Entries</option>
                                    </select>
                                    @error('import_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">Excel File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                           id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                    <div class="form-text">Supported formats: .xlsx, .xls, .csv (Max size: 10MB)</div>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-upload me-1"></i> Import Data
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="downloadTemplate('investments')">
                                        <i class="bx bx-download me-1"></i> Download Template
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Import Instructions</h6>
                                <ul class="mb-0">
                                    <li>Download the template first</li>
                                    <li>Fill in your data following the template format</li>
                                    <li>Ensure all required fields are filled</li>
                                    <li>Save as Excel (.xlsx) format</li>
                                    <li>Upload the completed file</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Import Errors -->
                    @if(session('import_errors') && count(session('import_errors')) > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">Import Warnings</h6>
                                    <ul class="mb-0">
                                        @foreach(session('import_errors') as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Imports -->
    @if($recentImports->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Imports</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Imported By</th>
                                        <th>Date</th>
                                        <th>Rows Imported</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentImports as $import)
                                        <tr>
                                            <td>{{ $import->filename }}</td>
                                            <td>{{ $import->importedBy->name }}</td>
                                            <td>{{ $import->imported_at->format('M d, Y H:i') }}</td>
                                            <td>{{ $import->rows_imported }}</td>
                                            <td>
                                                @if($import->has_errors)
                                                    <span class="badge bg-warning">With Errors</span>
                                                @else
                                                    <span class="badge bg-success">Success</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($import->has_errors)
                                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                                            data-bs-toggle="modal" data-bs-target="#errorsModal{{ $import->id }}">
                                                        <i class="bx bx-error me-1"></i> View Errors
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Error Modals -->
@foreach($recentImports as $import)
    @if($import->has_errors)
        <div class="modal fade" id="errorsModal{{ $import->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Errors - {{ $import->filename }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <h6>Import completed with {{ $import->error_count }} errors:</h6>
                            <ul class="mb-0">
                                @foreach($import->errors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script>
function downloadTemplate(type) {
    window.location.href = `/app/investments/import/template/${type}`;
}
</script>
@endsection

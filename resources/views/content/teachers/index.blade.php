@extends('layouts/layoutMaster')

@section('title', 'Teacher List')


@section('page-script')
<script src="{{asset('assets/js/teacher-management.js')}}"></script>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Teacher List</h5>
    <div class="card-actions">
      <a href="{{ route('teachers.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
        <i class="ti ti-file-excel me-1"></i>Export Excel
      </a>
      <a href="{{ route('teachers.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
        <i class="ti ti-file-pdf me-1"></i>Export PDF
      </a>
    </div>
  </div>
  <div class="card-body">
    <!-- Filter Section -->
    <form method="GET" action="{{ route('teachers.view-teacher') }}" class="mb-4">
      <div class="row">
        <div class="col-md-3">
          <label for="designation_id" class="form-label">Filter by Designation</label>
          <select name="designation_id" id="designation_id" class="form-select">
            <option value="">All Designations</option>
            @foreach($designations as $designation)
              <option value="{{ $designation->id }}" {{ request('designation_id') == $designation->id ? 'selected' : '' }}>
                {{ $designation->designation_name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label for="search" class="form-label">Search</label>
          <input type="text" name="search" id="search" class="form-control" 
                 placeholder="Search teachers..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <label for="per_page" class="form-label">Rows per page</label>
          <select name="per_page" id="per_page" class="form-select">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">&nbsp;</label>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('teachers.view-teacher') }}" class="btn btn-secondary">Clear</a>
          </div>
        </div>
      </div>
    </form>

    <!-- Results Summary -->
    <div class="row mb-3">
      <div class="col-md-6">
        <p class="text-muted">
          Showing {{ $teachers->firstItem() ?? 0 }} to {{ $teachers->lastItem() ?? 0 }} 
          of {{ $teachers->total() }} results
        </p>
      </div>
    </div>

    <!-- Teachers Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Photo</th>
            <th>Teacher Name</th>
            <th>Teacher ID</th>
            <th>Designation</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($teachers as $index => $teacher)
            <tr>
              <td>{{ $teachers->firstItem() + $index }}</td>
              <td>
                @if($teacher->picture)
                  <img src="{{ asset('profile_pictures/' . $teacher->picture) }}" 
                       alt="{{ $teacher->teacher_name }}" 
                       class="rounded-circle" width="40" height="40">
                @else
                  <img src="{{ asset('images/default-avatar.png') }}" 
                       alt="default" class="rounded-circle" width="40" height="40">
                @endif
              </td>
              <td>{{ $teacher->teacher_name }}</td>
              <td>{{ $teacher->teacher_unique_id ?? 'N/A' }}</td>
              <td>{{ $teacher->designation->designation_name ?? 'N/A' }}</td>
              <td>{{ $teacher->email ?? 'N/A' }}</td>
              <td>{{ $teacher->mobile ?? 'N/A' }}</td>
              <td>
                <div class="d-inline-block">
                  <a href="{{ route('teachers.edit', $teacher) }}" 
                     class="btn btn-sm btn-text-secondary rounded-pill btn-icon">
                    <i class="ti ti-pencil ti-md"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-text-secondary rounded-pill btn-icon delete-teacher" 
                          data-id="{{ $teacher->id }}" data-url="{{ route('teachers.destroy', $teacher) }}">
                    <i class="ti ti-trash ti-md"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center">No teachers found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
      <div>
        <p class="text-muted">
          Showing {{ $teachers->firstItem() ?? 0 }} to {{ $teachers->lastItem() ?? 0 }} 
          of {{ $teachers->total() }} results
        </p>
      </div>
      <div>
        {{ $teachers->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
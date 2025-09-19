@extends('layouts/layoutMaster')

@section('title', 'Student List')

@section('page-script')
    <script src="{{ asset('assets/js/student-management.js') }}"></script>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Student List</h5>
    <div class="card-actions">
      <a href="{{ route('students.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
        <i class="ti ti-file-excel me-1"></i>Export Excel
      </a>
      <a href="{{ route('students.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
        <i class="ti ti-file-pdf me-1"></i>Export PDF
      </a>
    </div>
  </div>
  <div class="card-body">
    <!-- Filter Section -->
    <form method="GET" action="{{ route('students.index') }}" class="mb-4">
      <div class="row">
        <div class="col-md-3">
          <label for="academic_year_id" class="form-label">Academic Year</label>
          <select name="academic_year_id" id="academic_year_id" class="form-select select2">
            <option value="">All Academic Years</option>
            @foreach($academicYears as $academicYear)
              <option value="{{ $academicYear->id }}" {{ request('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                {{ $academicYear->academic_year_name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label for="semester_id" class="form-label">Semester</label>
          <select name="semester_id" id="semester_id" class="form-select select2">
            <option value="">All Semesters</option>
            @foreach($semesters as $semester)
              <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                {{ $semester->semester_name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label for="search" class="form-label">Search</label>
          <input type="text" name="search" id="search" class="form-control" 
                 placeholder="Search students..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <label for="per_page" class="form-label">Rows per page</label>
          <select name="per_page" id="per_page" class="form-select select2">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
          </select>
        </div>
        <div class="col-md-1">
          <label class="form-label">&nbsp;</label>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Filter</button>
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-12">
          <a href="{{ route('students.index') }}" class="btn btn-secondary">Clear Filters</a>
        </div>
      </div>
    </form>

    <!-- Results Summary -->
    <div class="row mb-3">
      <div class="col-md-6">
        <p class="text-muted">
          Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} 
          of {{ $students->total() }} results
        </p>
      </div>
    </div>

    <!-- Students Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Photo</th>
            <th>Student Name<br>Phone</th>
            <th>Student ID</th>
            <th>Father's Name<br>Mother's Name</th>
            <th>Academic Year</th>
            <th>Semester</th>
            <th>Technology</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $index => $student)
            <tr>
              <td>{{ $students->firstItem() + $index }}</td>
              <td>
                @if($student->picture)
                  <img src="{{ asset('assets/images/students/' . $student->picture) }}" 
                       alt="{{ $student->full_name_in_english_block_letter }}" 
                       class="rounded-circle" width="40" height="40">
                @else
                  <img src="{{ asset('images/students/default.png') }}" 
                       alt="default" class="rounded-circle" width="40" height="40">
                @endif
              </td>
              <td>{{ $student->full_name_in_english_block_letter }}<br>{{ $student->personal_number }}</td>
              <td>{{ $student->student_unique_id ?? 'N/A' }}</td>
              <td>{{ $student->father_name_in_english_block_letter }}<br>{{ $student->mother_name_in_english_block_letter }}</td>
              
              
              <td>{{ $student->academicYear->academic_year_name ?? 'N/A' }}</td>
              <td>{{ $student->semester->semester_name ?? 'N/A' }}</td>
              <td>{{ $student->technology->technology_name ?? 'N/A' }}</td>
              <td>
                <div class="d-inline-block">
                  <a href="{{ route('students.show', $student->id) }}" 
                     class="btn btn-sm btn-text-secondary rounded-pill btn-icon">
                    <i class="ti ti-eye ti-md"></i>
                  </a>
                  @permission('student-edit')
                  <a href="{{ route('students.edit', $student->id) }}" 
                     class="btn btn-sm btn-text-secondary rounded-pill btn-icon">
                    <i class="ti ti-pencil ti-md"></i>
                  </a>
                  @endpermission
                  @permission('student-delete')
                  <button type="button" class="btn btn-sm btn-text-secondary rounded-pill btn-icon delete-student" 
                          data-id="{{ $student->id }}" data-url="{{ route('students.destroy', $student->id) }}">
                    <i class="ti ti-trash ti-md"></i>
                  </button>
                  @endpermission
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center">No students found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
      <div>
        <p class="text-muted">
          Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} 
          of {{ $students->total() }} results
        </p>
      </div>
      <div>
        {{ $students->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

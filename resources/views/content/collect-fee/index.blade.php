@extends('layouts/layoutMaster')

@section('title', 'Fee Collection List')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-script')
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script>
  // Initialize flatpickr
  document.addEventListener('DOMContentLoaded', function () {
    flatpickr('.datepicker', {
      dateFormat: 'Y-m-d'
    });

    // Delete confirmation
    const deleteForms = document.querySelectorAll('.delete-fee-form');
    deleteForms.forEach(form => {
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!',
          customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-label-secondary'
          },
          buttonsStyling: false
        }).then(function (result) {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  });
</script>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Fee Collection</h5>
  </div>
  <div class="card-body">
    {{-- Search Form --}}
    <form method="GET" action="{{ route('app-collect-fee.view-collect-fee') }}">
      <div class="row">
        <div class="col-md-3">
          <label for="academic_year_id" class="form-label">Academic Year</label>
          <select id="academic_year_id" name="academic_year_id" class="form-select">
            <option value="">All</option>
            @foreach ($academicYears as $year)
            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
              {{ $year->academic_year_name }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label for="semester_id" class="form-label">Semester</label>
          <select id="semester_id" name="semester_id" class="form-select">
            <option value="">All</option>
            @foreach ($semesters as $semester)
            <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
              {{ $semester->semester_name }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label for="student_info" class="form-label">Student Info (Name/ID)</label>
          <input type="text" id="student_info" name="student_info" class="form-control"
            value="{{ request('student_info') }}">
        </div>
        <div class="col-md-2">
          <label for="from_date" class="form-label">From Date</label>
          <input type="text" id="from_date" name="from_date" class="form-control datepicker"
            value="{{ request('from_date') }}">
        </div>
        <div class="col-md-2">
          <label for="to_date" class="form-label">To Date</label>
          <input type="text" id="to_date" name="to_date" class="form-control datepicker"
            value="{{ request('to_date') }}">
        </div>
        
      </div>
      <div class="row mt-3">
        <div class="col-md-12">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </div>
    </form>
    <hr>

    {{-- Fee Collection Table --}}
    <div class="table-responsive">
      <table class="table table-responsive table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Student ID</th>
            <th>Academic Year</th>
            <th>Year</th>
            <th>Semester</th>
            <th>Date</th>
            <th>Total Amount</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($feeCollections as $fee)
          <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{ $fee->student->full_name_in_english_block_letter ?? '' }}</td>
            <td>{{ $fee->student->student_unique_id ?? '' }}</td>
            <td>{{ $fee->academic_year->academic_year_name ?? '' }}</td>
            <td>{{ $fee->year ?? 'N/A' }}</td>
            <td>{{ $fee->semester->semester_name ?? '' }}</td>
            <td>{{ $fee->date }}</td>
            <td>{{ $fee->total_amount }}</td>
            <td>
              <a href="{{ route('app-collect-fee.details', $fee->id) }}" class="btn btn-sm btn-primary">Details</a>
              <a href="{{ route('app-collect-fee.receipt', $fee->id) }}" class="btn btn-sm btn-info"
                target="_blank">Print</a>
              @permission('fee-collect-edit')
              <a href="{{ route('app-collect-fee.edit', $fee->id) }}" class="btn btn-sm btn-secondary">Edit</a>
              @endpermission
              @permission('fee-collect-delete')
              <form action="{{ route('app-collect-fee.destroy', $fee->id) }}" method="POST"
                style="display: inline-block;" class="delete-fee-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
              </form>
              @endpermission
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center">No fee collections found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $feeCollections->links() }}
    </div>
  </div>
</div>
@endsection

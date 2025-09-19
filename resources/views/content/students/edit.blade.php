@extends('layouts/layoutMaster')

@section('title', 'Edit Student')
@section('page-script')

<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.appConfig = {
        checkPersonalNumberUrl: "{{ route('students.check-personal-number-duplicate') }}"
    };
</script>
<script src="{{ asset('assets/js/student-management.js') }}"></script>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Edit Student</h5>
    </div>
    <div class="card-body">
        <form id="editStudentForm" action="{{ route('students.update', $student->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('content.students._form_edit')
          
            <input type="hidden" id="student_id" value="{{ $student->id }}">
          
            <button id="submit-edit-button" type="button" class="btn btn-primary">
                <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status"
                    aria-hidden="true"></span>
                <span id="button-text">Update</span>
            </button>
        </form>


    </div>
</div>
@endsection
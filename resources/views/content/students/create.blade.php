@extends('layouts/layoutMaster')

@section('title', 'Create Student')
@section('page-script')
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.appConfig = {
            checkPersonalNumberUrl: "{{ route('students.check-personal-number-duplicate') }}",
            storeStudentUrl: "{{ route('students.store') }}"
        };
    </script>
    
    <script src="{{ asset('assets/js/student-management.js') }}"></script>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Create Student</h5>
        </div>
        <div class="card-body">
        <form id="createStudentForm" method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
    @csrf
    @include('content.students._form_add')

    <button id="submit-button" type="button" class="btn btn-primary">
        <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        <span id="button-text">Save</span>
    </button>
</form>


        </div>
    </div>
@endsection


</div>
</div>

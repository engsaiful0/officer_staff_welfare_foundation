@extends('layouts/layoutMaster')

@section('title', 'Employee List')

@section('page-script')
<script>
    window.employeeAjaxUrl = '{{ route("employees.view-employee") }}';
</script>
<script src="{{asset('assets/js/employee-management.js')}}?v={{ time() }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">Employee List</h5>
        <a href="{{ route('employees.add-employee') }}" class="btn btn-primary">Add Employee</a>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-users table table-bordered table-hover" id="employee-datatable">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Employee Name</th>
                    <th>Photo</th>
                    <th>Designation</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection
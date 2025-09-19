@extends('layouts/layoutMaster')

@section('title', 'Member List')

@section('page-script')
<script>
    window.memberAjaxUrl = '{{ route("members.index") }}';
</script>
<script src="{{asset('assets/js/member-management.js')}}?v={{ time() }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">Member List</h5>
        @can('member-add')
        <a href="{{ route('members.create') }}" class="btn btn-primary">Add Member</a>
        @endcan
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-users table table-bordered table-hover" id="member-datatable">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Member Name</th>
                    <th>Photo</th>
                    <th>Unique ID</th>
                    <th>Designation</th>
                    <th>Branch</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Date of Join</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Member Details Modal -->
<div class="modal fade" id="memberDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Member Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="memberDetailsContent">
                <!-- Member details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

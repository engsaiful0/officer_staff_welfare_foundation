@extends('layouts/layoutMaster')

@section('title', 'Add Member')

@section('page-script')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.checkEmailUrl = '{{ route("members.check-email-unique") }}';
    window.checkMobileUrl = '{{ route("members.check-mobile-unique") }}';
    window.checkNidUrl = '{{ route("members.check-nid-unique") }}';
    window.getMembersUrl = '{{ route("members.get-members") }}';
    window.memberStoreUrl = '{{ route("members.store") }}';
    window.membersListUrl = '{{ route("members.view-member") }}';
</script>
<script src="{{asset('assets/js/member-utils.js')}}?v={{ time() }}"></script>
<script src="{{asset('assets/js/member-add.js')}}?v={{ time() }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Add New Member</h5>
    </div>
    <div class="card-body">
        <form id="memberForm" action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Personal Information -->
                <div class="col-12">
                    <h6 class="fw-semibold">Personal Information</h6>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" placeholder="Name" name="name" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="father_name" class="form-label">Father Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="father_name" placeholder="Father Name" name="father_name" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="mother_name" class="form-label">Mother Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="mother_name" placeholder="Mother Name" name="mother_name" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="spouse_name" class="form-label">Spouse Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="spouse_name" placeholder="Spouse Name" name="spouse_name" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="mobile" class="form-label">Mobile <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="mobile" placeholder="Mobile" name="mobile" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="email" class="form-label">Email </label>
                    <input type="email" class="form-control" id="email" placeholder="Email" name="email">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="nid_number" class="form-label">NID Number </label>
                    <input type="text" class="form-control" id="nid_number" placeholder="NID Number" name="nid_number">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="picture" class="form-label">Picture</label>
                    <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                    <div class="invalid-feedback" style="width: 100px;height: 100px;">

                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="diposit_account_number" class="form-label">Diposit Account Number </label>
                    <input type="text" class="form-control" id="diposit_account_number" placeholder="Diposit Account Number" name="diposit_account_number">
                    <div class="invalid-feedback"></div>
                </div>

                <!-- Professional Information -->
                <div class="col-12 mt-4">
                    <h6 class="fw-semibold">Professional Information</h6>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="designation_id" class="form-label">Designation <span class="text-danger">*</span></label>
                    <select class="form-select select2" id="designation_id" placeholder="Select Designation" name="designation_id" required>
                        <option value="">Select Designation</option>
                        @foreach($designations as $designation)
                        <option value="{{ $designation->id }}">{{ $designation->designation_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date_of_birth" placeholder="Date of Birth" name="date_of_birth" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="date_of_join_in_ibbl" class="form-label">Date of Join in IBBL <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date_of_join_in_ibbl" placeholder="Date of Join in IBBL" name="date_of_join_in_ibbl" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                    <select class="form-select select2" id="branch_id" placeholder="Select Branch" name="branch_id" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="religion_id" class="form-label">Religion <span class="text-danger">*</span></label>
                    <select class="form-select select2" id="religion_id" placeholder="Select Religion" name="religion_id" required>
                        <option value="">Select Religion</option>
                        @foreach($religions as $religion)
                        <option value="{{ $religion->id }}">{{ $religion->religion_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="employees_id">Employees ID <span class="text-danger">*</span></label>
                    <input  type="text" id="employees_id" class="form-control" placeholder="Employees ID" name="employees_id" required
                        class="form-control" placeholder="Employees ID"  />
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="member_unique_id">Member ID</label>
                    <input  type="text" id="member_unique_id" class="form-control" placeholder="Member ID" name="member_unique_id"
                        class="form-control" placeholder="ID"  />
                    <input type="hidden" id="serial" name="serial" class="form-control"
                        value="{{ $nextSerial }}" />
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="account_opening_date">Account Opening Date <span class="text-danger">*</span></label>
                    <input  type="date" id="account_opening_date" class="form-control" placeholder="Account Opening Date" name="account_opening_date" required
                        class="form-control" placeholder="Account Opening Date"  />
                </div>

                <!-- Address Information -->
                <div class="col-12 mt-4">
                    <h6 class="fw-semibold">Address Information</h6>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="present_address" class="form-label">Present Address <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="present_address" name="present_address" rows="3" placeholder="Present Address" required></textarea>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="permanent_address" class="form-label">Permanent Address <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="permanent_address" name="permanent_address" rows="3" placeholder="Permanent Address" required></textarea>
                    <div class="invalid-feedback"></div>
                </div>

                <!-- Nominee Information -->
                <div class="col-12 mt-4">
                    <h6 class="fw-semibold">Nominee Information</h6>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="nominee_name" class="form-label">Nominee Name</label>
                    <input type="text" class="form-control" id="nominee_name" placeholder="Nominee Name" name="nominee_name">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nominee_father_name" class="form-label">Nominee Father Name</label>
                    <input type="text" class="form-control" id="nominee_father_name" placeholder="Nominee Father Name" name="nominee_father_name">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nominee_mother_name" class="form-label">Nominee Mother Name</label>
                    <input type="text" class="form-control" id="nominee_mother_name" placeholder="Nominee Mother Name" name="nominee_mother_name">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nominee_spouse_name" class="form-label">Nominee Spouse Name</label>
                    <input type="text" class="form-control" id="nominee_spouse_name" placeholder="Nominee Spouse Name" name="nominee_spouse_name">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="nominee_relation_id" class="form-label">Nominee Relation</label>
                    <select class="form-select select2" id="nominee_relation_id" placeholder="Select Relation" name="nominee_relation_id">
                        <option value="">Select Relation (Optional)</option>
                        @foreach($relations as $relation)
                        <option value="{{ $relation->id }}">{{ $relation->relation_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="nominee_phone" class="form-label">Nominee Phone</label>
                    <input type="text" class="form-control" id="nominee_phone" placeholder="Nominee Phone" name="nominee_phone">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nominee_nid_number" class="form-label">Nominee NID Number</label>
                    <input type="text" class="form-control" id="nominee_nid_number" placeholder="Nominee NID Number" name="nominee_nid_number">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nominee_date_of_birth" class="form-label">Nominee Date of Birth</label>
                    <input type="date" class="form-control" id="nominee_date_of_birth" placeholder="Nominee Date of Birth" name="nominee_date_of_birth">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nominee_nid_number" class="form-label">Nominee Picture</label>
                    <input type="file" class="form-control" id="nominee_picture" accept="image/*" name="nominee_picture">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nominee_present_address" class="form-label">Present Address</label>
                    <textarea class="form-control" id="nominee_present_address" name="nominee_present_address" rows="3" placeholder="Present Address"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nominee_permanent_address" class="form-label">Permanent Address</label>
                    <textarea class="form-control" id="nominee_permanent_address" placeholder="Permanent Address" name="nominee_permanent_address" rows="3"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save Member</button>
                    <a href="{{ route('members.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
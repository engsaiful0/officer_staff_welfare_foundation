@extends('layouts/layoutMaster')

@section('title', 'Edit Member')

@section('page-script')
<script>
    window.checkEmailUrl = '{{ route("members.check-email-unique") }}';
    window.checkMobileUrl = '{{ route("members.check-mobile-unique") }}';
    window.checkNidUrl = '{{ route("members.check-nid-unique") }}';
    window.getMembersUrl = '{{ route("members.get-members") }}';
    window.memberId = {{ $member->id }};
</script>
<script src="{{asset('assets/js/member-form.js')}}?v={{ time() }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Edit Member</h5>
    </div>
    <div class="card-body">
        <form id="memberForm" action="{{ route('members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="member_id" name="member_id" value="{{ $member->id }}">
            
            <div class="row">
                <!-- Personal Information -->
                <div class="col-12">
                    <h6 class="fw-semibold">Personal Information</h6>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $member->name }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="father_name" class="form-label">Father Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="father_name" name="father_name" value="{{ $member->father_name }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="mobile" class="form-label">Mobile <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $member->mobile }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $member->email }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="nid_number" class="form-label">NID Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nid_number" name="nid_number" value="{{ $member->nid_number }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="picture" class="form-label">Picture</label>
                    <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                    @if($member->picture)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $member->picture) }}" alt="Current Picture" class="img-thumbnail" style="max-width: 100px;">
                        <small class="text-muted d-block">Current picture</small>
                    </div>
                    @endif
                    <div class="invalid-feedback"></div>
                </div>
                
                <!-- Professional Information -->
                <div class="col-12 mt-4">
                    <h6 class="fw-semibold">Professional Information</h6>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="designation_id" class="form-label">Designation <span class="text-danger">*</span></label>
                    <select class="form-select" id="designation_id" name="designation_id" required>
                        <option value="">Select Designation</option>
                        @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" {{ $member->designation_id == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="date_of_join" class="form-label">Date of Join <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date_of_join" name="date_of_join" value="{{ $member->date_of_join->format('Y-m-d') }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                    <select class="form-select" id="branch_id" name="branch_id" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $member->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="religion_id" class="form-label">Religion <span class="text-danger">*</span></label>
                    <select class="form-select" id="religion_id" name="religion_id" required>
                        <option value="">Select Religion</option>
                        @foreach($religions as $religion)
                        <option value="{{ $religion->id }}" {{ $member->religion_id == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="introducer_id" class="form-label">Introducer</label>
                    <select class="form-select" id="introducer_id" name="introducer_id">
                        <option value="">Select Introducer (Optional)</option>
                        @foreach($members as $memberOption)
                        <option value="{{ $memberOption->id }}" {{ $member->introducer_id == $memberOption->id ? 'selected' : '' }}>{{ $memberOption->name }} ({{ $memberOption->unique_id }})</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="member_unique_id">Member ID</label>
                    <input readonly type="text" id="member_unique_id" class="form-control" name="member_unique_id"
                        class="form-control" placeholder="M-0001" value="{{ $member->member_unique_id }}" />
                  
                </div>
                
                <!-- Address Information -->
                <div class="col-12 mt-4">
                    <h6 class="fw-semibold">Address Information</h6>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="present_address" class="form-label">Present Address <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="present_address" name="present_address" rows="3" required>{{ $member->present_address }}</textarea>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="permanent_address" class="form-label">Permanent Address <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="permanent_address" name="permanent_address" rows="3" required>{{ $member->permanent_address }}</textarea>
                    <div class="invalid-feedback"></div>
                </div>
                
                <!-- Nominee Information -->
                <div class="col-12 mt-4">
                    <h6 class="fw-semibold">Nominee Information</h6>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="nominee_name" class="form-label">Nominee Name</label>
                    <input type="text" class="form-control" id="nominee_name" name="nominee_name" value="{{ $member->nominee_name }}">
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="nominee_relation_id" class="form-label">Nominee Relation</label>
                    <select class="form-select" id="nominee_relation_id" name="nominee_relation_id">
                        <option value="">Select Relation (Optional)</option>
                        @foreach($relations as $relation)
                        <option value="{{ $relation->id }}" {{ $member->nominee_relation_id == $relation->id ? 'selected' : '' }}>{{ $relation->relation_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="nominee_phone" class="form-label">Nominee Phone</label>
                    <input type="text" class="form-control" id="nominee_phone" name="nominee_phone" value="{{ $member->nominee_phone }}">
                    <div class="invalid-feedback"></div>
                </div>
                
                <!-- Account Information (Read Only) -->
                <div class="col-12 mt-4">
                    <h6 class="fw-semibold">Account Information</h6>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Unique ID</label>
                    <input type="text" class="form-control" value="{{ $member->unique_id }}" readonly>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Temporary Username</label>
                    <input type="text" class="form-control" value="{{ $member->temp_username }}" readonly>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Temporary Password</label>
                    <input type="text" class="form-control" value="{{ $member->temp_password }}" readonly>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Member</button>
                    <a href="{{ route('members.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

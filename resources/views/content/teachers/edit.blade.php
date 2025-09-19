@extends('layouts/layoutMaster')

@section('title', 'Edit Teacher')

@section('page-style')
    <style>
        .text-danger {
            color: #dc3545 !important;
        }
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
        .form-label .text-danger {
            font-weight: bold;
        }
        .required-field {
            position: relative;
        }
        .required-field::after {
            content: '*';
            color: #dc3545;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
@endsection

@section('page-script')
<script src="{{asset('assets/js/teacher-management.js')}}"></script>
@endsection

@section('content')
<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title">Edit Teacher</h5>
  </div>
  <div class="card-body">
    <form id="editTeacherForm" action="{{ route('teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label class="form-label" for="teacher_name">Teacher Name <span class="text-danger">*</span></label>
          <input type="text" id="teacher_name" name="teacher_name" class="form-control" placeholder="John Doe" value="{{ old('teacher_name', $teacher->teacher_name) }}" required />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="teacher_id">Teacher ID</label>
          <input readonly type="text" id="teacher_unique_id" name="teacher_unique_id" class="form-control" placeholder="T-0001" value="{{ old('teacher_unique_id', $teacher->teacher_unique_id) }}" />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="father_name">Father's Name <span class="text-danger">*</span></label>
          <input type="text" id="father_name" name="father_name" class="form-control" placeholder="Robert Doe" value="{{ old('father_name', $teacher->father_name) }}" required />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="mother_name">Mother's Name <span class="text-danger">*</span></label>
          <input type="text" id="mother_name" name="mother_name" class="form-control" placeholder="Mary Doe" value="{{ old('mother_name', $teacher->mother_name) }}" required />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="mobile">Mobile <span class="text-danger">*</span></label>
          <input type="text" id="mobile" name="mobile" class="form-control" placeholder="+1 (123) 456-7890" value="{{ old('mobile', $teacher->mobile) }}" required />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="email">Email</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="john.doe@example.com" value="{{ old('email', $teacher->email) }}" />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="nid">NID</label>
          <input type="text" id="nid" name="nid" class="form-control" placeholder="1234567890" value="{{ old('nid', $teacher->nid) }}" />
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label" for="present_address">Present Address</label>
            <textarea id="present_address" name="present_address" class="form-control" placeholder="123, Main Street, New York, USA">{{ old('present_address', $teacher->present_address) }}</textarea>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label" for="permanent_address">Permanent Address</label>
            <textarea id="permanent_address" name="permanent_address" class="form-control" placeholder="123, Main Street, New York, USA">{{ old('permanent_address', $teacher->permanent_address) }}</textarea>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label" for="joining_date">Joining Date</label>
            <input type="date" id="joining_date" name="joining_date" class="form-control" value="{{ old('joining_date', $teacher->joining_date) }}" />
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label" for="gender">Gender</label>
            <select id="gender" name="gender" class="form-select select2">
              <option value="male" @if($teacher->gender == 'male') selected @endif>Male</option>
              <option value="female" @if($teacher->gender == 'female') selected @endif>Female</option>
              <option value="other" @if($teacher->gender == 'other') selected @endif>Other</option>
            </select>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label" for="designation_id">Designation <span class="text-danger">*</span></label>
          <select id="designation_id" name="designation_id" class="form-select select2" required>
            <option value="">Select Designation</option>
            @foreach($designations as $designation)
            <option value="{{ $designation->id }}" @if($teacher->designation_id == $designation->id) selected @endif>{{ $designation->designation_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label" for="picture">Profile Picture</label>
          <input type="file" id="picture" name="picture" class="form-control" accept="image/*" />
          <div class="mt-2">
            @if($teacher->picture)
            <img id="current-picture" src="{{ asset('profile_pictures/' . $teacher->picture) }}" alt="Current Picture" width="100" class="img-thumbnail">
            @endif
            <img id="picture-preview" src="" alt="Profile Picture Preview" style="max-width: 150px; max-height: 150px; display: none;" class="img-thumbnail">
          </div>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="nid_picture">NID Picture</label>
          <input type="file" id="nid_picture" name="nid_picture" class="form-control" accept="image/*" />
          <div class="mt-2">
            @if($teacher->nid_picture)
            <img id="current-nid-picture" src="{{ asset('nid_pictures/' . $teacher->nid_picture) }}" alt="Current NID Picture" width="100" class="img-thumbnail">
            @endif
            <img id="nid-picture-preview" src="" alt="NID Picture Preview" style="max-width: 150px; max-height: 150px; display: none;" class="img-thumbnail">
          </div>
        </div>

        <div class="col-12">
          <h6 class="mt-2">Salary Information</h6>
          <hr>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label" for="basic_salary">Basic Salary <span class="text-danger">*</span></label>
          <input type="number" id="basic_salary" name="basic_salary" class="form-control" placeholder="50000" value="{{ old('basic_salary', $teacher->basic_salary) }}" required />
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label" for="house_rent">House Rent <span class="text-danger">*</span></label>
          <input type="number" id="house_rent" name="house_rent" class="form-control" placeholder="10000" value="{{ old('house_rent', $teacher->house_rent) }}" required />
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label" for="medical_allowance">Medical Allowance</label>
          <input type="number" id="medical_allowance" name="medical_allowance" class="form-control" placeholder="5000" value="{{ old('medical_allowance', $teacher->medical_allowance) }}" min="0" step="0.01" />
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label" for="other_allowance">Other Allowance</label>
          <input type="number" id="other_allowance" name="other_allowance" class="form-control" placeholder="5000" value="{{ old('other_allowance', $teacher->other_allowance) }}" min="0" step="0.01" />
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label" for="gross_salary">Gross Salary</label>
            <input type="number" id="gross_salary" name="gross_salary" class="form-control" placeholder="75000" value="{{ old('gross_salary', $teacher->gross_salary) }}" />
        </div>

        <div class="col-12">
          <h6 class="mt-2">Educational Qualifications</h6>
          <hr>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label" for="ssc_or_equivalent_group">SSC or Equivalent Group</label>
          <input type="text" id="ssc_or_equivalent_group" name="ssc_or_equivalent_group" class="form-control" placeholder="Science" value="{{ old('ssc_or_equivalent_group', $teacher->ssc_or_equivalent_group) }}" />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="ssc_or_equivalent_gpa">SSC or Equivalent GPA</label>
          <input type="text" id="ssc_or_equivalent_gpa" name="ssc_or_equivalent_gpa" class="form-control" placeholder="5.00" value="{{ old('ssc_or_equivalent_gpa', $teacher->ssc_or_equivalent_gpa) }}" />
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label" for="hsc_or_equivalent_group">HSC or Equivalent Group</label>
          <input type="text" id="hsc_or_equivalent_group" name="hsc_or_equivalent_group" class="form-control" placeholder="Science" value="{{ old('hsc_or_equivalent_group', $teacher->hsc_or_equivalent_group) }}" />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="hsc_or_equivalent_gpa">HSC or Equivalent GPA</label>
          <input type="text" id="hsc_or_equivalent_gpa" name="hsc_or_equivalent_gpa" class="form-control" placeholder="5.00" value="{{ old('hsc_or_equivalent_gpa', $teacher->hsc_or_equivalent_gpa) }}" />
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label" for="bachelor_or_equivalent_group">Bachelor or Equivalent Group</label>
          <input type="text" id="bachelor_or_equivalent_group" name="bachelor_or_equivalent_group" class="form-control" placeholder="Computer Science" value="{{ old('bachelor_or_equivalent_group', $teacher->bachelor_or_equivalent_group) }}" />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="bachelor_or_equivalent_gpa">Bachelor or Equivalent GPA</label>
          <input type="number" id="bachelor_or_equivalent_gpa" name="bachelor_or_equivalent_gpa" class="form-control" placeholder="4.00" value="{{ old('bachelor_or_equivalent_gpa', $teacher->bachelor_or_equivalent_gpa) }}" min="0" max="4" step="0.01" />
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label" for="master_or_equivalent_group">Master or Equivalent Group</label>
          <input type="text" id="master_or_equivalent_group" name="master_or_equivalent_group" class="form-control" placeholder="Computer Science" value="{{ old('master_or_equivalent_group', $teacher->master_or_equivalent_group) }}" />
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="master_or_equivalent_gpa">Master or Equivalent GPA</label>
          <input type="number" id="master_or_equivalent_gpa" name="master_or_equivalent_gpa" class="form-control" placeholder="4.00" value="{{ old('master_or_equivalent_gpa', $teacher->master_or_equivalent_gpa) }}" min="0" max="4" step="0.01" />
        </div>

        <div class="col-12 text-center">
          <button type="submit" class="btn btn-primary me-sm-3 me-1">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            Update
          </button>
          <a href="{{ route('teachers.view-teacher') }}" class="btn btn-label-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

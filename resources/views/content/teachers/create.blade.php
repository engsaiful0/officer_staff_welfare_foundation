@extends('layouts/layoutMaster')

@section('title', 'Add Teacher')

<!-- Vendor Scripts -->
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
        <h5 class="card-title">Add Teacher</h5>
    </div>
    <div class="card-body">
        <form id="createTeacherForm" action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="teacher_name">Teacher Name <span class="text-danger">*</span></label>
                    <input type="text" id="teacher_name" name="teacher_name" class="form-control"
                        placeholder="John Doe" value="{{ old('teacher_name') }}" required />
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="teacher_unique_id">Teacher Unique ID</label>
                    <input readonly type="text" id="teacher_unique_id" name="teacher_unique_id" class="form-control" placeholder="T-0001"
                        value="{{ $teacher_unique_id }}" />
                    <input type="hidden" id="serial" name="serial" class="form-control"
                        value="{{ $nextSerial }}" />

                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="father_name">Father's Name <span class="text-danger">*</span></label>
                    <input type="text" id="father_name" name="father_name" class="form-control"
                        placeholder="Robert Doe" value="{{ old('father_name') }}" required />
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="mother_name">Mother's Name <span class="text-danger">*</span></label>
                    <input type="text" id="mother_name" name="mother_name" class="form-control"
                        placeholder="Mary Doe" value="{{ old('mother_name') }}" required />
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="mobile">Mobile <span class="text-danger">*</span></label>
                    <input type="text" id="mobile" name="mobile" class="form-control"
                        placeholder="+1 (123) 456-7890" value="{{ old('mobile') }}" required />
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        placeholder="john.doe@example.com" value="{{ old('email') }}" />
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="nid">NID</label>
                    <input type="text" id="nid" name="nid" class="form-control" placeholder="1234567890"
                        value="{{ old('nid') }}" />
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="present_address">Present Address</label>
                    <textarea id="present_address" name="present_address" class="form-control"
                        placeholder="123, Main Street, New York, USA">{{ old('present_address') }}</textarea>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="permanent_address">Permanent Address</label>
                    <textarea id="permanent_address" name="permanent_address" class="form-control"
                        placeholder="123, Main Street, New York, USA">{{ old('permanent_address') }}</textarea>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="joining_date">Joining Date</label>
                    <input type="date" id="joining_date" name="joining_date" class="form-control"
                        value="{{ old('joining_date') }}" />
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="gender">Gender <span class="text-danger">*</span></label>
                    <select id="gender" name="gender" class="form-select select2" required>
                        <option value="">Select Gender</option>
                        <option value="male" @if (old('gender')=='male' ) selected @endif>Male</option>
                        <option value="female" @if (old('gender')=='female' ) selected @endif>Female</option>
                        <option value="other" @if (old('gender')=='other' ) selected @endif>Other</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label" for="designation_id">Designation <span class="text-danger">*</span></label>
                    <select id="designation_id" name="designation_id" class="form-select select2" required>
                        <option value="">Select Designation</option>
                        @foreach ($designations as $designation)
                        <option value="{{ $designation->id }}" @if (old('designation_id')==$designation->id) selected @endif>
                            {{ $designation->designation_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label for="religion_id" class="form-label">Religion</label>
                    <select class="form-control select2" id="religion_id" name="religion_id">
                        <option value="">Select Religion</option>
                        @foreach ($religions as $religion)
                        <option value="{{ $religion->id }}"
                            {{ isset($student) && $student->religion_id == $religion->id ? 'selected' : '' }}>
                            {{ $religion->religion_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label" for="picture">Profile Picture</label>
                    <input type="file" id="picture" name="picture" class="form-control" accept="image/*" />
                    <div class="mt-2">
                        <img id="picture-preview" src="" alt="Profile Picture Preview" style="max-width: 150px; max-height: 150px; display: none;" class="img-thumbnail">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="nid_picture">NID Picture</label>
                    <input type="file" id="nid_picture" name="nid_picture" class="form-control" accept="image/*" />
                    <div class="mt-2">
                        <img id="nid-picture-preview" src="" alt="NID Picture Preview" style="max-width: 150px; max-height: 150px; display: none;" class="img-thumbnail">
                    </div>
                </div>

                <div class="col-12">
                    <h6 class="mt-2">Salary Information</h6>
                    <hr>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label" for="basic_salary">Basic Salary <span class="text-danger">*</span></label>
                    <input type="number" id="basic_salary" name="basic_salary" class="form-control"
                        placeholder="50000" value="{{ old('basic_salary') }}" required />
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label" for="house_rent">House Rent <span class="text-danger">*</span></label>
                    <input type="number" id="house_rent" name="house_rent" class="form-control"
                        placeholder="10000" value="{{ old('house_rent') }}" required />
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label" for="medical_allowance">Medical Allowance</label>
                    <input type="number" id="medical_allowance" name="medical_allowance" class="form-control"
                        placeholder="5000" value="{{ old('medical_allowance') }}" min="0" step="0.01" />
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label" for="other_allowance">Other Allowance</label>
                    <input type="number" id="other_allowance" name="other_allowance" class="form-control"
                        placeholder="5000" value="{{ old('other_allowance') }}" min="0" step="0.01" />
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label" for="gross_salary">Gross Salary</label>
                    <input readonly type="number" id="gross_salary" name="gross_salary" class="form-control"
                        placeholder="75000" value="{{ old('gross_salary') }}" readonly />
                </div>

                <!-- jQuery Script -->

                <script>
                    function calculateGrossSalary() {
                        let basic = parseFloat(document.getElementById('basic_salary').value) || 0;
                        let house = parseFloat(document.getElementById('house_rent').value) || 0;
                        let medical = parseFloat(document.getElementById('medical_allowance').value) || 0;
                        let other = parseFloat(document.getElementById('other_allowance').value) || 0;

                        let gross = basic + house + medical + other;
                        document.getElementById('gross_salary').value = gross;
                    }

                    document.querySelectorAll('#basic_salary, #house_rent, #medical_allowance, #other_allowance')
                        .forEach(input => input.addEventListener('input', calculateGrossSalary));
                </script>


                <div class="col-12">
                    <h6 class="mt-2">Educational Qualifications</h6>
                    <hr>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="ssc_or_equivalent_group">SSC or Equivalent Group</label>
                    <select id="ssc_or_equivalent_group" name="ssc_or_equivalent_group" class="form-control select2"
                        placeholder="Science">
                        <option value="Science" {{ old('ssc_or_equivalent_group') == 'Science' ? 'selected' : '' }}>
                            Science</option>
                        <option value="Arts" {{ old('ssc_or_equivalent_group') == 'Arts' ? 'selected' : '' }}>Arts
                        </option>
                        <option value="Commerce" {{ old('ssc_or_equivalent_group') == 'Commerce' ? 'selected' : '' }}>
                            Commerce</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="ssc_or_equivalent_gpa">SSC or Equivalent GPA</label>
                    <input type="text" id="ssc_or_equivalent_gpa" name="ssc_or_equivalent_gpa"
                        class="form-control" placeholder="5.00" value="{{ old('ssc_or_equivalent_gpa') }}" />
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="hsc_or_equivalent_group">HSC or Equivalent Group</label>
                    <select id="hsc_or_equivalent_group" name="hsc_or_equivalent_group" class="form-control select2"
                        placeholder="Science">
                        <option value="Science" {{ old('hsc_or_equivalent_group') == 'Science' ? 'selected' : '' }}>
                            Science</option>
                        <option value="Arts" {{ old('hsc_or_equivalent_group') == 'Arts' ? 'selected' : '' }}>Arts
                        </option>
                        <option value="Commerce" {{ old('hsc_or_equivalent_group') == 'Commerce' ? 'selected' : '' }}>
                            Commerce</option>
                        <option value="ET" {{ old('hsc_or_equivalent_group') == 'ET' ? 'selected' : '' }}>
                            ET</option>
                        <option value="CMT" {{ old('hsc_or_equivalent_group') == 'CMT' ? 'selected' : '' }}>
                            CMT</option>
                        <option value="MT" {{ old('hsc_or_equivalent_group') == 'MT' ? 'selected' : '' }}>
                            MT</option>
                        <option value="CT" {{ old('hsc_or_equivalent_group') == 'CT' ? 'selected' : '' }}>
                            CT</option>
                        <option value="PT" {{ old('hsc_or_equivalent_group') == 'PT' ? 'selected' : '' }}>
                            PT</option>
                        <option value="Other" {{ old('hsc_or_equivalent_group') == 'Other' ? 'selected' : '' }}>
                            Other</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="hsc_or_equivalent_gpa">HSC or Equivalent GPA</label>
                    <input type="number" id="hsc_or_equivalent_gpa" name="hsc_or_equivalent_gpa"
                        class="form-control" placeholder="5.00" value="{{ old('hsc_or_equivalent_gpa') }}" />
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="bachelor_or_equivalent_group">Bachelor or Equivalent Group</label>
                    <select id="bachelor_or_equivalent_group" name="bachelor_or_equivalent_group"
                        class="form-control">
                        <option value="">-- Select Group/Subject --</option>

                        <optgroup label="General">
                            <option value="Science"
                                {{ old('bachelor_or_equivalent_group') == 'Science' ? 'selected' : '' }}>Science
                            </option>
                            <option value="Arts"
                                {{ old('bachelor_or_equivalent_group') == 'Arts' ? 'selected' : '' }}>Arts</option>
                            <option value="Commerce"
                                {{ old('bachelor_or_equivalent_group') == 'Commerce' ? 'selected' : '' }}>Commerce
                            </option>
                        </optgroup>

                        <optgroup label="Engineering & Technology">
                            <option value="EEE"
                                {{ old('bachelor_or_equivalent_group') == 'EEE' ? 'selected' : '' }}>EEE</option>
                            <option value="ETE"
                                {{ old('bachelor_or_equivalent_group') == 'ETE' ? 'selected' : '' }}>ETE</option>
                            <option value="CSE"
                                {{ old('bachelor_or_equivalent_group') == 'CSE' ? 'selected' : '' }}>CSE</option>
                            <option value="CCE"
                                {{ old('bachelor_or_equivalent_group') == 'CCE' ? 'selected' : '' }}>CCE</option>
                            <option value="ME"
                                {{ old('bachelor_or_equivalent_group') == 'ME' ? 'selected' : '' }}>ME</option>
                            <option value="CE"
                                {{ old('bachelor_or_equivalent_group') == 'CE' ? 'selected' : '' }}>CE</option>
                            <option value="IPE"
                                {{ old('bachelor_or_equivalent_group') == 'IPE' ? 'selected' : '' }}>IPE</option>
                            <option value="TE"
                                {{ old('bachelor_or_equivalent_group') == 'TE' ? 'selected' : '' }}>TE
                            </option>
                            <option value="ARC"
                                {{ old('bachelor_or_equivalent_group') == 'ARC' ? 'selected' : '' }}>
                                Architecture</option>
                            <option value="ICT"
                                {{ old('bachelor_or_equivalent_group') == 'ICT' ? 'selected' : '' }}>ICT</option>
                        </optgroup>

                        <optgroup label="Business & Management">
                            <option value="BBA"
                                {{ old('bachelor_or_equivalent_group') == 'BBA' ? 'selected' : '' }}>BBA</option>
                            <option value="Accounting"
                                {{ old('bachelor_or_equivalent_group') == 'Accounting' ? 'selected' : '' }}>Accounting
                            </option>
                            <option value="Finance"
                                {{ old('bachelor_or_equivalent_group') == 'Finance' ? 'selected' : '' }}>Finance
                            </option>
                            <option value="Marketing"
                                {{ old('bachelor_or_equivalent_group') == 'Marketing' ? 'selected' : '' }}>Marketing
                            </option>
                            <option value="Management"
                                {{ old('bachelor_or_equivalent_group') == 'Management' ? 'selected' : '' }}>Management
                            </option>
                            <option value="HRM"
                                {{ old('bachelor_or_equivalent_group') == 'HRM' ? 'selected' : '' }}>HRM</option>
                        </optgroup>

                        <optgroup label="Health Sciences">
                            <option value="MBBS"
                                {{ old('bachelor_or_equivalent_group') == 'MBBS' ? 'selected' : '' }}>MBBS</option>
                            <option value="Dental"
                                {{ old('bachelor_or_equivalent_group') == 'Dental' ? 'selected' : '' }}>Dental</option>
                            <option value="Nursing"
                                {{ old('bachelor_or_equivalent_group') == 'Nursing' ? 'selected' : '' }}>Nursing
                            </option>
                            <option value="Pharmacy"
                                {{ old('bachelor_or_equivalent_group') == 'Pharmacy' ? 'selected' : '' }}>Pharmacy
                            </option>
                            <option value="Public Health"
                                {{ old('bachelor_or_equivalent_group') == 'Public Health' ? 'selected' : '' }}>Public
                                Health</option>
                            <option value="Physiotherapy"
                                {{ old('bachelor_or_equivalent_group') == 'Physiotherapy' ? 'selected' : '' }}>
                                Physiotherapy</option>
                        </optgroup>

                        <optgroup label="Social Sciences & Humanities">
                            <option value="Economics"
                                {{ old('bachelor_or_equivalent_group') == 'Economics' ? 'selected' : '' }}>Economics
                            </option>
                            <option value="Political Science"
                                {{ old('bachelor_or_equivalent_group') == 'Political Science' ? 'selected' : '' }}>
                                Political Science</option>
                            <option value="Sociology"
                                {{ old('bachelor_or_equivalent_group') == 'Sociology' ? 'selected' : '' }}>Sociology
                            </option>
                            <option value="Psychology"
                                {{ old('bachelor_or_equivalent_group') == 'Psychology' ? 'selected' : '' }}>Psychology
                            </option>
                            <option value="History"
                                {{ old('bachelor_or_equivalent_group') == 'History' ? 'selected' : '' }}>History
                            </option>
                            <option value="English"
                                {{ old('bachelor_or_equivalent_group') == 'English' ? 'selected' : '' }}>English
                            </option>
                            <option value="Bangla"
                                {{ old('bachelor_or_equivalent_group') == 'Bangla' ? 'selected' : '' }}>Bangla</option>
                            <option value="Philosophy"
                                {{ old('bachelor_or_equivalent_group') == 'Philosophy' ? 'selected' : '' }}>Philosophy
                            </option>
                            <option value="International Relations"
                                {{ old('bachelor_or_equivalent_group') == 'International Relations' ? 'selected' : '' }}>
                                International Relations</option>
                        </optgroup>

                        <optgroup label="Agriculture & Others">
                            <option value="Agriculture"
                                {{ old('bachelor_or_equivalent_group') == 'Agriculture' ? 'selected' : '' }}>
                                Agriculture</option>
                            <option value="Fisheries"
                                {{ old('bachelor_or_equivalent_group') == 'Fisheries' ? 'selected' : '' }}>Fisheries
                            </option>
                            <option value="Veterinary Science"
                                {{ old('bachelor_or_equivalent_group') == 'Veterinary Science' ? 'selected' : '' }}>
                                Veterinary Science</option>
                            <option value="Law"
                                {{ old('bachelor_or_equivalent_group') == 'Law' ? 'selected' : '' }}>Law</option>
                            <option value="Fine Arts"
                                {{ old('bachelor_or_equivalent_group') == 'Fine Arts' ? 'selected' : '' }}>Fine Arts
                            </option>
                            <option value="Education"
                                {{ old('bachelor_or_equivalent_group') == 'Education' ? 'selected' : '' }}>Education
                            </option>
                            <option value="Other"
                                {{ old('bachelor_or_equivalent_group') == 'Other' ? 'selected' : '' }}>Other</option>
                        </optgroup>
                    </select>

                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="bachelor_or_equivalent_gpa">Bachelor or Equivalent GPA</label>
                    <input type="number" id="bachelor_or_equivalent_gpa" name="bachelor_or_equivalent_gpa"
                        class="form-control" placeholder="4.00" value="{{ old('bachelor_or_equivalent_gpa') }}" min="0" max="4" step="0.01" />
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="master_or_equivalent_group">Master or Equivalent Group</label>
                    <select id="master_or_equivalent_group" name="master_or_equivalent_group" class="form-control select2">
                        <option value="">-- Select Group/Subject --</option>

                        <optgroup label="General">
                            <option value="Science"
                                {{ old('master_or_equivalent_group') == 'Science' ? 'selected' : '' }}>Science</option>
                            <option value="Arts"
                                {{ old('master_or_equivalent_group') == 'Arts' ? 'selected' : '' }}>Arts</option>
                            <option value="Commerce"
                                {{ old('master_or_equivalent_group') == 'Commerce' ? 'selected' : '' }}>Commerce
                            </option>
                        </optgroup>

                        <optgroup label="Engineering & Technology">
                            <option value="EEE"
                                {{ old('bachelor_or_equivalent_group') == 'EEE' ? 'selected' : '' }}>EEE</option>
                            <option value="ETE"
                                {{ old('bachelor_or_equivalent_group') == 'ETE' ? 'selected' : '' }}>ETE</option>
                            <option value="CSE"
                                {{ old('bachelor_or_equivalent_group') == 'CSE' ? 'selected' : '' }}>CSE</option>
                            <option value="CCE"
                                {{ old('bachelor_or_equivalent_group') == 'CCE' ? 'selected' : '' }}>CCE</option>
                            <option value="ME"
                                {{ old('bachelor_or_equivalent_group') == 'ME' ? 'selected' : '' }}>ME</option>
                            <option value="CE"
                                {{ old('bachelor_or_equivalent_group') == 'CE' ? 'selected' : '' }}>CE</option>
                            <option value="IPE"
                                {{ old('bachelor_or_equivalent_group') == 'IPE' ? 'selected' : '' }}>IPE</option>
                            <option value="TE"
                                {{ old('bachelor_or_equivalent_group') == 'TE' ? 'selected' : '' }}>TE
                            </option>
                            <option value="ARC"
                                {{ old('bachelor_or_equivalent_group') == 'ARC' ? 'selected' : '' }}>
                                Architecture</option>
                            <option value="ICT"
                                {{ old('bachelor_or_equivalent_group') == 'ICT' ? 'selected' : '' }}>ICT</option>
                        </optgroup>

                        <optgroup label="Business & Management">
                            <option value="MBA" {{ old('master_or_equivalent_group') == 'MBA' ? 'selected' : '' }}>
                                MBA</option>
                            <option value="Accounting"
                                {{ old('master_or_equivalent_group') == 'Accounting' ? 'selected' : '' }}>Accounting
                            </option>
                            <option value="Finance"
                                {{ old('master_or_equivalent_group') == 'Finance' ? 'selected' : '' }}>Finance</option>
                            <option value="Marketing"
                                {{ old('master_or_equivalent_group') == 'Marketing' ? 'selected' : '' }}>Marketing
                            </option>
                            <option value="Management"
                                {{ old('master_or_equivalent_group') == 'Management' ? 'selected' : '' }}>Management
                            </option>
                            <option value="HRM"
                                {{ old('master_or_equivalent_group') == 'HRM' ? 'selected' : '' }}>HRM</option>
                        </optgroup>

                        <optgroup label="Health Sciences">
                            <option value="MS/MD"
                                {{ old('master_or_equivalent_group') == 'MS/MD' ? 'selected' : '' }}>MS/MD</option>
                            <option value="MDS"
                                {{ old('master_or_equivalent_group') == 'MDS' ? 'selected' : '' }}>MDS</option>
                            <option value="Nursing"
                                {{ old('master_or_equivalent_group') == 'Nursing' ? 'selected' : '' }}>Nursing
                            </option>
                            <option value="Pharmacy"
                                {{ old('master_or_equivalent_group') == 'Pharmacy' ? 'selected' : '' }}>Pharmacy
                            </option>
                            <option value="Public Health (MPH)"
                                {{ old('master_or_equivalent_group') == 'Public Health (MPH)' ? 'selected' : '' }}>
                                Public Health (MPH)</option>
                            <option value="Physiotherapy"
                                {{ old('master_or_equivalent_group') == 'Physiotherapy' ? 'selected' : '' }}>
                                Physiotherapy</option>
                        </optgroup>

                        <optgroup label="Social Sciences & Humanities">
                            <option value="Economics"
                                {{ old('master_or_equivalent_group') == 'Economics' ? 'selected' : '' }}>Economics
                            </option>
                            <option value="Political Science"
                                {{ old('master_or_equivalent_group') == 'Political Science' ? 'selected' : '' }}>
                                Political Science</option>
                            <option value="Sociology"
                                {{ old('master_or_equivalent_group') == 'Sociology' ? 'selected' : '' }}>Sociology
                            </option>
                            <option value="Psychology"
                                {{ old('master_or_equivalent_group') == 'Psychology' ? 'selected' : '' }}>Psychology
                            </option>
                            <option value="History"
                                {{ old('master_or_equivalent_group') == 'History' ? 'selected' : '' }}>History
                            </option>
                            <option value="English"
                                {{ old('master_or_equivalent_group') == 'English' ? 'selected' : '' }}>English
                            </option>
                            <option value="Bangla"
                                {{ old('master_or_equivalent_group') == 'Bangla' ? 'selected' : '' }}>Bangla</option>
                            <option value="Philosophy"
                                {{ old('master_or_equivalent_group') == 'Philosophy' ? 'selected' : '' }}>Philosophy
                            </option>
                            <option value="International Relations"
                                {{ old('master_or_equivalent_group') == 'International Relations' ? 'selected' : '' }}>
                                International Relations</option>
                        </optgroup>

                        <optgroup label="Agriculture & Others">
                            <option value="Agriculture"
                                {{ old('master_or_equivalent_group') == 'Agriculture' ? 'selected' : '' }}>Agriculture
                            </option>
                            <option value="Fisheries"
                                {{ old('master_or_equivalent_group') == 'Fisheries' ? 'selected' : '' }}>Fisheries
                            </option>
                            <option value="Veterinary Science"
                                {{ old('master_or_equivalent_group') == 'Veterinary Science' ? 'selected' : '' }}>
                                Veterinary Science</option>
                            <option value="Law (LLM)"
                                {{ old('master_or_equivalent_group') == 'Law (LLM)' ? 'selected' : '' }}>Law (LLM)
                            </option>
                            <option value="Fine Arts"
                                {{ old('master_or_equivalent_group') == 'Fine Arts' ? 'selected' : '' }}>Fine Arts
                            </option>
                            <option value="Education (M.Ed)"
                                {{ old('master_or_equivalent_group') == 'Education (M.Ed)' ? 'selected' : '' }}>
                                Education (M.Ed)</option>
                            <option value="Other"
                                {{ old('master_or_equivalent_group') == 'Other' ? 'selected' : '' }}>Other</option>
                        </optgroup>
                    </select>

                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="master_or_equivalent_gpa">Master or Equivalent GPA</label>
                    <input type="number" id="master_or_equivalent_gpa" name="master_or_equivalent_gpa"
                        class="form-control" placeholder="4.00" value="{{ old('master_or_equivalent_gpa') }}" min="0" max="4" step="0.01" />
                </div>

                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Submit
                    </button>
                    <a href="{{ route('teachers.view-teacher') }}" class="btn btn-label-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
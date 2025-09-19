 <script>
     function previewImage(event) {
         const file = event.target.files[0];
         const reader = new FileReader();

         reader.onload = function(e) {
             const image = document.getElementById('imagePreview');
             image.src = e.target.result;
         };

         if (file) {
             reader.readAsDataURL(file);
         }
     }
 </script>

 <style>
     .phone-error-message {
         display: block;
         margin-top: 0.25rem;
         font-size: 0.875rem;
         color: #dc3545;
     }
     
     .form-control.is-valid {
         border-color: #198754;
         background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.94-.94 1.88 1.88 3.75-3.75.94.94L4.12 9.55z'/%3e%3c/svg%3e");
         background-repeat: no-repeat;
         background-position: right calc(0.375em + 0.1875rem) center;
         background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
     }
     
    .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4 1.4-1.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    
    .personal-number-status {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.875rem;
    }
    
    .personal-number-status.available {
        color: #198754;
    }
    
    .personal-number-status.unavailable {
        color: #dc3545;
    }
 </style>

 <div class="row">
     <!-- Personal Information Fieldset -->
     <div class="card mb-4">
         <div class="card-header d-flex align-items-center justify-content-between">
             <h5 class="mb-0">Personal Information</h5>
         </div>
         <div class="card-body">
             <div class="row"> <!-- Wrap in a row for columns -->
                 <div class="col-md-4">
                     <div class="mb-3">
                         <label for="full_name_in_banglai" class="form-label">Full Name (Bangla) <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="full_name_in_banglai"
                             name="full_name_in_banglai" placeholder="Enter Full Name in Bangla"
                             value="{{ old('full_name_in_banglai', $student->full_name_in_banglai ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="father_name_in_banglai" class="form-label">Father's Name (Bangla) <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="father_name_in_banglai"
                             name="father_name_in_banglai" placeholder="Enter Father's Name in Bangla"
                             value="{{ old('father_name_in_banglai', $student->father_name_in_banglai ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="mother_name_in_banglai" class="form-label">Mother's Name (Bangla) <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="mother_name_in_banglai"
                             name="mother_name_in_banglai" placeholder="Enter Mother's Name in Bangla"
                             value="{{ old('mother_name_in_banglai', $student->mother_name_in_banglai ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="mother_name_in_english_block_letter" class="form-label">Mother's Name
                             (English) <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="mother_name_in_english_block_letter"
                             name="mother_name_in_english_block_letter" placeholder="Enter Mother's Name in English"
                             value="{{ old('mother_name_in_english_block_letter', $student->mother_name_in_english_block_letter ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="personal_number" class="form-label">Personal Phone <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="personal_number"
                             name="personal_number" placeholder="Enter Personal Phone Number (11 digits)"
                             value="{{ old('personal_number', $student->personal_number ?? '') }}"
                             maxlength="11" pattern="[0-9]{11}" required>
                         <div class="form-text">Enter exactly 11 digits (e.g., 01712345678)</div>
                         <div class="personal-number-error"></div>
                     </div>
                      <div class="mb-3">
                         <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                         <input type="email" class="form-control" id="email"
                             name="email" placeholder="Enter Email Address"
                             value="{{ old('email', $student->email ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="guardian_phone" class="form-label">Guardian Phone <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="guardian_phone"
                             name="guardian_phone" placeholder="Enter Guardian Phone Number"
                             value="{{ old('guardian_phone', $student->guardian_phone ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="present_address" class="form-label">Present Address <span class="text-danger">*</span></label>
                         <textarea class="form-control" id="present_address" name="present_address" placeholder="Enter Present Address">{{ old('present_address', $student->present_address ?? '') }}</textarea>
                     </div>
                     
                 </div>

                 <div class="col-md-4">
                     <div class="mb-3">
                         <label for="full_name_in_english_block_letter" class="form-label">Full Name (English) <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="full_name_in_english_block_letter"
                             name="full_name_in_english_block_letter" placeholder="Enter Full Name in English"
                             value="{{ old('full_name_in_english_block_letter', $student->full_name_in_english_block_letter ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="father_name_in_english_block_letter" class="form-label">Father's Name
                             (English) <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="father_name_in_english_block_letter"
                             name="father_name_in_english_block_letter" placeholder="Enter Father's Name in English"
                             value="{{ old('father_name_in_english_block_letter', $student->father_name_in_english_block_letter ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="guardian_name_absence_of_father" class="form-label">Guardian's Name</label>
                         <input type="text" class="form-control" id="guardian_name_absence_of_father"
                             name="guardian_name_absence_of_father" placeholder="Enter Guardian's Name"
                             value="{{ old('guardian_name_absence_of_father', $student->guardian_name_absence_of_father ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <div class="row">
                             <div class="col-md-6">
                                 <label for="picture" class="form-label">Picture</label>
                                 <input type="file" class="form-control" id="picture" name="picture"
                                     onchange="previewImage(event)">
                             </div>
                             <div class="col-md-6">
                                 <img id="imagePreview"
                                     src="{{ isset($student) && $student->picture ? asset('assets/images/students/' . $student->picture) : asset('assets/images/students/default.png') }}"
                                     alt="Student Picture" class="img-thumbnail mt-2"
                                     style="max-width: 150px;height:130px">
                             </div>
                         </div>
                     </div>
                     <div class="mb-3">
                         <label for="permanent_address" class="form-label">Permanent Address <span class="text-danger">*</span></label>
                         <textarea class="form-control" id="permanent_address" name="permanent_address" placeholder="Enter Permanent Address">{{ old('permanent_address', $student->permanent_address ?? '') }}</textarea>
                     </div>
                     <div class="mb-3">
                         <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                         <select class="form-select form-control select2" id="semester_id" name="semester_id">
                             <option disabled selected value="">Select Semester</option>
                             @foreach ($semesters as $semester)
                                 <option value="{{ $semester->id }}"
                                     {{ isset($student) && $student->semester_id == $semester->id ? 'selected' : '' }}>
                                     {{ $semester->semester_name }}</option>
                             @endforeach
                         </select>
                     </div>
                     <div class="mb-3">
                         <label for="academic_year_id" class="form-label">Academic Year</label>
                         <select  class="form-control select2" id="academic_year_id"
                             name="academic_year_id">
                             <option value="">Select Academic Year</option>
                             @foreach ($academic_years as $academic_year)
                                 <option value="{{ $academic_year->id }}"
                                     {{ isset($student) && $student->academic_year_id == $academic_year->id ? 'selected' : '' }}>
                                     {{ $academic_year->academic_year_name }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                     
                 </div>

                 <div class="col-md-4">
                     
                     <div class="mb-3">
                         <label for="student_unique_id" class="form-label">Unique ID</label>
                         <input readonly placeholder="Student Unique Id" class="form-control" id="student_unique_id"
                             name="student_unique_id"
                             value="{{ old('student_unique_id', $student_unique_id ?? ($student->student_unique_id ?? '')) }}">

                         <input type="hidden" name="serial" id="serial" 
       value="{{ isset($student) ? $student->serial : $nextSerial }}">
                         
                         @if(isset($student))
                         <input type="hidden" name="student_id" id="student_id" value="{{ $student->id }}">
                         @endif

                     </div>
                     <div class="mb-3">
                         <label for="nationality_id" class="form-label">Nationality <span class="text-danger">*</span></label>
                         <select class="form-control select2" id="nationality_id" name="nationality_id">
                             <option value="">Select Nationality</option>
                             @foreach ($nationalities as $nationality)
                                 <option value="{{ $nationality->id }}"
                                     {{ isset($student) && $student->nationality_id == $nationality->id ? 'selected' : '' }}>
                                     {{ $nationality->nationality_name }}</option>
                             @endforeach
                         </select>
                     </div>
                     <div class="mb-3">
                         <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                         <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                             value="{{ old('date_of_birth', $student->date_of_birth ?? '') }}" placeholder="YYYY-MM-DD">
                     </div>
                     <div class="mb-3">
                         <label for="religion_id" class="form-label">Religion <span class="text-danger">*</span></label>
                         <select class="form-control select2" id="religion_id" name="religion_id">
                             <option value="">Select Religion</option>
                             @foreach ($religions as $religion)
                                 <option value="{{ $religion->id }}"
                                     {{ isset($student) && $student->religion_id == $religion->id ? 'selected' : '' }}>
                                     {{ $religion->religion_name }}</option>
                             @endforeach
                         </select>
                     </div>

                     <div class="mb-3">
                         <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                         <select class="form-control select2" id="shift_id" name="shift_id">
                             <option value="">Select Shift</option>
                             @foreach ($shifts as $shift)
                                 <option value="{{ $shift->id }}"
                                     {{ isset($student) && $student->shift_id == $shift->id ? 'selected' : '' }}>
                                     {{ $shift->shift_name }}</option>
                             @endforeach
                         </select>
                     </div>
                     <div class="mb-3">
                         <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                         <select class="form-control select2" id="gender" name="gender">
                             <option value="">Select Gender</option>
                             <option value="male"
                                 {{ isset($student) && $student->gender == 'male' ? 'selected' : '' }}>Male</option>
                             <option value="female"
                                 {{ isset($student) && $student->gender == 'female' ? 'selected' : '' }}>Female
                             </option>
                         </select>
                     </div>
                     <div class="mb-3">
                         <label for="technology_id" class="form-label">Technology <span class="text-danger">*</span></label>
                         <select class="form-control select2" id="technology_id" name="technology_id">
                             <option value="">Select Technology</option>
                             @foreach ($technologies as $technology)
                                 <option value="{{ $technology->id }}"
                                     {{ isset($student) && $student->technology_id == $technology->id ? 'selected' : '' }}>
                                     {{ $technology->technology_name }}</option>
                             @endforeach
                         </select>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!-- Educational Information Fieldset -->
     <div class="card mb-6">
         <div class="card-header d-flex align-items-center justify-content-between">
             <h5 class="mb-0">Educational Information</h5>
         </div>
         <div class="card-body">
             <div class="row">
                 <div class="col-md-4">
                     <div class="mb-3">
                         <label for="ssc_or_equivalent_institute_name" class="form-label">SSC Institute Name <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="ssc_or_equivalent_institute_name"
                             name="ssc_or_equivalent_institute_name" placeholder="Enter SSC Institute Name"
                             value="{{ old('ssc_or_equivalent_institute_name', $student->ssc_or_equivalent_institute_name ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="ssc_or_equivalent_institute_address" class="form-label">SSC Institute
                             Address</label>
                         <input type="text" class="form-control" id="ssc_or_equivalent_institute_address"
                             name="ssc_or_equivalent_institute_address" placeholder="Enter SSC Institute Address"
                             value="{{ old('ssc_or_equivalent_institute_address', $student->ssc_or_equivalent_institute_address ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="ssc_or_equivalent_number_potro" class="form-label">SSC Number Potro</label>
                         <input type="text" class="form-control" id="ssc_or_equivalent_number_potro"
                             name="ssc_or_equivalent_number_potro" placeholder="Enter SSC Number Potro"
                             value="{{ old('ssc_or_equivalent_number_potro', $student->ssc_or_equivalent_number_potro ?? '') }}">
                     </div>
                 </div>

                 <div class="col-md-4">
                     <div class="mb-3">
                         <label for="ssc_or_equivalent_roll_number" class="form-label">SSC Roll Number <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="ssc_or_equivalent_roll_number"
                             name="ssc_or_equivalent_roll_number" placeholder="Enter SSC Roll Number"
                             value="{{ old('ssc_or_equivalent_roll_number', $student->ssc_or_equivalent_roll_number ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="ssc_or_equivalent_registration_number" class="form-label">SSC Registration
                             Number <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="ssc_or_equivalent_registration_number"
                             name="ssc_or_equivalent_registration_number" placeholder="Enter SSC Registration Number"
                             value="{{ old('ssc_or_equivalent_registration_number', $student->ssc_or_equivalent_registration_number ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="ssc_or_equivalent_session_id" class="form-label">SSC Session <span class="text-danger">*</span></label>
                         <select type="text" class="form-control select2" id="ssc_or_equivalent_session_id"
                             name="ssc_or_equivalent_session_id">
                             <option disabled selected>Select Session</option>
                             @foreach ($ssc_passing_sessions as $session)
                                 <option value="{{ $session->id }}"
                                     {{ isset($student) && $student->ssc_or_equivalent_session_id == $session->id ? 'selected' : '' }}>
                                     {{ $session->session_name }}</option>
                             @endforeach
                         </select>

                     </div>
                 </div>

                 <div class="col-md-4">
                     <div class="mb-3">
                         <label for="ssc_or_equivalent_passing_year_id" class="form-label">SSC Passing Year <span class="text-danger">*</span></label>
                         <select type="text" class="form-control select2" id="ssc_or_equivalent_passing_year_id"
                             name="ssc_or_equivalent_passing_year_id")">
                             <option disabled selected>Select Passing Year</option>
                             @foreach ($ssc_passing_years as $year)
                                 <option value="{{ $year->id }}"
                                     {{ isset($student) && $student->ssc_or_equivalent_passing_year_id == $year->id ? 'selected' : '' }}>
                                     {{ $year->passing_year_name }}</option>
                             @endforeach
                         </select>
                     </div>
                     <div class="mb-3">
                         <label for="ssc_or_equivalent_gpa" class="form-label">SSC GPA <span class="text-danger">*</span></label>
                         <input type="number" class="form-control" id="ssc_or_equivalent_gpa"
                             name="ssc_or_equivalent_gpa" placeholder="Enter SSC GPA"
                             value="{{ old('ssc_or_equivalent_gpa', $student->ssc_or_equivalent_gpa ?? '') }}">
                     </div>
                     <div class="mb-3">
                         <label for="board_id" class="form-label">Board <span class="text-danger">*</span></label>
                         <select class="form-control select2" id="board_id" name="board_id">
                             <option value="">Select Board</option>
                             @foreach ($boards as $board)
                                 <option value="{{ $board->id }}"
                                     {{ isset($student) && $student->board_id == $board->id ? 'selected' : '' }}>
                                     {{ $board->board_name }}</option>
                             @endforeach
                         </select>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>

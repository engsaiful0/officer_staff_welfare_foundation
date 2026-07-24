@php
    $member = $member ?? null;
    $fmt = function ($value) {
        if (is_object($value) && method_exists($value, 'format')) {
            return $value->format('Y-m-d');
        }

        return $value ?? '';
    };
@endphp

{{-- First Guarantor --}}
<div class="col-12 mt-4">
    <h6 class="fw-semibold">Guarantor Information — First Guarantor</h6>
</div>

<div class="col-md-4 mb-3">
    <label for="first_guarantor_name" class="form-label">Name of Guarantor</label>
    <input type="text" class="form-control" id="first_guarantor_name" name="first_guarantor_name"
        placeholder="Name of Guarantor" value="{{ old('first_guarantor_name', $member->first_guarantor_name ?? '') }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="first_guarantor_employees_id" class="form-label">Employee ID</label>
    <input type="text" class="form-control" id="first_guarantor_employees_id" name="first_guarantor_employees_id"
        placeholder="Employee ID" value="{{ old('first_guarantor_employees_id', $member->first_guarantor_employees_id ?? '') }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="first_guarantor_designation_id" class="form-label">Designation</label>
    <select class="form-select select2" id="first_guarantor_designation_id" name="first_guarantor_designation_id">
        <option value="">Select Designation</option>
        @foreach($designations as $designation)
            <option value="{{ $designation->id }}"
                {{ (string) old('first_guarantor_designation_id', $member->first_guarantor_designation_id ?? '') === (string) $designation->id ? 'selected' : '' }}>
                {{ $designation->designation_name }}
            </option>
        @endforeach
    </select>
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="first_guarantor_branch_name" class="form-label">Branch Name</label>
    <input type="text" class="form-control" id="first_guarantor_branch_name" name="first_guarantor_branch_name"
        placeholder="Branch Name" value="{{ old('first_guarantor_branch_name', $member->first_guarantor_branch_name ?? '') }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="first_guarantor_date_of_birth" class="form-label">Birth Date</label>
    <input type="date" class="form-control" id="first_guarantor_date_of_birth" name="first_guarantor_date_of_birth"
        value="{{ old('first_guarantor_date_of_birth', $fmt($member->first_guarantor_date_of_birth ?? null)) }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="first_guarantor_date_of_joining" class="form-label">Date of Joining the Bank</label>
    <input type="date" class="form-control" id="first_guarantor_date_of_joining" name="first_guarantor_date_of_joining"
        value="{{ old('first_guarantor_date_of_joining', $fmt($member->first_guarantor_date_of_joining ?? null)) }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="first_guarantor_mobile" class="form-label">Mobile</label>
    <input type="text" class="form-control" id="first_guarantor_mobile" name="first_guarantor_mobile"
        placeholder="Mobile" value="{{ old('first_guarantor_mobile', $member->first_guarantor_mobile ?? '') }}">
    <div class="invalid-feedback"></div>
</div>

{{-- Second Guarantor --}}
<div class="col-12 mt-4">
    <h6 class="fw-semibold">Guarantor Information — Second Guarantor</h6>
</div>

<div class="col-md-4 mb-3">
    <label for="second_guarantor_name" class="form-label">Name of Guarantor</label>
    <input type="text" class="form-control" id="second_guarantor_name" name="second_guarantor_name"
        placeholder="Name of Guarantor" value="{{ old('second_guarantor_name', $member->second_guarantor_name ?? '') }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="second_guarantor_employees_id" class="form-label">Employee ID</label>
    <input type="text" class="form-control" id="second_guarantor_employees_id" name="second_guarantor_employees_id"
        placeholder="Employee ID" value="{{ old('second_guarantor_employees_id', $member->second_guarantor_employees_id ?? '') }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="second_guarantor_designation_id" class="form-label">Designation</label>
    <select class="form-select select2" id="second_guarantor_designation_id" name="second_guarantor_designation_id">
        <option value="">Select Designation</option>
        @foreach($designations as $designation)
            <option value="{{ $designation->id }}"
                {{ (string) old('second_guarantor_designation_id', $member->second_guarantor_designation_id ?? '') === (string) $designation->id ? 'selected' : '' }}>
                {{ $designation->designation_name }}
            </option>
        @endforeach
    </select>
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="second_guarantor_branch_name" class="form-label">Branch Name</label>
    <input type="text" class="form-control" id="second_guarantor_branch_name" name="second_guarantor_branch_name"
        placeholder="Branch Name" value="{{ old('second_guarantor_branch_name', $member->second_guarantor_branch_name ?? '') }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="second_guarantor_date_of_birth" class="form-label">Birth Date</label>
    <input type="date" class="form-control" id="second_guarantor_date_of_birth" name="second_guarantor_date_of_birth"
        value="{{ old('second_guarantor_date_of_birth', $fmt($member->second_guarantor_date_of_birth ?? null)) }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="second_guarantor_date_of_joining" class="form-label">Date of Joining the Bank</label>
    <input type="date" class="form-control" id="second_guarantor_date_of_joining" name="second_guarantor_date_of_joining"
        value="{{ old('second_guarantor_date_of_joining', $fmt($member->second_guarantor_date_of_joining ?? null)) }}">
    <div class="invalid-feedback"></div>
</div>
<div class="col-md-4 mb-3">
    <label for="second_guarantor_mobile" class="form-label">Mobile</label>
    <input type="text" class="form-control" id="second_guarantor_mobile" name="second_guarantor_mobile"
        placeholder="Mobile" value="{{ old('second_guarantor_mobile', $member->second_guarantor_mobile ?? '') }}">
    <div class="invalid-feedback"></div>
</div>

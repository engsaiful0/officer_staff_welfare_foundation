@extends('layouts/layoutMaster')

@section('title', 'Edit Fee Collection')

<!-- Vendor Styles -->
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/collect-fee.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('.datepicker', {
                dateFormat: 'Y-m-d'
            });
            $('.select2').select2();

            // Initial check for paid fee heads on page load
            checkPaidFeeHeadsOnEdit({{ $feeCollect->student_id }}, {{ $feeCollect->academic_year_id }},
                {{ $feeCollect->semester_id }});
        });
    </script>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Fee Collection</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('app-collect-fee.update', $feeCollect->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-3">
                        <label for="academic_year_id" class="form-label">Academic Year</label>
                        <select id="academic_year_id" name="academic_year_id" class="form-select select2">
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}"
                                    {{ $feeCollect->academic_year_id == $year->id ? 'selected' : '' }}>
                                    {{ $year->academic_year_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                        <select id="year" name="year" class="form-select select2" required>
                            @foreach ($years as $yearOption)
                                <option value="{{ $yearOption }}" {{ $yearOption == ($feeCollect->year ?? $currentYear) ? 'selected' : '' }}>
                                    {{ $yearOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="semester_id" class="form-label">Semester</label>
                        <select id="semester_id" name="semester_id" class="form-select select2">
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}"
                                    {{ $feeCollect->semester_id == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->semester_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select id="student_id" name="student_id" class="form-select select2">
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ $feeCollect->student_id == $student->id ? 'selected' : '' }}>
                                    {{ $student->full_name_in_english_block_letter }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" value="{{ $feeCollect->date }}" class="form-control" id="date"
                                name="date" required>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Fee Heads</h6>
                        <div class="row">
                            <table class="table table-bordered" id="fee_heads_table">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Fee Head</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($fee_heads as $fee_head)
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="checkbox" name="fee_heads[]"
                                                    value="{{ $fee_head->id }}" id="fee_head_{{ $fee_head->id }}"
                                                    {{ in_array($fee_head->id, json_decode($feeCollect->fee_heads)) ? 'checked' : '' }}>
                                            </td>
                                            <td id="fee_head_name_{{ $fee_head->id }}">
                                                {{ $fee_head->name }}
                                            </td>
                                            <td id="fee_head_amount_{{ $fee_head->id }}">
                                                {{ $fee_head->amount }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="discount" class="form-label">Discount</label>
                        <input type="number" id="discount" name="discount" class="form-control"
                            value="{{ $feeCollect->discount }}">
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="total_payable" class="form-label">Total Payable</label>
                            <input value="{{ $feeCollect->total_payable }}" type="text" class="form-control" id="total_payable" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="payment_method_id" class="form-label">Payment Method</label>
                        <select id="payment_method_id" name="payment_method_id" class="form-select select2">
                            @foreach ($payment_methods as $method)
                                <option value="{{ $method->id }}"
                                    {{ $feeCollect->payment_method_id == $method->id ? 'selected' : '' }}>
                                    {{ $method->payment_method_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('app-collect-fee.view-collect-fee') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

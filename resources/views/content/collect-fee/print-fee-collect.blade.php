@extends('layouts/layoutMaster')

@section('title', 'Collect Fee')

@section('content')
    <form id="collect-fee-form" action="{{ route('app-collect-fee.store') }}" method="POST">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Collect Fee</h5>
                    </div>
                    <div class="card-body">

                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="academic_year_id" class="form-label">Academic Year</label>
                                    <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->academic_year_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="fee_type" class="form-label">Fee Type</label>
                                    <select class="form-select" id="fee_type" name="fee_type" required>
                                        <option value="" disabled selected>Select Fee Type</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Monthly">Monthly</option>
                                        <option value="Both">Both</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="semester_id" class="form-label">Semester</label>
                                    <select class="form-select" id="semester_id" name="semester_id" required>
                                        <option value="">Select Semester</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}">{{ $semester->semester_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student</label>
                                    <select class="form-select" id="student_id" name="student_id" required>
                                        <option value="">Select Student</option>
                                    </select>
                                    <!-- Loading Spinner -->
                                    <div id="student-loading-spinner" class="spinner-border spinner-border-sm d-none"
                                        role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span id="student_id_error" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Fee Heads</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table" id="fee_heads_table">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="select_all_fees"></th>
                                                    <th>Fee Head</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Fee heads will be populated by JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Discount and Total Section -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="discount" class="form-label">Discount</label>
                                    <input type="number" class="form-control" id="discount" name="discount"
                                        placeholder="Enter discount amount">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_payable" class="form-label">Total Payable</label>
                                    <input type="text" class="form-control" id="total_payable" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_method_id" class="form-label">Payment Method</label>
                                    <select  class="form-control" id="payment_method_id" name="payment_method_id">
                                        <option value="">Select Payment Method</option>
                                        @foreach ($payment_methods as $method)
                                            <option value="{{ $method->id }}">{{ $method->payment_method_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>

                        <!-- Submit Button -->
                        <button id="submit-button" type="submit" class="btn btn-primary">
                            <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <span id="button-text">Submit</span>
                        </button>


                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Heads Section -->

    </form>


@endsection

@section('page-script')
    <script src="{{ asset('assets/js/collect-fee.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Handle Submit Button Click
            $('#submit-button').click(function(e) {
                e.preventDefault(); // Prevent default button action
                var formData = new FormData($('#collect-fee-form')[0]); // Get form data
                var form = $('#collect-fee-form');
                //validation
                var studentId = $('#student_id').val();
                if (!studentId) {
                    $('#student_id_error').text('Please select a student.');
                    return;
                } else {
                    $('#student_id_error').text('');
                }
                // Disable form elements and show the loading spinner
                form.find('input, select, button').prop('disabled', true);
                var spinner = $('#spinner');
                var buttonText = $('#button-text');
                var submitButton = $('#submit-button');
                submitButton.prop('disabled', true);
                buttonText.text('Submitting...');
                spinner.removeClass('d-none'); // Show spinner
                // Make AJAX request
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle success
                        toastr.success("Data has been saved successfully.");
                        window.location.href =
                            "{{ route('app-collect-fee.create') }}"; // Redirect on success
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON;
                            if (errors.error) {
                                toastr.error(errors.error);
                            } else {
                                toastr.error("An error occurred. Please try again.");
                            }
                        } else {
                            toastr.error("An error occurred. Please try again.");
                        }
                    },
                    complete: function() {
                        // Re-enable the form and reset spinner
                        form.find('input, select, button').prop('disabled', false);
                        $(form).find(':input').prop('disabled', false); // Re-enable form fields
                        spinner.addClass('d-none'); // Hide spinner
                        buttonText.text('Submit'); // Reset button text
                        submitButton.prop('disabled', false); // Re-enable submit button
                    }
                });
            });
        });
    </script>
@endsection

@extends('layouts/layoutMaster')

@section('title', 'Collect Fee')

<!-- Vendor Styles -->
@section('page-style')
    <style>
        .paid-fee {
            color: gray;
            text-decoration: line-through;
        }

        .text-danger {
            color: red;
        }
        
        .student-loading {
            position: relative;
        }
        
        .student-loading .form-select {
            padding-right: 40px;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        
        .form-text {
            margin-top: 5px;
        }
        
        .select2-container--disabled .select2-selection--single {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
    </style>
@endsection


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
                                    <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="academic_year_id" name="academic_year_id" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->academic_year_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="semester_id" name="semester_id" required>
                                        <option value="">Select Semester</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}">{{ $semester->semester_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <select class="form-select select2" id="student_id" name="student_id" required>
                                            <option value="">Select Student</option>
                                        </select>
                                        <!-- Loading Spinner -->
                                        <div id="student-loading-spinner" class="position-absolute top-50 end-0 translate-middle-y me-3 d-none" role="status">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading students...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Select Academic Year and Semester first to load students
                                        </small>
                                    </div>
                                    <span id="student_id_error" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="year" name="year" required>
                                        @foreach ($years as $yearOption)
                                            <option value="{{ $yearOption }}" {{ $yearOption == $currentYear ? 'selected' : '' }}>
                                                {{ $yearOption }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="fee_type" class="form-label">Fee Type</label>
                                    <select class="form-select select2" id="fee_type" name="fee_type" required>
                                        <option value="" disabled selected>Select Fee Type</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Monthly">Monthly</option>
                                        <option value="Both">Both</option>
                                    </select>
                                </div>
                            </div>
                           
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="months_section" style="display: none;">
                                <div class="mb-3">
                                    <label for="months" class="form-label">Months</label>
                                    <select class="form-select select2" id="months" name="months[]" multiple>
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
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
                                                    <th id="fine_column_header" style="display: none;">Fine Amount</th>
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
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="fine_amount" class="form-label">Total Fine Amount</label>
                                    <input type="text" class="form-control text-danger fw-bold" name="fine_amount" id="fine_amount" readonly>
                                    <small class="form-text text-muted">Overdue fine for monthly fees</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="discount" class="form-label">Discount</label>
                                    <input type="number" class="form-control" name="discount" id="discount"
                                        placeholder="Enter discount amount">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="total_payable" class="form-label">Total Payable</label>
                                    <input type="text" class="form-control fw-bold" name="total_payable" id="total_payable" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="net_payable" class="form-label">Net Payable</label>
                                    <input type="text" name="net_payable" class="form-control fw-bold text-success" id="net_payable" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_method_id" class="form-label">Payment Method</label>
                                    <select required class="form-control select2" id="payment_method_id"
                                        name="payment_method_id">
                                        <option value="">Select Payment Method</option>
                                        @foreach ($payment_methods as $method)
                                            <option value="{{ $method->id }}">{{ $method->payment_method_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span id="payment_method_id_error" class="text-danger"></span>
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
    <script>
        // Inject Laravel route URLs as global variables
        window.getStudentsUrl = '{{ route("app-collect-fee.get-students", [":academic_year_id", ":semester_id"]) }}';
        window.getFeesUrl = '{{ route("app-collect-fee.get-fees", [":semester_id", ":fee_type"]) }}';
        window.getPaidFeeHeadsUrl = '{{ route("app-collect-fee.get-paid-fee-heads", [":student_id", ":academic_year_id", ":semester_id"]) }}';
        window.checkPaidStatusUrl = '{{ route("app-collect-fee.check-paid-status") }}';
    </script>
    <script src="{{ asset('assets/js/collect-fee.js') }}?v={{ time() }}"></script>
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

                var paymentMethodId = $('#payment_method_id').val();
                if (!paymentMethodId) {
                    $('#payment_method_id_error').text('Please select a payment method.');
                    return;
                } else {
                    $('#payment_method_id_error').text('');
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
                        // Redirect to the receipt page
                        window.location.href = "{{ route('app-collect-fee.receipt', '') }}/" + response
                            .fee_collect_id;
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

@extends('layouts/layoutMaster')

@section('title', 'Branch Settings')

@section('page-script')
    <script>
        window.branchUrls = AppUtils.buildApiUrls('app/settings/branch');
        console.log('Branch URLs:', window.branchUrls);
    </script>
    <script src="{{ asset('assets/js/branch-datatables.js') }}?v={{ time() }}"></script>
@endsection

@section('content')
    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Branch Name</th>
                        <th>Branch Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="add-new-record">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">New Branch</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="add-new-record pt-0 row g-2" id="form-add-new-record" onsubmit="return false">
                <div class="col-sm-12">
                    <label class="form-label" for="branch_name">Branch Name</label>
                    <div class="input-group input-group-merge">
                        <span id="branch_name2" class="input-group-text"><i class="ti ti-building"></i></span>
                        <input type="text" id="branch_name" class="form-control dt-full-name"
                            name="branch_name" placeholder="Enter branch name"
                            aria-label="Enter branch name" aria-describedby="branch_name2" />
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="branch_address">Branch Address</label>
                    <div class="input-group input-group-merge">
                        <span id="branch_address2" class="input-group-text"><i class="ti ti-map-pin"></i></span>
                        <textarea id="branch_address" name="branch_address" class="form-control" 
                            placeholder="Enter branch address" rows="3" 
                            aria-label="Enter branch address" aria-describedby="branch_address2"></textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1" id="submit-btn">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="submit-spinner" role="status" aria-hidden="true"></span>
                        <span id="submit-text">Submit</span>
                    </button>
                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <!--/ DataTable with Buttons -->

@endsection

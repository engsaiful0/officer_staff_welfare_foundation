@extends('layouts/layoutMaster')

@section('title', 'Zone Settings')

@section('page-script')
    <script>
        window.zoneUrls = {
            getData: '{{ url("app/settings/get-zone") }}',
            store: '{{ url("app/settings/zone") }}',
            update: '{{ url("app/settings/zone") }}',
            destroy: '{{ url("app/settings/zone") }}'
        };
        if (typeof AppUtils !== 'undefined' && AppUtils.buildApiUrls) {
            var fallback = AppUtils.buildApiUrls('app/settings/zone');
            if (!window.zoneUrls.getData) window.zoneUrls.getData = fallback.getData;
            if (!window.zoneUrls.store) window.zoneUrls.store = fallback.store;
            if (!window.zoneUrls.update) window.zoneUrls.update = fallback.update;
            if (!window.zoneUrls.destroy) window.zoneUrls.destroy = fallback.destroy;
        }
    </script>
    <script src="{{ asset('assets/js/zone-datatables.js') }}?v={{ time() }}"></script>
@endsection

@section('content')
    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Zone Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="add-new-record">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">New Zone</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="add-new-record pt-0 row g-2" id="form-add-new-record" onsubmit="return false">
                <div class="col-sm-12">
                    <label class="form-label" for="zone_name">Zone Name</label>
                    <div class="input-group input-group-merge">
                        <span id="zone_name2" class="input-group-text"><i class="ti ti-map-2"></i></span>
                        <input type="text" id="zone_name" class="form-control dt-full-name"
                            name="zone_name" placeholder="Enter zone name"
                            aria-label="Enter zone name" aria-describedby="zone_name2" />
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

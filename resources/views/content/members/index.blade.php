@extends('layouts/layoutMaster')

@section('title', 'Member List')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Member List</h5>
                <div class="d-flex gap-2">
                    @can('member-add')
                    <a href="{{ route('members.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Add Member
                    </a>
                    @endcan
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bx bx-export me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('members.export-excel', request()->query()) }}">
                                <i class="bx bx-file me-2"></i>Export to Excel
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('members.export-pdf', request()->query()) }}">
                                <i class="bx bx-file-pdf me-2"></i>Export to PDF
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card-body border-bottom">
                <form method="GET" action="{{ route('members.view-member') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by name, email, mobile, or ID">
                    </div>
                    <div class="col-md-2">
                        <label for="mobile" class="form-label">Mobile</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" 
                               value="{{ request('mobile') }}" placeholder="Mobile number">
                    </div>
                    <div class="col-md-2">
                        <label for="designation_id" class="form-label">Designation</label>
                        <select class="form-select" id="designation_id" name="designation_id">
                            <option value="">All Designations</option>
                            @foreach($designations as $designation)
                                <option value="{{ $designation->id }}" {{ request('designation_id') == $designation->id ? 'selected' : '' }}>
                                    {{ $designation->designation_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-select" id="branch_id" name="branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="date_of_join" {{ request('sort_by') == 'date_of_join' ? 'selected' : '' }}>Join Date</option>
                            <option value="mobile" {{ request('sort_by') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label for="sort_order" class="form-label">Order</label>
                        <select class="form-select" id="sort_order" name="sort_order">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Desc</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Asc</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('members.view-member') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-refresh me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Results Summary -->
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0 text-muted">
                        Showing {{ $members->firstItem() ?? 0 }} to {{ $members->lastItem() ?? 0 }} 
                        of {{ $members->total() }} members
                    </p>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2">Per page:</span>
                        <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Members Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>IBBL Employee ID</th>
                            <th>Member ID</th>
                            <th>Designation</th>
                            <th>Branch</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Join Date</th>
                            <th>View</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        <tr>
                            <td>{{ $member->id }}</td>
                            <td>
                                @if($member->picture)
                                    <img src="{{ asset('storage/app/public/' . $member->picture) }}" 
                                         alt="{{ $member->name }}" 
                                         class="rounded-circle" 
                                         width="40" height="40">
                                @else
                                    <div class="avatar-initial rounded-circle bg-label-primary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        {{ substr($member->name, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0">{{ $member->name }}</h6>
                                    
                                </div>
                            </td>
                            <td>
                            {{ $member->employees_id }}
                            </td>
                            <td>
                               {{ $member->member_unique_id }}
                            </td>
                            <td>{{ $member->designation ? $member->designation->designation_name : 'N/A' }}</td>
                            <td>{{ $member->branch ? $member->branch->branch_name : 'N/A' }}</td>
                            <td>{{ $member->email }}</td>
                            <td>{{ $member->mobile }}</td>
                            <td>{{ $member->date_of_join ? $member->date_of_join->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a class="btn btn-sm btn-text-secondary rounded-pill btn-icon member-edit" href="{{ route('members.show', $member) }}">
                                    <i class="ti ti-eye ti-md"></i>
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-text-secondary rounded-pill btn-icon member-edit" href="{{ route('members.edit', $member) }}">
                                    <i class="ti ti-pencil ti-md"></i>
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-text-secondary rounded-pill btn-icon delete-member" href="#" 
                                   onclick="deleteMember({{ $member->id }}, '{{ $member->name }}')">
                                    <i class="ti ti-trash ti-md"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-user-x display-4 text-muted mb-2"></i>
                                    <h6 class="text-muted">No members found</h6>
                                    <p class="text-muted">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($members->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        {{ $members->links() }}
                    </div>
                    <div class="text-muted">
                        Page {{ $members->currentPage() }} of {{ $members->lastPage() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete member <strong id="memberName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script>
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    window.location.href = url.toString();
}

function deleteMember(memberId, memberName) {
    document.getElementById('memberName').textContent = memberName;
    document.getElementById('deleteForm').action = `/members/${memberId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('#search, #mobile, #designation_id, #branch_id');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endsection

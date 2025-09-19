$(document).ready(function() {
    console.log('Member management script loaded');
    console.log('Member AJAX URL:', window.memberAjaxUrl);
    
    // Check if table element exists
    var tableElement = $('#member-datatable');
    console.log('Table element found:', tableElement.length);
    console.log('Table element:', tableElement);
    
    if (tableElement.length === 0) {
        console.error('Table element #member-datatable not found!');
        return;
    }
    
    // Initialize DataTable
    var table = $('#member-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.memberAjaxUrl,
            type: 'GET',
            success: function(data) {
                console.log('DataTable AJAX success:', data);
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX error:', error, thrown);
                console.error('Response:', xhr.responseText);
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { 
                data: 'picture', 
                name: 'picture',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    if (data) {
                        return '<img src="' + window.location.origin + '/storage/' + data + '" alt="Member Photo" class="rounded-circle" width="40" height="40">';
                    } else {
                        return '<div class="avatar-initial rounded-circle bg-label-secondary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><span>' + row.name.charAt(0).toUpperCase() + '</span></div>';
                    }
                }
            },
            { 
                data: 'unique_id', 
                name: 'unique_id',
                render: function(data, type, row) {
                    return '<span class="badge bg-primary">' + data + '</span>';
                }
            },
            { 
                data: 'designation', 
                name: 'designation_id',
                render: function(data, type, row) {
                    return data ? data.designation_name : '-';
                }
            },
            { 
                data: 'branch', 
                name: 'branch_id',
                render: function(data, type, row) {
                    return data ? data.branch_name : '-';
                }
            },
            { data: 'email', name: 'email' },
            { data: 'mobile', name: 'mobile' },
            { 
                data: 'date_of_join', 
                name: 'date_of_join',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString() : '-';
                }
            },
            { 
                data: 'id', 
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var actions = '<div class="dropdown">';
                    actions += '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">';
                    actions += '<i class="bx bx-dots-vertical-rounded"></i>';
                    actions += '</button>';
                    actions += '<div class="dropdown-menu">';
                    
                    // View action
                    actions += '<a class="dropdown-item" href="javascript:void(0);" onclick="viewMember(' + data + ')">';
                    actions += '<i class="bx bx-show me-1"></i> View';
                    actions += '</a>';
                    
                    // Edit action
                    actions += '<a class="dropdown-item" href="' + window.location.origin + '/members/' + data + '/edit">';
                    actions += '<i class="bx bx-edit-alt me-1"></i> Edit';
                    actions += '</a>';
                    
                    // Delete action
                    actions += '<a class="dropdown-item" href="javascript:void(0);" onclick="deleteMember(' + data + ')">';
                    actions += '<i class="bx bx-trash me-1"></i> Delete';
                    actions += '</a>';
                    
                    actions += '</div>';
                    actions += '</div>';
                    
                    return actions;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true,
        language: {
            processing: "Loading members...",
            emptyTable: "No members found",
            zeroRecords: "No matching members found"
        },
        initComplete: function(settings, json) {
            console.log('DataTable initialization complete');
            console.log('Data received:', json);
        }
    });
    
    console.log('DataTable initialized:', table);

    // View member details
    window.viewMember = function(id) {
        // Show loading spinner in modal
        $('#memberDetailsContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        $('#memberDetailsModal').modal('show');
        
        $.ajax({
            url: window.location.origin + '/members/' + id,
            type: 'GET',
            success: function(response) {
                var member = response.member;
                var content = '<div class="row">';
                
                // Personal Information
                content += '<div class="col-12"><h6 class="fw-semibold mb-3">Personal Information</h6></div>';
                content += '<div class="col-md-6 mb-3"><strong>Name:</strong> ' + member.name + '</div>';
                content += '<div class="col-md-6 mb-3"><strong>Father Name:</strong> ' + member.father_name + '</div>';
                content += '<div class="col-md-6 mb-3"><strong>Mobile:</strong> ' + member.mobile + '</div>';
                content += '<div class="col-md-6 mb-3"><strong>Email:</strong> ' + member.email + '</div>';
                content += '<div class="col-md-6 mb-3"><strong>NID Number:</strong> ' + member.nid_number + '</div>';
                content += '<div class="col-md-6 mb-3"><strong>Religion:</strong> ' + (member.religion ? member.religion.name : '-') + '</div>';
                
                // Professional Information
                content += '<div class="col-12 mt-4"><h6 class="fw-semibold mb-3">Professional Information</h6></div>';
                content += '<div class="col-md-6 mb-3"><strong>Unique ID:</strong> <span class="badge bg-primary">' + member.unique_id + '</span></div>';
                content += '<div class="col-md-6 mb-3"><strong>Designation:</strong> ' + (member.designation ? member.designation.name : '-') + '</div>';
                content += '<div class="col-md-6 mb-3"><strong>Branch:</strong> ' + (member.branch ? member.branch.name : '-') + '</div>';
                content += '<div class="col-md-6 mb-3"><strong>Date of Join:</strong> ' + (member.date_of_join ? new Date(member.date_of_join).toLocaleDateString() : '-') + '</div>';
                
                // Address Information
                content += '<div class="col-12 mt-4"><h6 class="fw-semibold mb-3">Address Information</h6></div>';
                content += '<div class="col-12 mb-3"><strong>Present Address:</strong><br>' + member.present_address + '</div>';
                content += '<div class="col-12 mb-3"><strong>Permanent Address:</strong><br>' + member.permanent_address + '</div>';
                
                // Introducer Information
                if (member.introducer) {
                    content += '<div class="col-12 mt-4"><h6 class="fw-semibold mb-3">Introducer Information</h6></div>';
                    content += '<div class="col-12 mb-3"><strong>Introducer:</strong> ' + member.introducer.name + ' (' + member.introducer.unique_id + ')</div>';
                }
                
                // Nominee Information
                if (member.nominee_name) {
                    content += '<div class="col-12 mt-4"><h6 class="fw-semibold mb-3">Nominee Information</h6></div>';
                    content += '<div class="col-md-4 mb-3"><strong>Nominee Name:</strong> ' + member.nominee_name + '</div>';
                    content += '<div class="col-md-4 mb-3"><strong>Relation:</strong> ' + (member.nominee_relation ? member.nominee_relation.relation_name : '-') + '</div>';
                    content += '<div class="col-md-4 mb-3"><strong>Phone:</strong> ' + (member.nominee_phone || '-') + '</div>';
                }
                
                // Account Information
                content += '<div class="col-12 mt-4"><h6 class="fw-semibold mb-3">Account Information</h6></div>';
                content += '<div class="col-md-4 mb-3"><strong>Username:</strong> ' + member.temp_username + '</div>';
                content += '<div class="col-md-4 mb-3"><strong>Password:</strong> ' + member.temp_password + '</div>';
                content += '<div class="col-md-4 mb-3"><strong>Status:</strong> ' + (member.user ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-warning">Inactive</span>') + '</div>';
                
                content += '</div>';
                
                $('#memberDetailsContent').html(content);
                $('#memberDetailsModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to load member details', 'error');
            }
        });
    };

    // Delete member
    window.deleteMember = function(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the member',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: window.location.origin + '/members/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to delete member', 'error');
                    }
                });
            }
        });
    };
});

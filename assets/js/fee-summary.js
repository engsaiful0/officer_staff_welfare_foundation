'use strict';

$(function () {
  console.log('Fee summary JavaScript loaded');

  // Initialize tooltips
  $('[data-bs-toggle="tooltip"]').tooltip();

  // Real-time search functionality
  $('#studentSearch').on('keyup', function() {
    var searchTerm = $(this).val().toLowerCase();
    $('#feeSummaryTable tbody tr').each(function() {
      var studentId = $(this).find('td:nth-child(2)').text().toLowerCase();
      var studentName = $(this).find('td:nth-child(3)').text().toLowerCase();
      
      if (studentId.includes(searchTerm) || studentName.includes(searchTerm)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  // Status filter functionality
  $('#statusFilter').on('change', function() {
    var selectedStatus = $(this).val();
    $('#feeSummaryTable tbody tr').each(function() {
      var statusBadge = $(this).find('.badge');
      
      if (selectedStatus === '' || statusBadge.hasClass('bg-' + getStatusColor(selectedStatus))) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  function getStatusColor(status) {
    switch(status) {
      case 'complete': return 'success';
      case 'partial': return 'warning';
      case 'none': return 'danger';
      default: return '';
    }
  }

  // Update fee summary for individual student
  $('.update-summary').on('click', function(e) {
    e.preventDefault();
    
    var studentId = $(this).data('student-id');
    var academicYearId = $(this).data('academic-year-id');
    var button = $(this);
    var originalText = button.html();
    
    button.html('<span class="spinner-border spinner-border-sm me-1"></span>Updating...');
    button.prop('disabled', true);
    
    $.post(`/app/fee-summary/student/${studentId}/update`, {
      academic_year_id: academicYearId,
      _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
      if (response.success) {
        toastr.success('Fee summary updated successfully!');
        setTimeout(function() {
          window.location.reload();
        }, 1000);
      } else {
        toastr.error('Failed to update fee summary');
      }
    })
    .fail(function(xhr) {
      console.error('Error updating fee summary:', xhr);
      toastr.error('Error updating fee summary');
    })
    .always(function() {
      button.html(originalText);
      button.prop('disabled', false);
    });
  });

  // Progress bar animation
  $('.progress-bar').each(function() {
    var percentage = $(this).attr('aria-valuenow');
    $(this).animate({
      width: percentage + '%'
    }, 1500);
  });

  // Fee breakdown modal functionality
  $('.view-breakdown').on('click', function(e) {
    e.preventDefault();
    
    var studentId = $(this).data('student-id');
    var academicYearId = $(this).data('academic-year-id');
    
    $.get(`/app/fee-summary/student/${studentId}/breakdown`, {
      academic_year_id: academicYearId
    })
    .done(function(response) {
      // Populate modal with breakdown data
      populateBreakdownModal(response);
      $('#feeBreakdownModal').modal('show');
    })
    .fail(function(xhr) {
      console.error('Error fetching fee breakdown:', xhr);
      toastr.error('Failed to load fee breakdown');
    });
  });

  function populateBreakdownModal(data) {
    var modal = $('#feeBreakdownModal');
    var student = data.student;
    var feeSummary = data.fee_summary;
    
    modal.find('.modal-title').text(`Fee Breakdown - ${student.student_unique_id}`);
    modal.find('#student-name').text(student.full_name_in_english_block_letter);
    modal.find('#total-expected').text(formatCurrency(feeSummary.total_fees));
    modal.find('#total-paid').text(formatCurrency(feeSummary.total_paid));
    modal.find('#total-due').text(formatCurrency(feeSummary.total_due));
    modal.find('#completion-percent').text(feeSummary.completion_percentage + '%');
    
    // Update progress bar
    var progressBar = modal.find('.progress-bar');
    progressBar.css('width', feeSummary.completion_percentage + '%');
    progressBar.text(feeSummary.completion_percentage + '%');
    
    // Populate semester fees table
    populateFeesTable('#semesterFeesTable', data.semester_fees);
    
    // Populate monthly fees table
    populateFeesTable('#monthlyFeesTable', data.monthly_fees);
  }

  function populateFeesTable(tableSelector, fees) {
    var tbody = $(tableSelector + ' tbody');
    tbody.empty();
    
    fees.forEach(function(fee, index) {
      var statusBadge = fee.is_paid 
        ? '<span class="badge bg-success">Paid</span>'
        : '<span class="badge bg-danger">Unpaid</span>';
      
      var row = `
        <tr>
          <td>${index + 1}</td>
          <td>${fee.semester ? fee.semester.semester_name : (fee.month ? fee.month.month_name : 'N/A')}</td>
          <td class="text-end">${formatCurrency(fee.amount)}</td>
          <td>${fee.payment_date ? formatDate(fee.payment_date) : 'N/A'}</td>
          <td>${statusBadge}</td>
        </tr>
      `;
      tbody.append(row);
    });
  }

  function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(amount);
  }

  function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US');
  }

  // Export functionality
  $('.export-btn').on('click', function(e) {
    e.preventDefault();
    
    var exportType = $(this).data('export-type');
    var academicYearId = $('#academic_year_id').val();
    var button = $(this);
    var originalText = button.html();
    
    button.html('<span class="spinner-border spinner-border-sm me-1"></span>Exporting...');
    button.prop('disabled', true);
    
    var url = exportType === 'excel' 
      ? '/app/fee-summary/export-all-excel'
      : '/app/fee-summary/export-all-pdf';
    
    if (academicYearId) {
      url += '?academic_year_id=' + academicYearId;
    }
    
    window.open(url, '_blank');
    
    setTimeout(function() {
      button.html(originalText);
      button.prop('disabled', false);
    }, 2000);
  });

  // Print individual report
  $('.print-report').on('click', function(e) {
    e.preventDefault();
    
    var url = $(this).attr('href');
    var printWindow = window.open(url, '_blank');
    
    printWindow.onload = function() {
      printWindow.print();
    };
  });

  // Fee collection statistics refresh
  $('#refreshStats').on('click', function(e) {
    e.preventDefault();
    
    var academicYearId = $('#academic_year_id').val();
    var button = $(this);
    var originalText = button.html();
    
    button.html('<span class="spinner-border spinner-border-sm"></span>');
    button.prop('disabled', true);
    
    $.get('/app/fee-summary/stats', {
      academic_year_id: academicYearId
    })
    .done(function(stats) {
      // Update statistics cards
      updateStatisticsCards(stats);
      toastr.success('Statistics refreshed successfully!');
    })
    .fail(function(xhr) {
      console.error('Error refreshing stats:', xhr);
      toastr.error('Failed to refresh statistics');
    })
    .always(function() {
      button.html(originalText);
      button.prop('disabled', false);
    });
  });

  function updateStatisticsCards(stats) {
    $('#totalStudents').text(stats.total_students);
    $('#studentsComplete').text(stats.students_with_complete_fees);
    $('#studentsPartial').text(stats.students_with_partial_fees);
    $('#collectionPercentage').text(stats.collection_percentage + '%');
  }

  // Enhanced table sorting
  $('.sortable').on('click', function() {
    var column = $(this).data('column');
    var order = $(this).hasClass('asc') ? 'desc' : 'asc';
    
    $('.sortable').removeClass('asc desc');
    $(this).addClass(order);
    
    sortTable(column, order);
  });

  function sortTable(column, order) {
    var tbody = $('#feeSummaryTable tbody');
    var rows = tbody.find('tr').toArray();
    
    rows.sort(function(a, b) {
      var aVal = $(a).find('td:nth-child(' + getColumnIndex(column) + ')').text();
      var bVal = $(b).find('td:nth-child(' + getColumnIndex(column) + ')').text();
      
      if (column === 'completion' || column === 'total_paid' || column === 'total_due') {
        aVal = parseFloat(aVal.replace(/[^\d.-]/g, ''));
        bVal = parseFloat(bVal.replace(/[^\d.-]/g, ''));
      }
      
      if (order === 'asc') {
        return aVal > bVal ? 1 : -1;
      } else {
        return aVal < bVal ? 1 : -1;
      }
    });
    
    tbody.empty().append(rows);
  }

  function getColumnIndex(column) {
    switch(column) {
      case 'student_id': return 2;
      case 'student_name': return 3;
      case 'total_paid': return 7;
      case 'total_due': return 8;
      case 'completion': return 9;
      default: return 1;
    }
  }

  // Bulk operations
  $('#selectAll').on('change', function() {
    $('.student-checkbox').prop('checked', $(this).is(':checked'));
    toggleBulkActions();
  });

  $('.student-checkbox').on('change', function() {
    toggleBulkActions();
  });

  function toggleBulkActions() {
    var checkedCount = $('.student-checkbox:checked').length;
    var bulkActions = $('#bulkActions');
    
    if (checkedCount > 0) {
      bulkActions.show();
      $('#selectedCount').text(checkedCount);
    } else {
      bulkActions.hide();
    }
  }

  // Bulk update summaries
  $('#bulkUpdateSummaries').on('click', function(e) {
    e.preventDefault();
    
    var selectedStudents = [];
    $('.student-checkbox:checked').each(function() {
      selectedStudents.push($(this).val());
    });
    
    if (selectedStudents.length === 0) {
      toastr.warning('Please select at least one student');
      return;
    }
    
    var button = $(this);
    var originalText = button.html();
    
    button.html('<span class="spinner-border spinner-border-sm me-1"></span>Updating...');
    button.prop('disabled', true);
    
    // Process updates sequentially to avoid overloading
    processStudentUpdates(selectedStudents, 0, button, originalText);
  });

  function processStudentUpdates(students, index, button, originalText) {
    if (index >= students.length) {
      button.html(originalText);
      button.prop('disabled', false);
      toastr.success('All selected fee summaries updated successfully!');
      setTimeout(function() {
        window.location.reload();
      }, 1500);
      return;
    }
    
    var studentId = students[index];
    var academicYearId = $('#academic_year_id').val();
    
    $.post(`/app/fee-summary/student/${studentId}/update`, {
      academic_year_id: academicYearId,
      _token: $('meta[name="csrf-token"]').attr('content')
    })
    .always(function() {
      // Process next student
      processStudentUpdates(students, index + 1, button, originalText);
    });
  }
});

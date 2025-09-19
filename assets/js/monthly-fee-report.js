/**
 * Monthly Fee Report Management
 */

'use strict';

(function () {
  // Elements
  const monthFilter = document.querySelector('#monthFilter');
  const yearFilter = document.querySelector('#yearFilter');
  const statusFilter = document.querySelector('#statusFilter');
  const searchFilter = document.querySelector('#searchFilter');
  const applyFiltersBtn = document.querySelector('#applyFiltersBtn');
  const exportPdfBtn = document.querySelector('#exportPdfBtn');
  const exportExcelBtn = document.querySelector('#exportExcelBtn');
  const bulkActionBtn = document.querySelector('#bulkActionBtn');
  const monthlyFeeTable = document.querySelector('#monthlyFeeTable');
  const selectAllCheckbox = document.querySelector('#selectAllCheckbox');
  const bulkActionModal = new bootstrap.Modal(document.querySelector('#bulkActionModal'));
  const bulkActionForm = document.querySelector('#bulkActionForm');
  const confirmBulkActionBtn = document.querySelector('#confirmBulkActionBtn');

  // Statistics elements
  const totalStudents = document.querySelector('#totalStudents');
  const paidCount = document.querySelector('#paidCount');
  const unpaidCount = document.querySelector('#unpaidCount');
  const overdueCount = document.querySelector('#overdueCount');
  const totalFeeAmount = document.querySelector('#totalFeeAmount');
  const totalFineAmount = document.querySelector('#totalFineAmount');
  const totalCollected = document.querySelector('#totalCollected');
  const totalPending = document.querySelector('#totalPending');
  const collectionRate = document.querySelector('#collectionRate');
  const recordCount = document.querySelector('#recordCount');

  // Data
  let currentData = [];
  let selectedPayments = [];

  // Initialize
  if (monthlyFeeTable) {
    // Wait a bit for DOM to be fully loaded
    setTimeout(() => {
      initializeReport();
    }, 100);
  }

  function initializeDefaultValues() {
    // Get current date for defaults
    const now = new Date();
    const currentMonth = now.getMonth() + 1; // JavaScript months are 0-based
    const currentYear = now.getFullYear();
    
    console.log('Initializing default values...', {
      monthFilter: !!monthFilter,
      yearFilter: !!yearFilter,
      statusFilter: !!statusFilter
    });
    
    // If any required elements are missing, try to find them again
    if (!monthFilter || !yearFilter || !statusFilter) {
      console.warn('Some form elements not found, retrying...');
      setTimeout(() => {
        initializeDefaultValues();
      }, 200);
      return;
    }
    
    // Set default values
    if (monthFilter && !monthFilter.value) {
      monthFilter.value = currentMonth;
      console.log('Set month to:', currentMonth);
    }
    
    if (yearFilter && !yearFilter.value) {
      yearFilter.value = currentYear;
      console.log('Set year to:', currentYear);
    }
    
    if (statusFilter && !statusFilter.value) {
      statusFilter.value = 'all';
      console.log('Set status to: all');
    }
    
    console.log('Initialized default values:', {
      month: monthFilter?.value,
      year: yearFilter?.value,
      status: statusFilter?.value
    });
  }

  function initializeReport() {
    // Initialize default values
    initializeDefaultValues();
    
    // Load initial data
    loadReportData();

    // Event listeners
    applyFiltersBtn.addEventListener('click', loadReportData);
    exportPdfBtn.addEventListener('click', exportToPdf);
    exportExcelBtn.addEventListener('click', exportToExcel);
    bulkActionBtn.addEventListener('click', showBulkActionModal);
    selectAllCheckbox.addEventListener('change', handleSelectAll);
    confirmBulkActionBtn.addEventListener('click', processBulkAction);

    // Filter changes
    [monthFilter, yearFilter, statusFilter].forEach(filter => {
      filter.addEventListener('change', loadReportData);
    });

    // Search on Enter
    searchFilter.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        loadReportData();
      }
    });

    // Bulk action form changes
    document.querySelector('#bulkAction').addEventListener('change', function() {
      const paymentDateSection = document.querySelector('#paymentDateSection');
      if (this.value === 'mark_paid') {
        paymentDateSection.style.display = 'block';
        document.querySelector('#bulkPaymentDate').value = new Date().toISOString().split('T')[0];
      } else {
        paymentDateSection.style.display = 'none';
      }
    });
  }

  function loadReportData() {
    // Get current date for defaults
    const now = new Date();
    const currentMonth = now.getMonth() + 1; // JavaScript months are 0-based
    const currentYear = now.getFullYear();
    
    // Set default values if not already set
    if (!monthFilter.value) {
      monthFilter.value = currentMonth;
    }
    
    if (!yearFilter.value) {
      yearFilter.value = currentYear;
    }
    
    if (!statusFilter.value) {
      statusFilter.value = 'all';
    }

    const params = new URLSearchParams({
      month: monthFilter.value || currentMonth,
      year: yearFilter.value || currentYear,
      status: statusFilter.value || 'all',
      search: searchFilter.value || ''
    });

    // Debug: Log the parameters being sent
    console.log('Sending parameters:', {
      month: monthFilter.value,
      year: yearFilter.value,
      status: statusFilter.value,
      search: searchFilter.value
    });

    // Validate required parameters
    if (!monthFilter.value || !yearFilter.value) {
      console.error('Missing required parameters:', {
        month: monthFilter.value,
        year: yearFilter.value
      });
      showErrorMessage('Please select month and year');
      return;
    }

    // Show loading state
    showLoadingState();

    fetch(window.buildUrl(`app/fee-management/monthly-report/data?${params}`), {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                        document.querySelector('input[name="_token"]')?.value
      }
    })
      .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
          // Try to get the response text for debugging
          return response.text().then(text => {
            console.error('Error response body:', text);
            throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
          });
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          currentData = data.data.data || data.data; // Handle both paginated and non-paginated responses
          updateStatistics(data.stats);
          renderTable(data.data.data || data.data);
        } else {
          showErrorMessage(data.message || 'Failed to load report data');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showErrorMessage('An error occurred while loading the report');
      })
      .finally(() => {
        hideLoadingState();
      });
  }

  function updateStatistics(stats) {
    if (totalStudents) totalStudents.textContent = stats.total_students || 0;
    if (paidCount) paidCount.textContent = stats.paid_count || 0;
    if (unpaidCount) unpaidCount.textContent = stats.unpaid_count || 0;
    if (overdueCount) overdueCount.textContent = stats.overdue_count || 0;
    if (totalFeeAmount) totalFeeAmount.textContent = stats.total_fee_amount || '0.00';
    if (totalFineAmount) totalFineAmount.textContent = stats.total_fine_amount || '0.00';
    if (totalCollected) totalCollected.textContent = stats.total_collected || '0.00';
    if (totalPending) totalPending.textContent = stats.total_pending || '0.00';
    if (collectionRate) collectionRate.textContent = stats.collection_rate || 0;
  }

  function renderTable(data) {
    const tbody = monthlyFeeTable.querySelector('tbody');
    tbody.innerHTML = '';

    if (data && data.length > 0) {
      data.forEach((payment, index) => {
        const row = createTableRow(payment, index + 1);
        tbody.appendChild(row);
      });

      // Update record count
      if (recordCount) {
        recordCount.textContent = `${data.length} records`;
      }
    } else {
      tbody.innerHTML = `
        <tr>
          <td colspan="13" class="text-center py-4">
            <div class="text-muted">
              <i class="ti ti-inbox ti-sm mb-2"></i>
              <p>No payment records found for the selected criteria.</p>
            </div>
          </td>
        </tr>
      `;
      if (recordCount) {
        recordCount.textContent = '0 records';
      }
    }

    // Reset selections
    selectedPayments = [];
    updateBulkActionButton();
    selectAllCheckbox.checked = false;
  }

  function createTableRow(payment, index) {
    const row = document.createElement('tr');
    
    // Status badge
    let statusBadge = '';
    if (payment.is_paid) {
      statusBadge = '<span class="badge bg-success">Paid</span>';
    } else if (payment.is_overdue) {
      statusBadge = '<span class="badge bg-danger">Overdue</span>';
    } else {
      statusBadge = '<span class="badge bg-warning">Pending</span>';
    }

    row.innerHTML = `
      <td>
        <input type="checkbox" class="form-check-input payment-checkbox" 
               value="${payment.id}" data-payment-id="${payment.id}">
      </td>
      <td>${index}</td>
      <td>${payment.student?.student_unique_id || 'N/A'}</td>
      <td>${payment.student?.full_name_in_english_block_letter || 'N/A'}</td>
      <td>${payment.month_name}</td>
      <td>৳${parseFloat(payment.fee_amount).toFixed(2)}</td>
      <td>৳${parseFloat(payment.fine_amount).toFixed(2)}</td>
      <td>৳${parseFloat(payment.total_amount).toFixed(2)}</td>
      <td>${formatDate(payment.due_date)}</td>
      <td>${payment.payment_date ? formatDate(payment.payment_date) : '-'}</td>
      <td>${statusBadge}</td>
      <td>${payment.days_overdue > 0 ? payment.days_overdue : '-'}</td>
      <td>
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                  data-bs-toggle="dropdown" aria-expanded="false">
            Actions
          </button>
          <ul class="dropdown-menu">
            ${!payment.is_paid ? 
              `<li><a class="dropdown-item" href="#" onclick="markAsPaid(${payment.id})">
                <i class="ti ti-check me-2"></i>Mark as Paid
              </a></li>` : 
              `<li><a class="dropdown-item" href="#" onclick="markAsUnpaid(${payment.id})">
                <i class="ti ti-x me-2"></i>Mark as Unpaid
              </a></li>`
            }
            <li><a class="dropdown-item" href="#" onclick="viewPaymentDetails(${payment.id})">
              <i class="ti ti-eye me-2"></i>View Details
            </a></li>
          </ul>
        </div>
      </td>
    `;

    // Add event listener for checkbox
    const checkbox = row.querySelector('.payment-checkbox');
    checkbox.addEventListener('change', handlePaymentSelection);

    return row;
  }

  function handleSelectAll() {
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    checkboxes.forEach(checkbox => {
      checkbox.checked = selectAllCheckbox.checked;
    });
    updateSelectedPayments();
  }

  function handlePaymentSelection() {
    updateSelectedPayments();
    
    // Update select all checkbox state
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
      selectAllCheckbox.indeterminate = false;
      selectAllCheckbox.checked = false;
    } else if (checkedBoxes.length === checkboxes.length) {
      selectAllCheckbox.indeterminate = false;
      selectAllCheckbox.checked = true;
    } else {
      selectAllCheckbox.indeterminate = true;
      selectAllCheckbox.checked = false;
    }
  }

  function updateSelectedPayments() {
    const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
    selectedPayments = Array.from(checkedBoxes).map(checkbox => 
      parseInt(checkbox.getAttribute('data-payment-id'))
    );
    updateBulkActionButton();
  }

  function updateBulkActionButton() {
    if (bulkActionBtn) {
      bulkActionBtn.disabled = selectedPayments.length === 0;
      if (selectedPayments.length > 0) {
        bulkActionBtn.innerHTML = `<i class="ti ti-edit me-1"></i>Bulk Actions (${selectedPayments.length})`;
      } else {
        bulkActionBtn.innerHTML = '<i class="ti ti-edit me-1"></i>Bulk Actions';
      }
    }
  }

  function showBulkActionModal() {
    document.querySelector('#selectedCount').textContent = selectedPayments.length;
    bulkActionModal.show();
  }

  function processBulkAction() {
    const formData = new FormData(bulkActionForm);
    formData.append('payment_ids', JSON.stringify(selectedPayments));

    const confirmBtn = confirmBulkActionBtn;
    const originalText = confirmBtn.innerHTML;

    // Show loading state
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="ti ti-loader ti-xs me-2"></i>Processing...';

    fetch(window.buildUrl('app/fee-management/monthly-report/bulk-update'), {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                        document.querySelector('input[name="_token"]')?.value
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showSuccessMessage(data.message);
        bulkActionModal.hide();
        loadReportData(); // Reload data
      } else {
        showErrorMessage(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showErrorMessage('An error occurred while processing bulk action.');
    })
    .finally(() => {
      // Reset button state
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = originalText;
    });
  }

  function exportToPdf() {
    const params = new URLSearchParams({
      month: monthFilter.value,
      year: yearFilter.value,
      status: statusFilter.value
    });

    window.open(window.buildUrl(`app/fee-management/monthly-report/export-pdf?${params}`), '_blank');
  }

  function exportToExcel() {
    const params = new URLSearchParams({
      month: monthFilter.value,
      year: yearFilter.value,
      status: statusFilter.value
    });

    window.location.href = window.buildUrl(`app/fee-management/monthly-report/export-excel?${params}`);
  }

  function showLoadingState() {
    const tbody = monthlyFeeTable.querySelector('tbody');
    tbody.innerHTML = `
      <tr>
        <td colspan="13" class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading payment records...</p>
        </td>
      </tr>
    `;
  }

  function hideLoadingState() {
    // Loading state will be replaced by renderTable
  }

  function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB');
  }

  function showSuccessMessage(message) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK'
      });
    } else {
      alert(message);
    }
  }

  function showErrorMessage(message) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
      });
    } else {
      alert(message);
    }
  }

  // Global functions for dropdown actions
  window.markAsPaid = function(paymentId) {
    if (confirm('Are you sure you want to mark this payment as paid?')) {
      updatePaymentStatus([paymentId], 'mark_paid');
    }
  };

  window.markAsUnpaid = function(paymentId) {
    if (confirm('Are you sure you want to mark this payment as unpaid?')) {
      updatePaymentStatus([paymentId], 'mark_unpaid');
    }
  };

  window.viewPaymentDetails = function(paymentId) {
    // Find payment in current data
    const payment = currentData?.find(p => p.id === paymentId);
    if (payment) {
      // Show payment details modal or navigate to details page
      showPaymentDetailsModal(payment);
    }
  };

  function updatePaymentStatus(paymentIds, action) {
    const formData = new FormData();
    formData.append('payment_ids', JSON.stringify(paymentIds));
    formData.append('action', action);
    if (action === 'mark_paid') {
      formData.append('payment_date', new Date().toISOString().split('T')[0]);
    }

    fetch(window.buildUrl('app/fee-management/monthly-report/bulk-update'), {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                        document.querySelector('input[name="_token"]')?.value
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showSuccessMessage(data.message);
        loadReportData(); // Reload data
      } else {
        showErrorMessage(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showErrorMessage('An error occurred while updating payment status.');
    });
  }

  function showPaymentDetailsModal(payment) {
    // This could be implemented to show detailed payment information
    // For now, we'll just show an alert with basic info
    const details = `
Payment Details:
- Student: ${payment.student?.full_name_in_english_block_letter || 'N/A'}
- Student ID: ${payment.student?.student_unique_id || 'N/A'}
- Month: ${payment.month_name}
- Fee Amount: ৳${parseFloat(payment.fee_amount).toFixed(2)}
- Fine Amount: ৳${parseFloat(payment.fine_amount).toFixed(2)}
- Total Amount: ৳${parseFloat(payment.total_amount).toFixed(2)}
- Due Date: ${formatDate(payment.due_date)}
- Payment Date: ${payment.payment_date ? formatDate(payment.payment_date) : 'Not paid'}
- Status: ${payment.is_paid ? 'Paid' : (payment.is_overdue ? 'Overdue' : 'Pending')}
- Days Overdue: ${payment.days_overdue > 0 ? payment.days_overdue : 'None'}
    `;
    
    alert(details);
  }
})();

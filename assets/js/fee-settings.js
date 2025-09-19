/**
 * Fee Settings Management
 */

'use strict';

(function () {
  // Fee Settings Form
  const feeSettingsForm = document.querySelector('#feeSettingsForm');
  const fineTypeRadios = document.querySelectorAll('input[name="fine_type"]');
  const fixedFineSection = document.querySelector('#fixedFineSection');
  const percentageFineSection = document.querySelector('#percentageFineSection');
  const settingsSummary = document.querySelector('#settingsSummary');

  // Quick Action Buttons
  const generateCurrentMonthBtn = document.querySelector('#generateCurrentMonthBtn');
  const updateOverdueBtn = document.querySelector('#updateOverdueBtn');
  const updateFeeAmountsBtn = document.querySelector('#updateFeeAmountsBtn');
  const generatePaymentsModal = new bootstrap.Modal(document.querySelector('#generatePaymentsModal'));
  const generatePaymentsForm = document.querySelector('#generatePaymentsForm');
  const confirmGenerateBtn = document.querySelector('#confirmGenerateBtn');

  // Initialize
  if (feeSettingsForm) {
    initializeFeeSettings();
  }

  function initializeFeeSettings() {
    // Handle fine type change
    fineTypeRadios.forEach(radio => {
      radio.addEventListener('change', handleFineTypeChange);
    });

    // Initialize fine type display
    handleFineTypeChange();

    // Handle form submission
    feeSettingsForm.addEventListener('submit', handleFormSubmit);

    // Handle quick actions
    if (generateCurrentMonthBtn) {
      generateCurrentMonthBtn.addEventListener('click', showGeneratePaymentsModal);
    }

    if (updateOverdueBtn) {
      updateOverdueBtn.addEventListener('click', updateOverdueStatus);
    }

    if (updateFeeAmountsBtn) {
      updateFeeAmountsBtn.addEventListener('click', updateFeeAmounts);
    }

    if (confirmGenerateBtn) {
      confirmGenerateBtn.addEventListener('click', generateMonthlyPayments);
    }

    // Load academic years for generate modal
    loadAcademicYears();
  }

  function handleFineTypeChange() {
    const selectedType = document.querySelector('input[name="fine_type"]:checked');
    
    if (selectedType && selectedType.value === 'percentage') {
      fixedFineSection.style.display = 'none';
      percentageFineSection.style.display = 'block';
      
      // Clear fixed amount and make percentage required
      document.querySelector('#fineAmountPerDay').removeAttribute('required');
      document.querySelector('#finePercentage').setAttribute('required', 'required');
    } else {
      fixedFineSection.style.display = 'block';
      percentageFineSection.style.display = 'none';
      
      // Clear percentage and make fixed amount required
      document.querySelector('#finePercentage').removeAttribute('required');
      document.querySelector('#fineAmountPerDay').setAttribute('required', 'required');
    }
  }

  function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(feeSettingsForm);
    const submitBtn = feeSettingsForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader ti-xs me-2"></i>Saving...';

    // Submit form
    fetch('/app/fee-settings', {
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
        updateSettingsSummary(data.data);
      } else {
        showErrorMessage(data.message);
        if (data.errors) {
          displayValidationErrors(data.errors);
        }
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showErrorMessage('An error occurred while saving settings.');
    })
    .finally(() => {
      // Reset button state
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
  }

  function showGeneratePaymentsModal() {
    // Set current month and year
    const currentDate = new Date();
    document.querySelector('#generateMonth').value = currentDate.getMonth() + 1;
    document.querySelector('#generateYear').value = currentDate.getFullYear();
    
    generatePaymentsModal.show();
  }

  function generateMonthlyPayments() {
    const formData = new FormData(generatePaymentsForm);
    const confirmBtn = confirmGenerateBtn;
    const originalText = confirmBtn.innerHTML;

    // Show loading state
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="ti ti-loader ti-xs me-2"></i>Generating...';

    fetch('/app/fee-settings/generate-payments', {
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
        generatePaymentsModal.hide();
      } else {
        showErrorMessage(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showErrorMessage('An error occurred while generating payments.');
    })
    .finally(() => {
      // Reset button state
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = originalText;
    });
  }

  function updateOverdueStatus() {
    const btn = updateOverdueBtn;
    const originalText = btn.innerHTML;

    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-xs me-2"></i>Updating...';

    fetch('/app/fee-settings/update-overdue', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                        document.querySelector('input[name="_token"]')?.value
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showSuccessMessage(data.message);
      } else {
        showErrorMessage(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showErrorMessage('An error occurred while updating overdue status.');
    })
    .finally(() => {
      // Reset button state
      btn.disabled = false;
      btn.innerHTML = originalText;
    });
  }

  function updateFeeAmounts() {
    const btn = updateFeeAmountsBtn;
    const originalText = btn.innerHTML;

    // Show confirmation dialog
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Update Fee Amounts?',
        text: 'This will update all existing monthly fee payments with the current fee amount from settings. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update them!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          performUpdateFeeAmounts(btn, originalText);
        }
      });
    } else {
      if (confirm('This will update all existing monthly fee payments with the current fee amount from settings. Continue?')) {
        performUpdateFeeAmounts(btn, originalText);
      }
    }
  }

  function performUpdateFeeAmounts(btn, originalText) {
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-xs me-2"></i>Updating...';

    fetch('/app/fee-settings/update-fee-amounts', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                        document.querySelector('input[name="_token"]')?.value
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showSuccessMessage(data.message);
      } else {
        showErrorMessage(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showErrorMessage('An error occurred while updating fee amounts.');
    })
    .finally(() => {
      // Reset button state
      btn.disabled = false;
      btn.innerHTML = originalText;
    });
  }

  function loadAcademicYears() {
    const academicYearSelect = document.querySelector('#generateAcademicYear');
    if (!academicYearSelect) return;

    // This would typically load from an API endpoint
    // For now, we'll leave it as is since the current academic year option is sufficient
  }

  function updateSettingsSummary(settings) {
    if (!settingsSummary || !settings) return;

    const html = `
      <div class="d-flex justify-content-between mb-2">
        <span>Monthly Fee:</span>
        <strong>৳${parseFloat(settings.monthly_fee_amount).toFixed(2)}</strong>
      </div>
      <div class="d-flex justify-content-between mb-2">
        <span>Payment Deadline:</span>
        <strong>${settings.payment_deadline_day}${getOrdinalSuffix(settings.payment_deadline_day)} of each month</strong>
      </div>
      <div class="d-flex justify-content-between mb-2">
        <span>Fine Type:</span>
        <strong>${settings.is_percentage_fine ? 'Percentage' : 'Fixed Amount'}</strong>
      </div>
      ${settings.is_percentage_fine ? 
        `<div class="d-flex justify-content-between mb-2">
          <span>Fine Rate:</span>
          <strong>${parseFloat(settings.fine_percentage).toFixed(2)}% per day</strong>
        </div>` :
        `<div class="d-flex justify-content-between mb-2">
          <span>Fine Amount:</span>
          <strong>৳${parseFloat(settings.fine_amount_per_day).toFixed(2)} per day</strong>
        </div>`
      }
      ${settings.maximum_fine_amount ? 
        `<div class="d-flex justify-content-between mb-2">
          <span>Maximum Fine:</span>
          <strong>৳${parseFloat(settings.maximum_fine_amount).toFixed(2)}</strong>
        </div>` : ''
      }
      <div class="d-flex justify-content-between mb-2">
        <span>Grace Period:</span>
        <strong>${settings.grace_period_days} days</strong>
      </div>
    `;

    settingsSummary.innerHTML = html;
  }

  function getOrdinalSuffix(day) {
    if (day >= 11 && day <= 13) {
      return 'th';
    }
    switch (day % 10) {
      case 1: return 'st';
      case 2: return 'nd';
      case 3: return 'rd';
      default: return 'th';
    }
  }

  function displayValidationErrors(errors) {
    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => {
      el.classList.remove('is-invalid');
    });
    document.querySelectorAll('.invalid-feedback').forEach(el => {
      el.remove();
    });

    // Display new errors
    Object.keys(errors).forEach(field => {
      const input = document.querySelector(`[name="${field}"]`);
      if (input) {
        input.classList.add('is-invalid');
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = errors[field][0];
        input.parentNode.appendChild(feedback);
      }
    });
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
})();

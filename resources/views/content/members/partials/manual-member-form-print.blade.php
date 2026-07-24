{{--
  Printable blank form for manual (handwritten) member data collection.
  Used on Add Member and on the standalone print page.
--}}
@php
  $orgName = isset($appSettings) && $appSettings && $appSettings->app_name ? $appSettings->app_name : config('app.name', 'Organization');
@endphp
<div class="manual-member-form-print border rounded p-3 p-md-4 bg-white" id="member-manual-print-area">
  <style>
    .manual-member-form-print .field-line { border-bottom: 1px solid #333; min-height: 1.5rem; }
    .manual-member-form-print .section-title { font-size: 0.95rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; margin: 1.25rem 0 0.75rem; padding-bottom: 0.25rem; border-bottom: 2px solid #333; }
    .manual-member-form-print table { width: 100%; }
    .manual-member-form-print td { vertical-align: top; padding: 0.35rem 0.5rem; font-size: 0.9rem; }
    .manual-member-form-print .label-col { width: 32%; font-weight: 600; }
    @media print {
      .manual-member-form-print { border: none !important; padding: 0 !important; }
      .no-print, .btn { display: none !important; }
    }
  </style>

  <div class="text-center mb-3">
    <h4 class="mb-1">{{ $orgName }}</h4>
    <p class="mb-0 text-muted small">Member registration — manual data form</p>
    <p class="small mb-0">(Fill clearly in block letters or attach documents where indicated)</p>
  </div>

  <div class="section-title">Personal information</div>
  <table class="table table-sm table-bordered mb-0">
    <tbody>
      <tr><td class="label-col">Name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Father's name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Mother's name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Date of birth</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Spouse's name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Mobile</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Email</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">NID number</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Photo</td><td><div class="field-line" style="min-height: 3rem;">&nbsp;</div><small class="text-muted">(Paste / staple photograph)</small></td></tr>
      <tr><td class="label-col">Present address</td><td><div class="field-line" style="min-height: 3.5rem;">&nbsp;</div></td></tr>
      <tr><td class="label-col">Permanent address</td><td><div class="field-line" style="min-height: 3.5rem;">&nbsp;</div></td></tr>
      <tr><td class="label-col">Religion</td><td><div class="field-line">&nbsp;</div></td></tr>
    </tbody>
  </table>

  <div class="section-title">Professional / account information</div>
  <table class="table table-sm table-bordered mb-0">
    <tbody>
      <tr><td class="label-col">Designation</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Date of join in IBBL</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Branch</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Employee ID</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">OSWF ID</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Member ID</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Deposit account number</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Account opening date</td><td><div class="field-line">&nbsp;</div></td></tr>
    </tbody>
  </table>

  <div class="section-title">Nominee information</div>
  <table class="table table-sm table-bordered mb-0">
    <tbody>
      <tr><td class="label-col">Nominee name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Father's name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Mother's name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Spouse's name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Relation to member</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Phone</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">NID number</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Date of birth</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Photo</td><td><div class="field-line" style="min-height: 3rem;">&nbsp;</div></td></tr>
      <tr><td class="label-col">Present address</td><td><div class="field-line" style="min-height: 3rem;">&nbsp;</div></td></tr>
      <tr><td class="label-col">Permanent address</td><td><div class="field-line" style="min-height: 3rem;">&nbsp;</div></td></tr>
    </tbody>
  </table>

  <div class="section-title">First Guarantor</div>
  <table class="table table-sm table-bordered mb-0">
    <tbody>
      <tr><td class="label-col">Name of Guarantor</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Employee ID</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Designation</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Branch Name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Birth Date</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Date of Joining the Bank</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Mobile</td><td><div class="field-line">&nbsp;</div></td></tr>
    </tbody>
  </table>

  <div class="section-title">Second Guarantor</div>
  <table class="table table-sm table-bordered mb-0">
    <tbody>
      <tr><td class="label-col">Name of Guarantor</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Employee ID</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Designation</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Branch Name</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Birth Date</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Date of Joining the Bank</td><td><div class="field-line">&nbsp;</div></td></tr>
      <tr><td class="label-col">Mobile</td><td><div class="field-line">&nbsp;</div></td></tr>
    </tbody>
  </table>

  <div class="row mt-4 pt-2 small">
    <div class="col-md-6 mb-2">
      <strong>Applicant's signature</strong>
      <div class="field-line mt-1" style="min-height: 2rem;">&nbsp;</div>
    </div>
    <div class="col-md-6 mb-2">
      <strong>Date</strong>
      <div class="field-line mt-1" style="min-height: 2rem;">&nbsp;</div>
    </div>
    <div class="col-12">
      <strong>Office use only</strong>
      <div class="field-line mt-1" style="min-height: 2.5rem;">&nbsp;</div>
    </div>
  </div>
</div>

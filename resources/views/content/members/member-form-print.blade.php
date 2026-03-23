@extends('layouts/blankLayout')

@section('title', 'Member manual registration form')

@section('content')
<div class="container-xxl py-4">
  @include('content.members.partials.manual-member-form-print')
  <div class="text-center mt-4 no-print">
    <button type="button" class="btn btn-primary me-2" onclick="window.print()">
      <i class="bx bx-printer me-1"></i> Print
    </button>
    <button type="button" class="btn btn-outline-secondary" onclick="window.close()">Close</button>
  </div>
</div>
@endsection

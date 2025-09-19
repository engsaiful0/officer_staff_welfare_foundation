@extends('layouts/layoutMaster')

@section('title', 'Login')

@section('page-style')
<link href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <!-- Login -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{url('/')}}" class="app-brand-link">
              <img style="height: 100px;width: 100px;"
                src="{{ $appSettings?->logo ? asset('assets/img/branding/'.$appSettings->logo) : asset('assets/img/default-logo.png') }}"
                alt="Logo">
            </a>
          </div>
          <!-- /Logo -->

          <h4 class="mb-1">Welcome to {{ $appSettings->app_name ?? config('app.name') }}! 👋</h4>
          <p class="mb-6">Please sign-in to your account and start the adventure</p>

          <div id="auth-error" class="alert alert-danger mt-3 d-none"></div>

          <form id="formAuthentication" class="mb-4" action="{{ route('auth-login-basic.post') }}" method="POST">
            @csrf
            <div class="mb-6">
              <label for="email" class="form-label">Email or Username</label>
              <input type="text" value="test@example.com" class="form-control" id="email" name="email-username" placeholder="Enter your email or username" autofocus>
            </div>
            <div class="mb-6 form-password-toggle">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" value="123456" class="form-control" name="password" placeholder="********" />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              </div>
            </div>

            <div class="mb-6 mt-3">
              <button class="btn btn-primary d-grid w-100" id="login-btn" type="button">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                Sign in
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script src="{{ asset('assets/js/pages-auth.js') }}"></script>
@endsection

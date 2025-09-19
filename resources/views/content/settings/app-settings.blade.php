@extends('layouts/layoutMaster')

@section('title', 'App Settings')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">App Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('app-settings.update', $settings->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="app_name">App Name</label>
                                <input type="text" class="form-control" id="app_name" name="app_name" value="{{ $settings->app_name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ $settings->address }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $settings->phone }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $settings->email }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="website">Website</label>
                                <input type="text" class="form-control" id="website" name="website" value="{{ $settings->website }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="currency">Currency</label>
                                <input type="text" class="form-control" id="currency" name="currency" value="{{ $settings->currency }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="logo">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo">
                                @if($settings->logo)
                                    <img src="{{ asset('assets/img/branding/' . $settings->logo) }}" alt="logo" class="mt-2" width="100">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="fevicon">Fevicon</label>
                                <input type="file" class="form-control" id="fevicon" name="fevicon">
                                @if($settings->fevicon)
                                    <img src="{{ asset('assets/img/branding/' . $settings->fevicon) }}" alt="fevicon" class="mt-2" width="50">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $settings->start_date }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="date_format">Date Format</label>
                                <input type="text" class="form-control" id="date_format" name="date_format" value="{{ $settings->date_format }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="time_format">Time Format</label>
                                <input type="text" class="form-control" id="time_format" name="time_format" value="{{ $settings->time_format }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="maintainence_mode">Maintainence Mode</label>
                                <select class="form-select" id="maintainence_mode" name="maintainence_mode">
                                    <option value="1" {{ $settings->maintainence_mode == 1 ? 'selected' : '' }}>On</option>
                                    <option value="0" {{ $settings->maintainence_mode == 0 ? 'selected' : '' }}>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="maintainence_mode_message">Maintainence Mode Message</label>
                                <textarea class="form-control" id="maintainence_mode_message" name="maintainence_mode_message" rows="3">{{ $settings->maintainence_mode_message }}</textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

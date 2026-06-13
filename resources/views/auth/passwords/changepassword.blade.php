@extends('layouts.admin')
@section('content')
<div class="col-2"></div>
<div class="card-body col-5 ml-5">
    <div class="login-logo">
        <a><b>{{-- {{ env('APP_NAME') }}--}} Change Password</b></a>
    </div>
@if (\Session::has('success'))
<div class="alert alert-success">
    <ul>
        <li>{!! \Session::get('success') !!}</li>
    </ul>
</div>
@endif
    <div class="card">
        <!-- <div class="card-header">Change Password</div> -->

        <div class="card-body">
            <form method="POST" action="{{ route('admin.change.password') }}">
                @csrf 

                    @foreach ($errors->all() as $error)
                    <p class="text-danger">{{ $error }}</p>
                    @endforeach 

                <div class="form-group">
                    <label for="password" class="col-md-5 col-form-label">Current Password</label>

                    <div class="">
                        <input id="password" type="password" class="form-control" name="current_password" autocomplete="current-password">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="col-md-5 col-form-label">New Password</label>

                    <div class="">
                        <input id="new_password" type="password" class="form-control" name="new_password" autocomplete="current-password">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="col-md-5 col-form-label">New Confirm Password</label>

                    <div class="">
                        <input id="new_confirm_password" type="password" class="form-control" name="new_confirm_password" autocomplete="current-password">
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-primary">
                            Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
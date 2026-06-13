@extends('layouts.app')
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a><b>{{ env('APP_NAME') }}</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
        
            <form method="POST" action="{{ route('password.email') }}">
                {{ csrf_field() }}
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" required="required"="autofocus" placeholder="{{ trans('global.login_email') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @if($errors->has('email'))
                        <em class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </em>
                    @endif
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">{{ trans('global.reset_password') }}</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        
            <p class="mt-3 mb-1">
                <a href="{{ route('adminlogin') }}">{{ trans('global.login') }}</a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->
@endsection
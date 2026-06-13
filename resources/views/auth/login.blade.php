@extends('layouts.app')
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a><b>{{ env('APP_NAME') }}</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            @if(\Session::has('message'))
                <p class="alert alert-info">
                    {{ \Session::get('message') }}
                </p>
            @endif
            @if($errors->has('email'))
                <p class="alert alert-info">
                    {{ $errors->first('email') }}
                </p>
            @endif
        
            <form method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="input-group mb-3">
                    <input name="email" type="text" class="form-control" placeholder="{{ trans('global.login_email') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input name="password" type="password" class="form-control" placeholder="{{ trans('global.login_password') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                        <input type="checkbox"name="remember" id="remember">
                        <label for="remember">
                            {{ trans('global.remember_me') }}
                        </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">{{ trans('global.login') }}</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        
            <p class="mb-1">
                <a href="{{ route('password.request') }}">{{ trans('global.forgot_password') }}</a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->
@endsection
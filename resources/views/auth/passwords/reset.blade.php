@extends('layouts.app')
@section('content')
<style>
.has-error .invalid-feedback {
    display: block !important;
}
</style>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-group">
            <div class="card p-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('password.request') }}">
                        {{ csrf_field() }}
                        <h1>
                            <div class="login-logo">
                                <a href="#">
                                    {{ env('APP_NAME') }}
                                </a>
                            </div>
                        </h1>
                        <p class="text-muted"></p>
                        <div>
                            <input name="token" value="{{ $token }}" type="hidden">
                            <div class="form-group has-feedback ">
                                <input type="email" name="email" class="form-control" required="required" placeholder="{{ trans('global.login_email') }}">
                            </div>
                            <div class="form-group has-feedback ">
                                <input type="password" name="password" class="form-control" required="required" placeholder="{{ trans('global.login_password') }}">
                            </div>
                            <div class="form-group has-feedback">
                                <input type="password" name="password_confirmation" class="form-control" required="required" placeholder="{{ trans('global.login_password_confirmation') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-primary btn-block btn-flat">
                                    {{ trans('global.reset_password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('frontend.layout.applogin')
@section('content')
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
        
            <section class="midContent loginWrapper" id="midContent">
    <div class="container">
        <div class="loginWidget">
            <div class="d-flex loginWidgetTop"><a href="{{url('/')}}"> <img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a></div>
            <div class="loginFormWidget" data-aos="fade-left">
                <div class="loginFormHeader">
                    <h2>Login</h2>
                    <p>We engage with the trends and issues that<br> will shape the future of our world</p>
                </div>
                <div class="loginFormFields">
                <form method="POST" action="{{ route('adminlogin') }}">
                {{ csrf_field() }}
                        <div class="formFields fieldTxt">
                            <span class="formIcon"><img src="{{asset('fassets/images/loginTxtIcon.png')}}" /></span>
                    <input name="email" type="text" class="formInput" placeholder="{{ trans('global.login_email') }}">
                            </div>
                        <div class="formFields fieldPass">
                            <span class="formIcon"><img src="{{asset('fassets/images/loginPassIcon.png')}}" /></span>
                    <input name="password" type="password" class="formInput" placeholder="{{ trans('global.login_password') }}">
                    
                        </div>
                        <div class="d-flex chkWraper">
                            <div>
                                <label class="loginChkBox">
                                Keep me logged in
                                <input type="checkbox"name="remember" id="remember">
                                <span class="checkmark"></span>
                                </label>
                            </div>

                            <div>
                            <a href="{{ route('password.request') }}">{{ trans('global.forgot_password') }}</a>
                            </div>
                        </div>
                        
                        <div class="loginFormBtn">
                            <div class="loginBtn">                            
                            <span class="">
                        <button type="submit" >{{ trans('global.login') }}</button>
                            </span>
                            </div>                            
                        </div>
                        
                    </form>
                </div>
                
            </div>
            <div class="loginFormFooter">
                <p>© 2021 WILO SE</p>
            </div>
        </div>
    </div>
</section>

@endsection
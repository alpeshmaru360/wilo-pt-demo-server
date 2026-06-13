<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
       $user = \App\User::where('email',$request->email)->get();
       if(isset($user[0])) {
           return redirect('login')->with('message', trans('We have emailed you the reset password link.'));
       }
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        $user = \App\User::where('email',$request->email)->get();
        if(empty($user[0])) {
           return redirect('password/reset')->with('message', trans("There is no account with this email"));
        }
    }
}

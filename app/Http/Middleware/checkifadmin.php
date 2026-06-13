<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;

class checkifadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = User::with('roles')->find(Auth::user()->id);
        $user_role = $user->roles[0]->title;
        if (Auth::check() && $user_role != 'User') {
            return $next($request);
        }
        elseif (Auth::check() && $user_role == 'Admin' || $user_role == 'Supervisor') {
            return redirect('/admin');
        }
        else {
            return redirect('/admin/login');
        }
            
    }
}

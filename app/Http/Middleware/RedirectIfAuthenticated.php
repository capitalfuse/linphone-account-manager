<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $password_bool = false;
        
        if ($request->has('email')) {
            
            $email = $request->input('email');
            $input_password = $request->input('password');

            $account = Account::where('email', $email)->first();
            if ($account == null) {
                return $next($request);
            }

            $username = $request->input('username', $account->username);
            $domain = $account->domain;

            $hash_pass = hash('sha256', $username.':'.$domain.':'.$input_password);

            if ($request->has('token')) {
                /* $token = $request->token;
                $hashed_token = DB::table('password_resets')->where('email',$email)->value('token');
                // dd(password_verify($token,$hashed_token));
                if(!password_verify($token,$hashed_token)) {
                    return $next($request);
                } */
                if (Auth::guard($guard)->check()) {
                    return redirect('/home');
                } else {
                    return $next($request);
                }
            }
            
            $password = $account->password;
            $password_bool = hash_equals($password->password, $hash_pass);
                
        }
        if ($password_bool) {
        //if (Auth::guard($guard)->check()) {
            Auth::login($account, true);
            Auth::guard($guard)->check();
            return redirect('/home');
        }

        return $next($request);
    }
}

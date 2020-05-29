<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Account;
use App\Models\Password;
use App\Traits\ActivationTrait;
use App\Traits\CaptchaTrait;
use App\Traits\CaptureIpTrait;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use jeremykenedy\LaravelRoles\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use ActivationTrait;
    use CaptchaTrait;
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/activate';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', [
            'except' => 'logout',
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $data['captcha'] = $this->captchaCheck();

        if (! config('settings.reCaptchStatus')) {
            $data['captcha'] = true;
        }

        return Validator::make(
            $data,
            [
                'username'                  => 'required|max:255|unique:accounts',
                'first_name'            => '',
                'last_name'             => '',
                'domain'                => 'required|max:64',
                'email'                 => 'required|email|max:255|unique:accounts',
                'password'              => 'required|min:6|max:30|confirmed',
                'password_confirmation' => 'required|same:password',
                'g-recaptcha-response'  => '',
                'captcha'               => 'required|min:1',
            ],
            [
                'username.unique'                   => trans('auth.userNameTaken'),
                'username.required'                 => trans('auth.userNameRequired'),
                'first_name.required'           => trans('auth.fNameRequired'),
                'last_name.required'            => trans('auth.lNameRequired'),
                'domain.required'               => trans('auth.domainRequired'),
                'email.required'                => trans('auth.emailRequired'),
                'email.email'                   => trans('auth.emailInvalid'),
                'password.required'             => trans('auth.passwordRequired'),
                'password.min'                  => trans('auth.PasswordMin'),
                'password.max'                  => trans('auth.PasswordMax'),
                'g-recaptcha-response.required' => trans('auth.captchaRequire'),
                'captcha.min'                   => trans('auth.CaptchaWrong'),
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return Account
     */
    protected function create(array $data)
    {
        $ipAddress = new CaptureIpTrait();

        if (config('settings.activation')) {
            $role = Role::where('slug', '=', 'unverified')->first();
            $activated = false;
        } else {
            $role = Role::where('slug', '=', 'user')->first();
            $activated = true;
        }

        $user = Account::create([
            'username'          => $data['username'],
            'first_name'        => $data['first_name'],
            'last_name'         => $data['last_name'],
            'domain'            => $data['domain'],
            'email'             => $data['email'],
            'token'             => str_random(64),
            'signup_ip_address' => $ipAddress->getClientIp(),
            'activated'         => $activated,
        ]);

        $password = Password::create ([
            'account_id'       => $user->id,
            'password'         => hash('sha256', $user->username.':'.$user->domain.':'.$data['password']),
            'algorithm'        => 'SHA-256',
        ]);

        $user->attachRole($role);
        $this->initiateEmailActivation($user);

        if (! config('settings.activation')) {
            $user->password()->save($password);
            $profile = new Profile();
            $user->profile()->save($profile);
            $user->save();
        }

        return $user;
    }
}

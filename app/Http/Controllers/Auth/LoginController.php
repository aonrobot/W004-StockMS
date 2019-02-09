<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (\Auth::attempt($credentials)) {
            // Authentication passed...
            $id = \Auth::id();
            $user = \App\User::find($id);
            $token = $user->createToken('TokenAccessAPI')->accessToken;
            \Session::put('api-token', $token);
            return \Redirect::to("/home");
        } else {
            return \Redirect::to("/login")->with('status', 'Username หรือ Password ผิดครับ')->withInput();
        }
    }

    public function logout(Request $request)
    {
        \Auth::logout();
        \Session::flush();
        return \Redirect::to("/login");
    }
}

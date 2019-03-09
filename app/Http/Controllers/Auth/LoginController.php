<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'identity' => 'required',
            'password' => 'required',
        ]);

        $identity  = $request->input('identity');
        $loginType = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $request->merge([$loginType => $identity]);

        if (auth()->attempt($request->only($loginType, 'password'))) {
            return redirect()->intended($this->redirectPath());
        }

        return redirect()->back()
            ->withInput()
            ->withErrors([
                'identity' => __('These credentials do not match our records.'),
            ]);
    }

    public function logout(Request $request)
    {
        if (!auth()->check()) {
            return redirect(route('login'))->with(['info' => 'You have not logged in before!']);
        }
        $this->guard()->logout();
        $request->session()->invalidate();
        return $this->loggedOut($request) ?: redirect(route('login'))->with(['success' => 'You\'ve been logged out.']);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Login Page
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('auth.login');
    }


    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Login Details
        |--------------------------------------------------------------------------
        */

        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Attempt Login
        |--------------------------------------------------------------------------
        */

        if (!Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {

            return back()
                ->withErrors([
                    'email' => 'The email or password is incorrect.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Check User Status
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if (!$user->status) {

            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return back()
                ->withErrors([
                    'email' => 'Your account is inactive. Please contact administrator.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Redirect To Dashboard
        |--------------------------------------------------------------------------
        */

        return redirect()->route('admin.dashboard');
    }


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Logout User
        |--------------------------------------------------------------------------
        */

        Auth::logout();


        /*
        |--------------------------------------------------------------------------
        | Invalidate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();


        /*
        |--------------------------------------------------------------------------
        | Regenerate CSRF Token
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerateToken();


        /*
        |--------------------------------------------------------------------------
        | Redirect To Login
        |--------------------------------------------------------------------------
        */

        return redirect()->route('login');
    }
}
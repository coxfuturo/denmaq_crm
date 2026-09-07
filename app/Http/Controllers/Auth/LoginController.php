<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show Login Page
    public function showLogin()
    {
        return view('auth.login');
    }


    // Login
    public function login(Request $request)
    {
        // Validate
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);


        // Check Login
        if (!Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {

            return back()
                ->withErrors([
                    'email' => 'The email or password is incorrect.',
                ])
                ->withInput($request->only('email'));
        }


        // Regenerate session
        $request->session()->regenerate();


        // Redirect Admin Dashboard
        return redirect()->route('admin.dashboard');
    }


    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

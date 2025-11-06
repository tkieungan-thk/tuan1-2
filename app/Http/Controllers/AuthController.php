<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin');
        }

        return view('auth.login');
    }
    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('admin')
                ->with('success', __('messages.login_success'));
        }

        return back()->withInput()
            ->with('error', __('messages.login_failed'));
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Auth::login($user);

            return redirect('/admin')->with('success', __('messages.register_success'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', __('messages.register_failed'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', __('messages.logout'));
    }
}
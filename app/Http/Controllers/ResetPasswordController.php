<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ResetPasswordRequest; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /**
     * Hiển thị view nhập mật khẩu mới
     * 
     * @param \Illuminate\Http\Request $request
     * @param mixed $token
     * @return View
     */
    public function showResetForm(Request $request, $token = null): View
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Xử lý reset password
     * 
     * @param  \App\Http\Requests\ResetPasswordRequest  $request
     * @return RedirectResponse
     */
    public function reset(ResetPasswordRequest  $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                Auth::login($user);
            }

            return redirect()->route('admin')->with('success', __('passwords.reset'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
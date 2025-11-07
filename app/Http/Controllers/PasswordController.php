<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Hiển thị view update passwords
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.update-password', compact('user'));
    }

    /**
     * Xử lý update mật khẩu sau khi kiểm tra mật khẩu hiện tại, mật khẩu mới
     * 
     * @param  \App\Http\Requests\UpdatePasswordRequest  $request
     * @return RedirectResponse
     */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();

            $user->update(['password'=> Hash::make($request->password)]);

            return back()->with('success', __('passwords.updated'));

        } catch (\Throwable $e) {
            return back()->with('error', __('passwords.error'));
        }
    }
}
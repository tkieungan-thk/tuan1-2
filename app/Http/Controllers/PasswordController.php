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
    public function edit(Request $request): View
    {
        return view('profile.update-password', [
            'user' => $request->user(),
        ]);
    }
    public function update(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $user = $request->user();

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('success', __('passwords.updated'));

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors(), 'updatePassword');
        } catch (\Throwable $e) {
            return back()->with('error', __('passwords.error'));
        }
    }
    
}
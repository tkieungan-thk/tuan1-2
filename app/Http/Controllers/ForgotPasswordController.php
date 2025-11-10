<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetLinkRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Hiển thị form yêu cầu gửi liên kết đặt lại mật khẩu.
     * Form cho phép người dùng nhập email để nhận liên kết reset mật khẩu.
     *
     * @return View
     */
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Hàm kiểm tra email hợp lệ và gửi link reset mật khẩu qua email
     *
     * @param ResetLinkRequest $request
     * @return RedirectResponse
     */
    public function sendResetLinkEmail(ResetLinkRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );
        Log::info('Password reset status:', ['status' => $status]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}

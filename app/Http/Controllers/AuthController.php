<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập.
     * Nếu người dùng đã đăng nhập, sẽ được chuyển hướng đến trang admin.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLoginForm(): RedirectResponse|View
    {
        if (Auth::check()) {
            return redirect()->route('admin');
        }

        return view('auth.login');
    }

    /**
     * Xử lý yêu cầu đăng nhập người dùng.
     * Xác thực thông tin đăng nhập và đăng nhập người dùng nếu thành công.
     *
     * @param \App\Http\Requests\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return back()->withInput()
                ->with('error', __('messages.account_not_found'));
        }

        if ($user->status == 0) {
            return back()->withInput()
                ->with('error', __('messages.account_locked'));
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('admin')
                ->with('success', __('messages.login_success'));
        }

        return back()->withInput()
            ->with('error', __('messages.login_failed'));
    }

    /**
     * Hiển thị form đăng ký tài khoản mới.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Xử lý yêu cầu đăng ký tài khoản mới.
     * Tạo người dùng mới, mã hóa mật khẩu và tự động đăng nhập sau khi đăng ký thành công.
     *
     * @param \App\Http\Requests\RegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Auth::login($user);

            return redirect()->route('admin')->with('success', __('messages.register_success'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', __('messages.register_failed'));
        }
    }

    /**
     * Xử lý yêu cầu đăng xuất người dùng.
     * Hủy phiên đăng nhập hiện tại và chuyển hướng đến trang đăng nhập.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', __('messages.logout'));
    }
}

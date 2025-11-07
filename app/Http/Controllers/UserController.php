<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CreateUserRequest;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     * 
     * @return View.
     */
    public function index(): View
    {
        //$users = User::all();
        $users = User::orderBy('id', 'desc')->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Hiển thị form tạo người dùng mới
     * 
     * @return View
     */
    public function create(): View
    {
        return view('users.create-user');
    }

    /**
     * Xử lý tạo người dùng mới
     * 
     * @return RedirectResponse
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => true,
            ]);

            //Mail::to($user->email)->send(new UserCreatedMail($user));

            return redirect()
                ->route('users.index')
                ->with('success', __('messages.user_created'));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', __('messages.user_create_failed'));
        }
    }

    /**
     * Hiển thị form chỉnh sửa người dùng
     * 
     * @return View
     */
    public function edit(User $user): View
    {
        return view('users.edit-user', compact('user'));
    }

    /**
     * Xử lý cập nhật thông tin người dùng
     * 
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Xử lý xóa người dùng
     * 
     * @return RedirectResponse
     */

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Người dùng đã được xóa thành công.');
    }

    /*
     * Xử lý thay đổi trạng thái người dùng, khóa tài khoản
     * 
     * @return RedirectResponse
     */
    public function updateStatus(User $user): RedirectResponse
    {
        $user->status = ! $user->status;
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Cập nhật trạng thái người dùng thành công!');
    }
}
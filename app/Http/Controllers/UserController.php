<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\UserCreatedMail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     *
     * @return View.
     */
    public function index(): View
    {
        $users = User::orderBy('id', 'desc')->paginate(5);

        return view('users.index', compact('users'));
    }

    /**
     * Hiển thị form tạo người dùng mới
     */
    public function create(): View
    {
        return view('users.create-user');
    }

    /**
     * Lưu người dùng mới vào cơ sở dữ liệu.
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

            // Mail::to($user->email)->send(new UserCreatedMail($user));

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
     * Hiển thị form chỉnh sửa thông tin người dùng.
     */
    public function edit(User $user): View
    {
        return view('users.edit-user', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $dirty = false;

            if ($user->name !== $validated['name']) {
                $user->name = $validated['name'];
                $dirty = true;
            }

            if ($user->email !== $validated['email']) {
                $user->email = $validated['email'];
                $dirty = true;
            }

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
                $dirty = true;
            }

            if ($dirty) {
                $user->save();
            }

            return redirect()
                ->route('users.index')
                ->with('success', __('messages.user_updated'));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', __('messages.user_update_failed'));
        }
    }

    /**
     * Xử lý xóa người dùng
     *
     * @param User
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'messages.user_deleted');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', __('messages.user_delete_failed'));
        }
    }

    /*
     * Xử lý thay đổi trạng thái người dùng, khóa tài khoản
     *
     * @param User
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

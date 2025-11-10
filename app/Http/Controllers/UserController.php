<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\UserCreatedMail;
use App\Mail\UserPasswordUpdatedMail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     *
     * @return View.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

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
     * Lưu người dùng mới vào cơ sở dữ liệu.
     *
     * @param CreateUserRequest $request
     * @return RedirectResponse .
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'status'   => true,
            ]);
            Mail::to($user->email)->send(new UserCreatedMail($user, $request->password));

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
     *
     * @param User
     * @return View
     */
    public function edit(User $user): View
    {
        return view('users.edit-user', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng.
     *
     * @param User
     * @param UpdateUserRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $dirty           = false;
            $passwordChanged = false;

            if ($user->name !== $validated['name']) {
                $user->name = $validated['name'];
                $dirty      = true;
            }

            if ($user->email !== $validated['email']) {
                $user->email = $validated['email'];
                $dirty       = true;
            }

            if (! empty($validated['password'])) {
                $user->password  = Hash::make($validated['password']);
                $dirty           = true;
                $passwordChanged = true;
            }

            if ($dirty) {
                $user->save();
                if ($passwordChanged) {
                    Mail::to($user->email)->send(
                        new UserPasswordUpdatedMail($user, $validated['password'])
                    );
                }
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
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', __('messages.user_deleted'));
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
        try {
            $user->status = ! $user->status;
            $user->save();

            $message = $user->status
                ? __('users.account_unlocked')
                : __('users.account_locked');

            return redirect()
                ->route('users.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', __('users.update_status_failed'));
        }
    }
}

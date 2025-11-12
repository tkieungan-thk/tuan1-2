<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     *
     * @return View.
     */
    public function index(UserRequest $request): View
    {
        $safe = $request->safe()->only(['keyword', 'status']);

        $keyword = $safe['keyword'] ?? null;
        $status  = $safe['status'] ?? null;

        $users = User::query()
            ->search($keyword)
            ->status($status)
            ->lasted()
            ->get();
        $statuses = UserStatus::cases();

        return view('users.index', compact('users', 'statuses'));
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
     * @param UserRequest $request
     * @return RedirectResponse .
     */
    public function store(UserRequest $request): RedirectResponse
    {
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->password,
                'status'   => UserStatus::ACTIVE,
            ]);
            $user->notify(new UserNotification($request->password, 'created'));

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
     * @param UserRequest $request
     * @return RedirectResponse
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        try {
            $validated = $request->validated();

            if (empty($validated['password'])) {
                unset($validated['password']);
            }

            $user->fill($validated);

            $passwordChanged = $user->isDirty('password');

            if ($user->isDirty()) {
                $user->save();

                if ($passwordChanged) {
                    $user->notify(new UserNotification($request->password, 'updated'));
                }
            }

            return redirect()
                ->route('users.index')
                ->with('success', __('messages.user_updated'));
        } catch (\Exception $e) {
            report($e);

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
            $user->status = $user->status === UserStatus::ACTIVE
                            ? UserStatus::LOCKED
                            : UserStatus::ACTIVE;
            $user->save();

            $message = $user->status === UserStatus::ACTIVE
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

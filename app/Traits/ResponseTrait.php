<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;

trait ResponseTrait
{
    /**
     * Trả về response redirect kèm lỗi và giữ lại input.
     *
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function responseError(string $message = ''): RedirectResponse
    {
        return back()
            ->withInput()
            ->with('error', $message);
    }

    /**
     * Trả về response redirect kèm thông báo thành công.
     *
     * @param string $message
     * @param string $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public function responseSuccess(string $message = '', string $route = 'users.index'): RedirectResponse
    {
        return redirect()
            ->route($route)
            ->with('success', $message);
    }
}

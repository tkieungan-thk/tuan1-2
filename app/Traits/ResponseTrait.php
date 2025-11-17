<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;

trait ResponseTrait
{
    /**
     * Trả về response redirect kèm thông báo thành công.
     *
     * @param string $message
     * @param string $route
     * @param $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function responseSuccess(string $route, string $message, $model = null): RedirectResponse
    {
        return redirect()->route($route, $model)
            ->with('success', $message);
    }

    /**
     * Trả về response redirect và thông tin.
     *
     * @param string $message
     * @param string $route
     * @param $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function responseInfo(string $route, string $message, $model = null): RedirectResponse
    {
        return redirect()->route($route, $model)
            ->with('info', $message);
    }

    /**
     * Trả về response redirect kèm lỗi và giữ lại input.
     *
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function responseError(string $message): RedirectResponse
    {
        return back()->withInput()->with('error', $message);
    }
}

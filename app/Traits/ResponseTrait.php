<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;

trait ResponseTrait
{
    public function responseSuccess(string $route, string $message, $model = null): RedirectResponse
    {
        return redirect()->route($route, $model)
            ->with('success', $message);
    }

    public function responseInfo(string $route, string $message, $model = null): RedirectResponse
    {
        return redirect()->route($route, $model)
            ->with('info', $message);
    }

    public function responseError(string $message): RedirectResponse
    {
        return back()->withInput()->with('error', $message);
    }
}

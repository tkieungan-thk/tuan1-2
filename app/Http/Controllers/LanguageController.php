<?php

namespace App\Http\Controllers;

use App\Enums\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Thay đổi ngôn ngữ hệ thống
     *
     * @param string $lang
     * @return RedirectResponse
     */
    public function change(string $lang): RedirectResponse
    {
        $avaliable = Locale::all();

        if (! in_array($lang, $avaliable)) {
            $lang = Locale::EN->value;
        }
        Session::put('locale', $lang);
        App::setLocale($lang);

        return back();
    }
}

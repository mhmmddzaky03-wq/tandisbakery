<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /** @var array<int, string> */
    private const SUPPORTED = ['id', 'en'];

    public function switch(string $locale): RedirectResponse
    {
        if (! in_array($locale, self::SUPPORTED, true)) {
            abort(404);
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return redirect()
            ->back()
            ->with('success', __('app.flash.language_changed', [
                'language' => __('app.locale.'.$locale),
            ]));
    }
}

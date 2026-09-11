<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $supported = array_keys(config('languages.supported', []));

        if (! in_array($locale, $supported, true)) {
            abort(404);
        }

        $request->session()->put('locale', $locale);

        // Remember the choice on the account itself too, so it follows the
        // user to their next session/device instead of resetting.
        if ($request->user()) {
            $request->user()->forceFill(['locale' => $locale])->save();
        }

        return redirect()->back();
    }
}

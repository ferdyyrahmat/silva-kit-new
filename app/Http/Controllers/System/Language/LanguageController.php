<?php

namespace App\Http\Controllers\System\Language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        if (in_array($lang, ['id', 'en'])) {
            session(['locale' => $lang]);
        }

        return redirect()->back();
    }

    public function toggleTheme(Request $request)
    {
        $theme = $request->input('theme', 'light');
        if (in_array($theme, ['light', 'dark'])) {
            session(['theme' => $theme]);
        }

        return response()->json(['success' => true, 'theme' => $theme]);
    }
}

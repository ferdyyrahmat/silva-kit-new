<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__ . '/auth.php';
require __DIR__ . '/partials/admin.php';
require __DIR__ . '/partials/user.php';

Route::get('lang/{lang}', [App\Http\Controllers\System\Language\LanguageController::class, 'switchLang'])->name('lang.switch');
Route::post('theme/toggle', [App\Http\Controllers\System\Language\LanguageController::class, 'toggleTheme'])->name('theme.toggle');
Route::get('global-search', [App\Http\Controllers\System\Search\SearchController::class, 'search'])->middleware(['auth'])->name('global.search');

Route::get('', [RoutingController::class, 'index'])->middleware(['auth'])->name('root');
Route::prefix('v1')->name('v1.')->middleware(['auth'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Dashboard\DashboardController::class,'index'])->name('dashboard');
});
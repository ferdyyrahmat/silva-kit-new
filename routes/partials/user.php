<?php

Route::prefix('v1')->name('v1.')->middleware(['auth', 'check_permission'])->group(function () {
   Route::prefix('profile')->name('profile.')->group(function () {
      Route::get('', [App\Http\Controllers\System\Profile\ProfileController::class, 'index'])->name('index');
      Route::put('info', [App\Http\Controllers\System\Profile\ProfileController::class, 'updateInfo'])->name('update-info');
      Route::put('password', [App\Http\Controllers\System\Profile\ProfileController::class, 'updatePassword'])->name('update-password');
   });
});
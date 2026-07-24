<?php

Route::prefix('v1')->name('v1.')->middleware(['auth'])->group(function () {
   Route::prefix('profile')->name('profile.')->group(function () {
      Route::get('', [App\Http\Controllers\System\Profile\ProfileController::class, 'index'])->name('index');
      Route::post('info', [App\Http\Controllers\System\Profile\ProfileController::class, 'updateInfo'])->name('update-info');
      Route::post('password', [App\Http\Controllers\System\Profile\ProfileController::class, 'updatePassword'])->name('update-password');
   });
});
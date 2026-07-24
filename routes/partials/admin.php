<?php

Route::prefix('admin')->name('admin.')->middleware(['auth', 'check_permission'])->group(function () {
   Route::prefix('users')->name('users.')->group(function () {
      Route::get('', [App\Http\Controllers\System\User\UserController::class, 'index'])->name('index');
      Route::get('create', [App\Http\Controllers\System\User\UserController::class, 'create'])->name('create');
      Route::post('store', [App\Http\Controllers\System\User\UserController::class, 'store'])->name('store');
      Route::get('{id}/edit', [App\Http\Controllers\System\User\UserController::class, 'edit'])->name('edit');
      Route::put('{id}/update', [App\Http\Controllers\System\User\UserController::class, 'update'])->name('update');
      Route::delete('{id}/destroy', [App\Http\Controllers\System\User\UserController::class, 'destroy'])->name('destroy');
   });

   Route::prefix('permissions')->name('permissions.')->group(function () {
      Route::get('', [App\Http\Controllers\System\Permission\PermissionController::class, 'index'])->name('index');
      Route::get('create', [App\Http\Controllers\System\Permission\PermissionController::class, 'create'])->name('create');
      Route::post('store', [App\Http\Controllers\System\Permission\PermissionController::class, 'store'])->name('store');
      Route::get('{id}/edit', [App\Http\Controllers\System\Permission\PermissionController::class, 'edit'])->name('edit');
      Route::put('{id}/update', [App\Http\Controllers\System\Permission\PermissionController::class, 'update'])->name('update');
      Route::delete('{id}/destroy', [App\Http\Controllers\System\Permission\PermissionController::class, 'destroy'])->name('destroy');
   });

   Route::prefix('maintenance')->name('maintenance.')->group(function () {
      Route::get('', [App\Http\Controllers\System\Maintenance\MaintenanceController::class, 'index'])->name('index');
      Route::get('create', [App\Http\Controllers\System\Maintenance\MaintenanceController::class, 'create'])->name('create');
      Route::post('store', [App\Http\Controllers\System\Maintenance\MaintenanceController::class, 'store'])->name('store');
      Route::get('{id}/edit', [App\Http\Controllers\System\Maintenance\MaintenanceController::class, 'edit'])->name('edit');
      Route::put('{id}/update', [App\Http\Controllers\System\Maintenance\MaintenanceController::class, 'update'])->name('update');
      Route::delete('{id}/destroy', [App\Http\Controllers\System\Maintenance\MaintenanceController::class, 'destroy'])->name('destroy');
   });

   Route::prefix('notifications')->name('notifications.')->group(function () {
      Route::get('', [App\Http\Controllers\System\Notification\NotificationController::class, 'index'])->name('index');
      Route::get('create', [App\Http\Controllers\System\Notification\NotificationController::class, 'create'])->name('create');
      Route::post('store', [App\Http\Controllers\System\Notification\NotificationController::class, 'store'])->name('store');
      Route::get('{id}/edit', [App\Http\Controllers\System\Notification\NotificationController::class, 'edit'])->name('edit');
      Route::put('{id}/update', [App\Http\Controllers\System\Notification\NotificationController::class, 'update'])->name('update');
      Route::delete('{id}/destroy', [App\Http\Controllers\System\Notification\NotificationController::class, 'destroy'])->name('destroy');
   });

   Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
      Route::get('', [App\Http\Controllers\System\AuditLog\AuditLogController::class, 'index'])->name('index');
   });

   Route::prefix('feedbacks')->name('feedbacks.')->group(function () {
      Route::get('', [App\Http\Controllers\System\Feedback\FeedbackController::class, 'index'])->name('index');
      Route::get('create', [App\Http\Controllers\System\Feedback\FeedbackController::class, 'create'])->name('create');
      Route::post('store', [App\Http\Controllers\System\Feedback\FeedbackController::class, 'store'])->name('store');
      Route::get('{id}/edit', [App\Http\Controllers\System\Feedback\FeedbackController::class, 'edit'])->name('edit');
      Route::put('{id}/update', [App\Http\Controllers\System\Feedback\FeedbackController::class, 'update'])->name('update');
      Route::delete('{id}/destroy', [App\Http\Controllers\System\Feedback\FeedbackController::class, 'destroy'])->name('destroy');
   });
});
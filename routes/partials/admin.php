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
      Route::post('settings', [App\Http\Controllers\System\Notification\NotificationController::class, 'storeSettings'])->name('settings.store');
      Route::post('test-connector', [App\Http\Controllers\System\Notification\NotificationController::class, 'testConnector'])->name('test-connector');
      Route::post('send-blast', [App\Http\Controllers\System\Notification\NotificationController::class, 'sendBlast'])->name('send-blast');
   });

   Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
      Route::get('', [App\Http\Controllers\System\AuditLog\AuditLogController::class, 'index'])->name('index');
   });

   Route::prefix('backups')->name('backups.')->group(function () {
      Route::get('', [\App\Http\Controllers\System\Backup\BackupController::class, 'index'])->name('index');
      Route::post('create', [\App\Http\Controllers\System\Backup\BackupController::class, 'create'])->name('create');
      Route::get('download', [\App\Http\Controllers\System\Backup\BackupController::class, 'download'])->name('download');
      Route::delete('{file}', [\App\Http\Controllers\System\Backup\BackupController::class, 'destroy'])->name('destroy');
   });

   Route::prefix('queues')->name('queues.')->group(function () {
      Route::get('', [\App\Http\Controllers\System\Queue\QueueManagerController::class, 'index'])->name('index');
      Route::post('{id}/retry', [\App\Http\Controllers\System\Queue\QueueManagerController::class, 'retryJob'])->name('retry');
      Route::delete('{id}', [\App\Http\Controllers\System\Queue\QueueManagerController::class, 'deleteJob'])->name('delete');
      Route::post('purge', [\App\Http\Controllers\System\Queue\QueueManagerController::class, 'purgeAll'])->name('purge');
   });

   Route::prefix('tickets')->name('tickets.')->group(function () {
      Route::get('', [\App\Http\Controllers\System\Ticket\TicketController::class, 'index'])->name('index');
      Route::get('developers', [\App\Http\Controllers\System\Ticket\DeveloperController::class, 'index'])->name('developers.index');
      Route::post('developers', [\App\Http\Controllers\System\Ticket\DeveloperController::class, 'store'])->name('developers.store');
      Route::put('developers/{id}', [\App\Http\Controllers\System\Ticket\DeveloperController::class, 'update'])->name('developers.update');
      Route::delete('developers/{id}', [\App\Http\Controllers\System\Ticket\DeveloperController::class, 'destroy'])->name('developers.destroy');
      Route::get('{id}', [\App\Http\Controllers\System\Ticket\TicketController::class, 'show'])->name('show');
      Route::post('{id}/reply', [\App\Http\Controllers\System\Ticket\TicketController::class, 'reply'])->name('reply');
      Route::post('{id}/assign', [\App\Http\Controllers\System\Ticket\TicketController::class, 'assign'])->name('assign');
      Route::delete('{id}', [\App\Http\Controllers\System\Ticket\TicketController::class, 'destroy'])->name('destroy');
   });

   Route::prefix('settings')->name('settings.')->group(function () {
      Route::prefix('branding')->name('branding.')->group(function () {
         Route::get('', [\App\Http\Controllers\System\Setting\BrandingSettingController::class, 'index'])->name('index');
         Route::post('', [\App\Http\Controllers\System\Setting\BrandingSettingController::class, 'update'])->name('update');
      });
      Route::prefix('websocket')->name('websocket.')->group(function () {
         Route::get('', [\App\Http\Controllers\System\Setting\WebSocketSettingController::class, 'index'])->name('index');
         Route::post('', [\App\Http\Controllers\System\Setting\WebSocketSettingController::class, 'update'])->name('update');
         Route::post('test', [\App\Http\Controllers\System\Setting\WebSocketSettingController::class, 'test'])->name('test');
      });
   });

   Route::prefix('directory')->name('directory.')->group(function () {
      Route::get('', [\App\Http\Controllers\System\Directory\DirectoryController::class, 'index'])->name('index');
      Route::post('upload', [\App\Http\Controllers\System\Directory\DirectoryController::class, 'upload'])->name('upload');
      Route::post('folder', [\App\Http\Controllers\System\Directory\DirectoryController::class, 'makeFolder'])->name('folder');
      Route::get('download', [\App\Http\Controllers\System\Directory\DirectoryController::class, 'download'])->name('download');
      Route::delete('destroy', [\App\Http\Controllers\System\Directory\DirectoryController::class, 'destroy'])->name('destroy');
      Route::post('settings', [\App\Http\Controllers\System\Directory\DirectoryController::class, 'saveSettings'])->name('settings');
   });
});
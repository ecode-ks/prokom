<?php
use App\Http\Controllers\API\CheckInApiController;
use App\Http\Controllers\API\NotificationsApiController;
use App\Http\Controllers\API\ProjectReportsApiController;
use App\Http\Controllers\API\TaskReportsApiController;
use App\Http\Controllers\API\TasksApiController;
use App\Http\Controllers\API\UsersApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\WorkingHoursApiController;

// Users / auth
Route::controller(UsersApiController::class)->group(function () {
    Route::post('/check-login', 'checkLogin')->name('check.login');
    Route::post('/update-profile-password', 'updateProfilePassword')->name('update.profile.password');
    Route::post('/update-company-language', 'updateCompanyLanguage')->name('update.company.language');
});

// Tasks
Route::controller(TasksApiController::class)->prefix('tasks/{companyId}/{employeeId}/{role}')->group(function () {
    Route::post('/create', 'create')->name('api.tasks.create');
    Route::get('/read', 'read')->name('api.tasks.read');
    Route::get('/{id}/edit', 'edit')->name('api.tasks.edit');
    Route::get('/{id}', 'taskDetails')->name('api.tasks.details');
    Route::post('/update', 'update')->name('api.tasks.update');
    Route::delete('/{id}', 'delete')->name('api.tasks.delete');
    Route::patch('/{id}/status/{status}', 'updateStatus')->name('api.tasks.status');
});

// Task Reports
Route::controller(TaskReportsApiController::class)->prefix('task-reports/{companyId}/{employeeId}/{role}')->group(function () {
    Route::post('/create', 'create')->name('api.task.reports.create');
    Route::get('/read', 'read')->name('api.task.reports.read');
    Route::get('/{id}', 'details')->name('api.task.reports.details');
    Route::get('/{id}/edit', 'edit')->name('api.task.reports.edit');
    Route::post('/update', 'update')->name('api.task.reports.update');
    Route::delete('/{id}', 'delete')->name('api.task.reports.delete');
    Route::patch('/{id}/status/{status}', 'updateStatus')->name('api.task.reports.status');
});

// Project Reports
Route::controller(ProjectReportsApiController::class)->prefix('project-reports/{companyId}/{employeeId}/{role}')->group(function () {
    Route::post('/create', 'create')->name('api.project.reports.create');
    Route::get('/read', 'read')->name('api.project.reports.read');
    Route::get('/{id}', 'reportDetails')->name('api.project.reports.details');
    Route::get('/{id}/edit', 'edit')->name('api.project.reports.edit');
    Route::post('/update', 'update')->name('api.project.reports.update');
    Route::delete('/{id}', 'delete')->name('api.project.reports.delete');
    Route::patch('/{id}/status/{status}', 'updateStatus')->name('api.project.reports.status');
});

// Working Hours
Route::controller(WorkingHoursApiController::class)->group(function () {
    Route::post('/working-hours/create', 'create')->name('api.working.hours.create');
    Route::get('/working-hours/read/{companyId}/{employeeId}/{role}', 'read')->name('api.working.hours.read');
    Route::delete('/working-hours/delete/{id}', 'delete')->name('api.working.hours.delete');
});

// Check-ins
Route::controller(CheckInApiController::class)->prefix('check-ins')->group(function () {
    Route::get('/', 'read')->name('api.checkins.read');
    Route::get('/map', 'map')->name('api.checkins.map');
    Route::get('/{id}', 'details')->name('api.checkins.details');
    Route::post('/{id}/accept', 'accept')->name('api.checkins.accept');
    Route::delete('/{id}', 'delete')->name('api.checkins.delete');
    Route::post('/start-work', 'startWork')->name('api.checkins.start.work');
    Route::post('/start-break', 'startBreak')->name('api.checkins.start.break');
    Route::post('/end-break', 'endBreak')->name('api.checkins.end.break');
    Route::post('/end-work', 'endWork')->name('api.checkins.end.work');
});

// Notifications
Route::controller(NotificationsApiController::class)->prefix('notifications')->group(function () {
    Route::get('/', 'read')->name('api.notifications.read');
    Route::post('/open/{id}', 'open')->name('api.notifications.open');
    Route::post('/mark-all-read', 'markAllRead')->name('api.notifications.mark.all');
});



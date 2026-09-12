<?php
use App\Http\Controllers\API\CheckInApiController;
use App\Http\Controllers\API\CompanyNewsApiController;
use App\Http\Controllers\API\NotificationsApiController;
use App\Http\Controllers\API\ProjectReportsApiController;
use App\Http\Controllers\API\TaskReportsApiController;
use App\Http\Controllers\API\TasksApiController;
use App\Http\Controllers\API\UsersApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\WorkingHoursApiController;

// Users / auth
Route::controller(UsersApiController::class)->group(function () {
    Route::post('/api/check-login', 'checkLogin')->name('api.check.login');
    Route::post('/api/update-profile-password', 'updateProfilePassword')->name('api.update.profile.password');
    Route::post('/api/update-company-language', 'updateCompanyLanguage')->name('api.update.company.language');
});

// Company News
Route::controller(CompanyNewsApiController::class)->group(function () {
    Route::get('/api/company-news/{companyId}', 'read')->name('api.company.news.read');
});

// Tasks
Route::controller(TasksApiController::class)->group(function () {
    Route::post('/api/create', 'create')->name('api.tasks.create');
    Route::get('/api/tasks/{companyId}/{employeeId}/{role}', 'read')->name('api.tasks.read');
    Route::get('/api/{id}/edit', 'edit')->name('api.tasks.edit');
    Route::get('/api/{id}', 'taskDetails')->name('api.tasks.details');
    Route::post('/api/update', 'update')->name('api.tasks.update');
    Route::delete('/api/{id}', 'delete')->name('api.tasks.delete');
    Route::patch('/api/{id}/status/{status}', 'updateStatus')->name('api.tasks.status');
});

// Task Reports
Route::controller(TaskReportsApiController::class)->prefix('task-reports/{companyId}/{employeeId}/{role}')->group(function () {
    Route::post('/api/create', 'create')->name('api.task.reports.create');
    Route::get('/api/read', 'read')->name('api.task.reports.read');
    Route::get('/api/{id}', 'details')->name('api.task.reports.details');
    Route::get('/api/{id}/edit', 'edit')->name('api.task.reports.edit');
    Route::post('/api/update', 'update')->name('api.task.reports.update');
    Route::delete('/api/{id}', 'delete')->name('api.task.reports.delete');
    Route::patch('/api/{id}/status/{status}', 'updateStatus')->name('api.task.reports.status');
});

// Project Reports
Route::controller(ProjectReportsApiController::class)->prefix('project-reports/{companyId}/{employeeId}/{role}')->group(function () {
    Route::post('/api/create', 'create')->name('api.project.reports.create');
    Route::get('/api/read', 'read')->name('api.project.reports.read');
    Route::get('/api/{id}', 'reportDetails')->name('api.project.reports.details');
    Route::get('/api/{id}/edit', 'edit')->name('api.project.reports.edit');
    Route::post('/api/update', 'update')->name('api.project.reports.update');
    Route::delete('/api/{id}', 'delete')->name('api.project.reports.delete');
    Route::patch('/api/{id}/status/{status}', 'updateStatus')->name('api.project.reports.status');
});

// Working Hours
Route::controller(WorkingHoursApiController::class)->group(function () {
    Route::post('/api/working-hours/create', 'create')->name('api.working.hours.create');
    Route::get('/api/working-hours/read/{companyId}/{employeeId}/{role}', 'read')->name('api.working.hours.read');
    Route::delete('/api/working-hours/delete/{id}', 'delete')->name('api.working.hours.delete');
});

// Check-ins
Route::controller(CheckInApiController::class)->prefix('check-ins')->group(function () {
    Route::get('/', 'read')->name('api.checkins.read');
    Route::get('/api/map', 'map')->name('api.checkins.map');
    Route::get('/api/{id}', 'details')->name('api.checkins.details');
    Route::post('/api/{id}/accept', 'accept')->name('api.checkins.accept');
    Route::delete('/api/{id}', 'delete')->name('api.checkins.delete');
    Route::post('/api/start-work', 'startWork')->name('api.checkins.start.work');
    Route::post('/api/start-break', 'startBreak')->name('api.checkins.start.break');
    Route::post('/api/end-break', 'endBreak')->name('api.checkins.end.break');
    Route::post('/api/end-work', 'endWork')->name('api.checkins.end.work');
});

// Notifications
Route::controller(NotificationsApiController::class)->prefix('api.notifications')->group(function () {
    Route::get('/api/', 'read')->name('api.notifications.read');
    Route::post('/api/open/{id}', 'open')->name('api.notifications.open');
    Route::post('/api/mark-all-read', 'markAllRead')->name('api.notifications.mark.all');
});


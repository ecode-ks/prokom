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
use App\Http\Controllers\API\ExtraHoursApiController;
use App\Http\Controllers\API\EmployeeReportApiController;
use App\Http\Controllers\API\ContractsApiController;
use App\Http\Controllers\API\SalariesApiController;
use App\Http\Controllers\API\VacationsApiController;
use App\Http\Controllers\API\ProjectsApiController;

// Users / auth
Route::controller(UsersApiController::class)->group(function () {
   Route::post('/api/check-login', 'checkLogin')->name('api.check.login');
   Route::middleware(['auth:sanctum', 'api.locale'])->get('/user', 'profile')->name('api.user.profile');
   Route::middleware(['auth:sanctum', 'api.locale'])->get('/api/user', 'profile')->name('api.user.profile.nested');
   Route::middleware(['auth:sanctum', 'api.locale'])->post('/logout', 'logout')->name('api.logout');
   Route::middleware(['auth:sanctum', 'api.locale'])->post('/api/logout', 'logout')->name('api.logout.nested');
   Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->post('/api/update-profile-password', 'updateProfilePassword')->name('api.update.profile.password');
});

// Company News
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(CompanyNewsApiController::class)->group(function () {
    Route::post('/api/company-news/create/{companyId}/{employeeId}/{role}', 'create')->name('api.company.news.create');
    Route::get('/api/company-news/read/{companyId}/{employeeId}/{role}', 'read')->name('api.company.news.read');
    Route::get('/api/company-news/{id}/edit/{companyId}/{employeeId}/{role}', 'edit')->name('api.company.news.edit');
    Route::post('/api/company-news/update/{companyId}/{employeeId}/{role}', 'update')->name('api.company.news.update');
    Route::delete('/api/company-news/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.company.news.delete');
});

// Working Hours
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(WorkingHoursApiController::class)->group(function () {
    Route::post('/api/working-hours/create/{companyId}/{employeeId}/{role}', 'create')->name('api.working.hours.create');
    Route::get('/api/working-hours/read/{companyId}/{employeeId}/{role}', 'read')->name('api.working.hours.read');
    Route::delete('/api/working-hours/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.working.hours.delete');
});

// Check-ins
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(CheckInApiController::class)->group(function () {
    Route::get('/api/check-ins/read/{companyId}/{employeeId}/{role}', 'read')->name('api.checkins.read');
    Route::get('/api/check-ins/map/{companyId}/{employeeId}/{role}', 'map')->name('api.checkins.map');
    Route::get('/api/check-ins/{id}/details/{companyId}/{employeeId}/{role}', 'details')->name('api.checkins.details');
    Route::get('/api/check-ins/{id}/accept/{companyId}/{employeeId}/{role}', 'accept')->name('api.checkins.accept');
    Route::delete('/api/check-ins/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.checkins.delete');
    Route::post('/api/check-ins/start-work/{companyId}/{employeeId}/{role}', 'startWork')->name('api.checkins.start.work');
    Route::post('/api/check-ins/start-break/{companyId}/{employeeId}/{role}', 'startBreak')->name('api.checkins.start.break');
    Route::post('/api/check-ins/end-break/{companyId}/{employeeId}/{role}', 'endBreak')->name('api.checkins.end.break');
    Route::post('/api/check-ins/end-work/{companyId}/{employeeId}/{role}', 'endWork')->name('api.checkins.end.work');
});

// Notifications
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(NotificationsApiController::class)->prefix('api.notifications')->group(function () {
   Route::get('/api/', 'read')->name('api.notifications.read');
   Route::post('/api/open/{id}', 'open')->name('api.notifications.open');
   Route::post('/api/mark-all-read', 'markAllRead')->name('api.notifications.mark.all');
});

// Extra Hours
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(ExtraHoursApiController::class)->group(function () {
    Route::post('/api/extra-hours/create/{companyId}/{employeeId}/{role}', 'create')->name('api.extra.hours.create');
    Route::get('/api/extra-hours/read/{companyId}/{employeeId}/{role}', 'read')->name('api.extra.hours.read');
    Route::get('/api/extra-hours/{id}/edit/{companyId}/{employeeId}/{role}', 'edit')->name('api.extra.hours.edit');
    Route::post('/api/extra-hours/update/{companyId}/{employeeId}/{role}', 'update')->name('api.extra.hours.update');
    Route::delete('/api/extra-hours/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.extra.hours.delete');
});

// Contracts
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(ContractsApiController::class)->group(function () {
   Route::get('/api/contracts/read/{companyId}/{employeeId}/{role}', 'read')->name('api.contracts.read');
   Route::get('/api/contracts/{id}/details/{companyId}/{employeeId}/{role}', 'contractDetails')->name('api.contracts.details');
});

// Salaries
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(SalariesApiController::class)->group(function () {
   Route::get('/api/salaries/read/{companyId}/{employeeId}/{role}', 'read')->name('api.salaries.read');
});

// Vacations
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(VacationsApiController::class)->group(function () {
   Route::post('/api/vacations/create/{companyId}/{employeeId}/{role}', 'create')->name('api.vacations.create');
   Route::get('/api/vacations/read/{companyId}/{employeeId}/{role}', 'read')->name('api.vacations.read');
   Route::get('/api/vacations/{id}/edit/{companyId}/{employeeId}/{role}', 'edit')->name('api.vacations.edit');
   Route::post('/api/vacations/update/{companyId}/{employeeId}/{role}', 'update')->name('api.vacations.update');
   Route::delete('/api/vacations/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.vacations.delete');
});

// Tasks
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(TasksApiController::class)->group(function () {
    Route::post('/api/tasks/create/{companyId}/{employeeId}/{role}', 'create')->name('api.tasks.create');
    Route::get('/api/tasks/read/{companyId}/{employeeId}/{role}', 'read')->name('api.tasks.read');
    Route::get('/api/tasks/{id}/edit/{companyId}/{employeeId}/{role}', 'edit')->name('api.tasks.edit');
    Route::get('/api/tasks/{id}/details/{companyId}/{employeeId}/{role}', 'taskDetails')->name('api.tasks.details');
    Route::post('/api/tasks/update/{companyId}/{employeeId}/{role}', 'update')->name('api.tasks.update');
    Route::delete('/api/tasks/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.tasks.delete');
    Route::get('/api/tasks/{id}/status/{status}/{companyId}/{employeeId}/{role}', 'updateStatus')->name('api.tasks.status');
});

// Task Reports
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(TaskReportsApiController::class)->group(function () {
   Route::post('/api/task-reports/create/{companyId}/{employeeId}/{role}', 'create')->name('api.task.reports.create');
   Route::get('/api/task-reports/read/{companyId}/{employeeId}/{role}', 'read')->name('api.task.reports.read');
   Route::get('/api/task-reports/{id}/details/{companyId}/{employeeId}/{role}', 'details')->name('api.task.reports.details');
   Route::get('/api/task-reports/{id}/edit/{companyId}/{employeeId}/{role}', 'edit')->name('api.task.reports.edit');
   Route::post('/api/task-reports/update/{companyId}/{employeeId}/{role}', 'update')->name('api.task.reports.update');
   Route::delete('/api/task-reports/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.task.reports.delete');
   Route::get('/api/task-reports/{id}/status/{status}/{companyId}/{employeeId}/{role}', 'updateStatus')->name('api.task.reports.status');
});

// Project Reports
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(ProjectReportsApiController::class)->group(function () {
    Route::post('/api/project-reports/create/{companyId}/{employeeId}/{role}', 'create')->name('api.project.reports.create');
    Route::get('/api/project-reports/read/{companyId}/{employeeId}/{role}', 'read')->name('api.project.reports.read');
    Route::get('/api/project-reports/{id}/details/{companyId}/{employeeId}/{role}', 'reportDetails')->name('api.project.reports.details');
    Route::get('/api/project-reports/{id}/edit/{companyId}/{employeeId}/{role}', 'edit')->name('api.project.reports.edit');
    Route::post('/api/project-reports/update/{companyId}/{employeeId}/{role}', 'update')->name('api.project.reports.update');
    Route::delete('/api/project-reports/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.project.reports.delete');
    Route::get('/api/project-reports/{id}/status/{status}/{companyId}/{employeeId}/{role}', 'updateStatus')->name('api.project.reports.status');
});

  // Employee Report
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(EmployeeReportApiController::class)->group(function () {
    Route::get('/employee-report/read/{companyId}/{employeeId}/{role}', 'read')
        ->name('api.employee.report.read');
});

// Projects
Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(ProjectsApiController::class)->group(function () {
    Route::get('/api/projects/read/{companyId}/{employeeId}/{role}', 'read')->name('api.projects.read');
    Route::get('/api/projects/{id}/details/{companyId}/{employeeId}/{role}', 'projectDetails')->name('api.projects.details');
    Route::get('/api/projects/{id}/context/{companyId}/{employeeId}/{role}', 'setProjectContext')->name('api.projects.context');
});

Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(NotificationsApiController::class)->prefix('api.notifications')->group(function () {
    Route::get('/api/', 'read')->name('api.notifications.read');
    Route::post('/api/open/{id}', 'open')->name('api.notifications.open');
    Route::post('/api/mark-all-read', 'markAllRead')->name('api.notifications.mark.all');
    Route::post('/api/push-token', 'registerPushToken')->name('api.notifications.push.token');
});

Route::middleware(['auth:sanctum', 'api.locale', 'api.payment'])->controller(TaskReportsApiController::class)->group(function () {
    Route::post('/api/task-reports/create/{companyId}/{employeeId}/{role}', 'create')->name('api.task.reports.create');
    Route::get('/api/task-reports/read/{companyId}/{employeeId}/{role}', 'read')->name('api.task.reports.read');
    Route::get('/api/task-reports/{id}/details/{companyId}/{employeeId}/{role}', 'details')->name('api.task.reports.details');
    Route::get('/api/task-reports/{id}/edit/{companyId}/{employeeId}/{role}', 'edit')->name('api.task.reports.edit');
    Route::post('/api/task-reports/update/{companyId}/{employeeId}/{role}', 'update')->name('api.task.reports.update');
    Route::delete('/api/task-reports/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.task.reports.delete');
    Route::get('/api/task-reports/{id}/status/{status}/{companyId}', 'updateStatus')->name('api.task.reports.status');
});

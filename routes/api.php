<?php
//use App\Http\Controllers\API\CheckInApiController;
//use App\Http\Controllers\API\CompanyNewsApiController;
//use App\Http\Controllers\API\NotificationsApiController;
//use App\Http\Controllers\API\ProjectReportsApiController;
//use App\Http\Controllers\API\TaskReportsApiController;
//use App\Http\Controllers\API\TasksApiController;
//use App\Http\Controllers\API\UsersApiController;
//use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\API\WorkingHoursApiController;
//use App\Http\Controllers\API\ExtraHoursApiController;
//use App\Http\Controllers\API\EmployeeReportApiController;
//use App\Http\Controllers\API\ContractsApiController;
//use App\Http\Controllers\API\SalariesApiController;
//use App\Http\Controllers\API\VacationsApiController;
//
//// Users / auth
//Route::controller(UsersApiController::class)->group(function () {
//    Route::post('/api/check-login', 'checkLogin')->name('api.check.login');
//    Route::post('/api/update-profile-password', 'updateProfilePassword')->name('api.update.profile.password');
//    Route::post('/api/update-company-language', 'updateCompanyLanguage')->name('api.update.company.language');
//});
//
//// Company News
//Route::controller(CompanyNewsApiController::class)->group(function () {
//    Route::get('/api/company-news/{companyId}', 'read')->name('api.company.news.read');
//});
//
//// Working Hours
//Route::controller(WorkingHoursApiController::class)->group(function () {
//    Route::post('/api/working-hours/create', 'create')->name('api.working.hours.create');
//    Route::get('/api/working-hours/read/{companyId}/{employeeId}/{role}', 'read')->name('api.working.hours.read');
//    Route::delete('/api/working-hours/delete/{id}', 'delete')->name('api.working.hours.delete');
//});
//
//// Check-ins
//Route::controller(CheckInApiController::class)->prefix('check-ins')->group(function () {
//    Route::get('/', 'read')->name('api.checkins.read');
//    Route::get('/api/map', 'map')->name('api.checkins.map');
//    Route::get('/api/{id}', 'details')->name('api.checkins.details');
//    Route::post('/api/{id}/accept', 'accept')->name('api.checkins.accept');
//    Route::delete('/api/{id}', 'delete')->name('api.checkins.delete');
//    Route::post('/api/start-work', 'startWork')->name('api.checkins.start.work');
//    Route::post('/api/start-break', 'startBreak')->name('api.checkins.start.break');
//    Route::post('/api/end-break', 'endBreak')->name('api.checkins.end.break');
//    Route::post('/api/end-work', 'endWork')->name('api.checkins.end.work');
//});
//
//// Notifications
//Route::controller(NotificationsApiController::class)->prefix('api.notifications')->group(function () {
//    Route::get('/api/', 'read')->name('api.notifications.read');
//    Route::post('/api/open/{id}', 'open')->name('api.notifications.open');
//    Route::post('/api/mark-all-read', 'markAllRead')->name('api.notifications.mark.all');
//});
//
//// Extra Hours
//Route::controller(ExtraHoursApiController::class)->group(function () {
//    Route::get('/api/extra-hours/read/{companyId}/{employeeId}/{role}', 'read')->name('api.extra.hours.read');
//});
//
//// Contracts
//Route::controller(ContractsApiController::class)->group(function () {
//    Route::get('/api/contracts/read/{companyId}/{employeeId}/{role}', 'read')->name('api.contracts.read');
//    Route::get('/api/contracts/{id}/details/{companyId}/{employeeId}/{role}', 'contractDetails')->name('api.contracts.details');
//});
//
//// Salaries
//Route::controller(SalariesApiController::class)->group(function () {
//    Route::get('/api/salaries/read/{companyId}/{employeeId}/{role}', 'read')->name('api.salaries.read');
//});
//
//// Vacations
//Route::controller(VacationsApiController::class)->group(function () {
//    Route::post('/api/vacations/create/{companyId}/{employeeId}/{role}', 'create')->name('api.vacations.create');
//    Route::get('/api/vacations/read/{companyId}/{employeeId}/{role}', 'read')->name('api.vacations.read');
//    Route::get('/api/vacations/{id}/edit/{companyId}/{employeeId}/{role}', 'edit')->name('api.vacations.edit');
//    Route::post('/api/vacations/update/{companyId}/{employeeId}/{role}', 'update')->name('api.vacations.update');
//    Route::delete('/api/vacations/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.vacations.delete');
//});
//
//// Tasks
//Route::controller(TasksApiController::class)->group(function () {
//    Route::get('/api/tasks/read/{companyId}/{employeeId}/{role}', 'read')->name('api.tasks.read');
//    Route::get('/api/tasks/{id}/details/{companyId}/{employeeId}/{role}', 'taskDetails')->name('api.tasks.details');
//    Route::get('/api/tasks/{id}/status/{status}/{companyId}/{employeeId}/{role}', 'updateStatus')->name('api.tasks.status');
//});
//
//// Task Reports
//Route::controller(TaskReportsApiController::class)->group(function () {
//    Route::post('/api/task-reports/create/{companyId}/{employeeId}/{role}', 'create')->name('api.task.reports.create');
//    Route::get('/api/task-reports/read/{companyId}/{employeeId}/{role}', 'read')->name('api.task.reports.read');
//    Route::get('/api/task-reports/{id}/details/{companyId}/{employeeId}/{role}', 'details')->name('api.task.reports.details');
//    Route::get('/api/task-reports/{id}/edit/{companyId}/{employeeId}/{role}', 'edit')->name('api.task.reports.edit');
//    Route::post('/api/task-reports/update/{companyId}/{employeeId}/{role}', 'update')->name('api.task.reports.update');
//    Route::delete('/api/task-reports/{id}/{companyId}/{employeeId}/{role}', 'delete')->name('api.task.reports.delete');
//    Route::get('/api/task-reports/{id}/status/{status}/{companyId}', 'updateStatus')->name('api.task.reports.status');
//});
//
//// Employee Report
//Route::controller(EmployeeReportApiController::class)->group(function () {
//    Route::get('/employee-report/read/{companyId}/{employeeId}/{role}', 'read')
//        ->name('api.employee.report.read');
//});

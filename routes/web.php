<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\MaterialsController;
use App\Http\Controllers\MaterialsStockController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\FinancialTransactionsController;
use App\Http\Controllers\CompanyNewsController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\VacationsController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ProjectReportsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TaskReportsController;
use App\Http\Controllers\WorkingHoursController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\SalariesController;
use App\Http\Controllers\ExtraHoursController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\MarketingController;

// Marketing (public)
Route::middleware(\App\Http\Middleware\SetMarketingLocale::class)->group(function () {
    Route::get('/', [MarketingController::class, 'home'])->name('home');
    Route::get('/produkti', [MarketingController::class, 'product'])->name('marketing.product');
    Route::get('/produkti/erp', [MarketingController::class, 'erp'])->name('marketing.erp');
    Route::get('/produkti/crm', [MarketingController::class, 'crm'])->name('marketing.crm');
    Route::get('/produkti/hr', [MarketingController::class, 'hr'])->name('marketing.hr');
    Route::get('/faq', [MarketingController::class, 'faq'])->name('marketing.faq');
    Route::get('/kontakt', [MarketingController::class, 'contact'])->name('marketing.contact');
    Route::get('/locale/{locale}', [MarketingController::class, 'setLocale'])->name('marketing.locale');
    Route::post('/kontakt', [MarketingController::class, 'submitContact'])->name('marketing.contact.submit');
});

// Users / auth
Route::controller(UsersController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/check-login', 'checkLogin')->name('check.login');
    Route::post('/register-admin', 'registerAdmin')->name('register.admin');
    Route::get('/logout', 'logout')->name('logout');
});

Route::middleware('payment.expired')->group(function () {

// Users (authenticated)
Route::controller(UsersController::class)->group(function () {
    Route::get('/profile', 'profile')->middleware('authorization:admin,company,manager,employee,finance,hr')->name('profile');
    Route::post('/profile-password', 'updateProfilePassword')->middleware('authorization:admin,company,manager,employee,finance,hr')->name('update.profile.password');
//    Route::post('/profile-language', 'updateCompanyLanguage')->middleware('authorization:company')->name('update.company.language');
});

// Companies
Route::controller(CompaniesController::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/register-company', 'registerCompany')->name('register.company');
    Route::middleware('authorization:admin')->group(function () {
        Route::get('/companies', 'read')->name('companies');
        Route::get('/company-details/{id}', 'details')->name('company.details');
        Route::get('/update-company-status/{id}/{status}', 'updateStatus')->name('update.company.status');
        Route::get('/delete-company/{id}', 'delete')->name('delete.company');
        Route::get('/payments', 'payments')->name('payments');
    });
});

// Dashboard
Route::controller(DashboardController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')
        ->middleware('authorization:admin,company,manager,employee,finance,hr')
        ->name('dashboard');
});

// Check In
Route::middleware('authorization:company,manager,hr,finance,employee')->controller(CheckInController::class)->group(function () {
    Route::get('/checkins', 'read')->name('checkins');
    Route::get('/checkins-map', 'map')->name('checkins.map');
    Route::get('/checkin-details/{id}', 'details')->name('checkin.details');
    Route::get('/accept-checkin/{id}', 'accept')->name('accept.checkin');
    Route::get('/delete-checkin/{id}', 'delete')->name('delete.checkin');
    Route::post('/start-work', 'startWork')->name('start.work');
    Route::post('/start-break', 'startBreak')->name('start.break');
    Route::post('/end-break', 'endBreak')->name('end.break');
    Route::post('/end-work', 'endWork')->name('end.work');
});

// Packages
Route::middleware('authorization:admin')->controller(PackagesController::class)->group(function () {
    Route::post('/create-package', 'create')->name('create.package');
    Route::get('/packages', 'read')->name('packages');
    Route::get('/edit-package/{id}', 'edit')->name('edit.package');
    Route::post('/update-package', 'update')->name('update.package');
    Route::get('/delete-package/{id}', 'delete')->name('delete.package');
});
Route::middleware('authorization:company')->controller(PackagesController::class)->group(function () {
    Route::get('/change-package/{id}', 'change')->name('change.package');
});

// Company News
Route::middleware('authorization:company,manager')->controller(CompanyNewsController::class)->group(function () {
    Route::post('/create-company-news', 'create')->name('create.company.news');
    Route::get('/company-news', 'read')->name('company.news');
    Route::get('/edit-company-news/{id}', 'edit')->name('edit.company.news');
    Route::post('/update-company-news', 'update')->name('update.company.news');
    Route::get('/delete-company-news/{id}', 'delete')->name('delete.company.news');
});

// Clients
Route::middleware('authorization:company,manager,finance')->controller(ClientsController::class)->group(function () {
    Route::post('/create-client', 'create')->name('create.client');
    Route::get('/clients', 'read')->name('clients');
    Route::get('/edit-client/{id}', 'edit')->name('edit.client');
    Route::post('/update-client', 'update')->name('update.client');
    Route::get('/delete-client/{id}', 'delete')->name('delete.client');
});

// Materials
Route::middleware('authorization:company,manager')->controller(MaterialsController::class)->group(function () {
    Route::post('/create-material', 'create')->name('create.material');
    Route::get('/materials', 'read')->name('materials');
    Route::get('/edit-material/{id}', 'edit')->name('edit.material');
    Route::post('/update-material', 'update')->name('update.material');
    Route::get('/delete-material/{id}', 'delete')->name('delete.material');
});

// Materials Stock
Route::middleware('authorization:company,manager')->controller(MaterialsStockController::class)->group(function () {
    Route::post('/create-material-stock', 'create')->name('create.material.stock');
    Route::get('/materials-stock', 'read')->name('materials.stock');
    Route::get('/materials-stock-report', 'report')->name('materials.stock.report');
    Route::get('/edit-material-stock/{id}', 'edit')->name('edit.material.stock');
    Route::post('/update-material-stock', 'update')->name('update.material.stock');
    Route::get('/delete-material-stock/{id}', 'delete')->name('delete.material.stock');
});

// Offers
Route::middleware('authorization:company,finance')->controller(OffersController::class)->group(function () {
    Route::get('/create-offer', 'createForm')->name('create.offer');
    Route::post('/create-offer-client', 'saveClientSession')->name('save.offer.client.session');
    Route::post('/create-offer-details', 'saveDetailsSession')->name('save.offer.details.session');
    Route::post('/clear-offer-session', 'clearSession')->name('clear.offer.session');
    Route::post('/create-offer', 'create')->name('create.offer');
    Route::get('/offers', 'read')->name('offers');
    Route::get('/edit-offer/{offerNumber}', 'edit')->name('edit.offer');
    Route::post('/update-offer-client-info', 'updateClientInfo')->name('update.offer.client.info');
    Route::post('/add-offer-content', 'addContent')->name('add.offer.content');
    Route::get('/delete-offer-content/{id}', 'deleteContent')->name('delete.offer.content');
    Route::get('/delete-offer/{offerNumber}', 'delete')->name('delete.offer');
    Route::get('/offer-invoice/{offerNumber}', 'invoice')->name('offer.invoice');
    Route::get('/update-offer-status/{offerNumber}/{status}', 'updateStatus')->name('update.offer.status');
});

// Suppliers
Route::middleware('authorization:company,manager,finance')->controller(SuppliersController::class)->group(function () {
    Route::post('/create-supplier', 'create')->name('create.supplier');
    Route::get('/suppliers', 'read')->name('suppliers');
    Route::get('/edit-supplier/{id}', 'edit')->name('edit.supplier');
    Route::post('/update-supplier', 'update')->name('update.supplier');
    Route::get('/delete-supplier/{id}', 'delete')->name('delete.supplier');
});

// Financial Transactions
Route::middleware('authorization:company,finance')->controller(FinancialTransactionsController::class)->group(function () {
    Route::post('/create-transaction', 'create')->name('create.transaction');
    Route::get('/transactions', 'read')->name('financial.transactions');
    Route::get('/financial-report', 'report')->name('financial.report');
    Route::get('/edit-transaction/{id}', 'edit')->name('edit.transaction');
    Route::post('/update-transaction', 'update')->name('update.transaction');
    Route::get('/delete-transaction/{id}', 'delete')->name('delete.transaction');
});

// Employees
Route::middleware('authorization:company,hr')->controller(EmployeesController::class)->group(function () {
    Route::post('/create-employee', 'create')->name('create.employee');
    Route::get('/employees', 'read')->name('employees');
    Route::get('/edit-employee/{id}', 'edit')->name('edit.employee');
    Route::post('/update-employee', 'update')->name('update.employee');
    Route::get('/delete-employee/{id}', 'delete')->name('delete.employee');
    Route::get('/block-employee/{id}', 'block')->name('block.employee');
    Route::get('/activate-employee/{id}', 'activate')->name('activate.employee');
});

// Vacations
Route::middleware('authorization:company,manager,hr,employee')->controller(VacationsController::class)->group(function () {
    Route::post('/create-vacation', 'create')->name('create.vacation');
    Route::get('/vacations', 'read')->name('vacations');
    Route::get('/edit-vacation/{id}', 'edit')->name('edit.vacation');
    Route::post('/update-vacation', 'update')->name('update.vacation');
    Route::get('/delete-vacation/{id}', 'delete')->name('delete.vacation');
    Route::get('/update-vacation-status/{id}/{status}', 'updateStatus')->name('update.vacation.status');
});

// Contracts
Route::middleware('authorization:company,hr')->controller(ContractsController::class)->group(function () {
    Route::post('/create-contract', 'create')->name('create.contract');
    Route::get('/edit-contract/{id}', 'edit')->name('edit.contract');
    Route::post('/update-contract', 'update')->name('update.contract');
    Route::get('/delete-contract/{id}', 'delete')->name('delete.contract');
});
Route::middleware('authorization:company,hr,employee')->controller(ContractsController::class)->group(function () {
    Route::get('/contracts', 'read')->name('contracts');
    Route::get('/contract-details/{id}', 'contractDetails')->name('contract.details');
});

// Projects
Route::middleware('authorization:company')->controller(ProjectsController::class)->group(function () {
    Route::post('/create-project', 'create')->name('create.project');
    Route::get('/edit-project/{id}', 'edit')->name('edit.project');
    Route::post('/update-project', 'update')->name('update.project');
    Route::get('/delete-project/{id}', 'delete')->name('delete.project');
    Route::get('/update-project-status/{id}/{status}', 'updateStatus')->name('update.project.status');
});
Route::middleware('authorization:company,manager')->controller(ProjectsController::class)->group(function () {
    Route::get('/projects', 'read')->name('projects');
    Route::get('/project/{id}', 'projectDetails')->name('project.details');
    Route::get('/set-project-context/{id}', 'setProjectContext')->name('set.project.context');
});

// Project Reports
Route::middleware('authorization:company,manager')->controller(ProjectReportsController::class)->group(function () {
    Route::post('/create-project-report', 'create')->name('create.project.report');
    Route::get('/project-reports', 'read')->name('project.reports');
    Route::get('/project-report/{id}', 'reportDetails')->name('project.report.details');
    Route::get('/edit-project-report/{id}', 'edit')->name('edit.project.report');
    Route::post('/update-project-report', 'update')->name('update.project.report');
    Route::get('/delete-project-report/{id}', 'delete')->name('delete.project.report');
    Route::get('/update-project-report-status/{id}/{status}', 'updateStatus')->name('update.project.report.status');
});

// Tasks
Route::middleware('authorization:company,manager')->controller(TasksController::class)->group(function () {
    Route::post('/create-task', 'create')->name('create.task');
    Route::get('/edit-task/{id}', 'edit')->name('edit.task');
    Route::post('/update-task', 'update')->name('update.task');
    Route::get('/delete-task/{id}', 'delete')->name('delete.task');
});
Route::middleware('authorization:company,manager,employee')->controller(TasksController::class)->group(function () {
    Route::get('/tasks', 'read')->name('tasks');
    Route::get('/task/{id}', 'taskDetails')->name('task.details');
    Route::get('/update-task-status/{id}/{status}', 'updateStatus')->name('update.task.status');
});

// Task Reports
Route::middleware('authorization:company,manager,employee')->controller(TaskReportsController::class)->group(function () {
    Route::post('/create-task-report', 'create')->name('create.task.report');
    Route::get('/task-reports', 'read')->name('task.reports');
    Route::get('/task-report/{id}', 'details')->name('task.report.details');
    Route::get('/edit-task-report/{id}', 'edit')->name('edit.task.report');
    Route::post('/update-task-report', 'update')->name('update.task.report');
    Route::get('/delete-task-report/{id}', 'delete')->name('delete.task.report');
    Route::get('/update-task-report-status/{id}/{status}', 'updateStatus')->name('update.task.report.status');
});

// Working Hours
Route::middleware('authorization:company,manager,hr')->controller(WorkingHoursController::class)->group(function () {
    Route::post('/create-working-hour', 'create')->name('create.working.hour');
    Route::get('/delete-working-hour/{id}', 'delete')->name('delete.working.hour');
});
Route::middleware('authorization:company,manager,hr,employee')->controller(WorkingHoursController::class)->group(function () {
    Route::get('/working-hours', 'read')->name('working.hours');
});

// Notifications
Route::middleware('authorization:company,manager,employee,finance,hr')->controller(NotificationsController::class)->group(function () {
    Route::get('/notifications', 'read')->name('notifications');
    Route::post('/notifications/open/{id}', 'open')->name('notifications.open');
    Route::post('/notifications/mark-all-read', 'markAllRead')->name('notifications.mark.all');
});

// Salaries
Route::middleware('authorization:company,finance')->controller(SalariesController::class)->group(function () {
    Route::post('/create-salary', 'create')->name('create.salary');
    Route::get('/edit-salary/{id}', 'edit')->name('edit.salary');
    Route::post('/update-salary', 'update')->name('update.salary');
    Route::get('/delete-salary/{id}', 'delete')->name('delete.salary');
    Route::get('/calculate-salary', 'calculateSalary')->name('calculate.salary');
    Route::get('/calculate', 'calculate')->name('calculate');
    Route::post('/save-calculated-salaries', 'saveCalculated')->name('save.calculated.salaries');
});
Route::middleware('authorization:company,finance,employee')->controller(SalariesController::class)->group(function () {
    Route::get('/salaries', 'read')->name('salaries');
});

// Extra Hours
Route::middleware('authorization:company,manager,hr')->controller(ExtraHoursController::class)->group(function () {
    Route::post('/create-extra-hour', 'create')->name('create.extra.hour');
    Route::get('/edit-extra-hour/{id}', 'edit')->name('edit.extra.hour');
    Route::post('/update-extra-hour', 'update')->name('update.extra.hour');
    Route::get('/delete-extra-hour/{id}', 'delete')->name('delete.extra.hour');
});
Route::middleware('authorization:company,manager,hr,employee')->controller(ExtraHoursController::class)->group(function () {
    Route::get('/extra-hours', 'read')->name('extra.hours');
});

// Reports
Route::middleware('authorization:company,manager,employee,hr')->controller(ReportsController::class)->group(function () {
    Route::get('/employee-report', 'read')->name('employee.report');
    Route::get('/generate-employee-report', 'generate')->name('generate.employee.report');
});

}); // payment.expired

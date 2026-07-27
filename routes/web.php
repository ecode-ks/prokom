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
use App\Http\Controllers\WorkingHoursController;

// Users
Route::controller(UsersController::class)->group(function () {
    Route::get('/', 'login')->name('login');
    Route::post('/check-login', 'checkLogin')->name('check.login');
    Route::post('/register-admin', 'registerAdmin')->name('register.admin');
    Route::get('/logout', 'logout')->name('logout');
    Route::get('/profile', 'profile')->name('profile');
    Route::post('/profile-password', 'updateProfilePassword')->name('update.profile.password');
});

// Companies
Route::controller(CompaniesController::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/register-company', 'registerCompany')->name('register.company');
    Route::get('/companies', 'read')->name('companies');
    Route::get('/company-details/{id}', 'details')->name('company.details');
    Route::get('/update-company-status/{id}/{status}', 'updateStatus')->name('update.company.status');
    Route::get('/delete-company/{id}', 'delete')->name('delete.company');
});

// Dashboard
Route::controller(DashboardController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
});

// Check In
Route::controller(CheckInController::class)->group(function () {
    Route::post('/start-work', 'startWork')->name('start.work');
    Route::post('/start-break', 'startBreak')->name('start.break');
    Route::post('/end-break', 'endBreak')->name('end.break');
    Route::post('/end-work', 'endWork')->name('end.work');
});

// Packages
Route::controller(PackagesController::class)->group(function () {
    Route::post('/create-package', 'create')->name('create.package');
    Route::get('/packages', 'read')->name('packages');
    Route::get('/edit-package/{id}', 'edit')->name('edit.package');
    Route::post('/update-package', 'update')->name('update.package');
    Route::get('/delete-package/{id}', 'delete')->name('delete.package');
});

// Company News
Route::controller(CompanyNewsController::class)->group(function () {
    Route::post('/create-company-news', 'create')->name('create.company.news');
    Route::get('/company-news', 'read')->name('company.news');
    Route::get('/edit-company-news/{id}', 'edit')->name('edit.company.news');
    Route::post('/update-company-news', 'update')->name('update.company.news');
    Route::get('/delete-company-news/{id}', 'delete')->name('delete.company.news');
});

// Clients
Route::controller(ClientsController::class)->group(function () {
    Route::post('/create-client', 'create')->name('create.client');
    Route::get('/clients', 'read')->name('clients');
    Route::get('/edit-client/{id}', 'edit')->name('edit.client');
    Route::post('/update-client', 'update')->name('update.client');
    Route::get('/delete-client/{id}', 'delete')->name('delete.client');
});

// Materials
Route::controller(MaterialsController::class)->group(function () {
    Route::post('/create-material', 'create')->name('create.material');
    Route::get('/materials', 'read')->name('materials');
    Route::get('/edit-material/{id}', 'edit')->name('edit.material');
    Route::post('/update-material', 'update')->name('update.material');
    Route::get('/delete-material/{id}', 'delete')->name('delete.material');
});

// Materials Stock
Route::controller(MaterialsStockController::class)->group(function () {
    Route::post('/create-material-stock', 'create')->name('create.material.stock');
    Route::get('/materials-stock', 'read')->name('materials.stock');
    Route::get('/materials-stock-report', 'report')->name('materials.stock.report');
    Route::get('/edit-material-stock/{id}', 'edit')->name('edit.material.stock');
    Route::post('/update-material-stock', 'update')->name('update.material.stock');
    Route::get('/delete-material-stock/{id}', 'delete')->name('delete.material.stock');
});

// Offers
Route::controller(OffersController::class)->group(function () {
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
Route::controller(SuppliersController::class)->group(function () {
    Route::post('/create-supplier', 'create')->name('create.supplier');
    Route::get('/suppliers', 'read')->name('suppliers');
    Route::get('/edit-supplier/{id}', 'edit')->name('edit.supplier');
    Route::post('/update-supplier', 'update')->name('update.supplier');
    Route::get('/delete-supplier/{id}', 'delete')->name('delete.supplier');
});

// Financial Transactions
Route::controller(FinancialTransactionsController::class)->group(function () {
    Route::post('/create-transaction', 'create')->name('create.transaction');
    Route::get('/transactions', 'read')->name('financial.transactions');
    Route::get('/financial-report', 'report')->name('financial.report');
    Route::get('/edit-transaction/{id}', 'edit')->name('edit.transaction');
    Route::post('/update-transaction', 'update')->name('update.transaction');
    Route::get('/delete-transaction/{id}', 'delete')->name('delete.transaction');
});

// Employees
Route::controller(EmployeesController::class)->group(function () {
    Route::post('/create-employee', 'create')->name('create.employee');
    Route::get('/employees', 'read')->name('employees');
    Route::get('/edit-employee/{id}', 'edit')->name('edit.employee');
    Route::post('/update-employee', 'update')->name('update.employee');
    Route::get('/delete-employee/{id}', 'delete')->name('delete.employee');
    Route::get('/block-employee/{id}', 'block')->name('block.employee');
    Route::get('/activate-employee/{id}', 'activate')->name('activate.employee');
});

// Vacations
Route::controller(VacationsController::class)->group(function () {
    Route::post('/create-vacation', 'create')->name('create.vacation');
    Route::get('/vacations', 'read')->name('vacations');
    Route::get('/edit-vacation/{id}', 'edit')->name('edit.vacation');
    Route::post('/update-vacation', 'update')->name('update.vacation');
    Route::get('/delete-vacation/{id}', 'delete')->name('delete.vacation');
    Route::get('/update-vacation-status/{id}/{status}', 'updateStatus')->name('update.vacation.status');
});

// Contracts
Route::controller(ContractsController::class)->group(function () {
    Route::post('/create-contract', 'create')->name('create.contract');
    Route::get('/contracts', 'read')->name('contracts');
    Route::get('/contract-details/{id}', 'contractDetails')->name('contract.details');
    Route::get('/edit-contract/{id}', 'edit')->name('edit.contract');
    Route::post('/update-contract', 'update')->name('update.contract');
    Route::get('/delete-contract/{id}', 'delete')->name('delete.contract');
});
// Working Hours
Route::controller(WorkingHoursController::class)->group(function () {
    Route::post('/create-working-hour', 'create')->name('create.working.hour');
    Route::get('/working-hours', 'read')->name('working.hours');
    Route::get('/delete-working-hour/{id}', 'delete')->name('delete.working.hour');
});

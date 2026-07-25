<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\MaterialsController;
use App\Http\Controllers\MaterialsStockController;
use App\Http\Controllers\CompanyNewsController;
use App\Http\Controllers\OffersController;

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

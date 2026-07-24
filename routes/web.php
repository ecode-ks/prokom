<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackagesController;

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

// Clients
Route::controller(ClientsController::class)->group(function () {
    Route::get('/clients', 'index')->name('clients');
    Route::post('/clients', 'create')->name('clients.create');
    Route::get('/clients/{id}', 'edit')->name('clients.edit');
    Route::put('/clients/{id}', 'update')->name('clients.update');
    Route::delete('/clients/{id}', 'delete')->name('clients.delete');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

// Users
Route::controller(UsersController::class)->group(function () {
    Route::get('/', 'login')->name('login');
    Route::post('/check-login', 'checkLogin')->name('check.login');
    Route::post('/register-admin', 'registerAdmin')->name('register.admin');
    Route::get('/logout', 'logout')->name('logout');
}); 

// Clients
Route::controller(ClientsController::class)->group(function () {
    Route::get('/clients', 'index')->name('clients');
    Route::post('/clients', 'create')->name('clients.create');
    Route::get('/clients/{id}', 'edit')->name('clients.edit');
    Route::put('/clients/{id}', 'update')->name('clients.update');
    Route::delete('/clients/{id}', 'delete')->name('clients.delete');
});

<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.auth');
});

Route::get('/admin', function () {
    return view('layouts.app');
});

Route::get('/employee', function () {
    return view('employees.index');
});

Route::get('/product', function () {
    return view('products.index');
});

Route::get('/categories', function () {
    return view('categories.index');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin', function () {
    return view('layouts.app');
})->middleware('auth')->name('admin');
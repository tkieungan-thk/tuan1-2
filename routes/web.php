<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.auth');
});

Route::get('/admin', function () {
    return view('layouts.app');
});

Route::resource('users', UserController::class)->except(['show']);
Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');

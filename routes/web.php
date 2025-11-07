<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.auth');
});

Route::get('/admin', function () {
    return view('layouts.app');
});

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
Route::patch('/users/status/{user}', [UserController::class, 'updateStatus'])
    ->name('users.updateStatus');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
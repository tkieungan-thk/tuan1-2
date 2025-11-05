<?php

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


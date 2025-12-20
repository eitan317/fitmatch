<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/trainers', function () {
    return view('trainers');
});

Route::get('/trainer-profile', function () {
    return view('trainer-profile');
});

Route::get('/admin', function () {
    return view('admin');
});

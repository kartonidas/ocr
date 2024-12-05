<?php

use Illuminate\Support\Facades\Route;

Auth::routes([
    'register' => false,
    'reset' => false,
]);

Route::group(['middleware' => 'auth'], function () {
    Route::view('/', 'home')->name('home');
});


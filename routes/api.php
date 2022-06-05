<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::resource('bookings', \App\Http\Controllers\BookingController::class);
    Route::post('/bookings/calculate', [\App\Http\Controllers\BookingController::class, 'calculateSpace']);
});

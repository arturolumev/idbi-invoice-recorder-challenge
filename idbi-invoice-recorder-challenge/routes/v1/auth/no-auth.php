<?php

use App\Http\Controllers\Auth\LoginHandler;
use App\Http\Controllers\Auth\LogoutHandler;
use App\Http\Controllers\Auth\SignUpHandler;
// use App\Http\Controllers\Montos\GetMontosHandler;
use Illuminate\Support\Facades\Route;

// Route::get('/montos', action: GetMontosHandler::class);
Route::post('/users', SignUpHandler::class);
Route::post('/login', LoginHandler::class);
Route::post('/logout', LogoutHandler::class)
    ->middleware('jwt.verify');
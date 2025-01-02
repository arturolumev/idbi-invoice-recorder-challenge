<?php

use App\Http\Controllers\Montos\GetMontosHandler;
use Illuminate\Support\Facades\Route;

// Route::prefix('montos')->group(
//     function () {
//         Route::get('/', GetMontosHandler::class);
//     }
// );

Route::get('/montos', action: GetMontosHandler::class);

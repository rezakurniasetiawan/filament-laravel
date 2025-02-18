<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::get('/', function () {
    return view('welcome');
});


// Route::prefix('users')->group(function () {
//     Route::post('/login', [AuthController::class, 'login']);
// });
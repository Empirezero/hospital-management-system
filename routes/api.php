<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MpesaCallbackController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/mpesa/callback', [MpesaCallbackController::class, 'handle'])
    ->name('mpesa.callback');

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirtimeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('vend')->group(function() {
    Route::post('recharge/{type}', [AirtimeController::class, 'recharge']);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsPaymentController;
use App\Http\Controllers\AdPaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Package ID endpoint (bez CSRF middleware)  
Route::get('/package-id/{type}/{duration}', [AdPaymentController::class, 'getPackageId'])->name('api.package-id');

// Test route
Route::get('/sms/test-route', function() {
    return response()->json(['message' => 'API SMS route works!']);
}); 
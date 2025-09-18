<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/vnpay/ipn-callback', [PaymentController::class, 'vnpayIPNCallback']);

Route::group([
    'middleware' => 'jwt',
    'prefix' => 'auth'
], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/vnpay/create-payment', [PaymentController::class, 'createPayment']);
    // Route::get('/vnpay/payment-return', [PaymentController::class, 'paymentReturn']);
    Route::get('/vnpay/payment-history', [PaymentController::class, 'paymentHistory']);
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb']);
Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

Route::middleware('jwt')->group(function () {
    Route::get('/', function () {
        return view('payments.index');
    });
    Route::get('/payments/create', [PaymentController::class, 'showCreateForm']);
    Route::post('/payments/create', [PaymentController::class, 'createPaymentWeb']);
    Route::get('/payments/history', [PaymentController::class, 'paymentHistoryWeb']);
    Route::get('/payments/retry/{code}', [PaymentController::class, 'retryPaymentWeb']);
});

Route::get('/return-vnpay', [PaymentController::class, 'paymentReturn']);
Route::get('/payments/ipn-callback', [PaymentController::class, 'vnpayIPNCallbackWeb']);

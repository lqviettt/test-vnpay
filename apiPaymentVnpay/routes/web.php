<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/return-vnpay', [PaymentController::class, 'paymentReturn']);


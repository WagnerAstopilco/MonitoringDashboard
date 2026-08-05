<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionPaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('users', UsersController::class);
Route::apiResource('clients', ClientController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('promotions',PromotionController::class);
Route::apiResource('payment-methods', PaymentMethodController::class);
Route::apiResource('transactions', TransactionController::class);
Route::apiResource('transaction-payments', TransactionPaymentController::class);
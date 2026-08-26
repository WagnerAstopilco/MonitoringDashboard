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
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\DB;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando'
    ]);
});
Route::get('/test-db', function () {
    return response()->json([
        'database' => DB::connection()->getDatabaseName(),
        'status' => 'Conexión correcta'
    ]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::get('/services/publicServices', [ServiceController::class, 'publicServices']);
Route::get('/services/publicServices/{service}', [ServiceController::class, 'showPublicService']);
Route::get('/promotions/publicPromotions', [PromotionController::class, 'publicPromotions']);
Route::get('/promotions/publicPromotions/{promotion}', [PromotionController::class, 'showPublicPromotion']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route::post('/forgot-password', [AuthController::class, 'recoveryPassword']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::patch('/me', [AuthController::class, 'updateProfile']);
});

//rutas de usuarios
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UsersController::class, 'index'])
        ->middleware('permission:users.view');
    Route::post('/users', [UsersController::class, 'store'])
        ->middleware('permission:users.create');
    Route::get('/users/{user}', [UsersController::class, 'show'])
        ->middleware('permission:users.view');
    Route::put('/users/{user}', [UsersController::class, 'update'])
        ->middleware('permission:users.edit');
    Route::patch('/users/{user}', [UsersController::class, 'update'])
        ->middleware('permission:users.edit');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])
        ->middleware('permission:users.delete');
    Route::patch('/users/{user}/status', [UsersController::class, 'changeStatus'])
        ->middleware('permission:users.change_status');
    Route::patch('/users/{user}/resp', [AuthController::class, 'resetPassword'])
        ->middleware('permission:users.reset_password');
});

//rutas de clientes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/clients/search', [ClientController::class, 'searchByRuc'])
        ->middleware('permission:clients.search');
    Route::get('/clients', [ClientController::class, 'index'])
        ->middleware('permission:clients.view');
    Route::post('/clients', [ClientController::class, 'store'])
        ->middleware('permission:clients.create');
    Route::get('/clients/{client}', [ClientController::class, 'show'])
        ->middleware('permission:clients.view');
    Route::put('/clients/{client}', [ClientController::class, 'update'])
        ->middleware('permission:clients.edit');
    Route::patch('/clients/{client}', [ClientController::class, 'update'])
        ->middleware('permission:clients.edit');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])
        ->middleware('permission:clients.delete');
});

//rutas de servicios
Route::middleware('auth:sanctum')->group(function () {
    Route::patch('/services/{service}/status', [ServiceController::class, 'changeStatus'])
        ->middleware('permission:services.change_status');
    Route::get('/services', [ServiceController::class, 'index'])
        ->middleware('permission:services.view');
    Route::post('/services', [ServiceController::class, 'store'])
        ->middleware('permission:services.create');
    Route::get('/services/{service}', [ServiceController::class, 'show'])
        ->middleware('permission:services.view');
    Route::patch('/services/{service}/promotions', [ServiceController::class, 'syncPromotions'])
        ->middleware('permission:services.sync_promotions');
    Route::put('/services/{service}', [ServiceController::class, 'update'])
        ->middleware('permission:services.edit');
    Route::patch('/services/{service}', [ServiceController::class, 'update'])
        ->middleware('permission:services.edit');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])
        ->middleware('permission:services.delete');
});

//rutas de promociones
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/promotions', [PromotionController::class, 'index'])
        ->middleware('permission:promotions.view');
    Route::post('/promotions', [PromotionController::class, 'store'])
        ->middleware('permission:promotions.create');
    Route::get('/promotions/{promotion}', [PromotionController::class, 'show'])
        ->middleware('permission:promotions.view');
    Route::patch('/promotions/{promotion}/status', [PromotionController::class, 'changeStatus'])
        ->middleware('permission:promotions.change_status');
    Route::patch('/promotions/{promotion}/services', [PromotionController::class, 'syncServices'])
        ->middleware('permission:promotions.sync_services');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])
        ->middleware('permission:promotions.edit');
    Route::patch('/promotions/{promotion}', [PromotionController::class, 'update'])
        ->middleware('permission:promotions.edit');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])
        ->middleware('permission:promotions.delete');
});

//rutas de medios de pago
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])
        ->middleware('permission:payment_methods.view');
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])
        ->middleware('permission:payment_methods.create');
    Route::get('/payment-methods/{payment_method}', [PaymentMethodController::class, 'show'])
        ->middleware('permission:payment_methods.view');
    Route::put('/payment-methods/{payment_method}', [PaymentMethodController::class, 'update'])
        ->middleware('permission:payment_methods.edit');
    Route::patch('/payment-methods/{payment_method}', [PaymentMethodController::class, 'update'])
        ->middleware('permission:payment_methods.edit');
    Route::delete('/payment-methods/{payment_method}', [PaymentMethodController::class, 'destroy'])
        ->middleware('permission:payment_methods.delete');
});

//rutas de transacciones
Route::middleware('auth:sanctum')->group(function () {
    Route::patch('/transactions/{transaction}/delivery', [TransactionController::class, 'changeDeliveryStatus'])
        ->middleware('permission:transactions.change_delivery_status');
    Route::get('/transactions/export', [TransactionController::class, 'export'])
        ->middleware('permission:transactions.view');
    Route::get('transactions/reports', [TransactionController::class, 'reports'])
        ->middleware('permission:transactions.reports');
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->middleware('permission:transactions.view');
    Route::post('/transactions', [TransactionController::class, 'store'])
        ->middleware('permission:transactions.create');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->middleware('permission:transactions.view');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])
        ->middleware('permission:transactions.edit');
    Route::patch('/transactions/{transaction}', [TransactionController::class, 'update'])
        ->middleware('permission:transactions.edit');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])
        ->middleware('permission:transactions.delete');
});

//rutas de pagos
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/transaction-payments', [TransactionPaymentController::class, 'index'])
        ->middleware('permission:payments.view');
    Route::post('/transaction-payments', [TransactionPaymentController::class, 'store'])
        ->middleware('permission:payments.create');
    Route::get('/tramsaction-payments/{transaction_payment}', [TransactionPaymentController::class, 'show'])
        ->middleware('permission:payments.view');
    Route::put('/transaction-payments/{transaction_payment}', [TransactionPaymentController::class, 'update'])
        ->middleware('permission:payment.edit');
    Route::patch('/transaction-payments/{transaction_payment}', [TransactionPaymentController::class, 'update'])
        ->middleware('permission:payments.edit');
    Route::delete('/transaction-payments/transaction_payment', [TransactionPaymentController::class, 'destroy'])
        ->middleware('permission:payment.delete');
});

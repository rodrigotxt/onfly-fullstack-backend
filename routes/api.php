<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    // Pedidos de viagem
    Route::get('/travel-orders', [TravelOrderController::class, 'index']);
    Route::post('/travel-orders', [TravelOrderController::class, 'store']);
    Route::get('/travel-orders/{id}', [TravelOrderController::class, 'show']);
    Route::put('/travel-orders/{id}/status', [TravelOrderController::class, 'updateStatus']);
    
    // Notificações
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
});
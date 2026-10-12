<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Kargo Takip & Yönetim Sistemi
|--------------------------------------------------------------------------
*/

// Ana Sayfa - Kargo Takip Portalı
Route::get('/', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/takip/ara', [TrackingController::class, 'search'])->name('tracking.search');

// Tam Kaynak CRUD Rotaları (Customer, Status, Order, Shipment)
Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::resource('customers', CustomerController::class);
Route::resource('statuses', StatusController::class);
Route::resource('orders', OrderController::class);
Route::resource('shipments', ShipmentController::class);

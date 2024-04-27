<?php

use App\Modules\Order\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('order', [OrderController::class, 'store']);
Route::post('order/{trackerId}', [OrderController::class, 'get']);

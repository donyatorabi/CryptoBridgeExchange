<?php

use App\Modules\Coin\Controllers\CoinController;
use Illuminate\Support\Facades\Route;

Route::get('coins/update', [CoinController::class, 'get']);

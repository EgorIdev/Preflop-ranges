<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RangeController;

Route::get('/ranges', [RangeController::class, 'index']);

Route::post('/ranges', [RangeController::class, 'store']);
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RangeController;
use App\Http\Controllers\Api\RangeGroupController;

Route::get('/ranges', [RangeController::class, 'index']);

Route::post('/ranges', [RangeController::class, 'store']);

Route::post(
    '/ranges/{range}/items',
    [RangeController::class, 'saveItems']
);

Route::delete(
    '/ranges/{range}',
    [RangeController::class, 'destroy']
);

Route::post(
    '/ranges/{range}/copy',
    [RangeController::class, 'copy']
);

Route::get(
    '/range-groups',
    [RangeGroupController::class, 'index']
);

Route::post(
    '/range-groups',
    [RangeGroupController::class, 'store']
);
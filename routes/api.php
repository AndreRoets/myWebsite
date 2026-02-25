<?php

use App\Http\Controllers\Api\ListingSyncController;
use Illuminate\Support\Facades\Route;

Route::middleware('validate.sync.token')->group(function () {
    Route::post('/listings/sync', [ListingSyncController::class, 'sync']);
    Route::delete('/listings/{externalId}', [ListingSyncController::class, 'destroy']);
});

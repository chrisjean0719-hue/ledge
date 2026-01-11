<?php

use App\Http\Controllers\BedController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [BedController::class, 'health']);
Route::get('/sample', [BedController::class, 'sample']);
Route::post('/beds', [BedController::class, 'store']);

<?php

use App\Http\Controllers\BedController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [BedController::class, 'health']);
Route::get('/sample', [BedController::class, 'sample']);
Route::get('/sample/elevation', [BedController::class, 'sampleElevation']);
Route::post('/beds', [BedController::class, 'store']);
Route::post('/beds/elevation', [BedController::class, 'elevation']);
Route::post('/beds/compare', [BedController::class, 'compare']);

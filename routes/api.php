<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

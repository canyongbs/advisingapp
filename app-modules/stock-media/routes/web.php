<?php

use AdvisingApp\StockMedia\Http\Controllers\StockImagesController;
use Illuminate\Support\Facades\Route;

Route::post('/api/stock-images', StockImagesController::class)->middleware(['signed'])->name('api.stock-images');

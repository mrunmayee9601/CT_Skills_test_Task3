<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController; 


Route::get('/', [ProductController::class, 'index']);


// A GET request to /api/products will call the fetchProducts method
Route::get('/products', [ProductController::class, 'fetchProducts']);

// A POST request to /api/products will call the store method
Route::post('/products', [ProductController::class, 'store']);

//The update route to edit the product 
Route::put('/products/{id}', [ProductController::class, 'update']);
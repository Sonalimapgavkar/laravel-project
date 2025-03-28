<?php
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index']); // Get all products
Route::post('/products', [ProductController::class, 'store']); // Create a product
Route::get('/products/{id}', [ProductController::class, 'show']); // Get a single product
Route::put('/products/{id}', [ProductController::class, 'update']); // Update a product
Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete a product


Route::post('/cart', [CartController::class, 'addToCart']);
Route::get('/cart', [CartController::class, 'getCartItems']);
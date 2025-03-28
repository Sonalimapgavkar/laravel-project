<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;

Route::get('/products', function () {
    return view('products');
});

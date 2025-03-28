<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function getAllProducts()
    {
        $products = Product::with('images')->get(); // Fetch products with images
        return response()->json($products);
    }
}


<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        // Validate request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        // Check if product exists
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Add product to cart
        $cart = Cart::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity ?? 1,
        ]);

        return response()->json(['message' => 'Product added to cart!', 'cart' => $cart], 201);
    }
    public function getCartItems()
    {
        $cartItems = Cart::with('product.images')->where('user_id', 1)->get();
        
        return response()->json($cartItems);
    }
}

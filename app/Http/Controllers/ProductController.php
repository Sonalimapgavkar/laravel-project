<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Get all products
    public function index()
    {
        return response()->json(Product::with('images')->get());
    }

    // Get single product
    public function show($id)
    {
        $product = Product::with('images')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    // Create a new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path
                ]);
            }
        }

        return response()->json(['message' => 'Product created successfully']);
    }

    // Update product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product->update($request->only(['name', 'price']));

        if ($request->hasFile('images')) {
            foreach ($product->images as $image) {
                Storage::delete('public/' . $image->image_path);
                $image->delete();
            }

            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path
                ]);
            }
        }

        return response()->json(['message' => 'Product updated successfully']);
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        foreach ($product->images as $image) {
            Storage::delete('public/' . $image->image_path);
            $image->delete();
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
   
    public function getAllProducts()
    {
        $products = Product::with('images')->get(); // Ensure 'images' relationship exists in Product model
        return response()->json($products);
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController
{
    public function index()
    {
        return response()->json(Product::with('category')->get());
    }

    public function show($id)
    {
        return response()->json(Product::with('category')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string',
            'price'       => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock'       => 'required|integer',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        } elseif ($request->image_url) {
            $imagePath = $request->image_url;
        }

        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category_id' => $request->category_id,
            'image'       => $imagePath,
        ]);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $imagePath = $product->image;

    if ($request->hasFile('image')) {
        if ($imagePath && !str_starts_with($imagePath, 'http')) {
            Storage::disk('public')->delete($imagePath);
        }
        $imagePath = $request->file('image')->store('products', 'public');
    } elseif ($request->filled('image_url')) {
        $imagePath = $request->image_url;
    }

    $product->update([
        'name'        => $request->name ?? $product->name,
        'description' => $request->description ?? $product->description,
        'price'       => $request->price ?? $product->price,
        'stock'       => $request->stock ?? $product->stock,
        'category_id' => $request->category_id ?? $product->category_id,
        'image'       => $imagePath,
    ]);

    return response()->json($product);
}

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && !str_starts_with($product->image, 'http')) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return response()->json(['message' => 'Produit supprimé']);
    }
}
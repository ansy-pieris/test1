<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->orderBy('created_at', 'desc')->get();
        $categories = Category::all();

        return view('admin.products', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:100',
            'description' => 'required|string|min:10|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->category_id = $request->category_id;
            $product->added_by = Auth::id();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/products', $imageName);
                $product->image = $imageName;
            }

            $product->save();

            if ($request->wantsJson()) {
                return response()->json(['success' => 'Product added successfully!']);
            }

            return redirect()->route('admin.products')->with('success', 'Product added successfully!');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to add product. Please try again.'], 500);
            }

            return redirect()->back()->with('error', 'Failed to add product. Please try again.');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'name' => 'required|string|min:2|max:100',
            'description' => 'required|string|min:10|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $product = Product::where('product_id', $request->product_id)->firstOrFail();
            
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->category_id = $request->category_id;

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image && Storage::exists('public/products/' . $product->image)) {
                    Storage::delete('public/products/' . $product->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/products', $imageName);
                $product->image = $imageName;
            }

            $product->save();

            if ($request->wantsJson()) {
                return response()->json(['success' => 'Product updated successfully!']);
            }

            return redirect()->route('admin.products')->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to update product. Please try again.'], 500);
            }

            return redirect()->back()->with('error', 'Failed to update product. Please try again.');
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id'
        ]);

        try {
            $product = Product::where('product_id', $request->product_id)->firstOrFail();
            
            // Delete image if exists
            if ($product->image && Storage::exists('public/products/' . $product->image)) {
                Storage::delete('public/products/' . $product->image);
            }

            $product->delete();

            if ($request->wantsJson()) {
                return response()->json(['success' => 'Product deleted successfully!']);
            }

            return redirect()->route('admin.products')->with('success', 'Product deleted successfully!');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to delete product. Please try again.'], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete product. Please try again.');
        }
    }
}
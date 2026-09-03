<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tax;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('tax')->orderBy('id', 'desc')->get();
        $taxes = Tax::orderBy('name', 'asc')->get();
        return view('admin.products.index', compact('products', 'taxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'tax_id' => $request->tax_id,
        ]);

        return redirect()->route('products.index')->with('success', 'Product/Service created successfully.');
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'tax_id' => $request->tax_id,
        ]);

        $product->load('tax');

        return response()->json([
            'success' => true,
            'message' => 'Product/Service created successfully.',
            'product' => $product,
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'tax_id' => $request->tax_id,
        ]);

        return redirect()->route('products.index')->with('success', 'Product/Service updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product/Service deleted successfully.');
    }

    public function getProductsAjax()
    {
        $products = Product::with('tax')->orderBy('name', 'asc')->get();
        return response()->json(['products' => $products]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::orderBy('id', 'desc')->get();
        return view('admin.taxes.index', compact('taxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        Tax::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'description' => $request->description ?: ($request->name . ' (' . $request->rate . '%)'),
        ]);

        return redirect()->route('taxes.index')->with('success', 'Tax created successfully.');
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $tax = Tax::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'description' => $request->description ?: ($request->name . ' (' . $request->rate . '%)'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tax created successfully.',
            'tax' => $tax,
        ]);
    }

    public function update(Request $request, $id)
    {
        $tax = Tax::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $tax->update([
            'name' => $request->name,
            'rate' => $request->rate,
            'description' => $request->description ?: ($request->name . ' (' . $request->rate . '%)'),
        ]);

        return redirect()->route('taxes.index')->with('success', 'Tax updated successfully.');
    }

    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);
        $tax->delete();

        return redirect()->route('taxes.index')->with('success', 'Tax deleted successfully.');
    }
}

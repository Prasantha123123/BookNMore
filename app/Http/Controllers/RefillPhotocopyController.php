<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RefillPhotocopy;
use App\Models\Product;

class RefillPhotocopyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('refillphotocopy.index', [
            'refills' => RefillPhotocopy::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        // Add refill record
        $refill = RefillPhotocopy::create([
            'product_id' => $validated['product_id'],
            'product_name' => $validated['product_name'],
            'quantity' => $validated['quantity'],
            'stock' => $validated['quantity'],
        ]);

        // Update total stock in refill table
        $totalStock = RefillPhotocopy::where('product_id', $validated['product_id'])->sum('stock');
        RefillPhotocopy::where('id', $refill->id)->update(['stock' => $totalStock]);

        // Deduct the refill quantity from the product's available stock
        $product = Product::find($validated['product_id']);
        if ($product) {
            $product->stock_quantity -= $validated['quantity'];
            $product->save();
        }

        return response()->json(['message' => 'Refill added successfully and total stock updated'], 201);
    }
}
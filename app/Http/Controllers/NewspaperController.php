<?php

namespace App\Http\Controllers;

use App\Models\Newspaper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewspaperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $newspapers = Newspaper::paginate(10); // Adjust the number of items per page
        return Inertia::render('Newspaper/Index', [
            'newspapers' => $newspapers,
            'totalNewspapers' => Newspaper::count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Newspaper/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'productcode' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'batch_no' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'duration' => 'required|in:monthly,weekly',
            'publication_date' => 'required|date',
            'expire_date' => 'required|date',
            'language' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric'
        ]);

        Newspaper::create($validated);

        return redirect()->route('newspapers.index')->with('success', 'Newspaper created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Newspaper $newspaper)
    {
        return Inertia::render('Newspaper/Show', [
            'newspaper' => $newspaper,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Newspaper $newspaper)
    {
        return Inertia::render('Newspaper/Edit', [
            'newspaper' => $newspaper,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Newspaper $newspaper)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'productcode' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'batch_no' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'duration' => 'required|in:monthly,weekly',
            'publication_date' => 'required|date',
            'expire_date' => 'required|date',
            'language' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric',
        ]);

        $newspaper->update($validated);

        return redirect()->route('newspapers.index')->with('success', 'Newspaper updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newspaper $newspaper)
    {
        $newspaper->delete();

        return redirect()->route('newspapers.index')->with('success', 'Newspaper deleted successfully.');
    }
}
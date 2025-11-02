<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaminatingService;
use Inertia\Inertia;

class LaminatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check for AJAX/JSON requests more reliably
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json(LaminatingService::all());
        }

        return Inertia::render('Laminating/Index', [
            'laminatingServices' => LaminatingService::paginate(10),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pouch_size' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'service_amount' => 'required|numeric|min:0',
        ]);

        LaminatingService::create($validated);

        return redirect()->back()->with('success', 'Service created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, LaminatingService $laminatingService)
    {
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json($laminatingService);
        }

        return Inertia::render('Laminating/Show', [
            'laminatingService' => $laminatingService,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LaminatingService $laminatingService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pouch_size' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'service_amount' => 'required|numeric|min:0',
        ]);

        $laminatingService->update($validated);

        return redirect()->back()->with('success', 'Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaminatingService $laminatingService)
    {
        $laminatingService->delete();

        return redirect()->back()->with('success', 'Service deleted successfully');
    }
}
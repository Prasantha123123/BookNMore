<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PrintoutService;

class PrintoutController extends Controller
{
    /**
     * Display a listing of the printout services.
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $printoutServices = PrintoutService::all();
            return response()->json($printoutServices);
        }

        $printoutServices = PrintoutService::paginate(10);
        return Inertia::render('Services/Printout', [
            'printoutServices' => $printoutServices,
        ]);
    }

    /**
     * Store a newly created printout service in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'required|string',
            'side' => 'required|string',
            'pages' => 'required|string',
            'color' => 'required|string',
            'price' => 'required|numeric|min:0',
            'service_charge' => 'required|numeric|min:0',
        ]);

        $service = PrintoutService::create($request->all());

        if ($request->expectsJson()) {
            return response()->json($service, 201);
        }

        return redirect()->back()->with('success', 'Printout service created successfully.');
    }

    /**
     * Update the specified printout service in storage.
     */
    public function update(Request $request, PrintoutService $printoutService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'required|string',
            'side' => 'required|string',
            'pages' => 'required|string',
            'color' => 'required|string',
            'price' => 'required|numeric|min:0',
            'service_charge' => 'required|numeric|min:0',
        ]);

        $printoutService->update($request->all());

        if ($request->expectsJson()) {
            return response()->json($printoutService);
        }

        return redirect()->back()->with('success', 'Printout service updated successfully.');
    }

    /**
     * Remove the specified printout service from storage.
     */
    public function destroy(PrintoutService $printoutService)
    {
        $printoutService->delete();

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->back()->with('success', 'Printout service deleted successfully.');
    }
}
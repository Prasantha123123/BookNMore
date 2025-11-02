<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\BindingService;

class BindingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $bindingServices = BindingService::all();
            return response()->json($bindingServices);
        }

        $bindingServices = BindingService::paginate(10);
        return Inertia::render('Services/Binding', [
            'bindingServices' => $bindingServices,
        ]);
    }

    /**
     * Store a newly created binding service in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pages' => 'required|string',
            'price' => 'required|numeric|min:0',
            'service_charge' => 'required|numeric|min:0',
        ]);

        $service = BindingService::create($request->all());

        if ($request->expectsJson()) {
            return response()->json($service, 201);
        }

        return redirect()->back()->with('success', 'Binding service created successfully.');
    }

    /**
     * Display the specified binding service.
     */
    public function show(BindingService $bindingService)
    {
        return response()->json($bindingService);
    }

    /**
     * Update the specified binding service in storage.
     */
    public function update(Request $request, BindingService $bindingService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pages' => 'required|string',
            'price' => 'required|numeric|min:0',
            'service_charge' => 'required|numeric|min:0',
        ]);

        $bindingService->update($request->all());

        if ($request->expectsJson()) {
            return response()->json($bindingService);
        }

        return redirect()->back()->with('success', 'Binding service updated successfully.');
    }

    /**
     * Remove the specified binding service from storage.
     */
    public function destroy(BindingService $bindingService)
    {
        $bindingService->delete();

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->back()->with('success', 'Binding service deleted successfully.');
    }
}
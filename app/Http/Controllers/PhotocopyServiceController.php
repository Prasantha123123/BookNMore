<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PhotocopyService;

class PhotocopyServiceController extends Controller
{
    /**
     * Display a listing of the photocopy services.
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $photocopyServices = PhotocopyService::all();
            return response()->json($photocopyServices);
        }

        $photocopyServices = PhotocopyService::paginate(10);
        return Inertia::render('Services/Photocopy', [
            'photocopyServices' => $photocopyServices,
        ]);
    }

    /**
     * Store a newly created photocopy service in storage.
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

        $service = PhotocopyService::create($request->all());

        if ($request->expectsJson()) {
            return response()->json($service, 201);
        }

        return redirect()->back()->with('success', 'Photocopy service created successfully.');
    }

    /**
     * Display the specified photocopy service.
     */
    public function show(PhotocopyService $photocopyService)
    {
        return response()->json($photocopyService);
    }

    /**
     * Update the specified photocopy service in storage.
     */
    public function update(Request $request, PhotocopyService $photocopyService)
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

        $photocopyService->update($request->all());

        if ($request->expectsJson()) {
            return response()->json($photocopyService);
        }

        return redirect()->back()->with('success', 'Photocopy service updated successfully.');
    }

    /**
     * Remove the specified photocopy service from storage.
     */
    public function destroy(PhotocopyService $photocopyService)
    {
        $photocopyService->delete();

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->back()->with('success', 'Photocopy service deleted successfully.');
    }
}
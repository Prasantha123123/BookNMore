<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PhotocopyService;
use App\Models\Category;
use App\Models\Product;

class PhotocopyServiceController extends Controller
{
    /**
     * Display a listing of the photocopy services.
     */
    public function index(Request $request)
    {
        if ($request->expectsJson() || $request->wantsJson()) {
            $photocopyServices = PhotocopyService::all();
            return response()->json([
                'success' => true,
                'photocopyServices' => $photocopyServices
            ]);
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'size' => 'required|string|max:255',
                'side' => 'required|string|max:255',
                'pages' => 'required|string|max:255',
                'color' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'service_charge' => 'required|numeric|min:0',
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            $validated['service_id'] = 1;

            $photocopyService = PhotocopyService::create($validated);

            foreach ($validated['products'] as $product) {
                $photocopyService->rawMaterials()->create([
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                ]);
            }

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Photocopy service created successfully',
                    'photocopyService' => $photocopyService
                ], 201);
            }

            return redirect()->route('photocopy.services.index')->with('success', 'Photocopy service created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating photocopy service', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'An error occurred while creating the photocopy service',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred while creating the photocopy service.');
        }
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

    /**
     * Fetch all categories.
     */
    public function fetchCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Fetch products based on the selected category.
     */
    public function fetchProducts(Request $request)
    {
        $categoryId = $request->query('category_id');
        $products = Product::where('category_id', $categoryId)->get();
        return response()->json($products);
    }
}
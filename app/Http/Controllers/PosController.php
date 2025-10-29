<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Size;
use App\Models\StockTransaction;
use App\Models\Employee;
use App\Models\Newspaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PosController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('hasRole', ['Admin', 'Cashier'])) {
            abort(403, 'Unauthorized');
        }

        $allcategories = Category::with('parent')->get()->map(function ($category) {
            $category->hierarchy_string = $category->parent ? $category->parent->name : null;
            return $category;
        });
        $colors = Color::orderBy('created_at', 'desc')->get();
        $sizes = Size::orderBy('created_at', 'desc')->get();
        $allemployee = Employee::orderBy('created_at', 'desc')->get();

        return Inertia::render('Pos/Index', [
            'product' => null,
            'error' => null,
            'loggedInUser' => Auth::user(),
            'allcategories' => $allcategories,
            'allemployee' => $allemployee,
            'colors' => $colors,
            'sizes' => $sizes,
        ]);
    }

    public function getProduct(Request $request)
    {
        if (!Gate::allows('hasRole', ['Admin', 'Cashier'])) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'barcode' => 'required',
        ]);

        // First try to find in products
        $product = Product::where('barcode', $request->barcode)
            ->orWhere('code', $request->barcode)
            ->first();

        if ($product) {
            return response()->json([
                'product' => $product,
                'type' => 'product',
                'error' => null,
            ]);
        }

        // If not found in products, try newspapers
        $newspaper = Newspaper::where('barcode', $request->barcode)
            ->orWhere('productcode', $request->barcode)
            ->first();

        if ($newspaper) {
            // Format newspaper to match product structure
            return response()->json([
                'product' => [
                    'id' => $newspaper->id,
                    'name' => $newspaper->name,
                    'barcode' => $newspaper->barcode,
                    'selling_price' => $newspaper->selling_price,
                    'cost_price' => $newspaper->cost_price ?? 0,
                    'stock_quantity' => $newspaper->stock_quantity,
                    'discount' => $newspaper->discount ?? 0,
                    'discounted_price' => $newspaper->discount_price ?? $newspaper->selling_price,
                    'is_newspaper' => true,
                    'image' => null,
                ],
                'type' => 'newspaper',
                'error' => null,
            ]);
        }

        return response()->json([
            'product' => null,
            'error' => 'Product or Newspaper not found',
        ]);
    }

    public function getCoupon(Request $request)
    {
        $request->validate(
            ['code' => 'required|string'],
            ['code.required' => 'The coupon code missing.', 'code.string' => 'The coupon code must be a valid string.']
        );

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json(['error' => 'Invalid coupon code.']);
        }

        if (!$coupon->isValid()) {
            return response()->json(['error' => 'Coupon is expired or has been fully used.']);
        }

        return response()->json(['success' => 'Coupon applied successfully.', 'coupon' => $coupon]);
    }

    public function submit(Request $request)
    {
        if (!Gate::allows('hasRole', ['Admin', 'Cashier'])) {
            abort(403, 'Unauthorized');
        }

        $customer = null;
        $products = $request->input('products', []);

        // Calculate totals
        $totalAmount = collect($products)->reduce(function ($carry, $item) {
            return $carry + ($item['quantity'] * $item['selling_price']);
        }, 0);

        $totalCost = collect($products)->reduce(function ($carry, $item) {
            $costPrice = $item['cost_price'] ?? 0;
            return $carry + ($item['quantity'] * $costPrice);
        }, 0);

        $productDiscounts = collect($products)->reduce(function ($carry, $item) {
            if (isset($item['discount']) && $item['discount'] > 0 && isset($item['apply_discount']) && $item['apply_discount'] == true) {
                $discountAmount = ($item['selling_price'] - $item['discounted_price']) * $item['quantity'];
                return $carry + $discountAmount;
            }
            return $carry;
        }, 0);

        $couponDiscount = isset($request->input('appliedCoupon')['discount']) ?
            floatval($request->input('appliedCoupon')['discount']) : 0;

        $totalDiscount = $productDiscounts + $couponDiscount;

        DB::beginTransaction();

        try {
            // Handle customer creation
            if ($request->input('customer.contactNumber') || $request->input('customer.name') || $request->input('customer.email')) {
                $phone = $request->input('customer.countryCode') . $request->input('customer.contactNumber');
                $customer = Customer::where('email', $request->input('customer.email'))->first();
                $customer1 = Customer::where('phone', $phone)->first();

                if ($customer) {
                    $email = '';
                } else {
                    $email = $request->input('customer.email');
                }

                if ($customer1) {
                    $phone = '';
                }

                if (!empty($phone) || !empty($email) || !empty($request->input('customer.name'))) {
                    $customer = Customer::create([
                        'name' => $request->input('customer.name'),
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $request->input('customer.address', ''),
                        'member_since' => now()->toDateString(),
                        'loyalty_points' => 0,
                    ]);
                }
            }

            // Create the sale record
            $sale = Sale::create([
                'customer_id' => $customer ? $customer->id : null,
                'employee_id' => $request->input('employee_id'),
                'user_id' => $request->input('userId'),
                'order_id' => $request->input('orderid'),
                'total_amount' => $totalAmount,
                'discount' => $totalDiscount,
                'total_cost' => $totalCost,
                'payment_method' => $request->input('paymentMethod'),
                'sale_date' => now()->toDateString(),
                'cash' => $request->input('cash'),
                'custom_discount' => $request->input('custom_discount'),
            ]);

            // Process each item (product or newspaper)
            foreach ($products as $item) {
                $isNewspaper = isset($item['is_newspaper']) && $item['is_newspaper'] === true;

                if ($isNewspaper) {
                    // Handle newspaper
                    $newspaperModel = Newspaper::find($item['id']);
                    
                    if (!$newspaperModel) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Newspaper not found: {$item['name']}"
                        ], 404);
                    }

                    $newStockQuantity = $newspaperModel->stock_quantity - $item['quantity'];

                    if ($newStockQuantity < 0) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Insufficient stock for newspaper: {$newspaperModel->name} ({$newspaperModel->stock_quantity} available)"
                        ], 423);
                    }

                    // Create sale item for newspaper
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'newspaper_id' => $item['id'],
                        'product_id' => null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['selling_price'],
                        'total_price' => $item['quantity'] * $item['selling_price'],
                    ]);

                    // Create stock transaction for newspaper
                    StockTransaction::create([
                        'newspaper_id' => $item['id'],
                        'transaction_type' => 'Sold',
                        'quantity' => $item['quantity'],
                        'transaction_date' => now(),
                    ]);

                    // Update newspaper stock
                    $newspaperModel->update([
                        'stock_quantity' => $newStockQuantity,
                    ]);
                } else {
                    // Handle product
                    $productModel = Product::find($item['id']);
                    
                    if (!$productModel) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Product not found: {$item['name']}"
                        ], 404);
                    }

                    $newStockQuantity = $productModel->stock_quantity - $item['quantity'];

                    if ($newStockQuantity < 0) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Insufficient stock for product: {$productModel->name} ({$productModel->stock_quantity} available)",
                        ], 423);
                    }

                    if ($productModel->expire_date && now()->greaterThan(new \Carbon\Carbon($productModel->expire_date))) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "The product '{$productModel->name}' has expired (Expiration Date: {$productModel->expire_date}).",
                        ], 423);
                    }

                    // Create sale item for product
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['id'],
                        'newspaper_id' => null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['selling_price'],
                        'total_price' => $item['quantity'] * $item['selling_price'],
                    ]);

                    // Create stock transaction for product
                    StockTransaction::create([
                        'product_id' => $item['id'],
                        'transaction_type' => 'Sold',
                        'quantity' => $item['quantity'],
                        'transaction_date' => now(),
                        'supplier_id' => $productModel->supplier_id ?? null,
                    ]);

                    // Update product stock
                    $productModel->update([
                        'stock_quantity' => $newStockQuantity,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Sale recorded successfully!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while processing the sale.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

   public function getNewspapers()
{
    try {
        $newspapers = Newspaper::select(
            'id', 
            'name', 
            'barcode', 
            'stock_quantity', 
            'selling_price', 
            'cost_price', 
            'discount', 
            'discount_price'
        )
        ->where('stock_quantity', '>', 0)
        ->get()
        ->map(function ($newspaper) {
            return [
                'id' => $newspaper->id,
                'name' => $newspaper->name,
                'barcode' => $newspaper->barcode,
                'stock_quantity' => $newspaper->stock_quantity,
                'selling_price' => $newspaper->selling_price,
                'cost_price' => $newspaper->cost_price ?? 0,
                'discount' => $newspaper->discount ?? 0,
                'discount_price' => $newspaper->discount_price ?? $newspaper->selling_price,
            ];
        });

        return response()->json(['newspapers' => $newspapers]);
    } catch (\Exception $e) {
        \Log::error('Error fetching newspapers: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch newspapers'], 500);
    }
}
}
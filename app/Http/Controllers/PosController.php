<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PosOrder;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PosController extends Controller
{
    // 1. Головна сторінка POS-термінала (каталог + форма)
    public function index()
    {
        $products = Product::with(['variants.size', 'images'])
            ->where('is_active', true)
            ->get();

        // Останні продажі для віджета на сторінці термінала
        $recentSales = PosOrder::with(['product', 'variant.size', 'seller'])
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Pos/Index', [
            'products' => $products,
            'recentSales' => $recentSales,
        ]);
    }

    // 2. Окрема сторінка зі списком усіх продажів (таблиця з пагінацією)
    public function history()
    {
        $sales = PosOrder::with(['product', 'variant.size', 'seller'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Pos/Sales', [
            'sales' => $sales,
        ]);
    }

    // 3. Швидка фіксація продажу та списання залишків
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'         => 'required|exists:products,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity'           => 'required|integer|min:1',
            'customer_name'      => 'nullable|string|max:255',
            'customer_phone'     => 'nullable|string|max:255',
            'payment_method'     => 'required|string|in:cash,card',
        ]);

        DB::transaction(function () use ($validated) {
            $variant = ProductVariant::with('product')->findOrFail($validated['product_variant_id']);

            if ($variant->stock < $validated['quantity']) {
                abort(422, 'Недостатньо товару на складі для цього розміру.');
            }

            $price = $variant->price_override ?? $variant->product->price;

            PosOrder::create([
                'user_id'            => Auth::id(),
                'product_id'         => $validated['product_id'],
                'product_variant_id' => $variant->id,
                'quantity'           => $validated['quantity'],
                'price'              => $price * $validated['quantity'],
                'customer_name'      => $validated['customer_name'] ?? null,
                'customer_phone'     => $validated['customer_phone'] ?? null,
                'payment_method'     => $validated['payment_method'],
            ]);

            $variant->decrement('stock', $validated['quantity']);
        });

        return back()->with('success', 'Продаж успішно зафіксовано, залишки оновлено!');
    }
}

<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        // 1. Загальна статистика
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'low_stock_variants' => ProductVariant::where('stock', '<=', 3)->count(), // Кількість розмірів, де залишилося <= 3 шт.
            // Примітка: якщо таблиці orders ще немає, можна поставити заглушки 0
            'total_orders' => class_exists(Order::class) ? Order::count() : 0,
            'total_revenue' => class_exists(Order::class) ? Order::where('status', 'completed')->sum('total_price') : 0,
        ];

        // 2. Варіації товарів (розміри), що закінчуються (stock <= 3)
        $lowStockItems = ProductVariant::with(['product', 'size'])
            ->where('stock', '<=', 3)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // 3. Останні додані товари
        $recentProducts = Product::with(['category', 'brand', 'variants.size'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'lowStockItems' => $lowStockItems,
            'recentProducts' => $recentProducts,
        ]);
    }
}

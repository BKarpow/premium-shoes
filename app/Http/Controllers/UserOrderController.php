<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserOrderController extends Controller
{
    /**
     * Відображення списку замовлень поточного користувача
     */
    public function index(): Response
    {
        $orders = Order::where('user_id', Auth::id())
            ->with([
                'items.variant.product.images',
                'items.variant.size'
            ])
            ->latest()
            ->paginate(10);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Окремий деталiзований перегляд конкретного замовлення
     */
    public function show(Order $order): Response
    {
        // Перевірка, що замовлення належить саме авторизованому користувачу
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load([
            'items.variant.product.images',
            'items.variant.size'
        ]);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }
}

<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * Список усіх замовлень з можливістю фільтрації за статусом
     */
    public function index(Request $request): Response
    {
        $status = $request->input('status');

        $orders = Order::with('user')
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'status' => $status,
            ],
        ]);
    }

    /**
     * Детальний перегляд конкретного замовлення
     */
    public function show(Order $order): Response
    {
        $order->load(['items.variant.product', 'items.variant.size', 'user']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Оновлення статусу замовлення або статусу оплати
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,processing,shipped,completed,cancelled',
            'payment_status' => 'required|in:pending,paid',
        ]);

        $order->update($validated);

        return back()->with('success', 'Статус замовлення успішно оновлено!');
    }
}

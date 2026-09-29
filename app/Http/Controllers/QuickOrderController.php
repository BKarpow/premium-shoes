<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuickOrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Валідація даних з форми "Купити в 1 клік"
        $validated = $request->validate([
            'phone'              => 'required|string|max:255',
            'name'         => 'nullable|string|max:255',
            'variant_id' => 'required|exists:product_variants,id',
        ]);

        DB::transaction(function () use ($validated) {
            // Отримуємо варіант товару з його ціною та основним товаром
            $variant = ProductVariant::with('product')
            ->findOrFail($validated['variant_id']);
            $product = $variant->product;

            // Визначаємо ціну товару (зі знижкою, якщо є)
            $price = $product->sale_price ?? $product->price;

            // 2. Створюємо запис у таблиці orders
            $order = Order::create([
                'user_id'       => Auth::id(), // null, якщо гостьовий запит
                'first_name'    => $validated['first_name'] ?? 'Швидке',
                'last_name'     => 'Замовлення',
                'phone'         => $validated['phone'],
                'email'         => Auth::user()?->email,
                'shipping_type' => 'quick_order', // Можна вказати 'pickup' або інший статус
                'total_price'   => $price,
                'status'        => 'new',
                'payment_status'=> 'pending',
            ]);

            // 3. Додаємо позицію в order_items
            OrderItem::create([
                'order_id'           => $order->id,
                // 'orders_id'          => $order->id, // Для сумісності із зовнішніми ключами
                'product_variant_id' => $variant->id,
                'product_name'       => $product->name ?? $product->title,
                'size_value'         => $variant->size->value ?? $variant->size->name ?? null,
                'price'              => $price,
                'quantity'           => 1,
            ]);

            // 4. Списуємо 1 шт. зі складу
            $variant->decrement('stock', 1);
        });

        return back()->with('success', 'Дякуємо! Замовлення успішно прийняте. Менеджер зв’яжеться з Вами найближчим часом.');
    }
}

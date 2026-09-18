<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Отримати або створити кошик у БД для поточного користувача/сесії
     */
    protected function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = Session::getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Додати варіант товару до кошика
     */
    public function add(int $variantId, int $quantity = 1): void
    {
        $cart = $this->getOrCreateCart();

        $cartItem = $cart->items()->where('product_variant_id', $variantId)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }
    }

    /**
     * Отримати деталі кошика для фронтенду
     */
    public function getCartDetails(): array
    {
        $cart = $this->getOrCreateCart();

        $items = $cart->items()
            ->with(['variant.product.images', 'variant.size'])
            ->get();

        $cartItems = [];
        $totalSum = 0;
        $totalCount = 0;

        foreach ($items as $item) {
            $variant = $item->variant;
            $product = $variant->product;

            $price = $product->sale_price ?? $product->price;
            $subtotal = $price * $item->quantity;

            $totalSum += $subtotal;
            $totalCount += $item->quantity;

            $cartItems[] = [
                'id' => $item->id,
                'variant_id' => $variant->id,
                'product_id' => $product->id,
                'name' => $product->name ?? $product->title,
                'slug' => $product->slug,
                'image' => $product->primary_image_url ?? $product->images->first()?->path ?? '/images/placeholder.jpg',
                'size' => $variant->size->name ?? $variant->size->value ?? null,
                'price' => $price,
                'quantity' => $item->quantity,
                'subtotal' => $subtotal,
            ];
        }

        return [
            'items' => $cartItems,
            'total' => $totalSum,
            'count' => $totalCount,
        ];
    }

    /**
     * Видалити позицію
     */
    public function remove(int $cartItemId): void
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->where('id', $cartItemId)->delete();
    }

    /**
     * Оновити кількість
     */
    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($cartItemId);
            return;
        }

        $cart = $this->getOrCreateCart();
        $cart->items()->where('id', $cartItemId)->update(['quantity' => $quantity]);
    }


    /**
         * Очистити весь кошик
         */
        public function clear(): void
        {
            $cart = $this->getOrCreateCart();
            $cart->items()->delete();
        }
}

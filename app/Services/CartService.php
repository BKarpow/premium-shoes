<?php


namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'shopping_cart';

    /**
     * Отримати вміст кошика
     */
    public function getCart(): array
    {
        return Session::get($this->sessionKey, []);
    }

    /**
     * Отримати деталізований кошик із обчисленими сумами та даними про товари
     */
    public function getCartDetails(): array
    {
        $cart = $this->getCart();
        $items = [];
        $total = 0;
        $totalCount = 0;

        if (empty($cart)) {
            return [
                'items' => [],
                'total' => 0,
                'total_count' => 0,
            ];
        }

        $variantIds = array_keys($cart);
        $variants = ProductVariant::with(['product', 'size', 'color'])
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        foreach ($cart as $variantId => $quantity) {
            if (isset($variants[$variantId])) {
                $variant = $variants[$variantId];
                $product = $variant->product;

                $price = $product->sale_price ?? $product->price;
                $subtotal = $price * $quantity;
                $total += $subtotal;
                $totalCount += $quantity;

                $items[] = [
                    'variant_id' => $variant->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->primary_image_url ?? '/images/placeholder.jpg',
                    'size' => $variant->size->name ?? $variant->size->value ?? null,
                    'color' => $variant->color->name ?? null,
                    'price' => (float) $price,
                    'quantity' => (int) $quantity,
                    'max_stock' => (int) $variant->stock,
                    'subtotal' => (float) $subtotal,
                ];
            }
        }

        return [
            'items' => $items,
            'total' => (float) $total,
            'total_count' => (int) $totalCount,
        ];
    }

    /**
     * Додати варіант товару в кошик
     */
    public function add(int $variantId, int $quantity = 1): void
    {
        $cart = $this->getCart();

        if (isset($cart[$variantId])) {
            $cart[$variantId] += $quantity;
        } else {
            $cart[$variantId] = $quantity;
        }

        Session::put($this->sessionKey, $cart);
    }

    /**
     * Оновити кількість товару
     */
    public function update(int $variantId, int $quantity): void
    {
        $cart = $this->getCart();

        if ($quantity <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId] = $quantity;
        }

        Session::put($this->sessionKey, $cart);
    }

    /**
     * Видалити варіант із кошика
     */
    public function remove(int $variantId): void
    {
        $cart = $this->getCart();

        if (isset($cart[$variantId])) {
            unset($cart[$variantId]);
            Session::put($this->sessionKey, $cart);
        }
    }

    /**
     * Очистити весь кошик
     */
    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }
}

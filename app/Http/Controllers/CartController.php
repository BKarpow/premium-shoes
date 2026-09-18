<?php
namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Додати варіант товару до кошика
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'exists:product_variants,id'],
            'quantity'   => ['nullable', 'integer', 'min:1'],
        ]);

        $quantity = $validated['quantity'] ?? 1;

        $this->cartService->add($validated['variant_id'], $quantity);

        return back()->with('success', 'Товар успішно додано до кошика!');
    }

    /**
     * Оновити кількість конкретної позиції в кошику (по cart_item_id)
     */
    public function update(Request $request, int $itemId): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $this->cartService->updateQuantity($itemId, $validated['quantity']);

        return back()->with('success', 'Кількість оновлено');
    }

    /**
     * Видалити позицію з кошика
     */
    public function destroy(int $itemId): RedirectResponse
    {
        $this->cartService->remove($itemId);

        return back()->with('success', 'Товар видалено з кошика');
    }
}

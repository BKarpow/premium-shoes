<?php
namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\NovaPoshtaService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected NovaPoshtaService $novaPoshtaService;

    public function success(Order $order)
    {
        // Перевіряємо, чи належить замовлення поточному користувачу (якщо він авторизований) або перенаправляємо
        // Завантажуємо зв'язані позиції замовлення
        $order->load('items');

        return Inertia::render('Checkout/Success', [
            'order' => $order,
        ]);
    }

    public function __construct(CartService $cartService, NovaPoshtaService $novaPoshtaService)
    {
        $this->cartService = $cartService;
        $this->novaPoshtaService = $novaPoshtaService;
    }

    public function index()
    {
        $cartDetails = $this->cartService->getCartDetails();

        // Якщо кошик порожній, перенаправляємо на головну
        if (empty($cartDetails['items'])) {
            return redirect()->route('home')->with('error', 'Ваш кошик порожній!');
        }

        return Inertia::render('Checkout/Index', [
            'cart' => $cartDetails,
        ]);
    }

    public function searchCities(Request $request)
    {
        $query = $request->input('q', '');
        return response()->json($this->novaPoshtaService->getCities($query));
    }

    public function getWarehouses(Request $request)
    {
        $cityRef = $request->input('city_ref');
        $typeRef = $request->input('type_ref', ''); // Можна фільтрувати за типом відділення/поштомату

        return response()->json($this->novaPoshtaService->getWarehouses($cityRef, $typeRef));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'shipping_type' => 'required|in:pickup,nova_poshta',
            'city_ref' => 'required_if:shipping_type,nova_poshta|nullable|string',
            'city_name' => 'required_if:shipping_type,nova_poshta|nullable|string',
            'warehouse_ref' => 'required_if:shipping_type,nova_poshta|nullable|string',
            'warehouse_address' => 'required_if:shipping_type,nova_poshta|nullable|string',
        ]);

        $cartDetails = $this->cartService->getCartDetails();

        if (empty($cartDetails['items'])) {
            return back()->with('error', 'Кошик порожній!');
        }

        // Транзакція для безпеки даних (замовлення + позиції + списання складу)
        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'first_name' => $validated['first_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'shipping_type' => $validated['shipping_type'],
                'city_ref' => $validated['city_ref'] ?? null,
                'city_name' => $validated['city_name'] ?? null,
                'warehouse_ref' => $validated['warehouse_ref'] ?? null,
                'warehouse_address' => $validated['warehouse_address'] ?? null,
                'total_price' => $cartDetails['total'],
                'status' => 'new',
            ]);

            foreach ($cartDetails['items'] as $item) {
                $order->items()->create([
                    'product_variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'size_value' => $item['size'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);

                // Зменшуємо залишок товару на складі для конкретної варіації (SKU)
                if (isset($item['variant_id'])) {
                    \App\Models\ProductVariant::where('id', $item['variant_id'])
                        ->decrement('stock', $item['quantity']);
                }
            }

            // Очищуємо кошик після успішного замовлення
            $this->cartService->clear();

            DB::commit();

            // Тут можна додати відправку сповіщення в Telegram або на пошту адміністратору

            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('success', 'Замовлення успішно оформлено!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Сталася помилка при оформленні замовлення. Спробуйте пізніше.');
        }
    }
}

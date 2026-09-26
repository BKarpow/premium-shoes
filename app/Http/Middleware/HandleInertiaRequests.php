<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Services\CartService;
use App\Models\Category;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cartService = app(CartService::class);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                                'id' => $request->user()->id,
                                'name' => $request->user()->name,
                                'email' => $request->user()->email,
                                'roles' => $request->user()->roles, // 👈 Впевніться, що ролі підвантажуються тут
                            ] : null,
            ],
                    'cart' => fn () => $cartService->getCartDetails(),
                    'categories' => fn () => Category::whereNull('parent_id')
                                ->with('children')
                                ->get(),
                    'flash' => [
                        'success' => fn () => $request->session()->get('success'),
                        'error' => fn () => $request->session()->get('error'),
                    ],
        ];
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Головна сторінка каталогу взуття
     */
    public function index(Request $request): Response
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['category', 'brand', 'images', 'variants.size']);

        // 1. Фільтр за категорією (slug)
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // 2. Фільтр за брендами (масив id або slug)
        if ($request->filled('brands')) {
            $brands = is_array($request->input('brands'))
                ? $request->input('brands')
                : explode(',', $request->input('brands'));

            $query->whereHas('brand', function ($q) use ($brands) {
                $q->whereIn('slug', $brands);
            });
        }

        // 3. Фільтр за розмірами взуття (розміри беруться з таблиці product_variants)
        if ($request->filled('sizes')) {
            $sizes = is_array($request->input('sizes'))
                ? $request->input('sizes')
                : explode(',', $request->input('sizes'));

            $query->whereHas('variants', function ($q) use ($sizes) {
                $q->whereIn('size_id', $sizes)
                  ->where('stock', '>', 0); // Тільки ті, що є в наявності
            });
        }

        // 4. Фільтр за ціною (від / до)
        if ($request->filled('price_from')) {
            $query->where('price', '>=', $request->input('price_from'));
        }
        if ($request->filled('price_to')) {
            $query->where('price', '<=', $request->input('price_to'));
        }

        // 5. Сортування
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        // Пагінація по 12 товарів на сторінку + збереження GET-параметрів в URL
        $products = $query->paginate(12)->withQueryString();

        // Повертаємо Vue-компонент через Inertia
        return Inertia::render('Catalog/Index', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->get(['id', 'name', 'slug']),
            'brands' => Brand::where('is_active', true)->get(['id', 'name', 'slug']),
            'sizes' => Size::orderBy('sort_order')->get(['id', 'value', 'length_cm']),
            // Передаємо поточні фільтри назад на фронтенд для збереження стану форми
            'filters' => $request->only(['category', 'brands', 'sizes', 'price_from', 'price_to', 'sort']),
        ]);
    }

    /**
     * Картка конкретного товару
     */
    public function show(string $slug): Response
        {
            $product = Product::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->with([
                    'category:id,name,slug',
                    'brand:id,name,slug',
                    'images' => fn($query) => $query->orderBy('sort_order'),
                    'variants.size' => fn($query) => $query->orderBy('sort_order')
                ])
                ->firstOrFail();

            // Схожі товари (з цієї ж категорії)
            $relatedProducts = Product::query()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->with(['brand', 'images' => fn($q) => $q->where('is_main', true)])
                ->limit(4)
                ->get();

            return Inertia::render('Product/Show', [
                'product' => $product,
                'similarProducts' => $relatedProducts,
            ]);
        }
}

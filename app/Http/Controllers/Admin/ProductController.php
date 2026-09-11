<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::query()
            ->with(['category', 'brand', 'images', 'variants.size'])
            ->when($request->search, function ($q, $search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::all(['id', 'name']),
            'brands' => Brand::all(['id', 'name']),
            'sizes' => Size::orderBy('sort_order')->get(['id', 'value']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'variants' => 'required|array|min:1',
            'variants.*.size_id' => 'required|exists:sizes,id',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:100',
        ]);

        // 1. Створення товару
        $product = Product::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'price' => $validated['price'],
            'old_price' => $validated['old_price'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // 2. Збереження фотографій
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => '/storage/' . $path,
                    'sort_order' => $index,
                    'is_main' => $index === 0, // перше фото робимо головним
                ]);
            }
        }

        // 3. Збереження розмірів із залишками
        foreach ($validated['variants'] as $variant) {
            ProductVariant::create([
                'product_id' => $product->id,
                'size_id' => $variant['size_id'],
                'stock' => $variant['stock'],
                'sku' => $variant['sku'] ?? ($product->slug . '-' . $variant['size_id']),
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Товар успішно створено!');
    }

    public function edit(Product $product): Response
    {
        $product->load(['images', 'variants']);

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => Category::all(['id', 'name']),
            'brands' => Brand::all(['id', 'name']),
            'sizes' => Size::orderBy('sort_order')->get(['id', 'value']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'variants' => 'required|array|min:1',
            'variants.*.size_id' => 'required|exists:sizes,id',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:100',
            'main_image_id' => 'nullable|exists:product_images,id',
        ]);

        // 1. Оновлення основної інформації
        $product->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'price' => $validated['price'],
            'old_price' => $validated['old_price'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // 2. Додавання нових фото, якщо завантажені
        if ($request->hasFile('new_images')) {
            $lastOrder = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('new_images') as $index => $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => '/storage/' . $path,
                    'sort_order' => $lastOrder + $index + 1,
                    'is_main' => false,
                ]);
            }
        }

        // 3. Зміна головного фото
        if ($request->filled('main_image_id')) {
            $product->images()->update(['is_main' => false]);
            ProductImage::where('id', $request->main_image_id)->update(['is_main' => true]);
        }

        // 4. Оновлення варіантів (видаляємо старі та створюємо нові)
        $product->variants()->delete();
        foreach ($validated['variants'] as $variant) {
            ProductVariant::create([
                'product_id' => $product->id,
                'size_id' => $variant['size_id'],
                'stock' => $variant['stock'],
                'sku' => $variant['sku'] ?? ($product->slug . '-' . $variant['size_id']),
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Товар оновлено!');
    }

    /**
     * Окреме видалення фото через AJAX/Inertia
     */
    public function destroyImage(ProductImage $image)
    {
        // Видалення фізичного файлу
        $relativePath = str_replace('/storage/', '', $image->path);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        $image->delete();

        return redirect()->back()->with('success', 'Фото видалено!');
    }

    public function destroy(Product $product)
    {
        // Видалення всіх фото з диска
        foreach ($product->images as $img) {
            $relativePath = str_replace('/storage/', '', $img->path);
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }

        $product->delete();
        return redirect()->back()->with('success', 'Товар видалено!');
    }
}

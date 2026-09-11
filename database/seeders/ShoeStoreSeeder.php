<?php
namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShoeStoreSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Створюємо реальні розміри взуття
        $sizesData = [
            ['value' => '36', 'length_cm' => 23.0, 'sort_order' => 1],
            ['value' => '37', 'length_cm' => 23.5, 'sort_order' => 2],
            ['value' => '38', 'length_cm' => 24.5, 'sort_order' => 3],
            ['value' => '39', 'length_cm' => 25.0, 'sort_order' => 4],
            ['value' => '40', 'length_cm' => 25.5, 'sort_order' => 5],
            ['value' => '41', 'length_cm' => 26.5, 'sort_order' => 6],
            ['value' => '42', 'length_cm' => 27.0, 'sort_order' => 7],
            ['value' => '43', 'length_cm' => 27.5, 'sort_order' => 8],
            ['value' => '44', 'length_cm' => 28.5, 'sort_order' => 9],
            ['value' => '45', 'length_cm' => 29.0, 'sort_order' => 10],
        ];

        $createdSizes = collect();
        foreach ($sizesData as $size) {
            $createdSizes->push(Size::create($size));
        }

        // 2. Створюємо реальні категорії
        $categoriesData = ['Кросівки', 'Черевики', 'Кеди', 'Сандалі', 'Шльопанці'];
        $createdCategories = collect();
        foreach ($categoriesData as $catName) {
            $createdCategories->push(Category::create([
                'name' => $catName,
                'slug' => Str::slug($catName),
                'description' => "Якісне взуття у категорії {$catName}",
                'is_active' => true,
            ]));
        }

        // 3. Створюємо основні бренди
        $brandsData = ['Nike', 'Adidas', 'Puma', 'New Balance', 'Reebok'];
        $createdBrands = collect();
        foreach ($brandsData as $brandName) {
            $createdBrands->push(Brand::create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
                'is_active' => true,
            ]));
        }

        // 4. Генеруємо 30 товарів з фото та розмірами
        Product::factory(30)->make()->each(function ($product) use ($createdCategories, $createdBrands, $createdSizes) {
            // Прив'язуємо рандомну категорію та бренд
            $product->category_id = $createdCategories->random()->id;
            $product->brand_id = $createdBrands->random()->id;
            $product->save();

            // Додаємо 3 фотографії до кожного товару (1 головна)
            ProductImage::create([
                'product_id' => $product->id,
                'path' => 'https://via.placeholder.com/600x600.png/003366?text=' . urlencode($product->title),
                'sort_order' => 1,
                'is_main' => true,
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'path' => 'https://via.placeholder.com/600x600.png/336699?text=Side+View',
                'sort_order' => 2,
                'is_main' => false,
            ]);

            // Прив'язуємо від 3 до 6 випадкових розмірів із залишками на складі
            $randomSizes = $createdSizes->random(rand(3, 6));
            foreach ($randomSizes as $size) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size_id' => $size->id,
                    'sku' => strtoupper(Str::random(3)) . '-' . rand(1000, 9999) . '-' . $size->value,
                    'stock' => rand(1, 10),
                ]);
            }
        });
    }
}

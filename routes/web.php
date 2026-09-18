<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CartController;

use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index'])->name('catalog.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('catalog.show');

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });
//


Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Публічна сторінка товару за його slug
Route::get('/product/{slug}', [ProductController::class, 'show'])
->name('products.show');

// Авторизовані користувачі з роллю адміна (або поки просто auth)
Route::middleware(['auth', 'role:admin|manager'])->prefix('admin')->name('admin.')->group(function () {

    // Дашборд
    Route::get('/', DashboardController::class)->name('dashboard');

    // CRUD для товарів
    Route::resource('products', AdminProductController::class);
    Route::delete('product-images/{image}', [AdminProductController::class, 'destroyImage'])
    ->name('products.images.destroy');

    // CRUD для категорій та брендів
    Route::resource('products', AdminProductController::class);
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::resource('brands', AdminBrandController::class)->except(['show']);

        Route::middleware('role:admin')->group(function () {
                Route::resource('users', UserController::class);
            });
});


Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/add', [CartController::class, 'store'])->name('add');
    Route::patch('/{variantId}', [CartController::class, 'update'])->name('update');
    Route::delete('/{variantId}', [CartController::class, 'destroy'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});


use App\Http\Controllers\CheckoutController;

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/store', [CheckoutController::class, 'store'])->name('store');

    // Новий маршрут сторінки успіху
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');

    // API ендпоінти для Нової Пошти (для фронтенд-автозаповнення)
    Route::get('/np/cities', [CheckoutController::class, 'searchCities'])->name('np.cities');
    Route::get('/np/warehouses', [CheckoutController::class, 'getWarehouses'])->name('np.warehouses');
});

require __DIR__.'/auth.php';

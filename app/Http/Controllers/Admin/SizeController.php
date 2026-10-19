<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SizeController extends Controller
{
    // Список розмірів
    public function index(Request $request): Response
    {
        $sizes = Size::query()
            ->when($request->search, function ($q, $search) {
                $q->where('value', 'like', "%{$search}%");
            })
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Sizes/Index', [
            'sizes' => $sizes,
            'filters' => $request->only(['search']),
        ]);
    }

    // Збереження нового розміру
    public function store(Request $request)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255|unique:sizes,value',
            'length_cm' => 'nullable|numeric|min:10|max:50',
            'sort_order' => 'nullable|integer',
        ]);

        Size::create([
            'value' => $validated['value'],
            'length_cm' => $validated['length_cm'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'Розмір успішно створено!');
    }

    // Оновлення розміру
    public function update(Request $request, Size $size)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'length_cm' => 'nullable|numeric|min:10|max:50',
            'sort_order' => 'nullable|integer',
        ]);

        // $size->update($validated);
        $size->value = $validated['value'];
        $size->length_cm = $validated['length_cm'];
        $size->sort_order = $validated['sort_order'];
        $size->save();

        return redirect()->back()->with('success', 'Розмір оновлено!');
    }

    // Видалення розміру
    public function destroy(Size $size)
    {
        // Перевіряємо, чи використовується розмір у товарах (варіантах)
        if ($size->variants()->count() > 0) {
            return redirect()->back()->with('error', 'Неможливо видалити розмір, оскільки він прив’язаний до товарів!');
        }

        $size->delete();

        return redirect()->back()->with('success', 'Розмір успішно видалено!');
    }
}

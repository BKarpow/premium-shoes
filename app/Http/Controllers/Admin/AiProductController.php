<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiProductController extends Controller
{
    public function generateDescription(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        $category = Category::find($request->category_id);
        $categoryName = $category ? $category->name : 'Взуття';

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Не знайдено Gemini API ключ у конфігурації (.env).'
            ], 500);
        }

        // Використовуємо актуальну та швидку модель Gemini через прямий REST API
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $prompt = "Напиши привабливий, професійний та сучасний маркетинговий опис для інтернет-магазину взуття українською мовою. "
            . "Назва товару: '{$request->title}'. "
            . "Категорія: '{$categoryName}'. "
            . "Ціна: {$request->price} грн. "
            . "Опис має включати ключові переваги, стиль, можливі матеріали та комфорт при носінні. Обсяг — 2-3 абзаци.";

        try {
            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $description = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                return response()->json([
                    'success' => true,
                    'description' => trim($description)
                ]);
            }

            Log::error('Gemini API Error: ' . $response->body());

            return response()->json([
                'success' => false,
                'message' => 'Помилка під час звернення до Gemini API.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Сталася системна помилка: ' . $e->getMessage()
            ], 500);
        }
    }
}

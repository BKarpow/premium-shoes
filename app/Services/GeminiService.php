<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function generateProductDescription(string $title, float|string $price, string $categoryName): ?string
    {
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            Log::error('Gemini API key is missing.');
            return null;
        }

        // Використовуємо офіційний endpoint моделі gemini-2.5-flash (або gemini-1.5-flash)
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $prompt = "Напиши привабливий, професійний та сучасний маркетинговий опис для інтернет-магазину шкіряного взуття українською мовою. "
            ."Опис має акцентувати на те що взуття вироблено в Україні."
            ."Також акцентуй на те що це натуральна шкіра."
            . "Назва товару: '{$title}'. "
            . "Категорія: '{$categoryName}'. "
            . "Ціна: {$price} грн. "
            . "Опис має включати ключові переваги, стиль, можливі матеріали та комфорт при носінні. Обсяг — 2-3 абзаци."

            ."ОПИС МАЄ БУТИ ТІЛЬКИ У ФОРМАТІ MARKDOWN У СТИЛІ GitHub.";

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

                // Витягуємо текст з відповіді Gemini API
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            Log::error('Gemini API error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Gemini exception: ' . $e->getMessage());
            return null;
        }
    }
}

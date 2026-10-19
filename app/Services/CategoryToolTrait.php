<?php

namespace App\Services;

use App\Models\Category;

trait CategoryToolTrait {

    private function getCategories(): array
        {
            $parentCategories = Category::whereNull('parent_id')->with('children')
            ->orderBy('name')->get();

                // Формуємо плоский список, але в правильному ієрархічному порядку
                $categories = [];
                foreach ($parentCategories as $parent) {
                    $categories[] = [
                        'id' => $parent->id,
                        'name' => $parent->name,
                        'is_parent' => true,
                    ];

                    if ($parent->children) {
                        foreach ($parent->children as $child) {
                            $categories[] = [
                                'id' => $child->id,
                                'name' => '↘️ ' . $child->name, // Візуальний відступ для дочірньої
                                'is_parent' => false,
                            ];
                        }
                    }
                }
                return $categories;
        }
}

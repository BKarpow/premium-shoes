<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NovaPoshtaService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.novaposhta.ua/v2.0/json/';

    public function __construct()
    {
        $this->apiKey = config('services.novaposhta.key');
    }

    /**
     * Пошук міст за назвою
     */
    public function getCities(string $cityName = ''): array
    {
        // Кешування або прямий запит до API
        $response = Http::post($this->apiUrl, [
            'apiKey' => $this->apiKey,
            'modelName' => 'Address',
            'calledMethod' => 'getCities',
            'methodProperties' => [
                'FindByString' => $cityName,
            ]
        ]);

        return $response->json()['data'] ?? [];
    }

    /**
     * Отримання відділень/поштоматів для конкретного міста (за CityRef)
     */
    public function getWarehouses(string $cityRef, string $warehouseTypeRef = ''): array
    {
        $cacheKey = "np_warehouses_{$cityRef}_{$warehouseTypeRef}";

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($cityRef, $warehouseTypeRef) {
            $properties = [
                'CityRef' => $cityRef,
            ];

            // Якщо потрібно фільтрувати конкретно поштомати чи відділення
            if ($warehouseTypeRef) {
                $properties['TypeOfWarehouseRef'] = $warehouseTypeRef;
            }

            $response = Http::post($this->apiUrl, [
                'apiKey' => $this->apiKey,
                'modelName' => 'AddressGeneral',
                'calledMethod' => 'getWarehouses',
                'methodProperties' => $properties
            ]);

            return $response->json()['data'] ?? [];
        });
    }
}

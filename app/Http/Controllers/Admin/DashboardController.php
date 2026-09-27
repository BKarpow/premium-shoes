<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // 1. Загальні метрики
        $stats = [
            'total_orders' => Order::count(),
            'total_users'  => User::count(), // або просто User::count()
            'total_products' => Product::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_price') ?? Order::sum('total_price'),
        ];

        // 2. Графіки замовлень за різними періодами
        $chartsData = [
            'week'    => $this->getOrdersChartData(7),
            'month'   => $this->getOrdersChartData(30),
            'quarter' => $this->getOrdersChartData(90),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'chartsData' => $chartsData,
        ]);
    }

    /**
     * Допоміжний метод для групування замовлень по днях за останні $days днів
     */
    private function getOrdersChartData(int $days)
    {
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        // Отримуємо кількість та суму за кожен день
        $rawOrders = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_price) as sum')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $counts = [];
        $sums = [];

        // Заповнюємо всі дні, навіть якщо в якийсь день замовлень не було (щоб графік був без пропусків)
        for ($i = $days - 1; $i >= 0; $i--) {
            $dateKey = Carbon::now()->subDays($i)->format('Y-m-d');
            $displayDate = Carbon::now()->subDays($i)->format('d.m');

            $labels[] = $displayDate;
            $counts[] = isset($rawOrders[$dateKey]) ? (int)$rawOrders[$dateKey]->count : 0;
            $sums[] = isset($rawOrders[$dateKey]) ? (float)$rawOrders[$dateKey]->sum : 0;
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'sums'   => $sums,
        ];
    }
}

<script setup>
import { Link, Head } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    lowStockItems: Array,
    recentProducts: Array,
});
</script>

<template>
<!-- Встановлюємо заголовок вкладки браузера -->
    <Head title="Адмін панель" />
    <div class="min-h-screen bg-slate-950 text-slate-100 p-6 md:p-8 font-sans">
        <!-- Шапка дашборду -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-6 border-b border-slate-800 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white">Панель управління</h1>
                <p class="text-sm text-slate-400 mt-1">Огляд стану магазину взуття та швидкий доступ до розділів</p>
            </div>

            <div class="flex items-center gap-3">
                <Link
                    :href="route('admin.products.index')"
                    :class="[
                        route().current('admin.products.*')
                            ? 'bg-amber-500/10 text-amber-400 border-amber-500/50'
                            : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900',
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl border border-transparent font-medium text-sm transition-all duration-200'
                    ]"
                >
                    <span class="text-lg">👟</span>
                    <span>Управління товарами</span>
                </Link>
                <Link
                    :href="route('admin.products.create')"
                    class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold rounded-lg text-sm transition shadow-lg shadow-amber-500/10 flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Додати товар
                </Link>
                <Link
                    href="/"
                    target="_blank"
                    class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-lg text-sm transition border border-slate-700/60"
                >
                    Перейти на сайт ↗
                </Link>
            </div>
        </div>

        <!-- 📊 Картки метрик (Stats Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Активні товари -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs uppercase tracking-wider font-semibold">Товари в каталозі</span>
                    <span class="p-2 bg-slate-800/80 text-amber-400 rounded-lg">👟</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ stats.active_products }} <span class="text-xs text-slate-500 font-normal">/ {{ stats.total_products }} зально</span></div>
                <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Доступні на витрині
                </p>
            </div>

            <!-- Попередження про малий залишок -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs uppercase tracking-wider font-semibold">Закінчуються розміри</span>
                    <span class="p-2 bg-slate-800/80 text-rose-400 rounded-lg">⚠️</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ stats.low_stock_variants }} <span class="text-xs text-slate-500 font-normal">позицій</span></div>
                <p class="text-xs text-rose-400/90 mt-2">
                    Залишок ≤ 3 пар на складі
                </p>
            </div>

            <!-- Замовлення -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs uppercase tracking-wider font-semibold">Всього замовлень</span>
                    <span class="p-2 bg-slate-800/80 text-blue-400 rounded-lg">📦</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ stats.total_orders }}</div>
                <p class="text-xs text-slate-500 mt-2">Очікують обробки або виконані</p>
            </div>

            <!-- Дохід -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs uppercase tracking-wider font-semibold">Загальна виручка</span>
                    <span class="p-2 bg-slate-800/80 text-emerald-400 rounded-lg">💳</span>
                </div>
                <div class="text-2xl font-bold text-emerald-400">{{ stats.total_revenue.toLocaleString() }} <span class="text-xs text-emerald-500">грн</span></div>
                <p class="text-xs text-slate-500 mt-2">Успішно оплачені замовлення</p>
            </div>
        </div>

        <!-- 📑 Основні блоки контенту -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Лівий блок (2/3): Останні додані товари -->
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-800">
                    <h2 class="text-lg font-bold text-white">Останні додані товари</h2>
                    <Link :href="route('admin.products.index')" class="text-xs font-semibold text-amber-400 hover:text-amber-300">
                        Усі товари →
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-xs uppercase text-slate-500 border-b border-slate-800">
                                <th class="pb-3">Товар</th>
                                <th class="pb-3">Категорія / Бренд</th>
                                <th class="pb-3">Ціна</th>
                                <th class="pb-3">Розміри в наявності</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <tr v-for="product in recentProducts" :key="product.id" class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 font-medium text-white">
                                    <Link :href="route('admin.products.edit', product.id)" class="hover:text-amber-400">
                                        {{ product.title }}
                                    </Link>
                                </td>
                                <td class="py-3.5 text-slate-400">
                                    {{ product.category?.name }} <span v-if="product.brand" class="text-slate-600">| {{ product.brand?.name }}</span>
                                </td>
                                <td class="py-3.5 font-semibold text-slate-200">
                                    {{ product.price }} грн
                                </td>
                                <td class="py-3.5">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="v in product.variants"
                                            :key="v.id"
                                            class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-slate-800 border border-slate-700 text-slate-300"
                                        >
                                            {{ v.size?.value }}: <span :class="v.stock <= 2 ? 'text-rose-400 font-bold' : 'text-slate-400'">{{ v.stock }}шт</span>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Правий блок (1/3): Сповіщення про закінчення розмірів -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-800">
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Дефіцит складських залишків
                        </h2>
                    </div>

                    <div v-if="lowStockItems.length === 0" class="text-center py-8 text-slate-500 text-sm">
                        Всі розміри є в достатній кількості 👍
                    </div>

                    <ul v-else class="space-y-3">
                        <li
                            v-for="item in lowStockItems"
                            :key="item.id"
                            class="p-3 bg-slate-950/60 border border-slate-800/80 rounded-lg flex items-center justify-between"
                        >
                            <div>
                                <p class="text-xs font-semibold text-slate-200">{{ item.product?.title }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Розмір: <span class="font-bold text-amber-400">{{ item.size?.value }}</span> (SKU: {{ item.sku }})
                                </p>
                            </div>
                            <span class="px-2 py-1 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold rounded">
                                {{ item.stock }} шт
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800">
                    <Link
                        :href="route('admin.products.index')"
                        class="w-full block text-center py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg text-xs font-semibold transition"
                    >
                        Управління всіма залишками
                    </Link>
                </div>
            </div>

        </div>
    </div>
</template>

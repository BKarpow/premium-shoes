<script setup>
import { ref, watch } from 'vue';
import { Link, router, Head } from '@inertiajs/vue3';

const props = defineProps({
    products: Object, // Пагінований список товарів від Laravel
    filters: Object,
});

const search = ref(props.filters?.search || '');

// Автоматичний пошук з невеликою затримкою (debounce)
let searchTimeout;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            route('admin.products.index'),
            { search: value },
            { preserveState: true, replace: true }
        );
    }, 300);
});

const deleteProduct = (id) => {
    if (confirm('Ви впевнені, що хочете видалити цей товар?')) {
        router.delete(route('admin.products.destroy', id));
    }
};
</script>

<template>
<!-- Встановлюємо заголовок вкладки браузера -->
    <Head title="Управління товарами" />
    <div class="min-h-screen bg-slate-950 text-slate-100 p-6 md:p-8 font-sans">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Шапка сторінки -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Управління товарами</h1>
                    <p class="text-xs text-slate-400 mt-1">Каталог взуття, залишки на складі та керування позиціями</p>
                </div>

                <Link
                    :href="route('admin.products.create')"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-sm transition shadow-lg shadow-amber-500/10"
                >
                    <span class="text-base">＋</span>
                    <span>Додати товар</span>
                </Link>
            </div>

            <!-- Фільтрація та Пошук -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">🔍</span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Пошук за назвою або артикулом..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none transition"
                    />
                </div>
                <div class="text-xs text-slate-400 self-end sm:self-center">
                    Всього товарів: <span class="font-bold text-amber-400">{{ products.total || products.data?.length || 0 }}</span>
                </div>
            </div>

            <!-- Таблиця товарів -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-950/60 border-b border-slate-800 text-[11px] uppercase font-semibold text-slate-400 tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Товар</th>
                                <th class="py-3.5 px-4">Категорія / Бренд</th>
                                <th class="py-3.5 px-4">Ціна</th>
                                <th class="py-3.5 px-4">Розміри / Залишок</th>
                                <th class="py-3.5 px-4">Статус</th>
                                <th class="py-3.5 px-4 text-right">Дії</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <tr
                                v-for="product in products.data"
                                :key="product.id"
                                class="hover:bg-slate-800/40 transition group"
                            >
                                <!-- Фото та Назва -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-slate-950 border border-slate-800 overflow-hidden flex-shrink-0 relative">
                                            <img
                                                v-if="product.images && product.images.length > 0"
                                                :src="product.images.find(img => img.is_main)?.path || product.images[0]?.path"
                                                :alt="product.title"
                                                class="w-full h-full object-cover"
                                            />
                                            <div v-else class="w-full h-full flex items-center justify-center text-slate-700 text-xs font-bold">
                                                NO IMG
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-100 text-sm group-hover:text-amber-400 transition">
                                                {{ product.title }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-mono">
                                                {{ product.slug }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Категорія / Бренд -->
                                <td class="py-3 px-4">
                                    <div class="text-xs text-slate-300 font-medium">{{ product.category?.name || '—' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ product.brand?.name || '—' }}</div>
                                </td>

                                <!-- Ціна -->
                                <td class="py-3 px-4">
                                    <div class="font-bold text-amber-400 text-sm">
                                        {{ product.price }} ₴
                                    </div>
                                    <div v-if="product.old_price" class="text-[11px] text-slate-500 line-through">
                                        {{ product.old_price }} ₴
                                    </div>
                                </td>

                                <!-- Розміри та Залишки -->
                                <td class="py-3 px-4">
                                    <div v-if="product.variants && product.variants.length > 0" class="flex flex-wrap gap-1 max-w-xs">
                                        <span
                                            v-for="variant in product.variants"
                                            :key="variant.id"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-950 border border-slate-800 text-[10px] text-slate-300"
                                        >
                                            <span class="font-semibold text-amber-400/90">{{ variant.size?.value }}</span>
                                            <span class="text-slate-500">({{ variant.stock }} шт)</span>
                                        </span>
                                    </div>
                                    <span v-else class="text-xs text-slate-600">Немає розмірів</span>
                                </td>

                                <!-- Статус -->
                                <td class="py-3 px-4">
                                    <span
                                        :class="product.is_active
                                            ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                                            : 'bg-rose-500/10 text-rose-400 border-rose-500/20'"
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-block"
                                    >
                                        {{ product.is_active ? 'Активний' : 'Чернетка' }}
                                    </span>
                                </td>

                                <!-- Дії -->
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('admin.products.edit', product.id)"
                                            class="p-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-400 hover:text-amber-400 hover:border-amber-500/40 transition"
                                            title="Редагувати"
                                        >
                                            ✏️
                                        </Link>
                                        <button
                                            @click="deleteProduct(product.id)"
                                            class="p-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-400 hover:text-rose-400 hover:border-rose-500/40 transition"
                                            title="Видалити"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Порожній стан -->
                            <tr v-if="!products.data || products.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <div class="text-3xl mb-2">👟</div>
                                    <p class="text-sm font-medium">Товарів поки не знайдено</p>
                                    <p class="text-xs text-slate-600 mt-1">Додайте перший товар або змініть параметри пошуку</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагінація -->
                <div v-if="products.links && products.links.length > 3" class="p-4 border-t border-slate-800 flex items-center justify-between bg-slate-950/40">
                    <div class="flex flex-wrap gap-1">
                        <Component
                            :is="link.url ? Link : 'span'"
                            v-for="(link, k) in products.links"
                            :key="k"
                            :href="link.url"
                            v-html="link.label"
                            :class="[
                                link.url ? 'hover:border-amber-500/50 hover:text-white' : 'opacity-40 cursor-not-allowed',
                                link.active ? 'bg-amber-500 text-slate-950 font-bold border-amber-400' : 'bg-slate-950 text-slate-400 border-slate-800',
                                'px-3 py-1.5 text-xs rounded-lg border transition'
                            ]"
                        />
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

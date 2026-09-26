<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    brands: Object,
});

const deleteBrand = (id) => {
    if (confirm('Ви впевнені, що хочете видалити цей бренд?')) {
        router.delete(route('admin.brands.destroy', id));
    }
};
</script>

<template>
    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">Бренди товарів</h1>
            <Link
                :href="route('admin.brands.create')"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-sm transition"
            >
                + Додати бренд
            </Link>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-lg">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800/80 text-slate-300 text-sm">
                        <th class="p-4">Назва</th>
                        <th class="p-4">Slug</th>
                        <th class="p-4">Опис</th>
                        <th class="p-4 text-right">Дії</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <tr v-for="brand in brands.data" :key="brand.id" class="hover:bg-slate-800/40">
                        <td class="p-4 font-medium text-white">{{ brand.name }}</td>
                        <td class="p-4 text-slate-400 font-mono text-xs">{{ brand.slug }}</td>
                        <td class="p-4 text-slate-400 text-sm truncate max-w-xs">{{ brand.description || '—' }}</td>
                        <td class="p-4 text-right space-x-2">
                            <Link
                                :href="route('admin.brands.edit', brand.id)"
                                class="px-3 py-1 bg-slate-800 hover:bg-slate-700 rounded text-xs text-slate-200 transition"
                            >
                                Редагувати
                            </Link>
                            <button
                                @click="deleteBrand(brand.id)"
                                class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-xs transition"
                            >
                                Видалити
                            </button>
                        </td>
                    </tr>
                    <tr v-if="brands.data.length === 0">
                        <td colspan="4" class="p-4 text-center text-slate-500 text-sm">
                            Брендів поки немає.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>

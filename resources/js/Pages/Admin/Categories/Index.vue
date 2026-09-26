<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    categories: Object,
});

const deleteCategory = (id) => {
    if (confirm('Ви впевнені, що хочете видалити цю категорію?')) {
        router.delete(route('admin.categories.destroy', id));
    }
};
</script>

<template>
    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">Категорії товарів</h1>
            <Link
                :href="route('admin.categories.create')"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-sm transition"
            >
                + Додати категорію
            </Link>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-lg">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800/80 text-slate-300 text-sm">
                        <th class="p-4">Назва</th>
                        <th class="p-4">Батьківська категорія</th>
                        <th class="p-4">Slug</th>
                        <th class="p-4 text-right">Дії</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <tr
                        v-for="category in categories.data"
                        :key="category.id"
                        class="hover:bg-slate-800/40 transition"
                    >
                        <!-- Назва з відступом для дочірньої категорії -->
                        <td class="p-4 font-medium text-white flex items-center gap-2">
                            <span v-if="category.parent_id" class="text-amber-500 font-bold pl-2">↳</span>
                            {{ category.name }}
                        </td>

                        <!-- Батьківська категорія -->
                        <td class="p-4 text-slate-400 text-sm">
                            <span v-if="category.parent" class="px-2.5 py-1 bg-amber-500/10 text-amber-400 rounded-md text-xs font-semibold border border-amber-500/20">
                                {{ category.parent.name }}
                            </span>
                            <span v-else class="text-slate-600">— (Головна)</span>
                        </td>

                        <!-- Slug -->
                        <td class="p-4 text-slate-400 font-mono text-xs">
                            <span class="bg-slate-800 px-2 py-1 rounded text-slate-300">
                                {{ category?.slug }}
                            </span>
                        </td>

                        <!-- Дії -->
                        <td class="p-4 text-right space-x-2">
                            <Link
                                :href="route('admin.categories.edit', category.id)"
                                class="px-3 py-1 bg-slate-800 hover:bg-slate-700 rounded text-xs text-slate-200 transition"
                            >
                                Редагувати
                            </Link>
                            <button
                                @click="deleteCategory(category.id)"
                                class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-xs transition"
                            >
                                Видалити
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>

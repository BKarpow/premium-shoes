<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    category: Object,
    parentCategories: Array,
});

const form = useForm({
    name: props.category.name,
    parent_id: props.category.parent_id || null,
    description: props.category.description || '',
});

const submit = () => {
    form.put(route('admin.categories.update', props.category.id));
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-xl mx-auto bg-slate-900 p-6 rounded-xl border border-slate-800 shadow-lg">
            <h1 class="text-xl font-bold text-white mb-6">Редагування категорії: {{ category.name }}</h1>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm mb-1 text-slate-300">Назва категорії</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white focus:outline-none focus:border-amber-500"
                        required
                    />
                    <div v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm mb-1 text-slate-300">Батьківська категорія</label>
                    <select
                        v-model="form.parent_id"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white focus:outline-none focus:border-amber-500"
                    >
                        <option :value="null">Без батьківської (Головна категорія)</option>
                        <option v-for="parent in parentCategories" :key="parent.id" :value="parent.id">
                            {{ parent.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.parent_id" class="text-red-400 text-xs mt-1">{{ form.errors.parent_id }}</div>
                </div>

                <div>
                    <label class="block text-sm mb-1 text-slate-300">Опис</label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white focus:outline-none focus:border-amber-500"
                    ></textarea>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-slate-800">
                    <Link :href="route('admin.categories.index')" class="text-slate-400 text-sm hover:text-white transition">
                        Скасувати
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-sm transition"
                    >
                        Оновити категорію
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

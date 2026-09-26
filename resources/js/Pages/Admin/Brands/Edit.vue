<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    brand: Object,
});

const form = useForm({
    name: props.brand.name,
    description: props.brand.description || '',
});

const submit = () => {
    form.put(route('admin.brands.update', props.brand.id));
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-xl mx-auto bg-slate-900 p-6 rounded-xl border border-slate-800 shadow-lg">
            <h1 class="text-xl font-bold text-white mb-6">Редагування бренду: {{ brand.name }}</h1>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm mb-1 text-slate-300">Назва бренду</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white focus:outline-none focus:border-amber-500"
                        required
                    />
                    <div v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm mb-1 text-slate-300">Опис</label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white focus:outline-none focus:border-amber-500"
                    ></textarea>
                    <div v-if="form.errors.description" class="text-red-400 text-xs mt-1">{{ form.errors.description }}</div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-slate-800">
                    <Link :href="route('admin.brands.index')" class="text-slate-400 text-sm hover:text-white transition">
                        Скасувати
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-sm transition"
                    >
                        Оновити бренд
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

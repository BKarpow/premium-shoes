<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    roles: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: props.roles[0] || 'customer',
});

const submit = () => {
    form.post(route('admin.users.store'));
};
</script>

<template>
    <Head title="Створення користувача"/>
<AdminLayout>
    <div class="max-w-2xl mx-auto p-6 bg-slate-900 text-white mt-10 rounded-xl shadow">
        <h1 class="text-xl font-bold mb-6">Створення нового користувача</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm mb-1 text-slate-300">Ім'я</label>
                <input v-model="form.name" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white" required />
                <div v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</div>
            </div>

            <div>
                <label class="block text-sm mb-1 text-slate-300">Email</label>
                <input v-model="form.email" type="email" class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white" required />
                <div v-if="form.errors.email" class="text-red-400 text-xs mt-1">{{ form.errors.email }}</div>
            </div>

            <div>
                <label class="block text-sm mb-1 text-slate-300">Пароль</label>
                <input v-model="form.password" type="password" class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white" required />
                <div v-if="form.errors.password" class="text-red-400 text-xs mt-1">{{ form.errors.password }}</div>
            </div>

            <div>
                <label class="block text-sm mb-1 text-slate-300">Призначати роль</label>
                <select v-model="form.role" class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2.5 text-white">
                    <option v-for="role in roles" :key="role" :value="role">
                        {{ role }}
                    </option>
                </select>
                <div v-if="form.errors.role" class="text-red-400 text-xs mt-1">{{ form.errors.role }}</div>
            </div>

            <div class="flex justify-between items-center pt-4">
                <Link :href="route('admin.users.index')" class="text-slate-400 text-sm hover:underline">Скасувати</Link>
                <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-sm">
                    Зберегти
                </button>
            </div>
        </form>
    </div>
</AdminLayout>
</template>

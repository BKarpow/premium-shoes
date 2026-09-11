<script setup>
import { Link, router, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    users: Object,
});

const deleteUser = (id) => {
    if (confirm('Ви впевнені, що хочете видалити цього користувача?')) {
        router.delete(route('admin.users.destroy', id));
    }
};
</script>

<template>
<Head title="Керування користувачами"/>
<AdminLayout>
    <div class="p-6 bg-slate-900 text-white min-h-screen">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Керування користувачами</h1>
            <Link
                :href="route('admin.users.create')"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-sm"
            >
                + Додати користувача
            </Link>
        </div>

        <div class="bg-slate-800 rounded-xl overflow-hidden shadow">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-700/50 text-slate-300 text-sm">
                        <th class="p-4">Ім'я</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Роль</th>
                        <th class="p-4 text-right">Дії</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-700/30">
                        <td class="p-4 font-medium">{{ user.name }}</td>
                        <td class="p-4 text-slate-400">{{ user.email }}</td>
                        <td class="p-4">
                            <span
                                v-for="role in user.roles"
                                :key="role.id"
                                class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20"
                            >
                                {{ role.name }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <Link
                                :href="route('admin.users.edit', user.id)"
                                class="px-3 py-1 bg-slate-700 hover:bg-slate-600 rounded text-xs text-slate-200"
                            >
                                Редагувати / Роль
                            </Link>
                            <button
                                @click="deleteUser(user.id)"
                                class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-xs"
                            >
                                Видалити
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const roles = computed(() => page.props.auth.user?.roles || []);
const isAdmin = computed(() => roles.value.includes('admin'));
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Панель керування</h1>
            <p class="text-slate-400 text-sm mt-1">Вітаємо в системі адміністрування інтернет-магазину.</p>
        </div>

        <!-- Сітка швидких дій / модулів -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Модуль Товарів -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-lg flex flex-col justify-between">
                <div>
                    <div class="text-3xl mb-3">📦</div>
                    <h2 class="text-xl font-bold text-white mb-2">Товари</h2>
                    <p class="text-slate-400 text-sm mb-4">Управління каталогом взуття, цінами, залишками та зображеннями.</p>
                </div>
                <Link
                    :href="route('admin.products.index')"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-amber-400 font-semibold text-sm rounded-lg transition"
                >
                    Перейти до товарів →
                </Link>
            </div>

            <!-- Модуль Категорій та Брендів -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-lg flex flex-col justify-between">
                <div>
                    <div class="text-3xl mb-3">🏷️</div>
                    <h2 class="text-xl font-bold text-white mb-2">Категорії та Бренди</h2>
                    <p class="text-slate-400 text-sm mb-4">Редагування структури категорій та виробників взуття.</p>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="route('admin.categories.index')"
                        class="flex-1 text-center py-2 bg-slate-800 hover:bg-slate-700 text-amber-400 font-semibold text-xs rounded-lg transition"
                    >
                        Категорії
                    </Link>
                    <Link
                        :href="route('admin.brands.index')"
                        class="flex-1 text-center py-2 bg-slate-800 hover:bg-slate-700 text-amber-400 font-semibold text-xs rounded-lg transition"
                    >
                        Бренди
                    </Link>
                </div>
            </div>

            <!-- Модуль Користувачів (Тільки для Admin) -->
            <div v-if="isAdmin" class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-lg flex flex-col justify-between">
                <div>
                    <div class="text-3xl mb-3">👥</div>
                    <h2 class="text-xl font-bold text-white mb-2">Користувачі та Ролі</h2>
                    <p class="text-slate-400 text-sm mb-4">Управління акаунтами, призначення ролей (`admin`, `manager`, `customer`).</p>
                </div>
                <Link
                    :href="route('admin.users.index')"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm rounded-lg transition"
                >
                    Керувати користувачами →
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>

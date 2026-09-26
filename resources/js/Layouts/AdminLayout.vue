<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

const page = usePage();

// Отримуємо ролі поточного користувача з Inertia props
const roles = computed(() => page.props.auth.user?.roles || []);

// Перевірка прав для відображення пунктів меню
console.debug("roels", roles);
const isAdmin = computed(() => roles.value[0].name == 'admin');
const isAdminOrManager = computed(() => roles.value[0].name == 'admin' || roles.value[0].name == 'manager');
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex">
        <!-- Бічне меню (Sidebar) -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0">
            <div>
                <!-- Логотип / Назва -->
                <div class="p-6 border-b border-slate-800">
                    <Link :href="route('admin.dashboard')" class="text-xl font-bold text-amber-500 tracking-wide flex items-center gap-2">
                        <span>👟</span> Admin Panel
                    </Link>
                </div>

                <!-- Навігаційне меню -->
                <nav class="p-4 space-y-1">
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Навігація
                    </div>

                    <!-- Дашборд -->
                    <Link
                        :href="route('admin.dashboard')"
                        :class="[
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition',
                            route().current('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800'
                        ]"
                    >
                        <span>📊</span> Дашборд
                    </Link>

                    <div class="pt-4 px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Каталог
                    </div>

                    <!-- Список товарів -->
                    <Link
                        v-if="isAdminOrManager"
                        :href="route('admin.products.index')"
                        :class="[
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition',
                            route().current('admin.products.index') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800'
                        ]"
                    >
                        <span>📦</span> Список товарів
                    </Link>

                    <!-- Додати товар -->
                    <Link
                        v-if="isAdminOrManager"
                        :href="route('admin.products.create')"
                        :class="[
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition',
                            route().current('admin.products.create') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800'
                        ]"
                    >
                        <span>➕</span> Додати товар
                    </Link>

                    <Link
                        :href="route('admin.orders.index')"
                        :class="[
                            route().current('admin.orders.*')
                                ? 'bg-amber-500/10 text-amber-400 border-amber-500/50'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900',
                            'flex items-center gap-3 px-4 py-2.5 rounded-xl border border-transparent font-medium text-sm transition-all duration-200'
                        ]"
                    >
                        <span class="text-lg">📦</span>
                        <span>Замовлення</span>
                    </Link>

                    <!-- Категорії -->
                    <Link
                        v-if="isAdminOrManager"
                        :href="route('admin.categories.index')"
                        :class="[
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition',
                            route().current('admin.categories.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800'
                        ]"
                    >
                        <span>📁</span> Категорії
                    </Link>

                    <!-- Бренди -->
                    <Link
                        v-if="isAdminOrManager"
                        :href="route('admin.brands.index')"
                        :class="[
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition',
                            route().current('admin.brands.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800'
                        ]"
                    >
                        <span>🏷️</span> Бренди
                    </Link>

                    <!-- Адміністрування користувачів (Тільки для Admin) -->
                    <template v-if="isAdmin">
                        <div class="pt-4 px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Адміністрування
                        </div>

                        <Link
                            :href="route('admin.users.index')"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition',
                                route().current('admin.users.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800'
                            ]"
                        >
                            <span>👥</span> Користувачі та Ролі
                        </Link>
                    </template>
                </nav>
            </div>

            <!-- Нижня частина сайдбару -->
            <div class="p-4 border-t border-slate-800 space-y-2">
                <Link
                    href="/"
                    class="flex items-center gap-2 w-full px-3 py-2 rounded-lg text-xs font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition"
                >
                    <span>🏠</span> На сайт магазину
                </Link>

                <div class="flex items-center justify-between px-3 py-2 bg-slate-800/50 rounded-lg">
                    <div class="truncate">
                        <p class="text-xs font-medium text-white truncate">{{ page.props.auth.user?.name }}</p>
                        <p class="text-[10px] text-amber-400 font-semibold uppercase">{{ roles.join(', ') }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Основна область контенту -->
        <main class="flex-1 p-8 overflow-y-auto">
            <slot />
        </main>
    </div>
</template>

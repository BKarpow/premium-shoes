<script setup>
import { ref, watch, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

// Стан мобільного меню (drawer)
const isSidebarOpen = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
    isSidebarOpen.value = false;
};

// Блокування скролу сторінки, коли відкрите мобільне сайдбар-меню
watch(isSidebarOpen, (isOpen) => {
    if (isOpen) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

onUnmounted(() => {
    document.body.style.overflow = '';
});

// Навігаційне меню адмінки
const navLinks = [
    { name: 'Дашборд', routeName: 'admin.dashboard', icon: '📊' },
    { name: 'Товари', routeName: 'admin.products.index', icon: '👟' },
    { name: 'Категорії', routeName: 'admin.categories.index', icon: '📁' },
    { name: 'Бренди', routeName: 'admin.brands.index', icon: '🏷️' },
    { name: 'Замовлення', routeName: 'admin.orders.index', icon: '📦' },
    { name: 'Користувачі', routeName: 'admin.users.index', icon: '👥' },
];
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans flex flex-col sm:flex-row">
        <!-- ================= 1. ЗАТЕМНЕННЯ ФОНУ ДЛЯ МОБІЛЬНОГО МЕНЮ ================= -->
        <Transition
            enter-active-class="transition-opacity ease-linear duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-linear duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isSidebarOpen"
                @click="closeSidebar"
                class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm sm:hidden"
            ></div>
        </Transition>

        <!-- ================= 2. БОКОВЕ МЕНЮ (САЙДБАР) ================= -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 flex flex-col transform transition-transform duration-300 ease-in-out sm:translate-x-0 sm:static sm:z-auto shrink-0"
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Шапка сайдбару -->
            <div class="flex items-center justify-between px-5 h-16 border-b border-slate-800 bg-slate-900/50">
                <Link :href="route('admin.dashboard')" class="flex items-center gap-2 font-black text-amber-500 text-lg tracking-wider uppercase">
                    <span>👑</span>
                    <span>Admin Panel</span>
                </Link>

                <!-- Кнопка закриття (лише для смартфона) -->
                <button
                    @click="closeSidebar"
                    class="sm:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Список розділів адмінки -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1.5">
                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Управління
                </div>

                <Link
                    v-for="item in navLinks"
                    :key="item.routeName"
                    :href="route(item.routeName)"
                    @click="closeSidebar"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
                    :class="route().current(item.routeName)
                        ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                >
                    <span class="text-base">{{ item.icon }}</span>
                    <span>{{ item.name }}</span>
                </Link>
            </nav>

            <!-- Нижня частина сайдбару (Повернення на сайт) -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40 space-y-2">
                <Link
                    :href="route('catalog.index')"
                    class="flex items-center justify-center gap-2 w-full px-3 py-2 rounded-xl border border-slate-700/80 bg-slate-800/50 text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition"
                >
                    <span>🌐</span>
                    <span>Перейти на сайт</span>
                </Link>
            </div>
        </aside>

        <!-- ================= 3. ОСНОВНА ОБЛАСТЬ КОНТЕНТУ ================= -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            <!-- Верхня панель (Topbar) -->
            <header class="h-16 bg-slate-900/80 backdrop-blur-md border-b border-slate-800 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <!-- Кнопка відкриття бокового меню на мобільних -->
                    <button
                        @click="toggleSidebar"
                        class="sm:hidden p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none active:scale-95 transition"
                        aria-label="Open menu"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <h1 class="text-sm sm:text-base font-bold text-slate-100 truncate">
                        <slot name="header">Панель управління</slot>
                    </h1>
                </div>

                <!-- Інформація про адміністратора -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-200">{{ page.props.auth?.user?.name || 'Адміністратор' }}</span>
                        <span class="text-[10px] text-amber-500 font-semibold">Super Admin</span>
                    </div>
                    <div class="h-9 w-9 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 font-bold text-xs">
                        {{ page.props.auth?.user?.name ? page.props.auth.user.name.charAt(0).toUpperCase() : 'A' }}
                    </div>
                </div>
            </header>

            <!-- Основний контент сторінки -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

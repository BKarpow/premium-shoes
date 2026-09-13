<script setup>
import { Link } from '@inertiajs/vue3';
import UserNavbar from '@/Components/UserNavbar.vue';
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans">
        <!-- Шапка сайту (Header) -->
        <header class="sticky top-0 z-40 border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <!-- Логотип -->
                <Link :href="route('catalog.index')" class="flex items-center gap-2 group">
                    <span class="text-2xl">👟</span>
                    <span class="text-lg font-black tracking-wider text-white uppercase group-hover:text-amber-500 transition-colors">
                        Sneaker<span class="text-amber-500">Store</span>
                    </span>
                </Link>

                <!-- Основні посилання каталогу -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-400">
                    <Link
                        :href="route('catalog.index')"
                        :class="[
                            'transition hover:text-white',
                            route().current('catalog.index') ? 'text-amber-500 font-semibold' : ''
                        ]"
                    >
                        Каталог
                    </Link>
                </nav>

                <!-- Права частина: Навбар користувача та Кошик -->
                <div class="flex items-center gap-4">
                    <UserNavbar />
                </div>
            </div>
        </header>

        <!-- Основна область контенту -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Футер -->
        <footer class="border-t border-slate-900 bg-slate-950 py-8 text-center text-xs text-slate-500">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p>© {{ new Date().getFullYear() }} SneakerStore. Всі права захищені.</p>
            </div>
        </footer>
    </div>
</template>

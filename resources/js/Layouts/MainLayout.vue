<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import UserNavbar from '@/Components/UserNavbar.vue';

const page = usePage();

// Отримуємо ролі поточного користувача з Inertia props
const roles = computed(() => page.props.auth.user?.roles || []);

// Отримуємо глобальні категорії з Inertia props
const categories = computed(() => page.props.categories || []);

// Керування випадаючим меню каталогів
const isCatalogOpen = ref(false);
const catalogDropdownRef = ref(null);

const toggleCatalogMenu = () => {
    isCatalogOpen.value = !isCatalogOpen.value;
};

// Закриття каталогу при кліку поза межами
const closeCatalogOnOutsideClick = (event) => {
    if (catalogDropdownRef.value && !catalogDropdownRef.value.contains(event.target)) {
        isCatalogOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('click', closeCatalogOnOutsideClick);
});

onUnmounted(() => {
    window.removeEventListener('click', closeCatalogOnOutsideClick);
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans">
        <!-- Шапка сайту (Header) -->
        <header class="sticky top-0 z-40 border-b border-slate-800/80 bg-slate-950/85 backdrop-blur-md">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">

                <!-- Ліва частина: Логотип + Кнопка Каталогу -->
                <div class="flex items-center gap-6">
                    <!-- Логотип -->
                    <Link :href="route('catalog.index')" class="flex items-center gap-2 group">
                        <span class="text-2xl">👟</span>
                        <span class="text-lg font-black tracking-wider text-white uppercase group-hover:text-amber-500 transition-colors">
                            Sneaker<span class="text-amber-500">Store</span>
                        </span>
                    </Link>

                    <!-- Помітна кнопка "Каталог" з випадаючим списком категорій (для десктопу) -->
                    <div ref="catalogDropdownRef" class="relative hidden sm:block">
                        <button
                            @click="toggleCatalogMenu"
                            class="flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 shadow-lg shadow-amber-500/10 transition hover:bg-amber-400 focus:outline-none"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <span>Каталог</span>
                            <svg
                                class="h-3.5 w-3.5 transition-transform duration-200"
                                :class="{ 'rotate-180': isCatalogOpen }"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Випадаюче меню категорій -->
                        <Transition
                            enter-active-class="transition duration-100 ease-out"
                            enter-from-class="transform scale-95 opacity-0"
                            enter-to-class="transform scale-100 opacity-100"
                            leave-active-class="transition duration-75 ease-in"
                            leave-from-class="transform scale-100 opacity-100"
                            leave-to-class="transform scale-95 opacity-0"
                        >
                            <div
                                v-if="isCatalogOpen"
                                class="absolute left-0 mt-2 w-56 rounded-xl border border-slate-800 bg-slate-900 py-2 shadow-2xl z-50 text-slate-200"
                            >
                                <div class="px-3 py-1 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">
                                    Категорії взуття
                                </div>
                                <div class="mt-1 divide-y divide-slate-800/60">
                                    <div class="py-1">
                                        <Link
                                            :href="route('catalog.index')"
                                            class="flex items-center px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800/70 hover:text-white transition"
                                            @click="isCatalogOpen = false"
                                        >
                                            🔥 Усі товари
                                        </Link>
                                    </div>
                                    <div class="py-1">
                                        <Link
                                            v-for="category in categories"
                                            :key="category.id"
                                            :href="route('catalog.index', { category: category.slug })"
                                            class="flex items-center px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800/70 hover:text-amber-400 transition"
                                            @click="isCatalogOpen = false"
                                        >
                                            {{ category.name }}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- Права частина: Адаптивний кошик та Меню користувача (всередині UserNavbar) -->
                <div class="flex items-center gap-3">
                    <UserNavbar />
                </div>
            </div>

            <!-- Мобільна навігація для категорій під шапкою -->
            <div class="flex sm:hidden overflow-x-auto px-4 py-2 border-t border-slate-900 bg-slate-950 gap-2 no-scrollbar">
                <Link
                    :href="route('catalog.index')"
                    class="whitespace-nowrap px-3 py-1 rounded-lg bg-amber-500 text-slate-950 text-xs font-bold"
                >
                    Всі
                </Link>
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="route('catalog.index', { category: category.slug })"
                    class="whitespace-nowrap px-3 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 text-xs font-medium hover:text-white"
                >
                    {{ category.name }}
                </Link>
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

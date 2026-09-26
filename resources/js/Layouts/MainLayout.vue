<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import UserNavbar from '@/Components/UserNavbar.vue';
import CatalogMenu from '@/Components/CatalogMenu.vue';

const page = usePage();

// Отримуємо глобальні ієрархічні категорії з Inertia props (HandleInertiaRequests)
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
                            Premium
                        </span>
                    </Link>


                </div>

                <!-- Права частина: Адаптивний кошик та Меню користувача -->
                <div class="flex items-center gap-3">
                    <UserNavbar />
                </div>
            </div>

            <!-- Мобільна навігація для категорій під шапкою -->
            <div class="ml-7 gap-3">
                <CatalogMenu />
            </div>

        </header>

        <!-- Основна область контенту -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Футер -->
        <footer class="border-t border-slate-900 bg-slate-950 py-8 text-center text-xs text-slate-500">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p>© {{ new Date().getFullYear() }} Premium Shoe Store. Всі права захищені.</p>
            </div>
        </footer>
    </div>
</template>

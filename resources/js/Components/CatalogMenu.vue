<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

// Отримуємо категорії
const categories = computed(() => page.props.categories || []);

// Стан меню
const isCatalogOpen = ref(false);
const catalogRef = ref(null);
const activeCategory = ref(null);

// Для перемикання підкатегорій на мобільному (за замовчуванням усі категорії розгорнуті для максимальної зручності)
const openMobileCategories = ref({});

const toggleCatalog = () => {
    isCatalogOpen.value = !isCatalogOpen.value;
    if (isCatalogOpen.value && categories.value.length > 0) {
        if (!activeCategory.value) {
            activeCategory.value = categories.value[0];
        }
        // За замовчуванням відкриваємо підкатегорії для всіх батьківських категорій на мобільному
        categories.value.forEach(cat => {
            openMobileCategories.value[cat.id] = true;
        });
    }
};

const closeCatalog = () => {
    isCatalogOpen.value = false;
};

const selectCategory = (category) => {
    activeCategory.value = category;
};

const toggleMobileCategory = (id) => {
    openMobileCategories.value[id] = !openMobileCategories.value[id];
};

// Блокування скролу заднього фону на мобільних при відкритому меню
watch(isCatalogOpen, (isOpen) => {
    if (isOpen) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

const closeOnOutsideClick = (event) => {
    if (catalogRef.value && !catalogRef.value.contains(event.target)) {
        closeCatalog();
    }
};

onMounted(() => {
    window.addEventListener('click', closeOnOutsideClick);
});

onUnmounted(() => {
    window.removeEventListener('click', closeOnOutsideClick);
    document.body.style.overflow = '';
});
</script>

<template>
    <div ref="catalogRef" class="relative">
        <!-- Кнопка відкриття каталогу -->
        <button
            @click="toggleCatalog"
            class="flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400 focus:outline-none active:scale-95"
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

        <!-- ================= 1. ДЕСКТОПНЕ ПІДМЕНЮ (sm+) ================= -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-if="isCatalogOpen"
                class="hidden sm:flex absolute left-0 mt-2 w-[560px] min-h-[340px] rounded-2xl border border-slate-800 bg-slate-900/95 shadow-2xl backdrop-blur-xl z-50 text-slate-200 overflow-hidden"
            >
                <!-- Ліва колонка: Головні категорії -->
                <div class="w-1/2 border-r border-slate-800/80 p-3 space-y-1 bg-slate-950/40">
                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-amber-500">
                        Категорії
                    </div>

                    <Link
                        :href="route('catalog.index')"
                        class="flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition"
                        @click="closeCatalog"
                    >
                        <span>🔥 Усі товари</span>
                    </Link>

                    <div
                        v-for="category in categories"
                        :key="category.id"
                        @mouseenter="selectCategory(category)"
                        class="flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold cursor-pointer transition"
                        :class="activeCategory?.id === category.id
                            ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30'
                            : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'"
                    >
                        <Link
                            :href="route('catalog.index', { category: category.slug })"
                            class="flex-1"
                            @click="closeCatalog"
                        >
                            {{ category.name }}
                        </Link>
                        <span v-if="category.children && category.children.length > 0" class="text-xs text-slate-500">›</span>
                    </div>
                </div>

                <!-- Права колонка: Дочірні підкатегорії -->
                <div class="w-1/2 p-4 bg-slate-900/60 flex flex-col justify-between">
                    <div v-if="activeCategory">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2 mb-3">
                            <Link
                                :href="route('catalog.index', { category: activeCategory.slug })"
                                class="text-xs font-bold text-amber-400 hover:underline"
                                @click="closeCatalog"
                            >
                                {{ activeCategory.name }}
                            </Link>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider">Підкатегорії</span>
                        </div>

                        <div
                            v-if="activeCategory.children && activeCategory.children.length > 0"
                            class="space-y-1 max-h-[230px] overflow-y-auto pr-1"
                        >
                            <Link
                                v-for="child in activeCategory.children"
                                :key="child.id"
                                :href="route('catalog.index', { category: child.slug })"
                                class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs text-slate-300 hover:bg-slate-800 hover:text-white transition"
                                @click="closeCatalog"
                            >
                                <span class="text-amber-500 font-bold">↳</span>
                                <span>{{ child.name }}</span>
                            </Link>
                        </div>

                        <div v-else class="text-xs text-slate-500 py-8 text-center italic">
                            Немає вкладених підкатегорій
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ================= 2. ІДЕАЛЬНЕ МОБІЛЬНЕ МЕНЮ (ПОВНИЙ ЕКРАН) ================= -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-2"
            >
                <div
                    v-if="isCatalogOpen"
                    class="fixed inset-0 z-50 flex flex-col bg-slate-950 sm:hidden text-slate-100"
                >
                    <!-- Фіксована шапка мобільного меню -->
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800 bg-slate-900/90 backdrop-blur-md shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">👟</span>
                            <span class="font-black text-sm text-amber-500 uppercase tracking-wider">Каталог товарів</span>
                        </div>
                        <button
                            @click="closeCatalog"
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 text-slate-400 hover:text-white focus:outline-none active:scale-95"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Контент з можливістю скролу -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        <!-- Головна кнопка "Усі товари" -->
                        <Link
                            :href="route('catalog.index')"
                            class="flex items-center justify-between w-full px-4 py-3.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-amber-500 to-amber-400 rounded-xl shadow-lg shadow-amber-500/10 active:scale-98 transition"
                            @click="closeCatalog"
                        >
                            <span class="flex items-center gap-2">
                                <span>🔥</span>
                                <span class="uppercase tracking-wider">Переглянути всі товари</span>
                            </span>
                            <span>→</span>
                        </Link>

                        <!-- Список категорій із рівненьким вирівнюванням -->
                        <div class="space-y-3">
                            <div
                                v-for="category in categories"
                                :key="category.id"
                                class="rounded-2xl border border-slate-800/80 bg-slate-900/70 p-3.5 shadow-md"
                            >
                                <!-- Заголовок категорії -->
                                <div class="flex items-center justify-between pb-2 border-b border-slate-800/60 mb-2">
                                    <Link
                                        :href="route('catalog.index', { category: category.slug })"
                                        class="text-sm font-bold text-amber-400 hover:text-amber-300 flex items-center gap-1.5"
                                        @click="closeCatalog"
                                    >
                                        <span>{{ category.name }}</span>
                                    </Link>

                                    <!-- Перемикач відображення (якщо є підкатегорії) -->
                                    <button
                                        v-if="category.children && category.children.length > 0"
                                        @click.stop.prevent="toggleMobileCategory(category.id)"
                                        type="button"
                                        class="text-xs font-medium text-slate-400 hover:text-slate-200 flex items-center gap-1 bg-slate-800/60 px-2.5 py-1 rounded-lg"
                                    >
                                        <span>{{ openMobileCategories[category.id] ? 'Сховати' : 'Показати' }}</span>
                                        <svg
                                            class="w-3.5 h-3.5 transition-transform duration-200"
                                            :class="{ 'rotate-180': openMobileCategories[category.id] }"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Рівненька сітка підкатегорій (Chips / Badges) -->
                                <div
                                    v-if="category.children && category.children.length > 0 && openMobileCategories[category.id]"
                                    class="grid grid-cols-2 gap-2 pt-1"
                                >
                                    <Link
                                        v-for="child in category.children"
                                        :key="child.id"
                                        :href="route('catalog.index', { category: child.slug })"
                                        class="flex items-center gap-1.5 rounded-xl bg-slate-950/80 border border-slate-800/80 px-3 py-2 text-xs font-medium text-slate-300 hover:border-amber-500/50 hover:text-amber-400 transition truncate"
                                        @click="closeCatalog"
                                    >
                                        <span class="text-amber-500 text-[10px]">↳</span>
                                        <span class="truncate">{{ child.name }}</span>
                                    </Link>
                                </div>

                                <!-- Якщо підкатегорій немає -->
                                <div
                                    v-else-if="!category.children || category.children.length === 0"
                                    class="text-[11px] text-slate-500 italic pt-1"
                                >
                                    Прямий перехід до товарів категорії
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import CartDrawer from '@/Components/Cart/CartDrawer.vue';

const page = usePage();

// Отримуємо поточного користувача, ролі та дані кошика з Inertia props
const user = computed(() => page.props.auth?.user);
const roles = computed(() => user.value?.roles || []);
const cart = computed(() => page.props.cart || { total_count: 0 });

// Перевірка, чи користувач є адміністратором або менеджером
const isAdminOrManager = computed(() => {
    return roles.value[0]?.name == 'admin' || roles.value[0]?.name == 'manager';
});

// Керування станом випадаючого меню користувача
const isMenuOpen = ref(false);
const dropdownRef = ref(null);

// Керування станом висувної панелі кошика
const isCartOpen = ref(false);

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

// Закриття меню при кліку поза його межами
const closeMenuOnOutsideClick = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isMenuOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('click', closeMenuOnOutsideClick);
});

onUnmounted(() => {
    window.removeEventListener('click', closeMenuOnOutsideClick);
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="flex items-center gap-3">
        <!-- Кнопка кошика з лічильником -->
        <button
            @click="isCartOpen = true"
            class="relative flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-900/80 px-3 py-2 text-slate-200 transition hover:border-slate-700 hover:bg-slate-800 focus:outline-none"
            title="Відкрити кошик"
        >
            <span class="text-base">🛒</span>
            <span class="hidden text-xs font-semibold sm:block">Кошик</span>

            <!-- Бейдж з кількістю товарів -->
            <span
                v-if="cart.total_count > 0"
                class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-slate-950 shadow"
            >
                {{ cart.total_count }}
            </span>
        </button>

        <!-- Висувна панель кошика (Drawer) -->
        <CartDrawer :is-open="isCartOpen" @close="isCartOpen = false" />

        <!-- Якщо користувач авторизований -->
        <div v-if="user" ref="dropdownRef" class="relative">
            <button
                @click.stop="toggleMenu"
                class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900/80 px-3 py-1.5 transition hover:border-slate-700 hover:bg-slate-800 focus:outline-none"
            >
                <!-- Аватар з першою літерою імені -->
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 font-bold text-slate-950 text-xs shadow-inner">
                    {{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
                </div>
                <div class="hidden text-left sm:block">
                    <p class="text-xs font-semibold text-slate-100 leading-tight">{{ user.name }}</p>
                    <p class="text-[10px] text-slate-400 leading-tight truncate max-w-[120px]">{{ user.email }}</p>
                </div>
                <!-- Стрілка вниз, що обертається -->
                <svg
                    class="h-4 w-4 text-slate-400 transition-transform duration-200"
                    :class="{ 'rotate-180': isMenuOpen }"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Випадаюче меню -->
            <Transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <div
                    v-if="isMenuOpen"
                    class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-800 bg-slate-900 py-2 shadow-2xl z-50 text-slate-200 divide-y divide-slate-800/80"
                >
                    <!-- Інформація про юзера для мобільних екранів -->
                    <div class="px-4 py-2.5 sm:hidden">
                        <p class="text-xs font-semibold text-slate-100">{{ user.name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ user.email }}</p>
                    </div>

                    <!-- Основні посилання особистого кабінету -->
                    <div class="py-1">
                        <!-- Посилання на Адмін-панель (доступно тільки admin та manager) -->
                        <Link
                            v-if="isAdminOrManager"
                            :href="route('admin.dashboard')"
                            class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-amber-400 hover:bg-slate-800/70 transition"
                            @click="isMenuOpen = false"
                        >
                            <span>⚙️</span> Адмін-панель
                        </Link>

                        <!-- Мої дані (заглушка / профіль) -->
                        <Link
                            :href="route('profile.edit')"
                            class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800/70 hover:text-white transition"
                            @click="isMenuOpen = false"
                        >
                            <span>👤</span> Мої дані
                        </Link>

                        <!-- Мої замовлення (заглушка) -->
                        <Link
                            href="#"
                            class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800/70 hover:text-white transition"
                            @click="isMenuOpen = false"
                        >
                            <span>📦</span> Мої замовлення
                        </Link>
                    </div>

                    <!-- Вихід із системи -->
                    <div class="py-1">
                        <button
                            @click="logout"
                            class="flex w-full items-center gap-2.5 px-4 py-2 text-xs font-medium text-red-400 hover:bg-slate-800/70 transition"
                        >
                            <span>🚪</span> Вийти
                        </button>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Якщо користувач гість (не залогінений) -->
        <div v-else class="flex items-center gap-2">
            <Link
                :href="route('login')"
                class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white"
            >
                Увійти
            </Link>
            <Link
                :href="route('register')"
                class="rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 transition hover:bg-amber-400"
            >
                Реєстрація
            </Link>
        </div>
    </div>
</template>

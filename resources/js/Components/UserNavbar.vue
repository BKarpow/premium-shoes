<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import CartDrawer from '@/Components/Cart/CartDrawer.vue';

const page = usePage();
const cart = computed(() => page.props.cart || { total_count: 0 });
const isCartOpen = ref(false);
</script>

<template>
    <div class="flex items-center gap-4">
        <!-- Кнопка кошика -->
        <button
            @click="isCartOpen = true"
            class="relative flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-900/80 px-3 py-2 text-slate-200 transition hover:border-slate-700 hover:bg-slate-800 focus:outline-none"
        >
            <span class="text-lg">🛒</span>
            <span class="hidden text-xs font-semibold sm:block">Кошик</span>

            <span
                v-if="cart.total_count > 0"
                class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-slate-950"
            >
                {{ cart.total_count }}
            </span>
        </button>

        <!-- Висувна панель кошика -->
        <CartDrawer :is-open="isCartOpen" @close="isCartOpen = false" />

        <!-- ...решта вашого коду UserNavbar (профіль/авторизація)... -->
    </div>
</template>

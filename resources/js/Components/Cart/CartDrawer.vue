<script setup>
import { computed } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';

defineProps({
    isOpen: Boolean,
});

const emit = defineEmits(['close']);

const page = usePage();
const cart = computed(() => page.props.cart || { items: [], total: 0, total_count: 0 });

const updateQuantity = (variantId, newQuantity) => {
    if (newQuantity < 1) {
        removeItem(variantId);
        return;
    }
    router.patch(route('cart.update', variantId), {
        quantity: newQuantity,
    }, {
        preserveScroll: true,
    });
};

const removeItem = (variantId) => {
    router.delete(route('cart.remove', variantId), {
        preserveScroll: true,
    });
};

const clearCart = () => {
    router.delete(route('cart.clear'), {
        preserveScroll: true,
    });
};

const closeDrawer = () => {
    emit('close');
};
</script>

<template>
    <Teleport to="body">
        <div v-if="isOpen" class="relative z-50">
            <!-- Затемнення фону -->
            <div
                class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                @click="closeDrawer"
            ></div>

            <!-- Контейнер панелі (на мобільних на весь екран, на десктопах збоку) -->
            <div class="fixed inset-y-0 right-0 flex w-full max-w-full sm:max-w-md pl-0 sm:pl-10">
                <div class="w-full h-full border-l border-slate-800 bg-slate-900 text-slate-100 shadow-2xl flex flex-col">

                    <!-- Шапка висувної панелі -->
                    <div class="flex items-center justify-between border-b border-slate-800 px-4 sm:px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🛒</span>
                            <h2 class="text-base sm:text-lg font-bold text-white">Ваш кошик</h2>
                            <span v-if="cart.total_count > 0" class="rounded-full bg-amber-500/10 px-2 py-0.5 text-xs font-bold text-amber-500 border border-amber-500/20">
                                {{ cart.total_count }}
                            </span>
                        </div>
                        <button
                            @click="closeDrawer"
                            class="rounded-lg p-2 text-slate-400 hover:bg-slate-800 hover:text-white transition"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Порожній кошик -->
                    <div v-if="!cart.items.length" class="flex flex-1 flex-col items-center justify-center p-6 text-center">
                        <div class="mb-4 text-5xl opacity-40">👟</div>
                        <p class="text-base font-semibold text-slate-300">Ваш кошик порожній</p>
                        <p class="mt-1 text-xs text-slate-500">Оберіть пару взуття у каталозі, щоб зробити замовлення.</p>
                        <button
                            @click="closeDrawer"
                            class="mt-6 rounded-xl bg-amber-500 px-6 py-3 text-xs font-bold text-slate-950 hover:bg-amber-400 transition"
                        >
                            Перейти до каталогу
                        </button>
                    </div>

                    <!-- Список товарів -->
                    <template v-else>
                        <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 space-y-3 sm:space-y-4">
                            <div
                                v-for="item in cart.items"
                                :key="item.variant_id"
                                class="flex gap-3 sm:gap-4 rounded-xl border border-slate-800 bg-slate-950/60 p-3 transition hover:border-slate-700"
                            >
                                <img
                                    :src="item.image"
                                    :alt="item.name"
                                    class="h-16 w-16 sm:h-20 sm:w-20 rounded-lg object-cover bg-slate-900 border border-slate-800 flex-shrink-0"
                                />

                                <div class="flex flex-1 flex-col justify-between">
                                    <div>
                                        <div class="flex items-start justify-between gap-2">
                                            <h3 class="text-xs sm:text-sm font-semibold text-white leading-tight line-clamp-1">
                                                {{ item.name }}
                                            </h3>
                                            <button
                                                @click="removeItem(item.variant_id)"
                                                class="text-xs text-slate-500 hover:text-red-400 transition p-1"
                                                title="Видалити"
                                            >
                                                🗑️
                                            </button>
                                        </div>
                                        <div class="mt-1 flex flex-wrap gap-1.5 text-[10px] sm:text-[11px] text-slate-400">
                                            <span v-if="item.size" class="rounded bg-slate-800 px-1.5 py-0.5">Розмір: {{ item.size }}</span>
                                            <span v-if="item.color" class="rounded bg-slate-800 px-1.5 py-0.5">{{ item.color }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mt-3">
                                        <!-- Кнопки зміни кількості -->
                                        <div class="flex items-center rounded-lg border border-slate-800 bg-slate-900">
                                            <button
                                                @click="updateQuantity(item.variant_id, item.quantity - 1)"
                                                class="px-2.5 py-1 text-xs font-bold text-slate-400 hover:text-white transition"
                                            >
                                                -
                                            </button>
                                            <span class="px-2 text-xs font-semibold text-slate-200">{{ item.quantity }}</span>
                                            <button
                                                @click="updateQuantity(item.variant_id, item.quantity + 1)"
                                                :disabled="item.quantity >= item.max_stock"
                                                class="px-2.5 py-1 text-xs font-bold text-slate-400 hover:text-white transition disabled:opacity-30"
                                            >
                                                +
                                            </button>
                                        </div>

                                        <p class="text-xs sm:text-sm font-bold text-amber-500">
                                            {{ item.subtotal.toLocaleString() }} грн
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Нижній блок підсумку та оформлення -->
                        <div class="border-t border-slate-800 bg-slate-900/95 p-4 sm:p-6 space-y-3 sm:space-y-4">
                            <div class="flex justify-between text-xs text-slate-400">
                                <button @click="clearCart" class="hover:text-red-400 transition underline">
                                    Очистити кошик
                                </button>
                                <span>Товарів: {{ cart.total_count }} шт.</span>
                            </div>

                            <div class="flex items-center justify-between text-base font-bold">
                                <span class="text-slate-300">До сплати:</span>
                                <span class="text-lg sm:text-xl text-amber-500">{{ cart.total.toLocaleString() }} грн</span>
                            </div>

                            <Link
                                :href="route('checkout.index')"
                                @click="closeDrawer"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 py-3.5 text-xs sm:text-sm font-bold text-slate-950 hover:bg-amber-400 transition shadow-lg shadow-amber-500/10"
                            >
                                Оформити замовлення ➔
                            </Link>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </Teleport>
</template>

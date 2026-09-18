<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
  order: Object,
});
</script>

<template>
  <Head :title="`Замовлення №${order.id} успішно створено`" />

  <MainLayout>
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">

      <!-- Успішне повідомлення -->
      <div class="rounded-3xl border border-slate-800 bg-slate-900/90 p-8 sm:p-10 shadow-2xl backdrop-blur-sm text-center space-y-6">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 shadow-lg shadow-amber-500/10">
          <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
          </svg>
        </div>

        <div class="space-y-2">
          <span class="text-xs font-bold uppercase tracking-widest text-amber-400">Дякуємо за покупку!</span>
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
            Замовлення №{{ order.id }} успішно створено
          </h1>
          <p class="text-sm text-slate-400 max-w-md mx-auto">
            Ми вже опрацьовуємо ваше замовлення. Найближчим часом менеджер зв'яжеться з вами для уточнення деталей.
          </p>
        </div>

        <!-- Деталі замовлення та дані покупця -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left pt-6 border-t border-slate-800">

          <!-- Дані покупця та доставка -->
          <div class="rounded-2xl border border-slate-800 bg-slate-950 p-5 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Дані отримувача та доставка</h3>

            <div class="space-y-1 text-sm">
              <p class="text-slate-300"><span class="text-slate-500">Отримувач:</span> {{ order.first_name }} {{ order.last_name }}</p>
              <p class="text-slate-300"><span class="text-slate-500">Телефон:</span> {{ order.phone }}</p>
              <p v-if="order.email" class="text-slate-300"><span class="text-slate-500">Email:</span> {{ order.email }}</p>

              <div class="pt-2 border-t border-slate-800/80 mt-2">
                <p class="text-slate-300">
                  <span class="text-slate-500">Спосіб доставки:</span>
                  <span class="font-medium text-white">{{ order.shipping_type === 'pickup' ? 'Самовивіз із магазину' : 'Нова Пошта' }}</span>
                </p>
                <p v-if="order.shipping_type === 'nova_poshta'" class="text-slate-300 mt-1 text-xs">
                  <span class="text-slate-500 block">Місто:</span> {{ order.city_name }}
                </p>
                <p v-if="order.shipping_type === 'nova_poshta'" class="text-slate-300 mt-1 text-xs">
                  <span class="text-slate-500 block">Відділення:</span> {{ order.warehouse_address }}
                </p>
                <p v-else class="text-slate-300 mt-1 text-xs text-slate-400">
                  Адреса: м. Київ, вул. Хрещатик, 1
                </p>
              </div>
            </div>
          </div>

          <!-- Склад замовлення -->
          <div class="rounded-2xl border border-slate-800 bg-slate-950 p-5 space-y-3 flex flex-col justify-between">
            <div>
              <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-3">Замовляні товари</h3>

              <div class="divide-y divide-slate-800 max-h-48 overflow-y-auto pr-1">
                <div v-for="item in order.items" :key="item.id" class="py-2.5 flex items-center justify-between gap-3 text-xs">
                  <div>
                    <p class="font-medium text-white line-clamp-1">{{ item.product_name }}</p>
                    <p class="text-slate-400">Розмір: <span class="text-amber-400 font-mono">{{ item.size_value }}</span> | Кільк: {{ item.quantity }}</p>
                  </div>
                  <span class="font-bold text-white whitespace-nowrap">{{ item.price * item.quantity }} грн</span>
                </div>
              </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex justify-between items-center text-sm font-bold">
              <span class="text-slate-400">Загальна сума:</span>
              <span class="text-amber-400 text-lg">{{ order.total_price }} грн</span>
            </div>
          </div>

        </div>

        <!-- Кнопка повернення на головну -->
        <div class="pt-4">
          <Link
            href="/"
            class="inline-flex items-center justify-center px-8 py-3.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold rounded-xl transition shadow-lg shadow-amber-500/20 text-sm tracking-wide"
          >
            Повернутися до магазину
          </Link>
        </div>

      </div>

    </div>
  </MainLayout>
</template>

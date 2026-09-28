<script setup>
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  order: Object,
});

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'new': return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
    case 'processing': return 'bg-blue-500/10 text-blue-400 border-blue-500/20';
    case 'shipped': return 'bg-purple-500/10 text-purple-400 border-purple-500/20';
    case 'completed': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
    case 'cancelled': return 'bg-red-500/10 text-red-400 border-red-500/20';
    default: return 'bg-slate-800 text-slate-400 border-slate-700';
  }
};

const getStatusText = (status) => {
  switch (status) {
    case 'new': return 'Нове (в обробці)';
    case 'processing': return 'Готується до відправки';
    case 'shipped': return 'Відправлено';
    case 'completed': return 'Виконано';
    case 'cancelled': return 'Скасовано';
    default: return status;
  }
};
</script>

<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
      <!-- Кнопка назад -->
      <div class="mb-6">
        <Link
          :href="route('orders.index')"
          class="inline-flex items-center text-xs font-semibold text-slate-400 hover:text-amber-400 transition gap-1"
        >
          ← Назад до списку замовлень
        </Link>
      </div>

      <!-- Детальний чек замовлення -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl space-y-8">

        <!-- Заголовок -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-6">
          <div>
            <h1 class="text-2xl font-black text-white">Замовлення #{{ order.id }}</h1>
            <p class="text-xs text-slate-400 mt-1">Оформлено {{ new Date(order.created_at).toLocaleString('uk-UA') }}</p>
          </div>

          <span :class="['px-3 py-1.5 text-xs font-bold rounded-xl border', getStatusBadgeClass(order.status)]">
            {{ getStatusText(order.status) }}
          </span>
        </div>

        <!-- Сетка інфо -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Дані покупця -->
          <div class="bg-slate-950/50 border border-slate-800/80 rounded-xl p-4 space-y-2">
            <h3 class="text-xs font-bold text-amber-500 uppercase tracking-wider">Отримувач</h3>
            <p class="text-sm font-semibold text-white">{{ order.first_name }} {{ order.last_name }}</p>
            <p class="text-xs text-slate-400">📞 {{ order.phone }}</p>
            <p v-if="order.email" class="text-xs text-slate-400">✉️ {{ order.email }}</p>
          </div>

          <!-- Дані доставки -->
          <div class="bg-slate-950/50 border border-slate-800/80 rounded-xl p-4 space-y-2">
            <h3 class="text-xs font-bold text-amber-500 uppercase tracking-wider">Доставка</h3>
            <p class="text-sm font-semibold text-white">
              {{ order.shipping_type === 'pickup' ? 'Самовивіз з магазину' : 'Нова Пошта' }}
            </p>
            <p v-if="order.city_name" class="text-xs text-slate-300">Місто: {{ order.city_name }}</p>
            <p v-if="order.warehouse_address" class="text-xs text-slate-400">Відділення: {{ order.warehouse_address }}</p>
          </div>
        </div>

        <!-- Таблиця товарів -->
        <div>
          <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-4">Склад замовлення</h3>

          <div class="divide-y divide-slate-800 border-t border-b border-slate-800">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="py-4 flex items-center justify-between gap-4"
            >
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-slate-800 rounded-xl border border-slate-700 overflow-hidden flex-shrink-0">
                  <img
                    :src="item.variant?.product?.primary_image_url || item.variant?.product?.images?.[0]?.path || '/images/placeholder.jpg'"
                    :alt="item.product_name"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div>
                  <h4 class="text-sm font-semibold text-white">{{ item.product_name }}</h4>
                  <p class="text-xs text-slate-400">
                    Розмір: <span class="text-amber-400 font-bold">{{ item.size_value || item.variant?.size?.name }}</span>
                    <span class="mx-2">•</span>
                    {{ item.quantity }} шт. x {{ item.price }} грн
                  </p>
                </div>
              </div>

              <div class="text-right font-bold text-white text-sm">
                {{ item.price * item.quantity }} грн
              </div>
            </div>
          </div>
        </div>

        <!-- Підсумок -->
        <div class="flex justify-between items-center pt-2">
          <span class="text-slate-400 text-sm font-medium">Загальна вартість:</span>
          <span class="text-2xl font-black text-amber-400">{{ order.total_price }} грн</span>
        </div>

      </div>
    </div>
  </MainLayout>
</template>

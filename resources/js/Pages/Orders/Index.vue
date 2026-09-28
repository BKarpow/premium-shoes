<script setup>
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  orders: Object,
});

// Відстеження розгорнутих замовлень для швидкого перегляду товарів
const expandedOrders = ref([]);

const toggleExpand = (orderId) => {
  if (expandedOrders.value.includes(orderId)) {
    expandedOrders.value = expandedOrders.value.filter(id => id !== orderId);
  } else {
    expandedOrders.value.push(orderId);
  }
};

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
    case 'new': return 'Нове';
    case 'processing': return 'В обробці';
    case 'shipped': return 'Відправлено';
    case 'completed': return 'Виконано';
    case 'cancelled': return 'Скасовано';
    default: return status;
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('uk-UA', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>

<template>
  <MainLayout>
    <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
      <!-- Заголовок -->
      <div class="mb-8 border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white flex items-center gap-2">
          🛍️ Мої замовлення
        </h1>
        <p class="text-sm text-slate-400 mt-1">
          Історія ваших покупок та відстеження статусу доставки
        </p>
      </div>

      <!-- Список замовлень -->
      <div v-if="orders.data.length > 0" class="space-y-4">
        <div
          v-for="order in orders.data"
          :key="order.id"
          class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl transition hover:border-slate-700"
        >
          <!-- Шапка картки замовлення -->
          <div class="p-5 flex flex-wrap items-center justify-between gap-4 border-b border-slate-800/60 bg-slate-950/40">
            <div class="flex items-center gap-4">
              <div>
                <span class="text-xs text-slate-400 block">Замовлення</span>
                <span class="font-mono font-bold text-amber-400 text-lg">#{{ order.id }}</span>
              </div>

              <div class="h-8 w-px bg-slate-800 hidden sm:block"></div>

              <div>
                <span class="text-xs text-slate-400 block">Дата</span>
                <span class="text-xs text-slate-200 font-medium">{{ formatDate(order.created_at) }}</span>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <!-- Статус -->
              <span :class="['px-3 py-1 text-xs font-semibold rounded-lg border', getStatusBadgeClass(order.status)]">
                {{ getStatusText(order.status) }}
              </span>

              <!-- Сума -->
              <div class="text-right">
                <span class="text-xs text-slate-400 block">Сума</span>
                <span class="text-base font-bold text-white">{{ order.total_price }} грн</span>
              </div>
            </div>
          </div>

          <!-- Основна інформація та коротка прев'ю товарів -->
          <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Інформація про доставку -->
            <div class="text-xs text-slate-300 space-y-1">
              <p class="font-semibold text-slate-200">
                📦 {{ order.shipping_type === 'pickup' ? 'Самовивіз' : 'Нова Пошта' }}
              </p>
              <p v-if="order.city_name || order.warehouse_address" class="text-slate-400">
                {{ order.city_name }} {{ order.warehouse_address ? '— ' + order.warehouse_address : '' }}
              </p>
              <p class="text-slate-400">Отримувач: {{ order.first_name }} {{ order.last_name }} ({{ order.phone }})</p>
            </div>

            <!-- Кнопки дій -->
            <div class="flex items-center gap-2 self-end md:self-center">
              <button
                @click="toggleExpand(order.id)"
                class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-xl transition flex items-center gap-1"
              >
                <span>{{ expandedOrders.includes(order.id) ? 'Сховати товари' : 'Показати товари' }}</span>
                <svg :class="['w-4 h-4 transition-transform', expandedOrders.includes(order.id) ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <Link
                :href="route('orders.show', order.id)"
                class="px-4 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 text-xs font-bold rounded-xl transition"
              >
                Детальніше
              </Link>
            </div>
          </div>

          <!-- Розгортальний список товарів в замовленні -->
          <div v-if="expandedOrders.includes(order.id)" class="border-t border-slate-800/80 bg-slate-950/60 p-5 space-y-3">
            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Товари у замовленні:</h4>

            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center gap-4 py-2 border-b border-slate-800/40 last:border-0"
            >
              <!-- Зображення товару -->
              <div class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden flex-shrink-0 border border-slate-700">
                <img
                  :src="item.variant?.product?.primary_image_url || item.variant?.product?.images?.[0]?.path || '/images/placeholder.jpg'"
                  :alt="item.product_name"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Деталі товару -->
              <div class="flex-grow min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ item.product_name }}</p>
                <p class="text-xs text-slate-400">
                  Розмір: <span class="text-amber-400 font-semibold">{{ item.size_value || item.variant?.size?.name }}</span>
                  <span class="mx-2">•</span>
                  Кількість: <span class="text-slate-200">{{ item.quantity }} шт.</span>
                </p>
              </div>

              <!-- Ціна за позицію -->
              <div class="text-right">
                <p class="text-sm font-bold text-white">{{ item.price * item.quantity }} грн</p>
                <p class="text-[11px] text-slate-500" v-if="item.quantity > 1">{{ item.price }} грн / шт.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Пагінація -->
        <div v-if="orders.links.length > 3" class="mt-6 flex justify-center gap-1">
          <component
            v-for="(link, index) in orders.links"
            :key="index"
            :is="link.url ? Link : 'span'"
            :href="link.url"
            v-html="link.label"
            :class="[
              'px-3.5 py-1.5 text-xs rounded-xl border transition',
              link.active
                ? 'bg-amber-500 text-slate-950 border-amber-500 font-bold'
                : link.url
                  ? 'border-slate-800 text-slate-300 hover:bg-slate-800'
                  : 'border-slate-900 text-slate-600 cursor-not-allowed'
            ]"
          />
        </div>
      </div>

      <!-- Порожній стан (якщо замовлень ще немає) -->
      <div v-else class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center shadow-xl">
        <div class="w-16 h-16 bg-slate-800/80 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
          📦
        </div>
        <h3 class="text-lg font-bold text-white mb-2">У вас ще немає замовлень</h3>
        <p class="text-sm text-slate-400 mb-6 max-w-md mx-auto">
          Ви ще нічого не замовляли в нашому магазині. Оберіть стильне взуття з нашого каталогу!
        </p>
        <Link
          href="/"
          class="inline-flex items-center px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm rounded-xl transition shadow-lg shadow-amber-500/10"
        >
          Перейти до шопінгу
        </Link>
      </div>
    </div>
  </MainLayout>
</template>

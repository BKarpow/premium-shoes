<script setup>
import { Link, router, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue'; // Або твій layout адмінки
import { ref, watch } from 'vue';

const props = defineProps({
  orders: Object,
  filters: Object,
});

const selectedStatus = ref(props.filters.status || '');

watch(selectedStatus, (newStatus) => {
  router.get(
    route('admin.orders.index'),
    { status: newStatus },
    { preserveState: true, replace: true }
  );
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
    case 'new': return 'Нове';
    case 'processing': return 'В обробці';
    case 'shipped': return 'Відправлено';
    case 'completed': return 'Виконано';
    case 'cancelled': return 'Скасовано';
    default: return status;
  }
};
</script>

<template>
<Head title="Замовлення" />
  <AdminLayout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

      <!-- Заголовок і фільтри -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-black tracking-tight text-white">Керування замовленнями</h1>
          <p class="text-xs text-slate-400 mt-1">Переглядайте та оновлюйте статуси замовлень покупців</p>
        </div>

        <!-- Фільтр по статусу -->
        <div class="flex items-center gap-2">
          <select
            v-model="selectedStatus"
            class="rounded-xl border border-slate-800 bg-slate-900 px-4 py-2 text-sm text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
          >
            <option value="">Усі статуси</option>
            <option value="new">Нові</option>
            <option value="processing">В обробці</option>
            <option value="shipped">Відправлені</option>
            <option value="completed">Виконані</option>
            <option value="cancelled">Скасовані</option>
          </select>
        </div>
      </div>

      <!-- Таблиця замовлень -->
      <div class="rounded-2xl border border-slate-800 bg-slate-900/90 overflow-hidden shadow-xl backdrop-blur-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-800 bg-slate-950/50 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <th class="p-4">ID</th>
                <th class="p-4">Клієнт</th>
                <th class="p-4">Телефон</th>
                <th class="p-4">Доставка</th>
                <th class="p-4">Сума</th>
                <th class="p-4">Статус</th>
                <th class="p-4">Дата</th>
                <th class="p-4 text-right">Дії</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80 text-sm">
              <tr v-for="order in orders.data" :key="order.id" class="hover:bg-slate-800/40 transition-colors">
                <td class="p-4 font-mono font-bold text-amber-400">#{{ order.id }}</td>
                <td class="p-4 font-medium text-white">{{ order.first_name }} {{ order.last_name }}</td>
                <td class="p-4 text-slate-300">{{ order.phone }}</td>
                <td class="p-4 text-slate-300">
                  <span class="text-xs px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800">
                    {{ order.shipping_type === 'pickup' ? 'Самовивіз' : 'Нова Пошта' }}
                  </span>
                </td>
                <td class="p-4 font-bold text-white">{{ order.total_price }} грн</td>
                <td class="p-4">
                  <span :class="['inline-block px-2.5 py-1 text-xs font-semibold rounded-lg border', getStatusBadgeClass(order.status)]">
                    {{ getStatusText(order.status) }}
                  </span>
                </td>
                <td class="p-4 text-xs text-slate-400">{{ new Date(order.created_at).toLocaleString() }}</td>
                <td class="p-4 text-right">
                  <Link
                    :href="route('admin.orders.show', order.id)"
                    class="inline-flex items-center px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 text-xs font-semibold rounded-lg border border-amber-500/20 transition"
                  >
                    Деталі
                  </Link>
                </td>
              </tr>
              <tr v-if="orders.data.length === 0">
                <td colspan="8" class="p-8 text-center text-slate-500 text-sm">
                  Замовлень поки немає.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Пагінація -->
        <div v-if="orders.links.length > 3" class="p-4 border-t border-slate-800 flex justify-center gap-1">
          <component
            v-for="(link, index) in orders.links"
            :key="index"
            :is="link.url ? Link : 'span'"
            :href="link.url"
            v-html="link.label"
            :class="[
              'px-3 py-1.5 text-xs rounded-lg border transition',
              link.active
                ? 'bg-amber-500 text-slate-950 border-amber-500 font-bold'
                : link.url
                  ? 'border-slate-800 text-slate-300 hover:bg-slate-800'
                  : 'border-slate-900 text-slate-600 cursor-not-allowed'
            ]"
          />
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  order: Object,
});

const form = useForm({
  status: props.order.status,
  payment_status: props.order.payment_status,
});

const updateOrder = () => {
  form.patch(route('admin.orders.update', props.order.id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head :title="`Замовлення №${order.id}`" />

  <MainLayout>
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">

      <!-- Верхня панель навігації -->
      <div class="flex items-center justify-between">
        <div>
          <Link :href="route('admin.orders.index')" class="text-xs font-medium text-amber-400 hover:underline mb-1 inline-block">
            &larr; Назад до списку замовлень
          </Link>
          <h1 class="text-2xl font-black tracking-tight text-white">Замовлення №{{ order.id }}</h1>
        </div>

        <!-- Форма зміни статусів -->
        <form @submit.prevent="updateOrder" class="flex items-center gap-3 bg-slate-900 p-3 rounded-2xl border border-slate-800">
          <div>
            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Статус замовлення</label>
            <select v-model="form.status" class="rounded-xl border border-slate-800 bg-slate-950 px-3 py-1.5 text-xs text-white">
              <option value="new">Нове</option>
              <option value="processing">В обробці</option>
              <option value="shipped">Відправлено</option>
              <option value="completed">Виконано</option>
              <option value="cancelled">Скасовано</option>
            </select>
          </div>

          <div>
            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Оплата</label>
            <select v-model="form.payment_status" class="rounded-xl border border-slate-800 bg-slate-950 px-3 py-1.5 text-xs text-white">
              <option value="pending">Очікується</option>
              <option value="paid">Оплачено</option>
            </select>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="mt-4 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-extrabold rounded-xl transition shadow-lg shadow-amber-500/10 cursor-pointer"
          >
            Зберегти
          </button>
        </form>
      </div>

      <!-- Основна інформація -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Дані покупця та доставка -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 space-y-4 shadow-xl">
          <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Інформація про клієнта та доставку</h3>

          <div class="space-y-2 text-sm">
            <p class="text-slate-300"><span class="text-slate-500">ПІБ:</span> <strong class="text-white">{{ order.first_name }} {{ order.last_name }}</strong></p>
            <p class="text-slate-300"><span class="text-slate-500">Телефон:</span> <strong class="text-white">{{ order.phone }}</strong></p>
            <p class="text-slate-300"><span class="text-slate-500">Email:</span> {{ order.email || 'Не вказано' }}</p>

            <div class="pt-3 border-t border-slate-800 space-y-1">
              <p class="text-slate-300"><span class="text-slate-500">Тип доставки:</span> <strong class="text-white">{{ order.shipping_type === 'pickup' ? 'Самовивіз із магазину' : 'Нова Пошта' }}</strong></p>
              <p v-if="order.shipping_type === 'nova_poshta'" class="text-xs text-slate-300"><span class="text-slate-500">Місто:</span> {{ order.city_name }}</p>
              <p v-if="order.shipping_type === 'nova_poshta'" class="text-xs text-slate-300"><span class="text-slate-500">Відділення:</span> {{ order.warehouse_address }}</p>
              <p v-else class="text-xs text-slate-400">Адреса: м. Київ, вул. Хрещатик, 1</p>
            </div>
          </div>
        </div>

        <!-- Товари в замовленні -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 space-y-4 shadow-xl flex flex-col justify-between">
          <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-3">Товари ({{ order.items.length }})</h3>

            <div class="divide-y divide-slate-800 max-h-60 overflow-y-auto pr-1">
              <div v-for="item in order.items" :key="item.id" class="py-3 flex items-center justify-between gap-3 text-xs">
                <div>
                  <p class="font-medium text-white">{{ item.product_name }}</p>
                  <p class="text-slate-400">Розмір: <span class="text-amber-400 font-mono">{{ item.size_value }}</span> | Кількість: {{ item.quantity }} шт.</p>
                </div>
                <span class="font-bold text-white whitespace-nowrap">{{ item.price * item.quantity }} грн</span>
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-slate-800 flex justify-between items-center text-sm font-bold">
            <span class="text-slate-400">Загальна сума:</span>
            <span class="text-amber-400 text-lg">{{ order.total_price }} грн</span>
          </div>
        </div>

      </div>

    </div>
  </MainLayout>
</template>

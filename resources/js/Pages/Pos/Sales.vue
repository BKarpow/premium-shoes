<script setup>
import { Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

defineOptions({ layout: MainLayout })

defineProps({
  sales: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="min-h-screen bg-neutral-950 text-neutral-100 font-sans antialiased p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">

      <!-- Шапка -->
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 mb-8 border-b border-neutral-800 gap-4">
        <div>
          <span class="text-xs font-semibold uppercase tracking-widest text-amber-500 block mb-1">Термінал продавця</span>
          <h1 class="text-2xl sm:text-3xl font-bold text-white">Історія швидких продажів (POS)</h1>
        </div>
        <div>
          <Link
            href="/pos"
            class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-neutral-950 rounded-xl text-xs font-extrabold uppercase tracking-wider transition-colors shadow-lg shadow-amber-500/20"
          >
            ← Повернутися до термінала
          </Link>
        </div>
      </header>

      <!-- Таблиця продажів -->
      <div class="bg-neutral-900 border border-neutral-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-neutral-800 bg-neutral-950/60 text-[11px] font-bold uppercase tracking-wider text-neutral-400">
                <th class="p-4">ID</th>
                <th class="p-4">Товар / Розмір</th>
                <th class="p-4">Кількість</th>
                <th class="p-4">Сума</th>
                <th class="p-4">Оплата</th>
                <th class="p-4">Клієнт</th>
                <th class="p-4">Продавець</th>
                <th class="p-4">Дата / Час</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-800/80 text-xs">
              <tr v-for="sale in sales.data" :key="sale.id" class="hover:bg-neutral-800/40 transition-colors">
                <td class="p-4 font-mono font-bold text-amber-400">#{{ sale.id }}</td>
                <td class="p-4">
                  <div class="font-bold text-white line-clamp-1">{{ sale.product?.title }}</div>
                  <div class="text-[10px] text-neutral-400 mt-0.5">Розмір: <span class="text-amber-300 font-semibold">{{ sale.variant?.size?.value }}</span></div>
                </td>
                <td class="p-4 font-medium text-neutral-300">{{ sale.quantity }} шт.</td>
                <td class="p-4 font-extrabold text-emerald-400">{{ sale.price }} ₴</td>
                <td class="p-4">
                  <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border"
                        :class="sale.payment_method === 'cash' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-500/30' : 'bg-blue-950/60 text-blue-400 border-blue-500/30'">
                    {{ sale.payment_method === 'cash' ? 'Готівка' : 'Картка' }}
                  </span>
                </td>
                <td class="p-4 text-neutral-300">
                  <div v-if="sale.customer_name" class="font-medium text-white">{{ sale.customer_name }}</div>
                  <div class="text-[10px] text-neutral-400">{{ sale.customer_phone || 'Без телефону' }}</div>
                </td>
                <td class="p-4 text-neutral-400 font-medium">{{ sale.seller?.name ?? 'Система' }}</td>
                <td class="p-4 text-neutral-400 text-[11px]">{{ new Date(sale.created_at).toLocaleString() }}</td>
              </tr>
              <tr v-if="sales.data.length === 0">
                <td colspan="8" class="p-12 text-center text-neutral-500 text-sm">
                  Ще не зафіксовано жодного продажу через термінал.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Пагінація -->
        <div v-if="sales.links && sales.links.length > 3" class="p-4 border-t border-neutral-800 flex justify-center gap-1">
          <component
            v-for="(link, index) in sales.links"
            :key="index"
            :is="link.url ? Link : 'span'"
            :href="link.url"
            v-html="link.label"
            :class="[
              'px-3 py-1.5 text-xs rounded-lg border transition',
              link.active
                ? 'bg-amber-500 text-neutral-950 border-amber-500 font-bold'
                : link.url
                  ? 'border-neutral-800 text-neutral-300 hover:bg-neutral-800'
                  : 'border-neutral-900 text-neutral-600 cursor-not-allowed'
            ]"
          />
        </div>
      </div>

    </div>
  </div>
</template>

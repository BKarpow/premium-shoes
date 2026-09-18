<script setup>
defineProps({
  cart: Object,
  form: Object,
});
</script>

<template>
  <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-xl backdrop-blur-sm space-y-4 sticky top-24">
    <h2 class="text-lg font-bold text-white">Ваше замовлення</h2>

    <!-- Список товарів -->
    <div class="divide-y divide-slate-800/80 max-h-64 overflow-y-auto pr-1">
      <div v-for="item in cart.items" :key="item.id" class="py-3 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <img :src="item.image" :alt="item.name" class="w-12 h-12 object-cover rounded-xl border border-slate-800 bg-slate-950" />
          <div>
            <h4 class="text-xs font-medium text-slate-200 line-clamp-1">{{ item.name }}</h4>
            <p class="text-[11px] text-slate-400">Розмір: <span class="text-amber-400 font-mono">{{ item.size }}</span> | Кількість: {{ item.quantity }}</p>
          </div>
        </div>
        <span class="text-xs font-bold text-white whitespace-nowrap">{{ item.subtotal }} грн</span>
      </div>
    </div>

    <!-- Розрахунок суми -->
    <div class="pt-4 border-t border-slate-800 space-y-2">
      <div class="flex justify-between text-sm text-slate-400">
        <span>Товари:</span>
        <span class="text-white">{{ cart.total }} грн</span>
      </div>
      <div class="flex justify-between text-sm text-slate-400">
        <span>Доставка:</span>
        <span class="text-xs text-slate-500">За тарифами перевізника</span>
      </div>
      <div class="flex justify-between text-base font-bold text-white pt-2 border-t border-slate-800">
        <span>До сплати:</span>
        <span class="text-amber-400 text-lg">{{ cart.total }} грн</span>
      </div>
    </div>

    <!-- Кнопка підтвердження -->
    <button
      type="submit"
      :disabled="form.processing"
      class="w-full py-3.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold rounded-xl transition shadow-lg shadow-amber-500/20 disabled:opacity-50 cursor-pointer text-center text-sm tracking-wide"
    >
      {{ form.processing ? 'Обробка...' : 'Підтвердити замовлення' }}
    </button>
  </div>
</template>

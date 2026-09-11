<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  product: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="group relative bg-neutral-900/40 rounded-2xl border border-neutral-800/80 overflow-hidden hover:border-neutral-700 transition-all duration-300 flex flex-col justify-between hover:shadow-2xl hover:shadow-neutral-950/80">
    <div>
      <!-- Зображення товару -->
      <div class="relative aspect-square bg-neutral-950 overflow-hidden">
        <img
          :src="product.images?.find(i => i.is_main)?.path || 'https://via.placeholder.com/600'"
          :alt="product.title"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
        />

        <span
          v-if="product.old_price"
          class="absolute top-3 left-3 bg-red-950/80 border border-red-500/30 text-red-400 text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded backdrop-blur-md"
        >
          Sale
        </span>

        <Link
          :href="`/product/${product.slug}`"
          class="absolute inset-0 bg-neutral-950/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center"
        >
          <span class="bg-amber-500 text-neutral-950 text-xs uppercase font-bold tracking-wider px-4 py-2 rounded-lg shadow-xl transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
            Переглянути
          </span>
        </Link>
      </div>

      <!-- Текстова інформація -->
      <div class="p-4">
        <div class="mb-2">
          <span class="text-[10px] uppercase tracking-widest text-neutral-500 font-semibold font-secondroad block mb-0.5">
            {{ product.brand?.name || 'Взуття' }}
          </span>
          <Link
            :href="`/product/${product.slug}`"
            class="text-lg font-handwritten text-neutral-100 hover:text-amber-400 transition-colors line-clamp-1"
          >
            {{ product.title }}
          </Link>
        </div>

        <!-- Розміри -->
        <div class="flex flex-wrap gap-1 mt-2">
          <span
            v-for="variant in product.variants"
            :key="variant.id"
            class="text-[9px] font-mono font-medium px-1.5 py-0.5 bg-neutral-950/80 text-neutral-400 border border-neutral-800/80 rounded"
          >
            {{ variant.size?.value }}
          </span>
        </div>
      </div>
    </div>

    <!-- Ціна -->
    <div class="p-4 pt-0 flex justify-between items-end border-t border-neutral-800/40 mt-3">
      <div class="flex items-baseline space-x-2 pt-3">
        <span class="text-base  font-extrabold text-white tracking-tight">
          {{ product.price }} ₴
        </span>
        <span v-if="product.old_price" class="text-xs text-neutral-500 line-through">
          {{ product.old_price }} ₴
        </span>
      </div>
    </div>
  </div>
</template>

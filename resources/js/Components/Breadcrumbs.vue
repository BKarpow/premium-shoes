<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  items: {
    type: Array,
    required: true,
    // Формат масиву: [ { label: 'Головна', url: '/' }, { label: 'Категорія', url: '...' }, { label: 'Назва товару' } ]
  }
})
</script>

<template>
  <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-neutral-400 overflow-hidden py-2">
    <template v-for="(item, index) in items" :key="index">
      <!-- Роздільник (стрілочка) -->
      <span v-if="index > 0" class="text-neutral-600 flex-shrink-0">/</span>

      <!-- Елемент з посиланням (якщо є url) -->
      <Link
        v-if="item.url"
        :href="item.url"
        class="hover:text-amber-500 transition-colors flex-shrink-0 truncate max-w-[120px] sm:max-w-[200px]"
      >
        {{ item.label }}
      </Link>

      <!-- Останній елемент (поточна сторінка, без посилання, з розумним скороченням) -->
      <span
        v-else
        class="text-neutral-200 font-medium truncate min-w-0"
        :title="item.label"
      >
        {{ item.label }}
      </span>
    </template>
  </nav>
</template>

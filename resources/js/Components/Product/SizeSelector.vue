<script setup>
defineProps({
  variants: {
    type: Array,
    default: () => []
  },
  modelValue: [Number, String]
})

defineEmits(['update:modelValue'])
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-3">
      <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Оберіть розмір (EU)</span>
      <button class="text-[11px] text-amber-500 hover:underline uppercase tracking-wider">Таблиця розмірів</button>
    </div>

    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
      <button
        v-for="variant in variants"
        :key="variant.id"
        :disabled="variant.stock <= 0"
        @click="$emit('update:modelValue', variant.size_id)"
        :class="[
          'py-3 rounded-xl border text-xs font-bold transition-all relative overflow-hidden flex flex-col items-center justify-center',
          variant.stock <= 0
            ? 'bg-neutral-950/50 text-neutral-600 border-neutral-800/50 cursor-not-allowed line-through'
            : modelValue === variant.size_id
              ? 'bg-amber-500 text-neutral-950 border-amber-500 shadow-lg shadow-amber-500/20'
              : 'bg-neutral-900 text-neutral-200 border-neutral-800 hover:border-neutral-700 hover:text-white'
        ]"
      >
        <span>{{ variant.size?.value }}</span>
        <span
          v-if="variant.stock > 0 && variant.stock <= 3"
          :class="modelValue === variant.size_id ? 'text-neutral-950/80' : 'text-amber-500'"
          class="text-[9px] font-normal"
        >
          Залишилось {{ variant.stock }}
        </span>
      </button>
    </div>
  </div>
</template>

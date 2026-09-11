<script setup>
defineProps({
  modelValue: {
    type: String,
    default: 'latest'
  }
})

defineEmits(['update:modelValue'])
</script>

<template>
  <select
    :value="modelValue"
    @change="$emit('update:modelValue', $event.target.value)"
    class="w-full bg-transparent border-none text-xs text-neutral-200 focus:ring-0 cursor-pointer font-medium p-1"
  >
    <option value="latest" class="bg-neutral-900 text-neutral-200">Новинки</option>
    <option value="price_asc" class="bg-neutral-900 text-neutral-200">Від дешевших</option>
    <option value="price_desc" class="bg-neutral-900 text-neutral-200">Від дорожчих</option>
  </select>
</template>

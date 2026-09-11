<script setup>
defineProps({
  categories: Array,
  brands: Array,
  sizes: Array,
  selectedCategory: String,
  selectedBrands: Array,
  selectedSizes: Array,
  priceFrom: [String, Number],
  priceTo: [String, Number],
})

const emit = defineEmits([
  'update:selectedCategory',
  'update:selectedBrands',
  'update:selectedSizes',
  'update:priceFrom',
  'update:priceTo',
  'apply',
  'reset'
])

const toggleSize = (sizeId, currentSizes) => {
  const newSizes = [...currentSizes]
  const idx = newSizes.indexOf(sizeId)
  if (idx > -1) newSizes.splice(idx, 1)
  else newSizes.push(sizeId)
  emit('update:selectedSizes', newSizes)
  emit('apply')
}
</script>

<template>
  <aside class="hidden lg:block bg-neutral-900/60 backdrop-blur-md p-6 rounded-2xl border border-neutral-800/80 h-fit space-y-7 shadow-2xl">
    <div class="flex justify-between items-center pb-4 border-b border-neutral-800">
      <h2 class="font-bold text-sm uppercase tracking-wider text-neutral-200">Фільтри</h2>
      <button
        @click="$emit('reset')"
        class="text-[11px] uppercase tracking-wider text-neutral-400 hover:text-amber-500 transition-colors"
      >
        Скинути
      </button>
    </div>

    <!-- Категорії -->
    <div>
      <h3 class="font-semibold text-xs uppercase tracking-wider text-neutral-400 mb-3">Категорія</h3>
      <select
        :value="selectedCategory"
        @change="$emit('update:selectedCategory', $event.target.value)"
        class="w-full bg-neutral-950 border border-neutral-800 rounded-lg text-xs text-neutral-200 focus:border-amber-500 focus:ring-amber-500 py-2.5 px-3"
      >
        <option value="">Усі категорії</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
          {{ cat.name }}
        </option>
      </select>
    </div>

    <!-- Бренди -->
    <div>
      <h3 class="font-semibold text-xs uppercase tracking-wider text-neutral-400 mb-3">Бренд</h3>
      <div class="space-y-2.5 max-h-44 overflow-y-auto pr-2 custom-scrollbar">
        <label
          v-for="brand in brands"
          :key="brand.id"
          class="flex items-center text-xs text-neutral-300 hover:text-white cursor-pointer group"
        >
          <input
            type="checkbox"
            :value="brand.slug"
            :checked="selectedBrands.includes(brand.slug)"
            @change="(e) => {
              const updated = e.target.checked
                ? [...selectedBrands, brand.slug]
                : selectedBrands.filter(b => b !== brand.slug);
              $emit('update:selectedBrands', updated);
              $emit('apply');
            }"
            class="rounded border-neutral-700 bg-neutral-950 text-amber-500 focus:ring-amber-500 focus:ring-offset-neutral-900 mr-2.5"
          />
          <span class="group-hover:translate-x-0.5 transition-transform duration-150">{{ brand.name }}</span>
        </label>
      </div>
    </div>

    <!-- Розміри -->
    <div>
      <h3 class="font-semibold text-xs uppercase tracking-wider text-neutral-400 mb-3">Розмір (EU)</h3>
      <div class="grid grid-cols-4 gap-1.5">
        <button
          v-for="size in sizes"
          :key="size.id"
          @click="toggleSize(size.id, selectedSizes)"
          :class="[
            'py-1.5 text-xs font-medium rounded-md border transition-all duration-200',
            selectedSizes.includes(size.id)
              ? 'bg-amber-500 text-neutral-950 border-amber-500 font-bold shadow-lg shadow-amber-500/20'
              : 'bg-neutral-950 text-neutral-400 border-neutral-800 hover:border-neutral-700 hover:text-white'
          ]"
        >
          {{ size.value }}
        </button>
      </div>
    </div>

    <!-- Ціна -->
    <div>
      <h3 class="font-semibold text-xs uppercase tracking-wider text-neutral-400 mb-3">Ціна (грн)</h3>
      <div class="flex items-center space-x-2">
        <input
          type="number"
          placeholder="Від"
          :value="priceFrom"
          @change="(e) => { $emit('update:priceFrom', e.target.value); $emit('apply'); }"
          class="w-full bg-neutral-950 border border-neutral-800 rounded-lg text-xs text-neutral-200 px-3 py-2 focus:border-amber-500 focus:ring-amber-500"
        />
        <span class="text-neutral-600">-</span>
        <input
          type="number"
          placeholder="До"
          :value="priceTo"
          @change="(e) => { $emit('update:priceTo', e.target.value); $emit('apply'); }"
          class="w-full bg-neutral-950 border border-neutral-800 rounded-lg text-xs text-neutral-200 px-3 py-2 focus:border-amber-500 focus:ring-amber-500"
        />
      </div>
    </div>
  </aside>
</template>

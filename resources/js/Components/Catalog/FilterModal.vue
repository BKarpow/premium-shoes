<script setup>
defineProps({
  isOpen: Boolean,
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
  'close',
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
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 lg:hidden flex justify-end">
    <!-- Бекдроп -->
    <div
      @click="$emit('close')"
      class="fixed inset-0 bg-neutral-950/80 backdrop-blur-sm transition-opacity"
    ></div>

    <!-- Модалка -->
    <div class="relative w-full max-w-xs bg-neutral-900 border-l border-neutral-800 h-full flex flex-col z-10 shadow-2xl">

      <!-- Шапка -->
      <div class="p-5 border-b border-neutral-800 flex items-center justify-between">
        <h2 class="font-bold text-sm uppercase tracking-wider text-neutral-200">Фільтри</h2>
        <button @click="$emit('close')" class="text-neutral-400 hover:text-white p-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Тіло -->
      <div class="p-5 overflow-y-auto space-y-6 flex-1 custom-scrollbar">
        <!-- Категорії -->
        <div>
          <h3 class="font-semibold text-xs uppercase tracking-wider text-neutral-400 mb-3">Категорія</h3>
          <select
            :value="selectedCategory"
            @change="$emit('update:selectedCategory', $event.target.value)"
            class="w-full bg-neutral-950 border border-neutral-800 rounded-lg text-xs text-neutral-200 focus:border-amber-500 py-2.5 px-3"
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
          <div class="space-y-2.5 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
            <label
              v-for="brand in brands"
              :key="brand.id"
              class="flex items-center text-xs text-neutral-300 hover:text-white cursor-pointer"
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
                }"
                class="rounded border-neutral-700 bg-neutral-950 text-amber-500 focus:ring-amber-500 mr-2.5"
              />
              <span>{{ brand.name }}</span>
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
                'py-2 text-xs font-medium rounded-md border transition-all duration-200',
                selectedSizes.includes(size.id)
                  ? 'bg-amber-500 text-neutral-950 border-amber-500 font-bold'
                  : 'bg-neutral-950 text-neutral-400 border-neutral-800'
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
              @input="$emit('update:priceFrom', $event.target.value)"
              class="w-full bg-neutral-950 border border-neutral-800 rounded-lg text-xs text-neutral-200 px-3 py-2"
            />
            <span class="text-neutral-600">-</span>
            <input
              type="number"
              placeholder="До"
              :value="priceTo"
              @input="$emit('update:priceTo', $event.target.value)"
              class="w-full bg-neutral-950 border border-neutral-800 rounded-lg text-xs text-neutral-200 px-3 py-2"
            />
          </div>
        </div>
      </div>

      <!-- Підвал -->
      <div class="p-4 border-t border-neutral-800 bg-neutral-950 space-y-2">
        <button
          @click="$emit('apply')"
          class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-neutral-950 font-bold text-xs uppercase tracking-wider rounded-xl transition-colors shadow-lg shadow-amber-500/20"
        >
          Застосувати фільтри
        </button>
        <button
          @click="$emit('reset')"
          class="w-full py-2 text-neutral-400 hover:text-white text-xs uppercase tracking-wider transition-colors"
        >
          Скинути все
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import ProductCard from '@/Components/Catalog/ProductCard.vue'
import FilterSidebar from '@/Components/Catalog/FilterSidebar.vue'
import FilterModal from '@/Components/Catalog/FilterModal.vue'
import SortSelect from '@/Components/Catalog/SortSelect.vue'

const props = defineProps({
  products: Object,
  categories: Array,
  brands: Array,
  sizes: Array,
  filters: Object,
})

const isMobileFilterOpen = ref(false)

// Стан фільтрів
const selectedCategory = ref(props.filters.category || '')
const selectedBrands = ref(
  props.filters.brands
    ? Array.isArray(props.filters.brands)
      ? props.filters.brands
      : props.filters.brands.split(',')
    : []
)
const selectedSizes = ref(
  props.filters.sizes
    ? Array.isArray(props.filters.sizes)
      ? props.filters.sizes
      : props.filters.sizes.split(',').map(Number)
    : []
)
const priceFrom = ref(props.filters.price_from || '')
const priceTo = ref(props.filters.price_to || '')
const sortBy = ref(props.filters.sort || 'latest')

const applyFilters = () => {
  router.get(
    '/',
    {
      category: selectedCategory.value || undefined,
      brands: selectedBrands.value.length ? selectedBrands.value.join(',') : undefined,
      sizes: selectedSizes.value.length ? selectedSizes.value.join(',') : undefined,
      price_from: priceFrom.value || undefined,
      price_to: priceTo.value || undefined,
      sort: sortBy.value !== 'latest' ? sortBy.value : undefined,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  )
}

const applyMobileFilters = () => {
  applyFilters()
  isMobileFilterOpen.value = false
}

const resetFilters = () => {
  selectedCategory.value = ''
  selectedBrands.value = []
  selectedSizes.value = []
  priceFrom.value = ''
  priceTo.value = ''
  sortBy.value = 'latest'
  applyFilters()
  isMobileFilterOpen.value = false
}

watch([selectedCategory, sortBy], () => {
  applyFilters()
})
</script>

<template>
  <div class="min-h-screen bg-neutral-950 text-neutral-100 font-sans antialiased py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

      <!-- Заголовок -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-6 border-b border-neutral-800">
        <div>
          <h1 class="text-3xl sm:text-4xl font-handwritten tracking-wide text-white">
            Каталог <span class="text-amber-500">Взуття</span>
          </h1>
          <p class="text-[11px] text-neutral-400 mt-1 uppercase tracking-widest">
            Преміальна колекція взуття
          </p>
        </div>

        <!-- Сортування (Десктоп) -->
        <div class="hidden sm:flex items-center space-x-3 bg-neutral-900/80 px-4 py-2 rounded-lg border border-neutral-800">
          <label class="text-xs uppercase tracking-wider text-neutral-400 font-medium">Сортування:</label>
          <SortSelect v-model="sortBy" />
        </div>
      </div>

      <!-- Мобільна панель (Кнопки фільтрів та сортування) -->
      <div class="flex lg:hidden justify-between items-center mb-6 gap-3">
        <button
          @click="isMobileFilterOpen = true"
          class="flex-1 flex items-center justify-center space-x-2 bg-neutral-900 border border-neutral-800 hover:border-neutral-700 text-neutral-200 py-2.5 px-4 rounded-xl text-xs font-semibold tracking-wider uppercase transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
          </svg>
          <span>Фільтри</span>
          <span v-if="selectedBrands.length || selectedSizes.length || selectedCategory || priceFrom || priceTo" class="w-2 h-2 rounded-full bg-amber-500 ml-1"></span>
        </button>

        <div class="flex-1 bg-neutral-900 border border-neutral-800 rounded-xl px-3 py-1.5 flex items-center">
          <SortSelect v-model="sortBy" />
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Десктопні фільтри -->
        <FilterSidebar
          :categories="categories"
          :brands="brands"
          :sizes="sizes"
          v-model:selectedCategory="selectedCategory"
          v-model:selectedBrands="selectedBrands"
          v-model:selectedSizes="selectedSizes"
          v-model:priceFrom="priceFrom"
          v-model:priceTo="priceTo"
          @apply="applyFilters"
          @reset="resetFilters"
        />

        <!-- Сітка товарів -->
        <main class="lg:col-span-3">
          <div v-if="products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <ProductCard
              v-for="product in products.data"
              :key="product.id"
              :product="product"
            />
          </div>

          <div v-else class="text-center py-16 bg-neutral-900/30 rounded-2xl border border-neutral-800">
            <p class="text-neutral-400 text-sm">Товарів за вибраними критеріями не знайдено.</p>
          </div>

          <!-- Пагінація -->
          <div v-if="products.links.length > 3" class="mt-10 flex justify-center space-x-1.5">
            <Link
              v-for="(link, key) in products.links"
              :key="key"
              :href="link.url || '#'"
              v-html="link.label"
              :class="[
                'px-3.5 py-2 text-xs font-semibold rounded-lg border transition-all duration-200',
                link.active
                  ? 'bg-amber-500 text-neutral-950 border-amber-500 font-bold shadow-lg shadow-amber-500/10'
                  : 'bg-neutral-900 text-neutral-300 border-neutral-800 hover:border-neutral-700 hover:text-white',
                !link.url ? 'opacity-40 cursor-not-allowed' : ''
              ]"
            />
          </div>
        </main>
      </div>

      <!-- Мобільне модальне вікно -->
      <FilterModal
        :is-open="isMobileFilterOpen"
        :categories="categories"
        :brands="brands"
        :sizes="sizes"
        v-model:selectedCategory="selectedCategory"
        v-model:selectedBrands="selectedBrands"
        v-model:selectedSizes="selectedSizes"
        v-model:priceFrom="priceFrom"
        v-model:priceTo="priceTo"
        @close="isMobileFilterOpen = false"
        @apply="applyMobileFilters"
        @reset="resetFilters"
      />

    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #0a0a0a;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #262626;
  border-radius: 4px;
}
</style>

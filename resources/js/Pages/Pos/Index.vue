<script setup>
import { ref, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

defineOptions({ layout: MainLayout })

const props = defineProps({
  products: {
    type: Array,
    required: true
  },
  recentSales: {
    type: Array,
    default: () => []
  }
})

// Пошуковий рядок (фільтрація товарів на клієнті)
const searchQuery = ref('')
const selectedProduct = ref(null)
const selectedVariant = ref(null)

// Форма оформлення продажу через Inertia useForm
const form = useForm({
  product_id: null,
  product_variant_id: null,
  quantity: 1,
  customer_name: '',
  customer_phone: '',
  payment_method: 'cash',
})

// Фільтрація товарів за назвою або артикулом
const filteredProducts = computed(() => {
  if (!searchQuery.value) return props.products
  const q = searchQuery.value.toLowerCase()
  return props.products.filter(p =>
    p.title.toLowerCase().includes(q) ||
    (p.sku && p.sku.toLowerCase().includes(q))
  )
})

// Вибір товару
const selectProduct = (product) => {
  selectedProduct.value = product
  form.product_id = product.id
  selectedVariant.value = null
  form.product_variant_id = null
}

// Вибір розміру (варіації)
const selectVariant = (variant) => {
  if (variant.stock <= 0) return
  selectedVariant.value = variant
  form.product_variant_id = variant.id
}

// Відправка продажу
const submitSale = () => {
  if (!form.product_id || !form.product_variant_id) return

  form.post(route('pos.store'), {
    preserveScroll: true,
    onSuccess: () => {
      // Скидання вибору після успішного продажу
      selectedProduct.value = null
      selectedVariant.value = null
      form.reset('product_id', 'product_variant_id', 'quantity', 'customer_name', 'customer_phone')
    },
  })
}
</script>

<template>
  <div class="min-h-screen bg-neutral-950 text-neutral-100 font-sans antialiased p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">

      <!-- Шапка сторінки продавця з посиланням на журнал продажів -->
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 mb-8 border-b border-neutral-800 gap-4">
        <div>
          <span class="text-xs font-semibold uppercase tracking-widest text-amber-500 block mb-1">Термінал продавця</span>
          <h1 class="text-2xl sm:text-3xl font-bold text-white">Швидкий продаж взуття</h1>
        </div>
        <div class="flex items-center space-x-3">
          <!-- Посилання на журнал продажів -->
          <Link
            :href="route('pos.sales')"
            class="px-4 py-2.5 bg-neutral-900 hover:bg-neutral-800 border border-neutral-700 rounded-xl text-xs font-bold uppercase tracking-wider text-amber-400 transition-colors shadow-md"
          >
            📋 Журнал продажів
          </Link>
          <a href="/" class="px-4 py-2.5 bg-neutral-950 hover:bg-neutral-800 border border-neutral-800 rounded-xl text-xs font-semibold uppercase tracking-wider text-neutral-300 transition-colors">
            На головну сайту
          </a>
        </div>
      </header>

      <!-- Сповіщення про успіх -->
      <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-950/80 border border-emerald-500/30 text-emerald-400 rounded-xl text-xs font-medium uppercase tracking-wider">
        {{ $page.props.flash.success }}
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Ліва колонка: Вибір товару та розміру (2 колонки) -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Пошук -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Пошук моделі за назвою або артикулом..."
              class="w-full bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-3 text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-amber-500 transition-colors"
            />
          </div>

          <!-- Список товарів (Сітка) -->
          <div>
            <h2 class="text-xs font-semibold uppercase tracking-widest text-neutral-400 mb-4">1. Оберіть модель взуття</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 max-h-[400px] overflow-y-auto pr-2">
              <div
                v-for="product in filteredProducts"
                :key="product.id"
                @click="selectProduct(product)"
                :class="[
                  'cursor-pointer bg-neutral-900/60 border rounded-xl p-3 transition-all flex flex-col justify-between',
                  selectedProduct?.id === product.id ? 'border-amber-500 bg-amber-500/10 shadow-lg shadow-amber-500/10' : 'border-neutral-800 hover:border-neutral-700'
                ]"
              >
                <div>
                  <div class="aspect-square bg-neutral-950 rounded-lg overflow-hidden mb-3 relative flex items-center justify-center">
                    <!-- Виправлено: додано атрибут :src для відображення фото товару -->
                    <img
                      v-if="product.images?.[0]"
                      :src="product.images[0].image_path || product.images[0].path"
                      :alt="product.title"
                      class="w-full h-full object-cover"
                    />
                    <span v-else class="text-neutral-600 text-xs">Немає фото</span>
                  </div>
                  <h3 class="text-xs font-medium text-white line-clamp-2 mb-1">{{ product.title }}</h3>
                </div>
                <div class="text-sm font-extrabold text-amber-400 mt-2">
                  {{ product.price }} ₴
                </div>
              </div>
            </div>
          </div>

          <!-- Вибір розміру (якщо товар обрано) -->
          <div v-if="selectedProduct" class="pt-6 border-t border-neutral-800">
            <h2 class="text-xs font-semibold uppercase tracking-widest text-neutral-400 mb-4">
              2. Оберіть розмір для: <span class="text-white font-bold">{{ selectedProduct.title }}</span>
            </h2>
            <div class="flex flex-wrap gap-3">
              <button
                v-for="variant in selectedProduct.variants"
                :key="variant.id"
                @click="selectVariant(variant)"
                :disabled="variant.stock <= 0"
                :class="[
                  'px-4 py-3 rounded-xl text-xs font-bold transition-all border',
                  variant.stock <= 0
                    ? 'opacity-40 bg-neutral-900 border-neutral-800 text-neutral-600 cursor-not-allowed'
                    : selectedVariant?.id === variant.id
                      ? 'bg-amber-500 text-neutral-950 border-amber-400 shadow-md shadow-amber-500/20'
                      : 'bg-neutral-900 text-neutral-300 border-neutral-800 hover:border-neutral-700'
                ]"
              >
                Розмір: {{ variant.size?.value }}
                <span class="block text-[10px] opacity-80 mt-0.5"> (зал: {{ variant.stock }})</span>
              </button>
            </div>
          </div>

        </div>

        <!-- Права колонка: Форма оформлення продажу та недавні операції -->
        <div class="space-y-6">

          <!-- Форма -->
          <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Деталі продажу</h2>

            <form @submit.prevent="submitSale" class="space-y-4">

              <!-- Вибраний товар інфо -->
              <div class="bg-neutral-950 p-3 rounded-xl border border-neutral-800 text-xs space-y-1">
                <p class="text-neutral-400">Товар:</p>
                <p class="font-bold text-white">{{ selectedProduct ? selectedProduct.title : 'Не обрано' }}</p>
                <p class="text-neutral-400 mt-2">Розмір:</p>
                <p class="font-bold text-amber-400">{{ selectedVariant ? `Розмір ${selectedVariant.size?.value}` : 'Не обрано' }}</p>
              </div>

              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Спосіб оплати</label>
                <select v-model="form.payment_method" class="w-full bg-neutral-950 border border-neutral-800 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                  <option value="cash">Готівка</option>
                  <option value="card">Термінал (Картка)</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Ім'я клієнта (необов'язково)</label>
                <input v-model="form.customer_name" type="text" placeholder="Іван" class="w-full bg-neutral-950 border border-neutral-800 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500" />
              </div>

              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Телефон клієнта</label>
                <input v-model="form.customer_phone" type="tel" placeholder="+380..." class="w-full bg-neutral-950 border border-neutral-800 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500" />
              </div>

              <div v-if="form.errors.product_variant_id" class="text-xs text-red-400">
                {{ form.errors.product_variant_id || form.errors.product_id }}
              </div>

              <button
                type="submit"
                :disabled="!selectedVariant || form.processing"
                class="w-full py-4 bg-amber-500 hover:bg-amber-400 disabled:opacity-50 disabled:cursor-not-allowed text-neutral-950 font-extrabold text-xs uppercase tracking-widest rounded-xl transition-colors shadow-lg shadow-amber-500/20"
              >
                {{ form.processing ? 'Фіксація...' : 'Зафіксувати продаж' }}
              </button>

            </form>
          </div>

          <!-- Останні продажі за зміну -->
          <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-4">Останні продажі</h3>
            <div class="space-y-3 max-h-[250px] overflow-y-auto pr-1">
              <div v-for="sale in recentSales" :key="sale.id" class="bg-neutral-950 p-3 rounded-xl border border-neutral-800/80 text-xs flex justify-between items-center">
                <div>
                  <p class="font-bold text-white line-clamp-1">{{ sale.product?.title }}</p>
                  <p class="text-[10px] text-neutral-400 mt-0.5">Розмір: {{ sale.variant?.size?.value }} | Оплата: {{ sale.payment_method }}</p>
                </div>
                <div class="text-right font-extrabold text-emerald-400">
                  {{ sale.price }} ₴
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>
  </div>
</template>

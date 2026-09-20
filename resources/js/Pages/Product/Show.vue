<script setup>
import { ref } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import ImageGallery from '@/Components/Product/ImageGallery.vue'
import SizeSelector from '@/Components/Product/SizeSelector.vue'
import OneClickModal from '@/Components/Cart/OneClickModal.vue'
import CartDrawer from '@/Components/Cart/CartDrawer.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  product: Object,
  similarProducts: {
    type: Array,
    default: () => []
  },
  cart: {
    type: Object,
    default: () => ({ items: [], total: 0 })
  }
})

const selectedSize = ref(null);
const selectedVariantId = ref(null);
const isCartOpen = ref(false)
const isOneClickOpen = ref(false)

const form = useForm({
  product_id: props.product.id,
  size_id: null,
  quantity: 1
})

// Функція для показу сповіщення про необраний розмір
const showSizeWarning = () => {
  Swal.fire({
    title: 'Будь ласка, оберіть розмір!',
    text: 'Для додавання товару в кошик або оформлення замовлення необхідно вказати розмір взуття.',
    icon: 'warning',
    background: '#171717', // neutral-900
    color: '#ffffff',
    confirmButtonColor: '#f59e0b', // amber-500
    confirmButtonText: 'Зрозуміло',
    customClass: {
      popup: 'border border-neutral-800 rounded-3xl shadow-2xl',
      confirmButton: 'font-bold uppercase text-xs tracking-wider px-6 py-3 rounded-xl text-neutral-950'
    }
  })
}

const addToCart = () => {
    if (!selectedSize.value) {
        showSizeWarning();
        return;
    }

    router.post(route('cart.add'), {
        variant_id: props.product.variants.find(s => s.size_id === selectedSize.value)?.id ,
        quantity: 1,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Товар додано!');
            isCartOpen.value = true;
        },
        onError: (errors) => {
            console.error('Помилка додавання:', errors);
        }
    });
};

const openOneClick = () => {
  // Якщо розмір не обрано — показуємо SweetAlert2
  if (!selectedSize.value) {
    showSizeWarning()
    return
  }
  isOneClickOpen.value = true
}
</script>

<template>
  <Head :title="product.title" />
  <MainLayout>

  <div class="min-h-screen bg-neutral-950 text-neutral-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-12">

      <!-- Хлібні крихти та Іконка Кошика зверху -->
      <div class="flex justify-between items-center">
        <nav class="flex items-center gap-2 text-xs text-neutral-400">
          <Link href="/" class="hover:text-amber-500 transition-colors">Головна</Link>
          <span>/</span>
          <span v-if="product.category" class="hover:text-amber-500 transition-colors">{{ product.category.name }}</span>
          <span v-if="product.category">/</span>
          <span class="text-neutral-200 truncate">{{ product.title }}</span>
        </nav>

        <!-- Кнопка Кошика у шапці -->
        <button
          @click="isCartOpen = true"
          class="relative bg-neutral-900 border border-neutral-800 p-2.5 rounded-xl hover:border-amber-500/50 transition-colors flex items-center gap-2 cursor-pointer"
        >
          <span>🛒</span>
          <span v-if="cart?.items?.length" class="bg-amber-500 text-neutral-950 font-extrabold text-[11px] px-2 py-0.5 rounded-full">
            {{ cart.items.length }}
          </span>
        </button>
      </div>

      <!-- ГОЛОВНИЙ БЛОК ТОВАРУ -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start min-w-0">

        <!-- ГАЛЕРЕЯ -->
        <div class="lg:col-span-7 w-full min-w-0 overflow-hidden">
          <ImageGallery :images="product.images" :product-title="product.title" />
        </div>

        <!-- ІНФОРМАЦІЯ ТА ПОКУПКА -->
        <div class="lg:col-span-5 w-full min-w-0 space-y-6 bg-neutral-900/40 p-6 sm:p-8 rounded-3xl border border-neutral-800/80 backdrop-blur-sm">

          <div>
            <span v-if="product.brand" class="text-amber-500 text-xs font-bold tracking-widest uppercase">
              {{ product.brand.name }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">
              {{ product.title }}
            </h1>
          </div>

          <!-- Ціна -->
          <div class="flex items-baseline gap-4">
            <span class="text-3xl font-black text-white">{{ product.price }} ₴</span>
            <span v-if="product.old_price" class="text-lg text-neutral-500 line-through">{{ product.old_price }} ₴</span>
          </div>

          <hr class="border-neutral-800" />

          <!-- Вибір Розміру -->
          <div>
            <div class="flex justify-between items-center mb-3">
              <span class="text-xs font-bold uppercase tracking-wider text-neutral-300">Оберіть розмір (EU)</span>
            </div>

            <SizeSelector
              v-if="product.variants"
              :variants="product.variants"
              v-model="selectedSize"
            />
          </div>

          <!-- КНОПКИ ДІЇ (Більше не заблоковані :disabled) -->
          <div class="space-y-3 pt-2">
            <!-- Додати в кошик -->
            <button
              @click="addToCart"
              :disabled="form.processing"
              class="w-full bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-neutral-950 font-bold py-4 px-6 rounded-2xl shadow-lg shadow-amber-500/10 transition-all flex items-center justify-center gap-2 cursor-pointer uppercase text-xs tracking-wider"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              <span>{{ form.processing ? 'Додаємо...' : 'Додати в кошик' }}</span>
            </button>

            <!-- Купити в 1 клік -->
            <button
              @click="openOneClick"
              class="w-full bg-neutral-900 hover:bg-neutral-800 border border-neutral-700/80 text-white font-bold py-3.5 px-6 rounded-2xl transition-all flex items-center justify-center gap-2 cursor-pointer uppercase text-xs tracking-wider"
            >
              ⚡ Купити в 1 клік
            </button>
          </div>

          <!-- Переваги -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-xs text-neutral-400">
            <div class="flex items-center gap-2.5 bg-neutral-900 border border-neutral-800 p-3 rounded-xl">
              <span class="text-amber-500">🚚</span>
              <span>Доставка Новою Поштою</span>
            </div>
            <div class="flex items-center gap-2.5 bg-neutral-900 border border-neutral-800 p-3 rounded-xl">
              <span class="text-amber-500">🔄</span>
              <span>Обмін/повернення 14 днів</span>
            </div>
          </div>

        </div>
      </div>

      <!-- БЛОК СХОЖИХ ТОВАРІВ -->
      <div v-if="similarProducts && similarProducts.length > 0" class="pt-12 border-t border-neutral-800 space-y-6">
        <div class="flex justify-between items-end">
          <div>
            <span class="text-amber-500 text-xs font-bold uppercase tracking-wider">Рекомендації</span>
            <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight mt-0.5">Вам також може сподобатися</h2>
          </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
          <Link
            v-for="item in similarProducts"
            :key="item.id"
            :href="`/products/${item.slug}`"
            class="group bg-neutral-900/40 border border-neutral-800/80 rounded-2xl p-4 transition-all hover:border-amber-500/40 hover:bg-neutral-900/80 flex flex-col justify-between"
          >
            <div>
              <div class="aspect-square w-full overflow-hidden rounded-xl bg-neutral-900 flex items-center justify-center p-2 mb-3">
                <img
                  :src="item.images && item.images[0] ? item.images[0].path : 'https://via.placeholder.com/300'"
                  :alt="item.title"
                  class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300"
                />
              </div>

              <span v-if="item.brand" class="text-[10px] text-amber-500 font-bold uppercase tracking-widest block mb-1">
                {{ item.brand.name }}
              </span>
              <h3 class="text-xs sm:text-sm font-bold text-white truncate group-hover:text-amber-500 transition-colors">
                {{ item.title }}
              </h3>
            </div>

            <div class="mt-3 pt-2 border-t border-neutral-800/60 flex items-center justify-between">
              <span class="text-sm font-black text-white">{{ item.price }} ₴</span>
              <span class="text-xs text-amber-500 opacity-0 group-hover:opacity-100 transition-opacity">Переглянути →</span>
            </div>
          </Link>
        </div>
      </div>

    </div>

    <!-- Модальне вікно 1-клік -->
    <OneClickModal
      :is-open="isOneClickOpen"
      :product="product"
      :selected-size="selectedSize"
      @close="isOneClickOpen = false"
      @success="Swal.fire({ title: 'Дякуємо!', text: 'Наш менеджер зателефонує вам найближчим часом.', icon: 'success', background: '#171717', color: '#fff', confirmButtonColor: '#f59e0b' })"
    />

    <!-- Висувний Кошик -->
    <CartDrawer
      :is-open="isCartOpen"
      :cart-items="cart?.items || []"
      :total-price="cart?.total || 0"
      @close="isCartOpen = false"
    />
  </div>
  </MainLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  isOpen: Boolean,
  cartItems: {
    type: Array,
    default: () => []
  },
  totalPrice: {
    type: [Number, String],
    default: 0
  }
})

const emit = defineEmits(['close'])

const updateQuantity = (itemId, newQty) => {
  if (newQty < 1) return
  router.patch(route('cart.update', itemId), { quantity: newQty }, { preserveScroll: true })
}

const removeItem = (itemId) => {
  router.delete(route('cart.remove', itemId), { preserveScroll: true })
}
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isOpen" class="fixed inset-0 z-50 flex justify-end bg-neutral-950/80 backdrop-blur-sm">
        <div @click="emit('close')" class="fixed inset-0"></div>

        <!-- Панель кошика -->
        <div class="relative w-full max-w-md bg-neutral-900 border-l border-neutral-800 h-full shadow-2xl flex flex-col z-10">

          <!-- Шапка -->
          <div class="p-6 border-b border-neutral-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-2">
              <span>🛒 Ваше замовлення</span>
              <span v-if="cartItems.length" class="text-xs bg-amber-500 text-neutral-950 font-black px-2 py-0.5 rounded-full">
                {{ cartItems.length }}
              </span>
            </h3>
            <button @click="emit('close')" class="text-neutral-400 hover:text-white p-2 rounded-full hover:bg-neutral-800">
              ✕
            </button>
          </div>

          <!-- Список товарів -->
          <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
            <div v-if="!cartItems || cartItems.length === 0" class="h-full flex flex-col items-center justify-center text-center text-neutral-500 space-y-3">
              <span class="text-4xl">👟</span>
              <p class="text-sm">Ваш кошик порожній</p>
            </div>

            <div
              v-for="item in cartItems"
              :key="item.id"
              class="flex items-center gap-4 bg-neutral-950/60 p-3.5 rounded-2xl border border-neutral-800/80"
            >
              <img
                :src="item.product.images && item.product.images[0] ? item.product.images[0].path : 'https://via.placeholder.com/100'"
                :alt="item.product.title"
                class="w-16 h-16 object-contain bg-neutral-900 rounded-xl p-1"
              />

              <div class="flex-1 min-w-0">
                <h4 class="text-xs font-bold text-white truncate">{{ item.product.title }}</h4>
                <p class="text-[11px] text-neutral-400 mt-0.5">Розмір: <span class="text-amber-500 font-bold">{{ item.size.value }}</span></p>
                <p class="text-xs font-extrabold text-white mt-1">{{ item.product.price }} ₴</p>

                <!-- Кількість -->
                <div class="flex items-center gap-2 mt-2">
                  <button
                    @click="updateQuantity(item.id, item.quantity - 1)"
                    class="w-6 h-6 bg-neutral-800 hover:bg-neutral-700 rounded-lg flex items-center justify-center text-xs text-white"
                  >-</button>
                  <span class="text-xs text-white font-bold w-4 text-center">{{ item.quantity }}</span>
                  <button
                    @click="updateQuantity(item.id, item.quantity + 1)"
                    class="w-6 h-6 bg-neutral-800 hover:bg-neutral-700 rounded-lg flex items-center justify-center text-xs text-white"
                  >+</button>
                </div>
              </div>

              <!-- Кнопка видалення -->
              <button
                @click="removeItem(item.id)"
                class="text-neutral-500 hover:text-red-400 p-1 transition-colors"
                title="Видалити"
              >
                🗑️
              </button>
            </div>
          </div>

          <!-- Підсумок та кнопка оформлення -->
          <div v-if="cartItems && cartItems.length > 0" class="p-6 border-t border-neutral-800 space-y-4 bg-neutral-950/40">
            <div class="flex justify-between items-baseline text-sm">
              <span class="text-neutral-400 uppercase tracking-wider text-xs">Разом:</span>
              <span class="text-2xl font-black text-white">{{ totalPrice }} ₴</span>
            </div>

            <Link
              :href="route('checkout.index')"
              @click="emit('close')"
              class="w-full bg-amber-500 hover:bg-amber-400 text-neutral-950 font-bold py-4 rounded-xl flex items-center justify-center gap-2 uppercase text-xs tracking-wider shadow-lg shadow-amber-500/10"
            >
              <span>Оформити замовлення</span>
              <span>→</span>
            </Link>
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

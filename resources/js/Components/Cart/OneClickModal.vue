<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  isOpen: Boolean,
  product: Object,
  selectedSize: Object
})

const emit = defineEmits(['close', 'success'])

const form = useForm({
  product_id: null,
  size_id: null,
  name: '',
  phone: ''
})

const submit = () => {
  if (!props.selectedSize) return

  form.product_id = props.product.id
  form.size_id = props.selectedSize.id

  form.post(route('orders.oneClick'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      emit('success')
      emit('close')
    }
  })
}
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-950/80 backdrop-blur-sm">
        <div @click.self="emit('close')" class="fixed inset-0"></div>

        <div class="relative w-full max-w-md bg-neutral-900 border border-neutral-800 rounded-3xl p-6 sm:p-8 shadow-2xl z-10 space-y-6">
          <!-- Кнопка закриття -->
          <button
            @click="emit('close')"
            class="absolute top-5 right-5 text-neutral-400 hover:text-white p-2 rounded-full hover:bg-neutral-800 transition-colors"
          >
            ✕
          </button>

          <div>
            <h3 class="text-xl font-bold text-white uppercase tracking-wider">Швидке замовлення</h3>
            <p class="text-xs text-neutral-400 mt-1">Залиште номер, і менеджер передзвонить для уточнення деталей</p>
          </div>

          <!-- Короткі дані про товар -->
          <div class="flex items-center gap-4 bg-neutral-950/60 p-3 rounded-2xl border border-neutral-800/80">
            <img
              :src="product.images && product.images[0] ? product.images[0].path : 'https://via.placeholder.com/100'"
              :alt="product.title"
              class="w-14 h-14 object-contain bg-neutral-900 rounded-xl p-1"
            />
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-white truncate">{{ product.title }}</h4>
              <p class="text-xs text-neutral-400">Розмір: <span class="text-amber-500 font-bold">{{ selectedSize?.value || selectedSize?.size?.value }}</span></p>
              <p class="text-sm font-extrabold text-white mt-0.5">{{ product.price }} ₴</p>
            </div>
          </div>

          <!-- Форма -->
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1.5">Ваше ім'я</label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="Іван"
                class="w-full bg-neutral-950 border border-neutral-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-4 py-3 text-sm text-white placeholder-neutral-600 outline-none transition-all"
              />
              <span v-if="form.errors.name" class="text-xs text-red-400 mt-1 block">{{ form.errors.name }}</span>
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1.5">Номер телефону</label>
              <input
                v-model="form.phone"
                type="tel"
                required
                placeholder="+380 (99) 000-00-00"
                class="w-full bg-neutral-950 border border-neutral-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-4 py-3 text-sm text-white placeholder-neutral-600 outline-none transition-all"
              />
              <span v-if="form.errors.phone" class="text-xs text-red-400 mt-1 block">{{ form.errors.phone }}</span>
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="w-full bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-neutral-950 font-bold py-3.5 rounded-xl transition-all uppercase text-xs tracking-wider shadow-lg shadow-amber-500/10 cursor-pointer"
            >
              {{ form.processing ? 'Оформлення...' : 'Підтвердити замовлення' }}
            </button>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

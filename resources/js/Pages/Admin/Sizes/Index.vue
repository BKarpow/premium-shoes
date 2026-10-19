<script setup>
import { ref } from 'vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  sizes: Object,
  filters: Object
})

const search = ref(props.filters.search || '')
const isModalOpen = ref(false)
const isEditing = ref(false)
const activeSizeId = ref(null)

const form = useForm({
  value: '',
  length_cm: '',
  sort_order: 0,
})

// Відкрити модальне вікно для створення
const openCreateModal = () => {
  isEditing.value = false
  activeSizeId.value = null
  form.reset()
  form.clearErrors()
  isModalOpen.value = true
}

// Відкрити модальне вікно для редагування
const openEditModal = (size) => {
  isEditing.value = true
  activeSizeId.value = size.id
  form.value = size.value
  form.length_cm = size.length_cm || ''
  form.sort_order = size.sort_order || 0
  form.clearErrors()
  isModalOpen.value = true
}

// Збереження (Store або Update)
const submit = () => {
  if (isEditing.value) {
    form.put(`/admin/sizes/${activeSizeId.value}`, {
      preserveScroll: true,
      onSuccess: () => {
        isModalOpen.value = false
        form.reset()
      }
    })
  } else {
    form.post('/admin/sizes', {
      preserveScroll: true,
      onSuccess: () => {
        isModalOpen.value = false
        form.reset()
      }
    })
  }
}

// Видалення
const deleteSize = (id) => {
  if (confirm('Ви впевнені, що хочете видалити цей розмір?')) {
    router.delete(`/admin/sizes/${id}`, {
      preserveScroll: true
    })
  }
}

// Пошук
const handleSearch = () => {
  router.get('/admin/sizes', { search: search.value }, { preserveState: true, replace: true })
}
</script>

<template>
  <Head title="Керування розмірами" />

  <AdminLayout>
    <div class="space-y-6 max-w-5xl mx-auto">

      <!-- Шапка сторінки (Адаптивна колонка на мобільних) -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-2xl font-extrabold text-white">Довідник розмірів</h1>
          <p class="text-xs text-neutral-400 mt-1">Керування сіткою європейських розмірів взуття та довжиною устілок.</p>
        </div>

        <button
          @click="openCreateModal"
          class="w-full sm:w-auto bg-amber-500 hover:bg-amber-400 text-neutral-950 font-bold px-4 py-2.5 rounded-xl transition-all cursor-pointer text-xs uppercase tracking-wider text-center"
        >
          + Додати розмір
        </button>
      </div>

      <!-- Пошук та контейнер таблиці / карток -->
      <div class="bg-neutral-900/50 border border-neutral-800 rounded-3xl p-4 sm:p-6 backdrop-blur-sm space-y-4">

        <!-- Панель пошуку -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
          <input
            v-model="search"
            @keyup.enter="handleSearch"
            type="text"
            placeholder="Пошук за розміром..."
            class="bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-amber-500 w-full sm:w-72"
          />
          <button @click="handleSearch" class="bg-neutral-800 hover:bg-neutral-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors cursor-pointer">
            Шукати
          </button>
        </div>

        <!-- 1. МОБІЛЬНЕ ВІДОБРАЖЕННЯ: Картки (тільки для екранів < sm)[cite: 2] -->
        <div class="space-y-3 sm:hidden">
          <div
            v-for="size in sizes.data"
            :key="size.id"
            class="bg-neutral-900 border border-neutral-800 rounded-2xl p-4 shadow-md space-y-3"
          >
            <div class="flex items-center justify-between border-b border-neutral-800 pb-2">
              <span class="font-bold text-amber-400 text-sm">Розмір (EU): {{ size.value }}</span>
              <span class="text-[10px] bg-neutral-800 text-neutral-400 px-2 py-0.5 rounded-md">ID: {{ size.id }}</span>
            </div>

            <div class="text-xs text-neutral-400 flex justify-between">
              <span>Довжина устілки:</span>
              <span class="text-neutral-200 font-medium">{{ size.length_cm ? size.length_cm + ' см' : '—' }}</span>
            </div>

            <div class="text-xs text-neutral-400 flex justify-between">
              <span>Сортування:</span>
              <span class="text-neutral-200 font-medium">{{ size.sort_order }}</span>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-800/60">
              <button
                @click="openEditModal(size)"
                class="px-3 py-1.5 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-semibold rounded-lg hover:bg-amber-500/20 cursor-pointer"
              >
                Редагувати
              </button>
              <button
                @click="deleteSize(size.id)"
                class="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 text-xs font-semibold rounded-lg hover:bg-red-500/20 cursor-pointer"
              >
                Видалити
              </button>
            </div>
          </div>

          <div v-if="sizes.data.length === 0" class="text-center py-8 text-neutral-500 text-xs">
            Розмірів не знайдено
          </div>
        </div>

        <!-- 2. ДЕСКТОПНЕ ВІДОБРАЖЕННЯ: Класична таблиця (від sm і вище)[cite: 2] -->
        <div class="hidden sm:block overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-neutral-800 text-xs uppercase tracking-wider text-neutral-400">
                <th class="py-3 px-4">ID</th>
                <th class="py-3 px-4">Розмір (EU)</th>
                <th class="py-3 px-4">Довжина устілки (см)</th>
                <th class="py-3 px-4">Сортування</th>
                <th class="py-3 px-4 text-right">Дії</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-800/60 text-sm">
              <tr v-for="size in sizes.data" :key="size.id" class="hover:bg-neutral-900/40 transition-colors">
                <td class="py-3.5 px-4 text-neutral-400">#{{ size.id }}</td>
                <td class="py-3.5 px-4 font-bold text-white">{{ size.value }}</td>
                <td class="py-3.5 px-4 text-neutral-300">{{ size.length_cm ? size.length_cm + ' см' : '—' }}</td>
                <td class="py-3.5 px-4 text-neutral-400">{{ size.sort_order }}</td>
                <td class="py-3.5 px-4 text-right space-x-2">
                  <button
                    @click="openEditModal(size)"
                    class="text-amber-400 hover:text-amber-300 font-semibold text-xs bg-amber-500/10 px-3 py-1.5 rounded-lg transition-colors cursor-pointer"
                  >
                    Редагувати
                  </button>
                  <button
                    @click="deleteSize(size.id)"
                    class="text-rose-400 hover:text-rose-300 font-semibold text-xs bg-rose-500/10 px-3 py-1.5 rounded-lg transition-colors cursor-pointer"
                  >
                    Видалити
                  </button>
                </td>
              </tr>
              <tr v-if="sizes.data.length === 0">
                <td colspan="5" class="text-center py-8 text-neutral-500 text-xs">Розмірів не знайдено</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Пагінація -->
        <div v-if="sizes.links && sizes.links.length > 3" class="flex flex-col sm:flex-row items-center justify-between pt-4 border-t border-neutral-800 gap-4">
          <div class="text-xs text-neutral-400 text-center sm:text-left">
            Показано з <span class="font-semibold text-white">{{ sizes.from || 0 }}</span> по <span class="font-semibold text-white">{{ sizes.to || 0 }}</span> із <span class="font-semibold text-white">{{ sizes.total }}</span> результатів
          </div>

          <div class="flex flex-wrap justify-center gap-1">
            <template v-for="(link, index) in sizes.links" :key="index">
              <div
                v-if="!link.url"
                v-html="link.label"
                class="px-3 py-1.5 text-xs rounded-xl bg-neutral-900 text-neutral-600 border border-neutral-800 cursor-not-allowed"
              />
              <Link
                v-else
                :href="link.url"
                v-html="link.label"
                preserve-scroll
                :class="[
                  'px-3 py-1.5 text-xs rounded-xl border transition-colors',
                  link.active
                    ? 'bg-amber-500 border-amber-500 text-neutral-950 font-bold'
                    : 'bg-neutral-900 border-neutral-800 text-neutral-300 hover:bg-neutral-800 hover:text-white'
                ]"
              />
            </template>
          </div>
        </div>

      </div>

    </div>

    <!-- Модальне вікно створення/редагування (повноекранне на смартфонах) -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/70 backdrop-blur-sm sm:p-4">
      <div class="bg-neutral-900 border-t sm:border border-neutral-800 rounded-t-3xl sm:rounded-3xl p-6 w-full max-w-md space-y-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-bold text-white">
            {{ isEditing ? 'Редагувати розмір' : 'Додати новий розмір' }}
          </h3>
          <button @click="isModalOpen = false" class="text-neutral-400 hover:text-white cursor-pointer text-lg p-1">✕</button>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-xs uppercase font-bold text-neutral-400 mb-1">Значення розміру (напр. 42 або 42.5)</label>
            <input
              v-model="form.value"
              type="text"
              class="w-full bg-neutral-950 border border-neutral-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-amber-500 text-sm"
              required
            />
            <div v-if="form.errors.value" class="text-red-500 text-xs mt-1">{{ form.errors.value }}</div>
          </div>

          <div>
            <label class="block text-xs uppercase font-bold text-neutral-400 mb-1">Довжина устілки в см (необов'язково)</label>
            <input
              v-model="form.length_cm"
              type="number"
              step="0.1"
              class="w-full bg-neutral-950 border border-neutral-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-amber-500 text-sm"
            />
            <div v-if="form.errors.length_cm" class="text-red-500 text-xs mt-1">{{ form.errors.length_cm }}</div>
          </div>

          <div>
            <label class="block text-xs uppercase font-bold text-neutral-400 mb-1">Порядок сортування</label>
            <input
              v-model="form.sort_order"
              type="number"
              class="w-full bg-neutral-950 border border-neutral-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-amber-500 text-sm"
            />
            <div v-if="form.errors.sort_order" class="text-red-500 text-xs mt-1">{{ form.errors.sort_order }}</div>
          </div>

          <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
            <button
              type="button"
              @click="isModalOpen = false"
              class="w-full sm:w-auto bg-neutral-800 hover:bg-neutral-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase transition-colors cursor-pointer text-center"
            >
              Скасувати
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full sm:w-auto bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-neutral-950 font-bold px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all cursor-pointer text-center"
            >
              {{ isEditing ? 'Зберегти зміни' : 'Створити' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

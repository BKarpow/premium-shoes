<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  form: Object,
});

const citySearchQuery = ref('');
const cities = ref([]);
const warehouses = ref([]);
const isLoadingCities = ref(false);
const isLoadingWarehouses = ref(false);

const searchCities = async () => {
  if (citySearchQuery.value.length < 2) {
    cities.value = [];
    return;
  }

  isLoadingCities.value = true;
  try {
    const response = await axios.get(route('checkout.np.cities'), {
      params: { q: citySearchQuery.value }
    });
    cities.value = response.data;
  } catch (error) {
    console.error('Помилка завантаження міст', error);
  } finally {
    isLoadingCities.value = false;
  }
};

const selectCity = async (city) => {
  props.form.city_ref = city.Ref;
  props.form.city_name = city.Description;
  citySearchQuery.value = city.Description;
  cities.value = [];

  props.form.warehouse_ref = '';
  props.form.warehouse_address = '';

  await loadWarehouses(city.Ref);
};

const loadWarehouses = async (cityRef) => {
  isLoadingWarehouses.value = true;
  try {
    const response = await axios.get(route('checkout.np.warehouses'), {
      params: { city_ref: cityRef }
    });
    warehouses.value = response.data;
  } catch (error) {
    console.error('Помилка завантаження відділень', error);
  } finally {
    isLoadingWarehouses.value = false;
  }
};

const selectWarehouse = (event) => {
  const selectedRef = event.target.value;
  const warehouse = warehouses.value.find(w => w.Ref === selectedRef);

  if (warehouse) {
    props.form.warehouse_ref = warehouse.Ref;
    props.form.warehouse_address = warehouse.Description;
  }
};
</script>

<template>
  <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-xl backdrop-blur-sm space-y-4">
    <h2 class="text-lg font-bold text-white flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-500/10 text-amber-400 text-xs border border-amber-500/20">2</span>
      Спосіб доставки
    </h2>

    <!-- Вибір типу доставки -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <label
        :class="[
          'rounded-xl p-4 cursor-pointer flex flex-col transition border',
          form.shipping_type === 'pickup'
            ? 'border-amber-500 bg-amber-500/10 shadow-lg shadow-amber-500/5'
            : 'border-slate-800 bg-slate-950 hover:border-slate-700'
        ]"
      >
        <input type="radio" v-model="form.shipping_type" value="pickup" class="sr-only" />
        <span class="font-semibold text-sm text-white">Самовивіз із магазину</span>
        <span class="text-xs text-slate-400 mt-1">Безкоштовно (м. Київ, вул. Хрещатик, 1)</span>
      </label>

      <label
        :class="[
          'rounded-xl p-4 cursor-pointer flex flex-col transition border',
          form.shipping_type === 'nova_poshta'
            ? 'border-amber-500 bg-amber-500/10 shadow-lg shadow-amber-500/5'
            : 'border-slate-800 bg-slate-950 hover:border-slate-700'
        ]"
      >
        <input type="radio" v-model="form.shipping_type" value="nova_poshta" class="sr-only" />
        <span class="font-semibold text-sm text-white">Нова Пошта</span>
        <span class="text-xs text-slate-400 mt-1">За тарифами перевізника</span>
      </label>
    </div>

    <!-- Поля для Нової Пошти -->
    <div v-if="form.shipping_type === 'nova_poshta'" class="space-y-4 pt-4 border-t border-slate-800">
      <!-- Пошук міста -->
      <div class="relative">
        <label class="block text-xs font-medium text-slate-400 mb-1">Населений пункт</label>
        <input
          v-model="citySearchQuery"
          @input="searchCities"
          type="text"
          class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
          placeholder="Почніть введення міста (наприклад, Київ)..."
        />
        <span v-if="form.errors.city_ref" class="text-xs text-red-400 mt-1 block">{{ form.errors.city_ref }}</span>

        <!-- Випадаючий список міст -->
        <ul v-if="cities.length > 0" class="absolute z-20 w-full bg-slate-900 border border-slate-800 rounded-xl mt-1 max-h-48 overflow-y-auto shadow-2xl backdrop-blur-md">
          <li
            v-for="city in cities"
            :key="city.Ref"
            @click="selectCity(city)"
            class="px-4 py-2.5 text-sm hover:bg-slate-800/80 cursor-pointer text-slate-200 transition-colors"
          >
            {{ city.Description }}
          </li>
        </ul>
      </div>

      <!-- Вибір відділення / поштомату -->
      <div>
        <label class="block text-xs font-medium text-slate-400 mb-1">Відділення або поштомат</label>
        <select
          @change="selectWarehouse"
          :disabled="!form.city_ref || isLoadingWarehouses"
          class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <option value="" class="bg-slate-950">Оберіть відділення</option>
          <option v-for="warehouse in warehouses" :key="warehouse.Ref" :value="warehouse.Ref" class="bg-slate-950">
            {{ warehouse.Description }}
          </option>
        </select>
        <span v-if="form.errors.warehouse_ref" class="text-xs text-red-400 mt-1 block">{{ form.errors.warehouse_ref }}</span>
      </div>
    </div>
  </div>
</template>

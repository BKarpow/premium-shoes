<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import CustomerForm from './Partials/CustomerForm.vue';
import ShippingSelector from './Partials/ShippingSelector.vue';
import OrderSummary from './Partials/OrderSummary.vue';

defineProps({
  cart: Object,
});

const form = useForm({
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  shipping_type: 'pickup',
  city_ref: '',
  city_name: '',
  warehouse_ref: '',
  warehouse_address: '',
});

const submitOrder = () => {
  form.post(route('checkout.store'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Оформлення замовлення" />

  <MainLayout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-black tracking-tight text-white mb-6">Оформлення замовлення</h1>

      <form @submit.prevent="submitOrder" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <!-- Ліва колонка: Контакти та Доставка -->
        <div class="lg:col-span-2 space-y-6">
          <CustomerForm :form="form" />
          <ShippingSelector :form="form" />
        </div>

        <!-- Права колонка: Підсумок замовлення -->
        <div class="lg:col-span-1">
          <OrderSummary :cart="cart" :form="form" />
        </div>

      </form>
    </div>
  </MainLayout>
</template>

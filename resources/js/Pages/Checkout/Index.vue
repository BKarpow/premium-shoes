<script setup>
import { useForm, Head, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import CustomerForm from './Partials/CustomerForm.vue';
import ShippingSelector from './Partials/ShippingSelector.vue';
import OrderSummary from './Partials/OrderSummary.vue';

const props = defineProps({
    cart: Object,
  savedProfile: Object,
});

const form = useForm({
  first_name: props.savedProfile?.first_name || '',
  last_name: props.savedProfile?.last_name || '',
  phone: props.savedProfile?.phone || '',
  email: usePage().props.auth.user?.email || '',
  shipping_type: 'nova_poshta',
  city_ref: props.savedProfile?.np_city_ref || '',
  city_name: props.savedProfile?.np_city_name || '',
  warehouse_ref: props.savedProfile?.np_warehouse_ref || '',
  warehouse_address: props.savedProfile?.np_warehouse_name || '',
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

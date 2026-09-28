<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    user: Object,
    profile: Object,
});

const form = useForm({
    name: props.user.name || '',
    first_name: props.profile?.first_name || '',
    last_name: props.profile?.last_name || '',
    middle_name: props.profile?.middle_name || '',
    phone: props.profile?.phone || '',
    np_city_ref: props.profile?.np_city_ref || '',
    np_city_name: props.profile?.np_city_name || '',
    np_warehouse_ref: props.profile?.np_warehouse_ref || '',
    np_warehouse_name: props.profile?.np_warehouse_name || '',
    birth_date: props.profile?.birth_date || '',
    gender: props.profile?.gender || '',
});

const submit = () => {
    form.put(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <MainLayout>
        <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 border-b border-slate-800 pb-4">
                <h1 class="text-2xl font-bold text-white">👤 Мій профіль</h1>
                <p class="text-sm text-slate-400">Керуйте персональними даними та адресою доставки за замовчуванням</p>
            </div>

            <!-- Сповіщення про успіх -->
            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl">
                {{ $page.props.flash.success }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Основна інформація -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
                    <h2 class="text-lg font-semibold text-amber-500 border-b border-slate-800 pb-2">Основні дані</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Отображаемое имя (Нікнейм)</label>
                            <input v-model="form.name" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:ring-amber-500" required />
                            <div v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Email (не редагується)</label>
                            <input :value="user.email" type="email" disabled class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-500 cursor-not-allowed" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Ім'я</label>
                            <input v-model="form.first_name" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:ring-amber-500" placeholder="Тарас" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Прізвище</label>
                            <input v-model="form.last_name" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:ring-amber-500" placeholder="Шевченко" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">По батькові</label>
                            <input v-model="form.middle_name" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:ring-amber-500" placeholder="Григорович" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Номер телефону</label>
                            <input v-model="form.phone" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:ring-amber-500" placeholder="+380XXXXXXXXX" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Дата народження</label>
                            <input v-model="form.birth_date" type="date" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:ring-amber-500" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Стать</label>
                            <select v-model="form.gender" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:ring-amber-500">
                                <option value="">Не вказано</option>
                                <option value="male">Чоловіча</option>
                                <option value="female">Жіноча</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Доставка Новою Поштою за замовчуванням -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
                    <h2 class="text-lg font-semibold text-amber-500 border-b border-slate-800 pb-2">📦 Доставка Новою Поштою (за замовчуванням)</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Місто</label>
                            <input v-model="form.np_city_name" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:ring-amber-500" placeholder="наприклад, Київ" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Відділення / Поштомат</label>
                            <input v-model="form.np_warehouse_name" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:ring-amber-500" placeholder="наприклад, Відділення №1" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm rounded-xl transition shadow-lg shadow-amber-500/10 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Збереження...' : 'Зберегти зміни' }}
                    </button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>

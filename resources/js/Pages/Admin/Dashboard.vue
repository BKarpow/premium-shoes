<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_orders: 0,
            total_users: 0,
            total_products: 0,
            total_revenue: 0,
        }),
    },
    chartsData: {
        type: Object,
        default: () => ({
            week: { labels: [], counts: [], sums: [] },
            month: { labels: [], counts: [], sums: [] },
            quarter: { labels: [], counts: [], sums: [] },
        }),
    },
});

// Обраний період: 'week' | 'month' | 'quarter'
const selectedPeriod = ref('week');
const chartCanvas = ref(null);
let chartInstance = null;

// Поточні дані для графіка залежно від обраного періоду
const currentChartData = computed(() => {
    return props.chartsData[selectedPeriod.value] || { labels: [], counts: [], sums: [] };
});

// Ініціалізація та оновлення Chart.js
const renderChart = () => {
    if (!chartCanvas.value) return;

    if (chartInstance) {
        chartInstance.destroy();
    }

    const ctx = chartCanvas.value.getContext('2d');

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: currentChartData.value.labels,
            datasets: [
                {
                    label: 'Кількість замовлень',
                    data: currentChartData.value.counts,
                    borderColor: '#f59e0b', // amber-500
                    backgroundColor: 'rgba(245, 158, 11, 0.15)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: currentChartData.value.labels.length > 30 ? 2 : 4,
                    yAxisID: 'yCount',
                },
                {
                    label: 'Сума (грн)',
                    data: currentChartData.value.sums,
                    borderColor: '#3b82f6', // blue-500
                    backgroundColor: 'transparent',
                    borderDash: [5, 5],
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 0,
                    yAxisID: 'ySum',
                }
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#94a3b8', // slate-400
                        font: { size: 12, weight: 'bold' },
                    },
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#cbd5e1',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 12,
                },
            },
            scales: {
                x: {
                    grid: { color: 'rgba(51, 65, 85, 0.3)' },
                    ticks: { color: '#94a3b8', font: { size: 11 } },
                },
                yCount: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Замовлення (шт)', color: '#f59e0b', font: { size: 11 } },
                    grid: { color: 'rgba(51, 65, 85, 0.4)' },
                    ticks: { color: '#94a3b8', precision: 0 },
                },
                ySum: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Сума (грн)', color: '#3b82f6', font: { size: 11 } },
                    grid: { drawOnChartArea: false },
                    ticks: { color: '#94a3b8' },
                },
            },
        },
    });
};

onMounted(() => {
    renderChart();
});

watch(selectedPeriod, () => {
    renderChart();
});
</script>

<template>
    <Head title="Дашборд — Адмін панель" />

    <AdminLayout>
        <template #header>
            Дашборд
        </template>

        <div class="space-y-6">
            <!-- ================= 1. КАРТКИ З СТАТИСТИКОЮ ================= -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Замовлення -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Замовлення</p>
                            <h3 class="text-2xl font-black text-slate-100 mt-1">{{ stats.total_orders }}</h3>
                        </div>
                        <div class="h-12 w-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl">
                            📦
                        </div>
                    </div>
                    <div class="mt-3 text-[11px] text-amber-400 font-medium">Усі створені замовлення</div>
                </div>

                <!-- Користувачі -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Користувачі</p>
                            <h3 class="text-2xl font-black text-slate-100 mt-1">{{ stats.total_users }}</h3>
                        </div>
                        <div class="h-12 w-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xl">
                            👥
                        </div>
                    </div>
                    <div class="mt-3 text-[11px] text-blue-400 font-medium">Зареєстровані клієнти</div>
                </div>

                <!-- Товари -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Товари</p>
                            <h3 class="text-2xl font-black text-slate-100 mt-1">{{ stats.total_products }}</h3>
                        </div>
                        <div class="h-12 w-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl">
                            👟
                        </div>
                    </div>
                    <div class="mt-3 text-[11px] text-emerald-400 font-medium">Позицій у каталозі</div>
                </div>

                <!-- Загальний виторг -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Загальний виторг</p>
                            <h3 class="text-2xl font-black text-amber-400 mt-1">
                                {{ Number(stats.total_revenue).toLocaleString('uk-UA') }} ₴
                            </h3>
                        </div>
                        <div class="h-12 w-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-xl">
                            💰
                        </div>
                    </div>
                    <div class="mt-3 text-[11px] text-purple-400 font-medium">Загальна сума продажів</div>
                </div>
            </div>

            <!-- ================= 2. ГРАФІК ЗАМОВЛЕНЬ З ПЕРЕМИКАЧЕМ ПЕРІОДІВ ================= -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                <!-- Шапка графіка з перемикачем періодів -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div>
                        <h2 class="text-base font-bold text-slate-100 flex items-center gap-2">
                            <span>📈</span>
                            <span>Динаміка замовлень та продажів</span>
                        </h2>
                        <p class="text-xs text-slate-400">Графік замовлень та доходу за обраний період</p>
                    </div>

                    <!-- Кнопки перемикання періоду -->
                    <div class="flex items-center gap-1.5 bg-slate-950 p-1.5 rounded-xl border border-slate-800 self-stretch sm:self-auto">
                        <button
                            @click="selectedPeriod = 'week'"
                            class="flex-1 sm:flex-initial px-3 py-1.5 text-xs font-bold rounded-lg transition active:scale-95"
                            :class="selectedPeriod === 'week'
                                ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20'
                                : 'text-slate-400 hover:text-white hover:bg-slate-800'"
                        >
                            Тиждень (7d)
                        </button>
                        <button
                            @click="selectedPeriod = 'month'"
                            class="flex-1 sm:flex-initial px-3 py-1.5 text-xs font-bold rounded-lg transition active:scale-95"
                            :class="selectedPeriod === 'month'
                                ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20'
                                : 'text-slate-400 hover:text-white hover:bg-slate-800'"
                        >
                            Місяць (30d)
                        </button>
                        <button
                            @click="selectedPeriod = 'quarter'"
                            class="flex-1 sm:flex-initial px-3 py-1.5 text-xs font-bold rounded-lg transition active:scale-95"
                            :class="selectedPeriod === 'quarter'
                                ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20'
                                : 'text-slate-400 hover:text-white hover:bg-slate-800'"
                        >
                            3 місяці (90d)
                        </button>
                    </div>
                </div>

                <!-- Контейнер для Chart.js полотна -->
                <div class="relative h-72 sm:h-80 w-full pt-2">
                    <canvas ref="chartCanvas"></canvas>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

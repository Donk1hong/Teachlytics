<script setup>
import { onMounted, ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Rating from 'primevue/rating';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    kpis: Object,
    topCurators: Array,
    currentPeriod: String,
    userStatus: Object,
    activities: Array,
    pendingTasks: Array
});

const changePeriod = (period) => {
    router.get(route('dashboard'), { period: period }, {
        preserveState: true,
        preserveScroll: true
    });
};

const getKpiData = () => [
    { id: 1, label: 'Средний балл', value: props.kpis?.avg_score || 0, trend: '+0.2', trendUp: true, icon: 'pi-star-fill', iconColor: 'text-yellow-500', bg: 'bg-yellow-500/10' },
    { id: 2, label: 'Кураторов', value: props.kpis?.curators_count || 0, trend: '+2', trendUp: true, icon: 'pi-users', iconColor: 'text-indigo-500', bg: 'bg-indigo-500/10' },
    { id: 3, label: 'Групп оценено', value: (props.kpis?.groups_evaluated || 0) + '%', trend: '-3%', trendUp: false, icon: 'pi-check-circle', iconColor: 'text-emerald-500', bg: 'bg-emerald-500/10' },
];

const chartCanvas = ref(null);
let chartInstance = null;

const updateChart = () => {
    if (!chartCanvas.value) return;

    const newData = [
        props.userStatus?.indicators?.attendance || 0,
        props.userStatus?.indicators?.activity || 0,
        props.userStatus?.indicators?.discipline || 0
    ];

    if (chartInstance) {
        chartInstance.data.datasets[0].data = newData;
        chartInstance.update();
    } else {
        const ctx = chartCanvas.value.getContext('2d');

        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Посещаемость', 'Активность', 'Дисциплина'],
                datasets: [{
                    data: newData,
                    backgroundColor: '#6366f1',
                    borderRadius: 12,
                    borderSkipped: false,
                    barThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 13, weight: 'bold' },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(148, 163, 184, 0.05)', drawBorder: false },
                        ticks: { color: '#64748b', font: { weight: 'bold', size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { weight: 'bold', size: 10 } }
                    }
                }
            }
        });
    }
};

onMounted(() => updateChart());
watch(() => props.userStatus, () => updateChart(), { deep: true });

const downloadReport = () => {
    window.location.href = route('dashboard.report');
};
</script>

<template>
    <Head title="Дашборд" />

    <AuthenticatedLayout>
        <template #header>
            Дашборд
        </template>

        <div class="max-w-[1500px] mx-auto space-y-6 animate-slide-up pb-10">

            <div class="flex items-center justify-end">
                <div class="inline-flex bg-white/60 dark:bg-[#111827]/60 backdrop-blur-md border border-slate-200 dark:border-slate-800 p-1.5 rounded-2xl shadow-sm">
                    <button
                        @click="changePeriod('month')"
                        :class="['px-5 py-2 rounded-xl text-xs font-bold transition-all', props.currentPeriod === 'month' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 dark:text-slate-400']"
                    >За месяц</button>
                    <button
                        @click="changePeriod('semester')"
                        :class="['px-5 py-2 rounded-xl text-xs font-bold transition-all', props.currentPeriod === 'semester' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 dark:text-slate-400']"
                    >Семестр</button>
                    <button
                        @click="changePeriod('year')"
                        :class="['px-5 py-2 rounded-xl text-xs font-bold transition-all', props.currentPeriod === 'year' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 dark:text-slate-400']"
                    >Год</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="kpi in getKpiData()" :key="kpi.id" class="bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2rem] p-6 shadow-xl flex items-center gap-5 hover:scale-[1.02] transition-transform cursor-default">
                    <div :class="['w-14 h-14 rounded-[1.25rem] flex items-center justify-center shrink-0 shadow-inner', kpi.bg]">
                        <i :class="['pi text-2xl', kpi.icon, kpi.iconColor]"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">{{ kpi.label }}</p>
                        <div class="flex items-end gap-3">
                            <h4 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter leading-none">{{ kpi.value }}</h4>
                            <span :class="['text-xs font-bold mb-1', kpi.trendUp ? 'text-emerald-500' : 'text-rose-500']">
                                <i :class="['pi text-[10px] mr-0.5', kpi.trendUp ? 'pi-arrow-up' : 'pi-arrow-down']"></i>{{ kpi.trend }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden transition-all">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Рейтинг кураторов: Топ-5</h3>
                            <Link :href="route('curators')" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 transition-colors">Смотреть весь</Link>
                        </div>

                        <div class="space-y-3 divide-y divide-slate-100 dark:divide-slate-800/50">
                            <div v-for="(curator, index) in props.topCurators" :key="curator.id" class="pt-4 flex items-center justify-between gap-5 group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 rounded-2xl transition-all p-3 -mx-3">
                                <Link :href="route('curators.show', curator.id)">
                                    <div class="flex items-center gap-5">
                                        <span class="w-8 text-center text-3xl font-black text-slate-300 dark:text-slate-700 tracking-tighter transition-colors group-hover:text-indigo-600">{{ index + 1 }}</span>
                                        <img :src="`https://ui-avatars.com/api/?name=${curator.name} &background=6366f1&color=fff&size=150`" alt="" class="w-12 h-12 rounded-full object-cover border-2 cursor-pointer border-white dark:border-slate-800 group-hover:border-indigo-500 transition-colors" />
                                        <div>
                                            <p class="text-lg font-bold text-slate-900 dark:text-white transition-colors group-hover:text-indigo-600">{{ curator.name }}</p>
                                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ curator.department || 'Общая кафедра' }}</p>
                                        </div>
                                    </div>
                                </Link>
                                <div class="text-right flex flex-col items-end gap-1">
                                    <Rating :modelValue="5" readonly :cancel="false" class="!text-yellow-400 scale-90" />
                                    <div class="flex flex-col items-end">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Рейтинг:</p>
                                        <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tighter leading-none">
                                            {{ parseFloat(curator.groups_avg_average_score || 0).toFixed(1) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] p-8 md:p-10 shadow-2xl transition-all">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight mb-8">Недавняя активность</h3>
                        <div class="relative border-l-2 border-slate-100 dark:border-slate-800/80 ml-4 space-y-8">
                            <div v-if="props.activities.length === 0" class="text-sm font-medium text-slate-500 dark:text-slate-400">Нет активности</div>
                            <div v-for="act in props.activities" :key="act.id" class="relative pl-8">
                                <div :class="['absolute -left-[17px] top-0 w-8 h-8 rounded-full flex items-center justify-center border-4 border-white dark:border-[#0B0F19]', act.bg || 'bg-indigo-500/10']">
                                    <i :class="['pi text-xs', act.icon, act.color]"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ act.title }}</h4>
                                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">{{ act.desc }}</p>
                                    <span class="text-[10px] font-bold text-slate-400 mt-2 block">{{ act.time }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1 space-y-8">

                    <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-[2.5rem] p-8 shadow-2xl shadow-indigo-600/30 relative overflow-hidden text-white">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-900/40 rounded-full blur-xl"></div>

                        <div class="relative z-10">
                            <p class="text-xs font-bold text-indigo-200 uppercase tracking-widest mb-2">Ваш статус</p>

                            <h3 v-if="props.userStatus?.rank !== '-'" class="text-2xl font-black tracking-tight mb-1">Вы на {{ props.userStatus?.rank }}-м месте! 🏆</h3>
                            <h3 v-else class="text-2xl font-black tracking-tight mb-1">Режим Администратора 👑</h3>

                            <p v-if="props.userStatus?.rank === '-'" class="text-sm font-medium text-indigo-100 mb-6">Администраторы не участвуют в рейтинге.</p>
                            <p v-else-if="props.userStatus?.rank === 1" class="text-sm font-medium text-indigo-100 mb-6">Вы опережаете ближайшего коллегу на {{ props.userStatus?.diff }} балла.</p>
                            <p v-else class="text-sm font-medium text-indigo-100 mb-6">До следующего места: {{ props.userStatus?.diff }} балла.</p>

                            <div class="space-y-3">
                                <Link :href="route('ratings.index')" class="w-full py-3 px-4 bg-white text-indigo-600 rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-slate-50 transition-colors shadow-lg active:scale-95">
                                    <i class="pi pi-pencil"></i> Оценить группу
                                </Link>
                                <button @click="downloadReport" class="w-full py-3 px-4 bg-indigo-800/50 border border-indigo-400/30 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-indigo-800/70 transition-colors active:scale-95">
                                    <i class="pi pi-download"></i> Скачать мой отчет
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/60 dark:bg-[#111827]/80 backdrop-blur-2xl border border-slate-200 dark:border-slate-800/80 rounded-[2.5rem] p-8 shadow-2xl relative transition-all">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight tracking-tight mb-6">Мои показатели</h3>

                        <div class="mt-8 h-[200px] relative w-full flex items-center justify-center">
                            <canvas ref="chartCanvas"></canvas>
                        </div>
                    </div>

                    <div class="bg-white/60 dark:bg-[#111827]/80 backdrop-blur-2xl border border-slate-200 dark:border-slate-800/80 rounded-[2.5rem] p-8 shadow-2xl relative transition-all">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight tracking-tight mb-6">Требует внимания</h3>

                        <div class="space-y-4">
                            <div v-if="props.pendingTasks.length === 0" class="text-sm font-medium text-slate-500 dark:text-slate-400">Нет задач</div>
                            <div v-for="task in props.pendingTasks" :key="task.id" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/50 hover:border-indigo-500/30 transition-colors group cursor-pointer">
                                <div class="flex items-center gap-4">
                                    <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0', task.urgent ? 'bg-rose-500/10 text-rose-500' : 'bg-indigo-500/10 text-indigo-500']">
                                        <i class="pi pi-exclamation-circle text-lg" v-if="task.urgent"></i>
                                        <i class="pi pi-clock text-lg" v-else></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ task.group }}</h4>
                                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ task.subject }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span :class="['text-[10px] font-bold px-2 py-1 rounded-full', task.urgent ? 'bg-rose-500/10 text-rose-500' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-300']">
                                        {{ task.deadline }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <Link :href="route('calendar')">
                            <button  class="w-full mt-6 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors">
                                Смотреть все задачи
                            </button>
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.8s ease-out forwards; }
</style>

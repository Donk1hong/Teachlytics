<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';

const props = defineProps({
    curators: Array,
    departments: Array,
    filters: Object
});

const search = ref(props.filters.search || '');
const selectedDept = ref(props.filters.dept || 'Все кафедры');

watch([search, selectedDept], ([newSearch, newDept]) => {
    router.get(route('curators'), { search: newSearch, dept: newDept }, {
        preserveState: true,
        replace: true
    });
});
</script>

<template>
    <Head title="Список кураторов" />

    <AuthenticatedLayout>
        <template #header>Кураторы</template>

        <div class="max-w-[1400px] mx-auto space-y-8 animate-slide-up">

            <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-white/40 dark:bg-[#111827]/40 p-4 rounded-[2rem] border border-white/50 dark:border-slate-800/80 backdrop-blur-xl">
                <div class="relative w-full md:w-96">
                    <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input v-model="search" type="text" placeholder="Поиск куратора..."
                           class="w-full pl-12 pr-4 h-12 rounded-2xl bg-white dark:bg-slate-900 border-none focus:ring-2 focus:ring-indigo-500/50 text-sm font-bold">
                </div>

                <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0 w-full md:w-auto">
                    <button @click="selectedDept = 'Все кафедры'"
                            :class="['px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap',
                        selectedDept === 'Все кафедры' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-white dark:bg-slate-900 text-slate-500 hover:bg-slate-50']">
                        Все
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="curator in props.curators" :key="curator.id"
                     class="bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] p-8 shadow-xl hover:scale-[1.02] transition-all group">

                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <img :src="`https://ui-avatars.com/api/?name=${curator.name} &background=6366f1&color=fff&size=150`" class="w-16 h-16 rounded-[1.5rem] object-cover shadow-lg  border-2 border-white dark:border-slate-800 group-hover:border-indigo-500 transition-colors">
                            <div>
                                <h4 class="text-lg font-black text-slate-900 dark:text-white leading-tight">{{ curator.name }}</h4>
                                <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-1">{{ curator.department || 'Без кафедры' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Групп</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white">{{ curator.groups_count }}</p>
                        </div>
                        <div class="bg-indigo-50 dark:bg-indigo-500/5 p-4 rounded-2xl">
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Рейтинг</p>
                            <p class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                                {{ curator.groups_avg_average_score ? parseFloat(curator.groups_avg_average_score).toFixed(1) : '0.0' }}
                            </p>
                        </div>
                    </div>

                    <Link :href="route('curators.show', curator.id)"
                          class="block w-full py-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-center text-xs font-black uppercase tracking-[0.2em] text-slate-500 hover:text-indigo-600 hover:border-indigo-500 transition-all">
                        Профиль
                    </Link>
                </div>
            </div>

            <div v-if="props.curators.length === 0" class="py-20 text-center">
                <i class="pi pi-search text-5xl text-slate-200 mb-4"></i>
                <p class="text-slate-500 font-bold">Кураторы не найдены</p>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
</style>

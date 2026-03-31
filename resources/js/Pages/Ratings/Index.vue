<script setup>
import { ref, computed, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    groups: Array,
    students: Array,
    selectedGroupId: Number,
    currentPeriod: String
});

const localStudents = ref([...props.students]);

watch(() => props.students, (newStudents) => {
    localStudents.value = [...newStudents];
}, { deep: true });

const selectGroup = (id) => {
    router.get(route('ratings.index'), { group_id: id, period: props.currentPeriod }, {
        preserveState: true,
        preserveScroll: true
    });
};

const saveRatings = () => {
    router.post(route('ratings.store'), {
        group_id: props.selectedGroupId,
        period: props.currentPeriod,
        students: localStudents.value
    });
};

const activeGroup = computed(() => props.groups.find(g => g.id === props.selectedGroupId));
</script>

<template>
    <Head title="Рейтинги групп" />

    <AuthenticatedLayout>
        <template #header>Рейтинги</template>

        <div class="max-w-[1500px] mx-auto flex flex-col lg:flex-row gap-8 animate-slide-up h-[calc(100vh-12rem)] overflow-hidden">

            <div class="w-full lg:w-80 flex flex-col gap-3 overflow-y-auto pr-4 custom-scrollbar shrink-0">
                <button v-for="group in props.groups" :key="group.id" @click="selectGroup(group.id)"
                        :class="['p-5 rounded-[2rem] border transition-all duration-300 text-left relative group',
                    props.selectedGroupId === group.id
                    ? 'bg-indigo-600 border-indigo-500 shadow-xl shadow-indigo-600/20 text-white translate-x-2'
                    : 'bg-white/40 dark:bg-[#111827]/40 border-white/50 dark:border-slate-800/80 hover:bg-white/60 dark:hover:bg-slate-800/60']">

                    <h4 class="font-black text-lg tracking-tight">
                        {{ group.name.startsWith('Группа') ? group.name : 'Группа ' + group.name }}
                    </h4>
                    <p :class="['text-[10px] font-bold uppercase tracking-widest mt-1', props.selectedGroupId === group.id ? 'text-indigo-100' : 'text-slate-500']">
                        {{ group.department || 'Факультет' }}
                    </p>
                    <div class="flex items-center justify-between mt-4">
                        <span class="text-[10px] font-black uppercase opacity-60">{{ group.students_count }} чел.</span>
                        <span :class="['text-sm font-black px-3 py-1 rounded-xl', props.selectedGroupId === group.id ? 'bg-white/20' : 'bg-indigo-500/10 text-indigo-500']">
                            {{ parseFloat(group.average_score || 0).toFixed(1) }}
                        </span>
                    </div>
                </button>
            </div>

            <div v-if="activeGroup" class="flex-1 bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[3rem] flex flex-col overflow-hidden shadow-2xl">

                <div class="p-8 md:p-10 border-b border-slate-100 dark:border-slate-800/50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gradient-to-r from-transparent to-indigo-500/5">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                                {{ activeGroup.name.startsWith('Группа') ? activeGroup.name : 'Группа ' + activeGroup.name }}
                            </h3>
                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest">Период открыт</span>
                        </div>
                    </div>

                    <button @click="saveRatings" class="px-10 h-11 rounded-2xl bg-indigo-600 text-white font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-600/30 hover:bg-indigo-500 transition-all active:scale-95">
                        Сохранить всё
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <div class="min-w-[800px]">
                        <div class="grid grid-cols-[1fr_140px_140px_140px_100px] gap-4 px-10 py-6 border-b border-slate-100 dark:border-slate-800/30">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Студент</div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Посещаемость %</div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Активность</div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Дисциплина</div>
                            <div class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] text-right">Итог</div>
                        </div>

                        <div v-for="student in localStudents" :key="student.id"
                             class="grid grid-cols-[1fr_140px_140px_140px_100px] gap-4 px-10 py-5 items-center hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors border-b border-slate-50 dark:border-slate-800/20">

                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-black text-slate-400 text-[10px] uppercase">
                                    {{ student.name.split(' ').map(n => n[0]).join('') }}
                                </div>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ student.name }}</span>
                            </div>

                            <div class="flex justify-center">
                                <input type="number" v-model="student.attendance" class="w-20 h-10 rounded-xl bg-slate-50 dark:bg-slate-900 border-none text-center text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/50">
                            </div>
                            <div class="flex justify-center">
                                <input type="number" v-model="student.activity" class="w-20 h-10 rounded-xl bg-slate-50 dark:bg-slate-900 border-none text-center text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/50">
                            </div>
                            <div class="flex justify-center">
                                <input type="number" v-model="student.discipline" class="w-20 h-10 rounded-xl bg-slate-50 dark:bg-slate-900 border-none text-center text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/50">
                            </div>

                            <div class="text-right font-black text-indigo-600 dark:text-indigo-400 text-lg tracking-tighter">
                                {{ ((student.attendance / 10 + student.activity + student.discipline) / 3).toFixed(1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="flex-1 flex flex-col items-center justify-center bg-white/40 dark:bg-[#111827]/40 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
                <i class="pi pi-users text-6xl text-slate-200 dark:text-slate-800 mb-6"></i>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-sm">Выберите группу из списка слева</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.6s ease-out forwards; }

.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #6366f122; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #6366f144; }

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Rating from 'primevue/rating';

const props = defineProps({
    curator: Object,
    recentRatings: Array,
    backUrl: String
});

const startChat = () => {
    router.post(route('messages.start', props.curator.id));
};
</script>

<template>
    <Head :title="`Профиль: ${props.curator.name}`" />

    <AuthenticatedLayout>
        <template #header>Профиль куратора</template>

        <div class="max-w-[1300px] mx-auto space-y-8 animate-slide-up pb-10">

            <Link :href="props.backUrl" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-indigo-500 transition-colors">
                <i class="pi pi-arrow-left text-xs"></i> Назад
            </Link>

            <div class="grid lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] p-8 shadow-xl flex flex-col items-center text-center">
                        <img :src="`https://ui-avatars.com/api/?name=${props.curator.name}&background=6366f1&color=fff&size=150`" class="w-32 h-32 rounded-[2rem] object-cover shadow-xl mb-6 ring-4 ring-white dark:ring-slate-800">
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-1">{{ props.curator.name }}</h2>
                        <p class="text-xs font-bold text-indigo-500 uppercase tracking-widest mb-6">{{ props.curator.department || 'Без кафедры' }}</p>

                        <div class="w-full flex justify-between gap-4">
                            <div class="flex-1 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Групп</p>
                                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ props.curator.groups_count }}</p>
                            </div>
                            <div class="flex-1 bg-indigo-50 dark:bg-indigo-500/5 p-4 rounded-2xl">
                                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Рейтинг</p>
                                <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">
                                    {{ props.curator.groups_avg_average_score ? parseFloat(props.curator.groups_avg_average_score).toFixed(1) : '0.0' }}
                                </p>
                            </div>
                        </div>

                        <button @click="startChat" class="mt-6 w-full py-3.5 px-4 bg-indigo-600 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-500/30 active:scale-95">
                            <i class="pi pi-envelope"></i> Написать сообщение
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] p-8 shadow-xl">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight mb-8">Закрепленные группы</h3>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div v-if="props.curator.groups.length === 0" class="text-sm font-medium text-slate-500 dark:text-slate-400">Нет групп</div>
                            <div v-for="group in props.curator.groups" :key="group.id" class="p-5 rounded-2xl bg-slate-50/50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/50 flex items-center justify-between hover:border-indigo-500/30 transition-colors">
                                <div>
                                    <h4 class="text-base font-bold text-slate-900 dark:text-white">{{ group.name }}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Студентов: {{ group.students.length }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-black text-indigo-600 dark:text-indigo-400">{{ parseFloat(group.average_score || 0).toFixed(1) }}</p>
                                    <Rating :modelValue="5" readonly :cancel="false" class="!text-yellow-400 scale-75 origin-right" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] p-8 shadow-xl">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight mb-6">Последние оценки групп</h3>

                        <div class="space-y-4">
                            <div v-if="props.recentRatings.length === 0" class="text-sm font-medium text-slate-500 dark:text-slate-400">Нет оценок</div>
                            <div v-for="rating in props.recentRatings" :key="rating.id" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/50 hover:border-indigo-500/30 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-yellow-500/10 text-yellow-500">
                                        <i class="pi pi-star-fill text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ rating.group_name }}</h4>
                                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">Оценка: <span class="font-bold text-indigo-400">{{ rating.score }}</span></p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-300">
                                    {{ rating.date }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
</style>

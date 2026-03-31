<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    events: {
        type: Array,
        default: () => []
    }
});

const viewDate = ref(new Date());
const selectedDate = ref(new Date());
const isModalOpen = ref(false);

const form = useForm({
    title: '',
    date: '',
    color: 'bg-indigo-500'
});

const monthNames = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];

const getLocalDateString = (date) => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

const isSelected = (date) => {
    return date.toDateString() === selectedDate.value.toDateString();
};

const calendarGrid = computed(() => {
    const year = viewDate.value.getFullYear();
    const month = viewDate.value.getMonth();

    const firstDayOfMonth = new Date(year, month, 1).getDay();
    const padding = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1;
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const days = [];
    for (let i = 1; i <= daysInMonth; i++) {
        const fullDate = new Date(year, month, i);
        const dateStr = getLocalDateString(fullDate);
        days.push({
            day: i,
            date: fullDate,
            dateString: dateStr,
            events: props.events?.filter(e => e.start_date?.split(' ')[0] === dateStr) || []
        });
    }

    return { padding, days };
});

const eventsForSelectedDay = computed(() => {
    const dateStr = getLocalDateString(selectedDate.value);
    return props.events?.filter(e => e.start_date?.split(' ')[0] === dateStr) || [];
});

const changeMonth = (offset) => {
    viewDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() + offset, 1);
};

const selectDay = (date) => {
    selectedDate.value = date;
};

const formatDate = (date) => {
    return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', weekday: 'short' });
};

const openModal = () => {
    form.date = getLocalDateString(selectedDate.value);
    isModalOpen.value = true;
};

const submit = () => {
    form.post(route('events.store'), {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Календарь" />

    <AuthenticatedLayout>
        <template #header>Календарь</template>

        <div class="max-w-[1550px] mx-auto flex flex-col lg:flex-row gap-8 animate-slide-up h-[calc(100vh-12rem)]">

            <div class="flex-1 bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] p-8 shadow-2xl flex flex-col overflow-hidden">
                <div class="flex items-center justify-between mb-10 shrink-0">
                    <div class="flex items-center gap-4">
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ monthNames[viewDate.getMonth()] }} {{ viewDate.getFullYear() }}
                        </h3>
                        <div class="flex gap-2">
                            <button @click="changeMonth(-1)" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-indigo-600 hover:text-white transition-all">
                                <i class="pi pi-chevron-left text-xs"></i>
                            </button>
                            <button @click="changeMonth(1)" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-indigo-600 hover:text-white transition-all">
                                <i class="pi pi-chevron-right text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <button @click="openModal" class="px-6 h-11 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs uppercase tracking-widest shadow-lg shadow-indigo-600/20 transition-all active:scale-95">
                        Добавить событие
                    </button>
                </div>

                <div class="grid grid-cols-7 gap-4 mb-4 shrink-0">
                    <div v-for="d in ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']" :key="d" class="text-center text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">
                        {{ d }}
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-3 overflow-y-auto custom-scrollbar pr-2 content-start">
                    <div v-for="item in calendarGrid.days" :key="item.day"
                         @click="selectDay(item.date)"
                         :class="['aspect-square rounded-[1.5rem] border p-4 transition-all cursor-pointer relative flex flex-col justify-between group',
                         isSelected(item.date) ? 'bg-indigo-600 border-indigo-500 shadow-xl shadow-indigo-600/20' : 'bg-slate-50/50 dark:bg-slate-900/30 border-transparent hover:border-indigo-500/50']">

                         <span :class="['text-sm font-black', isSelected(item.date) ? 'text-white' : 'text-slate-900 dark:text-slate-400 group-hover:text-indigo-500']">
                             {{ item.day }}
                         </span>

                        <div class="flex justify-center gap-1.5 flex-wrap">
                            <div v-for="event in item.events" :key="event.id"
                                 :class="['w-2 h-2 rounded-full shadow-sm ring-2 ring-white dark:ring-slate-900', event.color]">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-[400px] flex flex-col shrink-0">
                <div class="bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] p-8 shadow-2xl flex-1 flex flex-col overflow-hidden">
                    <div class="mb-8 shrink-0">
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-1">Планы на день</p>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight capitalize">{{ formatDate(selectedDate) }}</h3>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar space-y-4 pr-2">
                        <div v-if="eventsForSelectedDay.length === 0" class="py-20 text-center flex flex-col items-center opacity-20">
                            <i class="pi pi-calendar-minus text-5xl mb-4"></i>
                            <p class="text-xs font-black uppercase tracking-widest">Событий нет</p>
                        </div>

                        <div v-for="event in eventsForSelectedDay" :key="event.id" class="p-5 rounded-3xl bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800/50 flex items-center gap-4 group hover:border-indigo-500/30 transition-all">
                            <div :class="['w-1.5 h-10 rounded-full shrink-0', event.color]"></div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-500 transition-colors">{{ event.title }}</h4>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Мероприятие</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0   flex items-start justify-center p-4 bg-slate-950/60 backdrop-blur-md pt-24">
            <div class="w-full mt-40 max-w-md bg-white dark:bg-[#111827] rounded-[3rem] p-10 shadow-2xl border border-white/10 dark:border-slate-800/50 animate-slide-up">
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-8 tracking-tight">Новое событие</h3>
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Название</label>
                        <input v-model="form.title" type="text" class="w-full h-14 px-6 rounded-2xl bg-slate-50 dark:bg-slate-900 border-none focus:ring-1 focus:ring-indigo-500/50 text-sm font-bold" placeholder="Что добавим?">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Категория</label>
                        <select v-model="form.color" class="w-full h-14 px-6 rounded-2xl bg-slate-50 dark:bg-slate-900 border-none focus:ring-1 focus:ring-indigo-500/50 text-sm font-bold">
                            <option value="bg-indigo-500">Собрание</option>
                            <option value="bg-rose-500">Дедлайн</option>
                            <option value="bg-emerald-500">Мероприятие</option>
                        </select>
                    </div>
                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="isModalOpen = false" class="flex-1 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 font-bold text-sm">Отмена</button>
                        <button type="submit" class="flex-1 h-14 rounded-2xl bg-indigo-600 text-white font-bold text-sm shadow-xl shadow-indigo-600/20">Создать</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #6366f133; border-radius: 10px; }
</style>

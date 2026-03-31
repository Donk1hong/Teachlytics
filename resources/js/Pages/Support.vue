<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    tickets: Array
});

const page = usePage();
const authId = computed(() => page.props.auth.user.id);

const categories = [
    { title: 'Общие вопросы', icon: 'pi-info-circle', desc: 'Как работает система и расчет баллов.' },
    { title: 'Аккаунт и доступ', icon: 'pi-user', desc: 'Проблемы со входом или сменой данных.' },
    { title: 'Тех. ошибки', icon: 'pi-exclamation-triangle', desc: 'Если что-то не нажимается или не отображается.' },
    { title: 'Предложения', icon: 'pi-lightbulb', desc: 'Идеи по улучшению функционала системы.' },
];

const searchQuery = ref('');
const isModalOpen = ref(false);

const filteredTickets = computed(() => {
    return props.tickets.filter(t =>
        t.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        t.message.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const form = useForm({
    subject: '',
    message: ''
});

const submitTicket = () => {
    form.post(route('support.store'), {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        }
    });
};

const deleteTicket = (id) => {
    router.delete(route('support.destroy', id), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Поддержка" />
    <AuthenticatedLayout>
        <template #header>Поддержка</template>

        <div class="max-w-[1200px] mx-auto space-y-10 animate-slide-up pb-20">
            <div class="text-center py-10">
                <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter mb-4">Чем мы можем помочь?</h2>
                <div class="max-w-2xl mx-auto relative">
                    <i class="pi pi-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input v-model="searchQuery" type="text" placeholder="Поиск по базе знаний..."
                           class="w-full h-14 pl-14 pr-6 rounded-2xl bg-white/60 dark:bg-[#111827]/60 backdrop-blur-xl border border-white/50 dark:border-slate-800/80 shadow-xl focus:ring-1 focus:ring-indigo-500/50 outline-none transition-all dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="cat in categories" :key="cat.title" class="p-8 bg-white/40 dark:bg-[#111827]/40 backdrop-blur-xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] hover:-translate-y-2 transition-all cursor-pointer group text-center lg:text-left">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center mb-6 mx-auto lg:mx-0 group-hover:bg-indigo-500 group-hover:text-white transition-all text-indigo-500 shadow-inner">
                        <i :class="['pi text-xl', cat.icon]"></i>
                    </div>
                    <h4 class="font-black text-slate-900 dark:text-white mb-2 tracking-tight">{{ cat.title }}</h4>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 leading-relaxed">{{ cat.desc }}</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-4">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-6 uppercase tracking-wider">Обращения</h3>
                    <div v-if="filteredTickets.length === 0" class="p-10 text-center bg-white/40 dark:bg-[#111827]/40 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700">
                        <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Ничего не найдено</p>
                    </div>
                    <div v-for="item in filteredTickets" :key="item.id" class="p-6 bg-white/60 dark:bg-[#111827]/60 backdrop-blur-xl border border-white/50 dark:border-slate-800/80 rounded-2xl relative group">
                        <div class="flex justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <img :src="`https://ui-avatars.com/api/?name=${item.user.name}&background=6366f1&color=fff`"
                                     class="w-8 h-8 rounded-lg object-cover border border-white/10"
                                     alt="Avatar">
                                <div class="flex flex-col mr-4">
                                    <span class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-wider leading-none">
                                        {{ item.user.name }}
                                    </span>
                                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter mt-0.5">
                                            {{ item.created_at_human }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span :class="['text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-lg shadow-sm',
                                    item.answer ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500']">
                                    {{ item.answer ? 'Решено' : 'В обработке' }}
                                </span>
                                <button v-if="item.user_id === authId" @click="deleteTicket(item.id)" class="text-slate-400 hover:text-rose-500 transition-colors">
                                    <i class="pi pi-trash text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div class="ml-1">
                            <h5 class="font-bold text-slate-900 dark:text-white mb-2 leading-tight">{{ item.subject }}</h5>
                            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ item.message }}</p>
                        </div>

                        <div v-if="item.answer" class="mt-6 p-5 bg-indigo-500/5 dark:bg-indigo-500/10 border border-indigo-500/20 rounded-xl animate-scale-in">
                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2">Ответ администратора:</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 italic">{{ item.answer }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-500/20 relative overflow-hidden h-fit">
                    <div class="relative z-10">
                        <h3 class="text-xl font-black mb-2">Не нашли ответ?</h3>
                        <p class="text-sm text-indigo-100 mb-8 font-medium leading-relaxed">Создайте тикет, и наша команда ответит вам в ближайшее время.</p>
                        <button @click="isModalOpen = true" class="w-full py-4 bg-white text-indigo-600 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-indigo-100 transition-all shadow-lg active:scale-95">
                            Написать нам
                        </button>
                    </div>
                    <i class="pi pi-envelope absolute -bottom-10 -right-10 text-[12rem] text-white/10 rotate-12"></i>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-[450px] bg-[#111827] rounded-[2.5rem] p-10 shadow-2xl border border-white/5 relative">
                <button @click="isModalOpen = false" class="absolute top-8 right-8 text-slate-500 hover:text-white transition-colors">
                    <i class="pi pi-times text-xs"></i>
                </button>

                <div class="mb-8">
                    <h3 class="text-xl font-black text-white tracking-tight">Новое обращение</h3>
                </div>

                <form @submit.prevent="submitTicket" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[9px] font-black uppercase tracking-[0.15em] text-slate-500 ml-1">Тема сообщения</label>
                        <input v-model="form.subject" type="text" placeholder="Что добавим?" required
                               class="w-full h-12 px-5 rounded-xl dark:bg-slate-900 border-none focus:ring-1 focus:ring-indigo-500/50 text-xs font-bold text-white placeholder-slate-600 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[9px] font-black uppercase tracking-[0.15em] text-slate-500 ml-1">Описание проблемы</label>
                        <textarea v-model="form.message" rows="4" placeholder="Опишите ситуацию подробнее..." required
                                  class="w-full p-5 rounded-2xl dark:bg-slate-900 border-none focus:ring-1 focus:ring-indigo-500/50 text-xs font-bold text-white placeholder-slate-600 resize-none transition-all"></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="isModalOpen = false"
                                class="flex-1 h-12 bg-[#242c42] text-slate-300 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-[#2d3652] transition-all">
                            Отмена
                        </button>
                        <button type="submit" :disabled="form.processing"
                                class="flex-1 h-12 bg-[#5850ec] text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-[#4f46e5] transition-all shadow-lg shadow-indigo-600/20 disabled:opacity-50">
                            {{ form.processing ? '...' : 'Создать' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </transition>
</template>

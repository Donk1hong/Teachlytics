<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const page = usePage();

const isDark = ref(false);
const showingNotifications = ref(false);

const notifications = computed(() => page.props.auth.notifications || []);
const hasUnread = computed(() => notifications.value.length > 0);

const getNotifStyle = (type) => {
    const styles = {
        rating: { icon: 'pi-chart-bar', color: 'text-indigo-500', bg: 'bg-indigo-500/10' },
        message: { icon: 'pi-envelope', color: 'text-violet-500', bg: 'bg-violet-500/10' },
        deadline: { icon: 'pi-clock', color: 'text-rose-500', bg: 'bg-rose-500/10' },
    };
    return styles[type] || styles.rating; // По умолчанию - рейтинг
};

const navClass = (routeName) => {
    const isActive = route().current(routeName) || (routeName === 'settings' && route().current('profile.*'));
    const base = "w-12 h-12 mx-auto rounded-[1rem] flex items-center justify-center transition-all duration-300 hover:scale-110";
    const active = "bg-white dark:bg-indigo-500/15 border border-slate-200/60 dark:border-indigo-500/40 text-indigo-600 dark:text-indigo-400 shadow-sm";
    const inactive = "text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white";
    return `${base} ${isActive ? active : inactive}`;
};

const clearNotifications = () => {
    router.post(route('notifications.clear'), {}, {
        onSuccess: () => showingNotifications.value = false,
        preserveScroll: true
    });
};

const markAsRead = (id) => {
    router.post(route('notifications.read', id), {}, {
        preserveScroll: true
    });
};

const applyTheme = (dark) => {
    const html = document.documentElement;
    if (dark) {
        html.classList.add('dark', 'p-dark');
        localStorage.setItem('theme', 'dark');
    } else {
        html.classList.remove('dark', 'p-dark');
        localStorage.setItem('theme', 'light');
    }
};

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    applyTheme(isDark.value);
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    isDark.value = savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches);
    applyTheme(isDark.value);
});
</script>

<template>
    <div class="flex h-screen bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 font-sans antialiased transition-colors duration-500 overflow-hidden relative">

        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute top-[-10%] right-[-5%] w-[40rem] h-[40rem] bg-indigo-500/10 dark:bg-indigo-600/10 rounded-full blur-[120px] animate-blob"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[30rem] h-[30rem] bg-violet-500/10 dark:bg-violet-600/10 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>
        </div>

        <aside class="relative z-30 w-[88px] h-[calc(100vh-3rem)] my-6 ml-6 bg-white/60 dark:bg-[#111827]/80 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] flex flex-col items-center py-8 shadow-2xl shadow-indigo-900/10 dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] shrink-0 transition-all">
            <Link :href="route('dashboard')" class="mb-8 group">
                <div class="w-12 h-12 rounded-[1.15rem] bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:shadow-indigo-500/50 group-hover:scale-105 transition-all duration-300 border border-white/10">
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2.5L2.5 7.5L12 12.5L21.5 7.5L12 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.5 12.5L12 17.5L21.5 12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.5 17.5L12 22.5L21.5 17.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </Link>

            <nav class="flex flex-col gap-3 w-full px-4">
                <Link :href="route('dashboard')" :class="navClass('dashboard')" title="Дашборд">
                    <i class="pi pi-home text-[1.35rem]"></i>
                </Link>
                <Link :href="route('ratings.index')" :class="navClass('ratings.*')" title="Рейтинги групп">
                    <i class="pi pi-chart-line text-[1.35rem]"></i>
                </Link>
                <Link :href="route('curators')" :class="navClass('curators*')" title="Кураторы">
                    <i class="pi pi-users text-[1.35rem]"></i>
                </Link>
                <Link :href="route('messages.index')" :class="navClass('messages')" title="Сообщения">
                    <i class="pi pi-comments text-[1.35rem]"></i>
                </Link>
                <Link :href="route('calendar')" :class="navClass('calendar')" title="Календарь">
                    <i class="pi pi-calendar text-[1.35rem]"></i>
                </Link>
            </nav>

            <div class="mt-auto flex flex-col items-center gap-2 w-full px-4 mb-2">
                <Link :href="route('support')" :class="navClass('support')" title="Поддержка">
                    <i class="pi pi-question-circle text-[1.35rem]"></i>
                </Link>
            </div>
        </aside>

        <div class="flex-1 flex flex-col relative z-10 overflow-hidden">
            <header class="h-24 flex items-center justify-between px-10 relative z-20">
                <div class="w-48"></div> <div class="absolute left-1/2 -translate-x-1/2 text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase">
                <slot name="header" />
            </div>

                <div class="flex items-center gap-5">
                    <button @click="toggleDarkMode" class="w-11 h-11 flex items-center justify-center rounded-xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-md border border-slate-200 dark:border-slate-700/50 text-slate-600 dark:text-slate-300 hover:scale-105 transition-all shadow-sm">
                        <i :class="isDark ? 'pi pi-sun' : 'pi pi-moon'" class="text-lg"></i>
                    </button>

                    <div class="relative">
                        <button @click="showingNotifications = !showingNotifications" class="relative w-11 h-11 flex items-center justify-center rounded-xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-md border border-slate-200 dark:border-slate-700/50 text-slate-500 dark:text-slate-400 hover:text-indigo-500 transition-all shadow-sm">
                            <i class="pi pi-bell text-lg"></i>
                            <span v-if="hasUnread" class="absolute top-3 right-3 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-[#0B0F19]"></span>
                        </button>

                        <div v-if="showingNotifications" @click="showingNotifications = false" class="fixed inset-0 z-40"></div>

                        <transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 scale-95 -translate-y-2"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition ease-in duration-150"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 -translate-y-2"
                        >
                            <div v-show="showingNotifications" class="absolute top-full right-0 mt-4 w-96 rounded-[2rem] bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 shadow-2xl z-50 overflow-hidden">

                                <div class="p-6 border-b border-slate-100 dark:border-slate-800/50 flex justify-between items-center">
                                    <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Уведомления</h4>
                                    <button v-if="hasUnread" @click="clearNotifications" class="text-[10px] font-bold text-indigo-500 hover:text-indigo-600 uppercase">Очистить всё</button>
                                </div>

                                <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
                                    <div v-if="notifications.length === 0" class="p-10 text-center">
                                        <i class="pi pi-bell-slash text-slate-300 dark:text-slate-600 text-3xl mb-3"></i>
                                        <p class="text-xs font-bold text-slate-400 uppercase">Уведомлений нет</p>
                                    </div>

                                    <div v-for="notif in notifications" :key="notif.id"
                                         @click="markAsRead(notif.id)"
                                         class="p-5 flex gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer border-b border-slate-50 dark:border-slate-800/30 last:border-0 relative">

                                        <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm', getNotifStyle(notif.type).bg]">
                                            <i :class="['pi text-sm', getNotifStyle(notif.type).icon, getNotifStyle(notif.type).color]"></i>
                                        </div>

                                        <div class="overflow-hidden text-left flex-1 pr-16">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate uppercase tracking-wide">{{ notif.title }}</p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">{{ notif.desc }}</p>
                                        </div>
                                        <span class="absolute top-5 right-5 text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ notif.created_at_human }}</span>
                                    </div>
                                </div>

                                <div class="p-4 bg-slate-50/50 dark:bg-slate-900/30 border-t border-slate-100 dark:border-slate-800/50 text-center">
                                    <button @click="showingNotifications = false" class="w-full py-3.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-xs font-black text-slate-500 dark:text-slate-300 transition-colors uppercase tracking-widest active:scale-95">Закрыть</button>
                                </div>
                            </div>
                        </transition>
                    </div>

                    <Link :href="route('profile.edit')" class="flex items-center gap-3 p-1 rounded-full transition-all hover:bg-white dark:hover:bg-slate-800">
                        <img :src="page.props.auth.user.avatar || 'https://ui-avatars.com/api/?name=' + page.props.auth.user.name + '&background=6366f1&color=fff'" class="w-11 h-11 rounded-full object-cover shadow-sm border border-white/20" />
                    </Link>

                    <Link :href="route('logout')" method="post" as="button" class="w-11 h-11 flex items-center justify-center rounded-xl text-rose-500 hover:bg-rose-500/10 transition-all" title="Выйти">
                        <i class="pi pi-sign-out text-lg"></i>
                    </Link>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto px-10 pt-6 pb-10">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}
.animate-blob { animation: blob 10s infinite ease-in-out; }
.animation-delay-2000 { animation-delay: 2s; }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #6366f133; border-radius: 10px; }
</style>

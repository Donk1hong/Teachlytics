<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});

const isDark = ref(false);

const applyTheme = (dark) => {
    if (dark) {
        document.documentElement.classList.add('dark', 'p-dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark', 'p-dark');
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

const abstractGroups = [
    { name: 'ДИЗ-201', score: 95, color: 'bg-indigo-600', text: 'text-white' },
    { name: 'ИС-401', score: 92, color: 'bg-white dark:bg-slate-800', text: 'text-slate-900 dark:text-slate-100' },
    { name: 'ПИ-302', score: 88, color: 'bg-white dark:bg-slate-800', text: 'text-slate-900 dark:text-slate-100' },
];
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 selection:bg-indigo-100 relative overflow-x-hidden font-sans antialiased transition-colors duration-300">

        <div class="absolute inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-10%] right-[-5%] w-[30rem] h-[30rem] bg-indigo-200/40 dark:bg-indigo-900/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[25rem] h-[25rem] bg-violet-200/30 dark:bg-violet-900/10 rounded-full blur-[80px]"></div>
        </div>

        <nav v-if="canLogin" class="relative z-20 max-w-7xl mx-auto px-6 py-6 md:py-8 flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center gap-3 group">
                <span class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Рейтинг <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">кураторов</span>
                </span>
            </div>

            <div class="flex items-center gap-4">
                <Button
                    :icon="isDark ? 'pi pi-sun' : 'pi pi-moon'"
                    severity="secondary"
                    rounded
                    text
                    @click="toggleDarkMode"
                    class="!text-slate-600 dark:!text-slate-400"
                />

                <template v-if="$page.props.auth.user">
                    <Link :href="route('dashboard')">
                        <Button rounded severity="indigo" class="px-5 font-bold">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-user"></i>
                            <span>Кабинет</span>
                        </div>
                    </Button>
                    </Link>
                </template>

                <template v-else>
                    <div class="flex items-center gap-1 p-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-full shadow-sm">
                        <Link :href="route('login')">
                            <Button label="Войти" variant="text" severity="secondary" rounded class="!text-slate-700 dark:!text-slate-300 !font-bold !px-5 !text-sm" />
                        </Link>
                    </div>
                </template>
            </div>
        </nav>

        <main class="relative z-10 max-w-7xl mx-auto px-6 pt-10 pb-20 md:pt-20 md:pb-32">
            <div class="grid lg:grid-cols-2 gap-16 items-center">

                <div class="flex flex-col items-start space-y-8 text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-[10px] font-black tracking-[0.2em] uppercase text-indigo-600 dark:text-indigo-400 shadow-sm animate-fade-in">
                        <span class="flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        Курсовая
                    </div>

                    <h1 class="text-5xl md:text-7xl font-black text-slate-950 dark:text-white leading-[1.1] tracking-tighter animate-fade-in">
                        Цифровая <br/>
                        <span class="text-indigo-600">метрика</span> успеха <br/>
                        групп.
                    </h1>

                    <p class="text-lg md:text-xl text-slate-500 dark:text-slate-400 max-w-lg leading-relaxed font-medium animate-fade-in animation-delay-300">
                        Автоматизированный мониторинг достижений и дисциплины. Мгновенная аналитика в одном месте.
                    </p>
                </div>

                <div class="relative animate-slide-up animation-delay-700">
                    <div class="bg-white/40 dark:bg-slate-900/40 backdrop-blur-xl border border-white dark:border-slate-800 rounded-[2.5rem] p-8 shadow-2xl shadow-indigo-100/50 dark:shadow-none relative overflow-hidden">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="font-bold text-slate-900 dark:text-white">Текущий лидерборд</h3>
                        </div>

                        <div class="space-y-4">
                            <div v-for="(group, index) in abstractGroups" :key="index"
                                 class="p-5 rounded-2xl flex items-center justify-between border border-slate-100 dark:border-slate-800 transition-transform hover:scale-[1.02]"
                                 :class="group.color">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold"
                                         :class="index === 0 ? 'bg-white/20 text-white' : 'bg-slate-50 dark:bg-slate-700 text-slate-400 dark:text-slate-300'">
                                        {{ index + 1 }}
                                    </div>
                                    <span class="font-bold" :class="group.text">{{ group.name }}</span>
                                </div>
                                <div class="font-black text-xl" :class="group.text">{{ group.score }}</div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-sm font-bold text-slate-400 w-80">
                            <span>Все группы и баллы в этом блоке являются демонстрационными</span>
                        </div>
                    </div>

                    <div class="absolute -bottom-6 -right-6 md:-right-12 p-6 bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700 hidden md:block animate-float">
                        <div class="flex items-center gap-4 text-slate-900 dark:text-white">
                            <div>
                                <div class="text-xs font-black text-slate-400 uppercase tracking-widest">Топ недели</div>
                                <div class="text-lg font-bold">ДИЗ-201</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <section class="max-w-7xl mx-auto px-6 pb-24 grid md:grid-cols-3 gap-6 animate-slide-up animation-delay-1000">
            <div class="md:col-span-2 p-8 md:p-12 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between group hover:border-indigo-200 transition-colors">
                <h3 class="text-2xl md:text-3xl font-bold text-slate-950 dark:text-white max-w-md leading-tight">Объективный расчет балла по всем критериям</h3>
                <p class="mt-4 text-slate-500 dark:text-slate-400 font-medium">Система автоматически учитывает достижения, минимизируя отклонения в расчётах.</p>
            </div>
            <div class="p-8 md:p-12 bg-indigo-600 rounded-[2rem] text-white shadow-xl shadow-indigo-100 dark:shadow-none relative overflow-hidden group">
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div class="text-7xl font-black">100%</div>
                    <p class="text-lg font-bold leading-tight">Прозрачность и открытость всех данных рейтинга для учителей.</p>
                </div>
                <i class="pi pi-chart-line text-[10rem] absolute -bottom-10 -right-10 text-white/10 group-hover:scale-110 transition-transform"></i>
            </div>
        </section>
    </div>
</template>

<style scoped>
@keyframes fadeIn
{
    from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in { animation: fadeIn 0.8s ease-out forwards; opacity: 0; }

@keyframes fadeInDown
{
    from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-down { animation: fadeInDown 0.6s ease-out forwards; }

@keyframes slideUp
{
    from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); }
}

.animate-slide-up { animation: slideUp 0.8s ease-out forwards; opacity: 0; }
@keyframes float
{
    0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); }
}

.animate-float { animation: float 4s infinite ease-in-out; }
.animation-delay-300 { animation-delay: 0.3s; }
.animation-delay-700 { animation-delay: 0.7s; }
.animation-delay-1000 { animation-delay: 1s; }
</style>

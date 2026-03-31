<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

defineProps({
    status: {
        type: String,
    },
});

const isDark = ref(false);

const form = useForm({
    email: '',
});

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

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Восстановление пароля" />

    <div class="min-h-screen bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 relative overflow-hidden font-sans antialiased transition-colors duration-500 flex flex-col">

        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute top-[-10%] right-[-5%] w-[40rem] h-[40rem] bg-indigo-300/30 dark:bg-indigo-600/10 rounded-full blur-[120px] animate-blob"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[30rem] h-[30rem] bg-violet-300/30 dark:bg-violet-600/10 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>
        </div>

        <nav class="relative z-20 max-w-7xl mx-auto w-full px-6 py-8 flex justify-between items-center animate-fade-in-down">
            <Link href="/" class="flex items-center gap-3 group">
                <span class="text-2xl font-black tracking-tighter text-slate-900 dark:text-white">
                    Рейтинг <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">кураторов</span>
                </span>
            </Link>

            <button
                @click="toggleDarkMode"
                class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-md border border-slate-200 dark:border-slate-700/50 text-slate-600 dark:text-slate-300 hover:scale-105 transition-all shadow-sm"
            >
                <i :class="isDark ? 'pi pi-sun' : 'pi pi-moon'" class="text-lg"></i>
            </button>
        </nav>

        <main class="relative z-10 flex-1 flex items-center justify-center px-6 pb-20">
            <div class="w-full max-w-[460px] animate-slide-up">

                <div class="bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-700/50 rounded-[2rem] p-8 sm:p-10 shadow-2xl shadow-indigo-900/5 dark:shadow-none relative">

                    <div class="w-16 h-16 mx-auto bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center mb-6 shadow-inner border border-indigo-100 dark:border-indigo-500/20">
                        <i class="pi pi-unlock text-2xl text-indigo-600 dark:text-indigo-400"></i>
                    </div>

                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Забыли пароль?</h2>
                        <p class="text-slate-500 dark:text-slate-400 mt-3 text-sm font-medium leading-relaxed">
                            Укажите свой email, и мы отправим вам ссылку для создания нового пароля.
                        </p>
                    </div>

                    <div v-if="status" class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-sm font-bold text-green-600 dark:text-green-400 text-center animate-fade-in">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">

                        <div class="space-y-2">
                            <label for="email" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Электронная почта</label>
                            <div class="relative">
                                <i class="pi pi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <InputText
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="w-full !bg-white dark:!bg-[#0B0F19]/50 !border-slate-200 dark:!border-slate-700/50 !rounded-xl !py-3.5 !pl-11 !pr-4 text-slate-900 dark:text-white focus:!ring-2 focus:!ring-indigo-500 focus:!border-indigo-500 transition-all shadow-sm"
                                    placeholder="name@example.com"
                                    required
                                    autofocus
                                />
                            </div>
                            <p v-if="form.errors.email" class="text-red-500 text-xs font-bold ml-1">{{ form.errors.email }}</p>
                        </div>

                        <Button
                            type="submit"
                            class="w-full !py-4 !rounded-xl !bg-indigo-600 hover:!bg-indigo-500 !border-none !text-white transition-all shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 active:scale-[0.98]"
                            :loading="form.processing"
                        >
                            <div class="flex items-center justify-center gap-3">
                                <i class="pi pi-send text-lg"></i>
                                <span class="font-bold text-base">Отправить ссылку</span>
                            </div>
                        </Button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-center">
                        <Link :href="route('login')" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            <i class="pi pi-arrow-left text-xs"></i>
                            Вернуться ко входу
                        </Link>
                    </div>
                </div>

            </div>
        </main>
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

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }

@keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in-down { animation: fadeInDown 0.6s ease-out forwards; }

@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.8s ease-out forwards; }
</style>

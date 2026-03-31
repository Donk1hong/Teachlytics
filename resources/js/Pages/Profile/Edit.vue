<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const activeTab = ref('profile');
const tabs = [
    { id: 'profile', label: 'Профиль', icon: 'pi-user' },
    { id: 'security', label: 'Безопасность', icon: 'pi-lock' },
];
</script>

<template>
    <Head title="Настройки" />

    <AuthenticatedLayout>
        <template #header>Настройки</template>

        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute top-[-10%] right-[-5%] w-[40rem] h-[40rem] bg-indigo-200/40 dark:bg-indigo-900/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[35rem] h-[35rem] bg-violet-200/30 dark:bg-violet-900/10 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s"></div>
        </div>

        <div class="relative z-10 max-w-5xl mx-auto pt-10 pb-10 px-6 animate-slide-up">

            <div class="flex flex-wrap items-center justify-center gap-2 p-1.5 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md rounded-full border border-slate-200/60 dark:border-slate-800 mx-auto mb-16 w-fit shadow-sm">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'px-8 py-3 rounded-full text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 flex items-center gap-3',
                        activeTab === tab.id
                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30 scale-105'
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                    ]"
                >
                    <i :class="['pi text-xs', tab.icon]"></i>
                    {{ tab.label }}
                </button>
            </div>

            <div class="bg-white/40 dark:bg-slate-900/40 backdrop-blur-2xl border border-white dark:border-slate-800 rounded-[3.5rem] p-10 md:p-16 shadow-2xl shadow-indigo-100/50 dark:shadow-none overflow-hidden relative">

                <div class="absolute -top-10 -right-10 opacity-[0.03] dark:opacity-[0.05] pointer-events-none">
                    <i :class="['pi text-[20rem]', tabs.find(t => t.id === activeTab).icon]"></i>
                </div>

                <div class="relative z-10">
                    <div v-if="activeTab === 'profile'" class="animate-fade-in">
                        <div class="mb-7">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-[9px] font-black tracking-[0.2em] uppercase text-indigo-600 dark:text-indigo-400 mb-6">
                                <span class="flex h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                Аккаунт
                            </div>
                            <h2 class="text-4xl md:text-5xl font-black text-slate-950 dark:text-white leading-none tracking-tighter mb-4">
                                Личные <span class="text-indigo-600">данные</span>
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 font-medium max-w-md text-sm">
                                Настройте основную информацию вашего профиля и электронную почту.
                            </p>
                        </div>

                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>

                    <div v-if="activeTab === 'security'" class="space-y-20 animate-fade-in">
                        <section>
                            <div>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-50 dark:bg-violet-500/10 text-[9px] font-black tracking-[0.2em] uppercase text-violet-600 dark:text-violet-400 mb-6">
                                    <span class="flex h-1.5 w-1.5 rounded-full bg-violet-500"></span>
                                    Приватность
                                </div>
                                <h2 class="text-4xl md:text-5xl font-black text-slate-950 dark:text-white leading-none tracking-tighter mb-4">
                                    Пароль и <span class="text-violet-600">защита</span>
                                </h2>
                            </div>
                            <UpdatePasswordForm />
                        </section>

                        <section>
                            <div class="mb-8">
                                <h2 class="text-2xl font-black text-rose-500 tracking-tighter uppercase mb-2">Удаление аккаунта</h2>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Все ваши данные будут безвозвратно стерты без возможности восстановления.
                                </p>
                            </div>
                            <DeleteUserForm />
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
input[type="text"],
input[type="email"],
input[type="password"] {
    @apply w-full h-14 px-8 rounded-2xl bg-white/40 dark:bg-slate-950/40 backdrop-blur-sm border border-slate-200 dark:border-slate-800 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 dark:text-white transition-all shadow-sm !important;
}

label {
    @apply text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] ml-4 mb-3 block !important;
}

section header h2,
section header p {
    @apply hidden !important;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.primary-button {
    @apply h-11 px-10 bg-indigo-600 text-white rounded-full font-black text-[10px] uppercase tracking-[0.25em] hover:bg-indigo-500 hover:scale-105 transition-all active:scale-95 !important;
}

.danger-button {
    @apply h-11 px-10 bg-rose-500 text-white rounded-full font-black text-[10px] uppercase tracking-[0.25em] hover:bg-rose-600 hover:scale-105 transition-all active:scale-95 !important;
}

.secondary-button {
    @apply h-11 px-10 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 rounded-full font-black text-[10px] uppercase tracking-[0.25em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95 !important;
}

.animate-slide-up { animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
</style>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <InputLabel for="name" value="Имя" />
                    <TextInput
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Ваше имя"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <InputLabel for="email" value="Электронная почта" />
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="example@mail.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                    Ваш адрес электронной почты не подтвержден.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="text-indigo-500 hover:text-indigo-600 underline font-bold transition-colors ml-1"
                    >
                        Нажмите здесь, чтобы отправить письмо еще раз.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-2 text-xs font-black text-emerald-500 uppercase tracking-widest">
                    Новая ссылка для подтверждения была отправлена на ваш email.
                </div>
            </div>

            <div class="flex items-center gap-6 pt-4">
                <PrimaryButton :disabled="form.processing" class="primary-button">
                    Сохранить изменения
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0 translate-x-4"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0 translate-x-4"
                >
                    <div v-if="form.recentlySuccessful" class="flex items-center gap-2 text-emerald-500">
                        <i class="pi pi-check-circle"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Сохранено</span>
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>

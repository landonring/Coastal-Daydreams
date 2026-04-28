<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    hasCustomPassword: {
        type: Boolean,
        required: true,
    },
});

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.put('/admin/settings/password', {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin Settings" />

        <div class="mx-auto flex max-w-6xl flex-col gap-12">
            <div class="text-center">
                <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Settings</p>
                <h1 class="mt-4 font-serif text-4xl text-[#111111]">Admin settings</h1>
            </div>

            <div class="mx-auto w-full max-w-2xl">
                <form class="rounded-[1.75rem] bg-white p-6 text-left md:p-8" @submit.prevent="submit">
                    <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Password</p>
                    <h2 class="mt-4 font-serif text-3xl text-[#111111]">Change sign-in password</h2>
                    <p class="mt-4 text-sm leading-7 text-[#6b6b6b]">
                        Update the password used to access the admin. The stored hash in the database overrides the fallback value from the environment.
                    </p>
                    <p class="mt-3 text-[0.72rem] uppercase tracking-[0.28em] text-[#9a9a9a]">
                        {{ hasCustomPassword ? 'Custom password active' : 'Using environment fallback password' }}
                    </p>

                    <div class="mt-8 space-y-6">
                        <div>
                            <label class="mb-3 block text-sm text-[#111111]">Current password</label>
                            <div class="rounded-2xl bg-[#f7f6f3] px-5 py-2">
                                <div class="flex items-center gap-3">
                                    <input
                                        v-model="form.current_password"
                                        :type="showCurrentPassword ? 'text' : 'password'"
                                        class="min-w-0 flex-1 bg-transparent py-2 text-base text-[#111111] outline-none"
                                    >
                                    <button
                                        type="button"
                                        class="shrink-0 text-xs uppercase tracking-[0.22em] text-[#6b6b6b] transition-colors duration-200 hover:text-[#111111]"
                                        @click="showCurrentPassword = !showCurrentPassword"
                                    >
                                        {{ showCurrentPassword ? 'Hide' : 'Show' }}
                                    </button>
                                </div>
                            </div>
                            <p v-if="form.errors.current_password" class="mt-3 text-sm text-[#9c4b4b]">
                                {{ form.errors.current_password }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-3 block text-sm text-[#111111]">New password</label>
                            <div class="rounded-2xl bg-[#f7f6f3] px-5 py-2">
                                <div class="flex items-center gap-3">
                                    <input
                                        v-model="form.password"
                                        :type="showNewPassword ? 'text' : 'password'"
                                        class="min-w-0 flex-1 bg-transparent py-2 text-base text-[#111111] outline-none"
                                    >
                                    <button
                                        type="button"
                                        class="shrink-0 text-xs uppercase tracking-[0.22em] text-[#6b6b6b] transition-colors duration-200 hover:text-[#111111]"
                                        @click="showNewPassword = !showNewPassword"
                                    >
                                        {{ showNewPassword ? 'Hide' : 'Show' }}
                                    </button>
                                </div>
                            </div>
                            <p v-if="form.errors.password" class="mt-3 text-sm text-[#9c4b4b]">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-3 block text-sm text-[#111111]">Confirm new password</label>
                            <div class="rounded-2xl bg-[#f7f6f3] px-5 py-2">
                                <div class="flex items-center gap-3">
                                    <input
                                        v-model="form.password_confirmation"
                                        :type="showPasswordConfirmation ? 'text' : 'password'"
                                        class="min-w-0 flex-1 bg-transparent py-2 text-base text-[#111111] outline-none"
                                    >
                                    <button
                                        type="button"
                                        class="shrink-0 text-xs uppercase tracking-[0.22em] text-[#6b6b6b] transition-colors duration-200 hover:text-[#111111]"
                                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                                    >
                                        {{ showPasswordConfirmation ? 'Hide' : 'Show' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-start">
                        <button
                            type="submit"
                            class="rounded-2xl bg-[#111111] px-6 py-4 text-sm uppercase tracking-[0.18em] text-white transition-opacity duration-200 hover:opacity-90 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            Save Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

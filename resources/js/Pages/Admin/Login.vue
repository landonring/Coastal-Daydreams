<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    password: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post('/admin/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-[#f7f6f3] px-6">
        <Head title="Admin Login" />

        <div class="w-full max-w-md rounded-[2rem] bg-white px-8 py-10 md:px-10">
            <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Admin</p>
            <h1 class="mt-5 font-serif text-4xl leading-tight text-[#111111]">Project access</h1>
            <p class="mt-4 text-sm leading-7 text-[#6b6b6b]">
                Enter the admin password to manage portfolio projects.
            </p>

            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div>
                    <label for="password" class="mb-3 block text-sm text-[#111111]">Password</label>
                    <div class="rounded-2xl bg-[#f7f6f3] px-5 py-2">
                        <div class="flex items-center gap-3">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                class="min-w-0 flex-1 bg-transparent py-2 text-base text-[#111111] outline-none ring-0 placeholder:text-[#9a9a9a]"
                                placeholder="Enter password"
                                autofocus
                            >
                            <button
                                type="button"
                                class="shrink-0 text-xs uppercase tracking-[0.22em] text-[#6b6b6b] transition-colors duration-200 hover:text-[#111111]"
                                @click="showPassword = !showPassword"
                            >
                                {{ showPassword ? 'Hide' : 'Show' }}
                            </button>
                        </div>
                    </div>
                    <p v-if="form.errors.password" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.password }}</p>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-[#111111] px-6 py-4 text-sm tracking-[0.18em] text-white uppercase transition-opacity duration-200 hover:opacity-90 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Sign in
                </button>
            </form>
        </div>
    </div>
</template>

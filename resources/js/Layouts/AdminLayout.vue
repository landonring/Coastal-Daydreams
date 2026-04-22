<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const logoutForm = useForm({});

const flashSuccess = computed(() => page.props.flash?.success);

const logout = () => {
    logoutForm.post('/admin/logout');
};
</script>

<template>
    <div class="min-h-screen bg-[#f7f6f3] text-[#111111]">
        <header class="border-b border-black/5 bg-white/90">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5 md:px-10">
                <div>
                    <p class="font-serif text-2xl">Admin</p>
                    <p class="mt-1 text-[0.72rem] uppercase tracking-[0.3em] text-[#6b6b6b]">Portfolio Manager</p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        href="/admin/dashboard"
                        class="px-4 py-3 text-sm text-[#6b6b6b] transition-colors duration-200 hover:text-[#111111]"
                    >
                        Projects
                    </Link>
                    <Link
                        href="/admin/settings"
                        class="px-4 py-3 text-sm text-[#6b6b6b] transition-colors duration-200 hover:text-[#111111]"
                    >
                        Settings
                    </Link>
                    <button
                        type="button"
                        class="px-4 py-3 text-sm text-[#6b6b6b] transition-colors duration-200 hover:text-[#111111]"
                        @click="logout"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-6 py-8 md:px-10 md:py-10">
            <div
                v-if="flashSuccess"
                class="mb-6 rounded-2xl bg-white px-6 py-4 text-sm text-[#6b6b6b]"
            >
                {{ flashSuccess }}
            </div>

            <slot />
        </main>
    </div>
</template>

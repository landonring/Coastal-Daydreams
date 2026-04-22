<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const isScrolled = ref(false);

const navClasses = computed(() =>
    isScrolled.value
        ? 'border-b border-black/5 bg-white/88 shadow-[0_12px_40px_rgba(17,17,17,0.035)] backdrop-blur-xl'
        : 'bg-transparent',
);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 24;
};

onMounted(() => {
    handleScroll();
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <div class="min-h-screen bg-white text-[#111111]">
        <header class="sticky top-0 z-40 transition-all duration-500 ease-out">
            <div
                :class="[
                    'mx-auto flex max-w-[1600px] items-center justify-between px-6 py-5 transition-all duration-500 ease-out md:px-10 lg:px-16',
                    navClasses,
                ]"
            >
                <Link
                    href="/#top"
                    class="font-serif text-lg tracking-[0.08em] text-[#111111] transition-opacity duration-300 hover:opacity-60"
                >
                    Jennifer Williams
                </Link>

                <nav class="flex items-center gap-6 text-[0.72rem] uppercase tracking-[0.28em] text-[#6b6b6b]">
                    <Link href="/#about" class="transition-colors duration-300 hover:text-[#111111]">About</Link>
                    <Link href="/#projects" class="transition-colors duration-300 hover:text-[#111111]">Projects</Link>
                    <Link href="/#footer" class="transition-colors duration-300 hover:text-[#111111]">Contact</Link>
                </nav>
            </div>
        </header>

        <main class="overflow-hidden">
            <slot />
        </main>
    </div>
</template>

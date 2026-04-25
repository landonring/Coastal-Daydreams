<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const isScrolled = ref(false);
const isOpen = ref(false);
const mobileLinks = [
    { href: '/', label: 'Home' },
    { href: '/#about', label: 'About' },
    { href: '/#projects', label: 'Projects' },
    { href: '/#footer', label: 'Contact' },
];

const navClasses = computed(() =>
    isScrolled.value
        ? 'border-b border-black/5 bg-white/88 shadow-[0_12px_40px_rgba(17,17,17,0.035)] backdrop-blur-xl'
        : 'bg-transparent',
);

const setBodyScrollLock = () => {
    document.body.classList.toggle('overflow-hidden', isOpen.value);
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 24;
};

const closeMenu = () => {
    isOpen.value = false;
};

const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

watch(isOpen, () => {
    setBodyScrollLock();
});

onMounted(() => {
    handleScroll();
    setBodyScrollLock();
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onBeforeUnmount(() => {
    document.body.classList.remove('overflow-hidden');
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
                    @click="closeMenu"
                >
                    Jennifer Williams
                </Link>

                <nav class="hidden items-center gap-6 text-[0.72rem] uppercase tracking-[0.28em] text-[#6b6b6b] md:flex">
                    <Link href="/#about" class="transition-colors duration-300 hover:text-[#111111]">About</Link>
                    <Link href="/#projects" class="transition-colors duration-300 hover:text-[#111111]">Projects</Link>
                    <Link href="/#footer" class="transition-colors duration-300 hover:text-[#111111]">Contact</Link>
                </nav>

                <button
                    type="button"
                    class="text-[0.72rem] uppercase tracking-[0.28em] text-[#6b6b6b] transition-colors duration-300 hover:text-[#111111] md:hidden"
                    :aria-expanded="isOpen"
                    aria-controls="mobile-menu"
                    @click="toggleMenu"
                >
                    {{ isOpen ? 'Close' : 'Menu' }}
                </button>
            </div>
        </header>

        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-3"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-250 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-3"
        >
            <div
                v-if="isOpen"
                id="mobile-menu"
                class="fixed inset-0 z-50 bg-[#f7f6f3] md:hidden"
            >
                <div class="flex h-full flex-col px-6 py-5">
                    <div class="flex items-center justify-between">
                        <Link
                            href="/#top"
                            class="font-serif text-lg tracking-[0.08em] text-[#111111]"
                            @click="closeMenu"
                        >
                            Jennifer Williams
                        </Link>

                        <button
                            type="button"
                            class="text-[0.72rem] uppercase tracking-[0.28em] text-[#6b6b6b] transition-colors duration-300 hover:text-[#111111]"
                            @click="closeMenu"
                        >
                            Close
                        </button>
                    </div>

                    <nav class="flex flex-1 items-center justify-center">
                        <div class="flex flex-col items-center space-y-8 text-center">
                            <Link
                                v-for="link in mobileLinks"
                                :key="link.href"
                                :href="link.href"
                                class="font-serif text-4xl leading-none text-[#111111] transition-opacity duration-300 hover:opacity-60"
                                @click="closeMenu"
                            >
                                {{ link.label }}
                            </Link>
                        </div>
                    </nav>
                </div>
            </div>
        </Transition>

        <main class="overflow-hidden">
            <slot />
        </main>
    </div>
</template>

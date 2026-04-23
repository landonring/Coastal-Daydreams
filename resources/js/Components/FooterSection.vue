<script setup>
import { onBeforeUnmount, ref } from 'vue';

const currentYear = new Date().getFullYear();
const copied = ref(false);
let copiedTimer = null;

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    phone: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
    location: {
        type: String,
        required: true,
    },
    disciplines: {
        type: Array,
        default: () => [],
    },
});

const resetCopiedState = () => {
    if (copiedTimer) {
        window.clearTimeout(copiedTimer);
        copiedTimer = null;
    }
};

const copyEmail = async () => {
    try {
        await navigator.clipboard.writeText(props.email);
        copied.value = true;
        resetCopiedState();
        copiedTimer = window.setTimeout(() => {
            copied.value = false;
        }, 2200);
    } catch {
        copied.value = false;
    }
};

onBeforeUnmount(() => {
    resetCopiedState();
});
</script>

<template>
    <footer id="footer" class="bg-white px-6 pt-32 pb-16 md:px-10 md:pb-20 lg:px-16 lg:pt-40 lg:pb-24">
        <div class="mx-auto max-w-[1500px]">
            <div
                class="rounded-[2rem] border border-black/6 bg-[#f7f6f3]/65 px-8 py-16 md:px-12 lg:px-16 lg:py-20"
                v-reveal
            >
                <div class="mx-auto flex max-w-4xl flex-col items-center text-center">
                    <div class="mb-12 h-px w-24 bg-black/10" />
                    <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Contact</p>
                    <h2 class="mt-6 font-serif text-4xl leading-[1.02] text-[#111111] sm:text-5xl lg:text-[3.6rem]">
                        Interested in my work or photography?
                    </h2>
                    <button
                        type="button"
                        class="mt-10 inline-flex items-center justify-center rounded-full border border-black/10 px-8 py-4 text-sm tracking-[0.28em] text-[#111111] transition-all duration-300 hover:border-black/20 hover:bg-white"
                        :aria-label="copied ? 'Email copied to clipboard' : `Copy ${email} to clipboard`"
                        @click="copyEmail"
                    >
                        {{ copied ? 'Email copied to clipboard' : email }}
                    </button>
                    <p class="mt-4 inline-flex items-center justify-center rounded-full border border-black/10 px-8 py-4 text-sm tracking-[0.28em] text-[#111111]">
                        {{ phone }}
                    </p>
                </div>
            </div>

            <div
                class="mt-10 grid gap-10 border-t border-black/8 pt-8 text-center md:grid-cols-2 md:text-left xl:grid-cols-[1.2fr_0.8fr_0.8fr]"
                v-reveal="100"
            >
                <div>
                    <p class="font-serif text-2xl text-[#111111]">{{ name }}</p>
                    <p class="mt-2 text-[0.72rem] uppercase tracking-[0.28em] text-[#6b6b6b]">
                        {{ location }}
                    </p>
                    <p class="mt-5 max-w-md text-sm leading-7 text-[#6b6b6b]">
                        A portfolio shaped by quiet observation, layered atmosphere, and the shifting light of the Central Coast.
                    </p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3 md:justify-start">
                        <span
                            v-for="discipline in disciplines"
                            :key="discipline"
                            class="rounded-full bg-[#f7f6f3] px-4 py-2 text-[0.68rem] uppercase tracking-[0.26em] text-[#6b6b6b]"
                        >
                            {{ discipline }}
                        </span>
                    </div>
                </div>

                <div>
                    <p class="text-[0.72rem] uppercase tracking-[0.3em] text-[#6b6b6b]">Navigation</p>
                    <div class="mt-5 flex flex-col gap-3 text-sm text-[#111111]">
                        <a href="#top" class="transition-colors duration-300 hover:text-[#6b6b6b]">Home</a>
                        <a href="#about" class="transition-colors duration-300 hover:text-[#6b6b6b]">About</a>
                        <a href="#projects" class="transition-colors duration-300 hover:text-[#6b6b6b]">Projects</a>
                        <a href="#footer" class="transition-colors duration-300 hover:text-[#6b6b6b]">Contact</a>
                    </div>
                </div>

                <div class="flex flex-col items-center gap-3 md:items-start xl:items-end xl:text-right">
                    <p class="text-[0.72rem] uppercase tracking-[0.3em] text-[#6b6b6b]">Studio</p>
                    <a
                        :href="`mailto:${email}`"
                        class="text-sm tracking-[0.28em] text-[#111111] transition-colors duration-300 hover:text-[#6b6b6b]"
                    >
                        {{ email }}
                    </a>
                    <p class="text-sm tracking-[0.28em] text-[#111111]">
                        {{ phone }}
                    </p>
                    <a
                        href="#top"
                        class="mt-3 text-[0.72rem] uppercase tracking-[0.3em] text-[#6b6b6b] transition-colors duration-300 hover:text-[#111111]"
                    >
                        Back to top
                    </a>
                    <p class="text-[0.68rem] uppercase tracking-[0.28em] text-[#9a9a9a]">
                        © {{ currentYear }} {{ name }}
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>

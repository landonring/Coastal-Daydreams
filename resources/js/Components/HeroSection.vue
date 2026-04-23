<script setup>
const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
    discipline: {
        type: String,
        required: true,
    },
    location: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <section id="top" class="hero-ambient relative flex h-screen min-h-[720px] items-center justify-center overflow-visible">
        <div class="hero-gradient-layer hero-gradient-layer-one" />
        <div class="hero-gradient-layer hero-gradient-layer-two" />
        <div class="hero-grain" />
        <div class="hero-radial-light" />
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0.18),rgba(255,255,255,0.34)_50%,rgba(255,255,255,0.58))]" />

        <div class="relative z-10 flex w-full -translate-y-[8vh] justify-center px-6 text-center md:px-10 lg:-translate-y-[10vh]">
            <div class="max-w-4xl pt-4 md:pt-6">
                <p class="text-[0.72rem] uppercase tracking-[0.38em] text-[#6b6b6b]">
                    {{ discipline }}
                </p>
                <h1
                    class="mt-6 max-w-4xl pl-[0.08em] font-serif text-5xl leading-[1.1] tracking-[-0.02em] text-[#111111] antialiased sm:text-6xl lg:text-[5.6rem] xl:text-[6.2rem]"
                >
                    {{ name }}
                </h1>
                <p
                    v-if="subtitle"
                    class="mt-4 font-serif text-[1.5rem] leading-none tracking-[0.08em] text-[#6b6b6b] sm:text-[1.8rem] lg:text-[2.1rem]"
                >
                    {{ subtitle }}
                </p>
                <p class="mt-6 text-base tracking-[0.03em] text-[#6b6b6b] sm:text-lg">
                    {{ location }}
                </p>
            </div>
        </div>

        <a
            href="#about"
            class="absolute bottom-8 left-1/2 z-10 flex -translate-x-1/2 flex-col items-center gap-3 text-[0.65rem] uppercase tracking-[0.35em] text-[#6b6b6b]"
        >
            <span>Scroll</span>
            <span class="h-12 w-px bg-[#111111]/25 [animation:scroll-pulse_1.8s_ease-in-out_infinite]" />
        </a>
    </section>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

import Layout from '../../Layouts/Layout.vue';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    previousProject: {
        type: Object,
        default: null,
    },
    nextProject: {
        type: Object,
        default: null,
    },
});

const currentIndex = ref(0);
const isHovered = ref(false);
const touchStartX = ref(null);
const purchaseCopied = ref(false);
let autoplayTimer = null;
let purchaseCopyTimer = null;
const purchaseEmail = 'coastaldaydreams@yahoo.com';
const purchasePhone = '559-816-1411';

const activeImage = computed(() => props.project.images[currentIndex.value] ?? props.project.hero_image);
const showSoldMetadata = computed(() => props.project.category === 'Art' && props.project.is_sold);
const showPurchaseCta = computed(() => props.project.category === 'Art' && !props.project.is_sold);
const showSoldCta = computed(() => props.project.category === 'Art' && props.project.is_sold);
const showPhotographyCta = computed(() => props.project.category === 'Photography');

const copyPurchaseEmail = async () => {
    try {
        await navigator.clipboard.writeText(purchaseEmail);
        purchaseCopied.value = true;

        if (purchaseCopyTimer) {
            window.clearTimeout(purchaseCopyTimer);
        }

        purchaseCopyTimer = window.setTimeout(() => {
            purchaseCopied.value = false;
        }, 2200);
    } catch {
        purchaseCopied.value = false;
    }
};

const stopAutoplay = () => {
    if (autoplayTimer) {
        window.clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
};

const startAutoplay = () => {
    if (props.project.images.length < 2) {
        return;
    }

    stopAutoplay();
    autoplayTimer = window.setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % props.project.images.length;
    }, 8000);
};

const previous = () => {
    if (!props.project.images.length) {
        return;
    }

    currentIndex.value = (currentIndex.value - 1 + props.project.images.length) % props.project.images.length;
};

const next = () => {
    if (!props.project.images.length) {
        return;
    }

    currentIndex.value = (currentIndex.value + 1) % props.project.images.length;
};

const onTouchStart = (event) => {
    touchStartX.value = event.changedTouches[0]?.clientX ?? null;
};

const onTouchEnd = (event) => {
    const endX = event.changedTouches[0]?.clientX ?? null;

    if (touchStartX.value === null || endX === null) {
        touchStartX.value = null;
        return;
    }

    const delta = endX - touchStartX.value;

    if (Math.abs(delta) > 40) {
        if (delta > 0) {
            previous();
        } else {
            next();
        }
    }

    touchStartX.value = null;
};

watch(
    () => isHovered.value,
    (hovered) => {
        if (hovered) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    },
);

onMounted(() => {
    startAutoplay();
});

onBeforeUnmount(() => {
    stopAutoplay();

    if (purchaseCopyTimer) {
        window.clearTimeout(purchaseCopyTimer);
    }
});
</script>

<template>
    <Layout>
        <Head :title="project.title" />

        <section class="bg-white px-6 pb-28 pt-24 md:px-10 lg:px-16 lg:pb-40 lg:pt-28">
            <div class="mx-auto max-w-[1480px]">
                <div class="mx-auto max-w-6xl" v-reveal>
                    <div
                        class="group relative"
                        @mouseenter="isHovered = true"
                        @mouseleave="isHovered = false"
                    >
                        <button
                            v-if="project.images.length > 1"
                            type="button"
                            aria-label="Previous project image"
                            class="absolute left-0 top-1/2 z-10 flex h-14 w-14 -translate-x-2 -translate-y-1/2 items-center justify-center rounded-full bg-white/88 text-3xl text-[#111111]/45 shadow-[0_18px_40px_rgba(17,17,17,0.08)] backdrop-blur-sm transition-all duration-300 hover:text-[#111111]/80 md:-translate-x-8"
                            :class="isHovered ? 'opacity-100' : 'opacity-0 md:opacity-40'"
                            @click="previous"
                        >
                            ‹
                        </button>

                        <button
                            v-if="project.images.length > 1"
                            type="button"
                            aria-label="Next project image"
                            class="absolute right-0 top-1/2 z-10 flex h-14 w-14 translate-x-2 -translate-y-1/2 items-center justify-center rounded-full bg-white/88 text-3xl text-[#111111]/45 shadow-[0_18px_40px_rgba(17,17,17,0.08)] backdrop-blur-sm transition-all duration-300 hover:text-[#111111]/80 md:translate-x-8"
                            :class="isHovered ? 'opacity-100' : 'opacity-0 md:opacity-40'"
                            @click="next"
                        >
                            ›
                        </button>

                        <div
                            class="overflow-hidden"
                            @touchstart="onTouchStart"
                            @touchend="onTouchEnd"
                        >
                            <Transition
                                mode="out-in"
                                enter-active-class="transition duration-500 ease-in-out"
                                enter-from-class="opacity-0 translate-y-4"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition duration-500 ease-in-out"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-4"
                            >
                                <img
                                    :key="activeImage"
                                    :src="activeImage"
                                    :alt="project.title"
                                    class="mx-auto h-[62vh] max-h-[920px] w-full max-w-[1100px] rounded-[1.55rem] object-contain shadow-[0_28px_90px_rgba(17,17,17,0.08)] transition-transform duration-500 ease-out group-hover:scale-[1.008] md:h-[76vh]"
                                >
                            </Transition>
                        </div>
                    </div>

                    <div
                        v-if="project.images.length > 1"
                        class="mt-8 flex items-center justify-center gap-2.5"
                    >
                        <button
                            v-for="(image, index) in project.images"
                            :key="image"
                            type="button"
                            :aria-label="`Go to project image ${index + 1}`"
                            class="h-2.5 rounded-full transition-all duration-300"
                            :class="currentIndex === index ? 'w-8 bg-[#111111]' : 'w-2.5 bg-black/12 hover:bg-black/25'"
                            @click="currentIndex = index"
                        />
                    </div>
                </div>

                <div class="mx-auto mt-20 max-w-[52rem] text-center" v-reveal="90">
                    <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#8e8e8e]">{{ project.category }}</p>
                    <h1 class="mt-6 font-serif text-[2.8rem] leading-[1] text-[#111111] sm:text-[3.5rem] lg:text-[4.1rem]">
                        {{ project.title }}
                    </h1>

                    <p
                        v-if="project.description"
                        class="mx-auto mt-8 max-w-3xl text-[1rem] leading-8 text-[#6b6b6b]"
                    >
                        {{ project.description }}
                    </p>

                    <p
                        v-if="showSoldMetadata || project.location || project.medium || project.year"
                        class="mt-8 text-[0.76rem] uppercase tracking-[0.28em] text-[#9a9a9a]"
                    >
                        <span v-if="showSoldMetadata">Sold</span>
                        <span v-if="showSoldMetadata && project.location"> · </span>
                        <span v-if="project.location">{{ project.location }}</span>
                        <span v-if="((showSoldMetadata) || project.location) && project.medium"> · </span>
                        <span v-if="project.medium">{{ project.medium }}</span>
                        <span v-if="(showSoldMetadata || project.location || project.medium) && project.year"> · </span>
                        <span v-if="project.year">{{ project.year }}</span>
                    </p>

                    <div
                        v-if="showPurchaseCta"
                        class="mx-auto mt-10 max-w-2xl rounded-[1.5rem] bg-[#f7f6f3] px-6 py-6 text-center"
                    >
                        <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#9a9a9a]">Available Artwork</p>
                        <p class="mt-4 text-sm leading-7 text-[#6b6b6b]">
                            Email
                            <button
                                type="button"
                                class="font-medium text-[#111111] underline decoration-black/20 underline-offset-4 transition-opacity duration-200 hover:opacity-65"
                                @click="copyPurchaseEmail"
                            >
                                {{ purchaseCopied ? 'email copied to clipboard' : purchaseEmail }}
                            </button>
                            or text
                            <a
                                :href="`sms:${purchasePhone.replace(/[^0-9+]/g, '')}`"
                                class="font-medium text-[#111111] underline decoration-black/20 underline-offset-4 transition-opacity duration-200 hover:opacity-65"
                            >
                                {{ purchasePhone }}
                            </a>
                            to buy this painting.
                        </p>
                    </div>

                    <div
                        v-if="showPhotographyCta"
                        class="mx-auto mt-10 max-w-2xl rounded-[1.5rem] bg-[#f7f6f3] px-6 py-6 text-center"
                    >
                        <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#9a9a9a]">Photography Inquiries</p>
                        <p class="mt-4 text-sm leading-7 text-[#6b6b6b]">
                            Email
                            <button
                                type="button"
                                class="font-medium text-[#111111] underline decoration-black/20 underline-offset-4 transition-opacity duration-200 hover:opacity-65"
                                @click="copyPurchaseEmail"
                            >
                                {{ purchaseCopied ? 'email copied to clipboard' : purchaseEmail }}
                            </button>
                            or text
                            <a
                                :href="`sms:${purchasePhone.replace(/[^0-9+]/g, '')}`"
                                class="font-medium text-[#111111] underline decoration-black/20 underline-offset-4 transition-opacity duration-200 hover:opacity-65"
                            >
                                {{ purchasePhone }}
                            </a>
                            for photography inquiries.
                        </p>
                    </div>

                    <div
                        v-if="showSoldCta"
                        class="mx-auto mt-10 max-w-2xl rounded-[1.5rem] bg-[#f7f6f3] px-6 py-6 text-center"
                    >
                        <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#9a9a9a]">Sold</p>
                        <Link
                            href="/#projects"
                            class="mt-4 inline-block text-sm leading-7 text-[#111111] underline decoration-black/20 underline-offset-4 transition-opacity duration-200 hover:opacity-65"
                        >
                            View other art
                        </Link>
                    </div>
                </div>

                <div class="mx-auto mt-24 max-w-6xl" v-reveal="140">
                    <div class="border-t border-black/8 pt-16">
                        <div class="text-center">
                            <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#9a9a9a]">Archive Navigation</p>
                            <p class="mt-5 font-serif text-3xl leading-none text-[#111111] sm:text-[3.2rem]">
                                Continue through the work
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mx-auto mt-10 grid max-w-6xl gap-6 md:grid-cols-2" v-reveal="160">
                    <Link
                        v-if="previousProject"
                        :href="`/projects/${previousProject.slug}`"
                        class="group rounded-[1.6rem] border border-black/8 bg-[#f7f6f3] px-7 py-7 text-left text-sm uppercase tracking-[0.24em] text-[#6b6b6b] transition-all duration-300 hover:-translate-y-1 hover:opacity-90"
                    >
                        <span class="block">Previous project</span>
                        <span class="mt-4 block font-serif text-[2rem] normal-case tracking-normal text-[#111111] transition-transform duration-300 group-hover:-translate-x-1">
                            {{ previousProject.title }}
                        </span>
                    </Link>
                    <div v-else class="hidden md:block" />

                    <Link
                        v-if="nextProject"
                        :href="`/projects/${nextProject.slug}`"
                        class="group rounded-[1.6rem] border border-black/8 bg-[#f7f6f3] px-7 py-7 text-left md:text-right text-sm uppercase tracking-[0.24em] text-[#6b6b6b] transition-all duration-300 hover:-translate-y-1 hover:opacity-90"
                    >
                        <span class="block">Next project</span>
                        <span class="mt-4 block font-serif text-[2rem] normal-case tracking-normal text-[#111111] transition-transform duration-300 group-hover:translate-x-1">
                            {{ nextProject.title }}
                        </span>
                    </Link>
                </div>
            </div>
        </section>
    </Layout>
</template>

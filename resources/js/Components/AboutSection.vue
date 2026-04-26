<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    photos: {
        type: Array,
        required: true,
    },
    photographyParagraphs: {
        type: Array,
        required: true,
    },
    artParagraphs: {
        type: Array,
        required: true,
    },
});

const currentIndex = ref(0);
let autoplayTimer = null;

const stopAutoplay = () => {
    if (autoplayTimer) {
        window.clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
};

const startAutoplay = () => {
    if (props.photos.length < 2) {
        return;
    }

    stopAutoplay();

    autoplayTimer = window.setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % props.photos.length;
    }, 5000);
};

const goToSlide = (index) => {
    currentIndex.value = index;
};

onMounted(() => {
    startAutoplay();
});

onBeforeUnmount(() => {
    stopAutoplay();
});
</script>

<template>
    <section id="about" class="bg-white px-6 py-28 md:px-10 lg:px-16 lg:py-32">
        <div class="mx-auto grid max-w-[1560px] items-start gap-14 lg:grid-cols-[minmax(0,1fr)_minmax(500px,720px)] lg:gap-24 xl:gap-28">
            <div class="relative z-10 max-w-[42rem] space-y-8 lg:pt-6" v-reveal>
                <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">About</p>

                <div class="max-w-[44rem]">
                    <p class="font-serif text-[1.6rem] leading-none font-semibold text-[#111111] underline decoration-black/20 underline-offset-[10px] sm:text-[1.9rem]">
                        Photography
                    </p>
                    <div class="mt-4 space-y-3 text-[0.88rem] leading-7 text-[#6b6b6b]">
                        <p v-for="paragraph in photographyParagraphs" :key="`photo-${paragraph}`">
                            {{ paragraph }}
                        </p>
                    </div>

                    <div class="mt-10">
                        <p class="font-serif text-[1.6rem] leading-none font-semibold text-[#111111] underline decoration-black/20 underline-offset-[10px] sm:text-[1.9rem]">
                            Art
                        </p>
                        <div class="mt-4 space-y-3 text-[0.88rem] leading-7 text-[#6b6b6b]">
                            <p v-for="paragraph in artParagraphs" :key="`art-${paragraph}`">
                                {{ paragraph }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full lg:max-w-[720px] lg:justify-self-end lg:translate-y-14" v-reveal="140">
                <div
                    class="w-full rounded-[2.2rem] border border-black/6 bg-[#f7f6f3] p-5 shadow-[0_24px_70px_rgba(17,17,17,0.04)] md:p-6"
                    @mouseenter="stopAutoplay"
                    @mouseleave="startAutoplay"
                >
                    <div class="overflow-hidden rounded-[1.8rem] border border-black/8 bg-white">
                        <div
                            class="flex transition-transform duration-700 ease-out"
                            :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
                        >
                            <div
                                v-for="photo in photos"
                                :key="photo.src"
                                class="w-full shrink-0"
                            >
                                <img
                                    :src="photo.src"
                                    :alt="photo.alt"
                                    class="h-[340px] w-full object-cover md:h-[430px] xl:h-[500px]"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-center gap-3">
                        <button
                            v-for="(photo, index) in photos"
                            :key="`dot-${photo.src}`"
                            type="button"
                            :aria-label="`Go to about image ${index + 1}`"
                            class="h-2.5 rounded-full transition-all duration-300"
                            :class="currentIndex === index ? 'w-9 bg-[#111111]' : 'w-2.5 bg-black/12 hover:bg-black/24'"
                            @click="goToSlide(index)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

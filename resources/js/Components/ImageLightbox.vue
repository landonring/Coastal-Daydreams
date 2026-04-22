<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    items: {
        type: Array,
        default: () => [],
    },
    initialIndex: {
        type: Number,
        default: 0,
    },
    origin: {
        type: Object,
        default: () => ({ x: 50, y: 50 }),
    },
});

const emit = defineEmits(['close']);
const currentIndex = ref(props.initialIndex);

const activeItem = computed(() => props.items[currentIndex.value] ?? null);
const panelStyle = computed(() => ({
    transformOrigin: `${props.origin.x}% ${props.origin.y}%`,
}));

const releasePage = () => {
    document.body.style.overflow = '';
    window.removeEventListener('keydown', onKeydown);
};

const onKeydown = (event) => {
    if (!props.open) {
        return;
    }

    if (event.key === 'Escape') {
        emit('close');
    }

    if (event.key === 'ArrowRight') {
        next();
    }

    if (event.key === 'ArrowLeft') {
        previous();
    }
};

const previous = () => {
    if (!props.items.length) {
        return;
    }

    currentIndex.value = (currentIndex.value - 1 + props.items.length) % props.items.length;
};

const next = () => {
    if (!props.items.length) {
        return;
    }

    currentIndex.value = (currentIndex.value + 1) % props.items.length;
};

watch(
    () => props.initialIndex,
    (value) => {
        currentIndex.value = value;
    },
);

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            currentIndex.value = props.initialIndex;
            document.body.style.overflow = 'hidden';
            window.addEventListener('keydown', onKeydown);

            return;
        }

        releasePage();
    },
);

onBeforeUnmount(() => {
    releasePage();
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="open && activeItem"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 px-4 py-8 sm:px-8"
            @click.self="emit('close')"
        >
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="scale-95 opacity-0"
                enter-to-class="scale-100 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="scale-100 opacity-100"
                leave-to-class="scale-95 opacity-0"
                appear
            >
                <div class="relative w-full max-w-6xl" :style="panelStyle">
                    <button
                        type="button"
                        class="absolute right-0 top-[-3rem] text-[0.68rem] uppercase tracking-[0.28em] text-white/80 transition-opacity duration-300 hover:opacity-60"
                        @click="emit('close')"
                    >
                        Close
                    </button>

                    <div class="overflow-hidden bg-white/5 shadow-[0_24px_90px_rgba(0,0,0,0.35)]">
                        <img
                            :src="activeItem.image"
                            :alt="activeItem.alt"
                            class="max-h-[80vh] w-full object-contain"
                        >
                    </div>

                    <div class="mt-5 flex items-end justify-between gap-4 text-white">
                        <div>
                            <p class="font-serif text-2xl">{{ activeItem.title }}</p>
                            <p class="mt-1 text-sm text-white/70">{{ activeItem.caption }}</p>
                        </div>

                        <div class="flex items-center gap-4 text-[0.68rem] uppercase tracking-[0.28em] text-white/80">
                            <button type="button" class="transition-opacity duration-300 hover:opacity-60" @click="previous">
                                Prev
                            </button>
                            <span>{{ currentIndex + 1 }}/{{ props.items.length }}</span>
                            <button type="button" class="transition-opacity duration-300 hover:opacity-60" @click="next">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
});

const activeCategory = ref('All');
const categories = ['All', 'Photography', 'Art'];

const filteredProjects = computed(() =>
    activeCategory.value === 'All'
        ? props.projects
        : props.projects.filter((project) => project.category === activeCategory.value),
);
</script>

<template>
    <section id="projects" class="bg-white px-6 py-28 md:px-10 lg:px-16 lg:py-32">
        <div class="mx-auto max-w-[1540px]">
            <div class="mb-14 flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between" v-reveal>
                <div class="max-w-3xl space-y-5">
                    <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Projects</p>
                    <h2 class="font-serif text-4xl leading-[1.02] text-[#111111] sm:text-5xl">
                        Selected work, arranged with a quieter gallery rhythm.
                    </h2>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        v-for="category in categories"
                        :key="category"
                        type="button"
                        class="rounded-full px-5 py-3 text-[0.72rem] uppercase tracking-[0.28em] transition-colors duration-200"
                        :class="
                            activeCategory === category
                                ? 'bg-[#111111] text-white'
                                : 'bg-[#f7f6f3] text-[#6b6b6b] hover:text-[#111111]'
                        "
                        @click="activeCategory = category"
                    >
                        {{ category }}
                    </button>
                </div>
            </div>

            <div
                v-if="filteredProjects.length"
                class="columns-1 gap-12 md:columns-2 xl:columns-3 xl:gap-16"
            >
                <Link
                    v-for="(project, index) in filteredProjects"
                    :key="project.id"
                    :href="`/projects/${project.slug}`"
                    class="group mb-12 block break-inside-avoid xl:mb-16"
                    v-reveal="index * 60"
                >
                    <div class="relative overflow-hidden rounded-[0.75rem] bg-[#f7f6f3]">
                        <img
                            :src="project.image"
                            :alt="project.title"
                            class="block h-auto w-full transition-all duration-500 ease-out group-hover:scale-[1.015] group-hover:opacity-0"
                            loading="lazy"
                            decoding="async"
                        >
                        <img
                            v-if="project.images?.[1]"
                            :src="project.images[1]"
                            :alt="`${project.title} alternate view`"
                            class="absolute inset-0 h-full w-full object-cover opacity-0 transition-all duration-500 ease-out group-hover:scale-[1.015] group-hover:opacity-100"
                            loading="lazy"
                            decoding="async"
                        >
                        <span
                            v-if="project.category === 'Art' && project.is_sold"
                            class="absolute left-4 top-4 rounded-full bg-white/92 px-3 py-1 text-[0.65rem] uppercase tracking-[0.28em] text-[#6b6b6b]"
                        >
                            Sold
                        </span>
                    </div>

                    <div class="px-2 pt-5 text-center">
                        <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#8a8a8a]">
                            Jennifer Williams
                        </p>
                        <h3 class="mt-4 text-[1.18rem] uppercase tracking-[0.18em] text-[#111111]">
                            {{ project.title }}
                        </h3>
                    </div>
                </Link>
            </div>

            <div v-else class="rounded-[1.75rem] bg-[#f7f6f3] px-6 py-10 text-center" v-reveal>
                <p class="font-serif text-2xl text-[#111111]">No projects in this category yet</p>
                <p class="mt-3 text-sm text-[#6b6b6b]">Add projects from the admin panel to populate this view.</p>
            </div>
        </div>
    </section>
</template>

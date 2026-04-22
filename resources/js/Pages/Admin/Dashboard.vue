<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    projects: {
        type: Array,
        required: true,
    },
});

const deleteProject = (project) => {
    if (!window.confirm(`Delete "${project.title}"? This cannot be undone.`)) {
        return;
    }

    router.delete(`/admin/projects/${project.id}`);
};

const toggleSold = (project) => {
    router.patch(`/admin/projects/${project.id}/sold`);
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Admin</p>
                <h1 class="mt-3 font-serif text-4xl text-[#111111]">Projects</h1>
            </div>

            <Link
                href="/admin/projects/create"
                class="inline-flex items-center justify-center rounded-2xl bg-[#111111] px-6 py-4 text-sm uppercase tracking-[0.18em] text-white transition-opacity duration-200 hover:opacity-90"
            >
                Add Project
            </Link>
        </div>

        <div v-if="projects.length" class="mt-8 space-y-4">
            <article
                v-for="project in projects"
                :key="project.id"
                class="grid gap-5 rounded-[1.75rem] bg-white p-5 md:grid-cols-[120px_1fr_auto] md:items-center md:gap-6 md:p-6"
            >
                <img
                    :src="project.image_url"
                    :alt="project.title"
                    class="h-28 w-full rounded-2xl object-cover md:w-[120px]"
                >

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="font-serif text-2xl text-[#111111]">{{ project.title }}</h2>
                        <span class="rounded-full bg-[#f7f6f3] px-3 py-1 text-[0.68rem] uppercase tracking-[0.24em] text-[#6b6b6b]">
                            {{ project.category }}
                        </span>
                        <span
                            class="rounded-full px-3 py-1 text-[0.68rem] uppercase tracking-[0.24em]"
                            :class="project.is_sold ? 'bg-[#efe7de] text-[#7c6450]' : 'bg-[#f4f4f4] text-[#6b6b6b]'"
                        >
                            {{ project.is_sold ? 'Sold' : 'Available' }}
                        </span>
                    </div>

                    <p class="mt-3 text-sm leading-7 text-[#6b6b6b]">
                        {{ project.description || 'No description added.' }}
                    </p>

                    <p class="mt-3 text-[0.72rem] uppercase tracking-[0.28em] text-[#9a9a9a]">
                        /projects/{{ project.slug }} · {{ project.image_urls.length }} images · {{ project.created_at }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 md:justify-end">
                    <Link
                        :href="`/admin/projects/${project.id}/edit`"
                        class="rounded-2xl bg-[#f7f6f3] px-4 py-3 text-sm text-[#111111] transition-colors duration-200 hover:bg-[#efede8]"
                    >
                        Edit
                    </Link>
                    <button
                        type="button"
                        class="rounded-2xl bg-[#f7f6f3] px-4 py-3 text-sm text-[#111111] transition-colors duration-200 hover:bg-[#efede8]"
                        @click="toggleSold(project)"
                    >
                        {{ project.is_sold ? 'Mark Available' : 'Toggle Sold' }}
                    </button>
                    <button
                        type="button"
                        class="rounded-2xl bg-[#f7f6f3] px-4 py-3 text-sm text-[#111111] transition-colors duration-200 hover:bg-[#efe3e3]"
                        @click="deleteProject(project)"
                    >
                        Delete
                    </button>
                </div>
            </article>
        </div>

        <div v-else class="mt-8 rounded-[1.75rem] bg-white px-6 py-10 text-center">
            <p class="font-serif text-2xl text-[#111111]">No projects yet</p>
            <p class="mt-3 text-sm text-[#6b6b6b]">Add the first project to populate the portfolio.</p>
        </div>
    </AdminLayout>
</template>

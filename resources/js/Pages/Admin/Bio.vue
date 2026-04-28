<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    aboutPhotos: {
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

const objectUrls = [];
const photoItems = ref(
    props.aboutPhotos.map((photo, index) => ({
        key: `existing-${index}`,
        kind: 'existing',
        preview: photo.url,
        path: photo.path,
        name: photo.name,
    })),
);

const aboutContentForm = useForm({
    photography_text: props.photographyParagraphs.join('\n\n'),
    art_text: props.artParagraphs.join('\n\n'),
});

const submitAboutContent = () => {
    aboutContentForm.put('/admin/settings/about-content');
};

const aboutPhotosForm = useForm({
    images: [],
    new_image_keys: [],
    image_order: photoItems.value.map((item) => `existing:${item.path}`),
});

const syncPhotoFields = () => {
    const newItems = photoItems.value.filter((item) => item.kind === 'new');

    aboutPhotosForm.images = newItems.map((item) => item.file);
    aboutPhotosForm.new_image_keys = newItems.map((item) => item.key);
    aboutPhotosForm.image_order = photoItems.value.map((item) => (
        item.kind === 'existing' ? `existing:${item.path}` : `new:${item.key}`
    ));
};

const onPhotosSelected = (event) => {
    const files = Array.from(event.target.files ?? []);

    files.forEach((file, index) => {
        const preview = URL.createObjectURL(file);
        objectUrls.push(preview);

        photoItems.value.push({
            key: `${Date.now()}-${index}-${Math.random().toString(36).slice(2, 8)}`,
            kind: 'new',
            preview,
            file,
            name: file.name,
        });
    });

    syncPhotoFields();
    event.target.value = '';
};

const movePhoto = (index, direction) => {
    const target = index + direction;

    if (target < 0 || target >= photoItems.value.length) {
        return;
    }

    const updated = [...photoItems.value];
    const [item] = updated.splice(index, 1);
    updated.splice(target, 0, item);
    photoItems.value = updated;
    syncPhotoFields();
};

const removePhoto = (index) => {
    const [removed] = photoItems.value.splice(index, 1);

    if (removed?.kind === 'new' && removed.preview?.startsWith('blob:')) {
        URL.revokeObjectURL(removed.preview);
    }

    syncPhotoFields();
};

const submitAboutPhotos = () => {
    syncPhotoFields();

    aboutPhotosForm.put('/admin/settings/about-photos', {
        forceFormData: true,
    });
};

onBeforeUnmount(() => {
    objectUrls.forEach((url) => URL.revokeObjectURL(url));
});
</script>

<template>
    <AdminLayout>
        <Head title="Admin Bio" />

        <div class="mx-auto flex max-w-6xl flex-col gap-12">
            <div class="text-center">
                <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Bio</p>
                <h1 class="mt-4 font-serif text-4xl text-[#111111]">About section</h1>
            </div>

            <div class="space-y-10">
                <form class="rounded-[1.75rem] bg-white p-6 text-left md:p-8" @submit.prevent="submitAboutContent">
                    <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Bio</p>
                    <h2 class="mt-4 font-serif text-3xl text-[#111111]">Edit the About text</h2>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-[#6b6b6b]">
                        Update the Photography and Art text shown on the About section. Separate paragraphs with a blank line.
                    </p>

                    <div class="mt-8 space-y-6">
                        <div>
                            <label class="mb-3 block text-sm text-[#111111]">Photography</label>
                            <textarea
                                v-model="aboutContentForm.photography_text"
                                rows="12"
                                class="w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-sm leading-7 text-[#111111] outline-none"
                            />
                            <p v-if="aboutContentForm.errors.photography_text" class="mt-3 text-sm text-[#9c4b4b]">
                                {{ aboutContentForm.errors.photography_text }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-3 block text-sm text-[#111111]">Art</label>
                            <textarea
                                v-model="aboutContentForm.art_text"
                                rows="9"
                                class="w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-sm leading-7 text-[#111111] outline-none"
                            />
                            <p v-if="aboutContentForm.errors.art_text" class="mt-3 text-sm text-[#9c4b4b]">
                                {{ aboutContentForm.errors.art_text }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-start">
                        <button
                            type="submit"
                            class="rounded-2xl bg-[#111111] px-6 py-4 text-sm uppercase tracking-[0.18em] text-white transition-opacity duration-200 hover:opacity-90 disabled:opacity-50"
                            :disabled="aboutContentForm.processing"
                        >
                            Save Bio
                        </button>
                    </div>
                </form>

                <form class="rounded-[1.75rem] bg-white p-6 text-left md:p-8" @submit.prevent="submitAboutPhotos">
                    <div class="max-w-2xl">
                        <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Carousel Photos</p>
                        <h2 class="mt-4 font-serif text-3xl text-[#111111]">Update the About carousel</h2>
                        <p class="mt-4 max-w-xl text-sm leading-7 text-[#6b6b6b]">
                            Upload, remove, and reorder the images shown in the About section. The carousel follows the order you set here.
                        </p>
                    </div>

                    <input
                        type="file"
                        accept="image/*"
                        multiple
                        class="mt-6 w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-sm text-[#6b6b6b]"
                        @change="onPhotosSelected"
                    >

                    <p v-if="aboutPhotosForm.errors.images" class="mt-3 text-sm text-[#9c4b4b]">
                        {{ aboutPhotosForm.errors.images }}
                    </p>
                    <p v-if="aboutPhotosForm.errors.image_order" class="mt-3 text-sm text-[#9c4b4b]">
                        {{ aboutPhotosForm.errors.image_order }}
                    </p>

                    <div v-if="photoItems.length" class="mt-6 space-y-4">
                        <div
                            v-for="(item, index) in photoItems"
                            :key="item.key"
                            class="grid gap-4 rounded-[1.5rem] bg-[#f7f6f3] p-4 md:grid-cols-[140px_1fr_auto] md:items-center"
                        >
                            <img
                                :src="item.preview"
                                :alt="item.name"
                                class="h-28 w-full rounded-[1.25rem] object-cover md:w-[140px]"
                            >

                            <div class="min-w-0">
                                <p class="text-sm text-[#111111]">{{ item.name }}</p>
                                <p class="mt-2 text-[0.68rem] uppercase tracking-[0.24em] text-[#9a9a9a]">
                                    {{ `Photo ${index + 1}` }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2 md:justify-end">
                                <button
                                    type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-sm text-[#111111] transition-colors duration-200 hover:bg-[#efede8]"
                                    @click="movePhoto(index, -1)"
                                >
                                    Up
                                </button>
                                <button
                                    type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-sm text-[#111111] transition-colors duration-200 hover:bg-[#efede8]"
                                    @click="movePhoto(index, 1)"
                                >
                                    Down
                                </button>
                                <button
                                    type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-sm text-[#111111] transition-colors duration-200 hover:bg-[#efe3e3]"
                                    @click="removePhoto(index)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-start">
                        <button
                            type="submit"
                            class="rounded-2xl bg-[#111111] px-6 py-4 text-sm uppercase tracking-[0.18em] text-white transition-opacity duration-200 hover:opacity-90 disabled:opacity-50"
                            :disabled="aboutPhotosForm.processing"
                        >
                            Save Carousel Photos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

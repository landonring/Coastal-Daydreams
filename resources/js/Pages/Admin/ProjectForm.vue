<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const isEditing = computed(() => Boolean(props.project));
const slugWasEdited = ref(Boolean(props.project?.slug));
const objectUrls = [];
const isPreparingImages = ref(false);
const imagePreparationMessage = ref('');
const errorMessages = computed(() => Object.values(form.errors));
const MAX_CLIENT_UPLOAD_BYTES = 1.8 * 1024 * 1024;
const MAX_IMAGE_DIMENSION = 2400;

const slugify = (value) => value
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');

const imageItems = ref(
    (props.project?.image_urls ?? []).map((url, index) => ({
        key: `existing-${index}`,
        kind: 'existing',
        preview: url,
        path: props.project.image_paths[index],
        name: props.project.title,
    })),
);

const form = useForm({
    title: props.project?.title ?? '',
    slug: props.project?.slug ?? '',
    category: props.project?.category ?? props.categories[0],
    description: props.project?.description ?? '',
    location: props.project?.location ?? '',
    medium: props.project?.medium ?? '',
    year: props.project?.year ?? '',
    hover_preview_enabled: props.project?.hover_preview_enabled ?? true,
    is_sold: props.project?.is_sold ?? false,
    images: [],
    new_image_keys: [],
    image_order: imageItems.value.map((item) => `existing:${item.path}`),
});

const syncImageFields = () => {
    const newItems = imageItems.value.filter((item) => item.kind === 'new');

    form.images = newItems.map((item) => item.file);
    form.new_image_keys = newItems.map((item) => item.key);
    form.image_order = imageItems.value.map((item) => (
        item.kind === 'existing' ? `existing:${item.path}` : `new:${item.key}`
    ));
};

watch(
    () => form.title,
    (title) => {
        if (!slugWasEdited.value) {
            form.slug = slugify(title);
        }
    },
);

const onSlugInput = () => {
    slugWasEdited.value = true;
    form.slug = slugify(form.slug);
};

const loadImage = (src) => new Promise((resolve, reject) => {
    const image = new Image();
    image.onload = () => resolve(image);
    image.onerror = () => reject(new Error('Image could not be loaded.'));
    image.src = src;
});

const canvasToBlob = (canvas, type, quality) => new Promise((resolve, reject) => {
    canvas.toBlob((blob) => {
        if (blob) {
            resolve(blob);
            return;
        }

        reject(new Error('Image could not be compressed.'));
    }, type, quality);
});

const compressImage = async (file) => {
    if (file.size <= MAX_CLIENT_UPLOAD_BYTES) {
        return file;
    }

    const sourceUrl = URL.createObjectURL(file);

    try {
        const image = await loadImage(sourceUrl);
        const scale = Math.min(1, MAX_IMAGE_DIMENSION / Math.max(image.width, image.height));
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(image.width * scale));
        canvas.height = Math.max(1, Math.round(image.height * scale));

        const context = canvas.getContext('2d');

        if (!context) {
            throw new Error('Image context is unavailable.');
        }

        context.drawImage(image, 0, 0, canvas.width, canvas.height);

        let quality = 0.9;
        let blob = await canvasToBlob(canvas, 'image/jpeg', quality);

        while (blob.size > MAX_CLIENT_UPLOAD_BYTES && quality > 0.45) {
            quality -= 0.1;
            blob = await canvasToBlob(canvas, 'image/jpeg', quality);
        }

        const safeName = file.name.replace(/\.[^.]+$/, '') || 'image';

        return new File([blob], `${safeName}.jpg`, {
            type: 'image/jpeg',
            lastModified: Date.now(),
        });
    } finally {
        URL.revokeObjectURL(sourceUrl);
    }
};

const onFilesSelected = async (event) => {
    const files = Array.from(event.target.files ?? []);

    if (!files.length) {
        return;
    }

    isPreparingImages.value = true;
    imagePreparationMessage.value = 'Preparing images for upload...';

    try {
        const preparedFiles = await Promise.all(files.map((file) => compressImage(file)));

        preparedFiles.forEach((file, index) => {
            const preview = URL.createObjectURL(file);
            objectUrls.push(preview);

            imageItems.value.push({
                key: `${Date.now()}-${index}-${Math.random().toString(36).slice(2, 8)}`,
                kind: 'new',
                preview,
                file,
                name: file.name,
            });
        });

        imagePreparationMessage.value = preparedFiles.some((file, index) => file.name !== files[index].name || file.size !== files[index].size)
            ? 'Large images were optimized before upload.'
            : '';
    } catch (error) {
        imagePreparationMessage.value = error instanceof Error
            ? error.message
            : 'Images could not be prepared for upload.';
    } finally {
        isPreparingImages.value = false;
    }

    syncImageFields();
    event.target.value = '';
};

const moveImage = (index, direction) => {
    const target = index + direction;

    if (target < 0 || target >= imageItems.value.length) {
        return;
    }

    const updated = [...imageItems.value];
    const [item] = updated.splice(index, 1);
    updated.splice(target, 0, item);
    imageItems.value = updated;
    syncImageFields();
};

const removeImage = (index) => {
    const [removed] = imageItems.value.splice(index, 1);

    if (removed?.kind === 'new' && removed.preview?.startsWith('blob:')) {
        URL.revokeObjectURL(removed.preview);
    }

    syncImageFields();
};

const submit = () => {
    syncImageFields();

    if (isEditing.value) {
        form
            .transform((data) => ({ ...data, _method: 'put' }))
            .post(`/admin/projects/${props.project.id}`, {
                forceFormData: true,
                onFinish: () => form.transform((data) => data),
            });

        return;
    }

    form.post('/admin/projects', {
        forceFormData: true,
    });
};

onBeforeUnmount(() => {
    objectUrls.forEach((url) => URL.revokeObjectURL(url));
});
</script>

<template>
    <AdminLayout>
        <Head :title="isEditing ? 'Edit Project' : 'Add Project'" />

        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Project</p>
                <h1 class="mt-3 font-serif text-4xl text-[#111111]">
                    {{ isEditing ? 'Edit project' : 'Add project' }}
                </h1>
            </div>

            <Link
                href="/admin/dashboard"
                class="text-sm uppercase tracking-[0.22em] text-[#6b6b6b] transition-opacity duration-200 hover:opacity-70"
            >
                Cancel
            </Link>
        </div>

        <form class="mt-8 rounded-[1.75rem] bg-white p-6 md:p-8" @submit.prevent="submit">
            <div
                v-if="errorMessages.length"
                class="mb-6 rounded-[1.5rem] border border-[#e8caca] bg-[#fbf3f3] p-5"
            >
                <p class="text-[0.72rem] uppercase tracking-[0.28em] text-[#9c4b4b]">Please Fix These Fields</p>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-[#9c4b4b]">
                    <li v-for="message in errorMessages" :key="message">
                        {{ message }}
                    </li>
                </ul>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-3 block text-sm text-[#111111]">Title</label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-base text-[#111111] outline-none"
                    >
                    <p v-if="form.errors.title" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label class="mb-3 block text-sm text-[#111111]">Slug</label>
                    <input
                        v-model="form.slug"
                        type="text"
                        class="w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-base text-[#111111] outline-none"
                        @input="onSlugInput"
                    >
                    <p class="mt-2 text-xs uppercase tracking-[0.24em] text-[#9a9a9a]">Used for `/projects/{slug}`</p>
                    <p v-if="form.errors.slug" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.slug }}</p>
                </div>

                <div>
                    <label class="mb-3 block text-sm text-[#111111]">Category</label>
                    <select
                        v-model="form.category"
                        class="w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-base text-[#111111] outline-none"
                    >
                        <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                    </select>
                    <p v-if="form.errors.category" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.category }}</p>
                </div>

                <div class="md:col-span-2">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <label class="mb-3 block text-sm text-[#111111]">Images</label>
                            <p class="text-xs uppercase tracking-[0.22em] text-[#9a9a9a]">
                                Add as many images as you need. Image 1 is the main image, image 2 appears on hover, and each image can be up to 15 MB.
                            </p>
                        </div>
                    </div>

                    <input
                        type="file"
                        accept="image/*"
                        multiple
                        class="mt-4 w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-sm text-[#6b6b6b]"
                        :disabled="isPreparingImages"
                        @change="onFilesSelected"
                    >
                    <p v-if="imagePreparationMessage" class="mt-3 text-sm text-[#6b6b6b]">{{ imagePreparationMessage }}</p>
                    <p v-if="form.errors.images" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.images }}</p>
                    <p v-if="form.errors.image_order" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.image_order }}</p>

                    <div v-if="imageItems.length" class="mt-5 space-y-4">
                        <div
                            v-for="(item, index) in imageItems"
                            :key="item.key"
                            class="grid gap-4 rounded-[1.5rem] bg-[#f7f6f3] p-4 md:grid-cols-[120px_1fr_auto] md:items-center"
                        >
                            <img
                                :src="item.preview"
                                :alt="item.name"
                                class="h-28 w-full rounded-[1.25rem] object-cover md:w-[120px]"
                            >

                            <div class="min-w-0">
                                <p class="text-sm text-[#111111]">{{ item.name }}</p>
                                <p class="mt-2 text-[0.68rem] uppercase tracking-[0.24em] text-[#9a9a9a]">
                                    {{
                                        index === 0
                                            ? 'Main image'
                                            : index === 1
                                                ? 'Hover image'
                                                : `Gallery image ${index + 1}`
                                    }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2 md:justify-end">
                                <button
                                    type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-sm text-[#111111] transition-colors duration-200 hover:bg-[#efede8]"
                                    @click="moveImage(index, -1)"
                                >
                                    Up
                                </button>
                                <button
                                    type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-sm text-[#111111] transition-colors duration-200 hover:bg-[#efede8]"
                                    @click="moveImage(index, 1)"
                                >
                                    Down
                                </button>
                                <button
                                    type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-sm text-[#111111] transition-colors duration-200 hover:bg-[#efe3e3]"
                                    @click="removeImage(index)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-3 block text-sm text-[#111111]">Description</label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-base text-[#111111] outline-none"
                    />
                    <p v-if="form.errors.description" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.description }}</p>
                </div>

                <div>
                    <label class="mb-3 block text-sm text-[#111111]">Location</label>
                    <input
                        v-model="form.location"
                        type="text"
                        class="w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-base text-[#111111] outline-none"
                    >
                    <p v-if="form.errors.location" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.location }}</p>
                </div>

                <div>
                    <label class="mb-3 block text-sm text-[#111111]">Medium / Materials</label>
                    <input
                        v-model="form.medium"
                        type="text"
                        class="w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-base text-[#111111] outline-none"
                    >
                    <p class="mt-2 text-xs uppercase tracking-[0.24em] text-[#9a9a9a]">Example: Acrylic on canvas</p>
                    <p v-if="form.errors.medium" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.medium }}</p>
                </div>

                <div>
                    <label class="mb-3 block text-sm text-[#111111]">Year</label>
                    <input
                        v-model="form.year"
                        type="number"
                        min="1000"
                        max="9999"
                        class="w-full rounded-2xl bg-[#f7f6f3] px-5 py-4 text-base text-[#111111] outline-none"
                    >
                    <p v-if="form.errors.year" class="mt-3 text-sm text-[#9c4b4b]">{{ form.errors.year }}</p>
                </div>

                <label class="inline-flex items-center gap-3">
                    <input
                        v-model="form.hover_preview_enabled"
                        type="checkbox"
                        class="h-5 w-5 rounded border-black/10 text-[#111111]"
                    >
                    <span class="text-sm text-[#111111]">Show second image on hover</span>
                </label>

                <label class="inline-flex items-center gap-3 md:self-end">
                    <input v-model="form.is_sold" type="checkbox" class="h-5 w-5 rounded border-black/10 text-[#111111]">
                    <span class="text-sm text-[#111111]">Mark as sold</span>
                </label>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <button
                    type="submit"
                    class="rounded-2xl bg-[#111111] px-6 py-4 text-sm uppercase tracking-[0.18em] text-white transition-opacity duration-200 hover:opacity-90 disabled:opacity-50"
                    :disabled="form.processing || isPreparingImages"
                >
                    {{ isPreparingImages ? 'Preparing Images' : 'Save' }}
                </button>
                <Link
                    href="/admin/dashboard"
                    class="rounded-2xl bg-[#f7f6f3] px-6 py-4 text-center text-sm uppercase tracking-[0.18em] text-[#111111] transition-colors duration-200 hover:bg-[#efede8]"
                >
                    Cancel
                </Link>
            </div>
        </form>
    </AdminLayout>
</template>

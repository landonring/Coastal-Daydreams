<script setup>
import { Head } from '@inertiajs/vue3';

import AboutSection from '../Components/AboutSection.vue';
import FooterSection from '../Components/FooterSection.vue';
import HeroSection from '../Components/HeroSection.vue';
import ProjectsSection from '../Components/ProjectsSection.vue';
import Layout from '../Layouts/Layout.vue';

defineProps({
    aboutPhotos: {
        type: Array,
        default: () => [],
    },
    photographyParagraphs: {
        type: Array,
        default: () => [],
    },
    artParagraphs: {
        type: Array,
        default: () => [],
    },
    projects: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Layout>
        <Head title="Home" />

        <HeroSection
            name="Jennifer Williams"
            subtitle="Coastal Daydreams"
            discipline="Photography & Art"
            location="Central Coast, California"
        />

        <AboutSection
            :photography-paragraphs="photographyParagraphs"
            :art-paragraphs="artParagraphs"
            :photos="aboutPhotos"
        />

        <ProjectsSection :projects="projects" />

        <FooterSection
            email="coastaldaydreams@yahoo.com"
            phone="559-816-1411"
            name="Jennifer Williams"
            location="Central Coast, California"
            :disciplines="['Photography', 'Art']"
        />
    </Layout>
</template>

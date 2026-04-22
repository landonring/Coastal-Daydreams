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
    projects: {
        type: Array,
        default: () => [],
    },
});

const aboutParagraphs = [
    'With a camera as her constant companion, Jennifer has spent a lifetime chasing the fleeting beauty of the world.',
    'Growing up she was raised in the military life, in the rhythm of constant relocation and fleeting experiences. She learned early on that memories fade, but a photograph can hold a moment forever. Each image became a keepsake, a gentle reminder of faces and places, a slice of time that might otherwise be lost forever.',
    'Her journey eventually led her to the Central Coast of California. A place she now calls home and her photography flourished. Jennifer finds inspiration in the ever-changing dance of light and landscape. Her lens seeks not just to capture a scene, but to distill the feeling of a single unrepeatable instant.',
    'Through her work, she invites you to linger, to look a little longer, and to let yourself be drawn into the quiet magic of the world.',
    'In every photograph, Jennifer hopes to offer a brief sanctuary, a place where, for just a heartbeat, time stands still.',
];

</script>

<template>
    <Layout>
        <Head title="Home" />

        <HeroSection
            name="Jennifer Williams"
            discipline="Photography & Art"
            location="Central Coast, California"
        />

        <AboutSection :paragraphs="aboutParagraphs" :photos="aboutPhotos" />

        <ProjectsSection :projects="projects" />

        <FooterSection
            email="Jenniferwilliams1956@yahoo.com"
            name="Jennifer Williams"
            location="Central Coast, California"
            :disciplines="['Photography', 'Art', 'Commissions']"
        />
    </Layout>
</template>

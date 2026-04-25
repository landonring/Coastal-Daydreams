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

const photographyParagraphs = [
    'With a camera as her constant companion, Jennifer has spent a lifetime chasing the fleeting beauty of the world.',
    'Growing up she was raised in the military life, in the rhythm of constant relocation and fleeting experiences. She learned early on that memories fade, but a photograph can hold a moment forever. Each image became a keepsake, a gentle reminder of faces and places, a slice of time that might otherwise be lost forever.',
    'Her journey eventually led her to the Central Coast of California. A place she now calls home and her photography flourished. Jennifer finds inspiration in the ever-changing dance of light and landscape. Her lens seeks not just to capture a scene, but to distill the feeling of a single unrepeatable instant.',
    'Through her work, she invites you to linger, to look a little longer, and to let yourself be drawn into the quiet magic of the world.',
    'In every photograph, Jennifer hopes to offer a brief sanctuary, a place where, for just a heartbeat, time stands still.',
];

const artParagraphs = [
    'Jennifer Williams is a self taught artist with a flare for imagination.',
    'Working primarily with acrylic and painting on canvas, she creates whimsical wildflowers and surreal landscapes that evolve organically in the moment.',
    'Without a set plan or predetermined vision Jennifer allows each piece to develop intuitively as she paints. Her work reflects a spontaneous, imaginative process where each painting unfolds naturally, guided by the brush and the energy of the moment.',
];

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
            email="Jenniferwilliams1956@yahoo.com"
            phone="559-816-1411"
            name="Jennifer Williams"
            location="Central Coast, California"
            :disciplines="['Photography', 'Art']"
        />
    </Layout>
</template>

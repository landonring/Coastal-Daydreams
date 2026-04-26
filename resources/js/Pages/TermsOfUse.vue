<script setup>
import { Head } from '@inertiajs/vue3';

import Layout from '../Layouts/Layout.vue';
</script>

<template>
    <Layout>
        <Head title="Terms of Use" />

        <section class="bg-white px-6 pb-24 pt-28 md:px-10 lg:px-16 lg:pb-32 lg:pt-32">
            <div class="mx-auto max-w-4xl">
                <div class="rounded-[2rem] border border-black/6 bg-[#f7f6f3]/55 px-8 py-14 md:px-12 md:py-16">
                    <p class="text-[0.72rem] uppercase tracking-[0.34em] text-[#6b6b6b]">Terms of Use</p>
                    <h1 class="mt-5 font-serif text-4xl leading-[1.04] text-[#111111] sm:text-5xl">
                        Copyright Notice
                    </h1>

                    <div class="mt-8 space-y-6 max-w-3xl text-[1rem] leading-8 text-[#6b6b6b]">
                        <p>
                            All images and artwork on this site are the property of Jennifer Williams and are protected by copyright.
                        </p>
                        <p>
                            They may not be copied, reproduced, downloaded, screenshotted, printed, or used in any form without prior written permission. This includes personal, commercial, or resale use.
                        </p>
                        <p>
                            Unauthorized use of any images is strictly prohibited.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </Layout>
</template>

import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
    title: (title) => (title ? `${title} | Jennifer Williams` : 'Jennifer Williams'),
    resolve: async (name) => {
        const page = await pages[`./Pages/${name}.vue`]();

        return page.default;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .directive('reveal', {
                mounted(element, binding) {
                    const delay = typeof binding.value === 'number' ? binding.value : 0;

                    element.classList.add('reveal-ready');
                    element.style.transitionDelay = `${delay}ms`;
                    element.classList.add('reveal-visible');
                },
            })
            .mount(el);
    },
});

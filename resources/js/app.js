import './bootstrap';
import '../css/bootstrap.css';
import '../css/app.css';
import 'highlight.js/styles/atom-one-dark.css';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

import { createApp, h } from 'vue';
import VueHighlightJS from 'vue3-highlightjs';
import { QuillEditor } from '@vueup/vue-quill';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createInertiaApp, Head, Link } from "@inertiajs/vue3"
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const quillEditorOptions = {
    theme: 'snow',
    modules: {
        toolbar: [
            'bold', 'italic', 'underline', 'link', 'video',
            { 'list': 'bullet' },
            { 'indent': '+1' },
            { 'header': [1, 2, 3, 4, 5, 6, false] },
            { 'color': [] },
            'clean'
        ],
    },
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {

        const app = createApp({ render: () => h(App, props) });

        QuillEditor.props.globalOptions.default = () => quillEditorOptions;

        app.use(plugin);
        app.use(ZiggyVue);
        app.use(VueHighlightJS)
        app.component("Head", Head);
        app.component("Link", Link);
        app.component('QuillEditor', QuillEditor)
        app.mount(el);
    },
    progress: {
        color: "#3459e6",
    },
});

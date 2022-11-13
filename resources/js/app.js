import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/inertia-vue3';
import 'bootstrap';
import '../css/app.scss';
import Default from './Layouts/Default.vue';
import FontAwesomeIcon from './Utilities/FontAwesome';
import { InertiaProgress } from '@inertiajs/progress';
import CountryFlagEsm from 'vue-country-flag-next';
import Tutorial from '@/Layouts/Tutorial.vue';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

createInertiaApp({
    resolve: name => {
        const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

        page.then((module) => {
            if (name.includes('Pages/Tutorials/')) {
                module.default.layout = Tutorial;
            } else if (page.layout === undefined) {
                module.default.layout = Default;
            }
        });

        return page;
    },
    setup ({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mixin({ methods: { route } })
            .component('fa', FontAwesomeIcon)
            .component('InertiaLink', Link)
            .component('CountryFlag', CountryFlagEsm)
            .mount(el);
    },
});

InertiaProgress.init();

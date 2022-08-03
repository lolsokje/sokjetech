import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/inertia-vue3';
import 'bootstrap';
import route from 'ziggy';
import Default from './Layouts/Default';
import FontAwesomeIcon from './Utilities/FontAwesome';
import { InertiaProgress } from '@inertiajs/progress';

createInertiaApp({
    resolve: name => {
        let page = require(`./Pages/${name}`).default;

        if (page.layout === undefined) {
            page.layout = Default;
        }

        return page;
    },
    setup ({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mixin({ methods: { route } })
            .component('fa', FontAwesomeIcon)
            .component('InertiaLink', Link)
            .mount(el);
    },
});

InertiaProgress.init();

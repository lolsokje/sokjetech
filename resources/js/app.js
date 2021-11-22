import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import 'bootstrap';
import route from 'ziggy';
import Layout from './Shared/Layout';
import FontAwesomeIcon from './Utilities/FontAwesome';

createInertiaApp({
    resolve: name => {
        let page = require(`./Pages/${name}`).default;

        if (page.layout === undefined) {
            page.layout = Layout;
        }

        return page;
    },
    setup ({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mixin({ methods: { route } })
            .component('fa', FontAwesomeIcon)
            .mount(el);
    },
});

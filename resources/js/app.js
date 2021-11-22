import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import 'bootstrap';
import route from 'ziggy';

createInertiaApp({
  resolve: name => require(`./Pages/${name}`),
  setup ({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mixin({ methods: { route } })
      .mount(el);
  },
});

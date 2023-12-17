import './bootstrap';
import '../css/app.css';

import 'primeicons/primeicons.css'
import { createApp, h} from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import VsToast from '@vuesimple/vs-toast';
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import "../css/tooltip.css";
import Tooltip from 'primevue/tooltip';
// theme
import 'primevue/resources/themes/lara-light-green/theme.css';
import VueTelInput from 'vue-tel-input';
import 'vue-tel-input/vue-tel-input.css';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
const VueTelInputOptions = {
    mode: "international",
    onlyCountries: ['PH'],
  }

createInertiaApp({
    title: (title) => `${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(PrimeVue)
            .use(ConfirmationService)
            .use(ToastService)
            .use(VueTelInput, VueTelInputOptions)
            .directive('tooltip', Tooltip)
            .component('VueDatePicker', VueDatePicker)
            .component('VsToast', VsToast)
            .mount(el);
            
    },
    progress: {
        color: '#4B5563',
    },
});

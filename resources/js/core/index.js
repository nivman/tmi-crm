import Form from './Form';
import Event from './Event';
import moment from 'moment';
import Notify from './Notify';
import Mobile from './Mobile';
import Online from './Online';
import Storage from './Storage';
import 'flatpickr/dist/flatpickr.css';
import RadioComponent from '../components/helpers/RadioComponent.vue';
import LoadingComponent from '../components/helpers/LoadingComponent.vue';
import CheckboxComponent from '../components/helpers/CheckboxComponent.vue';
import NumberInputComponent from '../components/helpers/NumberInputComponent.vue';
import CustomFieldComponent from '../components/helpers/CustomFieldComponent.vue';

const Tec = {
    install(Vue, window) {
        Vue.use(Mobile);
        Vue.use(Online);
        Vue.mixin(Notify);
        Vue.prototype.$http = window.axios;
        Vue.prototype.$laravel = window.laravel;
        Vue.prototype.$form = window.Form = Form;
        Vue.component('loading', LoadingComponent);
        Vue.prototype.$event = window.Event = Event;
        Vue.prototype.$moment = window.moment = moment;
        Vue.prototype.$swc = 'serviceWorker' in navigator;
        Vue.prototype.$storage = window.Storage = Storage;
        Vue.component('radio-component', RadioComponent);
        Vue.component('checkbox-component', CheckboxComponent);
        Vue.component('custom-field-component', CustomFieldComponent);
        Vue.component('number-input-component', NumberInputComponent);
        Vue.component('flat-pickr', () => import('vue-flatpickr-component'));
        Vue.prototype.$accounting = window.accounting = require('accounting');
        Vue.prototype.$laravel = {
            token: document.head.querySelector('meta[name="csrf-token"]')
                ? document.head.querySelector('meta[name="csrf-token"]').content
                : '',
        };
    },
};

export default Tec;

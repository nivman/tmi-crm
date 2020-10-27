require('./bootstrap');
import Vue from 'vue';
import vSelect from 'vue-select';
import Tec from './core/index.js';
import VModal from 'vue-js-modal';
import VueRouter from 'vue-router';
import VeeValidate from 'vee-validate';
import Filters from './core/Filters.js';
import VueProgressBar from 'vue-progressbar';
import Notifications from 'vue-notification';

window.Vue = Vue;
Vue.config.performance = true;
Vue.config.productionTip = false;
const vpOpts = { color: '#3273dc', failedColor: '#ff3860' };
// const vpOpts = { color: '#00d1b2', failedColor: '#ff3860' };
Object.entries(Filters).forEach(([key, value]) => Vue.filter(key, value));

Vue.use(VueRouter);
Vue.use(Tec, window);
Vue.use(VeeValidate);
Vue.use(Notifications);
Vue.use(VueProgressBar, vpOpts);
Vue.component('v-select', vSelect);
Vue.use(VModal, { dialog: true, dynamic: true });

import { ServerTable } from './helpers/vue-tables-2/compiled';
// import { ServerTable } from '/Users/Saleem/Desktop/GitHub/vue-tables-2/compiled';
Vue.use(ServerTable, {
    sortIcon: { base: 'th-sort', up: 'sort-icon-up', down: 'sort-icon-down', is: 'sort-icon' },
    serverMultiSorting: true,
    debounce: 500,
    pagination: { chunk: 5, dropdown: false },
    texts: { filter: ' חיפוש: ', limit: ' מספר שורות: ' },

    skin: 'table is-fullwidth is-bordered is-striped is-hoverable is-rounded m-t-sm m-b-sm',
    requestFunction: function(data) {
        return window.axios.get(this.url, { params: data }).catch(err => {
            window.Event.fire('appError', err.response);
        });
    },
});

import store from './store';
import router from './routes.js';
import MainComponent from './components/MainComponent.vue';

axios
    .get('/data')
    .then(sd => {
        if (sd.data.settings) {
            window.axios.defaults.baseURL = sd.data.settings.baseURL;
            const app = new Vue({
                el: '#app',
                store: store(sd.data),
                router,
                components: { MainComponent },
                mounted() {
                    this.$refs.preloaderapp.remove();
                    this.$Progress.finish();
                },
                created() {
                    this.$Progress.start();
                    this.$router.beforeEach((to, from, next) => {
                        let appName = this.$store.state.settings.app ? this.$store.state.settings.app.name : window.settings.app.name;
                        document.title = to.meta.title + ' - ' + appName;
                        this.$Progress.start();
                        if (to.path != '/login') {
                            if (this.$store.getters.user) {
                                if (this.$store.getters.superAdmin) {
                                    next();
                                } else if (this.$store.getters.admin) {
                                    if (to.meta.super && !to.meta.admin) {
                                        this.$Progress.fail();
                                        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
                                        document.title = from.meta.title + ' - ' + appName;
                                        next(from ? from.path.toString() : '/');
                                    } else {
                                        next();
                                    }
                                } else if (this.$store.getters.customer && this.$store.getters.vendor) {
                                    if (to.meta.customer || to.meta.vendor) {
                                        next();
                                    } else {
                                        this.$Progress.fail();
                                        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
                                        document.title = from.meta.title + ' - ' + appName;
                                        next(from ? from.path.toString() : '/');
                                    }
                                } else if (this.$store.getters.customer && !this.$store.getters.vendor) {
                                    if (to.meta.customer) {
                                        next();
                                    } else {
                                        this.$Progress.fail();
                                        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
                                        document.title = from.meta.title + ' - ' + appName;
                                        next(from ? from.path.toString() : '/');
                                    }
                                } else if (this.$store.getters.vendor && !this.$store.getters.customer) {
                                    if (to.meta.vendor) {
                                        next();
                                    } else {
                                        this.$Progress.fail();
                                        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
                                        document.title = from.meta.title + ' - ' + appName;
                                        next(from ? from.path.toString() : '/');
                                    }
                                } else {
                                    if (to.meta.super || to.meta.admin) {
                                        this.$Progress.fail();
                                        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
                                        document.title = from.meta.title + ' - ' + appName;
                                        next(from ? from.path.toString() : '/');
                                    } else {
                                        next();
                                    }
                                }
                                // if (to.meta.super) {
                                //     if (this.$store.getters.superAdmin) {
                                //         next();
                                //     } else {
                                //         this.$Progress.fail();
                                //         this.notify("warn", "You are not allowed to access the requested resource.", "Access denied!");
                                //         document.title = from.meta.title + " - " + appName;
                                //         next(from ? from.path.toString() : "/");
                                //     }
                                // } else if (to.meta.admin) {
                                //     if (this.$store.getters.admin) {
                                //         next();
                                //     } else {
                                //         this.$Progress.fail();
                                //         this.notify("warn", "You are not allowed to access the requested resource.", "Access denied!");
                                //         document.title = from.meta.title + " - " + appName;
                                //         next(from ? from.path.toString() : "/");
                                //     }
                                // } else {
                                //     next();
                                // }
                            } else {
                                next('/login');
                            }
                        } else {
                            next();
                        }
                    });
                    this.$router.afterEach(() => {
                        this.$Progress.finish();
                    });
                    window.axios.interceptors.request.use(
                        config => {
                            this.$Progress.start();
                            return config;
                        },
                        error => {
                            this.$Progress.fail();
                            return Promise.reject(error);
                        }
                    );
                    window.axios.interceptors.response.use(
                        response => {
                            this.$Progress.finish();
                            return response;
                        },
                        error => {
                            this.$Progress.fail();
                            return Promise.reject(error);
                        }
                    );

                },
            });
        } else {
            throw new Error('Initial data loading failed! response: ' + JSON.stringify(sd));
        }
    })
    .catch(error => {
        window.console.error(error);
        document.querySelector('.cbf').classList.remove('cbf');
        document.querySelector('.wait-title').innerHTML = `
        <div class="svg-box">
            <svg class="circular yellow-stroke">
                <circle class="path" cx="75" cy="75" r="50" fill="none" stroke-width="5" stroke-miterlimit="10"/>
            </svg>
            <svg class="alert-sign yellow-stroke">
                <g transform="matrix(1,0,0,1,-615.516,-257.346)">
                    <g transform="matrix(0.56541,-0.56541,0.56541,0.56541,93.7153,495.69)">
                        <path class="line" d="M634.087,300.805L673.361,261.53" fill="none"/>
                    </g>
                    <g transform="matrix(2.27612,-2.46519e-32,0,2.27612,-792.339,-404.147)">
                        <circle class="dot" cx="621.52" cy="316.126" r="1.318" />
                    </g>
                </g>
            </svg>
            <h2 class="error is-size-2">Error!</h2>
        `;
        document.querySelector('.wait-message').innerHTML =
            '<h5 class="is-size-5">Unable to load application.<br>Please check the error that is logged to console.</h5>';
        if (!navigator.onLine) {
            document.querySelector('.cbf').classList.remove('cbf');
            window.console.error('Network Error! Unable to load application.');
            document.querySelector('.wait-title').innerHTML = 'Network Error!<br>';
            document.querySelector('.wait-message').innerText = 'Unable to load application.';
        }
    });

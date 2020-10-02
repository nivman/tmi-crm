import Vue from "vue";
import Vuex from "vuex";
// import * as actions from './actions'
import * as getters from "./getters";
import { UPDATE_SETTINGS, UPDATE_USER, TOGGLE_SIDEBAR, TOGGLE_LOADING, SET_STOCK, UPDATE_NOTIFICATION } from "./mutation-types";

Vue.use(Vuex);

// const debug = process.env.NODE_ENV !== "production";

function builder(data) {
    return new Vuex.Store({
        state: {
            loading: true,
            sideBar: false,
            user: data.user,
            settings: data.settings,
            notifications: data.notifications
        },
        // actions,
        getters,
        mutations: {
            [UPDATE_SETTINGS](state, settings) {
                state.settings = settings;
            },
            [UPDATE_USER](state, user) {
                state.user = user;
                if (user == null) {
                    state.sideBar = false;
                }
            },
            [TOGGLE_SIDEBAR](state, v) {
                state.sideBar = v;
            },
            [SET_STOCK](state, v) {
                state.settings.stock = v;
            },
            [TOGGLE_LOADING](state, v) {
                state.loading = v ? v : !state.loading;
            },
            [UPDATE_NOTIFICATION](state, notification) {
                let notifications = { ...state.notifications, ...notification };
                state.notifications = notifications;
            }
        }
    });
}
export default builder;

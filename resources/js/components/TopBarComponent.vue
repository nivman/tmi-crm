<template>
    <nav
        class="navbar animated faster fadeInDown"
        role="navigation"
        aria-label="main navigation"
        v-if="is_logged_in"
        :class="{ 'is-fixed-top m-b-md': $store.state.settings.ac.navPosition != 'top' }"
    >
        <div class="special-shadow" v-if="!menu || $store.state.settings.ac.navPosition != 'top'"></div>
        <div class="container">
            <div class="navbar-brand has-text-centered">
                <a
                    @click="toggleMenu"
                    :class="{ 'is-active': !menu }"
                    class="navbar-item is-hidden-touch"
                    v-if="$store.state.settings.ac.navPosition != 'top'"
                >
                    <i class="fas fa-bars" />
                </a>
                <span class="navbar-item has-text-weight-bold">
                    <router-link to="/" exact>
                        <span class="is-hidden-mobile">{{ site_name }}</span>
                        <span class="is-hidden-desktop">{{ $store.state.settings.ac.short_name || 'SBM' }}</span>
                    </router-link>
                    <a class="button is-small is-danger m-l-md" v-if="!isOnline">
                        <i class="fas fa-plug" />
                        <span class="m-l-sm has-text-weight-bold">Network Error!</span>
                    </a>
                </span>
                <router-link
                    class="is-hidden-desktop navbar-item is-radiusless is-shadowless"
                    style="margin-right:auto;"
                    to="/profile"
                    exact>
                    <i class="fas fa-user" />
                </router-link>
                <router-link class="is-hidden-desktop navbar-item is-radiusless is-shadowless" to="/change_password" exact>
                    <i class="fas fa-key" />
                </router-link>
                <a class="is-hidden-desktop navbar-item is-radiusless is-shadowless" @click="logout()">
                    <i class="fas fa-sign-out-alt" />
                </a>
                <a
                    @click="toggleMenu"
                    :class="{ 'is-active': !menu }"
                    class="is-hidden-desktop navbar-item navbar-burger is-radiusless is-shadowless m-l-none"
                >
                    <i class="fas" :class="menuIcon" />
                </a>
            </div>
            <div class="navbar-end is-hidden-touch" style="font-size: .95rem;" v-if="!$store.getters.customer && !$store.getters.vendor">
                    <router-link to="/" exact class="navbar-item">

                    <span class="is-hidden-touch m-l-sm">עמוד הסית</span>
                  <i class="fas fa-tachometer-alt" />
                </router-link>
                <router-link to="/settings" exact class="navbar-item" v-if="$store.getters.superAdmin">

                    <span class="is-hidden-touch m-l-sm">הגדרות</span>
                  <i class="fas fa-cogs" />
                </router-link>
                <router-link to="/report" exact class="navbar-item" v-if="$store.getters.admin">

                    <span class="is-hidden-touch m-l-sm">דוחות</span>
                  <i class="fas fa-chart-line" />
                </router-link>
                <router-link to="/calendar" exact class="navbar-item">

                    <span class="is-hidden-touch m-l-sm">יומן</span>
                  <i class="fas fa-calendar-alt" />
                </router-link>
                <router-link to="/logs" exact class="navbar-item" v-if="$store.getters.superAdmin">

                    <span class="is-hidden-touch m-l-sm">רישום פעולות</span>
                  <i class="fas fa-file-alt" />
                </router-link>
                <div class="navbar-item has-dropdown is-hoverable" v-if="$store.getters.notifications">
                    <a role="button" class="navbar-link is-arrowless no-caret p-x-md">
                        <span class="badge is-small is-badge-danger" data-badge>
                            <i class="fas fa-bell" />
                        </span>
                    </a>

                </div>
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        <span class="is-capitalized">{{ user_name }}</span>
                      <i class="fas fa-user m-r-sm" />
                    </a>
                    <div class="navbar-dropdown is-right">
                        <router-link to="/profile" exact class="navbar-item">{{ name }} פרופיל</router-link>
                        <router-link to="/change_password" exact class="navbar-item">שינוי סיסמה </router-link>
                        <a class="navbar-item" @click="logout()">יציאה</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    computed: {
        menuIcon() {
            return this.menu ? 'fa-bars' : 'fa-times';
        },
        menu() {
            return this.$store.state.sideBar;
        },
        site_name() {
            return this.$store.getters.settings.app.name;
        },
        is_logged_in() {
            return this.$store.getters.user ? true : false;
        },
        name() {
            return this.$store.getters.user ? this.$store.getters.user.name : '';
        },
        user_name() {
            return this.$store.getters.user ? this.$store.getters.user.username : '';
        },
    },
    methods: {
        toggleMenu() {
            window.scroll(0, 0);
            this.$store.commit('TOGGLE_SIDEBAR', !this.$store.state.sideBar);
        },
        logout() {
            this.$event.fire('logOut');
        },
    },
};
</script>

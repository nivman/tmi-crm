<template>
    <footer class="animated fastest fadeInUp">
        <span class="text-muted" v-html="'&copy; ' + year + ' ' + site_name"></span>
        <span class="text-muted is-pulled-right version">
            Version {{ version }}
            <small>
                <a href="https://tecdiary.github.io/sbm-guide/" target="_blank" class="animated fastest slideInRight">
                    <i class="fas fa-question-circle"></i>
                </a>
            </small>
        </span>
    </footer>
</template>

<script>
import composer from '@m/composer.json';
export default {
    computed: {
        year() {
            return new Date().getFullYear();
        },
        version() {
            return composer.version;
        },
        site_name() {
            return this.$store.getters.settings.app.name;
        },
    },
};
</script>

<style lang="scss" scoped>
.version {
    width: 125px;
    text-align: right;
    margin-right: -1rem;
    transition: width 0.5s ease-in;
    a {
        opacity: 0;
    }
    &:hover {
        a {
            opacity: 1;
        }
    }
}
</style>

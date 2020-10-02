<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card is-medium animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
                <p class="modal-card-title">Vendor Details</p>
                <button type="button" class="delete" @click="$router.go(-1)"></button>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <loading v-if="loading"></loading>
                <div v-else>
                    <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                        <tbody>
                            <tr>
                                <td>Name</td>
                                <td>{{ vendor.name }}</td>
                            </tr>
                            <tr>
                                <td>Company</td>
                                <td>{{ vendor.company }}</td>
                            </tr>
                            <tr>
                                <td>email</td>
                                <td>{{ vendor.email }}</td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>{{ vendor.phone }}</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td>{{ vendor.address }}</td>
                            </tr>
                            <tr>
                                <td>State</td>
                                <td>{{ vendor.state_name }}</td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td>{{ vendor.country_name }}</td>
                            </tr>
                            <template v-if="vendor.attributes">
                                <tr v-for="attr in vendor.attributes" :key="attr.slug">
                                    <td>{{ attr.name }}</td>
                                    <td v-if="attr.type == 'datetime' && vendor[attr.slug]">
                                        {{ vendor[attr.slug]['date'] | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}
                                    </td>
                                    <td v-else>{{ vendor[attr.slug] }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <button class="modal-close is-large is-hidden-mobile" aria-label="close" @click="$router.go(-1)"></button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: true,
            vendor: null,
        };
    },
    created() {
        this.$http
            .get(`app/vendors/${this.$route.params.id}`)
            .then(res => {
                this.vendor = res.data;
                this.loading = false;
            })
            .catch(err => {
                this.$event.fire('appError', err.response);
            });
    },
    methods: {},
};
</script>

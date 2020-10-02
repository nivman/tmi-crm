<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card is-medium animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
                <p class="modal-card-title">Customer Details</p>
                <button type="button" class="delete" @click="$router.go(-1)"></button>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <loading v-if="loading"></loading>
                <div v-else>
                    <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                        <tbody>
                            <tr>
                                <td>Name</td>
                                <td>{{ customer.name }}</td>
                            </tr>
                            <tr>
                                <td>Company</td>
                                <td>{{ customer.company }}</td>
                            </tr>
                            <tr>
                                <td>email</td>
                                <td>{{ customer.email }}</td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>{{ customer.phone }}</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td>{{ customer.address }}</td>
                            </tr>
                            <tr>
                                <td>State</td>
                                <td>{{ customer.state_name }}</td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td>{{ customer.country_name }}</td>
                            </tr>
                            <template v-if="customer.attributes">
                                <tr v-for="attr in customer.attributes" :key="attr.slug">
                                    <td>{{ attr.name }}</td>
                                    <td v-if="attr.type == 'datetime' && customer[attr.slug]">
                                        {{ customer[attr.slug]['date'] | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}
                                    </td>
                                    <td v-else>{{ customer[attr.slug] }}</td>
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
            customer: null,
        };
    },
    created() {
        this.$http
            .get(`app/customers/${this.$route.params.id}`)
            .then(res => {
                this.customer = res.data;
                this.loading = false;
            })
            .catch(err => {
                this.$event.fire('appError', err.response);
            });
    },
    methods: {},
};
</script>

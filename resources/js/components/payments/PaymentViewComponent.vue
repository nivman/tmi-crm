<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card is-medium animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
                <p class="modal-card-title">Payment Note</p>
                <button type="button" class="delete" @click="$router.go(-1)"></button>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <loading v-if="loading"></loading>
                <div v-else>
                    <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                        <tbody>
                            <tr>
                                <td>Created at</td>
                                <td>{{ payment.created_at | formatDate }}</td>
                            </tr>
                            <tr>
                                <td>Created by</td>
                                <td>{{ payment.user.name }}</td>
                            </tr>
                            <tr>
                                <td>Reference</td>
                                <td>{{ payment.reference }}</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>{{ payment.amount }}</td>
                            </tr>
                            <tr>
                                <td>Gateway</td>
                                <td>{{ payment.gateway | capitalize }}</td>
                            </tr>
                            <tr>
                                <td>Account</td>
                                <td>{{ payment.account.name }}</td>
                            </tr>
                            <tr>
                                <td>{{ payable() }}</td>
                                <td>{{ payment.payable.name }}</td>
                            </tr>
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
            payment: null,
        };
    },
    created() {
        this.$http
            .get(`app/payments/${this.$route.params.id}`)
            .then(res => {
                this.payment = res.data;
                this.loading = false;
            })
            .catch(err => this.$event.fire('appError', err.response));
    },
    methods: {
        payable() {
            return this.payment.payable_type.split('\\')[1];
        },
    },
};
</script>

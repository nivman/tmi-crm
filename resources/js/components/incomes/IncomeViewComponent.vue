<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card is-medium animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
                <p class="modal-card-title">Income Details</p>
                <button type="button" class="delete" @click="$router.go(-1)"></button>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <loading v-if="loading"></loading>
                <div v-else>
                    <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                        <tbody>
                            <tr>
                                <td>Created at</td>
                                <td>{{ income.created_at | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}</td>
                            </tr>
                            <tr>
                                <td>Title</td>
                                <td>{{ income.title }}</td>
                            </tr>
                            <tr>
                                <td>Reference</td>
                                <td>{{ income.reference }}</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>{{ income.amount | formatNumber }}</td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>{{ income.categories[0].name }}</td>
                            </tr>
                            <tr>
                                <td>Account</td>
                                <td>{{ income.account.name }}</td>
                            </tr>
                            <template v-if="income.attributes">
                                <tr v-for="attr in income.attributes" :key="attr.slug">
                                    <td>{{ attr.name }}</td>
                                    <td v-if="attr.type == 'datetime' && income[attr.slug]">
                                        {{ income[attr.slug]['date'] | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}
                                    </td>
                                    <td v-else>{{ income[attr.slug] }}</td>
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
            income: null,
        };
    },
    created() {
        this.$http
            .get(`app/incomes/${this.$route.params.id}`)
            .then(res => {
                this.income = res.data;
                this.loading = false;
            })
            .catch(err => {
                this.$event.fire('appError', err.response);
            });
    },
};
</script>

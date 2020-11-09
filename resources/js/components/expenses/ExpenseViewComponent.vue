<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card is-medium animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
                <p class="modal-card-title">פרטי הוצאה</p>
                <button type="button" class="delete" @click="$router.go(-1)"></button>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <loading v-if="loading"></loading>
                <div v-else>
                    <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                        <tbody>
                            <tr>
                                <td>נוצרה ב</td>
                                <td>{{ expense.created_at | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}</td>
                            </tr>
                            <tr>
                                <td>כותרת</td>
                                <td>{{ expense.title }}</td>
                            </tr>
                            <tr>
                                <td>הפנייה</td>
                                <td>{{ expense.reference }}</td>
                            </tr>
                            <tr>
                                <td>סכום</td>
                                <td>{{ expense.amount | formatNumber }}</td>
                            </tr>
                            <tr>
                                <td>קטגוריה</td>
                                <td>{{ expense.categories[0].name }}</td>
                            </tr>
                            <tr>
                              <td>פרוייקט</td>
                              <td>{{ expense.project ? expense.project.name : '' }}</td>
                            </tr>
                            <tr>
                                <td>חשבון</td>
                                <td>{{ expense.account.name }}</td>
                            </tr>
                            <template v-if="expense.attributes">
                                <tr v-for="attr in expense.attributes" :key="attr.slug">
                                    <td>{{ attr.name }}</td>
                                    <td v-if="attr.type == 'datetime' && expense[attr.slug]">
                                        {{ expense[attr.slug]['date'] | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}
                                    </td>
                                    <td v-else>{{ expense[attr.slug] }}</td>
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
            expense: null,
        };
    },
    created() {
        this.$http
            .get(`app/expenses/${this.$route.params.id}`)
            .then(res => {
                this.expense = res.data;
                this.loading = false;
            })
            .catch(err => {
                this.$event.fire('appError', err.response);
            });
    },
};
</script>

<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
                <p class="modal-card-title">Payments</p>
                <button type="button" class="delete" @click="hide()"></button>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <p v-if="!payments || payments.length < 1">
                    Only received payments will be listed here and there is no received payment for the selected record. If you have not yet
                    created payment, please <a @click="add(record)">add one</a>.
                </p>
                <div v-else>
                    <table class="table is-striped is-narrow is-hoverable is-fullwidth">
                        <tbody>
                            <tr v-for="p in payments" :key="'p_' + p.id" :class="{ warning: p.received != 1, success: p.received == 1 }">
                                <td>
                                    Date:
                                    <strong> {{ p.created_at | formatDate($store.state.settings.ac.dateformat) }} </strong>, Reference:
                                    <strong> {{ p.reference }} </strong>, Amount:
                                    <strong> {{ p.pivot.amount | formatNumber }} </strong>
                                    <br />
                                    Total Amount: {{ p.amount | formatNumber }}, For: <strong> {{ p.payable.name }} </strong>, By:
                                    <strong> {{ p.user ? p.user.name : 'System' }} </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        payments: {
            type: Array,
            required: true,
        },
        record: {
            type: Object,
            required: true,
        },
        add: {
            type: Function,
            required: true,
        },
        hide: {
            type: Function,
            required: true,
        },
    },
    data() {
        return {};
    },
    created() {},
    methods: {},
};
</script>

<style scoped>
.success td {
    background: #bbffaa;
}
.warning td {
    background: #ffeecc;
}
</style>

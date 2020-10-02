<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
                    <i class="fas fa-cog" />
                </button>
                <router-link
                    to="/purchases/add"
                    class="button is-link is-small is-pulled-right"
                    v-if="!$store.getters.customer && !$store.getters.vendor"
                >
                    <i class="fas fa-plus m-r-sm" /> Create New Purchase
                </router-link>
                Purchases
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block table-body-br">
                <v-server-table
                    :url="url"
                    :columns="columns"
                    :options="options"
                    @loaded="onLoaded"
                    ref="purchasesTable"
                    name="purchasesTable"
                >
                    <template slot="date" slot-scope="props">
                        {{ props.row.date | formatDate(dateformat) }}
                    </template>
                    <template slot="vendor" slot-scope="props">
                        {{ props.row.vendor.name }}
                    </template>
                    <template slot="status" slot-scope="props">
                        <div class="has-text-centered">
                            <span v-if="props.row.draft" class="tag is-warning">Draft</span>
                            <span v-else class="tag is-success"> Finalized</span>
                        </div>
                    </template>
                    <template slot="grand_total" slot-scope="props">
                        <div class="has-text-right">{{ props.row.grand_total | formatNumber }}</div>
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <!-- <router-link :to="'/purchases/'+props.row.id" class="button is-primary is-small">
                                    <i class="fas fa-file-invoice" />
                                    <span class="tooltip-text">View</span>
                                </router-link> -->
                                <a class="button is-primary is-small" :href="'/view/purchase/' + props.row.hash" target="_blank">
                                    <i class="fas fa-file-invoice-dollar" />
                                    <span class="tooltip-text">View</span>
                                </a>
                            </p>
                            <p class="control tooltip">
                                <a @click="payment(props.row)" class="button is-primary is-small">
                                    <i class="fas fa-money-bill"></i>
                                    <span class="tooltip-text">Payments</span>
                                </a>
                            </p>
                            <p class="control tooltip">
                                <a @click="email(props.row.id)" class="button is-info is-small">
                                    <i class="fas fa-envelope"></i>
                                    <span class="tooltip-text">Email</span>
                                </a>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link :to="'/purchases/edit/' + props.row.id" class="button is-warning is-small">
                                    <i class="fas fa-edit"></i>
                                    <span class="tooltip-text">Edit</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.superAdmin">
                                <button type="button" class="button is-danger is-small" @click="deleteRecord(props.row.id)">
                                    <i class="fas fa-trash"></i>
                                    <span class="tooltip-text">Delete</span>
                                </button>
                            </p>
                        </div>
                    </template>
                    <template slot="afterBody">
                        <table-filters-component :filters="filters" :amount="total_amount"></table-filters-component>
                    </template>
                </v-server-table>
            </div>
        </div>
        <panel-filters-component
            v-if="filtering"
            :filters="filters"
            :class="{ loaded: filtering }"
            @hide-panel-filters="filtering = false"
        ></panel-filters-component>
        <payments-modal :record="record" :payments="payments" :add="add_payment" :hide="hise_payment" v-if="show_payments"></payments-modal>
    </div>
</template>

<script>
import mId from '../../mixins/Mid';
import tBus from '../../mixins/Tbus';
import PaymentsModal from '../helpers/PaymentsModal';
export default {
    components: { PaymentsModal },
    mixins: [mId, tBus('app/purchases')],
    data() {
        return {
            record: {},
            payments: [],
            total_amount: 0,
            show_payments: false,
            columns: ['date', 'reference', 'vendor', 'status', 'grand_total', 'actions'],
            filters: new this.$form({
                date: '',
                reference: '',
                vendor: { name: '' },
                draft: '',
                grand_total: '',
                range: 'date',
                date_range: '',
            }),
            options: {
                orderBy: { ascending: false, column: 'date' },
                sortable: ['id', 'date', 'reference', 'draft', 'grand_total'],
                filterable: ['id', 'date', 'reference', 'draft', 'grand_total'],
                columnsClasses: {
                    id: 'w50 has-text-centered',
                    date: 'w150',
                    actions: 'w175 has-text-centered p-x-none',
                    status: 'w100',
                    grand_total: 'w150',
                },
            },
        };
    },
    methods: {
        onLoaded(data) {
            let table = data.data.data;
            this.total_amount = Object.keys(table).reduce(function(sum, key) {
                return sum + parseFloat(table[key].grand_total);
            }, 0);
        },
        email(id) {
            this.$http
                .post(`app/purchases/email/${id}`)
                .then(() => {
                    this.notify('success', 'Purchase has been successfully emailed.');
                })
                .catch(err => this.$event.fire('appError', err.response));
        },
        hise_payment() {
            this.payments = [];
            this.show_payments = false;
        },
        add_payment(row) {
            this.$router.push(`payments/add?purchase_id=${row.id}&payer_id=${row.vendor_id}&payer=vendor&amount=${row.grand_total}`);
        },
        payment(row) {
            if (row.draft) {
                this.notify('warning', 'You cannot add payment for draft orders.');
            } else {
                this.record = row;
                this.loading = true;
                this.$http
                    .get(`app/purchases/${row.id}/payments`)
                    .then(res => {
                        this.payments = res.data;
                        this.show_payments = true;
                    })
                    .catch(err => this.$event.fire('appError', err.response))
                    .finally(() => (this.loading = false));
            }
        },
    },
};
</script>

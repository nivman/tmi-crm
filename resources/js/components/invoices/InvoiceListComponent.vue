<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
                    <i class="fas fa-cog" />
                </button>
                <router-link
                    to="/invoices/add"
                    class="button is-link is-small is-pulled-right"
                    v-if="!$store.getters.customer && !$store.getters.vendor"
                >
                    <i class="fas fa-plus m-l-sm" /> יצירת חשבונית חדשה
                </router-link>
              חשבוניות
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block table-body-br">
                <v-server-table
                    :url="url"
                    :columns="columns"
                    :options="options"
                    ref="invoicesTable"
                    @loaded="onLoaded"
                    name="invoicesTable"
                >
                    <template slot="date" slot-scope="props">
                        {{ props.row.date | formatDate(dateformat) }}
                    </template>
                    <template slot="customer" slot-scope="props">
                        {{ props.row.customer.name }}
                    </template>
                    <template slot="status" slot-scope="props">
                        <div class="has-text-centered">
                            <span v-if="props.row.draft" class="tag is-warning">טיוטה</span>
                            <span v-else class="tag is-success"> Finalised</span>
                        </div>
                    </template>
                    <template slot="grand_total" slot-scope="props">
                        <div class="has-text-right">{{ props.row.grand_total | formatNumber }}</div>
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <!-- <router-link :to="'/invoices/'+props.row.id" class="button is-primary is-small">
                                    <i class="fas fa-file-invoice" />
                                    <span class="tooltip-text">View</span>
                                </router-link> -->
                                <a class="button is-primary is-small" :href="'/view/invoice/' + props.row.hash" target="_blank">
                                    <i class="fas fa-file-alt" />
                                    <span class="tooltip-text">הצגה</span>
                                </a>
                            </p>
                            <p class="control tooltip">
                                <a @click="payment(props.row)" class="button is-primary is-small">
                                    <i class="fas fa-money-bill"></i>
                                    <span class="tooltip-text">תשלום</span>
                                </a>
                            </p>
                            <p class="control tooltip">
                                <a @click="email(props.row.id)" class="button is-info is-small">
                                    <i class="fas fa-envelope"></i>
                                    <span class="tooltip-text">אימייל</span>
                                </a>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link :to="'/invoices/edit/' + props.row.id" class="button is-warning is-small">
                                    <i class="fas fa-edit"></i>
                                    <span class="tooltip-text">ערכיה</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.superAdmin">
                                <button type="button" class="button is-danger is-small" @click="deleteRecord(props.row.id)">
                                    <i class="fas fa-trash"></i>
                                    <span class="tooltip-text">מחיקה</span>
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
    mixins: [mId, tBus('app/invoices')],
    data() {
        return {
            record: {},
            payments: [],
            total_amount: 0,
            show_payments: false,
            columns: ['date', 'reference', 'customer', 'status', 'grand_total', 'actions'],
            filters: new this.$form({
                date: '',
                reference: '',
                customer: { name: '' },
                draft: '',
                grand_total: '',
                range: 'date',
                date_range: '',
            }),
            options: {
                orderBy: { ascending: false, column: 'date' },
                // multiSort: [{ column: 'id', ascending: true }],
                sortable: ['id', 'date', 'reference', 'draft', 'grand_total'],
                filterable: ['id', 'date', 'reference', 'draft', 'grand_total'],
                perPage: 10,
                columnsClasses: {
                    id: 'w50 has-text-centered',
                    date: 'w150',
                    actions: 'w175 has-text-centered p-x-none',
                    status: 'w100',
                    grand_total: 'w150',
                },
              headings: {
                date: 'תאריך יצירה',
                reference: 'הפניה',
                customer: 'לקוח',
                status: 'סטטוס',
                grand_total: 'סכום כולל',
                actions: 'פעולות',

              },
                multiSort: {
                    date: [
                        {
                            column: 'id',
                            matchDir: false,
                        },
                        {
                            column: 'created_at',
                            matchDir: true,
                        },
                    ],
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
                .post(`app/invoices/email/${id}`)
                .then(() => {
                    this.notify('success', 'Invoice has been successfully emailed.');
                })
                .catch(err => this.$event.fire('appError', err.response));
        },
        hise_payment() {
            this.payments = [];
            this.show_payments = false;
        },
        add_payment(row) {
            this.$router.push(`payments/add?invoice_id=${row.id}&payer_id=${row.customer_id}&payer=customer&amount=${row.grand_total}`);
        },
        payment(row) {
            if (row.draft) {
                this.notify('warning', 'You cannot add payment for draft orders.');
            } else {
                this.record = row;
                this.loading = true;
                this.$http
                    .get(`app/invoices/${row.id}/payments`)
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

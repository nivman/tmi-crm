<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
                    <i class="fas fa-cog" />
                </button>
                <router-link
                    to="/payments/add"
                    class="button is-link is-small is-pulled-right"
                    v-if="!$store.getters.customer && !$store.getters.vendor"
                >
                    <i class="fas fa-plus m-r-sm" /> Add Payment
                </router-link>
                Payments
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block table-body-br">
                <v-server-table
                    :url="url"
                    :columns="columns"
                    :options="options"
                    ref="paymentsTable"
                    @loaded="onLoaded"
                    name="paymentsTable"
                >
                    <template slot="created_at" slot-scope="props">
                        {{ props.row.created_at | formatDate }}
                    </template>
                    <template slot="amount" slot-scope="props">
                        <div class="has-text-right">{{ props.row.amount | formatNumber }}</div>
                    </template>
                    <template slot="gateway" slot-scope="props">
                        <span v-if="props.row.gateway == 'offline'">
                            Offline
                            <div class="buttons has-addons is-pulled-right m-none" v-if="!props.row.reviewed_by">
                                <p class="control tooltip">
                                    <a
                                        target="_blank"
                                        :href="'/uploads/payments/' + props.row.file"
                                        class="button is-info is-small m-b-none pointer"
                                    >
                                        <i class="fas fa-external-link-alt" />
                                        <span class="tooltip-text">View Upload</span>
                                    </a>
                                </p>
                                <p class="control tooltip">
                                    <a @click="approve(props.row.hash)" class="button is-success is-small m-b-none pointer">
                                        <i class="fas fa-check"></i>
                                        <span class="tooltip-text">Approve</span>
                                    </a>
                                </p>
                            </div>
                            <span v-else>
                                <a :href="'/uploads/payments/' + props.row.file" target="_blank" class="is-pulled-right">
                                    <i class="fas fa-external-link-alt" />
                                </a>
                            </span>
                        </span>
                        <span v-else>{{ getGateway(props.row.gateway) | capitalize }}</span>
                    </template>
                    <template slot="account" slot-scope="props">
                        {{ props.row.account ? props.row.account.name : '' }}
                    </template>
                    <template slot="for" slot-scope="props">
                        {{
                            props.row.payable
                                ? props.row.payable.name + (props.row.payable.company ? ' (' + props.row.payable.company + ')' : '')
                                : ''
                        }}
                    </template>
                    <template slot="by" slot-scope="props">
                        {{ props.row.user ? props.row.user.name : 'System' }}
                    </template>
                    <template slot="received" slot-scope="props">
                        <div
                            class="has-text-centered"
                            :class="{ 'has-text-success': props.row.received, 'has-text-danger': !props.row.received }"
                        >
                            <i class="fas" :class="props.row.received ? 'fa-check' : 'fa-times'" />
                        </div>
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <!-- <router-link :to="'/payments/'+props.row.id" class="button is-primary is-small">
                                    <i class="fas fa-file-alt" />
                                    <span class="tooltip-text">View</span>
                                </router-link> -->
                                <a class="button is-primary is-small" :href="'/view/payment/' + props.row.hash" target="_blank">
                                    <i class="fas fa-file-alt" />
                                    <span class="tooltip-text">View</span>
                                </a>
                            </p>
                            <p class="control tooltip">
                                <a @click="email(props.row.id)" class="button is-info is-small">
                                    <i class="fas fa-envelope"></i>
                                    <span class="tooltip-text">Email</span>
                                </a>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link :to="'/payments/edit/' + props.row.id" class="button is-warning is-small">
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
        <router-view></router-view>
    </div>
</template>

<script>
import mId from '../../mixins/Mid';
import tBus from '../../mixins/Tbus';
export default {
    mixins: [mId, tBus('app/payments')],
    data() {
        return {
            total_amount: 0,
            columns: ['created_at', 'reference', 'amount', 'gateway', 'account', 'for', 'by', 'received', 'actions'],
            filters: new this.$form({
                created_at: '',
                reference: '',
                amount: '',
                gateway: '',
                account: { name: '' },
                nf1: 'Customer/Vendor',
                user: { name: '' },
                received: '',
                range: 1,
                date_range: '',
            }),
            options: {
                perPage: 10,
                orderBy: { ascending: false, column: 'created_at' },
                sortable: ['id', 'created_at', 'reference', 'amount', 'gateway'],
                columnsClasses: {
                    id: 'w50 has-text-centered',
                    created_at: 'w200',
                    reference: 'w150',
                    amount: 'w150',
                    gateway: 'w150',
                    received: 'w100',
                    actions: 'w150 has-text-centered p-x-none',
                },
                filterable: ['id', 'created_at', 'reference', 'amount', 'gateway', 'received'],
            },
        };
    },
    created() {
        if (this.$route.query.due) {
            this.filters.received = 0;
            this.filters.gateway = '';
        }
        if (this.$route.query.review) {
            this.filters.received = 0;
            this.filters.gateway = 'offline';
        }
    },
    watch: {
        '$route.query.due'(val) {
            this.filters.received = val == 1 ? 0 : '';
            this.search();
        },
        '$route.query.review'(val) {
            if (val == 1) {
                this.filters.received = 0;
                this.filters.gateway = 'offline';
            } else {
                this.filters.received = this.$route.query.due ? 0 : '';
                this.filters.gateway = '';
            }
            this.search();
        },
    },
    methods: {
        onLoaded(data) {
            let table = data.data.data;
            let total_amount = Object.keys(table).reduce(function(sum, key) {
                return sum + parseFloat(table[key].amount);
            }, 0);
            this.total_amount = parseFloat(total_amount);
        },
        getGateway(gateway) {
            if (gateway == 'PayPal_Express') {
                return 'PayPal';
            } else if (gateway == 'PayPal_Rest') {
                return 'PayPal Rest';
            } else if (gateway == 'AuthorizeNetApi_Api') {
                return 'Authorize.net';
            } else {
                return gateway;
            }
        },
        email(id) {
            this.$http
                .post(`app/payments/email/${id}`)
                .then(() => {
                    this.notify('success', 'Payment has been successfully emailed.');
                })
                .catch(err => this.$event.fire('appError', err.response));
        },
        offline(row) {
            return `<div class="buttons has-addons is-centered">
                    <p class="control tooltip">
                        <a class="button is-info is-small" href="/uploads/payments/${row.file}" target="_blank">
                            <i class="fas fa-external-link-alt" />
                            <span class="tooltip-text">View Upload</span>
                        </a>
                    </p>
                    <p class="control tooltip">
                        <a @click="approve('${row.hash}')" class="button is-success is-small">
                            <i class="fas fa-check"></i>
                            <span class="tooltip-text">Approve</span>
                        </a>
                    </p>
                </div>`;
        },
        approve(hash) {
            this.$modal.show('dialog', {
                title: 'Approve Payment!',
                text: 'Have you checked the uploaded file amount against payment amount?',
                buttons: [
                    {
                        title: 'Yes, please approve',
                        class: 'button is-primary is-radiusless is-radius-bottom-left',
                        handler: () => {
                            this.$http
                                .post(`pay/offline/${hash}`, { approve: 1 })
                                .then(res => {
                                    this.$modal.hide('dialog');
                                    if (res.data.success) {
                                        this.search();
                                        this.$store.commit('UPDATE_NOTIFICATION', {
                                            payment_due: this.$store.state.notifications.payment_due - 1,
                                        });
                                        this.$store.commit('UPDATE_NOTIFICATION', {
                                            payment_review: this.$store.state.notifications.payment_review - 1,
                                        });
                                        this.notify('success', 'Payment has been aprroved and marked as received.');
                                    } else {
                                        this.notify('error', 'Request has been failed, please try again later.');
                                    }
                                })
                                .catch(err => this.$event.fire('appError', err.response));
                        },
                    },
                    { title: 'No, please cancel', class: 'button is-warning is-radiusless is-radius-bottom-right' },
                ],
            });
        },
    },
};
</script>

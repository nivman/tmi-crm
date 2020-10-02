<template>
    <div class="wrapper" v-if="!$store.getters.customer && !$store.getters.vendor">
        <div class="columns">
            <div class="column is-one-quarter">
                <chart-card :source="pie" :params="pieParams" @monthChanged="pieParamsMonthChanged" @yearChanged="pieParamsYearChanged" />
            </div>
            <div class="column is-three-quarters">
                <chart-card
                    width="100%"
                    :source="line"
                    :params="lineParams"
                    @monthChanged="lineParamsMonthChanged"
                    @yearChanged="lineParamsYearChanged"
                />
            </div>
        </div>
        <div class="columns">
            <div class="column">
                <chart-card :source="bar" width="100%" :params="barParams" @yearChanged="barParamsYearChanged" />
            </div>
        </div>
    </div>
    <div class="wrapper" v-else>
        <div v-if="$store.getters.customer && $store.getters.vendor">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Dashboard
                </div>
                <div class="panel-block table-body-br p-lg" v-if="customer">
                    <loading v-if="loading"></loading>
                    <div class="tile is-ancestor">
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{ customer.invoice ? customer.invoice.total_amount : 0 | formatNumber }}</span>
                                </p>
                                <p class="subtitle">Invoice Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        customer.invoice ? customer.invoice.total_tax_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Invoice Tax Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{ vendor.purchase ? vendor.purchase.total_amount : 0 | formatNumber }}</span>
                                </p>
                                <p class="subtitle">Purchase Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        vendor.purchase ? vendor.purchase.total_tax_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Purchase Tax Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        customer.payment.received ? customer.payment.received.total_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Total Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        customer.payment.due ? customer.payment.due.total_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Unsettled Amount</p>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="panel-block p-t-none p-r-lg p-b-none p-l-lg">
                    <div class="columns">
                        <div class="column">
                            <router-link to="/invoices" class="button is-link m-r-lg m-b-lg">List all invoices</router-link>
                            <router-link to="/recurrings" class="button is-warning m-r-lg m-b-lg">List all recurring invoice</router-link>
                            <router-link to="/purchases" class="button is-link m-r-lg m-b-lg">List all purchases</router-link>
                            <router-link to="/payments" class="button is-success m-r-lg m-b-lg">List all payments</router-link>
                            <router-link to="/customer" class="button is-info m-r-lg m-b-lg">Update Customer Company Details</router-link>
                            <router-link to="/vendor" class="button is-info m-r-lg m-b-lg">Update Vendor Company Details</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="$store.getters.customer && !$store.getters.vendor">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Customer Dashboard
                </div>
                <div class="panel-block table-body-br p-lg" v-if="customer">
                    <loading v-if="loading"></loading>
                    <div class="tile is-ancestor">
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{ customer.invoice ? customer.invoice.total_amount : 0 | formatNumber }}</span>
                                </p>
                                <p class="subtitle">Total Invoice Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        customer.invoice ? customer.invoice.total_tax_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Total Tax Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        customer.payment.received ? customer.payment.received.total_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Received Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        customer.payment.due ? customer.payment.due.total_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Requested Due Amount</p>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="panel-block p-t-none p-r-lg p-b-none p-l-lg">
                    <div class="columns">
                        <div class="column">
                            <router-link to="/invoices" class="button is-link m-r-lg m-b-lg">List all invoices</router-link>
                            <router-link to="/recurrings" class="button is-warning m-r-lg m-b-lg">List all recurring invoice</router-link>
                            <router-link to="/payments" class="button is-success m-r-lg m-b-lg">List all payments</router-link>
                            <router-link to="/customer" class="button is-info m-r-lg m-b-lg">Update Company Details</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="$store.getters.vendor && !$store.getters.customer">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Vendor Dashboard
                </div>
                <div class="panel-block table-body-br p-lg" v-if="vendor">
                    <loading v-if="loading"></loading>
                    <div class="tile is-ancestor">
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{ vendor.purchase ? vendor.purchase.total_amount : 0 | formatNumber }}</span>
                                </p>
                                <p class="subtitle">Total Purchase Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        vendor.purchase ? vendor.purchase.total_tax_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Total Tax Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        vendor.payment.received ? vendor.payment.received.total_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Received Amount</p>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">
                                    <span class="is-number">{{
                                        vendor.payment.due ? vendor.payment.due.total_amount : 0 | formatNumber
                                    }}</span>
                                </p>
                                <p class="subtitle">Requested Due Amount</p>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="panel-block p-t-none p-r-lg p-b-none p-l-lg">
                    <div class="columns">
                        <div class="column">
                            <router-link to="/purchases" class="button is-link m-r-lg m-b-lg">List all purchases</router-link>
                            <router-link to="/payments" class="button is-success m-r-lg m-b-lg">List all payments</router-link>
                            <router-link to="/vendor" class="button is-info m-r-lg m-b-lg">Update Company Details</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ChartCard from './charts/ChartCard.vue';
export default {
    components: { ChartCard },
    data() {
        return {
            vendor: null,
            loading: true,
            customer: null,
            pie: 'app/charts/pie_chart',
            bar: 'app/charts/bar_chart',
            line: 'app/charts/line_chart',
            barParams: { year: moment().format('YYYY') },
            lineParams: { month: moment().format('MM'), year: moment().format('YYYY') },
            pieParams: { month: moment().format('MM'), year: moment().format('YYYY'), hideFilter: true },
        };
    },
    created() {
        if (this.$store.getters.customer) {
            this.$http
                .get('app/dashboard/customer')
                .then(res => {
                    this.customer = res.data;
                    this.loading = false;
                })
                .catch(err => this.$event.fire('appError', err.response));
        }
        if (this.$store.getters.vendor) {
            this.$http
                .get('app/dashboard/vendor')
                .then(res => {
                    this.vendor = res.data;
                    this.loading = false;
                })
                .catch(err => this.$event.fire('appError', err.response));
        }
    },
    methods: {
        lineParamsMonthChanged(month) {
            this.lineParams.month = month;
        },
        lineParamsYearChanged(year) {
            this.lineParams.year = year;
        },
        pieParamsMonthChanged(month) {
            this.pieParams.month = month;
        },
        pieParamsYearChanged(year) {
            this.pieParams.year = year;
        },
        barParamsYearChanged(year) {
            this.barParams.year = year;
        },
    },
};
</script>

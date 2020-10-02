<template>
    <div class="wrapper">
        <div class="panel panel-border is-shadowless print">
            <p class="panel-heading print-none">
                Invoice Details
                <button type="button" class="button is-primary is-small is-pulled-right" @click="downloadPDF()">Save as PDF</button>
            </p>
            <div class="panel-block p-m-none p-p-none p-b-none">
                <loading v-if="loading"></loading>
                <div class="invoice" id="invoice" v-else>
                    <div class="invoice-header">
                        <div class="columns">
                            <div class="column is-6">
                                <div class="media">
                                    <div class="media-left">
                                        <img class="media-object logo" :src="company.logo" />
                                    </div>
                                    <ul class="media-body list-unstyled">
                                        <li>
                                            <strong>{{ company.name }}</strong>
                                        </li>
                                        <li>{{ company.address }}</li>
                                        <li>{{ company.phone }}</li>
                                        <li>{{ company.email }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="column is-4 is-offset-2">
                                <h1>
                                    Invoice No.
                                    <small>{{ invoice.id }}</small>
                                </h1>
                                <h4 class="text-muted">Date: {{ invoice.date }}</h4>
                                <h4 class="text-muted">Reference: {{ invoice.reference }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-body">
                        <div class="columns">
                            <div class="column is-6">
                                <div class="panel panel-border is-shadowless">
                                    <p class="panel-heading">
                                        Company Details
                                    </p>
                                    <div class="panel-block">
                                        <dl class="dl-horizontal">
                                            <dt>Name :</dt>
                                            <dd>
                                                <strong>{{ company.name }}</strong>
                                            </dd>
                                            <dt>Address :</dt>
                                            <dd>{{ company.address + ' ' + company.state_name + ' ' + company.country_name }}</dd>
                                            <dt>Phone :</dt>
                                            <dd>{{ company.phone }}</dd>
                                            <dt>Email :</dt>
                                            <dd>{{ company.email }}</dd>
                                            <span v-if="extra.cf1_label">
                                                <dt>{{ extra.cf1_label }} :</dt>
                                                <dd>{{ extra.cf1_value }}</dd>
                                            </span>
                                            <span v-if="extra.cf2_label">
                                                <dt>{{ extra.cf2_label }} :</dt>
                                                <dd>{{ extra.cf2_value }}</dd>
                                            </span>
                                            <span v-if="extra.cf3_label">
                                                <dt>{{ extra.cf3_label }} :</dt>
                                                <dd>{{ extra.cf3_value }}</dd>
                                            </span>
                                            <span v-if="extra.cf4_label">
                                                <dt>{{ extra.cf4_label }} :</dt>
                                                <dd>{{ extra.cf4_value }}</dd>
                                            </span>
                                            <span v-if="extra.cf5_label">
                                                <dt>{{ extra.cf5_label }} :</dt>
                                                <dd>{{ extra.cf5_value }}</dd>
                                            </span>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="panel panel-border is-shadowless">
                                    <p class="panel-heading">
                                        Customer Details
                                    </p>
                                    <div class="panel-block">
                                        <dl class="dl-horizontal">
                                            <dt>Name :</dt>
                                            <dd>{{ invoice.customer.name }}</dd>
                                            <dt>Company :</dt>
                                            <dd>{{ invoice.customer.company }}</dd>
                                            <dt>Address :</dt>
                                            <dd>
                                                {{
                                                    invoice.customer.address +
                                                        ' ' +
                                                        invoice.customer.state_name +
                                                        ' ' +
                                                        invoice.customer.country_name
                                                }}
                                            </dd>
                                            <dt>Phone :</dt>
                                            <dd>{{ invoice.customer.phone }}</dd>
                                            <dt>Email :</dt>
                                            <dd>{{ invoice.customer.email }}</dd>
                                            <span v-if="customerAttribites.length">
                                                <span v-for="attr in customerAttribites" :key="attr.slug">
                                                    <dt>{{ attr.key }} :</dt>
                                                    <dd>{{ attr.value }}</dd>
                                                </span>
                                            </span>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-border is-shadowless table-head-br">
                            <p class="panel-heading" style="border-bottom:0;">
                                Services / Products
                            </p>
                            <table class="table is-bordered is-fullwidth is-rounded is-narrow m-b-none">
                                <thead>
                                    <tr>
                                        <th>Item / Details</th>
                                        <th width="10%" class="has-text-centered colfix">Price</th>
                                        <th width="10%" class="has-text-centered colfix">Qty</th>
                                        <th width="10%" class="has-text-centered colfix">Discount</th>
                                        <th width="20%" class="has-text-centered colfix">Tax</th>
                                        <th width="20%" class="has-text-centered colfix">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in invoice.items" :key="item.id">
                                        <td>
                                            {{ item.code }}
                                            <br />
                                            <small class="text-muted">{{ item.name }}</small>
                                        </td>
                                        <td class="has-text-right">
                                            <span class="mono">{{ item.price | formatNumber }}</span>
                                        </td>
                                        <td class="has-text-right">
                                            <span class="mono">{{ item.qty | formatNumber }}</span>
                                        </td>
                                        <td class="has-text-right">
                                            <span class="mono">0.00</span>
                                        </td>
                                        <td>
                                            <div v-for="tax in item.taxes" :key="item.id + '_' + tax.id" class="inline-block m-b-none">
                                                <small class="text-muted">{{ tax.code }}</small>
                                                <span class="is-pulled-right mono">{{ tax.pivot.total_amount | formatNumber }}</span>
                                            </div>
                                        </td>
                                        <td class="has-text-right">
                                            <strong class="mono">{{ item.subtotal | formatNumber }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="panel panel-border is-shadowless">
                            <table class="table is-bordered is-fullwidth is-rounded is-narrow m-b-none">
                                <thead>
                                    <tr>
                                        <th width="20%" class="text-center">Sub Total</th>
                                        <th width="20%" class="text-center">Discount</th>
                                        <th width="20%" class="text-center">Total</th>
                                        <th width="20%" class="text-center">Tax</th>
                                        <th width="20%" class="text-center">Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center rowtotal mono">{{ subTotal | formatNumber }}</td>
                                        <td class="text-center rowtotal mono">{{ invoice.discount | formatNumber }}</td>
                                        <td class="text-center rowtotal mono">{{ invoice.total | formatNumber }}</td>
                                        <td class="text-center rowtotal mono">{{ invoice.total_tax_amount | formatNumber }}</td>
                                        <td class="text-center rowtotal mono">{{ invoice.grand_total | formatNumber }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- <div class="html2pdf__page-break"></div> -->
                        <div class="columns">
                            <div class="column is-7">
                                <div class="panel panel-border is-shadowless" v-if="invoice.note">
                                    <p class="panel-heading">Comment / Note</p>
                                    <div class="panel-block">
                                        {{ invoice.note }}
                                    </div>
                                </div>
                                <!-- <div class="panel panel-border is-shadowless">
                                        <p class="panel-heading">
                                            Payment Method
                                        </p>
                                        <div class="panel-block">
                                            <div class="columns">
                                                <div class="column is-12">
                                                    <p>For your convenience, you may deposite the final amount at one of our banks</p>
                                                    <ul class="list-unstyled">
                                                        <li>Alpha Bank -
                                                            <span class="mono">MO123456789456123</span>
                                                        </li>
                                                        <li>Beta Bank -
                                                            <span class="mono">MO123456789456123</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                            </div>
                            <div class="column is-5">
                                <div class="panel panel-border is-shadowless" v-if="invoiceAttributes.length">
                                    <p class="panel-heading">
                                        Extra Information
                                    </p>
                                    <div class="panel-block">
                                        <dl class="dl-horizontal">
                                            <span v-for="(attr, index) in invoiceAttributes" :key="attr.slug + index">
                                                <dt>{{ attr.key }} :</dt>
                                                <dd>{{ attr.value }}</dd>
                                            </span>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-footer">
                        {{ company.footer }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// import * as html2pdf from "html2pdf.js";
export default {
    data() {
        return {
            company: null,
            loading: true,
            invoice: null,
        };
    },
    created() {
        this.$http
            .get('app/companies/1')
            .then(res => (this.company = res.data))
            .catch(err => this.$event.fire('appError', err.response));
        this.$http
            .get(`app/invoices/${this.$route.params.id}`)
            .then(res => {
                this.invoice = res.data;
                this.loading = false;
            })
            .catch(err => {
                this.$event.fire('appError', err.response);
            });
    },
    computed: {
        extra() {
            return this.company.extra; // ? JSON.parse(this.company.extra) : {};
        },
        subTotal: function() {
            let pta = this.invoice.items.reduce((tax, product) => {
                let rowTax = product.taxes.reduce((a, t) => a + parseFloat(t.pivot.total_amount), 0);
                return parseFloat(tax + rowTax);
            }, 0);
            return parseFloat(this.invoice.total) - pta;
        },
        invoiceAttributes() {
            let attrs = [];
            for (let attr in this.invoice.attributes) {
                if (this.invoice[attr] || this.invoice.attributes[attr]['type'] == 'boolean') {
                    if (this.invoice.attributes[attr]['type'] == 'datetime') {
                        attrs.push({
                            slug: attr,
                            key: this.invoice.attributes[attr]['name'],
                            value: this.$options.filters.formatDate(this.invoice[attr].date),
                        });
                    } else {
                        attrs.push({ slug: attr, key: this.invoice.attributes[attr]['name'], value: this.invoice[attr] });
                    }
                }
            }
            return attrs;
        },
        customerAttribites() {
            let attrs = [];
            for (let attr in this.invoice.customer.attributes) {
                if (this.invoice.customer[attr]) {
                    attrs.push({ slug: attr, key: this.invoice.customer.attributes[attr]['name'], value: this.invoice.customer[attr] });
                }
            }
            return attrs;
        },
    },
    methods: {
        downloadPDF() {
            // var opts = {
            //     margin: 0,
            //     filename: "Invoice.pdf",
            //     image: { type: "jpeg", quality: 1 },
            //     html2canvas: { logging: false }
            //     // html2canvas: { logging: false, useCORS: true, proxy: "http://mim.test/" }
            //     // jsPDF: { format: "a4", orientation: "portrait" }
            // };
            // html2pdf(document.querySelector("#invoice"), opts);
        },
    },
};
</script>

<style lang="scss" scoped>
.invoice {
    width: 100%;
    margin: auto;
    // max-width: 900px;
    .invoice-header {
        padding: 25px 25px 15px;
        h1 {
            margin: 0;
        }
        .media {
            .media-body {
                margin: 0;
                font-size: 0.9em;
            }
        }
    }
    .invoice-body {
        padding: 25px;
        background: #fff;
        border-radius: 10px;
    }
    .invoice-footer {
        color: #999;
        padding: 15px;
        font-size: 0.9em;
        text-align: center;
    }
}
.logo {
    max-height: 70px;
    border-radius: 10px;
}
.inline-block {
    width: 100%;
    display: inline-block;
}
.dl-horizontal {
    margin: 0;
    dt {
        clear: left;
        float: left;
        width: 150px;
        overflow: hidden;
        text-align: right;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    dd {
        margin-left: 160px;
    }
}
.rowamount {
    padding-top: 15px !important;
}
.rowtotal {
    font-size: 1.1em;
    font-weight: bold;
}
.colfix {
    width: 12%;
}
.mono {
    text-align: right;
    // font-family: "Lucida Console", Monaco, monospace;
}
</style>

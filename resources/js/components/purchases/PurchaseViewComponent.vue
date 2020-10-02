<template>
    <div class="wrapper">
        <div class="panel panel-border is-shadowless">
            <p class="panel-heading">
                Invoice Details
                <button type="button" class="button is-primary is-small is-pulled-right" @click="downloadPDF()">Save as PDF</button>
            </p>
            <div class="panel-block">
                <loading v-if="loading"></loading>
                <div class="columns" v-else>
                    <div class="column invoice is-12" id="invoice">
                        <div class="invoice-header">
                            <div class="columns">
                                <div class="column is-6">
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object logo" src="../../../images/acme.png" />
                                        </div>
                                        <ul class="media-body list-unstyled">
                                            <li>
                                                <strong>Acme Corporation</strong>
                                            </li>
                                            <li>Software Development</li>
                                            <li>Field 3, Moon</li>
                                            <li>info@acme.com</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="column is-4 is-offset-2">
                                    <h1>
                                        Invoice
                                        <small>With Credit</small>
                                    </h1>
                                    <h4 class="text-muted">NO : 554775/R1</h4>
                                    <h4 class="text-muted">Date : 01/01/2015</h4>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-body">
                            <div class="columns">
                                <div class="column is-5">
                                    <div class="panel panel-border is-shadowless">
                                        <p class="panel-heading">
                                            Company Details
                                        </p>
                                        <div class="panel-block">
                                            <dl class="dl-horizontal">
                                                <dt>Name :</dt>
                                                <dd>
                                                    <strong>Acme Corporation</strong>
                                                </dd>
                                                <dt>Industry :</dt>
                                                <dd>Software Development</dd>
                                                <dt>Address :</dt>
                                                <dd>Field 3, Moon</dd>
                                                <dt>Phone :</dt>
                                                <dd>123.4456.4567</dd>
                                                <dt>Email :</dt>
                                                <dd>mainl@acme.com</dd>
                                                <dt>Tax NO :</dt>
                                                <dd class="mono">123456789</dd>
                                                <dt>Tax Office :</dt>
                                                <dd>A' Moon</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-7">
                                    <div class="panel panel-border is-shadowless">
                                        <p class="panel-heading">
                                            Customer Details
                                        </p>
                                        <div class="panel-block">
                                            <dl class="dl-horizontal">
                                                <dt>Name :</dt>
                                                <dd>Microsoft Corporation</dd>
                                                <dt>Industry :</dt>
                                                <dd>Software Development</dd>
                                                <dt>Address :</dt>
                                                <dd>One Microsoft Way Redmond, WA 98052-7329, USA</dd>
                                                <dt>Phone :</dt>
                                                <dd>(425) 882-8080</dd>
                                                <dt>Email :</dt>
                                                <dd>contact@microsoft.com</dd>
                                                <dt>Tax NO :</dt>
                                                <dd class="mono">123456789</dd>
                                                <dt>&nbsp;</dt>
                                                <dd>&nbsp;</dd>
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
                                            <th class="text-center colfix">Unit Cost</th>
                                            <th class="text-center colfix">Sum Cost</th>
                                            <th class="text-center colfix">Discount</th>
                                            <th class="text-center colfix">Tax</th>
                                            <th class="text-center colfix">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                Lorem Ipsum Dolor
                                                <br />
                                                <small class="text-muted">The best lorem in town, try it now or leave forever</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$1,000.00</span>
                                                <br />
                                                <small class="text-muted">Before Tax</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$18,000.00</span>
                                                <br />
                                                <small class="text-muted">18 Units</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">- $1,800.00</span>
                                                <br />
                                                <small class="text-muted">Special -10%</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">+ $3,240.00</span>
                                                <br />
                                                <small class="text-muted">VAT 20%</small>
                                            </td>
                                            <td class="text-right">
                                                <strong class="mono">$19,440.00</strong>
                                                <br />
                                                <small class="text-muted mono">$16,200.00</small>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Sit Amet Dolo
                                                <br />
                                                <small class="text-muted">Now you may sit</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$120.00</span>
                                                <br />
                                                <small class="text-muted">Before Tax</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$240.00</span>
                                                <br />
                                                <small class="text-muted">2 Units</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">- $0.00</span>
                                                <br />
                                                <small class="text-muted">-</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">+ $72.00</span>
                                                <br />
                                                <small class="text-muted">VAT:20% S:10%</small>
                                            </td>
                                            <td class="text-right">
                                                <strong class="mono">$312.00</strong>
                                                <br />
                                                <small class="text-muted mono">$240.00</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Sit Amet Dolo
                                                <br />
                                                <small class="text-muted">Now you may sit</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$120.00</span>
                                                <br />
                                                <small class="text-muted">Before Tax</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$240.00</span>
                                                <br />
                                                <small class="text-muted">2 Units</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">- $0.00</span>
                                                <br />
                                                <small class="text-muted">-</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">+ $72.00</span>
                                                <br />
                                                <small class="text-muted">VAT:20% S:10%</small>
                                            </td>
                                            <td class="text-right">
                                                <strong class="mono">$312.00</strong>
                                                <br />
                                                <small class="text-muted mono">$240.00</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Sit Amet Dolo
                                                <br />
                                                <small class="text-muted">Now you may sit</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$120.00</span>
                                                <br />
                                                <small class="text-muted">Before Tax</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$240.00</span>
                                                <br />
                                                <small class="text-muted">2 Units</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">- $0.00</span>
                                                <br />
                                                <small class="text-muted">-</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">+ $72.00</span>
                                                <br />
                                                <small class="text-muted">VAT:20% S:10%</small>
                                            </td>
                                            <td class="text-right">
                                                <strong class="mono">$312.00</strong>
                                                <br />
                                                <small class="text-muted mono">$240.00</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Sit Amet Dolo
                                                <br />
                                                <small class="text-muted">Now you may sit</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$120.00</span>
                                                <br />
                                                <small class="text-muted">Before Tax</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">$240.00</span>
                                                <br />
                                                <small class="text-muted">2 Units</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">- $0.00</span>
                                                <br />
                                                <small class="text-muted">-</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="mono">+ $72.00</span>
                                                <br />
                                                <small class="text-muted">VAT:20% S:10%</small>
                                            </td>
                                            <td class="text-right">
                                                <strong class="mono">$312.00</strong>
                                                <br />
                                                <small class="text-muted mono">$240.00</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="panel panel-border is-shadowless">
                                <table class="table is-bordered is-fullwidth is-rounded is-narrow m-b-none">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sub Total</th>
                                            <th class="text-center">Discount</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Tax</th>
                                            <th class="text-center">Final</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center rowtotal mono">$18,240.00</td>
                                            <td class="text-center rowtotal mono">-$1,800.00</td>
                                            <td class="text-center rowtotal mono">$16,440.00</td>
                                            <td class="text-center rowtotal mono">$3,312.00</td>
                                            <td class="text-center rowtotal mono">$19,752.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- <div class="html2pdf__page-break"></div> -->
                            <div class="columns">
                                <div class="column is-7">
                                    <div class="panel panel-border is-shadowless">
                                        <p class="panel-heading">Comments / Notes</p>
                                        <div class="panel-block">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit repudiandae numquam sit facere
                                            blanditiis, quasi distinctio ipsam? Libero odit ex expedita, facere sunt, possimus consectetur
                                            dolore, nobis iure amet vero.
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-5">
                                    <div class="panel panel-border is-shadowless">
                                        <p class="panel-heading">
                                            Payment Method
                                        </p>
                                        <div class="panel-block">
                                            <div class="columns">
                                                <div class="column is-12">
                                                    <p>For your convenience, you may deposite the final amount at one of our banks</p>
                                                    <ul class="list-unstyled">
                                                        <li>
                                                            Alpha Bank -
                                                            <span class="mono">MO123456789456123</span>
                                                        </li>
                                                        <li>
                                                            Beta Bank -
                                                            <span class="mono">MO123456789456123</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-footer">
                            Thank you for choosing our services.
                            <br />
                            We hope to see you again soon
                            <br />
                            <strong>~ACME~</strong>
                        </div>
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
            loading: true,
            invoice: null,
        };
    },
    created() {
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
            // var el = document.querySelector("#invoice");
            // html2pdf(el, opts);
        },
    },
};
</script>

<style lang="scss" scoped>
.invoice {
    margin: auto;
    // max-width: 900px;
    .invoice-header {
        padding: 25px 25px 15px;
        h1 {
            margin: 0;
        }
        .media {
            .media-body {
                font-size: 0.9em;
                margin: 0;
            }
        }
    }
    .invoice-body {
        border-radius: 10px;
        padding: 25px;
        background: #fff;
    }
    .invoice-footer {
        padding: 15px;
        font-size: 0.9em;
        text-align: center;
        color: #999;
    }
}
.logo {
    max-height: 70px;
    border-radius: 10px;
}
.dl-horizontal {
    margin: 0;
    dt {
        float: left;
        width: 100px;
        overflow: hidden;
        clear: left;
        text-align: right;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    dd {
        margin-left: 110px;
    }
}
.rowamount {
    padding-top: 15px !important;
}
.rowtotal {
    font-size: 1.3em;
}
.colfix {
    width: 12%;
}
.mono {
    font-family: monospace;
}
</style>

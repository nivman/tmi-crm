<template>
  <div class="wrapper">
    <div class="panel panel-default pa">
      <div class="panel-heading">
        <span v-if="!showForm">
          <button @click="showForm = true" class="button is-link is-small is-pulled-right">
            <i class="fas fa-file-alt m-r-sm" /> Show Form
          </button>
        </span>
        <span v-else-if="report">
          <button @click="showForm = false" class="button is-link is-small is-pulled-right">
            <i class="fas fa-chart-line m-r-sm" /> Show Report
          </button>
        </span>
        <span v-if="!showForm && report.title"
          >{{ report.title }} ({{ report.start_date | formatDate($store.state.settings.ac.dateformat) }}
          to
          {{ report.end_date | formatDate($store.state.settings.ac.dateformat) }})</span
        >
        <span v-else>Get Report</span>
      </div>
      <div v-if="showForm">
        <div class="panel-block">
          <p class="block">
            Please get report as you need by customizing the form below
          </p>
        </div>
        <form action="#" autocomplete="off" @submit.prevent="validateForm()" class="is-fullwidth">
          <div class="panel-body">
            <div class="columns is-multiline">
              <div class="column is-half p-b-none">
                <div class="field">
                  <label class="label" for="report">Report</label>
                  <div class="control">
                    <div
                      class="select is-fullwidth"
                      :class="{
                        'is-danger': errors.has('report'),
                      }"
                    >
                      <select v-model="form.report" v-validate="'required'" data-vv-name="report" id="report">
                        <option value="general">General</option>
                        <option value="customer">Customer</option>
                        <option value="user" v-if="$store.getters.superAdmin">User</option>
                        <option value="vendor">Vendor</option>
                        <option value="tax">Tax</option>
                      </select>
                    </div>
                  </div>
                  <div class="help is-danger">
                    {{ errors.first('report') }}
                  </div>
                </div>
              </div>
              <!-- <div class="column is-half p-b-none">
                                <div class="field">
                                    <label class="label" for="account">Account</label>
                                    <div class="control">
                                        <v-select v-model="account" @input="accountChange" :options="accounts" :style="{ width: '100%' }" placeholder="Select Account..." label="name" input-id="account" :class="{'select': true, 'is-danger': errors.has('account')}" name="account">
                                            <template slot="no-options">
                                                Please type to search...
                                            </template>
                                        </v-select>
                                    </div>
                                    <div class="help is-danger">{{ errors.first('account') }}</div>
                                </div>
                            </div> -->
              <div class="column is-half p-b-none">
                <transition-group
                  mode="out-in"
                  tag="div"
                  name="fade"
                  enter-active-class="animated faster fadeInRight"
                  leave-active-class=""
                  appear
                  style="width:100%;"
                >
                  <div class="field" v-if="form.report == 'customer'" key="customer">
                    <label class="label" for="customer">Customer</label>
                    <div class="control">
                      <v-select
                        v-model="customer"
                        @search="searchCustomer"
                        @input="customerChange"
                        :options="customers"
                        :style="{ width: '100%' }"
                        placeholder="Search Customer..."
                        label="name"
                        input-id="customer"
                        :class="{
                          select: true,
                          'is-danger': errors.has('customer'),
                        }"
                        name="customer"
                      >
                        <template slot="no-options">
                          Please type to search...
                        </template>
                      </v-select>
                    </div>
                    <div class="help is-danger">
                      {{ errors.first('customer') }}
                    </div>
                  </div>

                  <div class="field" v-if="form.report == 'vendor'" key="vendor">
                    <label class="label" for="vendor">Vendor</label>
                    <div class="control">
                      <v-select
                        v-model="vendor"
                        @search="searchVendor"
                        @input="vendorChange"
                        :options="vendors"
                        :style="{ width: '100%' }"
                        placeholder="Search Vendor..."
                        label="name"
                        input-id="vendor"
                        :class="{
                          select: true,
                          'is-danger': errors.has('vendor'),
                        }"
                        name="vendor"
                      >
                        <template slot="no-options">
                          Please type to search...
                        </template>
                      </v-select>
                    </div>
                    <div class="help is-danger">
                      {{ errors.first('vendor') }}
                    </div>
                  </div>

                  <div class="field" v-if="$store.getters.superAdmin && form.report == 'user'" key="user">
                    <label class="label" for="user">User</label>
                    <div class="control">
                      <v-select
                        v-model="user"
                        @input="userChange"
                        :options="users"
                        :style="{ width: '100%' }"
                        placeholder="Select User..."
                        label="name"
                        input-id="user"
                        :class="{
                          select: true,
                          'is-danger': errors.has('user'),
                        }"
                        name="user"
                      >
                        <template slot="no-options">
                          Please type to search...
                        </template>
                      </v-select>
                    </div>
                    <div class="help is-danger">
                      {{ errors.first('user') }}
                    </div>
                  </div>
                </transition-group>
              </div>
            </div>
            <div class="columns is-multiline">
              <div class="column is-half p-b-none">
                <div class="field">
                  <label class="label" for="start_date">Start Date</label>
                  <div class="control">
                    <flat-pickr
                      v-model="form.start_date"
                      format="yyyy-MM-dd"
                      name="start_date"
                      id="start_date"
                      class="input"
                      :class="{
                        'is-danger': errors.has('start_date'),
                      }"
                    ></flat-pickr>
                  </div>
                  <div class="help is-danger">
                    {{ errors.first('start_date') }}
                  </div>
                </div>
              </div>
              <div class="column is-half p-b-none">
                <div class="field">
                  <label class="label" for="end_date">End Date</label>
                  <div class="control">
                    <flat-pickr
                      v-model="form.end_date"
                      format="yyyy-MM-dd"
                      name="end_date"
                      id="end_date"
                      class="input"
                      :class="{
                        'is-danger': errors.has('end_date'),
                      }"
                    ></flat-pickr>
                  </div>
                  <div class="help is-danger">
                    {{ errors.first('end_date') }}
                  </div>
                </div>
              </div>
              <div class="column is-12">
                <div class="field">
                  <button type="submit" class="button is-link" :class="{ 'is-loading': isSaving }" :disabled="errors.any()">
                    Submit
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div v-else>
        <div class="panel-block">
          <h1>{{ report.heading }}</h1>
        </div>
        <div class="panel-body">
          <div class="p-md" v-if="form.report == 'general' && report.data">
            <div class="columns is-multiline">
              <div class="column">
                <div class="tile">
                  <span>{{ report.data.income | formatNumber }}</span>
                  Total Income Amount
                </div>
              </div>
              <div class="column">
                <div class="tile">
                  <span>{{ report.data.invoices.total | formatNumber }}</span>
                  Total Invoice Amount
                </div>
              </div>
              <div class="column">
                <div class="tile">
                  <span>{{ report.data.expense | formatNumber }}</span>
                  Total Expense Amount
                </div>
              </div>
              <div class="column">
                <div class="tile">
                  <span>{{ report.data.purchases.total | formatNumber }}</span>
                  Total Purchase Amount
                </div>
              </div>
            </div>

            <div class="columns is-multiline">
              <div class="column">
                <table class="table is-bordered is-rounded is-narrow is-fullwidth">
                  <thead>
                    <tr>
                      <th colspan="2" class="active">
                        Invoice report
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="active bold">
                      <td>Total invoice amount</td>
                      <td class="has-text-right">
                        {{ report.data.invoices.total | formatNumber }}
                      </td>
                    </tr>
                    <tr class="active bold">
                      <td>Total tax amount</td>
                      <td class="has-text-right">
                        {{ report.data.invoices.total_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Total order level tax amount
                      </td>
                      <td class="has-text-right">
                        {{ report.data.invoices.total_order_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Total product level tax amount
                      </td>
                      <td class="has-text-right">
                        {{ report.data.invoices.total_product_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr class="active bold">
                      <td>
                        Total recoverable tax amount
                      </td>
                      <td class="has-text-right">
                        {{ report.data.invoices.total_recoverable_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr class="active bold">
                      <td>
                        Total recoverable tax calculated on
                      </td>
                      <td class="has-text-right">
                        {{ report.data.invoices.total_recoverable_tax_calculated_on | formatNumber }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="column">
                <table class="table is-bordered is-rounded is-narrow is-fullwidth">
                  <thead>
                    <tr>
                      <th colspan="2" class="active">
                        Purchase report
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="active bold">
                      <td>Total purchase amount</td>
                      <td class="has-text-right">
                        {{ report.data.purchases.total | formatNumber }}
                      </td>
                    </tr>
                    <tr class="active bold">
                      <td>Total tax amount</td>
                      <td class="has-text-right">
                        {{ report.data.purchases.total_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Total order level tax amount
                      </td>
                      <td class="has-text-right">
                        {{ report.data.purchases.total_order_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Total product level tax amount
                      </td>
                      <td class="has-text-right">
                        {{ report.data.purchases.total_product_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr class="active bold">
                      <td>
                        Total recoverable tax amount
                      </td>
                      <td class="has-text-right">
                        {{ report.data.purchases.total_recoverable_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr class="active bold">
                      <td>
                        Total recoverable tax calculated on
                      </td>
                      <td class="has-text-right">
                        {{ report.data.purchases.total_recoverable_tax_calculated_on | formatNumber }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="p-md" v-else-if="form.report == 'customer' && report.data">
            <h2 class="m-b-lg">
              Customer: {{ report.data.customer.name }}, {{ report.data.customer.company }} ({{ report.data.customer.phone }},
              {{ report.data.customer.email }}) {{ report.data.customer.state_name }},
              {{ report.data.customer.country_name }}
            </h2>
            <div class="columns is-multiline">
              <div class="column">
                <div class="tile">
                  <span>{{ report.data.balance | formatNumber }}</span>
                  Receivable/Due
                </div>
              </div>
              <div class="column">
                <div class="tile">
                  <span>{{ (report.data.toral_invoice_amount + 0) | formatNumber }}</span>
                  Total Invoice Amount
                </div>
              </div>
              <div class="column">
                <div class="tile">
                  <span>{{ report.data.due_payments | formatNumber(0) }}</span>
                  Due Payments
                </div>
              </div>
            </div>
          </div>
          <div class="p-md" v-else-if="form.report == 'vendor' && report.data">
            <h2 class="m-b-lg">
              Vendor: {{ report.data.vendor.name }}, {{ report.data.vendor.company }} ({{ report.data.vendor.phone }},
              {{ report.data.vendor.email }}) {{ report.data.vendor.state_name }},
              {{ report.data.vendor.country_name }}
            </h2>
            <div class="columns is-multiline">
              <div class="column">
                <div class="tile">
                  <span>{{ report.data.balance | formatNumber }}</span>
                  Payable/Due
                </div>
              </div>
              <div class="column">
                <div class="tile">
                  <span>{{ (report.data.total_purchase_amount + 0) | formatNumber }}</span>
                  Total Purchase Amount
                </div>
              </div>
            </div>
          </div>
          <div class="p-md" v-else-if="form.report == 'user' && report.data">
            <h2 class="m-b-lg">User: {{ report.data.user.name }} ({{ report.data.user.phone }}, {{ report.data.user.email }})</h2>
            <div class="columns is-multiline">
              <div class="column">
                <div class="tile">
                  <span>{{ (report.data.total_income_amount + 0) | formatNumber }}</span>
                  Total Income Amount
                </div>
              </div>
              <div class="column">
                <div class="tile">
                  <span>{{ (report.data.toral_invoice_amount + 0) | formatNumber }}</span>
                  Total Invoice Amount
                </div>
              </div>
              <div class="column">
                <div class="tile">
                  <span>{{ (report.data.total_expense_amount + 0) | formatNumber }}</span>
                  Total Expense Amount
                </div>
              </div>
              <div class="column">
                <div class="tile">
                  <span>{{ (report.data.total_purchase_amount + 0) | formatNumber }}</span>
                  Total Purchase Amount
                </div>
              </div>
            </div>
          </div>
          <div class="p-md" v-if="form.report == 'tax' && report.data">
            <div class="columns is-multiline">
              <div class="column">
                <table class="table is-bordered is-rounded is-narrow is-fullwidth">
                  <thead>
                    <tr>
                      <th colspan="2" class="active">
                        Collected on Invoices (Total Invoice Amount:
                        {{ report.data.invoices.total | formatNumber }})
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- <tr class="active bold">
                                            <td>Total invoice amount</td>
                                            <td class="has-text-right">{{ report.data.invoices.total | formatNumber }}</td>
                                        </tr> -->
                    <tr class="active bold">
                      <td>Total tax amount</td>
                      <td class="has-text-right">
                        {{ report.data.invoices.total_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr class="active bold">
                      <td>
                        Total recoverable tax amount
                      </td>
                      <td class="has-text-right">
                        {{ report.data.invoices.total_recoverable_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <!-- <tr class="active bold">
                                            <td>Total recoverable tax calculated on</td>
                                            <td class="has-text-right">{{ report.data.invoices.total_recoverable_tax_calculated_on | formatNumber }}</td>
                                        </tr> -->
                    <tr v-for="tax in report.data.invoices.taxes">
                      <td>
                        {{ tax.name }} ({{ tax.code }})
                        {{ tax.recoverable ? ' - Recoverable' : '' }}
                      </td>
                      <td class="has-text-right">
                        {{ tax.total | formatNumber }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="column">
                <table class="table is-bordered is-rounded is-narrow is-fullwidth">
                  <thead>
                    <tr>
                      <th colspan="2" class="active">
                        Paid on Purchases (Total Purchase Amount:
                        {{ report.data.purchases.total | formatNumber }})
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- <tr class="active bold">
                                            <td>Total purchase amount</td>
                                            <td class="has-text-right">{{ report.data.purchases.total | formatNumber }}</td>
                                        </tr> -->
                    <tr class="active bold">
                      <td>Total tax amount</td>
                      <td class="has-text-right">
                        {{ report.data.purchases.total_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <tr class="active bold">
                      <td>
                        Total recoverable tax amount
                      </td>
                      <td class="has-text-right">
                        {{ report.data.purchases.total_recoverable_tax_amount | formatNumber }}
                      </td>
                    </tr>
                    <!-- <tr class="active bold">
                                            <td>Total recoverable tax calculated on</td>
                                            <td class="has-text-right">{{ report.data.purchases.total_recoverable_tax_calculated_on | formatNumber }}</td>
                                        </tr> -->
                    <tr v-for="tax in report.data.purchases.taxes">
                      <td>
                        {{ tax.name }} ({{ tax.code }})
                        {{ tax.recoverable ? ' - Recoverable' : '' }}
                      </td>
                      <td class="has-text-right">
                        {{ tax.total | formatNumber }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import _debounce from 'lodash/debounce';
export default {
  data() {
    return {
      users: [],
      user: null,
      report: '',
      vendors: [],
      vendor: null,
      accounts: [],
      account: null,
      loading: true,
      customers: [],
      showForm: true,
      customer: null,
      isSaving: false,
      form: new this.$form({
        start_date: '',
        end_date: '',
        account: '',
        customer: '',
        vendor: '',
        user: '',
        report: 'general',
      }),
    };
  },
  created() {
    this.form.end_date = new Date();
    this.$http
      .get('app/accounts?all=1')
      .then(acc => {
        this.accounts = acc.data;
        if (this.$store.getters.superAdmin) {
          this.$http
            .get('app/users?all=1')
            .then(res => {
              this.users = res.data;
              this.loading = false;
            })
            .catch(err => this.$event.fire('appError', err.response));
        } else {
          this.loading = false;
        }
      })
      .catch(err => this.$event.fire('appError', err.response));
  },
  methods: {
    submit() {
      this.isSaving = true;
      this.$http
        .post('app/report', this.form)
        .then(res => {
          this.isSaving = false;
          this.showForm = false;
          this.report = res.data;
        })
        .catch(err => {
          this.isSaving = false;
          this.$event.fire('appError', err.response);
        });
    },
    validateForm() {
      this.$validator
        .validateAll()
        .then(result => {
          if (result) {
            this.submit();
          }
        })
        .catch(err => this.$event.fire('appError', err));
    },
    accountChange(selected) {
      this.account = selected;
      this.form.account = selected ? selected.id : '';
    },
    getAccount(id) {
      this.$http
        .get(`app/accounts/${id}`)
        .then(res => {
          this.accountChange(res.data);
        })
        .catch(err => this.$event.fire('appError', err.response));
    },
    userChange(selected) {
      this.user = selected;
      this.form.user = selected ? selected.id : '';
    },
    getUser(id) {
      this.$http
        .get(`app/users/${id}`)
        .then(res => {
          this.userChange(res.data);
        })
        .catch(err => this.$event.fire('appError', err.response));
    },
    customerChange(selected) {
      this.customer = selected;
      this.form.customer = selected ? selected.value : '';
    },
    vendorChange(selected) {
      this.vendor = selected;
      this.form.vendor = selected ? selected.value : '';
    },
    searchCustomer(search, loading) {
      if (search) {
        this.getCustomers(search, loading, this);
      }
    },
    getCustomers: _debounce((search, loading, vm) => {
      loading(true);
      vm.$http
        .get('app/customers/search?query=' + search)
        .then(res => {
          vm.customers = res.data;
          loading(false);
        })
        .catch(err => {
          vm.$event.fire('appError', err.response);
        });
    }, 250),
    searchVendor(search, loading) {
      this.getVendors(search, loading, this);
    },
    getVendors: _debounce((search, loading, vm) => {
      loading(true);
      vm.$http
        .get('app/vendors/search?query=' + search)
        .then(res => {
          vm.vendors = res.data;
          loading(false);
        })
        .catch(err => this.$event.fire('appError', err.response));
    }, 250),
  },
};
</script>

<style scoped>
.bold {
  font-weight: bold;
}
tr.active th,
tr.active td {
  background: #fafbfd;
}
.tile {
  padding: 16px;
  display: block;
  border-radius: 5px;
  background: #fafbfc;
  border: 1px solid #eee;
}
.tile span {
  display: block;
  font-size: 2rem;
  font-weight: bold;
}
</style>

<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form autocomplete="off" action="#" @submit.prevent="validateForm()">
      <div class="modal-card is-medium animated fastest zoomIn">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            {{ form.id ? 'Edit Payment' : 'Add New Payment' }}
          </p>
          <button type="button" class="delete" @click="$router.go(-1)"></button>
        </header>
        <section class="modal-card-body is-radius-bottom">
          <loading v-if="loading"></loading>
          <div class="field">
            <checkbox-component id="state" name="state" label="For Vendor" v-model="form.state" :checked="form.state"></checkbox-component>
            <transition
              mode="out-in"
              name="fade"
              enter-active-class="animated faster fadeInDown"
              leave-active-class="animated fastest fadeOutRight"
            >
              <v-select
                label="name"
                class="m-t-sm"
                name="customer"
                input-id="customer"
                v-model="customer"
                :options="customers"
                v-validate="'required'"
                @input="customerChange"
                @search="searchCustomer"
                v-if="state == 'customer'"
                :style="{ width: '100%' }"
                placeholder="Search Customer..."
                :class="{
                  select: true,
                  'is-danger': errors.has('customer'),
                }"
              >
                <template slot="no-options">
                  Please type to search...
                </template>
              </v-select>
            </transition>
            <transition
              mode="out-in"
              name="fade"
              enter-active-class="animated faster fadeInDown"
              leave-active-class="animated fastest fadeOutRight"
            >
              <v-select
                label="name"
                class="m-t-sm"
                name="vendor"
                input-id="vendor"
                v-model="vendor"
                :options="vendors"
                @input="vendorChange"
                @search="searchVendor"
                v-validate="'required'"
                v-if="state == 'vendor'"
                :style="{ width: '100%' }"
                placeholder="Search Vendor..."
                :class="{
                  select: true,
                  'is-danger': errors.has('vendor'),
                }"
              >
                <template slot="no-options">
                  Please type to search...
                </template>
              </v-select>
            </transition>
          </div>
          <div class="field">
            <label class="label" for="reference">Reference</label>
            <input
              class="input"
              id="reference"
              name="reference"
              v-validate="'required'"
              v-model="form.reference"
              :class="{ 'is-danger': errors.has('reference') }"
            />
            <div class="help is-danger">
              {{ errors.first('reference') }}
            </div>
          </div>
          <div class="field">
            <label class="label" for="account">Account</label>
            <div class="control">
              <v-select
                label="name"
                id="account"
                name="account"
                v-model="account"
                input-id="account"
                :options="accounts"
                @input="accountChange"
                v-validate="'required'"
                :style="{ width: '100%' }"
                :class="{
                  select: true,
                  'is-danger': errors.has('account'),
                }"
              >

              </v-select>
            </div>
            <div class="help is-danger">
              {{ errors.first('account') }}
            </div>
          </div>
          <div v-if="form.customer_id" style="margin-bottom:0.75rem;">
            <div class="field">
              <label class="label" for="gateway">Gateway</label>
              <div class="control">
                <div class="select is-fullwidth">
                  <select
                    id="gateway"
                    v-model="form.gateway"
                    :disabled="this.form.received == 1"
                    :class="{
                      'is-danger': errors.has('gateway'),
                    }"
                  >
                    <option value="">Let user select any</option>
                    <option value="cash">Cash</option>
                    <option value="offline">Offline</option>
                    <option value="Stripe">Stripe</option>
                    <option value="PayPal_Pro">PayPal Pro</option>
                    <option value="PayPal_Rest">PayPal Rest</option>
                    <option value="AuthorizeNetApi_Api">Authorize.net</option>
                  </select>
                </div>
              </div>
              <div class="help is-danger">
                {{ errors.first('gateway') }}
              </div>
            </div>
            <div class="field">
              <div class="control">
                <div class="select is-fullwidth">
                  <select
                    id="status"
                    :disabled="true"
                    v-model="form.status"
                    v-validate="'required'"
                    :class="{
                      'is-danger': errors.has('status'),
                    }"
                  >
                    <option value="receiving">
                      {{ this.form.id != '' ? (this.form.received == 1 ? 'Received' : 'Receiving') : 'Receiving' }}
                    </option>
                    <option value="requesting">{{ this.form.id != '' ? 'Requested' : 'Requesting' }}</option>
                  </select>
                </div>
              </div>
              <div class="help is-danger">
                {{ errors.first('status') }}
              </div>
            </div>
          </div>
          <number-input-component
            :value.sync="form.amount"
            :field="{
              label: 'Amount',
              name: 'amount',
              id: 'amount',
              disabled: this.form.received == 1,
            }"
            :validation="{ rules: 'required', name: 'amount' }"
          ></number-input-component>
          <div class="help is-danger">
            {{ errors.first('amount') }}
          </div>
          <div class="field">
            <label class="label" for="note">Note</label>
            <textarea
              rows="2"
              id="note"
              name="note"
              class="textarea"
              v-model="form.note"
              :class="{ 'is-danger': errors.has('note') }"
            ></textarea>
            <div class="help is-danger">
              {{ errors.first('note') }}
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <button
                type="submit"
                class="button is-link is-fullwidth"
                :class="{ 'is-loading': isSaving }"
                :disabled="errors.any() || isSaving"
              >
                Submit
              </button>
            </div>
          </div>
        </section>
      </div>
    </form>
    <button class="modal-close is-large is-hidden-mobile" aria-label="close" @click="$router.go(-1)"></button>
  </div>
</template>

<script>
import _debounce from 'lodash/debounce';
export default {
  data() {
    return {
      payer: null,
      vendors: [],
      accounts: [],
      vendor: null,
      account: null,
      customers: [],
      loading: true,
      customer: null,
      isSaving: false,
      selected: false,
      state: 'customer',
      form: new this.$form({
        id: '',
        note: '',
        amount: '',
        gateway: '',
        state: false,
        vendor_id: '',
        invoice_id: '',
        account_id: '',
        customer_id: '',
        purchase_id: '',
        status: 'requesting',
      }),
    };
  },
  created() {
    this.$http
      .get('app/accounts?all=1')
      .then(account => {
        this.accounts = account.data;
        console.log(this.accounts)
        if (this.$route.query.invoice_id) {
          this.form.invoice_id = this.$route.query.invoice_id;
        }
        if (this.$route.query.purchase_id) {
          this.form.purchase_id = this.$route.query.purchase_id;
        }
        if (this.$route.query.payer && this.$route.query.payer_id) {
          console.log(this.$route.query.payer_id)
          this.$http
            .get(`app/${this.$route.query.payer}s/${this.$route.query.payer_id}`)
            .then(payer => {
              this.payer = payer.data;

              if (this.$route.query.payer == 'customer') {
                this.form.customer_id = this.payer.id;
              } else {
                this.form.vendor_id = this.payer.id;
              }
              this.selected = true;
              this.form.amount = this.$route.query.amount;

              if (this.$route.params.id) {
                this.fetchPayment(this.$route.params.id);
              } else {
                this.loading = false;
              }
            })
            .catch(err => this.$event.fire('appError', err.response));
        } else {
          if (this.$route.params.id) {
            this.fetchPayment(this.$route.params.id);
          } else {
            this.loading = false;
          }
        }
        this.account = this.accounts.find(account => account.id == this.$store.getters.settings.system.default_account_id);
      })
      .catch(err => this.$event.fire('appError', err.response));
  },
  watch: {
    'form.gateway': function(val) {

      this.form.status = val == 'cash' ? 'receiving' : 'requesting';
    },
    'form.state': function(val) {
      if (val) {
        this.customer = null;
        this.customerChange();
        this.state = 'vendor';
      } else {
        this.vendor = null;
        this.vendorChange();
        this.state = 'customer';
      }
    },
  },
  methods: {
    submit() {
      this.isSaving = true;
      if (this.form.id && this.form.id !== '') {
        this.form
          .put(`app/payments/${this.form.id}`)
          .then(() => {
            this.$event.fire('refreshPaymentsTable');
            this.notify('success', 'Payment has been successfully updated.');
            this.$router.push('/payments');
          })
          .catch(err => this.$event.fire('appError', err.response))
          .finally(() => (this.isSaving = false));
      } else {
        this.form
          .post('app/payments')
          .then(({ data }) => {
            if (!data.received) {
              this.$store.commit('UPDATE_NOTIFICATION', {
                payment_due: this.$store.state.notifications.payment_due + 1,
              });
            }
            this.$event.fire('refreshPaymentsTable');
            this.notify('success', 'Payment has been successfully added.');
            this.$router.push('/payments');
          })
          .catch(err => this.$event.fire('appError', err.response))
          .finally(() => (this.isSaving = false));
      }
    },
    fetchPayment(id) {
      this.$http
        .get(`app/payments/${id}`)
        .then(res => {
          if (res.data.gateway != 'paypal' || res.data.gateway != 'stripe' || res.data.gateway != 'authorize' || !res.data.received) {
            this.form = new this.$form(res.data);
            if (res.data.payable_type == 'App\\Customer') {
              this.form.customer_id = res.data.payable_id;
            } else {
              this.form.vendor_id = res.data.payable_id;
            }
            this.account = this.accounts.find(account => account.id == res.data.account_id);
            this.selected = true;
            this.loading = false;
          } else {
            this.notify('warn', `Payment with ${res.data.gateway} gateway can not be edited.`);
            this.$router.go(-1);
          }
        })
        .catch(err => this.$event.fire('appError', err.response));
    },
    accountChange(selected) {
      this.account = selected;
      this.form.account_id = selected ? selected.id : '';
    },
    validateForm() {
      this.$validator
        .validateAll()
        .then(res => (res ? this.submit() : false))
        .catch(err => this.$event.fire('appError', err));
    },
    customerChange(selected) {
      this.customer = selected;
      this.form.customer_id = selected ? selected.value : '';
    },
    vendorChange(selected) {
      this.vendor = selected;
      this.form.vendor_id = selected ? selected.value : '';
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
        .catch(err => {
          vm.$event.fire('appError', err.response);
        });
    }, 250),
  },
};
</script>

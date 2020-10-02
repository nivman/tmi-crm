<template>
  <div class="wrapper">
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <loading v-if="loading"></loading>
          <div class="panel-heading">Profile {{ user.name }}</div>
          <form autocomplete="off" @submit.prevent="validateForm">
            <div class="panel-body">
              <div class="columns is-multiline">
                <div class="column is-half">
                  <div class="field">
                    <label class="label" for="username">Username</label>
                    <input
                      type="text"
                      class="input"
                      id="username"
                      name="username"
                      :readonly="!user.admin"
                      :disabled="!user.admin"
                      v-model="form.username"
                      v-validate="'required'"
                      :class="{
                        'is-danger': errors.has('username'),
                      }"
                    />
                  </div>
                </div>
                <div class="column is-half">
                  <div class="field">
                    <label class="label" for="email">Email</label>
                    <input
                      id="email"
                      type="text"
                      name="email"
                      class="input"
                      v-model="form.email"
                      :readonly="!user.admin"
                      :disabled="!user.admin"
                      v-validate="'required|email'"
                      :class="{
                        'is-danger': errors.has('email'),
                      }"
                    />
                  </div>
                </div>
                <div class="column is-half">
                  <div class="field">
                    <label class="label" for="name">Name</label>
                    <input
                      id="name"
                      name="name"
                      type="text"
                      class="input"
                      v-model="form.name"
                      v-validate="'required'"
                      :class="{
                        'is-danger': errors.has('name'),
                      }"
                    />
                    <div class="help is-danger" v-if="errors.has('name')" v-text="errors.first('name')"></div>
                  </div>
                </div>
                <div class="column is-half">
                  <div class="field">
                    <label class="label" for="phone">Phone</label>
                    <input
                      id="phone"
                      type="text"
                      name="phone"
                      class="input"
                      v-model="form.phone"
                      :class="{
                        'is-danger': errors.has('phone'),
                      }"
                    />
                    <div class="help is-danger" v-if="errors.has('phone')" v-text="errors.first('phone')"></div>
                  </div>
                </div>
                <custom-field-component
                  :attr="attr"
                  :key="attr.slug"
                  v-model="form[attr.slug]"
                  v-for="attr in form.attributes"
                ></custom-field-component>

                <div class="column is-fullwidth" v-if="$store.getters.superAdmin && $store.getters.user.username != form.username">
                  <div class="field" v-if="roles.length > 0 && !loading">
                    <label class="label">Groups</label>
                    <span v-for="role in roles" :key="role.id">
                      <checkbox-component
                        :value="role"
                        name="roles[]"
                        :label="role.name"
                        :checked="hasRole(role)"
                        :id="role.slug + role.id"
                        @input="roleChanges($event, role)"
                      ></checkbox-component>
                    </span>
                  </div>
                  <div class="columns">
                    <div class="column is-half" v-if="showCustomer">
                      <div class="field">
                        <label class="label" for="customer">Customer</label>
                        <div class="control">
                          <v-select
                            label="name"
                            name="customer"
                            v-model="customer"
                            input-id="customer"
                            :options="customers"
                            v-validate="'required'"
                            @search="searchCustomer"
                            @input="customerChange"
                            :style="{
                              width: '100%',
                            }"
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
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('customer') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-half" v-if="showVendor">
                      <div class="field">
                        <label class="label" for="vendor">Vendor</label>
                        <div class="control">
                          <v-select
                            label="name"
                            name="vendor"
                            v-model="vendor"
                            input-id="vendor"
                            :options="vendors"
                            v-validate="'required'"
                            @search="searchVendor"
                            @input="vendorChange"
                            :style="{
                              width: '100%',
                            }"
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
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('vendor') }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="columns">
                    <div class="column is-half">
                      <div class="field">
                        <label class="label" for="password">Password<small>(optional)</small></label>
                        <input
                          class="input"
                          id="password"
                          ref="password"
                          name="password"
                          type="password"
                          v-validate="'min:6'"
                          v-model="form.password"
                          :class="{
                            'is-danger': errors.has('password'),
                          }"
                        />
                        <div class="help is-danger" v-if="errors.has('password')" v-text="errors.first('password')"></div>
                      </div>
                    </div>
                    <div class="column is-half">
                      <div class="field">
                        <label class="label" for="password_confirmation">Confirm Password</label>
                        <input
                          class="input"
                          type="password"
                          id="password_confirmation"
                          name="password_confirmation"
                          v-validate="'confirmed:password'"
                          v-model="form.password_confirmation"
                          :class="{
                            'is-danger': errors.has('password_confirmation'),
                          }"
                        />
                        <div
                          class="help is-danger"
                          v-if="errors.has('password_confirmation')"
                          v-text="errors.first('password_confirmation')"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="column is-fullwidth">
                <div class="columns">
                  <div class="field">
                    <button type="submit" class="button is-link" :disabled="errors.any()">
                      Submit
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
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
      roles: [],
      vendors: [],
      vendor: null,
      loading: true,
      customers: [],
      customer: null,
      isSaving: false,
      showVendor: false,
      showCustomer: false,
      form: new this.$form(),
    };
  },
  created() {
    if (this.$store.getters.superAdmin) {
      this.$http
        .get('app/roles')
        .then(res => {
          this.roles = res.data;
          if (this.$route.params.username) {
            this.fetchUser();
          } else {
            this.form = new this.$form(this.$store.getters.user);
            this.loading = false;
          }
        })
        .catch(err => this.$event.fire('appError', err.response));
    } else {
      if (this.$route.params.username) {
        this.fetchUser();
      } else {
        this.form = new this.$form(this.$store.getters.user);
        this.loading = false;
      }
    }
  },
  computed: {
    user() {
      return this.$store.getters.user;
    },
    username() {
      return this.$route.params.username ? this.$route.params.username : this.$store.getters.user.username;
    },
  },
  watch: {
    $route(to, from) {
      this.fetchUser();
    },
  },
  methods: {
    fetchUser() {
      this.$http
        .get(`app/users/${this.username}`)
        .then(res => this.updateForm(res))
        .catch(err => this.$event.fire('appError', err.response));
    },
    updateForm(res) {
      if (this.roles.length > 0) {
        for (let r of res.data.roles) {
          if (r.slug == 'customer') {
            this.showCustomer = true;
          }
          if (r.slug == 'vendor') {
            this.showVendor = true;
          }
        }
        if (res.data.customer_id) {
          this.$http
            .get(`app/customers/${res.data.customer_id}`)
            .then(({ data }) => {
              this.customer = data;
              this.customers = [data];
            })
            .catch(err => this.$event.fire('appError', err.response));
        }
        if (res.data.vendor_id) {
          this.$http
            .get(`app/vendors/${res.data.vendor_id}`)
            .then(({ data }) => {
              this.vendor = data;
              this.vendors = [data];
            })
            .catch(err => this.$event.fire('appError', err.response));
        }
        res.data.roles = this.roles.filter(r => (res.data.roles.filter(ur => r.id === ur.id).length > 0 ? true : false));
      }
      this.form = new this.$form(res.data);
      this.loading = false;
    },
    validateForm() {
      this.$validator
        .validateAll()
        .then(result => {
          if (result) {
            this.isSaving = true;
            this.form
              .put('app/users/' + this.username)
              .then(res => {
                this.updateForm(res);
                this.notify('success', 'Profile has been successfully update.');
                this.isSaving = false;
              })
              .catch(err => this.$event.fire('appError', err.response));
          }
        })
        .catch(err => this.$event.fire('appError', err));
    },
    hasRole(r) {
      return this.form.roles && this.form.roles.filter(ur => r.id == ur.id).length > 0 ? true : false;
    },
    roleChanges(e, r) {
      this.showVendor = false;
      this.showCustomer = false;
      if (e) {
        this.form.roles.push(r);
      } else {
        this.form.roles = this.form.roles.filter(ur => r.id != ur.id);
      }
      for (let r of this.form.roles) {
        if (r.slug == 'customer') {
          this.showCustomer = true;
        }
        if (r.slug == 'vendor') {
          this.showVendor = true;
        }
      }
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

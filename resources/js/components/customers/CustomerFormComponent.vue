<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form action="#" autocomplete="off" @submit.prevent="validateForm()">
      <div class="modal-card animated fastest zoomIn">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            {{ form.id ? "עריכת לקוח" : "יצירת לקוח חדש" }}
          </p>
          <button
              type="button"
              class="delete"
              @click="$router.go(-1)"
          ></button>
        </header>
        <section class="modal-card-body is-radius-bottom">
          <loading v-if="loading"></loading>
          <div class="columns">
            <div class="column is-half">
              <div class="field">
                <label class="label" for="name">שם</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    class="input"
                    v-model="form.name"
                    validate="'required'"
                    validate.persist="'required'"
                    :class="{ 'is-danger': errors.has('name') }"
                />
                <div
                    class="help is-danger"
                    v-if="errors.has('name')"
                    v-text="errors.first('name')"
                ></div>
              </div>
              <div class="field">
                <label class="label" for="company">חברה</label>
                <input
                    type="text"
                    id="company"
                    class="input"
                    name="company"
                    v-model="form.company"
                    :class="{
                                        'is-danger': errors.has('company')
                                    }"
                />
                <div
                    class="help is-danger"
                    v-if="errors.has('company')"
                    v-text="errors.first('company')"
                ></div>
              </div>
            </div>
            <div class="column is-half">
              <div class="field">
                <label class="label" for="email">אימייל</label>
                <input
                    id="email"
                    type="text"
                    name="email"
                    class="input"
                    v-model="form.email"
                    validate="'email'"
                    validate.persist="'required'"
                    :class="{
                                        'is-danger': errors.has('email')
                                    }"
                />
                <div
                    class="help is-danger"
                    v-if="errors.has('email')"
                    v-text="errors.first('email')"
                ></div>
              </div>
              <div class="field">
                <label class="label" for="phone">טלפון</label>
                <input
                    id="phone"
                    type="text"
                    name="phone"
                    class="input"
                    v-model="form.phone"
                    :class="{
                                        'is-danger': errors.has('phone')
                                    }"
                />
                <div
                    class="help is-danger"
                    v-if="errors.has('phone')"
                    v-text="errors.first('phone')"
                ></div>
              </div>
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="opening_balance"
                >Opening Balance</label
                >
                <input
                    class="input"
                    type="number"
                    id="opening_balance"
                    name="opening_balance"
                    v-model="form.opening_balance"
                    :readonly="form.id ? true : false"
                    :class="{
                                'is-danger': errors.has('opening_balance')
                            }"
                />
                <div class="help is-danger">
                  {{ errors.first("opening_balance") }}
                </div>
              </div>
            </div>

            <div class="column">

              <div class="field">
                <label class="label" for="status"
                >סטטוס</label>
                <select
                    class="input"
                    type=""
                    id="status"
                    name="status"
                    v-model="form.status">
                  <option
                      v-for="status in optionsStatuses"
                      :value="status.id"
                      :selected="status.id === form.status"
                      :style="{background: status.color}">
                    {{ status.name }}
                  </option>
                </select>
                <div class="help is-danger">
                  {{ errors.first("status") }}
                </div>
              </div>
            </div>
          </div>
          <div class="field">
            <label class="label" for="address">כתובת</label>
            <input
                type="text"
                id="address"
                class="input"
                name="address"
                v-model="form.address"
                :class="{ 'is-danger': errors.has('address') }"
            />
            <div
                class="help is-danger"
                v-if="errors.has('address')"
                v-text="errors.first('address')"
            ></div>
          </div>
          <div v-if="attributes">
            <h5 class="cf">שדות נוספים</h5>
            <div class="columns is-multiline">
              <div
                  class="column is-half"
                  v-for="attr in attributes"
                  :key="attr.slug"
              >
                <custom-field-component
                    :attr="attr"
                    v-model="form[attr.slug]"
                ></custom-field-component>
              </div>
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
                הוספה
              </button>
            </div>
          </div>
        </section>
      </div>
    </form>
    <button
        class="modal-close is-large is-hidden-mobile"
        aria-label="close"
        @click="$router.go(-1)"
    ></button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      states: [],
      state: null,
      countries: [],
      loading: true,
      attributes: [],
      status: [],
      optionsStatuses: [],
      isSaving: false,
      country: this.$store.state.settings.ac.country,
      form: new this.$form({
        id: "",
        name: "",
        company: "",
        email: "",
        phone: "",
        opening_balance: 0,
        address: "",
        country: "",
        state: "",
        status: ""
      })
    };
  },
  created() {
    this.$http
        .get("app/countries")
        .then(countries => {
          this.countries = countries.data;
          if (this.$route.params.id) {
            this.fetchCustomer(this.$route.params.id);
          } else {
            this.$http
                .get(`app/customers/create`)
                .then(res => {

                  this.attributes = res.data.attributes;
                  this.optionsStatuses = res.data.statuses;
                  this.loading = false;
                })
                .catch(err =>
                    this.$event.fire("appError", err.response)
                );
            if (this.country) {
              this.countryChange(this.country);
            }
          }
        })
        .catch(err => this.$event.fire("appError", err.response));
  },
  methods: {
    submit() {
      this.isSaving = true;
      if (this.form.id && this.form.id !== "") {

        this.form
            .put(`app/customers/${this.form.id}`)
            .then(() => {
              this.$event.fire("refreshCustomersTable");
              this.notify(
                  "success",
                  "Customer has been successfully updated."
              );
              this.$router.push("/customers");
            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      } else {
        console.log(this.form)
        this.form
            .post("app/customers")
            .then(() => {
              this.$event.fire("refreshCustomersTable");
              this.notify(
                  "success",
                  "Customer has been successfully added."
              );
              this.$router.push("/customers");
            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      }
    },
    fetchCustomer(id) {

      this.$http
          .get(`app/customers/${id}`)
          .then(res => {
            this.attributes = res.data.customer.attributes;
            delete res.data.customer.attributes;
            this.form = new this.$form(res.data.customer);
            let customerStatus = res.data.customer.status;
            this.optionsStatuses = res.data.statuses;
            this.form.status = customerStatus.length > 0 ? customerStatus[0].id : '';
            this.loading = false;
          })
          .catch(err => this.$event.fire("appError", err.response));
    },
    validateForm() {
      this.$validator
          .validateAll()
          .then(result => {
            if (result) {
              this.submit();
            }
          })
          .catch(err => this.$event.fire("appError", err));
    },
    countryChange(selected) {
      if (selected) {
        this.country = selected;
        this.getStates(selected.value);
        this.form.country = selected.value;
      } else {
        this.states = [];
        this.state = null;
        this.form.country = "";
        this.country = selected;
      }
    },
    getStates(country, selectedSate) {
      this.$http
          .get("app/states", {params: {country: country}})
          .then(res => {
            this.states = res.data;
            if (selectedSate) {
              this.state = this.states.find(
                  state => state.value == selectedSate
              );
              this.stateChange(this.state);
            }
          })
          .catch(err => this.$event.fire("appError", err.response));
    },
    stateChange(selected) {
      this.state = selected;
      this.form.state = selected ? selected.value : "";
    }
  }
};
</script>

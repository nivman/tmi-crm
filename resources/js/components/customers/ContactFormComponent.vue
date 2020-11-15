<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form action="#" autocomplete="off" @submit.prevent="validateForm()">
      <div class="modal-card animated is-medium fastest zoomIn">
        <header class="modal-card-head is-radius-top">

          <p class="modal-card-title">
            {{ form.id ? "עריכת איש קשר" : "יצירת  איש קשר" }}
          </p>
          <div class="buttons">
            <div v-if="customerId" class="has-addons is-centered" style="direction: ltr">
              <span class="control tooltip">
                  <a @click="addEvent" class="button is-info is-small">
                    <i class="fas fa-comment-dots"></i>
                    <span class="tooltip-text bottom"> התקשרות חדשה</span>
                  </a>
            </span>
            </div>
            <p class="control tooltip"  style="bottom: 5px; margin-right: 10px;">
              <button
                  type="button"
                  class="delete"
                  @click="$router.go(-1)">
              </button>
            </p>
          </div>
        </header>
        <section class="modal-card-body is-radius-bottom">

          <div class="field">
            <label class="label" for="customer">לקוח</label>
            <v-select
                label="name"
                id="customer"
                name="customer"
                item-value="id"
                item-text="name"
                class="rtl-direction"
                :options="customers"
                @search="searchCustomers"
                v-model="form.customer">
            </v-select>
          </div>
              <div class="field">
                <label class="label" for="firstName">שם פרטי</label>
                <input
                    id="firstName"
                    type="text"
                    name="firstName"
                    class="input"
                    v-model="form.first_name"
                    validate="'required'"
                    validate.persist="'required'"
                    :class="{ 'is-danger': errors.has('firstName') }"/>
                <div class="help is-danger"
                    v-if="errors.has('firstName')"
                    v-text="errors.first('firstName')">
                </div>
              </div>


              <div class="field">
                <label class="label" for="lastName">שם משפחה</label>
                <input
                    type="text"
                    id="lastName"
                    class="input"
                    name="lastName"
                    v-model="form.last_name"
                    :class="{'is-danger': errors.has('lastName')}"/>
                <div
                    class="help is-danger"
                    v-if="errors.has('lastName')"
                    v-text="errors.first('lastName')">
                </div>
              </div>
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
                    :class="{'is-danger': errors.has('email') }"/>
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
                    :class="{ 'is-danger': errors.has('phone') }"/>
                <div
                    class="help is-danger"
                    v-if="errors.has('phone')"
                    v-text="errors.first('phone')">
                </div>
              </div>


              <button
                  type="submit"
                  class="button is-link is-fullwidth"
                  :class="{ 'is-loading': isSaving }"
                  :disabled="errors.any() || isSaving">
                הוספה
              </button>

        </section>
      </div>
    </form>
    <button
        class="modal-close is-large is-hidden-mobile"
        aria-label="close"
        @click="$router.go(-1)"
    ></button>
    <event-form-modal></event-form-modal>

  </div>

</template>

<script>
import EventFormModal from "../calendar/EventFormModal.vue";

export default {
  props:['fromContactList'],
  data() {
    return {
      showExtraFields: false,

      loading: true,

      isSaving: false,
      customers: [],
      contacts: [],
      customerId: null,
      form: new this.$form({
        id: "",
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        customer: "",
        customer_id: ""
      })
    };
  },
  created() {
    if (this.$route.params.id) {
      if(!this.fromContactList) {
        this.getContact(this.$route.params.id);

      }else{
        this.getCustomer(this.$route.params.id);
      }

      this.customerId = this.$route.params.id
    } else {
      this.$http
          .get(`app/customers/create`)
          .then(res => {
            this.loading = false;
          })
          .catch(err =>
              this.$event.fire("appError", err.response)
          );
    }
  },
  methods: {
    searchCustomers (search) {

      if (search === '') {
        return
      }

      this.$http
          .get('app/customers/search?query=' + search)
          .then(res => {
            this.customers = res.data
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    getContact(contactId) {

      this.$http
          .get(`app/contact/${contactId}`)
          .then(res => {
            this.form.id = res.data.contact.id;
            this.form.first_name = res.data.contact.first_name;
            this.form.last_name = res.data.contact.last_name;
            this.form.phone = res.data.contact.phone;
            this.form.email = res.data.contact.email;
            this.form.customer = res.data.customer;
            this.form.customer_id = res.data.customer.id
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    submit() {
      this.isSaving = true;

      if (this.form.id) {

        this.form
            .put(`app/contacts/edit/${this.form.id}`)
            .then(() => {
              this.$event.fire( "refreshContactsTable");
              this.notify(
                  "success",
                  "איש קשר עודכן"
              );
              this.$router.push("/contacts");
            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      } else {
        this.form
            .post("app/contacts")
            .then(() => {
              this.$event.fire( "refreshContactsTable");
              this.notify(
                  "success",
                  "איש קשר נוסף בהצלחה"
              );
              this.$router.push("/contacts");
            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      }
    },
    getCustomer(id) {

      this.$http
          .get(`app/customer/${id}`)
          .then(res => {

            this.form.customer = res.data;
            this.form.customer_id = res.data.id
          })
          .catch(err =>
              this.$event.fire("appError", err.response)
          );
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
    addEvent() {

      this.$modal.show("event-form-modal", {customerId: this.$route.params.id});
    },
  },

  components: {EventFormModal},
};
</script>
<style>
.switch-lead-label {
  display: inline-block;
  position: relative;
  bottom: 10px;
  font-weight: bold;
  vertical-align: text-bottom;
}

.flex-container {
  display: flex;
  flex-wrap: nowrap;

}

.modal-card-head-customer {
  padding-right: 0px !important;
}

.list-enter,
.list-leave-to {
  visibility: hidden;
  height: 0;
  margin: 0;
  padding: 0;
  opacity: 0;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.3s;

}

.bounce-enter-active {
  animation: bounce-in .8s;
}

.bounce-leave-active {
  animation: bounce-in .8s reverse;
}

@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}
</style>
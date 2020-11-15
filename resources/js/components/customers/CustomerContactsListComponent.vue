<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <div class="modal-card is-medium animated fastest zoomIn">
      <header class="modal-card-head is-radius-top">
         <span class="control tooltip bottom">
                  <a @click="addContact" class="button-add">
                    <i class="fas fa fa-plus-circle"></i>
                    <span class="tooltip-text bottom">הוספת איש קשר</span>
                  </a>
        </span>
        <p class="modal-card-title">
          רשימת אנשי קשר
        </p>

        <button type="button" class="delete" @click="goBack"></button>

      </header>
      <section class="modal-card-body is-radius-bottom">
        <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
          <thead>
          <tr>
            <th>שם פרטי</th>
            <th>שם משפחה</th>
            <th>טלפון</th>
            <th>אימייל</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="contact in contacts" :key="contact.first_name">
            <td>
              <router-link :to="'/contacts/edit/' + contact.id" class="button is-small">
                {{contact.first_name}}
              </router-link>
            </td>
            <td>
              {{contact.last_name}}
            </td>
            <td>{{contact.phone}}</td>
            <td>{{contact.email}}</td>
          </tr>
          </tbody>
        </table>
      </section>
    </div>

    <div v-if="showNewContactForm">

      <contact-form-component fromContactList="true"></contact-form-component>
    </div>

  </div>

</template>

<script>
import ContactFormComponent from "./ContactFormComponent";
export default {
  props:['customerId'],
  data() {
    return {
      showNewContactForm: false,
      contacts: [],
      title : ''
    };
  },
  created() {

    this.$http
        .get(`app/contacts/details/${this.customerId}`)
        .then(res => {

          this.contacts = res.data;
        })
        .catch(err =>
            this.$event.fire("appError", err.response)
        );

  },
  methods: {
    goBack() {
      this.$emit("showContacts", true);
    },
    addContact() {

          this.showNewContactForm = this.showNewContactForm !== true
    }
  },
  components: {ContactFormComponent}
};
</script>
<style scoped>
.button-add{
  color: rgba(10, 10, 10, 0.2);

  border: none;
  border-radius: 290486px;
  cursor: pointer;
  pointer-events: auto;
  display: inline-block;
  flex-grow: 0;
  flex-shrink: 0;
  font-size: 20px;
  height: 20px;
  max-height: 20px;
  max-width: 20px;
  min-height: 20px;
  min-width: 20px;
  outline: none;
  position: relative;
  width: 20px;
  margin-left: 5px;

}

.delete::before {
  height: 63%;
  width: 16%;
}

.modal-close::after,
.delete::after {
  height: 16%;
  width: 63%;
}
</style>
<template>
  <div class="modal is-active customer-form-modal">
    <div class="modal-background"></div>
    <form action="#" autocomplete="off" @submit.prevent="validateForm()">
      <div class="modal-card animated fastest zoomIn">
        <header class="modal-card-head is-radius-top">
          <span style="left: 10px; position: relative;"> <Lightbulb :isOn="!form.is_lead"/></span>
          <p class="modal-card-title">
            {{ form.id ? "עריכת לקוח" : "יצירת לקוח חדש" }}
          </p>
          <div class="buttons">
            <div v-if="customerId" class="buttons has-addons is-centered" style="direction: ltr">
              <div class="button-child">
                <span class="control tooltip">
                      <a @click="statusHistory" class="button is-success is-small">
                        <i class="fas fa-history"></i>
                        <span class="tooltip-text bottom">היסטוריית סטטוסים</span>
                      </a>
                </span>
              </div>
              <div class="button-child">
                <span class="control tooltip">
                    <a @click="showContacts" class="button is-warning is-small">
                      <i style="color: white" class="fas fa-user-friends"></i>
                      <span class="tooltip-text bottom">אנשי קשר</span>
                    </a>
                </span>
              </div>
              <div class="button-child">
                <span class="control tooltip">
                  <a @click="addEvent" class="button is-info is-small">
                    <i class="fas fa-comment-dots"></i>
                    <span class="tooltip-text bottom"> התקשרות חדשה</span>
                  </a>
               </span>
                <span class="control tooltip">
                  <a @click="showEvents" class="button is-success is-small">
                    <i class="far fa-comments"></i>
                    <span class="tooltip-text bottom">רשימת התקשרויות</span>
                  </a>
                </span>
              </div>
              <div class="button-child">
                <span class="control tooltip">
                  <a @click="addTask" class="button is-warning	has-text-white is-small">
                 <i class="fas fa-thumbtack"></i>
                    <span class="tooltip-text bottom"> משימה חדשה</span>
                  </a>
               </span>
                <span class="control tooltip">
                  <a @click="showTasks" class="button is-danger has-text-white is-small">
                   <i class="fas fa-tasks"></i>
                    <span class="tooltip-text bottom">רשימת משימות</span>
                  </a>
                </span>
              </div>
              <div class="button-child">
                <span class="control tooltip">
                  <a @click="addProject" class="button is-success	has-text-white is-small">
                 <i class="far fa-edit"></i>
                    <span class="tooltip-text bottom"> פרוייקט חדש</span>
                  </a>
               </span>
                <span class="control tooltip">
                  <a @click="showProjects" class="button is-info has-text-white is-small">
                   <i class="fas fa-th-list"></i>
                    <span class="tooltip-text bottom">רשימת פרוייקטים</span>
                  </a>
                </span>
              </div>
              <div class="button-child">
                  <span class="control tooltip">
                    <a @click="addFile" class="button is-primary is-small">
                      <i class="far fa-file"></i>
                      <span class="tooltip-text bottom">העלת קבצים</span>
                    </a>
                  </span>
                <span style="margin-bottom: 0px;" class="control tooltip">
                    <a @click="showFiles" class="button is-danger is-small">
                      <i class="far fa-copy"></i>
                      <span class="tooltip-text bottom">רשימת קבצים</span>
                    </a>
                </span>
              </div>
            </div>
            <p class="control tooltip" style="bottom: 10px; margin-right: 10px;">
              <button
                  type="button"
                  class="delete"
                  @click="closeModal">
              </button>
            </p>
          </div>
        </header>
        <section class="modal-card-body is-radius-bottom">
          <loading v-if="loading"></loading>
          <div class="field">
            <p class="switch-lead-label"> לקוח: </p>
            <RockerSwitch id="is_lead"
                          :toggle="false"
                          name="is_lead"
                          :size="0.6"
                          style="display: inline"
                          labelOn="לא" labelOff="כן"
                          backgroundColorOff="#0084d0"
                          backgroundColorOn="#bd5757"
                          :value="form.is_lead"
                          @change="isOn => (form.is_lead = isOn)"/>
          </div>
          <hr style="margin: 0.1rem 0;">
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
                    :class="{ 'is-danger': errors.has('name') }"/>
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
                    :class="{'is-danger': errors.has('company')}"/>
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
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="opening_balance">תקציב</label>
                <input
                    class="input"
                    type="number"
                    id="opening_balance"
                    name="opening_balance"
                    v-model="form.opening_balance"
                    :readonly="form.id ? true : false"
                    :class="{'is-danger': errors.has('opening_balance')}"/>
                <div class="help is-danger">
                  {{ errors.first("opening_balance") }}
                </div>
              </div>
            </div>

            <div class="column">

              <div class="field">
                <label class="label" for="status">סטטוס</label>
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
                :class="{ 'is-danger': errors.has('address') }"/>
            <div
                class="help is-danger"
                v-if="errors.has('address')"
                v-text="errors.first('address')"
            ></div>
          </div>
          <header class="modal-card-head modal-card-head-customer">
            <div>
              <VueToggles
                  style="direction: ltr; margin-left: 9px;"
                  @click="displayExtraFields"
                  :value="showExtraFields"
                  height="20"
                  width="60"
                  fontSize="12"
                  checkedColor="#000000"
                  uncheckedColor="#000000"
                  fontWeight="bold"
                  checkedText="הסתר"
                  uncheckedText="הצג"
                  checkedBg="#b4d455"
                  uncheckedBg="lightgrey"/>
            </div>
            <h5 class="modal-card-title">שדות נוספים</h5>
          </header>
          <transition

              v-on:before-enter="beforeEnter"
              v-on:enter="enter"
              v-on:leave="leave"
              v-bind:css="false">
            <div class="animated zoomIn" style="overflow:auto; height: max-content " v-show="showExtraFields">

              <section class="modal-card-body  zoomIn">

                <div class="field">
                  <label class="label" for="arrivalSource">מקורות הגעה</label>
                  <select
                      class="input"
                      type=""
                      id="arrivalSource"
                      name="arrivalSource"
                      v-model="form.arrivalSource">
                    <option
                        v-for="arrivalSource in arrivalSources"
                        :value="arrivalSource.id"
                        :selected="arrivalSource.id === form.arrivalSource"
                        :style="{background: arrivalSource.color}">
                      {{ arrivalSource.name }}
                    </option>
                  </select>
                  <div class="help is-danger">
                    {{ errors.first("arrivalSources") }}
                  </div>
                </div>
                <div v-if="attributes">
                  <div class="columns is-multiline">
                    <div
                        class="column is-half"
                        v-for="attr in attributes"
                        :key="attr.slug">
                      <custom-field-component
                          :attr="attr"
                          v-model="form[attr.slug]"
                      ></custom-field-component>
                    </div>
                  </div>
                </div>
              </section>
            </div>
          </transition>
          <div class="columns">
            <div class="column">
              <button
                  type="submit"
                  class="button is-link is-fullwidth"
                  :class="{ 'is-loading': isSaving }"
                  :disabled="errors.any() || isSaving">
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
    <event-form-modal></event-form-modal>
    <div v-if="showStatusHistory">
      <status-history-view-component
          entityType="customer"
          :entityId="customerId"
          :entityName="form.name"
          @showStatusesHistory="showStatusesHistory">
      </status-history-view-component>
    </div>
    <div v-if="showContactsList">
      <customer-contacts-list :customerId="customerId" @showContacts="showContacts"></customer-contacts-list>
    </div>
    <div v-if="showFilesList">
      <customers-files-list-component :customerId="customerId" @showFiles="showFiles"></customers-files-list-component>
    </div>
    <div v-if="showProjectForm">
      <project-form-modal :customerId="customerId" :customerName="customerName"></project-form-modal>
    </div>
    <div v-if="showTaskForm">
      <task-form-modal :cusId="customerId" modal="customers"></task-form-modal>
    </div>
    <div class="modal is-active" v-if="showProjectsList">
      <div class="modal-background"></div>
      <div style="margin: 10%" class="animated fastest zoomIn">
        <header style="direction: ltr" class="modal-card-head is-radius-top">
          <button type="button" class="delete" @click="showProjects"></button>
        </header>
        <section class="modal-card-body is-radius-bottom customer-form-task-list">
          <project-list-component
              :customerName="customerName"
              :customerId="customerId"
          ></project-list-component>
        </section>
      </div>
    </div>
    <div class="modal is-active" v-if="showTasksList">
      <div class="modal-background"></div>
      <div style="margin: 10%" class="animated fastest zoomIn">
        <header style="direction: ltr" class="modal-card-head is-radius-top">
          <button type="button" class="delete" @click="showTasks"></button>
        </header>
        <section class="modal-card-body is-radius-bottom customer-form-task-list">
          <task-list-component
              :customerName="customerName"
              :customerId="customerId"
          ></task-list-component>
        </section>
      </div>
    </div>
    <div class="modal is-active" v-if="showEventsList">
      <div class="modal-background"></div>
      <div style="margin: 10%" class="animated fastest zoomIn">
        <header style="direction: ltr" class="modal-card-head is-radius-top">
          <button type="button" class="delete" @click="showEvents"></button>
        </header>
        <section class="modal-card-body is-radius-bottom customer-form-event-list">
          <events-list-component
              :customerName="customerName"
              :customerId="customerId"
              @showEvents="showEvents"
              modal="customer"></events-list-component>
        </section>
      </div>
    </div>
    <input @change="selectFile" id="fileUpload" type="file" hidden>
  </div>

</template>

<script>
import EventFormModal from "../calendar/EventFormModal.vue";
import RockerSwitch from "vue-rocker-switch";
import "vue-rocker-switch/dist/vue-rocker-switch.css";
import "animate.css"
import Lightbulb from "../Lightbulb";
import StatusHistoryViewComponent from "../statusHistory/StatusHistoryViewComponent";
import CustomerContactsList from "./CustomerContactsListComponent";
import CustomersFilesListComponent from "../files/CustomersFilesListComponent"
import VueToggles from 'vue-toggles';
import Velocity from 'velocity-animate'
import EventsListComponent from "../events/EventsListComponent";
import TaskFormModal from "../tasks/TaskFormModal";
import TaskListComponent from "../tasks/TaskListComponent";
import ProjectFormModal from "../projects/ProjectFormModal";
import ProjectListComponent from "../projects/ProjectListComponent";

export default {
  props: ['popupCustomerId'],
  data() {
    return {
      customerName: null,
      showExtraFields: false,
      states: [],
      loading: true,
      attributes: [],
      status: [],
      arrivalSources: [],
      optionsStatuses: [],
      isSaving: false,
      showProjectForm: false,
      showTaskForm: false,
      showTasksList: false,
      showProjectsList: false,
      showStatusHistory: false,
      showContactsList: false,
      showFilesList: false,
      showEventsList: false,
      createLead: false,
      customerId: null,
      file: null,
      form: new this.$form({
        id: "",
        name: "",
        company: "",
        email: "",
        phone: "",
        opening_balance: 0,
        address: "",
        status: "",
        arrivalSource: "",
        is_lead: true,
      })
    };
  },
  created() {

    if (this.popupCustomerId !== undefined) {
      this.fetchCustomer(this.popupCustomerId);
      this.customerId = this.popupCustomerId
    } else if (this.$route.params.id) {
      this.customerId = this.$route.params.id;
      this.fetchCustomer(this.customerId);

    } else {
      this.$http
          .get(`app/customers/create`)
          .then(res => {

            this.attributes = res.data.attributes;
            this.optionsStatuses = res.data.statuses;
            this.arrivalSources = res.data.arrivalSources
            this.loading = false;
          })
          .catch(err =>
              this.$event.fire("appError", err.response)
          );
    }
  },
  methods: {
    beforeEnter: function (el) {
      el.style.opacity = 0
      el.style.width = 0
      el.style.height = 0;
    },
    enter: function (el, done) {
      Velocity(el, {opacity: 1, width: "auto", height: 300}, {duration: "fast"})
    },
    leave: function (el, done) {
      Velocity(el, {opacity: 0, width: 0, height: 0}, {duration: "slow"})
    },
    displayExtraFields() {
      this.showExtraFields = !this.showExtraFields;
    },
    submit() {
      this.isSaving = true;
      console.log(this.$route)
      let path = (!this.$route || this.$route.name === 'lead') ? "/leads" : "/customers";
      let refreshTable = (!this.$route || this.$route.name === 'lead')? "refreshLeadsTable" : "refreshCustomersTable";
      console.log(path)
      console.log(refreshTable)
      if (this.form.id && this.form.id !== "") {

        this.form
            .put(`app/customers/${this.form.id}`)
            .then(() => {
              this.$event.fire(refreshTable);
              this.notify(
                  "success",
                  "לקוח עודכן"
              );
              if (this.$route) {
                this.$router.push(path);
              }else {
                this.closeModal();
              }

            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      } else {
        this.form
            .post("app/customers")
            .then(() => {
              this.$event.fire(refreshTable);
              this.notify(
                  "success",
                  "לקוח נוסף בהצלחה"
              );
              this.$router.push(path);
            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      }
    },
    fetchCustomer(id) {

      this.$http
          .get(`app/customers/${id}`)
          .then(res => {
            this.modalShow = true
            this.customerName = res.data.customer.name;
            this.attributes = res.data.customer.attributes;
            delete res.data.customer.attributes;
            this.form = new this.$form(res.data.customer);
            let customerStatus = res.data.customer.status;
            this.optionsStatuses = res.data.statuses;
            this.arrivalSources = res.data.arrivalSources
            this.form.status = customerStatus.length > 0 ? customerStatus[0].id : '';

            this.form.arrivalSource = res.data.customer.arrival_source_id;
            this.loading = false;
            this.form.is_lead = res.data.customer.is_lead === 0 ? false : true

          })
          .catch(err => this.$event.fire("appError", err.response));
    },
    closeModal() {
      if (!this.popupCustomerId) {
        this.$router.go(-1)
      } else {
        let modal = document.querySelector(".customer-form-modal")
        modal.parentNode.removeChild(modal);

      }

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
    addTask() {
      this.showTaskForm = this.showTaskForm !== true
    },
    addProject() {
      this.showProjectForm = this.showProjectForm !== true
    },
    addFile() {
      document.getElementById("fileUpload").click()
    },
    showTasks() {
      this.showTasksList = this.showTasksList !== true
    },
    showEvents() {
      this.showEventsList = this.showEventsList !== true
    },
    showProjects() {
      this.showProjectsList = this.showProjectsList !== true
    },
    showFiles() {
      this.showFilesList = this.showFilesList !== true
    },
    statusHistory() {
      this.showStatusHistory = this.showStatusHistory !== true;
    },
    showContacts() {
      this.showContactsList = this.showContactsList !== true
    },
    showStatusesHistory: function () {
      this.showStatusHistory = false

    },
    selectFile(event) {

      this.file = event.target.files[0];
      const upload = new FormData();
      upload.append('file', this.file);
      upload.append('customerId', this.customerId);
      this.$http
          .post(`app/file/customers`, upload)

          .then(res => {

            this.notify(
                "success",
                "קובץ נוסף בהצלחה"
            );
          })
          .catch(err => this.$event.fire("appError", err.response));
    },
  },

  components: {
    EventFormModal,
    TaskFormModal,
    TaskListComponent,
    ProjectListComponent,
    ProjectFormModal,
    CustomerContactsList,
    EventsListComponent,
    RockerSwitch,
    Lightbulb,
    StatusHistoryViewComponent,
    VueToggles,
    Velocity,
    CustomersFilesListComponent
  },
};
</script>
<style scoped>
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

.customer-form-event-list .wrapper {
  margin-top: 1px !important;
}

.customer-form-task-list .wrapper {
  margin-top: 1px !important;
}

.customer-form-event-list .wrapper .panel .table-body-br {
  overflow-y: scroll;
  position: relative;
  max-height: 300px;
}

.customer-form-task-list .wrapper .panel .table-body-br {
  overflow-y: hidden;
  position: relative;

}

.button-child {
  margin: 0px 2px 0px 2px;
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;

}

.customer-form-task-list {
  max-height: 500px;
}

.customer-form-event-list {
  max-height: 500px;
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
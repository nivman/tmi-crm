<template>
  <div class="modal is-active task-form-modal">
    <div class="modal-background"></div>
    <form action="#" autocomplete="off" @submit.prevent="validateForm()">
      <div class="modal-card animated fastest zoomIn">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            {{ form.id ? 'עריכת משימה' : 'יצירת משימה' }}
          </p>
          <div class="buttons duplicate-icon"  v-if="form.id">
            <div class="buttons has-addons" style="direction: ltr">
              <div class="button-child">
                <span class="control tooltip">
                           <a @click="addEvent" class="fas fa-comment-dots is-small button is-info">
                <span class="tooltip-text bottom"> התקשרות חדשה</span>
              </a>
                </span>
              </div>
              <div class="button-child">
                <span class="control tooltip">
                     <a @click="duplicateTask" class="far fa-copy is-small button is-success">
                <span class="tooltip-text bottom">שכפול משימה</span>
              </a>
                </span>
              </div>
            </div>
          </div>
          <div class="buttons is-centered">
            <p class="control tooltip recurring-schedule-icon" v-if="!form.id">
              <a @click="openRecurringSchedule" class="far fa-calendar recurring-schedule-a"></a>

            </p>
            <p class="control tooltip">
              <button
                  type="button"
                  class="delete"
                  @click="closeModal"
              ></button>
            </p>

          </div>
        </header>
        <section class="modal-card-body is-radius-bottom">
          <loading v-if="loading"></loading>
          <div class="columns">
            <div class="column">
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
            </div>
            <div class="column">
              <div class="field">
                <label class="label" for="contact">איש קשר</label>
                <v-select
                    label="full_name"
                    id="contact"
                    name="contact"
                    item-value="id"
                    item-text="full_name"
                    class="rtl-direction"
                    :options="contacts"
                    @search="searchContacts"
                    v-model="form.contact">
                </v-select>
              </div>
            </div>
            <div class="column">
              <div class="field">
                <label class="label" for="project">פרויקט</label>
                <v-select
                    label="name"
                    id="project"
                    name="project"
                    item-value="id"
                    item-text="name"
                    class="rtl-direction"
                    :options="projects"
                    @search="searchProjects"
                    v-model="form.project">
                </v-select>
              </div>
            </div>
          </div>

          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="name">כותרת</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    class="input"
                    v-model="form.name"
                    validate="'required'"
                    validate.persist="'required'"
                    :class="{ 'is-danger': errors.has('name') }"/>
                <div class="help is-danger"
                     v-if="errors.has('name')"
                     v-text="errors.first('name')">
                </div>
              </div>

              <div class="field">
                <label class="label" for="details">פרטים</label>
                <textarea
                    rows="3"
                    id="details"
                    name="details"
                    class="textarea"
                    v-model="form.details"
                    :class="{ 'is-danger': errors.has('details') }">
                </textarea>
                <div class="help is-danger">
                  {{ errors.first('details') }}
                </div>
              </div>
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="categories">קטגוריה</label>
                <v-select
                    label="name"
                    id="categories"
                    name="categories"
                    class="rtl-direction"
                    item-value="id"
                    item-text="name"
                    :options="categories"
                    v-model="form.category">
                </v-select>
              </div>
            </div>
            <div class="column">
              <div class="field">
                <label class="label" for="actual_time">זמן בפועל בדקות</label>
                <input
                    id="actual_time"
                    type="text"
                    name="actual_time"
                    class="input"
                    v-model="form.actual_time"/>
              </div>
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="priority">דחיפות</label>
                <v-select
                    label="name"
                    id="priority"
                    name="priority"
                    class="rtl-direction"
                    item-value="id"
                    item-text="name"
                    return-object
                    single-line
                    :options="priorities"
                    v-model="form.priority">
                  <template v-slot:option="priorities">
                    <div v-html="priorities.name"
                         :style="{
                      background: priorities.color,
                      textAlign: 'center',
                      display: 'block',
                      marginLeft:'-20px',
                      marginRight:'-20px'
                    } "></div>
                  </template>
                </v-select>
              </div>
            </div>
            <div class="column">
              <div class="field">
                <label class="label" for="status">סטטוס</label>

                <v-select
                    label="name"
                    id="status"
                    name="status"
                    class="rtl-direction"
                    item-value="id"
                    item-text="name"
                    return-object
                    single-line
                    :options="optionsStatuses"
                    v-model="form.status">
                </v-select>
                <div class="help is-danger">
                  {{ errors.first('status') }}
                </div>
              </div>
            </div>
          </div>

          <div class="columns">
            <div class="modal-card test">
              <header class="modal-card-head">
                <p class="modal-card-title">
                  תזכורות
                </p>
              </header>
              <section class="modal-card-body">
                <div class="columns">
                  <div class="column"
                       :style="{background: !this.repeatTask ? 'white': 'lightgrey',pointerEvents :!this.repeatTask ? '': 'none'}">
                    <div class="field">
                      <label class="label" for="start_date">זמן התחלה</label>
                      <flat-pickr
                          class="input"
                          id="start_date"
                          name="start_date"
                          :config="config"
                          v-model="form.start_date"
                          :class="{ 'is-danger': errors.has('start_date') }">
                      </flat-pickr>
                      <div class="help is-danger">
                        {{ errors.first('start_date') }}
                      </div>
                    </div>
                  </div>

                  <div class="column"
                       :style="{background: !this.repeatTask ? 'white': 'lightgrey',pointerEvents :!this.repeatTask ? '': 'none'}">

                    <div class="field">
                      <label class="label" for="end_date">זמן סיום</label>
                      <flat-pickr

                          class="input"
                          id="end_date"
                          name="end_date"
                          enableTime="true"
                          :config="config"
                          v-model="form.end_date"
                          :class="{'is-danger': errors.has('end_date'),}"
                      ></flat-pickr>
                      <div class="help is-danger">
                        {{ errors.first('end_date') }}
                      </div>
                    </div>
                  </div>
                  <div class="column">
                    <div class="field">
                      <label class="label" for="notification_time">תזכורת לפני בשעות</label>
                      <flat-pickr

                          class="input"
                          id="notification_time"
                          name="notification_time"
                          enableTime="true"
                          :config="config"
                          v-model="form.notification_time"
                          :class="{'is-danger': errors.has('notification_time'),}"
                      ></flat-pickr>
                      <div class="help is-danger">
                        {{ errors.first('notification_time') }}
                      </div>
                    </div>
                  </div>
                </div>
                <p>{{ recurrenceText }}</p>
              </section>
            </div>
          </div>
          <div v-if="attributes.length > 0">
            <h5 class="cf">שדות נוספים</h5>
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
            <div class="column" v-if="form.id">
              <button
                  type="button"
                  @click="deleteEvent(form.id)"
                  class="button is-danger is-fullwidth"
                  :disabled="!form.id">
                מחיקה
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

    <recurrence-editor-component v-model="everyWeek" :startDate="form.start_date"
                                 @clicked="onSubmitRecurrence"></recurrence-editor-component>
  </div>

</template>

<script>
import EventFormModal from '../calendar/EventFormModal.vue'
import RecurrenceEditorComponent from '../RecurrenceEditorComponent'
import hebrewDates from '../../mixins/hebrewDates'

export default {
  mixins: [hebrewDates],
  props: [
    'modal',
    'cusId',
    'projId',
    'popupTaskId'
  ],
  data() {
    return {
      everyWeek: {
        repeatOption: {
          type: 'every_week',
          everyWeekPicker: ['1', '2', '3', '4', '5', '6', '7']
        },
      },
      recurrence: [],
      recurrenceRule: '',
      recurrenceText: '',
      repeat: 0,
      repeatTaskId: '',
      updateAllRepeatedTask: false,
      repeatTask: '',
      customers: [],
      contacts: [],
      projects: [],
      loading: true,
      attributes: [],
      categories: [],
      status: [],
      priorities: [],
      optionsStatuses: [],
      isSaving: false,
      customerId: null,
      projectId: null,
      projectSelected: false,
      customerSelected: false,
      form: new this.$form({
        id: '',
        customer: '',
        contact: '',
        name: 'משימה',
        details: '',
        notification_time: null,
        start_date: null,
        end_date: null,
        status: '',
        priority: '',
        estimated_time: '',
        actual_time: '',
        category: '',
        notification_enable: 0
      })
    }
  },
  computed: {
    config() {
      return {
        locale: this.getLocale !== 'en' ? require(`flatpickr/dist/l10n/he.js`).default.he : 'en',
        altInput: true,
        enableTime: true,
        altFormat: 'd/m/Y H:i',
        dateFormat: 'd/m/Y H:i',
        time_24hr: true
      }
    }
  },
  created() {
    if (this.$route.query.calendarDates) {
      this.form.start_date = moment(this.$route.query.startDate.split(" ")[0]).format('DD/MM/YYYY HH:mm');
    }
    this.repeatTaskId = this.$route.query.repeatTaskId;
    this.updateAllRepeatedTask = this.$route.query.all;

    if (this.popupTaskId !== undefined) {

      this.fetchTask(this.popupTaskId);

    } else {
      let calendarDates = this.$route.query.calendarDates;

      let route = this.setRoute()
      this.$http
          .get(route)
          .then(res => {
            //click from customer form
            if (this.$route.query.customerId) {
              this.form.customer = res.data
              this.customerId = this.$route.query.customerId
            }
            //click from project list
            if (this.$route.query.projectId) {

              this.form.project = res.data
              let result = [{'customer_id': res.data.customer_id}]
              this.getCustomersByIdFromProjects(result)
              this.projectId = this.$route.query.projectId
            }
            //add task from customer form
            if (this.modal === 'customers') {
              this.form.customer = res.data
              this.customerId = this.cusId

            }
            //add task from project from
            if (this.modal === 'projects') {

              this.form.project = res.data

              this.getCustomersByIdFromProjects([res.data])
              this.projectId = this.projId
            }
            //click from task list
            if (this.$route.params.id && !this.modal && !this.$route.query.customerId) {

              this.fetchTask(this.$route.params.id)
              this.customerId = this.$route.params.id

            } else {
              //click from calendar slot
              if (calendarDates) {

                this.form.start_date = moment(this.$route.query.startDate.split(" ")[0]).format('DD/MM/YYYY HH:mm');
                this.form.end_date = moment(this.$route.query.endDate.split(" ")[0]).format('DD/MM/YYYY HH:mm');
              } else {
                //create new task
                this.setDateTime()
              }
              this.$http
                  .get(`app/tasks/create`)
                  .then(res => {
                    this.priorities = res.data.priorities.map(item => {
                      return item
                    })
                    this.attributes = res.data.attributes
                    this.optionsStatuses = res.data.statuses
                    this.categories = res.data.categories
                    if (this.$route.query.cusId) {
                      this.getCustomersByIdFromProjects([{'customer_id': this.$route.query.cusId}])
                    }
                  })
                  .catch(err =>
                      this.$event.fire('appError', err.response)
                  )
            }
          })
          .catch(err => this.$event.fire('appError', err.response))
    }

  },
  watch: {
    'form.start_date': function () {
      let parsed = moment(this.form.start_date, 'DD/MM/YYYY H:m');
      if (!this.loading) {
        this.form.end_date = moment(parsed._d).add(30, 'm').format('DD/MM/YYYY H:mm');
      }
      this.loading = false
    },
    'form.end_date': function () {

      if (!this.form.start_date) {
        const fp = flatpickr('#end_date', this.config);

        setTimeout(function () {
          fp.clear()
        }, 100)

        this.$event.fire('missingData', 'יש להכניס זמן התחלה');

      }
    },
    'form.notification_time': function () {
      if (!this.form.end_date) {

        setTimeout(function () {
          this.form.notification_time = '';
        }, 100)
        this.$event.fire('missingData', 'יש להכניס זמן התחלה וסיום');

      }
    },
    'form.project': function () {
      this.projectSelected = !this.projectSelected;
      if (this.projects.length > 0) {
        this.addProjectNameToTaskName();
        this.getCustomersByIdFromProjects(this.projects);
      }
    },
    'form.customer': function () {
      this.customerSelected = !this.customerSelected;
      if (this.form.customer) {
        this.getProjectsByCustomerId([this.form.customer]);
        this.getContactsByCustomerId([this.form.customer]);
      } else {
        this.customerSelected = false;
      }
    },
    'form.contact': function () {
      if (this.form.contact) {
        this.getCustomersByIdFromContacts(  [{'customer_id': this.form.contact.customer_id}])
      }
    }
  },

  methods: {
    format_date(value) {

      if (value) {
        return moment(String(value)).format('DD/MM/YYYY HH:mm');
      }
    },
    setRoute() {
      let entityId = this.modal === 'customers' ? this.cusId : this.projId;
      let route = !this.modal ? 'app/tasks' : `app/tasks/${this.modal}/${entityId}`;
      if (this.$route.query.customerId) {
        route = `app/tasks/customers/${this.$route.query.customerId}`;
      }
      if (this.$route.query.projectId) {
        route = `app/tasks/projects/${this.$route.query.projectId}`;
      }
      return route;
    },
    setDateTime() {
      let moment = require('moment-timezone');
      moment().tz('Asia/Jerusalem').format();
      this.form.start_date = moment(new Date()).format('DD/MM/YYYY H:mm');
      this.form.end_date = moment(new Date()).add(30, 'm').format('DD/MM/YYYY H:mm');
      this.form.date_to_complete = moment(new Date()).format('DD/MM/YYYY');
    },
    submit() {

      if (this.repeatTask) {
        this.repeatDialog();
        return
      }
      this.isSaving = true
      this.form.repeat = this.repeat;

      if (this.repeat === 1) {
        this.form.recurrence = this.recurrence;
        this.form.recurrenceRule = this.recurrenceRule
        this.form.recurrenceText = this.recurrenceText
      }

      let route = !this.modal ? '/tasks' : `/${this.modal}`;
      if (this.$route.name === 'calendar-task') {
        route = '/calendar';
      }

      if (this.form.notification_time) {
        this.form.notification_enable = 1;
      }
      if (this.form.id && this.form.id !== '') {

        this.form
            .put(`app/tasks/${this.form.id}`)
            .then(() => {
              this.$event.fire('refreshTasksTable');
              this.notify(
                  'success',
                  'משימה עוכנה'
              )

              this.$router.push(route);
            })
            .catch(err => this.$event.fire('appError', err.response))
            .finally(() => (this.isSaving = false))
      } else {

        this.form
            .post('app/tasks/add')
            .then(() => {
              this.$event.fire('refreshTasksTable');
              this.notify(
                  'success',
                  'משימה נוצרה'
              )

              this.$router.push(route);
            })
            .catch(err => this.$event.fire('appError', err.response))
            .finally(() => (this.isSaving = false))
      }
    },


    fetchTask(id) {

      this.$http
          .get(`app/tasks/${id}?repeatTaskId=${this.repeatTaskId}`)
          .then(res => {

            this.attributes = res.data.task.attributes;
            delete res.data.task.attributes;
            this.form = new this.$form(res.data.task);
            let taskStatus = res.data.task.status;
            this.optionsStatuses = res.data.statuses;
            this.priorities = res.data.priorities;
            this.categories = res.data.categories;
            this.form.category = res.data.task.category[0];
            this.form.priority = res.data.task.priority[0];
            this.form.project = res.data.task.project[0];
            if( res.data.task.contact) {

              this.form.contact.full_name = res.data.task.contact.first_name + ' ' + res.data.task.contact.last_name;
              this.form.contact.id = res.data.task.contact.id;
            }

            if (!res.data.repeatTask) {
              this.form.status = taskStatus.length > 0 ? taskStatus[0] : '';
              this.form.start_date = this.format_date(res.data.task.start_date);
              this.form.end_date = this.format_date(res.data.task.end_date);

            } else {
              this.repeatTask = res.data.repeatTask;
              this.form.start_date = this.format_date(res.data.repeatTask[0].start_date);
              this.form.end_date = this.format_date(res.data.repeatTask[0].end_date);
              this.form.status = res.data.repeatTask[0].status;
              this.form.details = res.data.repeatTask[0].details;
              this.form.name = res.data.repeatTask[0].name;
              this.form.repeatTaskId = res.data.repeatTask[0].id;
              this.recurrenceText = res.data.repeatTaskRuleText[0].text_rule;
            }
            this.form.notification_time = this.format_date(res.data.task.notification_time);
            this.repeat = res.data.task.repeat;
          })
          .catch(err => this.$event.fire('appError', err.response))
    },

    validateForm() {
      this.$validator
          .validateAll()
          .then(result => {
            if (result) {
              this.submit();
            }
          })
          .catch(err => this.$event.fire('appError', err))
    },

    addEvent() {

      this.$modal.show('event-form-modal', {customerId: this.form.customer.id});
    },
    duplicateTask() {
      this.$http
          .get(`app/tasks/duplicate/${this.form.id}`)
          .then(() => {
            this.$event.fire('refreshTasksTable');
            this.notify(
                'success',
                'משימה שוכפלה'
            )
            this.$modal.hide("dialog");

          })
          .catch(err => this.$event.fire('appError', err.response))

    },
    openRecurringSchedule() {
      this.$modal.show('recurring-schedule-modal', {value: this.everyWeek});

    },

    searchContacts(search) {

      if (search === '') {
        return;
      }
      this.$http
          .get('app/contacts/search?query=' + search)
          .then(res => {
            this.contacts = res.data;
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },

    searchCustomers(search) {

      if (search === '') {
        return;
      }
      this.$http
          .get('app/customers/search?query=' + search)
          .then(res => {
            this.customers = res.data;
            if (this.customers.length > 0) {
              this.getProjectsByCustomerId(this.customers);
              this.getContactsByCustomerId(this.customers);
            }
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },


    searchProjects(search) {

      if (search === '' || this.customerSelected) {
        return;
      }
      this.$http
          .get('app/projects/search?query=' + search)
          .then(res => {

            this.projects = res.data;

          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    getCustomersByIdFromProjects(projects) {
      let customer_id = projects.map(a => a.customer_id);
      this.$http
          .post('app/project-customers/' + customer_id)
          .then(res => {
            this.form.customer = res.data[0];
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },

    getCustomersByIdFromContacts(contacts) {

      let customer_id = contacts.map(a => a.customer_id);
      this.$http
          .get('app/customer/contact/' + customer_id)
          .then(res => {

            this.form.customer = res.data.customer;
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },

    getProjectsByCustomerId(customers) {

      let id = customers.map(a => a.id)
      this.$http
          .post('app/customers-projects/' + id)
          .then(res => {
            this.projects = []
            if (res.data.length > 0) {
              this.projects = res.data;
            }
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },

    getContactsByCustomerId(customers)  {
      let id = customers.map(a => a.id)
      this.$http
          .get('app/contacts/' + id)
          .then(res => {

            this.contacts = []
            if (res.data.length > 0) {
              this.contacts = res.data;
            }
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },

    closeModal() {

      if (!this.popupTaskId && this.$route.name !== 'notification-task') {
        this.$router.go(-1);
      } else {
        let modal = document.querySelector(".task-form-modal");
        modal.parentNode.removeChild(modal);

      }

    },
    addProjectNameToTaskName() {
      let checkTaskName = this.form.name.match(/משימה/g);
      if (checkTaskName) {
        this.form.name = ' ( ' + this.form.project.name + ' ) '
      }

    },
    deleteEvent(task) {

      if (this.repeat === 0) {
        let em = this;
        this.$modal.show("dialog", {
          title: "למחוק " + name + "!",
          text: "הפעולה תמחק את הרשומה ללא אפשרות שיחזור.",
          buttons: [
            {
              title: "כן למחוק",
              class: "button is-danger is-radiusless is-radius-bottom-left",
              handler: () => {
                this.$http
                    .delete(`app/tasks/delete/${task}`)
                    .then(res => {
                      em.closeModal();
                      em.notify("success", name + " נמחק.");
                      em.$modal.hide("dialog");
                      em.$event.fire("refreshTasksTable");

                    })
                    .catch(err => {
                      this.$event.fire("appError", err.response);
                    });
              }
            },
            {title: "ביטול", class: "button is-warning is-radiusless is-radius-bottom-right"}
          ]

        });
      } else {
        this.deleteMultipleEvents(task);
      }

    },
    deleteMultipleEvents(task) {

      this.$modal.show("dialog", {
        title: "משימה חוזרת",
        text: "האם למחוק את כל המשימות הקשורות או רק את זו?",
        buttons: this.repeatsDeleteButtons(task, 'delete')

      });
    },
    repeatsDeleteButtons(task, action) {
      let titleMassage = this.setRepeatTitle(action);
      let buttons = [
        {title: "ביטול", class: "button is-info is-radiusless is-radius-bottom-left"},
        {
          title: titleMassage[0].titleAll,
          class: "button is-danger is-radiusless",
          handler: () => {
            this.$http
                .delete(`app/tasks/delete/${task}`)
                .then(res => {
                  this.handleAfterRepeatUpdate(titleMassage[2].massageAll)
                })
                .catch(err => {
                  this.$event.fire("appError", err.response);
                });
          }
        }]
      if (!this.updateAllRepeatedTask) {
        buttons.push({
          title: titleMassage[1].titleSingle,
          class: "button is-warning ",
          handler: () => {
            this.$http
                .delete(`app/tasks/repeat/delete/${this.repeatTaskId}`)
                .then(res => {
                  this.handleAfterRepeatUpdate(titleMassage[3].massageSingle)
                })
                .catch(err => {
                  this.$event.fire("appError", err.response);
                });
          }
        });
      }
      return buttons;

    },
    repeatsUpdateButtons(action) {
      let titleMassage = this.setRepeatTitle(action);

      let buttons = [
        {title: "ביטול", class: "button is-info is-radiusless is-radius-bottom-left"},
        {
          title: titleMassage[0].titleAll,
          class: "button is-danger is-radiusless",
          handler: () => {
            this.form
                .post(`app/tasks/repeat/all`)
                .then(res => {
                  this.handleAfterRepeatUpdate(titleMassage[2].massageAll)
                })
                .catch(err => {
                  this.$event.fire("appError", err.response);
                });
          }
        }
      ]
      if (!this.updateAllRepeatedTask) {
        buttons.push({
          title: titleMassage[1].titleSingle,
          class: "button is-warning ",
          handler: () => {
            this.form
                .post(`app/tasks/repeat/single`)
                .then(res => {
                  this.handleAfterRepeatUpdate(titleMassage[3].massageSingle)

                })
                .catch(err => {
                  this.$event.fire("appError", err.response);
                });
          }
        })
      }
      return buttons;
    },
    handleAfterRepeatUpdate(titleMassage) {
      this.closeModal();
      this.notify("success", titleMassage);
      this.$modal.hide("dialog");
      this.$event.fire("refreshTasksTable");

    },
    setRepeatTitle(action) {

      if (action === 'delete') {
        return [{titleAll: 'למחוק הכל'}, {titleSingle: 'למחוק רק את המשימה הזאת'}, {massageAll: 'כל המשימות הקשורות נמחקו'}, {massageSingle: 'משימה נמחקה'}]
      }
      return [{titleAll: 'לעדכן הכל'}, {titleSingle: 'לעדכן רק את המשימה הזאת'}, {massageAll: 'כל המשימות הקשורות עודכנו'}, {massageSingle: 'משימה עודכנה'}]
    },
    repeatDialog() {

      this.$modal.show("dialog", {
        title: "משימה חוזרת",
        text: "האם לעדכן את כל המשימות הקשורות או רק את זו?",

        buttons: this.repeatsUpdateButtons('update')

      });
    },
    onSubmitRecurrence(recurrence) {
      this.recurrence = recurrence.all();
      let gettext =  function(id) {
        return hebrewDates.hebrewStrings[id] || id;
      };

      this.recurrenceText = recurrence.toText( gettext, hebrewDates);
      this.recurrenceRule = recurrence.origOptions
      this.repeat = 1;
    }
  },
  components: {EventFormModal, RecurrenceEditorComponent},
}
</script>
<style>
.recurring-schedule-icon {
  font-size: 20px;
  margin-left: 7px;
  bottom: 2px;
  cursor: pointer;
}

.recurring-schedule-a {
  color: rgba(10, 10, 10, 0.2);
}
.duplicate-icon{
  margin-bottom: 0px !important;
}
</style>
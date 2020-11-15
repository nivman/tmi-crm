<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form action="#" autocomplete="off" @submit.prevent="validateForm()">
      <div class="modal-card animated fastest zoomIn">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            {{ form.id ? 'עריכת משימה' : 'יצירת משימה' }}
          </p>
          <div class="buttons is-centered">
            <p class="control tooltip" v-if="customerId">
              <a
                  @click="addEvent"
                  class="fas fa-comment-dots is-small button is-info">
                <span class="tooltip-text bottom"> התקשרות חדשה</span>

              </a>
            </p>
            <p class="control tooltip">
              <button
                  type="button"
                  class="delete"
                  @click="$router.go(-1)"
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
                <label class="label" for="date_to_complete">תאריך לביצוע</label>
                <flat-pickr
                    class="input"
                    id="date_to_complete"
                    name="date_to_complete"
                    :config="config_to_complete"
                    v-model="form.date_to_complete">
                </flat-pickr>
              </div>
            </div>
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
                  <div class="column">
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
                  <div class="column">
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
                      <input
                          type="text"
                          id="notification_time"
                          class="input"
                          name="notification_time"
                          v-model="form.notification_time"/>
                    </div>
                  </div>
                </div>
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
  </div>

</template>

<script>
import EventFormModal from '../calendar/EventFormModal.vue'

export default {
  props: [
    'modal',
    'cusId',
    'projId'
  ],
  data () {
    return {
      customers: [],
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
        name: 'משימה',
        details: '',
        notification_time: '',
        start_date: null,
        end_date: null,
        status: '',
        priority: '',
        estimated_time: '',
        actual_time: '',
        date_to_complete: '',
        category: ''
      }),
      config: {
        altInput: true,
        enableTime: true,
        altFormat: 'd/m/Y H:i',
        dateFormat: 'd/m/Y H:i',
      },
      config_to_complete: {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'd/m/Y',
      }
    }
  },
  created () {

    let route = this.setRoute()
    this.$http
        .get(route)
        .then(res => {

          if (this.$route.query.customerId) {
            this.form.customer = res.data
            this.customerId = this.$route.query.customerId
          }
          if (this.$route.query.projectId) {

            this.form.project = res.data

            let result = [{'customer_id' : res.data.customer_id}]
            this.getCustomersById(result)
            this.projectId = this.$route.query.projectId
          }

          if (this.modal === 'customers') {

            this.form.customer = res.data
            this.customerId = this.cusId

          }
          if (this.modal === 'projects') {
            this.form.project = res.data
            this.projectId = this.projId
          }

          if (this.$route.params.id && !this.modal && !this.$route.query.customerId) {

            this.fetchTask(this.$route.params.id)
            this.customerId = this.$route.params.id

          } else {

            this.setDateTime()
            this.$http
                .get(`app/tasks/create`)
                .then(res => {
                  this.priorities = res.data.priorities.map(item => {
                    return item
                  })
                  this.attributes = res.data.attributes
                  this.optionsStatuses = res.data.statuses
                  this.categories = res.data.categories
                  this.loading = false
                })
                .catch(err =>
                    this.$event.fire('appError', err.response)
                )
          }
        })
        .catch(err => this.$event.fire('appError', err.response))
  },
  watch: {
    'form.end_date': function () {

      if (!this.form.start_date) {
        const fp = flatpickr('#end_date', this.config)

        setTimeout(function () {
          fp.clear()
        }, 100)

        this.$event.fire('missingData', 'יש להכניס זמן התחלה')

      }
    },
    'form.notification_time': function () {
      if (!this.form.end_date) {

        setTimeout(function () {
          this.form.notification_time = ''
        }, 100)
        this.$event.fire('missingData', 'יש להכניס זמן התחלה וסיום')

      }
    },
    'form.project': function () {
      this.projectSelected = !this.projectSelected
      if (this.projects.length > 0) {
        this.getCustomersById(this.projects)
      }
    },
    'form.customer': function () {

      this.customerSelected = !this.customerSelected
      if (this.form.customer) {
        console.log(this.form.customer)
        this.getProjectsByCustomerId([this.form.customer])
      } else {
        this.customerSelected = false
      }
    }
  },
  methods: {
    format_date (value) {

      if (value) {
        return moment(String(value)).format('DD/MM/YYYY hh:mm')
      }
    },
    setRoute () {

      let route = !this.modal ? 'app/tasks' : `app/tasks/${this.modal}/${this.cusId}`
      if (this.$route.query.customerId) {
        route = `app/tasks/customers/${this.$route.query.customerId}`
      }
      if (this.$route.query.projectId) {
        route = `app/tasks/projects/${this.$route.query.projectId}`
      }
      return route
    },
    setDateTime () {
      let moment = require('moment-timezone')
      moment().tz('Asia/Jerusalem').format()
      this.form.start_date = moment(new Date()).format('DD/MM/YYYY H:mm')
      this.form.end_date = moment(new Date()).add(30, 'm').format('DD/MM/YYYY H:mm')
      this.form.date_to_complete = moment(new Date()).format('DD/MM/YYYY')
    },
    submit () {
      this.isSaving = true
      let route = !this.modal ? '/tasks' : `/${this.modal}`
      if (this.form.id && this.form.id !== '') {

        this.form
            .put(`app/tasks/${this.form.id}`)
            .then(() => {
              this.$event.fire('refreshTasksTable')
              this.notify(
                  'success',
                  'משימה עוכנה'
              )

              this.$router.push(route)
            })
            .catch(err => this.$event.fire('appError', err.response))
            .finally(() => (this.isSaving = false))
      } else {

        this.form
            .post('app/tasks/add')
            .then(() => {
              this.$event.fire('refreshTasksTable')
              this.notify(
                  'success',
                  'משימה נוצרה'
              )

              this.$router.push(route)
            })
            .catch(err => this.$event.fire('appError', err.response))
            .finally(() => (this.isSaving = false))
      }
    },
    fetchTask (id) {

      this.$http
          .get(`app/tasks/${id}`)
          .then(res => {

            this.attributes = res.data.task.attributes
            delete res.data.task.attributes
            this.form = new this.$form(res.data.task)
            let taskStatus = res.data.task.status
            this.optionsStatuses = res.data.statuses
            this.priorities = res.data.priorities
            this.categories = res.data.categories
            this.form.category = res.data.task.category[0]
            this.form.priority = res.data.task.priority[0]
            this.form.project = res.data.task.project[0]
            this.form.status = taskStatus.length > 0 ? taskStatus[0] : ''
            this.form.end_date = this.format_date(res.data.task.end_date)
            this.form.start_date = this.format_date(res.data.task.start_date)
            this.form.date_to_complete = this.format_date(res.data.task.date_to_complete)
            this.form.notification_time = res.data.task.notification_time
            this.loading = false
          })
          .catch(err => this.$event.fire('appError', err.response))
    },
    validateForm () {
      this.$validator
          .validateAll()
          .then(result => {
            if (result) {
              this.submit()
            }
          })
          .catch(err => this.$event.fire('appError', err))
    },
    addEvent () {

      this.$modal.show('event-form-modal', { customerId: this.$route.params.id })
    },
    searchCustomers (search) {

      if (search === '') {
        return
      }

      this.$http
          .get('app/customers/search?query=' + search)
          .then(res => {

            this.customers = res.data
            if (this.customers.length > 0) {
              this.getProjectsByCustomerId(this.customers)
            }

          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    searchProjects (search) {

      if (search === '' || this.customerSelected) {
        return
      }
      this.$http
          .get('app/projects/search?query=' + search)
          .then(res => {

            this.projects = res.data

          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    getCustomersById (projects) {
      let customer_id = projects.map(a => a.customer_id)
      this.$http
          .post('app/project-customers/' + customer_id)
          .then(res => {

            this.form.customer = res.data[0]

          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    getProjectsByCustomerId (customers) {

      let id = customers.map(a => a.id)
      this.$http
          .post('app/customers-projects/' + id)
          .then(res => {
            this.projects = []
            if (res.data.length > 0) {
              this.projects = res.data
            }

          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })

    }
  },
  components: { EventFormModal },
}
</script>
<style>
</style>
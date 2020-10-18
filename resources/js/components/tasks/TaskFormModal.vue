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
                @search="searchTasks"
                v-model="form.customer">
            </v-select>
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
                <label class="label" for="start_date">זמן התחלה</label>
                <flat-pickr
                    class="input"
                    id="start_date"
                    name="start_date"
                    :config="config"
                    v-validate="'required'"
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
                    :class="{ 'is-danger': errors.has('end_date') }"
                ></flat-pickr>
                <div class="help is-danger">
                  {{ errors.first('end_date') }}
                </div>
              </div>
            </div>
          </div>

          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="priorities">דחיפות</label>
                <v-select
                    label="name"
                    id="priorities"
                    name="priorities"
                    class="rtl-direction"
                    item-value="id"
                    item-text="name"
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
  data () {
    return {
      customers: [],
      loading: true,
      attributes: [],
      status: [],
      priorities: [],
      optionsStatuses: [],
      isSaving: false,
      customerId: null,
      form: new this.$form({
        id: '',
        name: '',
        details: '',
        notification_time: '',
        start_date: '',
        end_date: '',
        status: '',
        priority:'',
      }),
      config: {
        altInput: true,
        enableTime: true,
        altFormat: "d/m/Y H:i",
        dateFormat: "d/m/Y H:i",
      },
    }
  },
  created () {
    let moment = require('moment-timezone');
    moment().tz("Asia/Jerusalem").format();

    this.form.start_date = moment(new Date()).format("DD/MM/YYYY H:mm");
    this.form.end_date =  moment(new Date()).add(30, 'm').format("DD/MM/YYYY H:mm");

    this.$http
        .get('app/tasks')
        .then(res => {

          if (this.$route.params.id) {
            this.fetchTask(this.$route.params.id)
            this.customerId = this.$route.params.id
          } else {
            this.$http
                .get(`app/tasks/create`)
                .then(res => {
                  this.priorities = res.data.priorities.map(item => {
                    return item
                  })
                  this.attributes = res.data.attributes;
                  this.optionsStatuses = res.data.statuses
                  this.loading = false
                })
                .catch(err =>
                    this.$event.fire('appError', err.response)
                )
          }
        })
        .catch(err => this.$event.fire('appError', err.response))
  },
  methods: {
    submit () {
      this.isSaving = true
      if (this.form.id && this.form.id !== '') {

        this.form
            .put(`app/tasks/${this.form.id}`)
            .then(() => {
              this.$event.fire('refreshTasksTable')
              this.notify(
                  'success',
                  'Customer has been successfully updated.'
              )
              this.$router.push('/tasks')
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
                  'Customer has been successfully added.'
              )
              this.$router.push('/tasks')
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
            this.priorities = res.data.priorities;
            this.form.status = taskStatus.length > 0 ? taskStatus[0] : ''
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
    searchTasks(search) {

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
    }
  },
  components: { EventFormModal },
}
</script>
<style>
</style>
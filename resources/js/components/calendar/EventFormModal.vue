<template>
  <modal
      name="event-form-modal"
      height="auto"
      :scrollable="true"
      width="300"
      :adaptive="true"
      classes="is-rounded rtl-direction"
      transition="custom"
      @before-open="beforeOpen">
    <form autocomplete="off" action="#" @submit.prevent="validateForm()">
      <div class="modal-card is-medium animated fastest zoomIn" style="width: 100%;">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            {{ form.id ? 'עריכת התקשרות' : 'הוספת התקשרות' }}
          </p>
          <button
              type="button"
              class="delete"
              @click="$modal.hide('event-form-modal')">
          </button>
        </header>
        <section class="modal-card-body is-radius-bottom">
          <div class="field">
            <label class="label" for="contact">איש קשר</label>
            <v-select
                label="contact"
                id="contact"
                name="contact"
                class="rtl-direction"
                :options="contacts"
                @search="searchContacts"
                v-model="form.contact">
            </v-select>
          </div>
          <div class="field">
            <label class="label" for="event-type">סוג</label>
            <v-select
                label="name"
                id="event-type"
                name="event-type"
                class="rtl-direction"
                item-value="id"
                item-text="name"
                return-object
                single-line
                :options="types"
                v-model="form.type_id">
            </v-select>
          </div>
          <div class="field">
            <label class="label" for="title">נושא</label>
            <input
                type="text"
                id="title"
                name="title"
                class="input"
                v-model="form.title"
                v-validate="'required'"
                :class="{ 'is-danger': errors.has('title') }"/>
            <div class="help is-danger">
              {{ errors.first('title') }}
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
          <div class="field">
            <label class="label" for="start_date">זמן התחלה</label>
            <flat-pickr
                class="input"
                id="start_date"
                name="start_date"
                :config="config"
                v-validate="'required'"
                v-model="form.start_date"
                :class="{ 'is-danger': errors.has('start_date') }"
            ></flat-pickr>
            <div class="help is-danger">
              {{ errors.first('start_date') }}
            </div>
          </div>
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
          <div class="field">
            <label class="label" for="color">צבע</label>
            <div class="control">
              <div class="select is-fullwidth">
                <select
                    v-model="form.color"
                    id="color"
                    :class="{'is-danger': errors.has('color') }">
                  <option value="black">Black</option>
                  <option value="blue">Blue</option>
                  <option value="green">Green</option>
                  <option value="orange">Orange</option>
                  <option value="purple">Purple</option>
                  <option value="red">Red</option>
                </select>
              </div>
            </div>
            <div class="help is-danger">
              {{ errors.first('color') }}
            </div>
          </div>

          <div class="field">
            <button
                type="submit"
                class="button is-link is-fullwidth"
                :class="{ 'is-loading': isSaving }"
                :disabled="errors.any() || isSaving">
              אישור
            </button>
          </div>
          <div class="field" v-if="form.id">
            <button
                type="button"
                @click="deleteEvent()"
                class="button is-danger is-fullwidth"
                :disabled="!form.id">
              מחיקה
            </button>
          </div>
        </section>
      </div>
    </form>
    <button
        class="modal-close is-large is-hidden-mobile"
        aria-label="close"
        @click="$modal.hide('event-form-modal')"
    ></button>
  </modal>
</template>

<script>
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

export default {
  components: { flatPickr },
  data () {
    return {
      contacts: [],
      eventTypes: { id: 1, name: 'שיחה' },
      types: [],
      form: new this.$form({
        id: '',
        start_date: '',
        end_date: '',
        title: '',
        details: '',
        color: '',
        contact: '',
        type: '',

      }),
      config: {
        altInput: true,
        enableTime: true,
        altFormat: "d/m/Y H:i",
        dateFormat: "d/m/Y H:i",
      },
      loading: false,
      isSaving: false,
      contact_id: null,
      type_id: null,
      type: '',
    }
  },
  methods: {
    beforeOpen (e) {
      let moment = require('moment-timezone');
      moment().tz("Asia/Jerusalem").format();

      this.form.start_date  = moment(new Date()).format("DD/MM/YYYY H:m");

      this.fetchTypes();
      if (e.params.event) {
        this.form = new this.$form(e.params.event)
      } else {
        if (e.params.customerId) {

          this.fetchContact(e.params.customerId)
        }
        this.form = new this.$form({
          id: '',
          start_date: '',
          end_date: '',
          title: '',
          details: '',
          color: '',
          contact: '',
          contact_id: null,
          type: '',
          type_id: null
        })
      }
    },

    searchContacts: function (search) {
      if (search === '') {
        return
      }
      this.$http
          .get(`app/contacts/search/${search}`)
          .then(res => {

            const contacts = res.data.map(item => {
              return item.full_name
            })
            this.form.contact = res.data[0].full_name
            this.contacts = contacts
            this.form.contact_id = res.data[0].id

          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },

    fetchTypes() {

      this.$http
          .get(`app/events/eventsTypes`)
          .then(res => {
            this.types = res.data.map(item => {
              return item
            })
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    fetchContact (customer_id) {

      this.$http
          .get(`app/contacts/${customer_id}`)
          .then(res => {

            this.form.contact = res.data[0].full_name
            this.form.contact_id = res.data[0].id
            debugger
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    submit () {
      this.isSaving = true
      if (this.form.id && this.form.id !== '') {
        this.form
            .put(`app/events/${this.form.id}`)
            .then(() => {
              this.$event.fire('refreshEvents')
              this.notify(
                  'success',
                  'Event has been successfully updated.'
              )
              this.$modal.hide('event-form-modal')
            })
            .catch(err => this.$event.fire('appError', err.response))
            .finally(() => (this.isSaving = false))
      } else {
        this.form
            .post('app/events')
            .then(() => {
              this.$event.fire('refreshEvents')
              this.notify(
                  'success',
                  'Event has been successfully added.'
              )
              this.$modal.hide('event-form-modal')
            })
            .catch(err => this.$event.fire('appError', err.response))
            .finally(() => (this.isSaving = false))
      }
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
    deleteEvent () {
      this.$modal.show('dialog', {
        title: 'Delete Event!',
        text:
            'This action will have permanent effect that can\'t be reversed.',
        buttons: [
          {
            title: 'Yes, please delete',
            class:
                'button is-danger is-radiusless is-radius-bottom-left',
            handler: () => {
              this.$http
                  .delete(`app/events/${this.form.id}`)
                  .then(() => {
                    this.$event.fire('refreshEvents')
                    this.notify(
                        'success',
                        'Event has been successfully deleted.'
                    )
                    this.$modal.hide('event-form-modal')
                    this.$modal.hide('dialog')
                  })
                  .catch(err => {
                    this.$event.fire('appError', err.response)
                  })
            }
          },
          {
            title: 'No, please cancel',
            class:
                'button is-warning is-radiusless is-radius-bottom-right'
          }
        ]
      })
    }
  }
}
</script>

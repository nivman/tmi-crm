<template>
  <modal
      name="event-form-modal"
      height="auto"
      :scrollable="true"
      classes="is-rounded rtl-direction"
      transition="custom"
      @before-open="beforeOpen">
    <form autocomplete="off" action="#" @submit.prevent="validateForm()">
      <div class="modal-card animated fastest zoomIn" style="width: 100%;">
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
                label="full_name"
                id="contact"
                name="contact"
                item-value="id"
                item-text="first_name"
                class="rtl-direction"
                :options="contacts"
                @search="searchContacts"
                v-model="form.contact">
            </v-select>
          </div>
          <div class="field">
            <label class="label" for="project">פרוייקט</label>
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
          <div class="field">
            <label class="label" for="event-type">סוג</label>
            <v-select
                label="name"
                id="event-type"
                name="event-type"
                class="rtl-direction"
                item-value="id"
                item-text="name"
                v-bind:style="{ 'background-color':form.typeColor  }"
                return-object
                single-line
                :options="types"
                v-model="form.type">
              <template v-slot:option="types">
                <div v-html="types.name"
                     :style="{
                      background: types.color,
                      textAlign: 'center',
                      display: 'block',
                      marginLeft:'-20px',
                      marginRight:'-20px'
                    } "></div>
              </template>
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
  components: {flatPickr},
  props: ['eventId', 'event', 'calendarDates'],
  data() {
    return {
      contacts: [],
      projects: [],
      eventTypes: {id: 1, name: 'שיחה'},
      types: [],
      typeColor: '',
      contactSelected: false,
      projectSelected: false,
      form: new this.$form({
        id: '',
        start_date: '',
        end_date: '',
        title: '',
        details: '',
        color: '',
        contact: '',
        type: '',
        project: ''

      }),
      config: {
        altInput: true,
        enableTime: true,
        altFormat: "d/m/Y H:i",
        dateFormat: "d/m/Y H:i",
        time_24hr: true
      },
      loading: false,
      isSaving: false,
      contact_id: null,
      type_id: null,
      type: '',
    }
  },
  watch: {
    'form.project': function () {

  //  this.projectSelected = !this.projectSelected
      if (this.projects.length > 0 && !this.contactSelected) {

        this.getContactsByProjectId(this.form.project)
      }
      this.projectSelected = !this.projectSelected
    },
    'form.contact': function () {

      this.contactSelected = !this.contactSelected

      if (this.form.contact && !this.projectSelected) {
        this.getProjectsByContactId([this.form.contact])
      }
      this.contactSelected = !this.contactSelected
    },
    'form.type': function () {
    this.form.typeColor = this.form.type.color

    }
  },
  methods: {
    getContactsByProjectId(project) {

     if(!project) {
       return false;
     }
      let customer_id = project.customer_id
      this.$http
          .post('app/project-customers/' + customer_id)
          .then(res => {
            let contacts = []

            if (res.data.length > 1) {
              res.data.forEach((element) => {
                contacts.push({
                  'id': element.contact_id,
                  'full_name': element.contact_first_name + ' ' + element.contact_last_name
                })
              })
              this.contacts = contacts;
            } else {
              this.form.contact = res.data[0].contact_first_name + ' ' + res.data[0].contact_last_name;
              this.form.contact_id = res.data[0].contact_id;
            }
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    getProjectsByContactId(contacts) {
      let id = contacts.map(a => a.customer_id)

      if(!id[0]) {
        return false;
      }
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

    },
    beforeOpen(e) {

      this.form = new this.$form({
        id: '',
        start_date: '',
        end_date: '',
        title: '',
        details: '',
        color: '',
        contact: '',
        contact_id: null,
        type: 'שיחה',
        type_id: 1,
        project: ''
      })
      let calendarDates = e.params;
      let moment = require('moment-timezone');
      moment().tz("Asia/Jerusalem").format();

      this.form.start_date = moment(new Date()).format("DD/MM/YYYY H:m");
      this.form.end_date = moment(new Date()).add(30, 'm').format('DD/MM/YYYY H:mm')
      this.create();

      if(e.params.eventId){

        this.getEventById(e.params.eventId);
      }
      else if (e.params.event) {

        this.form = new this.$form(e.params.event)
        this.form.contact = e.params.event.contact.first_name + ' ' + e.params.event.contact.last_name
        this.form.contact_id = e.params.event.contact.id
        this.form.start_date = moment( e.params.event.start_date).format("DD/MM/YYYY H:mm");
        this.form.end_date = moment( e.params.event.end_date).format("DD/MM/YYYY H:mm");
        let project = e.params.event.project;
        this.form.project = project ? project.name : '';
        this.form.project_id =  project ? project.id : null;

        this.form.details = this.clearContent(e.params.event.details);
        this.getProjectsByContactId([e.params.event.contact]);
      } else {
        // task-or-event-dialog = click on slot in the calendar
        if(calendarDates.name === 'task-or-event-dialog') {
          this.form.start_date = moment( e.params.params.startStr).format("DD/MM/YYYY H:mm");
          this.form.end_date = moment( e.params.params.endStr).format("DD/MM/YYYY H:mm");
        }

        if (e.params.customerId) {

          this.fetchContact(e.params.customerId)
        }
        if (e.params.projectId) {

          this.getProject(e.params.projectId)
        }
      }
    },
    searchContacts: function (search) {

      if (search === '') {
        return
      }
      this.$http
          .get('app/contacts/search?query=' + search)
          .then(res => {
            this.contacts = res.data
            this.contactSelected = false;
            this.projectSelected = false;
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    searchProjects: function (search) {

      if (search === '') {
        return
      }
      this.$http
          .get('app/projects/search?query=' + search)
          .then(res => {
            this.projects = res.data
            this.projectSelected = false;
            this.contactSelected = false;
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    create() {

      this.$http
          .get(`app/events/create`)
          .then(res => {
            this.types = res.data.map(item => {
              return item
            })
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    fetchContact(customer_id) {

      this.$http
          .get(`app/contacts/${customer_id}`)
          .then(res => {

            this.form.contact = res.data[0].full_name
            this.form.contact_id = res.data[0].id
            this.contacts = res.data

            this.getProjectsByContactId([res.data[0]])
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    getProject (id) {

      this.$http
          .get(`app/projects/${id}`)
          .then(res => {

            this.form.project = res.data.project.name
            this.form.project_id = res.data.project.id
            this.contacts = res.data.contacts
          })
          .catch(err => this.$event.fire('appError', err.response))
    },
    getEventById(id) {
      this.$http
          .get('app/events/' + id)
          .then(res => {

            this.form.id = res.data.id
            this.form.title = res.data.title
            this.form.contact = res.data.contact[0].first_name + ' ' + res.data.contact[0].last_name
            this.form.contact_id = res.data.contact[0].id
            this.form.type = res.data.type[0].name;
            this.form.type_id = res.data.type_id;
            this.form.details = res.data.details;

            this.form.start_date = moment( res.data.start_date).format("DD/MM/YYYY hh:mm");
            this.form.end_date = moment( res.data.end_date).format("DD/MM/YYYY hh:mm");

          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    submit() {
      this.isSaving = true
      if (this.form.id && this.form.id !== '') {

        this.form
            .put(`app/events/${this.form.id}`)
            .then(() => {

              this.$forceUpdate();
           //   this.$event.fire('refreshEventsListTable')
              this.notify(
                  'success',
                  'עודכן בהצלחה'
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
                  'התקשרות נוצרה בהצלחה.'
              )
              this.$modal.hide('event-form-modal')
            })
            .catch(err => this.$event.fire('appError', err.response))
            .finally(() => (this.isSaving = false))
      }
    },
    validateForm() {
      this.$validator
          .validateAll()
          .then(result => {
            if (result) {
              this.submit()
            }
          })
          .catch(err => this.$event.fire('appError', err))
    },
    clearContent(details) {
      if(details) {
        let removeEmailAddr=  details.replace(/([^.@\s]+)(\.[^.@\s]+)*@([^.@\s]+\.)+([^.@\s]+)/, '')
        let removeEmptyLine = removeEmailAddr.replaceAll(/\r?\n|\r/g, '')
        let removeBiggerLessSign = removeEmptyLine.replaceAll(/<|>/g,'')
        let breakLineOnDot = removeBiggerLessSign.replaceAll(/\./g,'\n')
        return breakLineOnDot.replaceAll(/\[.+\n.+/gi,'')

      }
      return ''
    },
    deleteEvent() {
      this.$modal.show('dialog', {
        title: 'מחיקת התקשרות',
        text:
            '',
        buttons: [
          {
            title: 'מחיקה',
            class:
                'button is-danger is-radiusless is-radius-bottom-left',
            handler: () => {
              this.$http
                  .delete(`app/events/${this.form.id}`)
                  .then(() => {
                    this.$event.fire('refreshEvents')
                    this.notify(
                        'success',
                        'התקשרות נמחקה'
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
            title: 'לא',
            class:
                'button is-warning is-radiusless is-radius-bottom-right'
          }
        ]
      })
    }
  }
}
</script>

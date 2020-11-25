<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form action="#" autocomplete="off" @submit.prevent="validateForm()">
      <div class="modal-card animated fastest zoomIn">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            {{ form.id ? 'עריכת פרויקט' : 'יצירת פרויקט' }}
          </p>
          <div class="buttons is-centered">
            <p class="control tooltip" v-if="projectId">
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
                @search="searchCustomers"
                v-model="form.customer">
            </v-select>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="name">שם פרויקט</label>
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
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="price">מחיר</label>
                <input
                    id="price"
                    type="text"
                    name="price"
                    class="input"
                    v-model="form.price"/>
              </div>
            </div>
            <div class="column">
              <div class="field">

                <div class="field">
                  <label class="label" for="expenses">הוצאות </label>

                  <input
                      id="expenses"
                      type="text"
                      name="expenses"
                      class="input"
                      v-model="form.expenses"/>
                </div>
              </div>
            </div>
            <div class="column">
              <div class="field">

                <div class="field">
                  <label class="label" for="type">סוג פרויקט</label>

                  <v-select
                      label="name"
                      id="type"
                      name="type"
                      class="rtl-direction"
                      item-value="id"
                      item-text="name"
                      return-object
                      single-line
                      :options="types"
                      v-model="form.type">
                  </v-select>
                </div>
              </div>
            </div>
          </div>
          <div class="columns">
            <div class="columns">
              <div class="modal-card test">
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
                  </div>
                </section>
              </div>
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <div v-if="attributes">
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

  </div>

</template>

<script>

export default {

  data () {
    return {
      customers: [],
      types: [],
      loading: true,
      attributes: [],
      isSaving: false,
      projectId: null,
      form: new this.$form({
        id: '',
        name: '',
        start_date: null,
        end_date: null,
        price: '',
        type: '',
        expenses: '',
      }),
      config: {
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

          if (this.$route.params.id) {

            this.fetchProject(this.$route.params.id)
            this.projectId = this.$route.params.id

          } else {

            this.setDateTime()
            this.$http
                .get(`app/projects/create`)
                .then(res => {

                  this.attributes = res.data.attributes

                  this.types = res.data.projectTypes
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

    format_date (value) {

      if (value) {
        return moment(String(value)).format('DD/MM/YYYY hh:mm')
      }
    },

    setRoute () {
      return 'app/projects'

    },
    setDateTime () {
      let moment = require('moment-timezone')
      moment().tz('Asia/Jerusalem').format()
      this.form.start_date = moment(new Date()).format('DD/MM/YYYY')
      this.form.end_date = moment(new Date()).add(30, 'm').format('DD/MM/YYYY')

    },
    submit () {
      this.isSaving = true
      let route = !this.modal ? '/projects' : `/${this.modal}`
      if (this.form.id && this.form.id !== '') {

        this.form
            .put(`app/projects/${this.form.id}`)
            .then(() => {
              this.$event.fire('refreshProjectsTable')
              this.notify(
                  'success',
                  'פרוייקט עודכן'
              )

              this.$router.push(route)
            })
            .catch(err => this.$event.fire('appError', err.response))
            .finally(() => (this.isSaving = false))
      } else {

        this.form
            .post('app/projects/add')
            .then(() => {
              this.$event.fire('refreshProjectsTable')
              this.notify(
                  'success',
                  'בום עוד פרוייקט'
              )

              this.$router.push(route)
            })
            .catch(err => this.$event.fire('appError', err.response))
            .finally(() => (this.isSaving = false))
      }
    },
    fetchProject (id) {

      this.$http
          .get(`app/projects/${id}`)
          .then(res => {

            this.attributes = res.data.project.attributes
            delete res.data.project.attributes
            this.form = new this.$form(res.data.project)

            this.types = res.data.projectTypes

            this.form.type = res.data.project.type[0];
            this.form.end_date = this.format_date(res.data.project.end_date)
            this.form.start_date = this.format_date(res.data.project.start_date)
            this.form.date_to_complete = this.format_date(res.data.project.date_to_complete)

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
    addEvent () {

      this.$modal.show('event-form-modal', { projectId: this.$route.params.id })
    },
  },

}
</script>
<style>
</style>
<template>
  <div class="wrapper">
    <div class="panel panel-default">
      <div class="panel-heading">
        <button @click="goBack" v-if="projectName" class="button is-primary is-small is-pulled-right">
          <p class="back-title"> חזרה </p>
          <i class="fas fa-backward"/>
        </button>
        <a @click="addEvent(null)"
           class="button is-link is-small is-pulled-right">
          <i class="fas fa-plus m-l-sm"/> התקשרות חדשה
        </a>

        <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
        התקשרויות
      </div>

      <div>

        <v-server-table
            name="eventsListTable"
            :url="url"
            :columns="columns"
            :options="options"
            ref="eventsListTable"
            class="center-text-table table is-bordered is-striped is-narrow is-hoverable is-fullwidth"
        >
          <template slot="contact" slot-scope="props">
            <div class="has-text-centered">

              <p class="control tooltip">
                <router-link :to="'/customer/contact/' + props.row.contact_id">
                  {{
                    props.row.contact ?
                        props.row.contact.first_name + ' ' + (props.row.contact.last_name ? props.row.contact.last_name : '') : ''
                  }}
                </router-link>
              </p>
            </div>
          </template>
          <template slot="project" slot-scope="props">
            <div class="has-text-centered">

              <p class="control tooltip">
                <router-link :to="'/projects/edit/' + props.row.project_id">
                  {{ props.row.project ? props.row.project.name : '' }}
                </router-link>
              </p>
            </div>
          </template>
          <template slot="type" slot-scope="props">
            <div class="has-text-centered" :style="{background: props.row.type ? props.row.type.color : ''} ">
              {{ props.row.type ? props.row.type.name : '' }}
            </div>
          </template>
          <template slot="start_date" slot-scope="props">
            <div class="has-text-centered">
              {{ format_date(props.row.start_date) }}
            </div>
          </template>
          <template slot="end_date" slot-scope="props">
            <div class="has-text-centered">
              {{ format_date(props.row.end_date) }}
            </div>
          </template>
          <template slot="details" slot-scope="props">

            <div class="has-text-centered details-event" :data-tooltip="clearContent(props.row.details)">
              <p class="control has-tooltip-right" >
              {{ props.row.details| truncate(200, '...', props.row.details) }}
<!--                <span class="tooltip-text has-tooltip-left">{{props.row.details}}</span>-->
              </p>
            </div>
          </template>
          <template slot="actions" slot-scope="props">
            <div class="buttons has-addons events-button" style="direction: ltr">
              <p class="control tooltip">
                <a @click="showEvent(props.row)"
                   class="button is-link is-small ">
                  <i class="fas fa-edit"/>
                  <span class="tooltip-text">עריכה</span>
                </a>
              </p>
              <p class="control tooltip">
                <button type="button" class="button is-danger is-small" @click="deleteEvent(props.row.id)">
                  <i class="fas fa-trash"></i>
                  <span class="tooltip-text">מחיקה</span>
                </button>
              </p>
              <p class="control tooltip">
<!--                <router-link style="font-size: 0.65rem" :to="{ path: '/tasks/edit/event', params: { projId: 5 }}" class="button is-warning is-small">-->
<!--                  <i class="fas fa-edit"></i>-->
<!--                  <span class="tooltip-text">עריכה</span>-->
<!--&lt;!&ndash;                  <router-link :to="{ path: '/tasks/edit/', params: { projId: 5 }}">Home</router-link>&ndash;&gt;-->
<!--                </router-link>-->
                  <a @click="addTask(props.row.contact)" class="button is-warning	has-text-white is-small">
                   <i class="fas fa-thumbtack"></i>
                    <span class="tooltip-text"> משימה חדשה</span>
                  </a>
               </p>
            </div>
          </template>
        </v-server-table>
      </div>
    </div>
    <panel-filters-component
        v-if="filtering"
        :filters="filters"
        :class="{ loaded: filtering }"
        @hide-panel-filters="filtering = false"
    ></panel-filters-component>
    <router-view></router-view>

    <div v-if="editEvent">
      <event-form-modal
          :eventId="eventId"
      ></event-form-modal>
    </div>


  </div>
</template>

<script>
import mId from '../../mixins/Mid'
import tBus from '../../mixins/Tbus'
import EventFormModal from '../calendar/EventFormModal.vue'
import '@creativebulma/bulma-tooltip/dist/bulma-tooltip.min.css'
import TaskFormModal from "../tasks/TaskFormModal";
export default {
  mixins: [mId, tBus('app/events/eventsList')],
  props: [
    'modal',
    'customerId',
    'customerName',
    'projectId',
    'projectName','params'
  ],
  data() {
    return {
      eventId: '',
      showTaskForm: false,
      editEvent: false,
      loading: true,
      columns: ['title', 'contact', 'project', 'start_date', 'end_date', 'type', 'details', 'actions'],
      filters: new this.$form(
          {
            title: '',
            details: '',
            type: '',
            contact: ''
          }),
      options: {
        filterByColumn: true,
        perPage: 10,
        dateColumns: ['start_date', 'end_date'],
        orderBy: {ascending: true, column: 'title'},
        sortable: ['title', 'start_date', 'end_date', 'created_at', 'type', 'contact', 'project'],
        listColumns: {},
        filterable: ['title', 'details', 'type','contact', 'start_date', 'end_date', 'project'],
        headings: {
          title: 'נושא',
          contact: 'איש קשר',
          project: 'פרוייקט',
          details: 'תוכן',
          type: 'סוג',
          start_date: 'זמן התחלה',
          end_date: 'זמן סיום',
          actions: 'פעולות',

        },
        columnsClasses: {
          id: 'w50 has-text-centered',
          title: 'w175  header-table-font',
          contact: 'w75 header-table-font',
          project: 'w75 has-text-centered header-table-font',
          details: 'w250 header-table-font',
          type: 'w75 header-table-font',
          start_date: 'w75 header-table-font',
          end_date: 'w75 header-table-font',
          actions: 'w75 header-table-font',
        },
      },
    }
  },

  created() {
    this.setTextFilter()
    this.loading = false
    if (this.$store.getters.admin || this.$store.getters.superAdmin) {
      let filters = {title: '', details: ''}
      let actions = this.columns.pop()

      this.columns.push(actions)
      this.filters = new this.$form(filters)
      this.getSelectableFilters();
    }
  },
  methods: {
    getSelectableFilters() {
      let types = ''
      let em = this
      this.$http
          .get(`app/eventsTypes/`)
          .then(res => {
            em.types =res.data.map(({name, id}) => ({text: name, id: name}))
            this.$http
                .post(`app/projects/tablefilter`)
                .then(res => {
                  em.options.listColumns = {
                    project: res.data.map(({text, id}) => ({text: text, id: text})),
                    type: em.types
                  }
                })
                .catch(err => this.$event.fire('appError', err.response))
          })
          .catch(err => this.$event.fire('appError', err.response))
    },
    format_date(value) {

      if (value) {
        return moment(String(value)).format('DD/MM/YYYY hh:mm')
      }
    },
    addEvent() {

      this.$modal.show('event-form-modal', {event: null})
    },
    showEvent(event) {

      this.$modal.show('event-form-modal', {event: event})
    },
    addTask(contact) {
      console.log(contact.customer_id)
      this.$root.$router.push({path : '/tasks/add/', query: {'cusId': contact.customer_id}})
      this.showTaskForm = true

    },
    goBack() {
      if (this.customerId) {
        this.$emit("showCustomerList", true);
      } else if (this.projectId) {
        this.$emit("showProjectsList", true);
      }
    },
    deleteEvent(eventId) {
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
                  .delete(`app/events/${eventId}`)
                  .then(() => {
                    this.$event.fire('refreshEventsListTable')
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
    setTextFilter(){
      // this is here because the clear button is not changing the filter text from the previous date selected
      // to the filter title
      let em = this;
      setTimeout(()=>{
        let element = em.$el.querySelector('.VueTables__filter-placeholder');
        element.addEventListener('click', function(event) {
          let btn = document.querySelectorAll('.cancelBtn');

          btn.forEach(function (item, idx) {
            item.addEventListener('click', function (e) {
              console.log(e)
              let dateFilter = document.querySelector('#VueTables__start_date-filter')
              dateFilter.innerHTML = "סינון זמן התחלה\n"
              let dateFilter1 = document.querySelector('#VueTables__end_date-filter')
              dateFilter1.innerHTML = "סינון זמן סיום\n"
            });
          });
        });
      },200)
    },
  },

  computed: {
    url: {
      get() {
        let route = 'app/events/eventsList';
        if (this.customerId) {
          route = `app/customers/events/${this.customerId}`;
        } else if (this.projectId) {

          route = `app/projects/events/${this.projectId}`;
        }
        return route;
      },

    },

  },
  components: {EventFormModal, TaskFormModal},
}
</script>
<style scoped>
tr td:nth-child(2) {
  width: 100px;
}

.details-event{
  font-size: 14px;
}
</style>
<style scoped>
tr td:nth-child(2) {
  width: 100px;
}

.details-event{
  font-size: 14px;
}

.events-button {
  align-items: center;
  display: flex;
  flex-wrap: unset;
  justify-content: center;
}
</style>
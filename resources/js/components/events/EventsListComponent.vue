<template>
  <div class="wrapper">
    <div class="panel panel-default">
      <div class="panel-heading">
        <button @click="goBack" v-if="customerName || projectName" class="button is-primary is-small is-pulled-right">
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

      <div class="panel-block table-body-br">

        <v-server-table name="eventsListTable" :url="url" :columns="columns" :options="options" ref="eventsListTable">
          <template slot="contact" slot-scope="props">
            <div class="has-text-centered">

              <p class="control tooltip">
                <router-link :to="'/customer/contact/' + props.row.contact_id">
                  {{ props.row.contact ? props.row.contact.first_name + ' ' + props.row.contact.last_name : '' }}
                </router-link>
              </p>
            </div>
          </template>
          <template slot="project" slot-scope="props">
            <div class="has-text-centered">

              <p class="control tooltip">
                <router-link :to="'/projects/' + props.row.project_id">
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

          <template slot="actions" slot-scope="props">
            <div class="buttons has-addons is-centered">
              <p class="control tooltip">
                <a @click="showEvent(props.row)"
                   class="button is-link is-small ">
                  <i class="fas fa-edit"/>
                  <span class="tooltip-text">עריכה</span>
                </a>
                <!--                <router-link :to="'/events/edit/' + props.row.id" class="button is-warning is-small">-->
                <!--                  <i class="fas fa-edit"></i>-->
                <!--                  <span class="tooltip-text">עריכה</span>-->
                <!--                </router-link>-->
              </p>
              <p class="control tooltip">
                <button type="button" class="button is-danger is-small" @click="deleteEvent(props.row.id)">
                  <i class="fas fa-trash"></i>
                  <span class="tooltip-text">מחיקה</span>
                </button>
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

export default {
  mixins: [mId, tBus('app/events/eventsList')],
  props: [
    'modal',
    'customerId',
    'customerName',
    'projectId',
    'projectName'
  ],
  data() {
    return {
      eventId: '',
      editEvent: false,
      loading: true,
      columns: ['title', 'contact', 'project', 'start_date', 'end_date', 'type', 'details', 'actions'],
      filters: new this.$form({title: '', details: ''}),
      options: {
        perPage: 10,
        orderBy: {ascending: true, column: 'title'},
        sortable: ['title', 'start_date', 'end_date', 'created_at', 'type'],
        columnsClasses: {id: 'w50 has-text-centered', qty: 'w75', actions: 'w125 has-text-centered p-x-none'},
        filterable: ['title', 'details'],
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
      },
    }
  },
  created() {

    this.loading = false
    if (this.$store.getters.admin || this.$store.getters.superAdmin) {
      let filters = {title: '', details: ''}
      let actions = this.columns.pop()

      this.columns.push(actions)
      this.filters = new this.$form(filters)
    }
  },
  methods: {
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
    goBack() {
      if(this.customerId) {
        this.$emit("showCustomerList", true);
      }else if (this.projectId) {
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
    }
  },
  computed: {
    url: {
      get () {
        let route = 'app/events/eventsList';
        if (this.customerId) {
          route = `app/customers/events/${this.customerId}`;
        }
        else if (this.projectId) {

          route = `app/projects/events/${this.projectId}`;
        }
        return route;
      },

    },

  },
  components: {EventFormModal},
}
</script>

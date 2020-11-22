<template>
  <div class='wrapper'>

    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="control is-pulled-right">

          <select class="select fetch-calendar-data " v-model="fetchData" >
            <option value="0">משימות</option>
            <option value="1">התקשרויות</option>
            <option value="2">משימות + התקשרויות</option>

          </select>


        <a @click="addEvent"
           class="button is-link is-small is-pulled-right">
          <i class="fas fa-plus m-l-sm"/> הוספת התקשרות
        </a>
        </div>
        יומני היקר

      </div>
      <div class="panel-body">
        <FullCalendar

            :options='calendarOptions'
            ref="calendar">
        </FullCalendar>

      </div>
    </div>
    <event-form-modal :eventId="eventId"></event-form-modal>
  </div>
</template>
<script>
import FullCalendar from '@fullcalendar/vue'
import EventFormModal from "./EventFormModal.vue";
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import '@fullcalendar/timegrid/main.css'
import heLocale from '@fullcalendar/core/locales/he.js';
import TaskFormModal from "../tasks/TaskFormModal";

export default {
  components: {
    FullCalendar, EventFormModal, TaskFormModal
  },
  data: function () {
    return {
      fetchData: '0',
      eventId: '',
      calendarOptions: {
        slotMinTime: "08:00:00",
        locales: [heLocale],
        plugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin // needed for dateClick
        ],
        headerToolbar: {

          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'timeGridWeek',
        eventBackgroundColor: 'red',
        displayEventTime: false,
        eventColor: 'black',
        editable: true,
        selectable: true,
        selectMirror: true,

        weekends: true,
        select: this.handleDateSelect,
        eventClick: this.handleEventClick,
        eventsSet: this.handleEvents,
        eventDrop: this.handleDrop,
        eventResize: this.handleDrag,
        events: [],

      },
      dir: 'rtl',
    }
  },
  created() {
    console.log(this.$event)
    this.$event.listen("refreshEvents", () => {

      this.fetchEvents(moment(this.showDate).format("YYYY-MM-DD"));
    });

  },
  beforeMount() {

    setTimeout(() => {

      let dates = this.formatDates(this.$refs.calendar.getApi().currentData.dateProfile.activeRange)

      this.fetchEvents(dates.start, dates.end);
      this.setButtonsListeners();

    }, 100)

  },

  watch: {
    showDate: function(val) {
      this.fetchEvents(moment(this.showDate).format("YYYY-MM"));
    },
    fetchData: function(val) {
     let dates = this.formatDates(this.$refs.calendar.getApi().currentData.dateProfile.activeRange)
      this.fetchData = val;
      this.fetchEvents(dates.start, dates.end);
    }
  },
  methods: {
    fetchEvents(start, end) {
    console.log(this.fetchData)
      this.loading = true;
      this.$http
          .get(`app/events?start=${start}&end=${end}&fetchData=${this.fetchData}`)
          .then(res => {
            let calendarView = this.$refs.calendar.$vnode.componentInstance.$options.calendar.currentData.currentViewType;
            let taskAndEvents = [...res.data.tasks, ...res.data.events];
            this.calendarOptions.events = taskAndEvents.map(event => {
              if (calendarView === 'dayGridMonth') {
                event.allDay = true
              }
              event.start = new Date(event.start_date);
              event.end = new Date(event.end_date);

              let statusColor = (event.status !== undefined && event.status !== null) ? event.status.color : '';

              event.backgroundColor = event.type ? event.type.color : statusColor;

              event.textColor = 'black';

              return event;
            });
          })
    },
    handleWeekendsToggle() {
      this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
    },
    handleDateSelect(selectInfo) {
      // let title = prompt('Please enter a new title for your event')
      // let calendarApi = selectInfo.view.calendar
      // calendarApi.unselect() // clear date selection
      // if (title) {
      //   calendarApi.addEvent({
      //     id: createEventId(),
      //     title,
      //     start: selectInfo.startStr,
      //     end: selectInfo.endStr,
      //     allDay: selectInfo.allDay
      //   })
      // }
    },
    handleEventClick(clickInfo) {
      //task have customer_id and event have contact_id
      if (clickInfo.event._def.extendedProps.customer_id) {
        this.$router.push({path: `/calendar-tasks/edit/${clickInfo.event._def.publicId}`})
      } else {
        this.eventId = clickInfo.event._def.publicId
        this.$modal.show("event-form-modal", {eventId: clickInfo.event._def.publicId});
      }
    },
    handleEvents(events) {
      this.events = events
    },
    handleDrop(event) {
      let updateRoute = event.event._def.extendedProps.customer_id ? 'tasks' : 'events';
      console.log(updateRoute)
      let start = moment.utc(event.event._instance.range.start).format("YYYY-MM-DD H:m:s");
      let end = moment.utc(event.event._instance.range.end).format("YYYY-MM-DD H:m:s");

      this.$http
          .post(`app/${updateRoute}/calender/dates`, {'event': event, 'start': start, 'end': end})

          .then(res => {
            console.log(res.data)


          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    handleDrag(event) {
      let updateRoute = event.event._def.extendedProps.customer_id ? 'tasks' : 'events';

      let start = moment.utc(event.event._instance.range.start).format("YYYY-MM-DD H:m:s");
      let end = moment.utc(event.event._instance.range.end).format("YYYY-MM-DD H:m:s");

      this.$http
          .post(`app/${updateRoute}/calender/dates`, {'event': event, 'start': start, 'end': end})

          .then(res => {
            console.log(res.data)


          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    addEvent() {
      this.$modal.show("event-form-modal", {event: null});
    },
    formatDates(dates) {
      let start = moment(dates.start).format("YYYY-MM-DD")
      let end = moment(dates.end).format("YYYY-MM-DD")
      return {'start': start, 'end': end};
    },
    setButtonsListeners() {
      let nextButton = this.$el.querySelector('.fc-next-button');
      let prevButton = this.$el.querySelector('.fc-prev-button');
      let day = this.$el.querySelector('.fc-timeGridDay-button');
      let week = this.$el.querySelector('.fc-timeGridWeek-button');
      let month = this.$el.querySelector('.fc-dayGridMonth-button');

      day.addEventListener('click', () => {
        let dates = this.formatDates(this.$refs.calendar.getApi().currentData.dateProfile.activeRange)
        this.fetchEvents(dates.start, dates.end);
      });
      week.addEventListener('click', () => {
        let dates = this.formatDates(this.$refs.calendar.getApi().currentData.dateProfile.activeRange)

        this.fetchEvents(dates.start, dates.end);
      });
      month.addEventListener('click', () => {
        let dates = this.formatDates(this.$refs.calendar.getApi().currentData.dateProfile.activeRange)
        this.fetchEvents(dates.start, dates.end);
      });

      nextButton.addEventListener('click', () => {
        let dates = this.formatDates(this.$refs.calendar.getApi().currentData.dateProfile.activeRange)

        this.fetchEvents(dates.start, dates.end);
      });

      prevButton.addEventListener('click', () => {
        let dates = this.formatDates(this.$refs.calendar.getApi().currentData.dateProfile.activeRange)

        this.fetchEvents(dates.start, dates.end);
      });
    }
  },

}
</script>
<style scoped>
.fetch-calendar-data {
  font-size: 12px;
  height: 2.5em !important;
  margin-left: 5px;
  border-radius: 4px;
  font-weight: bold;
}



.fc-event-title {
  color: black !important;
}

.fc-scrollgrid-sync-table {
  direction: rtl;
}

.fc-col-header {
  direction: rtl
}

h2 {
  margin: 0;
  font-size: 16px;
}

ul {
  margin: 0;
  padding: 0 0 0 1.5em;
}

li {
  margin: 1.5em 0;
  padding: 0;
}

b { /* used for event dates/times */
  margin-right: 3px;
}


.fc {
  max-width: 1100px;
  margin: 0 auto;
}
</style>
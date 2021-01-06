<template>
  <div class='wrapper'>

    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="control is-pulled-right">
          <div style="direction: ltr" class="buttons">
            <span class="control tooltip">
              <select class="button select fetch-calendar-data " v-model="fetchData">
                <option value="0">משימות</option>
                <option value="1">התקשרויות</option>
                <option value="2">משימות + התקשרויות</option>

              </select>
            </span>
            <span class="control tooltip">
               <a @click="addEvent" class="button is-link is-small is-pulled-left">
                     <i class="fas fa-comment-dots"></i>
                     <span class="tooltip-text bottom">התקשרות חדשה</span>
               </a>
            </span>
            <span class="control tooltip">
                  <a @click="addTask" class="button is-info is-small is-pulled-right">
                     <i class="fas fa-thumbtack"/>
                     <span class="tooltip-text bottom"> משימה חדשה</span>
                  </a>
            </span>
          </div>
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
    <task-or-event-dialog></task-or-event-dialog>
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
import TaskOrEventDialog from "./TaskOrEventDialog";

export default {
  components: {
    FullCalendar, EventFormModal, TaskFormModal, TaskOrEventDialog
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
        // eventDragStart: this.handleDragStart,
        events: [],

      },
      dir: 'rtl',
    }
  },
  created() {

    this.$event.listen("refreshEvents", () => {
      let activeRange = this.$refs.calendar.getApi().currentData.dateProfile.activeRange;
      let startDate = moment(activeRange.start).format("YYYY-MM-DD");
      let endDate = moment(activeRange.end).format("YYYY-MM-DD")
      this.fetchEvents(startDate, endDate);
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
    showDate: function (val) {
      this.fetchEvents(moment(this.showDate).format("YYYY-MM"));
    },
    fetchData: function (val) {
      let dates = this.formatDates(this.$refs.calendar.getApi().currentData.dateProfile.activeRange)
      this.fetchData = val;
      this.fetchEvents(dates.start, dates.end);
    }
  },

  methods: {
    fetchEvents(start, end) {

      this.loading = true;
      this.$http
          .get(`app/events?start=${start}&end=${end}&fetchData=${this.fetchData}`)
          .then(res => {

            let calendarView = this.$refs.calendar.$vnode.componentInstance.$options.calendar.currentData.currentViewType;
            let taskAndEvents = [...res.data.tasks, ...res.data.events, ...res.data.repeatTasksEventsTitles];

            this.calendarOptions.events = taskAndEvents.map(event => {
              if (calendarView === 'dayGridMonth') {
                event.allDay = true
              }
              event.start = new Date(event.start_date);
              event.end = new Date(event.end_date);

              let statusColor = (event.status !== undefined && event.status !== null) ? event.status.color : '';

              event.backgroundColor = event.type ? event.type.color : statusColor;

              event.textColor = 'black';
              let eventCustomerName = event.customer ? ' (' + event.customer.name + ')' : ''
              let eventTitle = event.name ? event.name : event.title;
              event.title = eventTitle + eventCustomerName;
              return event;
            });
          })
    },
    handleWeekendsToggle() {
      this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
    },
    handleDateSelect(calendarDates) {
      this.$modal.show("task-or-event-dialog", calendarDates);
    },
    handleEventClick(clickInfo) {
      //task have customer_id and event have contact_id
      if (clickInfo.event._def.extendedProps.customer_id) {
        this.$router.push({path: `/calendar-tasks/edit/${clickInfo.event._def.publicId}`})
      } else if (clickInfo.event._def.extendedProps.contact_id) {
        this.eventId = clickInfo.event._def.publicId
        this.$modal.show("event-form-modal", {eventId: clickInfo.event._def.publicId});
      } else {

        this.getRepeatedTask(clickInfo.event._def.extendedProps.task_id, clickInfo.event._def.publicId);
      }
    },
    handleEvents(events) {
      this.events = events
    },
    handleDrop(event) {
      let updateRoute = event.event._def.extendedProps.customer_id ? 'tasks' : 'events';
      let isRepeat = event.event._def.extendedProps.repeat;

      let start = moment.utc(event.event._instance.range.start).format("YYYY-MM-DD H:m:s");
      let end = moment.utc(event.event._instance.range.end).format("YYYY-MM-DD H:m:s");
      if (!isRepeat) {
        this.$http
            .post(`app/${updateRoute}/calender/dates`, {'event': event, 'start': start, 'end': end})
            .then(res => {
              //Todo
            })
            .catch(err => {
              this.$event.fire('appError', err.response)
            })
      } else {
        let originalStartDay = moment(event.event._def.extendedProps.start_date).day();
        let newStartDay = moment(start).day();

        if (originalStartDay !== newStartDay) {

          let msg = confirm("מעבר יום של אירוע חוזר יוציא את האירוע מסט האירועים החוזרים שלו");
          if (msg == true) {
            // if user move event to different day create new task or event
            this.handleDropRepeat(event, start, end, false, true);
          } else {
            event.revert();
          }
        } else {

          this.repeatDialogMassage(event, start, end)
        }

      }

    },
    handleDropRepeat(event, start, end, toAllEvents, createNewTaskEvent) {
      let dates = this.setChangeOnFirstEvent(event.event._def.extendedProps.task, start, end, createNewTaskEvent)

      let start_date = moment(dates[0]).format("YYYY-MM-DD H:m:s");
      let end_date = moment(dates[1]).format("YYYY-MM-DD H:m:s");
      this.$http
          .post(`app/tasks/calender/repeat/dates`, {
            'event': event,
            'start': start_date,
            'end': end_date,
            'toAllEvents': toAllEvents,
            'createNewTaskEvent' : createNewTaskEvent
          })
          .then(res => {
            //Todo
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },

    setChangeOnFirstEvent(firstEvent, start, end, createNewTaskEvent) {

      let originStartDateTask = new Date(firstEvent.start_date);
      let originEndDateTask = new Date(firstEvent.end_date);
      let changeStartTask = new Date(start)
      let changeEndTask = new Date(end)

      originStartDateTask.setHours(changeStartTask.getHours())
      originStartDateTask.setMinutes(changeStartTask.getMinutes())

      originEndDateTask.setHours(changeEndTask.getHours())
      originEndDateTask.setMinutes(changeEndTask.getMinutes())

      if (createNewTaskEvent) {
        originStartDateTask.setDate(changeStartTask.getDate())
        originEndDateTask.setDate(changeEndTask.getDate())
      }

      return [originStartDateTask, originEndDateTask]
    },

    handleDragStart(event) {

      event.revert();
      let em = this
      this.$modal.show("dialog", {
        title: "שינוי יום אירוע חוזר",
        text: "השינוי יוציא את האירוע מה סט האירועים החוזרים אליהם הוא שייך",
        buttons: [
          {
            title: "אישור",
            class: "button is-danger is-radiusless is-radius-bottom-left",
            handler: () => {
              event.disableDragging = true
              em.$modal.hide("dialog");

            }
          },
          {
            title: "ביטול", class: "button is-warning is-radiusless is-radius-bottom-right",
            handler: () => {

              em.$modal.hide("dialog");
            }
          }
        ]
      });





    },
    handleDrag(event) {
      let isRepeat = event.event._def.extendedProps.repeat;

      let updateRoute = event.event._def.extendedProps.customer_id ? 'tasks' : 'events';

      let start = moment.utc(event.event._instance.range.start).format("YYYY-MM-DD H:m:s");
      let end = moment.utc(event.event._instance.range.end).format("YYYY-MM-DD H:m:s");

      if (!isRepeat) {
        this.$http
            .post(`app/${updateRoute}/calender/dates`, {'event': event, 'start': start, 'end': end})
            .then(res => {
              //Todo
            })
            .catch(err => {
              this.$event.fire('appError', err.response)
            })
      }else {
        this.repeatDialogMassage(event, start, end)
      }
    },
    addEvent() {
      this.$modal.show("event-form-modal", {event: null});
    },
    addTask() {
      this.$router.push({path: `/tasks/add/`})
    },
    getRepeatedTask(taskId, repeatTaskId) {
      this.$router.push({path: `/calendar-tasks/edit/${taskId}?repeatTaskId=${repeatTaskId}`})
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
    },
    repeatDialogMassage(event, start, end) {
      let em = this;

      this.$modal.show("dialog", {
        title: "שינוי פרטים לאירוע חוזר",
        text: "האם לשנות לאירוע הזה או לכל האירועים שקשורים אליו",
        buttons: [
          {
            title: "לכל האירועים",
            class: "button is-danger is-radiusless is-radius-bottom-left",
            handler: () => {
              this.handleDropRepeat(event, start, end, true, false);
              em.$modal.hide("dialog");

            }
          },
          {
            title: "רק לאירוע הזה", class: "button is-warning is-radiusless is-radius-bottom-right",
            handler: () => {
              this.handleDropRepeat(event, start, end, false,false);
              em.$modal.hide("dialog");
            }
          }
        ]
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
  background: lightgray;
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
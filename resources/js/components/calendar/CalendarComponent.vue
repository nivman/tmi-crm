<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a
                    @click="addEvent"
                    class="button is-link is-small is-pulled-right"
                >
                    <i class="fas fa-plus m-r-sm" /> Add New Event
                </a>
                Calendar
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-body">
                <div class="columns is-mobile">
                    <div class="column">
                        <calendar-view
                            :events="events"
                            :show-date="showDate"
                            :disable-past="false"
                            :starting-day-of-week="1"
                            @setShowDate="setShowDate"
                            @click-event="onClickEvent"
                            :display-period-uom="'month'"
                            @show-date-change="setShowDate"
                            :time-format-options="{
                                hour: 'numeric',
                                minute: '2-digit'
                            }"
                        />
                    </div>
                </div>
                <div class="columns is-mobile" v-if="isMobile">
                    <div class="column">
                        <ul>
                            <li
                                v-for="event in events"
                                :key="event.id"
                                class="message"
                                :class="{
                                    'is-danger': event.color == 'red',
                                    'is-success': event.color == 'green',
                                    'is-info': event.color == 'blue',
                                    'is-dark': event.color == 'black',
                                    'is-warning': event.color == 'orange',
                                    'is-link': event.color == 'purple'
                                }"
                            >
                                <div
                                    class="message-body"
                                    @click="onClickEvent(event, true)"
                                >
                                    <strong>{{
                                        event.start_date
                                            | formatDate(
                                                $store.state.settings.ac
                                                    .dateformat
                                            )
                                    }}</strong>
                                    {{ event.end_date ? "to" : "" }}
                                    <strong>{{
                                        event.end_date
                                            | formatDate(
                                                $store.state.settings.ac
                                                    .dateformat
                                            )
                                    }}</strong
                                    ><br />
                                    {{ event.title }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <event-form-modal></event-form-modal>
    </div>
</template>

<script>
import CalendarView from "vue-simple-calendar";
import EventFormModal from "./EventFormModal.vue";
require("vue-simple-calendar/dist/static/css/default.css");

export default {
    components: { CalendarView, EventFormModal },
    data() {
        return {
            loading: true,
            showDate: new Date(),
            events: []
        };
    },
    created() {
        this.$event.listen("refreshEvents", () => {
            this.fetchEvents(moment(this.showDate).format("YYYY-MM"));
        });
    },
    beforeMount() {
        this.fetchEvents(moment(this.showDate).format("YYYY-MM"));
    },
    watch: {
        showDate: function(val) {
            this.fetchEvents(moment(this.showDate).format("YYYY-MM"));
        }
    },
    methods: {
        fetchEvents(month) {
            this.loading = true;
            this.$http
                .get(`app/events?date=${month}`)
                .then(res => {
                    this.events = res.data.map(event => {
                        event.startDate = new Date(event.start_date);
                        event.endDate = event.end_date
                            ? new Date(event.end_date)
                            : "";
                        event.classes = event.color;
                        return event;
                    });
                    this.loading = false;
                })
                .catch(err => {
                    this.$event.fire("appError", err.response);
                });
        },
        setShowDate(d) {
            this.showDate = d;
        },
        addEvent() {
            this.$modal.show("event-form-modal", { event: null });
        },
        onClickEvent(e, isE) {
            this.$modal.show("event-form-modal", {
                event: isE ? e : e.originalEvent
            });
        }
    }
};
</script>

<style lang="scss">
@import "../../../sass/variables";

.cv-header-days {
    font-weight: bold;
}
.cv-header,
.cv-header button,
.cv-day,
.cv-event,
.cv-header-day,
.cv-header-days,
.cv-week,
.cv-weeks {
    border-color: #eee !important;
}
.cv-wrapper,
.cv-wrapper .cv-header-nav button,
.cv-wrapper .cv-event {
    border-radius: 0 !important;
    cursor: pointer;
}
.cv-wrapper .cv-header {
    border-radius: $radius $radius 0 0 !important;
}
.cv-wrapper .cv-weeks,
.cv-wrapper .cv-weeks .cv-week:last-child {
    border-radius: 0 0 $radius $radius !important;
}
.cv-wrapper .cv-weeks .cv-week:last-child .cv-day:first-child {
    border-bottom-left-radius: $radius !important;
}
.cv-wrapper .cv-weeks .cv-week:last-child .cv-day:last-child {
    border-bottom-right-radius: $radius !important;
}
// .cv-wrapper.period-month.periodCount-1,
// .cv-wrapper.period-week {
//     height: 40vw;
// }
.cv-wrapper .cv-event {
    margin: 2px 5px;
    display: inline-block;
    background-color: $white-bis;
    border-color: $grey-lighter;
    border: 0;
    color: $grey-dark;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    &:hover {
        font-weight: bold;
    }
}

.cv-wrapper .cv-event.span1 {
    width: calc(14.28571% - 10px) !important;
}
.cv-wrapper .cv-event.span2 {
    width: calc(28.57143% - 10px) !important;
}
.cv-wrapper .cv-event.span3 {
    width: calc(42.85714% - 10px) !important;
}
.cv-wrapper .cv-event.span4 {
    width: calc(57.14286% - 10px) !important;
}
.cv-wrapper .cv-event.span5 {
    width: calc(71.42857% - 10px) !important;
}
.cv-wrapper .cv-event.span6 {
    width: calc(85.71429% - 10px) !important;
}
.cv-wrapper .cv-event.span7 {
    width: calc(100% - 10px) !important;
}

.cv-wrapper .cv-event.blue {
    background-color: $blue !important;
    border-color: $blue !important;
    color: $white !important;
}
.cv-wrapper .cv-event.green {
    background-color: $green !important;
    border-color: $green !important;
    color: $white !important;
}
.cv-wrapper .cv-event.red {
    background-color: $red !important;
    border-color: $red !important;
    color: $white !important;
}
.cv-wrapper .cv-event.black {
    background-color: $black !important;
    border-color: $black !important;
    color: $white !important;
}
.cv-wrapper .cv-event.orange {
    background-color: $orange !important;
    border-color: $orange !important;
    color: $white !important;
}
.cv-wrapper .cv-event.purple {
    background-color: $purple !important;
    border-color: $purple !important;
    color: $white !important;
}
.cv-wrapper .cv-event.continued::before,
.cv-wrapper .cv-event.toBeContinued::after {
    color: $yellow !important;
}
.cv-wrapper {
    .cv-day,
    .cv-week {
        height: 120px;
        min-height: 120px;
        .outsideOfMonth {
            color: #aaa;
        }
    }
}
@include mobile {
    .cv-wrapper {
        .cv-header {
            display: flex;
            margin-bottom: 5px;
            flex-direction: column-reverse;
        }
        .cv-week,
        .cv-day {
            height: 25px !important;
            min-height: 25px !important;
        }
        .cv-event {
            height: 0 !important;
            display: none !important;
        }
    }
}
</style>

<template>
  <modal
      name="recurring-schedule-modal"
      height="auto"
      :scrollable="true"
      classes="is-rounded rtl-direction recurring-schedule-modal"
      transition="custom"
      @opened="beforeOpen">
    <form autocomplete="off" action="#">
      <div class="modal-card animated fastest zoomIn modal-card-recurring-schedule " style="width: 100%;">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            משימה חוזרת
          </p>
          <button
              type="button"
              class="delete"
              @click="$modal.hide('recurring-schedule-modal')">
          </button>
        </header>
        <section class="modal-card-body is-radius-bottom">
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="end">זמן חזרה</label>
                <select class="custom-select select input" id="repeat_option" :value="value.repeatOption.type" v-model="repeatType"
                        @change="updateRepeatOption($event)">
                  <option value="every_month">כל חודש</option>
                  <option value="every_week">כל שבוע</option>
                  <option value="every_day">כל יום</option>
                </select>
              </div>
            </div>
            <div class="column">
              <div class="field">
                <label class="label" for="end">סיום</label>
                <v-select
                    label="end"
                    id="end"
                    name="end"
                    class="rtl-direction"
                    item-value="value"
                    item-text="name"
                    single-line
                    @input="endRepeatOption"
                    :options="endOption"
                    v-model="end">
                </v-select>
              </div>
            </div>
            <div class="column">
              <div class="field" style="width: 110%">
                <label class="label" for="repeatTimeStartDate">התחלה בתאריך</label>
                <flat-pickr
                    class="input"
                    id="repeatTimeStartDate"
                    name="repeatTimeStartDate"
                    enableTime="true"
                    :config="config"
                    v-model="startByRepetitionDate"
                ></flat-pickr>
              </div>
            </div>
            <div class="column">
              <div class="field">
                <label class="label" for="interval">אינטרוול</label>
                <input
                    @change="createDates()"
                    @keyup="createDates()"
                    id="interval"
                    type="number"
                    name="interval"
                    class="input"
                    v-model="interval"/>
              </div>
            </div>
            <hr>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="repeatTimeEndAfter">חזרות</label>
                <input
                    @change="createDates()"
                    @keyup="createDates()"
                    :style="{background: end.value === 'after' ? 'white': 'lightgrey',pointerEvents : end.value === 'after' ? '': 'none'} "
                    id="repeatTimeEndAfter"
                    type="number"
                    name="repeatTimeEndAfter"
                    class="input"
                    v-model="endByRepetition"/>
              </div>
            </div>
            <div class="column">
              <div class="field">

                <label class="label" for="repeatTimeEndDate">סיום בתאריך</label>
                <flat-pickr
                    @input="createDates()"
                    :style="{background: end.value === 'date' ? 'white': 'lightgrey',pointerEvents : end.value === 'date' ? '': 'none'} "
                    class="input repeatTimeEndDate1"
                    id="repeatTimeEndDate"
                    name="repeatTimeEndDate"
                    enableTime="true"
                    :config="config"
                    v-model="endByRepetitionDate"
                ></flat-pickr>
              </div>
            </div>
          </div>

          <div>
            <b> חזרה ביחידות </b>
          </div>
          <hr>
          <div>
            <div v-if="value.repeatOption.type === 'every_week'">
              <label v-for="date in weekDays">
                <input type="checkbox" :value="date.full_name" v-model="everyWeekPicker"
                       @change="createDates()">
                <span>{{ date.name }}</span>
              </label>
            </div>
            <div v-if="value.repeatOption.type === 'every_month'">
              <div class="columns">
                <div class="column onDayRadio-column">
                  <div class="field onDayRadio-field">
                    <label for="onDayRadio"></label>
                    <input type="radio" id="onDayRadio" name="monthPicker" v-model="monthRadioBox" value="0">
                  </div>
                </div>
                <div class="column">
                  <div class="field">
                    <label class="label" for="onMonthDay">תאריך בחודש</label>
                    <v-select
                        label="onMonthDay"
                        id="onMonthDay"
                        name="week"
                        :style="{background: monthRadioBox == '0' ? 'white': 'lightgrey',pointerEvents :monthRadioBox == '0' ? '': 'none'} "
                        class="rtl-direction"
                        item-value="value"
                        item-text="onMonthDay"
                        single-line
                        @input="createDates()"
                        :options="monthsDays"
                        v-model="onMonthDay">
                    </v-select>
                  </div>
                </div>
              </div>
              <div class="columns">

                <div class="column onDayRadio-column">
                  <div class=" onDayRadio-field">
                    <label for="onWeekRadio"></label>
                    <input type="radio" id="onWeekRadio" name="monthPicker" v-model="monthRadioBox" value="1">
                  </div>
                </div>
                <div class="column radio-box-days-column">
                  <div class="">
                    <label class="label" for="week">שבוע</label>
                    <v-select
                        label="week"
                        id="week"
                        name="week"
                        :style="{background: monthRadioBox == '1' ? 'white': 'lightgrey',pointerEvents :monthRadioBox == '1' ? '': 'none'} "
                        class="rtl-direction"
                        item-value="value"
                        item-text="week"
                        single-line
                        @input="createDates"
                        :options="weeksOption"
                        v-model="week">
                      <template v-slot:option="weeksOption">
                        <div class="weeksOption" v-html="weeksOption.week"
                             :style="{
                        direction: 'ltr',
                        position: 'relative',
                        textAlign: 'center',
                        display: 'block',} ">

                        </div>
                      </template>
                    </v-select>

                  </div>
                </div>
                <div class="column radio-box-days-column">
                  <div class="field radio-box-days-field">
                    <label class="label">ביום</label>
                    <label v-for="date in weekDays">
                      <input type="radio" class="radio-box-days" :value="date.full_name" v-model="everyWeekPicker"
                             @change="createDates()">
                      <span class="span-box-days">{{ date.name }}</span>
                    </label>

                  </div>
                </div>
              </div>
            </div>
            <div>
              {{ toString }}
            </div>
          </div>

        </section>
      </div>
      <div class="columns">
        <div class="column">
          <button
              type="button"
              class="button is-link is-fullwidth"
              @click="submitRecurrence">
            הוספה
          </button>
        </div>
      </div>

    </form>

    <button
        class="modal-close is-large is-hidden-mobile"
        aria-label="close"
        @click="$modal.hide('recurring-schedule-modal')"
    ></button>
  </modal>
</template>

<script>
import {RRule, RRuleSet, rrulestr} from 'rrule'

export default {
  props: ['value', 'startDate'],
  components: {RRule, RRuleSet, rrulestr},
  data() {
    return {
      toString: '',
      everyWeekPicker: [],
      endsPicked: this.value.endsPicked,
      end: '',
      days:'',
      monthRadioBox: '',
      interval: 1,
      onMonthDay: [],
      repeatType: '',
      rule: '',
      dayInWeek: '',
      endByRepetition: 1,
      endByRepetitionDate: '',
      startByRepetitionDate: new Date(),
      endOption: [{end: 'אף פעם', value: 'never'}, {end: 'חזרות', value: 'after'}, {end: 'בתאריך', value: 'date'}],
      weekDays: [
        {
          name: 'ראשון',
          value: '1',
          full_name: RRule.SU
        },
        {
          name: 'שני',
          value: '2',
          full_name: RRule.MO
        },
        {
          name: 'שלישי',
          value: '3',
          full_name: RRule.TU
        },
        {
          name: 'רביעי',
          value: '4',
          full_name: RRule.WE
        },
        {
          name: 'חמישי',
          value: '5',
          full_name: RRule.TH
        },
        {
          name: 'שישי',
          value: '6',
          full_name: RRule.FR
        }
        , {
          name: 'שבת',
          value: '7',
          full_name: RRule.SA
        },
      ],
      week: '',
      weeksOption: [
        {week: 'שבוע ראשון', value: 1},
        {week: 'שבוע שני', value: 2},
        {week: 'שבוע שלישי', value: 3},
        {week: 'שבוע רביעי', value: 4},
        {week: 'שבוע אחרון', value: -1}
      ],
      monthsDays: [
        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31
      ],
      config: {
        altInput: true,
        enableTime: true,
        altFormat: 'd/m/Y H:i',
        dateFormat: 'd/m/Y H:i',
        time_24hr: true
      }
    }
  },

  watch: {
    'end': function () {
      let endDateInput = document.querySelector('#repeatTimeEndDate ~ input');
      if (this.end.value === 'date') {
        this.endByRepetition = '';
        endDateInput.removeAttribute('disabled')
        endDateInput.style.backgroundColor = 'white';
      } else {
        this.endByRepetitionDate = '';
        this.endByRepetition = 1;
        endDateInput.setAttribute('disabled', 'disalbed')
        endDateInput.style.backgroundColor = 'lightgrey';
      }

    },
    'monthRadioBox': function () {
      if (this.monthRadioBox === '0') {
        this.week = ''
      } else {
        this.onMonthDay = ''
      }

    },
    'repeatType': function() {
      if (this.repeatType === 'every_day') {
        this.end = {end: 'חזרות', value: 'after'}
      }
     }
  },
  created() {

    if (this.startDate) {
      this.startByRepetitionDate = this.startDate
    }
  },
  methods: {
    submitRecurrence() {

      this.$emit('clicked', this.rule)
      this.$modal.hide('recurring-schedule-modal')
    },
    beforeOpen() {
      let endDateInput = document.querySelector('#repeatTimeEndDate ~ input');
      endDateInput.setAttribute('disabled', 'disalbed');
      endDateInput.style.backgroundColor = 'lightgrey';

    },
    emitData(data) {

      this.$emit('input', data);
      this.toString = this.generateString(data);
    },
    generateString(data) {
    },
    createDates() {

      if (this.end === '') {

        alert("צריך לבחור סיום לפני זה")

        return
      }
      let moment = require('moment-timezone');
      moment().tz('Asia/Jerusalem').format();
      let startDate = moment(this.startByRepetitionDate, 'DD/MM/YYYY HH:mm');

      let startYear = parseInt(startDate.format('YYYY'));
      let startMonth = parseInt(startDate.format('M'));
      let startDay = parseInt(startDate.format('D'));
      let startHour = parseInt(startDate.format('H'));
      let startMinute = parseInt(startDate.format('m'));
      let startDateTime = new Date(Date.UTC(startYear, startMonth - 1, startDay, startHour, startMinute, 0))
      let endDateTime = null;

      if (this.end.value === 'date' || this.end.value === 'never') {
        let date = this.end.value === 'date' ? this.endByRepetitionDate : new Date();
        endDateTime = moment(date, 'DD/MM/YYYY HH:mm');
        let endYear = parseInt(endDateTime.format('YYYY'));
        let endMonth = parseInt(endDateTime.format('M'));
        let endDay = parseInt(endDateTime.format('D'));
        let endHour = parseInt(endDateTime.format('H'));
        let endMinute = parseInt(endDateTime.format('m'));
        let setYear = this.end.value === 'date' ? endYear : endYear + 5
        endDateTime = new Date(Date.UTC(setYear, endMonth - 1, endDay, endHour, endMinute, 0))
      }
      let datesTime = {
        'startDateTime': startDateTime,
        'endDateTime': endDateTime
      }

      let count = this.end.value === 'after' ? parseInt(this.endByRepetition) : null;
      let rule = null
      if (this.value.repeatOption.type === 'every_week') {
        rule = this.updateWeekDayOption(datesTime, count);
      } else if (this.value.repeatOption.type === 'every_month') {
        rule = this.updateMonthDayOption(datesTime, count);
      }else{
        rule = this.updateDaysOption(datesTime, count)
      }
      this.printRule(rule);
    },

    updateDaysOption(datesTime, count) {

      let onMonthDay = this.monthRadioBox === '0' ? this.onMonthDay : null;

      let rule = new RRule({
        freq: RRule.DAILY,
        dtstart: datesTime.startDateTime,
        until: datesTime.endDateTime,
        bysetpos: this.week.value,
        byweekday: this.everyWeekPicker,
        bymonthday: onMonthDay,
        count: count,
        interval: this.interval
      })

      this.rule = rule;
      return rule;
    },

    updateMonthDayOption(datesTime, count) {

      let onMonthDay = this.monthRadioBox === '0' ? this.onMonthDay : null;

      let rule = new RRule({
        freq: RRule.MONTHLY,
        dtstart: datesTime.startDateTime,
        until: datesTime.endDateTime,
        bysetpos: this.week.value,
        byweekday: this.everyWeekPicker,
        bymonthday: onMonthDay,
        count: count,
        interval: this.interval
      })

      this.rule = rule;
      return rule;
    },

    updateWeekDayOption(datesTime, count) {

      let rule = new RRule({
        freq: RRule.WEEKLY,
        dtstart: datesTime.startDateTime,
        until: datesTime.endDateTime,
        byweekday: this.everyWeekPicker,
        count: count,
        interval: this.interval
      })

      this.rule = rule;
      return rule;
    },
    printRule(rule) {
      this.toString = rule.toText()

    },

    updateRepeatOption(event) {
      let data = {...this.value};
      data.repeatOption.type = event.target.value;
      this.emitData(data);
    },
    endRepeatOption() {


    },

  },

}
</script>

<style scoped>
input[type=checkbox] {
  display: none;
}

label :checked + span {
  background: #66bb6a;
  color: white;
}

label span {
  display: inline-block;
  background: lightgrey;
  height: 3em;
  width: 3em;
  line-height: 3em;
  text-align: center;
  border-radius: 50%;
  cursor: pointer;
  margin-right: 2px;
}

.round {
  position: relative;
}

.round label {
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 50%;
  cursor: pointer;
  height: 28px;
  left: 0;
  position: absolute;
  top: 0;
  width: 28px;
}

.round input[type="checkbox"] {
  visibility: hidden;
}

.round input[type="checkbox"]:checked + label {
  background-color: #66bb6a;
  border-color: #66bb6a;
}

.round input[type="checkbox"]:checked + label:after {
  opacity: 1;
}

.radio-box-days {
  visibility: hidden;
}

.onDayRadio-column {
  flex-grow: unset;
}

.onDayRadio-field {
  top: 60%;
  position: relative;
}

.span-box-days {
  display: inline-block;
  background: lightgrey;
  height: 2.5em;
  width: 2.5em;
  line-height: 2.5em;
  text-align: center;
  border-radius: 51%;
  cursor: pointer;
  margin-right: 1px;
  font-size: 14px;
}

.radio-box-days-column {

  flex-basis: unset;
}

.modal-card-recurring-schedule {
  height: 40em;
}

#week {
  width: 100%;
}
</style>
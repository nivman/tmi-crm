<template>
  <div class="wrapper">
    <div class="panel panel-default">
      <div class="panel-heading">

        <button @click="goBack" v-if="customerName || projectName" class="button is-primary is-small is-pulled-right">
          <p class="back-title"> חזרה </p>
          <i class="fas fa-backward"/>
        </button>
        <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop"
                @click="filtering = !filtering">
          <i class="fas fa-cog"/>
        </button>
        <router-link :to="addRoute" class="button is-link is-small is-pulled-right">
          <i class="fas fa-plus m-l-sm"/> הוספת משימה
        </router-link>
        משימות
        <span v-if="customerId || projectId"> עבור : <strong>{{customerName || projectName}}</strong> </span>

      </div>

      <div class="panel-block table-body-br">
        <v-server-table
            id="tasks-table"
            :url="url"
            :columns="columns"
            :options="options"
            ref="tasksTable"
            class="center-text-table"
            name="tasksTable">
          <template slot="customer" slot-scope="props">
            <div class="has-text-centered">
              {{ props.row.customer ? props.row.customer.name : '' }}

            </div>
          </template>
          <template slot="project" slot-scope="props">
            <div class="has-text-centered">
              {{ props.row.project ? props.row.project.name : '' }}

            </div>
          </template>
          <template slot="details" slot-scope="props" class="test">
            <textarea
                rows="3"
                name="details"
                :id="'details-textarea-'+ props.row.id"
                class="textarea details-textarea"
                @keyup="editDetails"
                v-model="props.row.details">
                </textarea>
          </template>
          <template slot="category" slot-scope="props">
            {{ props.row.category ? props.row.category.name : '' }}
          </template>
          <template slot="workingHours" slot-scope="props">
            {{ props.row.actual_time ?  parseFloat(props.row.actual_time / 60).toFixed(2)  : '' }}
          </template>

          <template slot="amountPerHours" slot-scope="props">
<!--            //TODO hour wage should be dynamic-->
            {{ props.row.actual_time ?  parseFloat(props.row.actual_time / 60 * 150).toFixed(2) : '' }}
          </template>
          <template slot="date_to_complete" slot-scope="props">

            <date-format-component :date="props.row.date_to_complete"></date-format-component>
          </template>

          <template slot="start_date" slot-scope="props">

            <date-format-component :dateTime="props.row.start_date"></date-format-component>
          </template>

          <template slot="priority" slot-scope="props">
            <div class="has-text-centered" :style="{background: props.row.priority ? props.row.priority.color : ''}">
              {{ props.row.priority ? props.row.priority.name : '' }}

            </div>
          </template>
          <template slot="status" slot-scope="props">
            <div class="has-text-centered" :style="{background: props.row.status ? props.row.status.color : ''}">
              {{ props.row.status ? props.row.status.name : '' }}

            </div>
          </template>
          <template slot="percentageOfProject" slot-scope="props">
            <div>
              <ul style="font-size: 12px">
                <li class="has-text-centered" v-if="props.row.actual_time">
                <strong>  מחיר פרוייקט</strong>
                </li>
                <li class="has-text-centered" >

                  <strong>{{ props.row.actual_time ? props.row.projectPrice : '' }} </strong>
                </li>
              </ul>
              <div class="has-text-centered">
                {{ props.row.actual_time ? calculatePercentage( props.row.actual_time, props.row.projectPrice) : '' }}
              </div>
            </div>
          </template>

          <template slot="actions" slot-scope="props">
            <div class="buttons has-addons is-centered">
              <p class="control tooltip">
                <router-link :to="'/tasks/' + props.row.id" class="button is-primary is-small">
                  <i class="fas fa-file-alt"/>
                  <span class="tooltip-text">פרטי משימה</span>
                </router-link>
              </p>
              <p class="control tooltip" v-if="$store.getters.admin">
                <router-link :to="'/tasks/edit/' + props.row.id" class="button is-warning is-small">
                  <i class="fas fa-edit"></i>
                  <span class="tooltip-text">עריכה</span>
                </router-link>
              </p>
              <p class="control tooltip" v-if="$store.getters.superAdmin">
                <button type="button" class="button is-danger is-small" @click="deleteRecord(props.row.id)">
                  <i class="fas fa-trash"></i>
                  <span class="tooltip-text">מחיקה</span>
                </button>
              </p>
            </div>
          </template>
        </v-server-table>
      </div>
    </div>

    <router-view></router-view>

  </div>
</template>

<script>
import mId from '../../mixins/Mid'
import tBus from '../../mixins/Tbus'
import DateFormatComponent from '../helpers/DateFormatComponent'
import 'daterangepicker/daterangepicker.css'
import 'daterangepicker/daterangepicker.js'

export default {
  mixins: [mId, tBus('app/tasks')],
  props: [
    'modal',
    'customerId',
    'customerName',
    'projectId',
    'projectName'
  ],
  data () {
    return {

      showTaskForm: false,
      columns: [
        'name',
        'customer',
        'project',
        'category',
        'start_date',
        'date_to_complete',
        'actual_time',
        'details',
        'priority',
        'status',
        'workingHours',
        'amountPerHours',
        'percentageOfProject',
        'actions'],
      filters: new this.$form({
        name: 'שם',
        customer: { name: 'לקוח' },
        project: 'פרוייקט',
        category: 'קטגוריה',
        date_to_complete: 'תאריך לביצוע',
        start_date: 'זמן התחלה',
        range: {
            startDate: '',
            endDate: ''
          },
        date_range: ''}),
        addRoute: null,
        options: {
        filterByColumn:true,
        dateColumns: ['date_to_complete','start_date'],
        datepickerOptions: {
          opens: 'right'
        },
        listColumns: ['customer','name', 'date_to_complete', 'start_date'],
        orderBy: { ascending: false, column: 'date_to_complete' },
        sortable: ['name','priority','customer','date_to_complete', 'project', 'status', 'category','start_date'],
        editableColumns: ['details'],
        perPage: 10,
        columnsClasses: {
          id: 'w50 has-text-centered',
          receivable: 'w125 has-text-right',
          actions: 'w175 has-text-centered p-x-none',
          details: 'details-td',
          date_to_complete: 'w50 has-text-centered'
        },
        filterable: ['name','details','customer', 'project', 'status', 'priority', 'category', 'date_to_complete', 'start_date'],
        headings: {
          name: 'נושא',
          customer: 'לקוח',
          project: 'פרוייקט',
          details: 'תוכן',
          workingHours: 'שעות עבודה',
          amountPerHours: 'סכום שעות העבודה',
          percentageOfProject : 'אחוז עבודה ביחס למחיר הפרוייקט',
          priority: 'עדיפות',
          status: 'סטטוס',
          date_to_complete: 'תאריך לביצוע',
          start_date: 'זמן התחלה',
          actual_time: 'זמן בפועל לביצוע',
          actions: 'פעולות',
          category: 'קטגוריה'
        },
      },
    }
  },

  methods: {

    editDetails (val) {
      let id = val.target.id.replace(/[^\d.]/g, '')
      this.$http(`app/tasks/details/${val.target.value}/${id}`).then()

    },
    goBack() {
      if(this.customerId) {
        this.$emit("showCustomerList", true);
      }else if (this.projectId) {
        this.$emit("showProjectsList", true);
      }
    },
    calculatePercentage(tasksTime, price) {
      if (tasksTime) {

        let convertToHours = (tasksTime / 60)
        //TODO hour wage should be dynamic
        let HourlyWage = convertToHours * 150; // how mush per hour need to be dynamic

        let totalTimeAsAmount = HourlyWage * 100 / price;

        let percentage = totalTimeAsAmount / price

        if (Number.isFinite(percentage)) {

          return ' % ' + totalTimeAsAmount.toFixed(2) ;
        }
      }
    },
    setTextFilter(){
      // this is here because the clear button is not changing the filter text from the previous date selected
      // to the filter title
      let em = this;
      setTimeout(()=>{
        let element = em.$el.querySelector('.VueTables__filter-placeholder');
        element.addEventListener('click', function(event) {
          let btn = document.querySelector('.cancelBtn');

          btn.addEventListener('click', function(event) {
            let dateFilter = document.querySelector('#VueTables__date_to_complete-filter')
            dateFilter.innerHTML = "סינון תאריך לביצוע\n"
          });
        });
      },200)
    }
  },

  created () {
      this.setTextFilter()
    if(this.customerId != undefined)    {

       this.addRoute = `/tasks/add?customerId=${this.customerId}`;
     }
     else if (this.projectId != undefined) {

       this.addRoute = `/tasks/add?projectId=${this.projectId}`;
     }else {

       this.addRoute = '/tasks/add';
     }
  //  this.addRoute = !this.customerId ? '/tasks/add' : `/tasks/add?customerId=${this.customerId}`;
  },
  computed: {
    url: {
      get () {

        let route = 'app/tasks';
        if (this.customerId) {
          route = `app/customers/tasks/${this.customerId}`;
        }
        else if (this.projectId) {
          route = `app/projects/tasks/${this.projectId}`;
        }
        return route;
      },

    },

  },
  components: { DateFormatComponent},

}
</script>
<style scoped>
table td .details-textarea {
  width: 100%;
  height: 100%;

}

#tasks-table table {
  width: 100%;
  height: 100%;
}

.details-td {
  padding: 0px !important;
}

.back-title{
  padding: 5px;
}
.svg-display{
  height: 30px !important;
}
.dates-range{
  display: inline;
}
.date-range-position{
  display: block !important;
}
.vc-popover-content-wrapper{
  direction: ltr;
}
.flatpickr-input{
  font-size: 12px !important;
}

</style>
<template>
  <div>
  <div class="wrapper"  v-if="!showTaskList && !showEventsList">
    <div class="panel panel-default">
      <div class="panel-heading">

        <button @click="goBack" v-if="customerName" class="button is-primary is-small is-pulled-right">
          <p class="back-title"> חזרה </p>
          <i class="fas fa-backward"/>
        </button>
        <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop"
                @click="filtering = !filtering">
          <i class="fas fa-cog"/>
        </button>
        <router-link :to="addRoute" class="button is-link is-small is-pulled-right">
          <i class="fas fa-plus m-l-sm"/> הוספת פרויקט
        </router-link>
        פרוייקטים
        <span v-if="customerId"> עבור : <strong>{{customerName}}</strong> </span>

      </div>
      <div class="panel-block table-body-br">
        <v-server-table
            id="projects-table"
            :url="url"
            :columns="columns"
            :options="options"
            ref="projectsTable"
            name="projectsTable">
          <template slot="customer" slot-scope="props">
            <div class="has-text-centered">
              {{ props.row.customer ? props.row.customer.name : '' }}
            </div>
          </template>

          <template slot="start_date" slot-scope="props">
            <date-format-component :dateTime="props.row.start_date"></date-format-component>
          </template>
          <template slot="end_date" slot-scope="props">
            <date-format-component :dateTime="props.row.end_date"></date-format-component>
          </template>
          <template slot="price" slot-scope="props">
            <div>
              <span v-if="props.row.originalPrice">
                    מחיר מקורי: {{ props.row.originalPrice }}
                  <br>
              </span>
              <span v-if="props.row.originalPrice !== props.row.price " >
                   מחיר כולל  : {{  props.row.price}}
              </span>

            </div>

          </template>
          <template slot="type" slot-scope="props">
            <div class="has-text-centered">
              {{ props.row.type ? props.row.type.name : '' }}
            </div>
          </template>
          <template slot="percentage_done" slot-scope="props">
            <div class="has-text-centered">
              <p class="percentageP" :class="{
                            moreThen100 : percentageCalculation(props.row.actual_time, props.row.price) > 100,
                            moreThen85 : percentageCalculation(props.row.actual_time, props.row.price) > 85,
                            lessThen85 : percentageCalculation(props.row.actual_time, props.row.price) < 85} ">
                {{ percentageCalculation(props.row.actual_time, props.row.price) }}%
                <span class="percentageRow" v-if="percentageCalculation(props.row.actual_time, props.row.price) > 100">
                              <i class="far fa-frown percentageRowIcon"></i>
                            </span>
                <span class="percentageRow" v-else-if="percentageCalculation(props.row.actual_time, props.row.price) > 85">
                             <i class="far fa-meh percentageRowIcon"></i>
                            </span>
                <span class="percentageRow" v-else>
                             <i class="far fa-smile percentageRowIcon"></i>
                            </span>
              </p>
            </div>
          </template>
          <template slot="active" slot-scope="props">
            <div class="has-text-centered">

                            {{props.row.active == 1 ? 'פעיל' : 'לא פעיל' }}

            </div>
          </template>
          <template slot="some_money_so_far" slot-scope="props">
            <div class="has-text-centered">

              {{ moneyBurnCalculation(props.row.actual_time) }}
            </div>
          </template>

          <template slot="actions" slot-scope="props">
            <div class="buttons has-addons is-centered">
              <p class="control tooltip">
                <router-link :to="'/projects/' + props.row.id" class="button is-primary is-small">
                  <i class="fas fa-file-alt"/>
                  <span class="tooltip-text">צפייה</span>
                </router-link>
              </p>
              <p class="control tooltip">
                <a
                    @click="showTasks(props.row.id, props.row.name)"
                    class="fas fa-list button is-info is-small">
                  <span class="tooltip-text bottom"> רשימת משימות</span>

                </a>
              </p>
              <p class="control tooltip">
                <a
                    @click="showEvents(props.row.id, props.row.name)"
                    class="far fa-calendar-alt button is-danger is-small">
                  <span class="tooltip-text bottom"> רשימת התקשרויות</span>

                </a>
              </p>
              <p class="control tooltip" v-if="$store.getters.admin">
                <router-link :to="'/projects/edit/' + props.row.id" class="button is-warning is-small">
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
    <panel-filters-component
        v-if="filtering"
        :filters="filters"
        :class="{ loaded: filtering }"
        @hide-panel-filters="filtering = false"
    ></panel-filters-component>
    <router-view></router-view>

  </div>
    <div v-if="showTaskList">
      <task-list-component
          :projectName="projectName"
          :projectId="projectId"
          @showProjectsList="showProjectsList"
          modal="projects"></task-list-component>
    </div>
    <div v-if="showEventsList">
      <events-list-component
          :projectName="projectName"
          :projectId="projectId"
          @showProjectsList="showProjectsList"
          modal="projects"></events-list-component>
    </div>
  </div>
</template>

<script>
import mId from '../../mixins/Mid'
import tBus from '../../mixins/Tbus'
import DateFormatComponent from '../helpers/DateFormatComponent'
import TaskListComponent from '../tasks/TaskListComponent'
import EventsListComponent from '../events/EventsListComponent'
export default {
  mixins: [mId, tBus('app/projects')],
  props: [
    'modal',
    'customerId',
    'customerName'
  ],
  data () {
    return {
      showTaskList: false,
      showEventsList:false,
      showProjectForm: false,
      projectId: null,
      projectName: null,
      columns: ['name', 'customer', 'start_date','end_date', 'price', 'expenses', 'type','percentage_done','some_money_so_far' ,'active', 'actions'],
      filters: new this.$form({ name: '', company: '', email: '', phone: '', balance: false, range: 0 }),
      addRoute: null,
      options: {
        orderBy: { ascending: true, column: 'name' },
        sortable: ['name', 'start_date', 'end_date', 'priority', 'active'],
        editableColumns: ['details'],
        perPage: 10,
        columnsClasses: {
          id: 'w50 has-text-centered',
          receivable: 'w125 has-text-right',
          actions: 'w175 has-text-centered p-x-none',
          details: 'details-td'
        },
        filterable: ['name', 'start_date', 'end_date', 'details'],
        headings: {
          name: 'שם',
          customer: 'לקוח',
          start_date :'תחילת עבודה',
          end_date : 'סיום עבודה',
          type: 'סוג',
          date_to_complete: 'תאריך התחלה',
          actual_time: 'תאריך סיום',
          price: 'מחיר',
          expenses: 'הוצאות',
          actions: 'פעולות',
          active: 'פעיל',
          percentage_done: 'אחוז מהעבודה עד כה',
          some_money_so_far : 'כמה כסף עד עכשיו'
        },
      },
    }
  },

  methods: {
    showTasks (projectId, projectName) {

       this.projectId = projectId
       this.projectName = projectName
       this.showTaskList = true
       this.showEventsList = false
    },
    showEvents (projectId, projectName) {

      this.projectId = projectId
      this.projectName = projectName
      this.showEventsList = true
      this.showTaskList = false
    },
    showProjectsList: function () {
      this.showTaskList = false
      this.showEventsList = false
    },
    editDetails (val) {
      let id = val.target.id.replace(/[^\d.]/g, '')
      this.$http(`app/projects/details/${val.target.value}/${id}`).then()

    },
    goBack() {
      //this.$emit("showCustomerList", true);
    },
    //
    // Calculate the percentage of the money that "burn" by the work hours that already put in the project
    percentageCalculation(tasksTime, price) {
      if (tasksTime) {
        let totalTimeAsAmount = this.moneyBurnCalculation(tasksTime)
        let percentage = totalTimeAsAmount / price
        if (Number.isFinite(percentage)) {
          return  (percentage * 100).toFixed(1) ;
        }
      }
    },
    moneyBurnCalculation(tasksTime) {
      if (tasksTime) {
        let sumTasksTime = tasksTime.reduce((a, b) => a + b, 0)
        //TODO hour wage should be dynamic
        let HourlyWage = 150;
        let convertToHours = (sumTasksTime / 60)
        return convertToHours *  HourlyWage;
      }
    },

  },
  created () {
    this.showTaskList = false
    this.addRoute = !this.customerId ? '/projects/add' : `app/customers/projects/${this.customerId}`;
    this.$root.$refs.ProjectListComponent = this;
  },
  computed: {
    url: {
      get () {
        return !this.customerId ? 'app/projects' : `app/customers/projects/${this.customerId}`;
      },

    },

  },
  components: { DateFormatComponent,TaskListComponent, EventsListComponent },

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
.moreThen85 {
  background: orange;
}
.moreThen100 {
  background: red;
}
.lessThen85{
  background: green;
}
.percentageRowIcon{
  padding-right: 6px;
  text-align: center;
  line-height: 2.1;
  color: white;

}
.percentageRow{
  float: right;
  text-align: center;
  line-height: 2;
  color: white;
  font-size: 19px;
}
.percentageP{
  display: table;
  height: 35px;
  width: 100%;
  text-align: center;
  line-height: 2.6;
  font-weight: bold;
  color: white;
}
</style>
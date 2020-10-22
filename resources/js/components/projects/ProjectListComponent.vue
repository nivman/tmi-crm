<template>
  <div class="wrapper">
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
            id="tasks-table"
            :url="url"
            :columns="columns"
            :options="options"
            ref="tasksTable"
            name="tasksTable">
          <template slot="customer" slot-scope="props">
            <div class="has-text-centered">
              {{ props.row.customer ? props.row.customer.name : '' }}

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
<!--          <template slot="start_date" slot-scope="props">-->
<!--            <date-format-component :dateTime="props.row.start_date"></date-format-component>-->
<!--          </template>-->
<!--          <template slot="end_date" slot-scope="props">-->
<!--            <date-format-component :dateTime="props.row.end_date"></date-format-component>-->
<!--          </template>-->
          <template slot="date_to_complete" slot-scope="props">
            <date-format-component :date="props.row.date_to_complete"></date-format-component>
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
          <template slot="actions" slot-scope="props">
            <div class="buttons has-addons is-centered">
              <p class="control tooltip">
                <router-link :to="'/tasks/' + props.row.id" class="button is-primary is-small">
                  <i class="fas fa-file-alt"/>
                  <span class="tooltip-text">View</span>
                </router-link>
              </p>
              <p class="control tooltip" v-if="$store.getters.admin">
                <router-link :to="'/tasks/edit/' + props.row.id" class="button is-warning is-small">
                  <i class="fas fa-edit"></i>
                  <span class="tooltip-text">Edit</span>
                </router-link>
              </p>
              <p class="control tooltip" v-if="$store.getters.superAdmin">
                <button type="button" class="button is-danger is-small" @click="deleteRecord(props.row.id)">
                  <i class="fas fa-trash"></i>
                  <span class="tooltip-text">Delete</span>
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
</template>

<script>
import mId from '../../mixins/Mid'
import tBus from '../../mixins/Tbus'
import DateFormatComponent from '../helpers/DateFormatComponent'

export default {
  mixins: [mId, tBus('app/tasks')],
  props: [
    'modal',
    'customerId',
    'customerName'
  ],
  data () {
    return {
      showTaskForm: false,
      columns: ['name', 'customer', 'category','date_to_complete', 'actual_time', 'details', 'priority', 'status', 'actions'],
      filters: new this.$form({ name: '', company: '', email: '', phone: '', balance: false, range: 0 }),
      addRoute: null,
      options: {
        orderBy: { ascending: true, column: 'name' },
        sortable: ['name', 'start_date', 'end_date', 'priority'],
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
          name: 'נושא',
          details: 'תוכן',
          // start_date : 'תאריך לביצוע',
          // end_date: 'סיום תאריך לביצוע',
          customer: 'לקוח',
          priority: 'עדיפות',
          status: 'סטטוס',
          date_to_complete: 'תאריך לביצוע',
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
      this.$emit("showCustomerList", true);
    },
  },
  created () {

    this.addRoute = !this.customerId ? '/tasks/add' : `/tasks/add?customerId=${this.customerId}`;;
  },
  computed: {
    url: {
      get () {
        return !this.customerId ? 'app/tasks' : `app/customers/tasks/${this.customerId}`;
      },

    },

  },
  components: { DateFormatComponent },

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
</style>
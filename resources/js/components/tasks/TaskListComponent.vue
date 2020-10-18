<template>
  <div class="wrapper">
    <div class="panel panel-default">
      <div class="panel-heading">
        <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
          <i class="fas fa-cog" />
        </button>
        <router-link to="/tasks/add" class="button is-link is-small is-pulled-right">
          <i class="fas fa-plus m-l-sm" /> הוספת משימה
        </router-link>
        משימות
        <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
      </div>
      <div class="panel-block table-body-br">
        <v-server-table
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
          <template slot="start_date" slot-scope="props">
            <date-format-component :date="props.row.start_date"></date-format-component>
          </template>
          <template slot="end_date" slot-scope="props">
            <date-format-component :date="props.row.end_date"></date-format-component>
          </template>
          <template slot="created_at" slot-scope="props">
              <date-format-component :date="props.row.created_at"></date-format-component>
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
                  <i class="fas fa-file-alt" />
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
import mId from '../../mixins/Mid';
import tBus from '../../mixins/Tbus';
import DateFormatComponent from "../helpers/DateFormatComponent";

export default {
  mixins: [mId, tBus('app/tasks')],
  data() {
    return {

      columns: ['name', 'customer', 'start_date','end_date','created_at','details', 'priority', 'status','actions'],
      filters: new this.$form({ name: '', company: '', email: '', phone: '', balance: false, range: 0 }),
      options: {
        orderBy: { ascending: true, column: 'name' },
        sortable: ['name', 'start_date', 'end_date', 'created_at','priority'],
        perPage: 10,
        columnsClasses: {
          id: 'w50 has-text-centered',
          receivable: 'w125 has-text-right',
          actions: 'w175 has-text-centered p-x-none',
        },
        filterable: ['name', 'start_date','end_date','created_at','details'],
        headings: {
          name: 'נושא',
          details: 'תוכן',
          start_date : 'זמן התחלה',
          end_date: 'זמן סיום',
          created_at : 'זמן יצירה',
          customer : 'לקוח',
          priority : 'עדיפות',
          status: 'סטטוס',
          actions : 'פעולות'
        },
      },
    };
  },
  methods: {
    format_date(value){
      if (value) {
        return moment(String(value)).format('DD/MM/YYYY hh:mm')
      }
    }
  },
  components: { DateFormatComponent },
};
</script>

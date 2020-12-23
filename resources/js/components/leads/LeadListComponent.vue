<template>
  <div>
    <div class="wrapper" v-if="!showTaskList">
      <div class="panel panel-default">
        <div class="panel-heading">
          <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop"
                  @click="filtering = !filtering">
            <i class="fas fa-cog"/>
          </button>
          <router-link to="/leads/add" class="button is-link is-small is-pulled-right">
            <i class="fas fa-plus m-l-sm"/> הוספת ליד
          </router-link>
          לידים
        </div>
        <div class="panel-block table-body-br">
          <v-server-table
              :url="url"
              :columns="columns"
              :options="options"
              @loaded="onLoaded"
              ref="leadsTable"
              name="leadsTable"
              :slots="slots">
            <template slot="status" slot-scope="props">
              <div class="has-text-centered" :style="{background: props.row.status ? props.row.status.color : ''} ">
                {{ props.row.status ? props.row.status.name : '' }}
              </div>
            </template>
            <template slot="receivable" slot-scope="props">
              <div class="has-text-right">
                {{ props.row.journal ? props.row.journal.balance.amount : 0 | formatJournalBalance }}
              </div>
            </template>
            <template v-for="(slot,i) in customColumn" :slot=customColumn[i] slot-scope="props">
              {{ setCustomFieldValue(props.row.custom, i, slot) }}
              <!--              {{ props.row.custom}}-->

            </template>


            <template :slot="customColumn" slot-scope="props">

              {{ props.row.status ? props.row.custom : '' }}
              <div class="has-text-right">
              </div>
            </template>

            <template slot="created_at" slot-scope="props">
              <date-format-component :date="props.row.created_at"></date-format-component>

            </template>
            <template slot="actions" slot-scope="props">
              <div class="buttons has-addons is-centered">
                <p class="control tooltip">
                  <a
                      @click="showTasks(props.row.id, props.row.name)"
                      class="fas fa-list button is-info is-small">
                    <span class="tooltip-text bottom"> רשימת משימות</span>

                  </a>
                </p>
                <p class="control tooltip" v-if="$store.getters.admin">
                  <router-link :to="'/leads/edit/' + props.row.id" class="button is-warning is-small">
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
            <template slot="afterBody">
              <table-filters-component :filters="filters"></table-filters-component>
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
          :customerName="customerName"
          :customerId="customerId"
          modal="customers"
          @showCustomerList="showCustomerList"
      ></task-list-component>
    </div>
  </div>
</template>

<script>
import mId from '../../mixins/Mid'
import tBus from '../../mixins/Tbus'
import TaskListComponent from '../tasks/TaskListComponent'
import DateFormatComponent from '../helpers/DateFormatComponent'

export default {
  mixins: [mId, tBus('app/customers/leads')],
  data () {
    return {
      loaded: false,
      slots: '',
      customerId: null,
      customerName: null,
      showTaskList: false,
      customColumn: [],

      columns: ['name', 'company', 'email', 'phone', 'status', 'created_at', 'actions'],
      filters: new this.$form({ name: '', company: '', email: '', phone: '', status: '',  created_at: '', range: 1 }),
      options: {
        orderBy: { ascending: true, column: 'name' },
        sortable: ['id', 'name', 'company', 'email', 'phone', 'created_at', 'range' ],
        perPage: 10,
        columnsClasses: {
          id: 'w50 has-text-centered',
          receivable: 'w125 has-text-right',
          actions: 'w175 has-text-centered p-x-none',
        },
        filterable: ['id', 'name', 'company', 'email', 'phone', 'created_at', 'range' ],
        headings: {
          name: 'שם',
          company: 'חברה',
          email: 'אימייל',
          phone: 'טלפון',
          status: 'סטטוס',
          created_at: 'נוצר בתאריך',
          actions: 'פעולות',

        },
      },
    }
  },
  methods: {
    setCustomFieldValue (customField, i) {

      if (customField) {
        let customRow = Object.entries(customField)
        if (customField) {

          if (customRow[i]) {
            return customRow[i][1]
          }
        }
      }
    },
    onLoaded (data) {
      let attributesNames = data.data.attributesNames
      attributesNames = Object.keys(attributesNames).map((k) => attributesNames[k])
      this.slots = attributesNames

      let em = this
      this.customColumn = attributesNames
      if (!this.loaded) {

        setTimeout(function () {
          attributesNames.forEach((item) => {
            em.columns.splice(6, 0, item)
          })
        }, 50)
      }
      this.loaded = true

    },
    showTasks (customerId, customerName) {

      this.customerId = customerId
      this.customerName = customerName
      this.showTaskList = true
    },
    showCustomerList: function () {
      this.showTaskList = false
    }
  },
  created () {
    this.showTaskList = false
  },
  components: {
    TaskListComponent, DateFormatComponent
  }
}
</script>

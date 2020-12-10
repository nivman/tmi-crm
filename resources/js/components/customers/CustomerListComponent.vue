<template>
  <div>
    <div class="wrapper" v-if="!showTaskList">
      <div class="panel panel-default">
        <div class="panel-heading">
          <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop"
                  @click="filtering = !filtering">
            <i class="fas fa-cog"/>
          </button>
          <router-link to="/customers/add" class="button is-link is-small is-pulled-right">
            <i class="fas fa-plus m-l-sm"/> הוספת לקוח
          </router-link>
          לקוחות
          <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
        </div>
        <div class="panel-block table-body-br">
          <v-server-table
              :url="url"
              :columns="columns"
              :options="options"
              @loaded="onLoaded"
              ref="customersTable"
              name="customersTable"
              :slots="slots"
          >
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


            </template>


<!--            <template :slot="customColumn" slot-scope="props">-->
<!--            {{customColumn}}-->
<!--              {{ props.row.status ? props.row.custom : '' }}-->
<!--              <div class="has-text-right">-->
<!--              </div>-->
<!--            </template>-->


            <template slot="actions" slot-scope="props">
              <div class="buttons has-addons is-centered">
                <p class="control tooltip">
                  <router-link :to="'/customers/' + props.row.id" class="button is-primary is-small">
                    <i class="fas fa-file-alt"/>
                    <span class="tooltip-text">פרטי לקוח</span>
                  </router-link>
                </p>
                <p class="control tooltip">
                  <a
                      @click="showTasks(props.row.id, props.row.name)"
                      class="fas fa-list button is-info is-small">
                    <span class="tooltip-text bottom"> רשימת משימות</span>

                  </a>
                </p>
                <p class="control tooltip" v-if="$store.getters.admin">
                  <router-link :to="'/payments/add?payer=customer&payer_id=' +
                           props.row.id + '&amount=' +  parseFloat(props.row.journal.balance.amount / 100)"
                      class="button is-success is-small">
                    <i class="fas fa-money-bill-alt"></i>
                    <span class="tooltip-text">הוספת תשלום</span>
                  </router-link>
                </p>
                <p class="control tooltip" v-if="$store.getters.admin">
                  <router-link :to="'/customers/edit/' + props.row.id" class="button is-warning is-small">
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


export default {
  mixins: [mId, tBus('app/customers')],
  data () {
    return {
      loaded: false,
      slots: '',
      customerId: null,
      customerName: null,
      showTaskList: false,
      customColumn: [],
      totalAmount: 0,
      columns: ['name', 'company', 'email', 'phone', 'status', 'actions'],
      filters: new this.$form({ name: '', company: '', email: '', phone: '', balance: false, range: 0 }),
      options: {
        orderBy: { ascending: true, column: 'name' },
        sortable: ['id', 'name', 'company', 'email', 'phone'],
        perPage: 10,
        columnsClasses: {
          id: 'w50 has-text-centered',
          receivable: 'w125 has-text-right',
          actions: 'w175 has-text-centered p-x-none',
        },
        filterable: ['id', 'name', 'company', 'email', 'phone'],
        headings: {
          name: 'שם',
          company: 'חברה',
          email: 'אימייל',
          phone: 'טלפון',
          status: 'סטטוס',
          actions: 'פעולות',

        },
      },
    }
  },
  methods: {
    setCustomFieldValue (customField, i, slot) {

      if (customField) {
        let customRow = Object.entries(customField)
        if (customField) {
          if (customRow[i] ) {
            return customRow[i][1]
          }
        }
      }
    },
    onLoaded (data) {

      let table = data.data.data
      let attributesNames = data.data.attributesNames
      attributesNames = Object.keys(attributesNames).map((k) => attributesNames[k])
      this.slots = attributesNames

      let totalAmount = Object.keys(table).reduce(function (sum, key) {
        return sum + parseFloat(table[key].journal.balance.amount)
      }, 0)
      this.totalAmount = parseFloat(totalAmount / 100)

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
    TaskListComponent
  }
}
</script>

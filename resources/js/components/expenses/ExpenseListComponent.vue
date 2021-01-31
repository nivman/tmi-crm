<template>
  <div class="wrapper">
    <div class="panel panel-default pa">
      <div class="panel-heading">
        <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop"
                @click="filtering = !filtering">
          <i class="fas fa-cog"/>
        </button>
        <router-link to="/expenses/add" class="button is-link is-small is-pulled-right">
          <i class="fas fa-plus m-l-sm"/> הוספת הוצאה
        </router-link>
        הוצאות
        <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
      </div>
      <div class="panel-block table-body-br">
        <v-server-table
            :url="url"
            :columns="columns"
            :options="options"
            ref="expensesTable"
            @loaded="onLoaded"
            name="expensesTable">
          <template slot="created_at" slot-scope="props">
            <date-format-component :date="props.row.created_at"></date-format-component>

          </template>
          <template slot="category" slot-scope="props">
            {{ props.row.categories[0].name }}
          </template>
          <template slot="project" slot-scope="props">
            {{ props.row.project ? props.row.project.name : '' }}
          </template>
          <template slot="vendor" slot-scope="props">
            {{ props.row.vendor ? props.row.vendor.name : '' }}
          </template>
          <template slot="account" slot-scope="props">
            {{ props.row.account.name }}
          </template>
          <template slot="amount" slot-scope="props">
            <div class="has-text-right">{{ props.row.amount | formatNumber }}</div>
          </template>
          <template slot="actions" slot-scope="props">
            <div class="buttons has-addons is-centered">
              <p class="control tooltip">
                <router-link :to="'/expenses/' + props.row.id" class="button is-primary is-small">
                  <i class="fas fa-file-invoice-dollar"/>
                  <span class="tooltip-text">הצגה</span>
                </router-link>
              </p>
              <p class="control tooltip" v-if="$store.getters.admin">
                <router-link :to="'/expenses/edit/' + props.row.id" class="button is-warning is-small">
                  <i class="fas fa-edit"/>
                  <span class="tooltip-text">עריכה</span>
                </router-link>
              </p>
              <p class="control tooltip" v-if="$store.getters.superAdmin">
                <button type="button" class="button is-danger is-small" @click="deleteRecord(props.row.id)">
                  <i class="fas fa-trash"/>
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
</template>

<script>
import mId from '../../mixins/Mid';
import tBus from '../../mixins/Tbus';
import DateFormatComponent from '../helpers/DateFormatComponent'

export default {
  mixins: [mId, tBus('app/expenses')],
  props: ['projectId'],
  data() {
    return {
      total_amount: 0,
      filters: new this.$form({
        created_at: '',
        title: '',
        reference: '',
        categories: {name: ''},
        account: {name: ''},
        amount: '',
        range: 1,
        date_range: '',
      }),
      columns: ['created_at', 'title', 'project', 'vendor', 'details', 'category', 'account', 'amount', 'actions'],
      options: {
        dateColumns: ['created_at'],
        listColumns: {},
        filterByColumn: true,
        perPage: 10,
        orderBy: {ascending: false, column: 'created_at'},
        sortable: ['created_at', 'title', 'project'],
        columnsClasses: {id: 'w50 has-text-centered', actions: 'w125 has-text-centered p-x-none', amount: 'w125', project: 'header-table-font'},
        filterable: ['created_at', 'title', 'project'],
        headings: {
          created_at: 'תאריך יצירה',
          title: 'כותרת',
          details: 'פרטים',
          category: 'קטגוריה',
          project: 'פרוייקט',
          vendor: 'ספק',
          amount: 'סכום',
          actions: 'פעולות',
        },
      },
    };
  },
  created() {
    this.getSelectableFilters();
  },
  methods: {
    onLoaded(data) {

      let table = data.data.data;
      this.total_amount = Object.keys(table).reduce(function (sum, key) {
        return sum + parseFloat(table[key].amount);
      }, 0);
    },
    getSelectableFilters() {

      let em = this

      this.$http
          .post(`app/projects/tablefilter`)
          .then(res => {
            em.options.listColumns = {
              project: res.data.map(({text, id}) => ({text: text, id: text})),
              type: em.types
            }
          })
          .catch(err => this.$event.fire('appError1', err.response))

    },
  },
  computed: {
    url: {
      get() {

        if (this.projectId) {
          return `app/expenses/project/${this.projectId}`;
        }
        return 'app/expenses';
      },

    },

  },
  components: {DateFormatComponent},
};
</script>

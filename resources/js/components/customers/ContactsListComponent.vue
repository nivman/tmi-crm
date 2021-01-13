<template>
  <div>
    <div class="wrapper">
      <div class="panel panel-default">
        <div class="panel-heading">
          <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop"
                  @click="filtering = !filtering">
            <i class="fas fa-cog"/>
          </button>
          <router-link to="/customers/add" class="button is-link is-small is-pulled-right">
            <i class="fas fa-plus m-l-sm"/> הוספת איש קשר
          </router-link>
          אנשי קשר

        </div>
        <div class="panel-block table-body-br">
          <v-server-table
              :url="url"
              :columns="columns"
              :options="options"
              @loaded="onLoaded"
              ref="contactsTable"
              name="contactsTable"
              :slots="slots">

            <template slot="customer" slot-scope="props">
              <div class="has-text-right">
                {{ props.row.customer.name}}
              </div>
            </template>

            <template slot="actions" slot-scope="props">
              <div class="buttons has-addons is-centered">
                <p class="control tooltip">
                  <router-link :to="'/contacts/' + props.row.id" class="button is-primary is-small">
                    <i class="fas fa-file-alt"/>
                    <span class="tooltip-text">פרטי איש קשר</span>
                  </router-link>
                </p>
                <p class="control tooltip" v-if="$store.getters.admin">
                  <router-link :to="'/contacts/edit/' + props.row.id" class="button is-warning is-small">
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
  </div>
</template>

<script>
import mId from '../../mixins/Mid'
import tBus from '../../mixins/Tbus'



export default {
  mixins: [mId, tBus('app/contacts')],
  data () {
    return {

      slots: '',
      columns: ['first_name', 'last_name', 'email', 'phone', 'customer', 'actions'],
      filters: new this.$form({ first_name: '', last_name: '', email: '', phone: '', customer: { name: '' } }),
      options: {
        orderBy: { ascending: true, column: 'first_name' },
        sortable: ['first_name', 'last_name', 'email', 'phone'],
        perPage: 10,
        columnsClasses: {
          id: 'w50 has-text-centered',
          receivable: 'w125 has-text-right',
          actions: 'w175 has-text-centered p-x-none',
        },
        filterable: ['first_name', 'last_name', 'email', 'phone', 'customer.name'],
        headings: {
          first_name: 'שם פרטי',
          last_name: 'שם משפחה',
          email: 'אימייל',
          phone: 'טלפון',
          customer: 'שם לקוח',
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


    },

  },

}
</script>

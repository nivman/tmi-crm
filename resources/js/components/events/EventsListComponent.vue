<template>
  <div class="wrapper">
    <div class="panel panel-default">
      <div class="panel-heading">
        <a
            @click="addEvent"
            class="button is-link is-small is-pulled-right"
        >
          <i class="fas fa-plus m-l-sm"/> התקשרות חדשה
        </a>

        <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
        התקשרויות

      </div>
      <div class="panel-block table-body-br">

        <v-server-table name="productsTable" :url="url" :columns="columns" :options="options" ref="productsTable">
                    <template slot="contact" slot-scope="props">
                      <div class="has-text-centered" >

                        <p class="control tooltip">
                          <router-link :to="'/customer/contact/' + props.row.contact_id" >
                            {{ props.row.contact ? props.row.contact.first_name + ' ' +  props.row.contact.last_name: '' }}
                          </router-link>
                        </p>
                      </div>
                    </template>
                    <template slot="type" slot-scope="props">
                      <div class="has-text-centered" :style="{background: props.row.type ? props.row.type.color : ''} ">
                        {{ props.row.type ? props.row.type.name : '' }}
                      </div>
                    </template>
                    <template slot="start_date" slot-scope="props">
                      <div class="has-text-centered" >
                        {{ format_date(props.row.start_date) }}
                    </div>
                    </template>
                    <template slot="end_date" slot-scope="props">
                      <div class="has-text-centered" >
                        {{ format_date(props.row.end_date) }}
                      </div>
                    </template>
                    <template slot="created_at" slot-scope="props">
                      <div class="has-text-centered" >
                        {{ format_date(props.row.created_at) }}
                      </div>
                    </template>
          <!--          <template v-if="$store.getters.stock" slot="qty" slot-scope="props">-->
          <!--            <div class="has-text-centered">{{ props.row.stock ? props.row.stock.qty : 0 | formatNumber }}</div>-->
          <!--          </template>-->
          <!--          <template v-if="$store.getters.admin || $store.getters.superAdmin" slot="cost" slot-scope="props">-->
          <!--            <div class="has-text-right">{{ props.row.cost | formatNumber }}</div>-->
          <!--          </template>-->
          <!--          <template slot="price" slot-scope="props">-->
          <!--            <div class="has-text-right">{{ props.row.price | formatNumber }}</div>-->
          <!--          </template>-->
          <!--          <template slot="actions" slot-scope="props">-->
          <!--            <div class="buttons has-addons is-centered">-->
          <!--              <p class="control tooltip">-->
          <!--                <router-link :to="'/products/' + props.row.id" class="button is-primary is-small">-->
          <!--                  <i class="fas fa-file"></i>-->
          <!--                  <span class="tooltip-text">View</span>-->
          <!--                </router-link>-->
          <!--              </p>-->
          <!--              <p class="control tooltip" v-if="$store.getters.admin">-->
          <!--                <router-link :to="'/products/edit/' + props.row.id" class="button is-warning is-small">-->
          <!--                  <i class="fas fa-edit"></i>-->
          <!--                  <span class="tooltip-text">Edit</span>-->
          <!--                </router-link>-->
          <!--              </p>-->
          <!--              <p class="control tooltip" v-if="$store.getters.superAdmin">-->
          <!--                <button type="button" class="button is-danger is-small" @click="deleteRecord(props.row.id)">-->
          <!--                  <i class="fas fa-trash"></i>-->
          <!--                  <span class="tooltip-text">Delete</span>-->
          <!--                </button>-->
          <!--              </p>-->
          <!--            </div>-->
          <!--          </template>-->
          <!--          <template slot="afterBody">-->
          <!--            <table-filters-component :filters="filters"></table-filters-component>-->
          <!--          </template>-->
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
    <event-form-modal></event-form-modal>
  </div>
</template>

<script>
import mId from '../../mixins/Mid'
import tBus from '../../mixins/Tbus'
import EventFormModal from '../calendar/EventFormModal.vue'

export default {
  mixins: [mId, tBus('app/events/list')],
    data () {
      return {
        loading: true,
        columns: ['title','contact','start_date','end_date','created_at','type','details'],
        filters: new this.$form({ title: '', details: ''}),
        options: {
          perPage: 10,
          orderBy: { ascending: true, column: 'title' },
          sortable: ['title','start_date','end_date','created_at','type'],
          columnsClasses: { id: 'w50 has-text-centered', qty: 'w75', actions: 'w125 has-text-centered p-x-none' },
          filterable: ['title', 'details'],
        },
      }
    },
    created () {

      this.loading = false
      if (this.$store.getters.admin || this.$store.getters.superAdmin) {
        let filters = { title: '', details: '' }
        let actions = this.columns.pop()

        this.columns.push(actions)
        this.filters = new this.$form(filters)
      }
    },
    methods: {
      format_date(value){
        if (value) {
         return moment(String(value)).format('DD/MM/YYYY hh:mm')
        }
      },
      addEvent () {

        this.$modal.show('event-form-modal', { event: null })
      },
    },
    components: { EventFormModal },
  }
</script>

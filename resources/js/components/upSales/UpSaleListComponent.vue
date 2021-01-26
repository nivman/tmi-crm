<template>
  <div class="wrapper">
    <div class="panel panel-default">
      <div class="panel-heading">
        <router-link to="/upsales/add" class="button is-link is-small is-pulled-right">
          <i class="fas fa-plus m-l-sm"/> הוספת מכירה
        </router-link>
        מכירות נוספות

      </div>
      <div class="panel-block">
        <v-server-table name="upsalesTable" :url="url" :columns="columns" :options="options" ref="upsalesTable">
          <template slot="category" slot-scope="props">
            {{ props.row.category.name }}
          </template>
          <template slot="project" slot-scope="props">
            {{ props.row.project ? props.row.project.name : '' }}
          </template>
          <template slot="actions" slot-scope="props">
            <div class="buttons has-addons is-centered">
              <p class="control tooltip">
                <router-link :to="'/upsales/edit/' + props.row.id" class="button is-warning is-small">
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

export default {
  mixins: [mId, tBus('app/upsales')],
  props:['projectId'],
  data () {
    return {
      columns: ['title', 'amount', 'category', 'project', 'actions'],
      options: {
        perPage: 10,
        orderBy: { ascending: true, column: 'title' },
        sortable: ['id', 'title'],
        columnsClasses: { id: 'w50 has-text-centered', actions: 'w100 has-text-centered p-x-none' },
        headings: {
          title: 'שם',
          amount: 'סכום',
          category: 'קטגוריה',
          project: 'פרויקט'
        },
      },

    }
  },
  computed: {
    url: {
      get () {
        let route = 'app/upsales';
        if (this.projectId) {
          return `app/upsales/project/${this.projectId}`;
        }
        return route;
      },

    },

  },
}
</script>

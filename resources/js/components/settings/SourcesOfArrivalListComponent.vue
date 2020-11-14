<template>
  <div class="wrapper">
    <div class="panel panel-default">
      <div class="panel-heading panel-heading-title" >

        <router-link to="/settings/arrival-source/add" class="button is-link is-small is-pulled-right">
          <i class="fas fa-plus m-l-sm" /> הוספת מקור הגעה
        </router-link>
        <span class="table-title">מקורות הגעה</span>

      </div>
      <div class="panel-block">

        <v-server-table name="statusesTable" :url="url" :columns="columns" :options="options" ref="statusesTable" >
          <template slot="color" slot-scope="props">
            <div  v-bind:style="{background: props.row.color, position: 'relative', height:20 + 'px',width:100 + '%'} "> </div>

          </template>
          <template slot="entities" slot-scope="props">
            {{ props.row.entities.map(entity => entity.entity_type.split('\\')[1]).join(', ') }}
          </template>
          <template slot="actions" slot-scope="props">
            <div class="buttons has-addons is-centered">
              <p class="control tooltip">
                <router-link :to="'/settings/arrival-source/edit/' + props.row.id" class="button is-warning is-small">
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
import mId from '../../mixins/Mid';
import tBus from '../../mixins/Tbus';
export default {
  mixins: [mId, tBus('app/arrival-source')],
  data() {
    return {
      columns: ['name',  'color', 'actions'],
      filters: new this.$form({name: ''}),
      options: {
        filterable: ['name'],
        headings: {
          name: 'שם',
          color : 'צבע',
          actions: 'פעולות'
        },
        perPage: 10,
        orderBy: { ascending: true, column: 'name' },
        sortable: ['id', 'name'],
        resizableColumns:  ['id', 'name'],
      },
    };
  },
};
</script>

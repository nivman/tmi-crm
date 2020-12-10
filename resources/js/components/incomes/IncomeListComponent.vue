<template>
    <div class="wrapper">
        <div class="panel panel-default pa">
            <div class="panel-heading">
                <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
                    <i class="fas fa-cog" />
                </button>
                <router-link to="/incomes/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-plus m-l-sm" /> הוספת הכנסה
                </router-link>
                הכנסות
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block table-body-br">
                <v-server-table name="incomesTable" :url="url" :columns="columns" :options="options" ref="incomesTable" @loaded="onLoaded">
                    <template slot="created_at" slot-scope="props">
                      <date-format-component :dateTime="props.row.created_at"></date-format-component>

                    </template>
                    <template slot="category" slot-scope="props">
                        {{ props.row.categories[0].name }}
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
                                <router-link :to="'/incomes/' + props.row.id" class="button is-primary is-small">
                                    <i class="fas fa-file-invoice-dollar" />
                                    <span class="tooltip-text">View</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link :to="'/incomes/edit/' + props.row.id" class="button is-warning is-small">
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
    mixins: [mId, tBus('app/incomes')],
    data() {
        return {
            total_amount: 0,
            filters: new this.$form({
                created_at: '',
                title: '',

                categories: { name: '' },
                details: '',
                amount: '',
                range: 1,
                date_range: '',
            }),
            columns: ['created_at', 'title', 'details', 'category', 'amount', 'actions'],
            options: {
                perPage: 10,
                orderBy: { ascending: false, column: 'created_at' },
                sortable: ['id', 'created_at', 'title', 'amount'],
                columnsClasses: { id: 'w50 has-text-centered', actions: 'w125 has-text-centered p-x-none', amount: 'w125' },
                filterable: ['id', 'created_at', 'title',  'categories.name', 'account.name', 'amount'],
              headings: {
                created_at: 'תאריך יצירה',
                title: 'כותרת',
                details: 'פרטים',
                category: 'קטגוריה',
                project: 'פרוייקט',
                amount: 'סכום',
                actions: 'פעולות',
              },
            },
        };
    },
    methods: {
        onLoaded(data) {
            let table = data.data.data;
            this.total_amount = Object.keys(table).reduce(function(sum, key) {
                return sum + parseFloat(table[key].amount);
            }, 0);
        },
    },
  components: {DateFormatComponent}
};
</script>

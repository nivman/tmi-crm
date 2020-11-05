<template>
    <div class="wrapper">
        <div class="panel panel-default pa">
            <div class="panel-heading">
                <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
                    <i class="fas fa-cog" />
                </button>
                <router-link to="/expenses/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-plus m-l-sm" /> הוספת הוצאה
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
                    name="expensesTable"
                >
                    <template slot="created_at" slot-scope="props">
                        {{ props.row.created_at | formatDate(dateformat + ' HH:mm') }}
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
                                <router-link :to="'/expenses/' + props.row.id" class="button is-primary is-small">
                                    <i class="fas fa-file-invoice-dollar" />
                                    <span class="tooltip-text">View</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link :to="'/expenses/edit/' + props.row.id" class="button is-warning is-small">
                                    <i class="fas fa-edit" />
                                    <span class="tooltip-text">Edit</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.superAdmin">
                                <button type="button" class="button is-danger is-small" @click="deleteRecord(props.row.id)">
                                    <i class="fas fa-trash" />
                                    <span class="tooltip-text">Delete</span>
                                </button>
                            </p>
                        </div>
                    </template>
                    <template slot="afterBody">
                        <table-filters-component :filters="filters" :amount="total_amount"></table-filters-component>
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
export default {
    mixins: [mId, tBus('app/expenses')],
    data() {
        return {
            total_amount: 0,
            filters: new this.$form({
                created_at: '',
                title: '',
                reference: '',
                categories: { name: '' },
                account: { name: '' },
                amount: '',
                range: 1,
                date_range: '',
            }),
            columns: ['created_at', 'title', 'details', 'category', 'account', 'amount', 'actions'],
            options: {
                perPage: 10,
                orderBy: { ascending: false, column: 'created_at' },
                sortable: ['id', 'created_at', 'title', 'reference', 'amount'],
                columnsClasses: { id: 'w50 has-text-centered', actions: 'w125 has-text-centered p-x-none', amount: 'w125' },
                filterable: ['id', 'created_at', 'title', 'reference', 'categories.name', 'account.name', 'amount'],
              headings: {
                created_at: 'תאריך יצירה',
                title: 'כותרת',
                details: 'פרטים',
                category: 'קטגוריה',
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
};
</script>

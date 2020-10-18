<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
                    <i class="fas fa-cog" />
                </button>
                <router-link to="/customers/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-plus m-l-sm" /> Add Customer
                </router-link>
                Customers
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
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <router-link :to="'/customers/' + props.row.id" class="button is-primary is-small">
                                    <i class="fas fa-file-alt" />
                                    <span class="tooltip-text">View</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link :to="'/customers/transactions/' + props.row.id" class="button is-info is-small">
                                    <i class="fas fa-list"></i>
                                    <span class="tooltip-text">List Transactions</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link
                                    :to="
                                        '/payments/add?payer=customer&payer_id=' +
                                            props.row.id +
                                            '&amount=' +
                                            parseFloat(props.row.journal.balance.amount / 100)
                                    "
                                    class="button is-success is-small"
                                >
                                    <i class="fas fa-money-bill-alt"></i>
                                    <span class="tooltip-text">Add Payment</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link :to="'/customers/edit/' + props.row.id" class="button is-warning is-small">
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
    mixins: [mId, tBus('app/customers')],
    data() {
        return {
            total_amount: 0,
            columns: ['name', 'company', 'email', 'phone', 'receivable', 'status','actions'],
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
            },
        };
    },
    methods: {
        onLoaded(data) {
            let table = data.data.data;
            let total_amount = Object.keys(table).reduce(function(sum, key) {
                return sum + parseFloat(table[key].journal.balance.amount);
            }, 0);
            this.total_amount = parseFloat(total_amount / 100);
        },
    },
};
</script>

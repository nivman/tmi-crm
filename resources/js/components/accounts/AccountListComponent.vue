<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button
                    v-if="filters"
                    class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop"
                    @click="filtering = !filtering"
                >
                    <i class="fas fa-cog" />
                </button>
                <router-link to="/accounts/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-book m-r-sm" /> Add Account
                </router-link>
                Accounts
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block">
                <v-server-table name="accountsTable" :url="url" :columns="columns" :options="options" ref="accountsTable">
                    <template slot="balance" slot-scope="props">
                        <div class="has-text-right">
                            {{ props.row.journal ? props.row.journal.balance.amount : 0 | formatJournalBalance }}
                        </div>
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <router-link :to="'/accounts/transactions/' + props.row.id" class="button is-primary is-small">
                                    <i class="fas fa-list"></i>
                                    <span class="tooltip-text">List Transactions</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.superAdmin">
                                <router-link :to="'/accounts/edit/' + props.row.id" class="button is-warning is-small">
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
        <router-view></router-view>
    </div>
</template>

<script>
import mId from '../../mixins/Mid';
import tBus from '../../mixins/Tbus';
export default {
    mixins: [mId, tBus('app/accounts')],
    data() {
        return {
            loading: true,
            columns: ['name', 'type', 'reference', 'details', 'balance', 'actions'],
            options: {
                orderBy: { ascending: true, column: 'name' },
                sortable: ['id', 'name', 'type', 'reference', 'details'],
                filterable: ['id', 'name', 'type', 'reference', 'details'],
                columnsClasses: {
                    id: 'w50 has-text-centered',
                    name: 'w200',
                    type: 'w100',
                    reference: 'w125',
                    actions: 'w125 has-text-centered p-x-none',
                    balance: 'w150',
                },
            },
        };
    },
};
</script>

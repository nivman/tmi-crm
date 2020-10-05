<template>
    <div class="wrapper">
        <div class="panel panel-default pa">
            <div class="panel-heading">
                <button
                    v-if="filters"
                    class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop"
                    @click="filtering = !filtering"
                >
                    <i class="fas fa-cog" />
                </button>
                <router-link to="/transfers/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-plus m-r-sm" /> Add Transfer
                </router-link>
                Transfers
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block">
                <v-server-table name="transfersTable" :url="url" :columns="columns" :options="options" ref="transfersTable">
                    <template slot="from" slot-scope="props">
                        <span>{{ props.row.from_account.name }}</span>
                    </template>
                    <template slot="to" slot-scope="props">
                        <span>{{ props.row.to_account.name }}</span>
                    </template>
                    <template slot="amount" slot-scope="props">
                        <div class="has-text-right">{{ props.row.amount | formatNumber }}</div>
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <router-link :to="'/transfers/edit/' + props.row.id" class="button is-warning is-small">
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
    mixins: [mId, tBus('app/transfers')],
    data() {
        return {
            amount: 0,
            filters: 0,
            columns: ['from', 'to', 'amount', 'details', 'actions'],
            options: {
                perPage: 10,
                orderBy: { ascending: false, column: 'id' },
                sortable: ['id', 'amount', 'details'],
                filterable: ['id', 'from', 'to', 'amount', 'details'],
                columnsClasses: {
                    id: 'w50 has-text-centered',
                    from: 'w200',
                    to: 'w200',
                    amount: 'w150',
                    actions: 'w100 has-text-centered p-x-none',
                },
            },
        };
    },
};
</script>

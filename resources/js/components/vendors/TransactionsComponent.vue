<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                Vendor Transactions
            </div>
            <div class="panel-block">
                <v-server-table
                    :key="url"
                    :url="url"
                    :columns="columns"
                    :options="options"
                    ref="transactionsTable"
                    name="transactionsTable"
                >
                    <template slot="created_at" slot-scope="props">
                        {{ props.row.created_at | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}
                    </template>
                    <template slot="vendor" slot-scope="props">
                        <div class="">{{ props.row.journal.morphed ? props.row.journal.morphed.name : '' }}</div>
                    </template>
                    <template slot="debit" slot-scope="props">
                        <div class="has-text-right">{{ (props.row.debit / 100) | formatNumber }}</div>
                    </template>
                    <template slot="credit" slot-scope="props">
                        <div class="has-text-right">{{ (props.row.credit / 100) | formatNumber }}</div>
                    </template>
                    <template slot="type" slot-scope="props">
                        <span v-html="typeCol(props.row.type)"></span>
                    </template>
                </v-server-table>
            </div>
        </div>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    data() {
        return {
            columns: ['id', 'created_at', 'vendor', 'subject_type', 'subject_id', 'debit', 'credit', 'type'],
            options: {
                orderBy: { ascending: false, column: 'id' },
                perPage: this.$store.state.settings.ac.noRows,
                columnsClasses: {
                    id: 'w50',
                    created_at: 'w175',
                    subject_type: 'w175',
                    subject_id: 'w150',
                    debit: 'w150',
                    credit: 'w150',
                    type: 'w175',
                },
                sortable: ['id', 'created_at', 'debit', 'credit', 'type'],
                filterable: ['id', 'created_at', 'debit', 'credit', 'type'],
            },
        };
    },
    created() {
        if (this.$store.state.settings.ac.idColumn == 1) {
            this.columns.splice(0, 1);
        }
    },
    computed: {
        url() {
            return `app/vendors/transactions/${this.$route.params.id ? this.$route.params.id : ''}`;
        },
    },
    watch: {
        $route(to, from) {
            this.refreshTable();
        },
    },
    methods: {
        typeCol(type) {
            const bt = type.split('_');
            const first = bt[0];
            const first_class = first == 'opening' ? 'is-success' : 'is-link';
            const second = bt[1] == 'deleting' ? 'deleted' : bt[1];
            const second_class =
                second == 'created' ? 'is-success' : second == 'updated' ? 'is-warning' : second == 'deleted' ? 'is-danger' : 'is-dark';
            return `<div class="tags has-addons">
            <span class="tag ${first_class}">${first}</span>
            <span class="tag ${second_class}">${second}</span>
            </div>`;
        },
        refreshTable() {
            if (this.$refs.transactionsTable) {
                this.$refs.transactionsTable.refresh();
            }
        },
    },
};
</script>

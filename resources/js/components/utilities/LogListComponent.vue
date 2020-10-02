<template>
    <div class="wrapper">
        <div class="panel panel-default pa">
            <div class="panel-heading">
                <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
                    <i class="fas fa-cog" />
                </button>
                Activity Logs
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block table-body-br">
                <v-server-table name="logsTable" :url="url" :columns="columns" :options="options" ref="logsTable">
                    <template slot="created_at" slot-scope="props">
                        {{ props.row.created_at | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}
                    </template>
                    <template slot="by" slot-scope="props">
                        {{ props.row.user ? props.row.user.name : '' }}
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <router-link :to="'/logs/' + props.row.id" class="button is-primary is-small">
                                    <i class="fas fa-file-alt" />
                                    <span class="tooltip-text">View</span>
                                </router-link>
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
</template>

<script>
import tBus from '../../mixins/Tbus';
export default {
    mixins: [tBus('app/logs')],
    data() {
        return {
            loading: true,
            filtering: false,
            columns: ['created_at', 'log_name', 'description', 'by', 'subject_type', 'subject_id', 'actions'],
            filters: new this.$form({
                created_at: '',
                log_name: '',
                description: '',
                user: { name: '' },
                subject_type: '',
                subject_id: '',
                range: 1,
                date_range: '',
            }),
            options: {
                orderBy: { ascending: false, column: 'created_at' },
                sortable: ['created_at', 'log_name', 'description', 'subject_id', 'subject_type', ''],
                columnsClasses: {
                    created_at: 'w175',
                    log_name: 'w125',
                    subject_id: 'w125 has-text-centered',
                    subject_type: 'w150',
                    by: 'w150',
                    actions: 'w75 has-text-centered p-x-none',
                },
                filterable: ['created_at', 'log_name', 'description', 'subject_id', 'subject_type'],
            },
        };
    },
};
</script>

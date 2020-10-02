<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
                    <i class="fas fa-cog" />
                </button>
                <router-link
                    to="/recurrings/add"
                    class="button is-link is-small is-pulled-right"
                    v-if="!$store.getters.customer && !$store.getters.vendor"
                >
                    <i class="fas fa-plus m-r-sm" /> Create New Recurring Invoice
                </router-link>
                Recurring Invoices
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block table-body-br">
                <v-server-table name="recurringsTable" :url="url" :columns="columns" :options="options" ref="recurringsTable">
                    <template slot="start_date" slot-scope="props">
                        {{ props.row.start_date | formatDate(dateformat) }}
                    </template>
                    <template slot="last_created_at" slot-scope="props">
                        {{ props.row.last_created_at | formatDate(dateformat) }}
                    </template>
                    <template slot="customer" slot-scope="props">
                        {{ props.row.customer.name }}
                    </template>
                    <template slot="repeat" slot-scope="props">
                        <div class="has-text-centered">
                            <span class="tag is-info">{{ props.row.repeat }}</span>
                        </div>
                    </template>
                    <template slot="active" slot-scope="props">
                        <div class="has-text-centered">
                            <span v-if="props.row.active" class="tag is-success">Active</span>
                            <span v-else class="tag is-warning"> Draft</span>
                        </div>
                    </template>
                    <template slot="grand_total" slot-scope="props">
                        <div class="has-text-right">{{ props.row.grand_total | formatNumber }}</div>
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <router-link :to="'/recurrings/edit/' + props.row.id" class="button is-warning is-small">
                                    <i class="fas fa-edit"></i>
                                    <span class="tooltip-text">Edit</span>
                                </router-link>
                            </p>
                            <p class="control tooltip">
                                <button type="button" class="button is-danger is-small" @click="deleteRecord(props.row.id)">
                                    <i class="fas fa-trash"></i>
                                    <span class="tooltip-text">Delete</span>
                                </button>
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
    </div>
</template>

<script>
import mId from '../../mixins/Mid';
import tBus from '../../mixins/Tbus';
export default {
    mixins: [mId, tBus('app/recurrings')],
    data() {
        return {
            columns: ['reference', 'start_date', 'last_created_at', 'repeat', 'customer', 'active', 'grand_total', 'actions'],
            filters: new this.$form({
                reference: '',
                start_date: '',
                last_created_at: '',
                repeat: '',
                customer: { name: '' },
                active: '',
                grand_total: '',
                range: 'start_date',
                date_range: '',
            }),
            options: {
                orderBy: { ascending: false, column: 'start_date' },
                sortable: ['id', 'reference', 'start_date', 'last_created_at', 'repeat', 'active', 'grand_total'],
                filterable: ['id', 'reference', 'start_date', 'last_created_at', 'repeat', 'customer.name', 'active', 'grand_total'],
                columnsClasses: {
                    id: 'w50 has-text-centered',
                    reference: 'w175',
                    repeat: 'w100',
                    last_created_at: 'w150',
                    customer: 'w200',
                    start_date: 'w150',
                    active: 'w100',
                    grand_total: 'w150',
                    actions: 'w100 has-text-centered p-x-none',
                },
            },
        };
    },
};
</script>

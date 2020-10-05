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
                <router-link to="/taxes/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-plus m-r-sm" /> Add Tax
                </router-link>
                Taxes
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block">
                <v-server-table name="taxesTable" :url="url" :columns="columns" :options="options" ref="taxesTable">
                    <template slot="rate" slot-scope="props">
                        <div class="has-text-right">{{ props.row.rate | formatNumber }}</div>
                    </template>
                    <template slot="compound" slot-scope="props">
                        <div
                            class="has-text-centered"
                            :class="{ 'has-text-success': props.row.compound, 'has-text-danger': !props.row.compound }"
                        >
                            <i class="fas" :class="props.row.compound ? 'fa-check' : 'fa-times'" />
                        </div>
                    </template>
                    <template slot="recoverable" slot-scope="props">
                        <div
                            class="has-text-centered"
                            :class="{ 'has-text-success': props.row.recoverable, 'has-text-danger': !props.row.recoverable }"
                        >
                            <i class="fas" :class="props.row.recoverable ? 'fa-check' : 'fa-times'" />
                        </div>
                    </template>
                    <template slot="state" slot-scope="props">
                        <div
                            class="has-text-centered"
                            :class="{ 'has-text-success': props.row.state, 'has-text-danger': !props.row.state }"
                        >
                            <span class="tag is-link" v-if="!props.row.state">For All</span>
                            <span class="tag is-primary" v-else-if="props.row.same">Same</span>
                            <span class="tag is-warning" v-else>Different</span>
                        </div>
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link :to="'/taxes/edit/' + props.row.id" class="button is-warning is-small">
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
    mixins: [mId, tBus('app/taxes')],
    data() {
        return {
            amount: 0,
            filters: 0,
            columns: ['code', 'name', 'rate', 'details', 'number', 'compound', 'recoverable', 'state', 'actions'],
            options: {
                perPage: 10,
                orderBy: { ascending: true, column: 'code' },
                filterable: ['id', 'code', 'name', 'rate', 'details', 'number'],
                sortable: ['id', 'code', 'name', 'rate', 'details', 'number', 'compound', 'recoverable', 'state'],
                columnsClasses: {
                    id: 'w50 has-text-centered',
                    code: 'w125',
                    name: 'w225',
                    compound: 'w125',
                    recoverable: 'w150',
                    rate: 'w125',
                    state: 'w125',
                    actions: 'w100 has-text-centered p-x-none',
                    number: 'w175',
                },
            },
        };
    },
};
</script>

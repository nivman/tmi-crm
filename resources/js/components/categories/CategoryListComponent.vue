<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <router-link to="/categories/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-plus m-r-sm" /> Add Category
                </router-link>
                Categories
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block">
                <v-server-table name="categoriesTable" :url="url" :columns="columns" :options="options" ref="categoriesTable">
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <router-link :to="'/categories/edit/' + props.row.id" class="button is-warning is-small">
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
    mixins: [mId, tBus('app/categories')],
    data() {
        return {
            columns: ['name', 'actions'],
            options: {
                perPage:10,
                orderBy: { ascending: true, column: 'name' },
                sortable: ['id', 'name'],
                columnsClasses: { id: 'w50 has-text-centered', actions: 'w100 has-text-centered p-x-none' },
            },
        };
    },
};
</script>

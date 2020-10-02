<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <router-link to="/users/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-plus m-r-sm" /> Add User
                </router-link>
                Users
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block">
                <v-server-table name="usersTable" :url="url" :columns="columns" :options="options" ref="usersTable">
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <router-link :to="'/profile/' + props.row.username" class="button is-warning is-small">
                                    <i class="fas fa-edit"></i>
                                    <span class="tooltip-text">Edit</span>
                                </router-link>
                            </p>
                            <p class="control tooltip">
                                <button type="button" class="button is-danger is-small" @click="deleteRecord(props.row.username)">
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
    mixins: [mId, tBus('app/users')],
    data() {
        return {
            loading: true,
            columns: ['name', 'username', 'email', 'phone', 'actions'],
            options: {
                orderBy: { ascending: true, column: 'name' },
                sortable: ['id', 'name', 'username', 'email', 'phone'],
                columnsClasses: { id: 'w50 has-text-centered', actions: 'w125 has-text-centered p-x-none' },
            },
        };
    },
};
</script>

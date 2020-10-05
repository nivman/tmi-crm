<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <router-link to="/settings/fields/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-plus m-r-sm" /> Add Custom Field
                </router-link>
                Custom Fields
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block">
                <v-server-table name="customFieldsTable" :url="url" :columns="columns" :options="options" ref="customFieldsTable">
                    <template slot="required" slot-scope="props">
                        <div class="has-text-centered">
                            <i class="fas" :class="props.row.is_required ? 'fa-check' : 'fa-times'" />
                        </div>
                    </template>
                    <template slot="entities" slot-scope="props">
                        {{ props.row.entities.map(entity => entity.entity_type.split('\\')[1]).join(', ') }}
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <router-link :to="'/settings/fields/edit/' + props.row.id" class="button is-warning is-small">
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
    mixins: [mId, tBus('app/custom_fields')],
    data() {
        return {
            columns: ['name', 'slug', 'type', 'sort_order', 'required', 'entities', 'actions'],
            options: {
                perPage: 10,
                orderBy: { ascending: true, column: 'sort_order' },
                sortable: ['id', 'name', 'slug', 'type', 'sort_order', 'required'],
                columnsClasses: {
                    id: 'w50',
                    name: 'w175',
                    sort_order: 'w125',
                    required: 'w125',
                    slug: 'w125',
                    type: 'w125',
                    actions: 'w100 has-text-centered p-x-none',
                },
            },
        };
    },
};
</script>

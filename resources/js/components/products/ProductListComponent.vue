<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button class="button is-primary is-small is-pulled-right m-l-sm is-hidden-desktop" @click="filtering = !filtering">
                    <i class="fas fa-cog" />
                </button>
                <router-link to="/products/add" class="button is-link is-small is-pulled-right">
                    <i class="fas fa-plus m-r-sm" /> Add Product
                </router-link>
                Products
                <i v-if="loading" class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="panel-block table-body-br">

                <v-server-table name="productsTable" :url="url" :columns="columns" :options="options" ref="productsTable">
                    <template slot="category" slot-scope="props">
                        {{ props.row.categories.map(c => c.name).join('') }}
                    </template>
                    <template slot="taxes" slot-scope="props">
                        <span v-html="showTaxes(props.row.taxes)"></span>
                    </template>
                    <template v-if="$store.getters.stock" slot="qty" slot-scope="props">
                        <div class="has-text-centered">{{ props.row.stock ? props.row.stock.qty : 0 | formatNumber }}</div>
                    </template>
                    <template v-if="$store.getters.admin || $store.getters.superAdmin" slot="cost" slot-scope="props">
                        <div class="has-text-right">{{ props.row.cost | formatNumber }}</div>
                    </template>
                    <template slot="price" slot-scope="props">
                        <div class="has-text-right">{{ props.row.price | formatNumber }}</div>
                    </template>
                    <template slot="actions" slot-scope="props">
                        <div class="buttons has-addons is-centered">
                            <p class="control tooltip">
                                <router-link :to="'/products/' + props.row.id" class="button is-primary is-small">
                                    <i class="fas fa-file"></i>
                                    <span class="tooltip-text">View</span>
                                </router-link>
                            </p>
                            <p class="control tooltip" v-if="$store.getters.admin">
                                <router-link :to="'/products/edit/' + props.row.id" class="button is-warning is-small">
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
import mId from '../../mixins/Mid';
import tBus from '../../mixins/Tbus';
export default {
    mixins: [mId, tBus('app/products')],
    data() {
        return {

            columns: ['code', 'name', 'category', 'taxes', 'price', 'actions'],
            filters: new this.$form({ code: '', name: '', categories: { name: '' }, taxes: { name: '' }, price: '', range: 0 }),
            options: {
                perPage:10,
                orderBy: { ascending: true, column: 'code' },
                sortable: ['id', 'code', 'name', 'cost', 'price'],
                columnsClasses: { id: 'w50 has-text-centered', qty: 'w75', actions: 'w125 has-text-centered p-x-none' },
                filterable: ['id', 'code', 'name', 'categories.name', 'taxes.name', 'cost', 'price'],
            },
        };
    },
    created() {
        // if (this.$store.getters.admin || this.$store.getters.superAdmin) {
        //     let filters = { code: '', name: '', categories: { name: '' }, taxes: { name: '' }, cost: '', price: '', nf1: 'Qty', range: 0 };
        //     let actions = this.columns.pop();
        //     let price = this.columns.pop();
        //     this.columns.push('cost');
        //     this.columns.push(price);
        //     if (this.$store.getters.stock) {
        //         this.columns.push('qty');
        //     } else {
        //         delete filters.nf1;
        //     }
        //     this.columns.push(actions);
        //     this.filters = new this.$form(filters);
        // }
    },
    methods: {
        showTaxes(taxes) {
            return taxes ? taxes.map(tax => '<span class="tag is-primary has-text-weight-bold">' + tax.name + '</span>').join(' ') : '';
        },
    },
};
</script>

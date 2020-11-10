<template>
    <tfoot>
        <tr class="is-active is-hidden-touch has-text-weight-bold">
            <td v-for="(col, index) in cols" :key="index + col" class="filter" v-if="shouldDisplay(col)">
                <flat-pickr
                    placeholder="Created at"
                    v-if="col == 'created_at'"
                    class="input input-filter"
                    v-model="filters.created_at"
                    :config="{ dateFormat: 'Y-m-d' }"
                ></flat-pickr>
                <flat-pickr
                    placeholder="Date"
                    v-model="filters.date"
                    v-else-if="col == 'date'"
                    class="input input-filter"
                    :config="{ dateFormat: 'Y-m-d' }"
                ></flat-pickr>
                <flat-pickr
                    placeholder="Start date"
                    class="input input-filter"
                    v-model="filters.start_date"
                    v-else-if="col == 'start_date'"
                    :config="{ dateFormat: 'Y-m-d' }"
                ></flat-pickr>
                <flat-pickr
                    placeholder="Created at"
                    class="input input-filter"
                    v-model="filters.last_created_at"
                    :config="{ dateFormat: 'Y-m-d' }"
                    v-else-if="col == 'last_created_at'"
                ></flat-pickr>
                <div v-else-if="col == 'range'" class="has-text-centered">Actions</div>
                <div v-else-if="col == 'nf1'" class="p-l-sm">{{ filters[col] }}</div>
                <div v-else-if="col == 'nf2'" class="p-l-sm">{{ filters[col] }}</div>
                <div class="p-r-sm has-text-right" v-else-if="col == 'amount' || col == 'balance' || col == 'grand_total'">
                    {{ amount | formatNumber }}
                </div>
                <input
                    :name="col"
                    type="text"
                    autocomplete="off"
                    class="input input-filter"
                    :placeholder="col | sc2txt"
                    v-else-if="col == 'account'"
                    v-model="filters.account.name"
                />
                <input
                    type="text"
                    :name="col"
                    autocomplete="off"
                    placeholder="Category"
                    class="input input-filter"
                    v-else-if="col == 'categories'"
                    v-model="filters.categories.name"
                />
                <input
                    type="text"
                    :name="col"
                    autocomplete="off"
                    placeholder="Customer"
                    class="input input-filter"
                    v-else-if="col == 'customer'"
                    v-model="filters.customer.name"
                />
                <input
                    type="text"
                    :name="col"
                    autocomplete="off"
                    placeholder="Vendor"
                    class="input input-filter"
                    v-else-if="col == 'vendor'"
                    v-model="filters.vendor.name"
                />
                <input
                    type="text"
                    :name="col"
                    placeholder="For"
                    autocomplete="off"
                    class="input input-filter"
                    v-else-if="col == 'payable'"
                    v-model="filters.payable.name"
                />
                <input
                    type="text"
                    :name="col"
                    placeholder="By"
                    autocomplete="off"
                    v-else-if="col == 'user'"
                    class="input input-filter"
                    v-model="filters.user.name"
                />
                <input
                    type="text"
                    :name="col"
                    autocomplete="off"
                    class="input input-filter"
                    v-else-if="col == 'taxes'"
                    :placeholder="col | sc2txt"
                    v-model="filters.taxes.name"
                />
                <div class="select transparent" v-else-if="col == 'draft'">
                    <select v-model="filters['draft']" :name="'draft'">
                        <option value="">All</option>
                        <option value="0">Finalised</option>
                        <option value="1">Draft</option>
                    </select>
                </div>
                <div class="select transparent" v-else-if="col == 'received'" style="width:100%;">
                    <select v-model="filters['received']" :name="'received'">
                        <option value="">All</option>
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <input
                    type="text"
                    :name="col"
                    autocomplete="off"
                    v-model="filters[col]"
                    class="input input-filter"
                    :placeholder="col | sc2txt"
                    v-else-if="col != 'draft' || col != 'received'"
                />
            </td>
        </tr>
        <tr class="is-active is-hidden-touch" v-if="filters.range">
            <td class="filter" colspan="100%">
                <flat-pickr
                    type="date"
                    placeholder="טווח תאריכים"
                    class="date-range-input"
                    v-model="filters.date_range"
                    :config="{ mode: 'range', dateFormat: 'd/m/y' }"

                ></flat-pickr>
            </td>
        </tr>
    </tfoot>
</template>

<script>
export default {
    data() {
        return {
            hidden: ['formdata', 'uploads', 'date_range', 'qty'],
        };
    },
    props: {
        filters: { type: Object, required: true },
        amount: { type: Number, default: 0 },
    },
    computed: {
        cols() {
            return Object.keys(this.filters).filter(c => c != 'date_range');
        },
    },
    methods: {
        shouldDisplay(col) {
            return !this.hidden.includes(col);
        },
    },
};
</script>
<style>
.date-range-input {
  height: 38px;
  text-align: right;
  background-color: white;
  border-color: #dbdbdb;
  border-radius: 4px;
  color: #363636;
  box-shadow: none;
  max-width: 100%;
  width: 138px;
  font-size: 12px;
}
.date-range-input::placeholder {
  font-size: 16px;
}
</style>
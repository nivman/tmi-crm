<template>
    <div class="panel panel-default is-hidden-desktop animated slideInDown">
        <div class="modal-card-body is-rounded">
            <button
                type="button"
                class="delete is-pulled-right"
                style="top:-15px;right:-15px;"
                @click="$emit('hide-panel-filters')"
            ></button>
            <div v-for="(col, index) in Object.keys(filters)" :key="index" class="field" v-if="shouldDisplay(col)">
                <label class="label">{{ col | sc2txt }}</label>
                <div class="control">
                    <flat-pickr
                        class="input"
                        placeholder="Created at"
                        v-if="col == 'created_at'"
                        v-model="filters.created_at"
                        :config="{ dateFormat: 'Y-m-d' }"
                    ></flat-pickr>
                    <flat-pickr
                        class="input"
                        placeholder="Date"
                        v-model="filters.date"
                        v-else-if="col == 'date'"
                        :config="{ dateFormat: 'Y-m-d' }"
                    ></flat-pickr>
                    <flat-pickr
                        class="input"
                        placeholder="Start date"
                        v-model="filters.start_date"
                        v-else-if="col == 'start_date'"
                        :config="{ dateFormat: 'Y-m-d' }"
                    ></flat-pickr>
                    <flat-pickr
                        class="input"
                        placeholder="Created at"
                        v-model="filters.last_created_at"
                        :config="{ dateFormat: 'Y-m-d' }"
                        v-else-if="col == 'last_created_at'"
                    ></flat-pickr>
                    <input
                        type="text"
                        :name="col"
                        class="input"
                        autocomplete="off"
                        :placeholder="col | sc2txt"
                        v-else-if="col == 'account'"
                        v-model="filters.account.name"
                    />
                    <input
                        type="text"
                        :name="col"
                        class="input"
                        autocomplete="off"
                        placeholder="Category"
                        v-else-if="col == 'categories'"
                        v-model="filters.categories.name"
                    />
                    <input
                        type="text"
                        :name="col"
                        class="input"
                        autocomplete="off"
                        placeholder="Customer"
                        v-else-if="col == 'customer'"
                        v-model="filters.customer.name"
                    />
                    <input
                        type="text"
                        :name="col"
                        class="input"
                        autocomplete="off"
                        placeholder="Vendor"
                        v-else-if="col == 'vendor'"
                        v-model="filters.vendor.name"
                    />
                    <input
                        type="text"
                        :name="col"
                        class="input"
                        placeholder="For"
                        autocomplete="off"
                        v-else-if="col == 'payable'"
                        v-model="filters.payable.name"
                    />
                    <input
                        type="text"
                        :name="col"
                        class="input"
                        placeholder="By"
                        autocomplete="off"
                        v-else-if="col == 'user'"
                        v-model="filters.user.name"
                    />
                    <input
                        type="text"
                        :name="col"
                        class="input"
                        autocomplete="off"
                        v-else-if="col == 'taxes'"
                        :placeholder="col | sc2txt"
                        v-model="filters.taxes.name"
                    />
                    <div class="select" v-else-if="col == 'draft'" style="width:100%;">
                        <select v-model="filters['draft']" :name="'draft'" style="width:100%;">
                            <option value="">All</option>
                            <option value="0">Finalised</option>
                            <option value="1">Draft</option>
                        </select>
                    </div>
                    <div class="select" v-else-if="col == 'received'" style="width:100%;">
                        <select v-model="filters['received']" :name="'received'" style="width:100%;">
                            <option value="">All</option>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <input
                        v-else
                        type="text"
                        :name="col"
                        class="input"
                        autocomplete="off"
                        v-model="filters[col]"
                        :placeholder="col | sc2txt"
                    />
                </div>
            </div>
            <div class="field" v-if="filters.range">
                <label class="label">Date range</label>
                <div class="control">
                    <flat-pickr
                        class="input"
                        placeholder="Date range"
                        v-model="filters.date_range"
                        :config="{ mode: 'range', dateFormat: 'Y-m-d' }"
                    ></flat-pickr>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            hidden: ['formdata', 'date_range', 'nf1', 'nf2', 'uploads', 'range', 'amount', 'balance', 'qty'],
        };
    },
    props: {
        filters: { type: Object, required: true },
    },
    methods: {
        shouldDisplay(col) {
            return !this.hidden.includes(col);
        },
    },
};
</script>

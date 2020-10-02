<template>
    <modal
        height="auto"
        width="640px"
        :adaptive="true"
        :scrollable="true"
        transition="custom"
        classes="is-rounded"
        :clickToClose="false"
        name="product-form-modal"
        @before-close="$storage.remove('order')"
    >
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "Edit Product" : "Add New Product" }}
                    </p>
                    <button
                        type="button"
                        class="delete"
                        @click="$modal.hide('product-form-modal')"
                    ></button>
                </header>
                <section class="modal-card-body is-radius-bottom">
                    <loading v-if="loading"></loading>
                    <div class="columns">
                        <div class="column is-half">
                            <div class="field">
                                <label class="label" for="code">Code</label>
                                <input
                                    id="code"
                                    name="code"
                                    type="text"
                                    class="input"
                                    v-model="form.code"
                                    v-validate="'required'"
                                    :class="{ 'is-danger': errors.has('code') }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("code") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="name">Name</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    class="input"
                                    v-model="form.name"
                                    v-validate="'required'"
                                    :class="{ 'is-danger': errors.has('name') }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("name") }}
                                </div>
                            </div>
                            <number-input-component
                                :value.sync="form.cost"
                                :validation="{
                                    rules: 'required',
                                    name: 'cost'
                                }"
                                :field="{
                                    label: 'Cost',
                                    name: 'cost',
                                    id: 'cost'
                                }"
                            ></number-input-component>
                            <number-input-component
                                :value.sync="form.price"
                                :validation="{
                                    rules: 'required',
                                    name: 'price'
                                }"
                                :field="{
                                    label: 'Price',
                                    name: 'price',
                                    id: 'price'
                                }"
                            ></number-input-component>
                            <number-input-component
                                v-if="$store.getters.stock"
                                :value.sync="form.qty"
                                :validation="{ rules: 'required', name: 'qty' }"
                                :field="{
                                    label: 'Qty',
                                    name: 'qty',
                                    id: 'qty'
                                }"
                            ></number-input-component>
                            <div class="field">
                                <label class="label" for="category"
                                    >Category</label
                                >
                                <div class="control">
                                    <div
                                        class="select is-fullwidth"
                                        :class="{
                                            'is-danger': errors.has('category')
                                        }"
                                    >
                                        <select
                                            id="category"
                                            name="category"
                                            v-model="form.category"
                                            v-validate="'required'"
                                            placeholder="Select Category..."
                                        >
                                            <option
                                                v-if="
                                                    $store.state.settings.ac
                                                        .select
                                                "
                                                value=""
                                                disabled
                                                >Select Category...</option
                                            >
                                            <option
                                                v-for="category in categories"
                                                :key="category.id"
                                                :value="category.id"
                                                >{{ category.name }}</option
                                            >
                                        </select>
                                    </div>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("category") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="field">
                                <label class="label" for="details"
                                    >Details</label
                                >
                                <textarea
                                    rows="5"
                                    id="details"
                                    name="details"
                                    class="textarea"
                                    v-model="form.details"
                                    :class="{
                                        'is-danger': errors.has('details')
                                    }"
                                ></textarea>
                                <div class="help is-danger">
                                    {{ errors.first("details") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="taxes">Taxes</label>
                                <div class="select is-fullwidth is-multiple">
                                    <select
                                        v-model="form.taxes"
                                        id="taxes"
                                        :class="{
                                            'is-danger': errors.has('taxes')
                                        }"
                                        multiple
                                        size="5"
                                    >
                                        <option
                                            v-for="tax in taxes"
                                            :key="tax.id"
                                            :value="tax.id"
                                            >{{ tax.name }}</option
                                        >
                                    </select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("taxes") }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="attributes">
                        <h5 class="cf">Custom Fields</h5>
                        <div class="columns is-multiline">
                            <div
                                class="column is-half"
                                v-for="attr in attributes"
                                :key="attr.slug"
                            >
                                <custom-field-component
                                    :attr="attr"
                                    v-model="form[attr.slug]"
                                ></custom-field-component>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <button
                                type="submit"
                                class="button is-link is-fullwidth"
                                :disabled="errors.any()"
                            >
                                Save & Add to Order
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </form>
        <button
            class="modal-close is-large is-hidden-mobile"
            aria-label="close"
            @click="$modal.hide('product-form-modal')"
        ></button>
    </modal>
</template>

<script>
export default {
    data() {
        return {
            update: 1,
            taxes: [],
            loading: true,
            categories: [],
            attributes: [],
            form: new this.$form({
                id: "",
                code: "",
                name: "",
                category: "",
                price: "",
                cost: "",
                details: "",
                taxes: []
            })
        };
    },
    created() {
        this.$http
            .get("app/categories?all=1")
            .then(res => {
                this.categories = res.data;
                if (
                    !this.$store.state.settings.ac.select &&
                    this.categories.length > 0
                ) {
                    this.form.category = this.categories[0].id;
                }
                this.$http
                    .get("app/taxes?all=1")
                    .then(res => {
                        this.taxes = res.data;
                        this.$http
                            .get(`app/products/create`)
                            .then(res => {
                                this.attributes = res.data;
                                this.loading = false;
                            })
                            .catch(err =>
                                this.$event.fire("appError", err.response)
                            );
                    })
                    .catch(err => this.$event.fire("appError", err.response));
            })
            .catch(err => this.$event.fire("appError", err.response));
    },
    methods: {
        submit() {
            this.form
                .post("app/products")
                .then(({ data }) => {
                    let order = "addProduct" + this.$storage.read("order");
                    this.$event.fire(order, data);
                    this.notify(
                        "success",
                        "Product has been successfully added."
                    );
                    this.$modal.hide("product-form-modal");
                })
                .catch(err => this.$event.fire("appError", err.response));
        },
        validateForm() {
            this.$validator
                .validateAll()
                .then(result => {
                    if (result) {
                        this.submit();
                    }
                })
                .catch(err => this.$event.fire("appError", err));
        }
    }
};
</script>

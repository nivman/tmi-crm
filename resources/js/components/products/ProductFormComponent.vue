<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "Edit Product" : "Add New Product" }}
                    </p>
                    <button
                        type="button"
                        class="delete"
                        @click="$router.go(-1)"
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
                                    type="text"
                                    name="code"
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
                                    type="text"
                                    name="name"
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
                                :value.sync="form.qty"
                                v-if="$store.getters.stock"
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
                                                :key="category.id"
                                                :value="category.id"
                                                v-for="category in categories"
                                            >
                                                {{ category.name }}
                                            </option>
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
                                        multiple
                                        size="5"
                                        id="taxes"
                                        v-model="form.taxes"
                                        :class="{
                                            'is-danger': errors.has('taxes')
                                        }"
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
                            <div class="field">
                                <label class="label" for="email">Photo</label>
                                <div class="control">
                                    <div class="file has-name is-fullwidth">
                                        <label class="file-label">
                                            <input
                                                type="file"
                                                ref="image"
                                                name="image"
                                                accept="image/*"
                                                class="file-input"
                                                @change="displayName"
                                            />
                                            <span class="file-cta">
                                                <span class="file-icon">
                                                    <i
                                                        class="fas fa-upload"
                                                    ></i>
                                                </span>
                                                <span class="file-label"></span>
                                            </span>
                                            <span
                                                class="file-name"
                                                ref="fileUploadName"
                                            ></span>
                                        </label>
                                    </div>
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
                                :class="{ 'is-loading': isSaving }"
                                :disabled="errors.any() || isSaving"
                            >
                                Submit
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </form>
        <button
            class="modal-close is-large is-hidden-mobile"
            aria-label="close"
            @click="$router.go(-1)"
        ></button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            taxes: [],
            loading: true,
            categories: [],
            attributes: [],
            isSaving: false,
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
                        if (this.$route.params.id) {
                            this.fetchProduct(this.$route.params.id);
                        } else {
                            this.$http
                                .get(`app/products/create`)
                                .then(res => {
                                    this.attributes = res.data;
                                    this.loading = false;
                                })
                                .catch(err =>
                                    this.$event.fire("appError", err.response)
                                );
                        }
                    })
                    .catch(err => {
                        this.$event.fire("appError", err.response);
                    });
            })
            .catch(err => {
                this.$event.fire("appError", err.response);
            });
    },
    methods: {
        submit() {
            this.isSaving = true;
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/products/${this.form.id}`, !!this.form.image)
                    .then(() => {
                        this.$event.fire("refreshProductsTable");
                        this.notify(
                            "success",
                            "Product has been successfully updated."
                        );
                        this.$router.push("/products");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            } else {
                this.form
                    .post("app/products", !!this.form.image)
                    .then(() => {
                        this.$event.fire("refreshProductsTable");
                        this.notify(
                            "success",
                            "Product has been successfully added."
                        );
                        this.$router.push("/products");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            }
        },
        fetchProduct(id) {
            this.$http
                .get(`app/products/${id}`)
                .then(res => {
                    delete res.data.image;
                    this.attributes = res.data.attributes;
                    res.data.category = res.data.categories[0].id;
                    delete res.data.attributes;
                    delete res.data.categories;
                    res.data.taxes = res.data.taxes.map(tax => tax.id);
                    this.form = new this.$form(res.data);
                    this.loading = false;
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
        },
        displayName(event) {
            this.form["image"] = true;
            this.form.appendData("image", this.$refs.image.files[0]);
            this.$refs.fileUploadName.innerText = event.srcElement.files[0]
                ? event.srcElement.files[0].name
                : "";
        }
    }
};
</script>

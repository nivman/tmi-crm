<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{
                            form.id
                                ? "Edit Custom Field"
                                : "Add New Custom Field"
                        }}
                    </p>
                    <button
                        type="button"
                        class="delete"
                        @click="$router.go(-1)"
                    ></button>
                </header>
                <section class="modal-card-body is-radius-bottom">
                    <div class="columns">
                        <div class="column is-half">
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
                            <div class="field">
                                <label class="label" for="slug">Slug</label>
                                <input
                                    id="slug"
                                    name="slug"
                                    type="text"
                                    class="input"
                                    v-model="form.slug"
                                    v-validate="'required'"
                                    :class="{ 'is-danger': errors.has('slug') }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("slug") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="type">Type</label>
                                <div class="select is-fullwidth">
                                    <select
                                        v-model="form.type"
                                        id="type"
                                        :class="{
                                            'is-danger': errors.has('type')
                                        }"
                                    >
                                        <option value="varchar">String</option>
                                        <option value="text">Text</option>
                                        <option value="boolean">Boolean</option>
                                        <option value="datetime"
                                            >Datetime</option
                                        >
                                        <option value="integer">Integer</option>
                                    </select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("type") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="is_required"
                                    >Required</label
                                >
                                <div class="select is-fullwidth">
                                    <select
                                        v-model="form.is_required"
                                        id="is_required"
                                        :class="{
                                            'is-danger': errors.has(
                                                'is_required'
                                            )
                                        }"
                                    >
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("is_required") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="sort_order"
                                    >Sort Order</label
                                >
                                <input
                                    type="number"
                                    class="input"
                                    id="sort_order"
                                    name="sort_order"
                                    v-model="form.sort_order"
                                    v-validate="'required|integer'"
                                    :class="{
                                        'is-danger': errors.has('sort_order')
                                    }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("sort_order") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="field">
                                <label class="label" for="entities"
                                    >Entities</label
                                >
                                <div class="select is-fullwidth is-multiple">
                                    <select
                                        size="5"
                                        multiple
                                        id="entities"
                                        v-model="form.entities"
                                        v-validate="'required'"
                                        :class="{
                                            'is-danger': errors.has('entities')
                                        }"
                                    >
                                        <option value="App\Account"
                                            >Accounts</option
                                        >
                                        <option value="App\Customer"
                                            >Customer</option
                                        >
                                        <option value="App\Expense"
                                            >Expenses</option
                                        >
                                        <option value="App\Income"
                                            >Income</option
                                        >
                                        <option value="App\Invoice"
                                            >Invoice</option
                                        >
                                        <option value="App\Product"
                                            >Products</option
                                        >
                                        <option value="App\Purchase"
                                            >Purchase</option
                                        >
                                        <!-- <option value="App\Seller">Sellers</option> -->
                                        <option value="App\Vendor"
                                            >Vendor</option
                                        >
                                    </select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("entities") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="description"
                                    >Description</label
                                >
                                <textarea
                                    rows="4"
                                    type="text"
                                    id="description"
                                    class="textarea"
                                    name="description"
                                    v-model="form.description"
                                    :class="{
                                        'is-danger': errors.has('description')
                                    }"
                                ></textarea>
                                <div class="help is-danger">
                                    {{ errors.first("description") }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <button
                            type="submit"
                            class="button is-link is-fullwidth"
                            :disabled="errors.any()"
                        >
                            Submit
                        </button>
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
            form: new this.$form({
                id: "",
                name: "",
                slug: "",
                type: "varchar",
                sort_order: "",
                description: "",
                is_required: "0",
                entities: []
            })
        };
    },
    computed: {
        is_edit() {
            return this.form.id && this.form.id !== "";
        }
    },
    created() {
        if (this.$route.params.id) {
            this.fetchExpense(this.$route.params.id);
        }
    },
    methods: {
        submit() {
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/custom_fields/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshCustomFieldsTable");
                        this.notify(
                            "success",
                            "Custom field has been successfully updated."
                        );
                        this.$router.push("/settings/fields");
                    })
                    .catch(err => {
                        this.$event.fire("appError", err.response);
                    });
            } else {
                this.form
                    .post("app/custom_fields")
                    .then(() => {
                        this.$event.fire("refreshCustomFieldsTable");
                        this.notify(
                            "success",
                            "Custom field has been successfully added."
                        );
                        this.$router.push("/settings/fields");
                    })
                    .catch(err => {
                        this.$event.fire("appError", err.response);
                    });
            }
        },
        fetchExpense(id) {
            this.$http
                .get(`app/custom_fields/${id}`)
                .then(res => {
                    let entities = res.data.entities.map(e => e.entity_type);
                    let is_required = res.data.is_required ? "1" : "0";
                    this.form = new this.$form(res.data);
                    this.form.is_required = is_required;
                    this.form.entities = entities;
                })
                .catch(err => {
                    this.$event.fire("appError", err.response);
                });
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
        hasValue(v) {
            return (
                this.form.entities.filter(e => e.entity_type == v).length > 0
            );
        }
    }
};
</script>

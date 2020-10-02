<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "Edit Income" : "Add New Income" }}
                    </p>
                    <button
                        type="button"
                        class="delete"
                        @click="$router.go(-1)"
                    ></button>
                </header>
                <section class="modal-card-body is-radius-bottom">
                    <loading v-if="loading"></loading>
                    <!-- <div class="field">
                    <label class="label" for="date">Date</label>
                    <div class="control">
                        <flat-pickr v-model="form.date" format="yyyy-MM-dd" v-validate="'required'" name="date" id="date" class="input" :class="{'is-danger': errors.has('date') }"></flat-pickr>
                    </div>
                    <div class="help is-danger">{{ errors.first('date') }}</div>
                </div> -->
                    <div class="columns">
                        <div class="column is-half">
                            <div class="field">
                                <label class="label" for="title">Title</label>
                                <input
                                    id="title"
                                    type="text"
                                    name="title"
                                    class="input"
                                    v-model="form.title"
                                    v-validate="'required'"
                                    :class="{
                                        'is-danger': errors.has('title')
                                    }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("title") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="reference"
                                    >Reference</label
                                >
                                <input
                                    type="text"
                                    class="input"
                                    id="reference"
                                    name="reference"
                                    v-validate="'required'"
                                    v-model="form.reference"
                                    :class="{
                                        'is-danger': errors.has('reference')
                                    }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("reference") }}
                                </div>
                            </div>
                            <number-input-component
                                :value.sync="form.amount"
                                :field="{
                                    label: 'Amount',
                                    name: 'amount',
                                    id: 'amount'
                                }"
                                :validation="{
                                    rules: 'required',
                                    name: 'amount'
                                }"
                            ></number-input-component>
                            <div class="help is-danger">
                                {{ errors.first("amount") }}
                            </div>
                        </div>
                        <div class="column is-half">
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
                                            v-validate="'required'"
                                            v-model="form.category"
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
                            <div class="field">
                                <label class="label" for="account"
                                    >Account</label
                                >
                                <div class="control">
                                    <v-select
                                        label="name"
                                        name="account"
                                        v-model="account"
                                        input-id="account"
                                        :options="accounts"
                                        @input="accountChange"
                                        v-validate="'required'"
                                        :style="{ width: '100%' }"
                                        placeholder="Select Account..."
                                        :class="{
                                            select: true,
                                            'is-danger': errors.has('account')
                                        }"
                                    >
                                        <template slot="no-options">
                                            Please type to search...
                                        </template>
                                    </v-select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("account") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="details"
                                    >Details</label
                                >
                                <textarea
                                    rows="2"
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
import _debounce from "lodash/debounce";
export default {
    data() {
        return {
            accounts: [],
            account: null,
            loading: true,
            categories: [],
            attributes: [],
            isSaving: false,
            form: new this.$form({
                id: "",
                date: new Date(),
                title: "",
                reference: "",
                amount: "",
                category: "",
                account_id: "",
                details: ""
            })
        };
    },
    created() {
        this.$http
            .get("app/accounts?all=1")
            .then(res => {
                this.accounts = res.data;
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
                        if (this.$route.params.id) {
                            this.fetchIncome(this.$route.params.id);
                        } else {
                            this.$http
                                .get(`app/incomes/create`)
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
                    .put(`app/incomes/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshIncomesTable");
                        this.notify(
                            "success",
                            "Income has been successfully updated."
                        );
                        this.$router.push("/incomes");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            } else {
                this.form
                    .post("app/incomes")
                    .then(() => {
                        this.$event.fire("refreshIncomesTable");
                        this.notify(
                            "success",
                            "Income has been successfully added."
                        );
                        this.$router.push("/incomes");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            }
        },
        fetchIncome(id) {
            this.$http
                .get(`app/incomes/${id}`)
                .then(res => {
                    this.attributes = res.data.attributes;
                    res.data.category = res.data.categories[0].id;
                    delete res.data.attributes;
                    delete res.data.categories;
                    this.form = new this.$form(res.data);
                    this.account = this.accounts.find(
                        account => account.id == res.data.account_id
                    );
                    this.loading = false;
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
        accountChange(selected) {
            this.account = selected;
            this.form.account_id = selected ? selected.id : "";
        },
        getAccount(id) {
            this.$http
                .get(`app/accounts/${id}`)
                .then(res => {
                    this.accountChange(res.data);
                })
                .catch(err => {
                    this.$event.fire("appError", err.response);
                });
        }
    }
};
</script>

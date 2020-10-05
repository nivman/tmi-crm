<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "Edit Account" : "Add New Account" }}
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
                                <label class="label" for="type">Type</label>
                                <input
                                    id="type"
                                    type="text"
                                    name="type"
                                    class="input"
                                    v-model="form.type"
                                    v-validate="'required'"
                                    :class="{ 'is-danger': errors.has('type') }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("type") }}
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
                        </div>
                        <div class="column is-half">
                            <div class="field">
                                <label class="label" for="opening_balance"
                                    >Opening Balance</label
                                >
                                <input
                                    class="input"
                                    type="number"
                                    id="opening_balance"
                                    name="opening_balance"
                                    v-model="form.opening_balance"
                                    :readonly="form.id ? true : false"
                                    :class="{
                                        'is-danger': errors.has(
                                            'opening_balance'
                                        )
                                    }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("opening_balance") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="details"
                                    >Details</label
                                >
                                <textarea
                                    rows="3"
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
                                <checkbox-component
                                    id="offline"
                                    name="offline"
                                    v-model="form.offline"
                                    :checked=form.offline
                                    label="Show this in offline payments"
                                ></checkbox-component>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.id && form.id != '' && attributes">
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
            loading: true,
            attributes: [],
            isSaving: false,
            form: new this.$form({
                id: "",
                name: "",
                type: "",
                reference: "",
                opening_balance: "",
                details: "",
                offline: false
            })
        };
    },
    created() {
        if (this.$route.params.id) {
            this.fetchAccount(this.$route.params.id);
        } else {
            // this.$http.get(`app/accounts/create`)
            // .then(res => {
            //     this.attributes = res.data;
            this.loading = false;
            // })
            // .catch(err => this.$event.fire('appError', err.response));
        }
    },
    methods: {
        submit() {
            this.isSaving = true;
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/accounts/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshAccountsTable");
                        this.notify(
                            "success",
                            "Account has been successfully updated."
                        );
                        this.$router.push("/accounts");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            } else {
                this.form
                    .post("app/accounts")
                    .then(() => {
                        this.$event.fire("refreshAccountsTable");
                        this.notify(
                            "success",
                            "Account has been successfully added."
                        );
                        this.$router.push("/accounts");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            }
        },
        fetchAccount(id) {
            this.$http
                .get(`app/accounts/${id}`)
                .then(res => {
                    this.attributes = res.data.attributes;
                    delete res.data.attributes;
                    delete res.data.journal;
                    this.form = new this.$form(res.data);
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
        }
    }
};
</script>

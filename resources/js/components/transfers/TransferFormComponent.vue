<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card is-medium animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "Edit Transfer" : "Add New Transfer" }}
                    </p>
                    <button
                        type="button"
                        class="delete"
                        @click="$router.go(-1)"
                    ></button>
                </header>
                <section class="modal-card-body is-radius-bottom">
                    <loading v-if="loading"></loading>
                    <div class="field">
                        <label class="label" for="from">From Account</label>
                        <div class="control">
                            <v-select
                                ref="from"
                                name="from"
                                label="name"
                                input-id="from"
                                v-model="from"
                                :options="accounts"
                                v-validate="'required'"
                                :style="{ width: '100%' }"
                                @input="fromAccountChange"
                                placeholder="Select Account..."
                                :class="{
                                    select: true,
                                    'is-danger': errors.has('from')
                                }"
                            >
                                <template slot="no-options">
                                    Please type to search...
                                </template>
                            </v-select>
                        </div>
                        <div class="help is-danger">
                            {{ errors.first("from") }}
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="to">To Account</label>
                        <div class="control">
                            <v-select
                                name="to"
                                label="name"
                                v-model="to"
                                input-id="to"
                                :options="accounts"
                                :style="{ width: '100%' }"
                                @input="toAccountChange"
                                placeholder="Select Account..."
                                v-validate="{
                                    required: true,
                                    is_not: form.from
                                }"
                                :class="{
                                    select: true,
                                    'is-danger': errors.has('to')
                                }"
                            >
                                <template slot="no-options">
                                    Please type to search...
                                </template>
                            </v-select>
                        </div>
                        <div class="help is-danger">
                            {{ errors.first("to") }}
                        </div>
                    </div>
                    <div class="field">
                        <number-input-component
                            :value.sync="form.amount"
                            :field="{
                                label: 'Amount',
                                name: 'amount',
                                id: 'amount'
                            }"
                            :validation="{
                                rules: { required: true },
                                name: 'amount'
                            }"
                        ></number-input-component>
                        <div class="help is-danger">
                            {{ errors.first("amount") }}
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="details">Details</label>
                        <textarea
                            row="5"
                            id="details"
                            name="details"
                            class="textarea"
                            v-model="form.details"
                            :class="{ 'is-danger': errors.has('details') }"
                        ></textarea>
                        <div class="help is-danger">
                            {{ errors.first("details") }}
                        </div>
                    </div>
                    <div class="field">
                        <button
                            type="submit"
                            class="button is-link is-fullwidth"
                            :class="{ 'is-loading': isSaving }"
                            :disabled="errors.any() || isSaving"
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
            to: null,
            from: null,
            accounts: [],
            loading: true,
            isSaving: false,
            form: new this.$form({
                id: "",
                to: "",
                from: "",
                amount: "",
                details: ""
            })
        };
    },
    created() {
        this.$http
            .get("app/accounts?all=1")
            .then(res => {
                this.accounts = res.data;
                if (this.$route.params.id) {
                    this.fetchTransfer(this.$route.params.id);
                } else {
                    this.loading = false;
                }
            })
            .catch(err => this.$event.fire("appError", err.response));
    },
    methods: {
        submit() {
            this.isSaving = true;
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/transfers/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshTransfersTable");
                        this.notify(
                            "success",
                            "Transfer has been successfully updated."
                        );
                        this.$router.push("/transfers");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            } else {
                this.form
                    .post("app/transfers")
                    .then(() => {
                        this.$event.fire("refreshTransfersTable");
                        this.notify(
                            "success",
                            "Transfer has been successfully added."
                        );
                        this.$router.push("/transfers");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            }
        },
        fetchTransfer(id) {
            this.$http
                .get(`app/transfers/${id}`)
                .then(res => {
                    this.to = this.accounts.find(
                        account => account.id == res.data.to
                    );
                    this.from = this.accounts.find(
                        account => account.id == res.data.from
                    );
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
        toAccountChange(selected) {
            if (selected && selected.id == this.form.from) {
                this.notify("warn", "To and from accounts should not be same.");
                this.to = this.form.to = selected = null;
            } else {
                this.to = selected;
                this.form.to = selected ? selected.id : "";
            }
        },
        fromAccountChange(selected) {
            if (selected && selected.id == this.form.to) {
                this.notify("warn", "To and from accounts should not be same.");
                this.from = this.form.from = selected = null;
            } else {
                this.from = selected;
                this.form.from = selected ? selected.id : "";
            }
        }
    }
};
</script>

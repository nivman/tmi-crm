<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "Edit Tax" : "Add New Tax" }}
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
                            <div class="field">
                                <number-input-component
                                    :value.sync="form.rate"
                                    :validation="{
                                        rules: 'required',
                                        name: 'rate'
                                    }"
                                    :field="{
                                        label: 'Rate (%)',
                                        name: 'rate',
                                        id: 'rate'
                                    }"
                                ></number-input-component>
                                <div class="help is-danger">
                                    {{ errors.first("rate") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="number"
                                    >Tax Number</label
                                >
                                <input
                                    type="text"
                                    id="number"
                                    class="input"
                                    name="number"
                                    v-model="form.number"
                                    :class="{
                                        'is-danger': errors.has('number')
                                    }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("number") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="field">
                                <label class="label" for="details"
                                    >Details</label
                                >
                                <textarea
                                    rows="4"
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
                            <!-- <div class="field">
                                <checkbox-component v-model="form.show" :checked="form.show ? true : false" name="show" id="show" label="Show on invoice"></checkbox-component>
                            </div> -->
                            <div class="field">
                                <checkbox-component
                                    id="compound"
                                    name="compound"
                                    v-model="form.compound"
                                    :checked="form.compound"
                                    label="This is a compound tax"
                                ></checkbox-component>
                            </div>
                            <div class="field">
                                <checkbox-component
                                    id="recoverable"
                                    name="recoverable"
                                    v-model="form.recoverable"
                                    :checked="form.recoverable"
                                    label="This tax is recoverable"
                                ></checkbox-component>
                            </div>
                            <div class="field">
                                <checkbox-component
                                    id="state"
                                    name="state"
                                    v-model="form.state"
                                    :checked="form.state"
                                    label="This is state related tax"
                                ></checkbox-component>
                                <transition
                                    name="fade"
                                    mode="out-in"
                                    enter-active-class="animated faster fadeInDown"
                                    leave-active-class="animated fastest fadeOutRight"
                                >
                                    <div
                                        class="select is-fullwidth m-t-md"
                                        v-if="form.state"
                                    >
                                        <select
                                            id="same"
                                            v-model="form.same"
                                            v-validate="'required'"
                                            :class="{
                                                'is-danger': errors.has('same')
                                            }"
                                        >
                                            <option :value="true" selected
                                                >Same state tax</option
                                            >
                                            <option :value="false"
                                                >Different state tax</option
                                            >
                                        </select>
                                    </div>
                                </transition>
                            </div>
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
            loading: true,
            isSaving: false,
            form: new this.$form({
                id: "",
                code: "",
                name: "",
                rate: "",
                number: "",
                details: "",
                show: true,
                compound: false,
                recoverable: false,
                same: false
            })
        };
    },
    created() {
        if (this.$route.params.id) {
            this.fetchTax(this.$route.params.id);
        } else {
            this.loading = false;
        }
    },
    methods: {
        submit() {
            this.isSaving = true;
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/taxes/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshTaxesTable");
                        this.notify(
                            "success",
                            "Tax has been successfully updated."
                        );
                        this.$router.push("/taxes");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            } else {
                this.form
                    .post("app/taxes")
                    .then(() => {
                        this.$event.fire("refreshTaxesTable");
                        this.notify(
                            "success",
                            "Tax has been successfully added."
                        );
                        this.$router.push("/taxes");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            }
        },
        fetchTax(id) {
            this.$http
                .get(`app/taxes/${id}`)
                .then(res => {
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

<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "עריכת ספק" : "ספק חדש" }}
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
                                <label class="label" for="name">שם</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    class="input"
                                    v-model="form.name"
                                    validate="'required'"
                                    validate.persist="'required'"
                                    :class="{ 'is-danger': errors.has('name') }"
                                />
                                <div
                                    class="help is-danger"
                                    v-if="errors.has('name')"
                                    v-text="errors.first('name')"
                                ></div>
                            </div>
                            <div class="field">
                                <label class="label" for="company"
                                    >חברה</label
                                >
                                <input
                                    type="text"
                                    id="company"
                                    class="input"
                                    name="company"
                                    v-model="form.company"
                                    :class="{
                                        'is-danger': errors.has('company')
                                    }"
                                />
                                <div
                                    class="help is-danger"
                                    v-if="errors.has('company')"
                                    v-text="errors.first('company')"
                                ></div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="field">
                                <label class="label" for="email">אימייל</label>
                                <input
                                    id="email"
                                    type="text"
                                    name="email"
                                    class="input"
                                    v-model="form.email"
                                    validate="'email'"
                                    validate.persist="'required'"
                                    :class="{
                                        'is-danger': errors.has('email')
                                    }"
                                />
                                <div
                                    class="help is-danger"
                                    v-if="errors.has('email')"
                                    v-text="errors.first('email')"
                                ></div>
                            </div>
                            <div class="field">
                                <label class="label" for="phone">טלפון</label>
                                <input
                                    id="phone"
                                    type="text"
                                    name="phone"
                                    class="input"
                                    v-model="form.phone"
                                    :class="{
                                        'is-danger': errors.has('phone')
                                    }"
                                />
                                <div
                                    class="help is-danger"
                                    v-if="errors.has('phone')"
                                    v-text="errors.first('phone')"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="field" v-if="form.id ? false : true">
                        <label class="label" for="opening_balance">תקציב</label>
                        <input
                            type="number"
                            class="input"
                            id="opening_balance"
                            name="opening_balance"
                            v-model="form.opening_balance"
                            :readonly="form.id ? true : false"
                            :class="{
                                'is-danger': errors.has('opening_balance')
                            }"
                        />
                        <div class="help is-danger">
                            {{ errors.first("opening_balance") }}
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="address">כתובת</label>
                        <input
                            type="text"
                            id="address"
                            class="input"
                            name="address"
                            v-model="form.address"
                            :class="{ 'is-danger': errors.has('address') }"
                        />
                        <div
                            class="help is-danger"
                            v-if="errors.has('address')"
                            v-text="errors.first('address')"
                        ></div>
                    </div>
                    <div v-if="attributes">
                        <h5 class="cf">שדות נוספים</h5>
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
                                :disabled="errors.any() || isSaving">
                                אישור
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
            states: [],
            state: null,
            countries: [],
            loading: true,
            attributes: [],
            isSaving: false,
            country: this.$store.state.settings.ac.country,
            form: new this.$form({
                id: "",
                name: "",
                company: "",
                email: "",
                phone: "",
                opening_balance: 0,
                address: "",
                country: "",
                state: ""
            })
        };
    },
    created() {
      console.log(this.projectId)

                if (this.$route.params.id) {
                    this.fetchVendor(this.$route.params.id);
                } else {
                    this.$http
                        .get(`app/vendors/create`)
                        .then(res => {
                            this.attributes = res.data;
                            this.loading = false;
                        })
                        .catch(err =>
                            this.$event.fire("appError", err.response)
                        );

                }

    },
    methods: {
        submit() {
            this.isSaving = true;
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/vendors/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshVendorsTable");
                        this.notify(
                            "success",
                            "Vendor has been successfully updated."
                        );
                        this.$router.push("/vendors");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            } else {
                this.form
                    .post("app/vendors")
                    .then(() => {
                        this.$event.fire("refreshVendorsTable");
                        this.notify(
                            "success",
                            "Vendor has been successfully added."
                        );
                        this.$router.push("/vendors");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            }
        },
        fetchVendor(id) {
            this.$http
                .get(`app/vendors/${id}`)
                .then(res => {
                    this.attributes = res.data.attributes;
                    delete res.data.attributes;
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
        },

    }
};
</script>

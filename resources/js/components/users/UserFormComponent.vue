<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card is-medium animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "עריכה" : "הוספה" }}
                    </p>
                    <button
                        type="button"
                        class="delete"
                        @click="$router.go(-1)"
                    ></button>
                </header>
                <section class="modal-card-body is-radius-bottom">
                    <div class="field">
                        <label class="label" for="name">שם</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            class="input"
                            v-model="form.name"
                            v-validate="'required'"
                            :class="{ 'is-danger': errors.has('name') }"
                        />
                        <div
                            class="help is-danger"
                            v-if="errors.has('name')"
                            v-text="errors.first('name')"
                        ></div>
                    </div>
                    <div class="field">
                        <label class="label" for="username">שם משתמש</label>
                        <input
                            type="text"
                            id="username"
                            class="input"
                            name="username"
                            v-model="form.username"
                            v-validate="{
                                required: true,
                                regex: /^[\w\-\_\.]*$/
                            }"
                            :class="{ 'is-danger': errors.has('username') }"
                        />
                        <div
                            class="help is-danger"
                            v-if="errors.has('username')"
                            v-text="errors.first('username')"
                        ></div>
                    </div>
                    <div class="field">
                        <label class="label" for="email">אימייל</label>
                        <input
                            id="email"
                            type="text"
                            name="email"
                            class="input"
                            v-model="form.email"
                            v-validate="'required|email'"
                            :class="{ 'is-danger': errors.has('email') }"
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
                            :class="{ 'is-danger': errors.has('phone') }"
                        />
                        <div
                            class="help is-danger"
                            v-if="errors.has('phone')"
                            v-text="errors.first('phone')"
                        ></div>
                    </div>
                    <div class="field">
                        <label class="label" for="password"
                            >Password
                            <small>{{ form.id ? "(optional)" : "" }}</small>
                        </label>
                        <input
                            id="password"
                            class="input"
                            ref="password"
                            type="password"
                            name="password"
                            v-model="form.password"
                            v-validate="form.id ? '' : 'required'"
                            :class="{ 'is-danger': errors.has('password') }"
                        />
                        <div
                            class="help is-danger"
                            v-if="errors.has('password')"
                            v-text="errors.first('password')"
                        ></div>
                    </div>
                    <div class="field">
                        <label class="label" for="password_confirmation"
                            >Confirm Password</label
                        >
                        <input
                            class="input"
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            v-model="form.password_confirmation"
                            :class="{
                                'is-danger': errors.has('password_confirmation')
                            }"
                            v-validate="
                                form.id
                                    ? 'confirmed:password'
                                    : 'required|confirmed:password'
                            "
                        />
                        <div
                            class="help is-danger"
                            v-if="errors.has('password_confirmation')"
                            v-text="errors.first('password_confirmation')"
                        ></div>
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
            isSaving: false,
            form: new this.$form({
                id: "",
                name: "",
                company: "",
                email: "",
                phone: ""
            })
        };
    },
    created() {
        if (this.$route.params.id) {
            this.fetchUser(this.$route.params.id);
        }
        const dict = {
            custom: {
                username: {
                    regex:
                        "מותר רק אותיות עם מקף, קו תחתון ונקודה."
                }
            }
        };
        this.$validator.localize("en", dict);
    },
    methods: {
        submit() {
            this.isSaving = true;
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/users/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshUsersTable");
                        this.notify(
                            "success",
                            "משתמשת עודכנה"
                        );
                        this.$router.push("/users");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            } else {
                this.form
                    .post("app/users")
                    .then(() => {
                        this.$event.fire("refreshUsersTable");
                        this.notify(
                            "success",
                            "משתמשת נוצרה"
                        );
                        this.$router.push("/users");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            }
        },
        fetchUser(id) {
            this.$http
                .get(`app/users/${id}`)
                .then(res => {
                    this.form = new this.$form(res.data);
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

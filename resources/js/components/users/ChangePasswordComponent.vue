<template>
    <div class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Change Password for <strong>{{ user_name }}</strong>
                    </div>

                    <form autocomplete="off" @submit.prevent="validateForm">
                        <div class="panel-body">
                            <div class="columns is-multiline">
                                <div class="column is-half">
                                    <div class="field">
                                        <label class="label" for="current"
                                            >Current Password</label
                                        >
                                        <input
                                            id="current"
                                            class="input"
                                            name="current"
                                            type="password"
                                            v-model="form.current"
                                            v-validate="'required'"
                                            :class="{
                                                'is-danger': errors.has(
                                                    'current'
                                                )
                                            }"
                                        />
                                        <div
                                            class="help is-danger"
                                            v-if="errors.has('current')"
                                            v-text="errors.first('current')"
                                        ></div>
                                    </div>

                                    <div class="field">
                                        <label class="label" for="password"
                                            >New Password</label
                                        >
                                        <input
                                            class="input"
                                            id="password"
                                            name="password"
                                            type="password"
                                            v-model="form.password"
                                            validate="'required'"
                                            :class="{
                                                'is-danger': errors.has(
                                                    'password'
                                                )
                                            }"
                                        />
                                        <div
                                            class="help is-danger"
                                            v-if="errors.has('password')"
                                            v-text="errors.first('password')"
                                        ></div>
                                    </div>

                                    <div class="field">
                                        <label
                                            class="label"
                                            for="password_confirmation"
                                            >Confirm Password</label
                                        >
                                        <input
                                            class="input"
                                            type="password"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            v-model="form.password_confirmation"
                                            validate="
                                                'required|confirmed:password'
                                            "
                                            :class="{
                                                'is-danger': errors.has(
                                                    'password_confirmation'
                                                )
                                            }"
                                        />
                                        <div
                                            class="help is-danger"
                                            v-if="
                                                errors.has(
                                                    'password_confirmation'
                                                )
                                            "
                                            v-text="
                                                errors.first(
                                                    'password_confirmation'
                                                )
                                            "
                                        ></div>
                                    </div>

                                    <div class="field">
                                        <button
                                            type="submit"
                                            class="button is-link"
                                            :class="{ 'is-loading': isSaving }"
                                            :disabled="errors.any() || isSaving"
                                        >
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isSaving: false,
            form: new this.$form({
                current: "",
                password: "",
                password_confirmation: ""
            })
        };
    },
    computed: {
        user_name() {
            return this.$store.getters.user
                ? this.$store.getters.user.username
                : "";
        }
    },
    methods: {
        validateForm() {
            this.$validator
                .validateAll()
                .then(result => {
                    if (result) {
                        this.isSaving = true;
                        this.form
                            .post("app/users/change_password")
                            .then(() => {
                                this.$event.fire("logOut");
                                this.notify(
                                    "success",
                                    "Password has been successfully update."
                                );
                            })
                            .catch(err =>
                                this.$event.fire("appError", err.response)
                            )
                            .finally(() => (this.isSaving = false));
                    }
                })
                .catch(err => this.$event.fire("appError", err));
        }
    }
};
</script>

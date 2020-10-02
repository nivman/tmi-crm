<template>
    <modal name="login-modal" height="auto" :scrollable="true" width="300" :adaptive="true" :clickToClose="false" classes="is-rounded" transition="custom">
        <div class="login m-t-none m-b-none is-rounded">
            <div class="login-box is-rounded animated fastest bounceIn">
                <div class="box-part box is-paddingless is-borderless">
                    <div id="partition-register" class="partition">
                        <div class="partition-title">Please sign in</div>
                        <div class="partition-form">
                            <p>Please login to perform the action again.</p>
                            <form action="#" @submit.prevent="validateForm()" class="m-t-md m-b-md">
                                <div class="field">
                                    <input v-model="form.username" v-validate="'required'" name="username" type="text" placeholder="Username" class="input" :class="{'is-danger': errors.has('username') }">
                                    <div class="help is-danger" v-if="errors.has('username')" v-text="errors.first('username')"></div>
                                </div>
                                <div class="field">
                                    <input v-model="form.password" v-validate="'required'" name="password" type="password" placeholder="Password" class="input" :class="{'is-danger': errors.has('password') }">
                                    <div class="help is-danger" v-if="errors.has('password')" v-text="errors.first('password')"></div>
                                </div>
                                <div class="field">
                                    <checkbox-component v-model="form.remember" :checked="!!form.remember" name="remember" id="remember" label="Remember Me" @changed="onRememberChange"></checkbox-component>
                                </div>
                                <div class="field m-t-md">
                                    <button type="submit" class="button is-fullwidth" :class="{'is-loading': loading}" :disabled="errors.any()">Sign In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
</template>

<script>
export default {
    data() {
        return {
            form: new this.$form({ username: "", password: "", remember: false }),
            loading: false
        };
    },
    methods: {
        signIn() {
            this.form
                .post("login")
                .then(response => {
                    this.loading = false;
                    this.form.password = "";
                    this.$modal.hide("login-modal");
                    // window.user = response.data;
                    this.$store.commit("UPDATE_USER", response.data);
                    this.notify("success", "You are successfully signed in, please perform action again.");
                    if (!this.$route.meta.save) {
                        this.$router.back();
                        setTimeout(() => {
                            this.$router.forward();
                        }, 200);
                    }
                    this.$http
                        .get(`app/token`)
                        .then(res => {
                            document.head.querySelector('meta[name="csrf-token"]').setAttribute("content", res.data.token);
                            window.axios.defaults.headers.common["X-CSRF-TOKEN"] = res.data.token;
                        })
                        .catch(err => this.$event.fire("appError", err.response));
                })
                .catch(err => {
                    this.loading = false;
                    if (err.response.status == 422) {
                        let error = err.response.data.errors.username
                            ? err.response.data.errors.username[0]
                            : err.response.data.errors.password ? err.response.data.errors.password[0] : err.response.data.message;
                        this.$modal.show("dialog", {
                            title: err.response.data.message,
                            text: error,
                            buttons: [
                                {
                                    title: "OK",
                                    handler: () => {
                                        this.$modal.hide("dialog");
                                    }
                                }
                            ]
                        });
                    } else {
                        this.$event.fire("appError", err.response);
                    }
                });
        },
        validateForm() {
            this.$validator.validateAll().then(result => {
                if (result) {
                    this.loading = true;
                    this.signIn();
                }
            });
        },
        onRememberChange(v) {
            this.form.remember = !this.form.remember;
        }
    }
};
</script>

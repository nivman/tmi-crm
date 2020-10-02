<template>
    <div class="login-container">
        <div class="login m-t-md m-b-none is-rounded">
            <!-- <div class="has-text-centered m-b-sm">
                <h2>{{ site_name }}</h2>
            </div> -->
            <div
                class="login-box is-rounded has-text-centered p-md m-b-md"
                v-if="$store.getters.settings.demo"
            >
                <p class="m-b-md">
                    <strong>Welcome to live demo site.</strong><br />
                    Emails are disabled on this demo.
                </p>
                <label
                    class="button is-outlined is-white"
                    for="demouser"
                    style="color:#363636;"
                    >Login as</label
                >
                <div class="select m-r-sm">
                    <select v-model="demouser" name="demouser" id="demouser">
                        <option value="super">Super</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Sales</option>
                        <option value="customer">Customer</option>
                        <option value="vendor">Vendor</option>
                    </select>
                </div>
                <button
                    type="button"
                    class="button is-link"
                    @click="loginDemoUser()"
                >
                    Login
                </button>
            </div>
            <div class="login-box is-rounded animated faster fadeInDown">
                <div class="box-part is-paddingless is-borderless">
                    <div id="partition-register" class="partition">
                        <div class="partition-title">
                            <span
                                class="is-pulled-right tooltip"
                                v-if="!isOnline"
                            >
                                <i class="fas fa-plug has-text-danger" />
                                <span class="tooltip-text left"
                                    >You are disconnected!</span
                                >
                            </span>
                            Please sign in
                        </div>
                        <div class="partition-form">
                            <p>Please login to access your account</p>
                            <form
                                autocomplete="off"
                                action="#"
                                @submit.prevent="validateForm()"
                                class="m-t-md m-b-md"
                            >
                                <div class="field">
                                    <input
                                        type="text"
                                        class="input"
                                        name="username"
                                        placeholder="Username"
                                        v-validate="'required'"
                                        v-model="form.username"
                                        :class="{
                                            'is-danger': errors.has('username')
                                        }"
                                    />
                                    <div
                                        class="help is-danger"
                                        v-if="errors.has('username')"
                                        v-text="errors.first('username')"
                                    ></div>
                                </div>
                                <div class="field">
                                    <input
                                        class="input"
                                        name="password"
                                        type="password"
                                        placeholder="Password"
                                        v-model="form.password"
                                        v-validate="'required'"
                                        :class="{
                                            'is-danger': errors.has('password')
                                        }"
                                    />
                                    <div
                                        class="help is-danger"
                                        v-if="errors.has('password')"
                                        v-text="errors.first('password')"
                                    ></div>
                                </div>
                                <div class="field m-b-none m-t-md">
                                    <checkbox-component
                                        id="remember"
                                        name="remember"
                                        label="Remember Me"
                                        v-model="form.remember"
                                        :checked="form.remember"
                                    ></checkbox-component>
                                </div>
                                <div class="field m-t-md">
                                    <button
                                        type="submit"
                                        class="button is-fullwidth"
                                        :class="{ 'is-loading': loading }"
                                        :disabled="errors.any() || !isOnline"
                                    >
                                        Sign In
                                    </button>
                                </div>
                                <a
                                    class="m-t-md m-b-md p-l-none"
                                    href="/password/reset"
                                >
                                    Forgot Your Password?
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <div
                    class="box-part right is-radiusless is-radius-top-right is-radius-bottom-right"
                >
                    <div
                        class="bg is-radius-top-right is-radius-bottom-right"
                    ></div>
                </div>
            </div>
            <div class="has-text-centered m-t-sm">
                <i class="fas fa-copyright"></i>
                <span class="text-muted" v-html="year + ' ' + site_name"></span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: false,
            demouser: "super",
            form: new this.$form({
                username: "",
                password: "",
                remember: false
            })
        };
    },
    beforeRouteUpdate(to, from, next) {
        if (this.$store.getters.user) {
            next("/");
        } else {
            next();
        }
    },
    beforeMount() {
        this.$store.commit("UPDATE_USER", null);
        setTimeout(() => {
            if (window.innerWidth <= 1023) {
                this.$store.commit("TOGGLE_SIDEBAR", true);
            }
        }, 5);
    },
    computed: {
        year() {
            return new Date().getFullYear();
        },
        site_name() {
            return this.$store.getters.settings.app.name;
        }
    },
    methods: {
        signIn() {
            this.form
                .post("login")
                .then(response => {
                    // window.user = response.data;
                    this.$store.commit("UPDATE_USER", response.data);
                    this.loading = false;
                    this.notify(
                        "success",
                        "You are successfully signed in, please perform action again."
                    );
                    this.$router.push("/");
                    this.$http
                        .get(`app/token`)
                        .then(res => {
                            document.head
                                .querySelector('meta[name="csrf-token"]')
                                .setAttribute("content", res.data.token);
                            window.axios.defaults.headers.common[
                                "X-CSRF-TOKEN"
                            ] = res.data.token;
                        })
                        .catch(err =>
                            this.$event.fire("appError", err.response)
                        );
                })
                .catch(err => {
                    this.loading = false;
                    if (err.response) {
                        if (err.response.status == 422) {
                            let error = err.response.data.errors.username
                                ? err.response.data.errors.username[0]
                                : err.response.data.errors.password
                                ? err.response.data.errors.password[0]
                                : err.response.data.message;
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
                    } else {
                        this.notify(
                            "error",
                            "Something went wrong, please make sure you are connected then try again."
                        );
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
        loginDemoUser() {
            this.form.username = this.demouser;
            this.form.password = "123456";
            this.signIn();
        }
    }
};
</script>

<style>
h2 {
    font-size: 1.5rem;
}
</style>

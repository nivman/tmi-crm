<template>
    <div class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        הגדרות מערכת
                    </div>
                    <form
                        autocomplete="off"
                        action="#"
                        @submit.prevent="validateForm()"
                        class="is-fullwidth"
                    >
                        <div class="panel-body">
                            <div class="columns is-multiline">
                                <div class="column is-half">
                                    <div class="field">
                                        <label class="label" for="app.name">שם האפליקציה</label>
                                        <div class="control">
                                            <input
                                                type="text"
                                                id="app.name"
                                                class="input"
                                                data-vv-name="app.name"
                                                v-model="form.app.name"
                                                v-validate="'required'"
                                                :class="{
                                                    'is-danger': errors.has(
                                                        'app.name'
                                                    )
                                                }"
                                            />
                                        </div>
                                        <div class="help is-danger">
                                            {{ errors.first("app.name") }}
                                        </div>
                                    </div>

                               </div>
                            </div>
                            <div class="field m-b-sm">
                                <button
                                    type="submit"
                                    class="button is-link"
                                    :disabled="errors.any()"
                                    :class="{ 'is-loading': isSaving }">
                                    שליחה
                                </button>
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
            form: null,
            settings: {},
            isSaving: false,
            deferredPrompt: null
        };
    },
    created() {
        this.settings = JSON.parse(
            JSON.stringify(this.$store.getters.settings)
        );
        this.form = new this.$form(this.settings);
    },


    methods: {
        validateForm() {
            this.$validator
                .validateAll()
                .then(result => {
                    if (result) {
                        this.isSaving = true;
                        this.form
                            .post("app/settings")
                            .then(res => {
                                this.form = new this.$form(this.settings);

                                this.form.app.name =  res.data.app.name;
                                this.$store.commit("UPDATE_SETTINGS", res.data);
                                this.notify(
                                    "success",
                                    "הגדרות עודכנו בהצלחה"
                                );
                                this.isSaving = false;
                            })
                            .catch(err =>
                                this.$event.fire("appError", err.response)
                            );
                    } else {
                        this.notify(
                            "error",
                            "כל השדות הם שדות חובה"
                        );
                    }
                })
                .catch(err => this.$event.fire("appError", err));
        }
    }
};
</script>

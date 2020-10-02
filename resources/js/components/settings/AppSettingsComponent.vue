<template>
    <div class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Application Settings
                    </div>
                    <div class="panel-block">
                        <p class="block">
                            Please update the settings by modifying them below
                        </p>
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
                                        <label class="label" for="app.name"
                                            >App Name</label
                                        >
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
                                    <div class="field">
                                        <label class="label" for="ac.short_name"
                                            >Short App Name</label
                                        >
                                        <div class="control">
                                            <input
                                                type="text"
                                                class="input"
                                                id="ac.short_name"
                                                v-validate="'required'"
                                                v-model="form.ac.short_name"
                                                data-vv-name="ac.short_name"
                                                :class="{
                                                    'is-danger': errors.has(
                                                        'ac.short_name'
                                                    )
                                                }"
                                            />
                                        </div>
                                        <div class="help is-danger">
                                            {{ errors.first("ac.short_name") }}
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label" for="ac.dateformat"
                                            >Date Format</label
                                        >
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select
                                                    id="ac.dateformat"
                                                    v-validate="'required'"
                                                    data-vv-name="ac.dateformat"
                                                    v-model="form.ac.dateformat"
                                                    :class="{
                                                        'is-danger': errors.has(
                                                            'ac.dateformat'
                                                        )
                                                    }"
                                                >
                                                    <option value="DD/MM/YYYY"
                                                        >DD/MM/YYYY</option
                                                    >
                                                    <option value="MM/DD/YYYY"
                                                        >MM/DD/YYYY</option
                                                    >
                                                    <option value="DD-MM-YYYY"
                                                        >DD-MM-YYYY</option
                                                    >
                                                    <option value="MM-DD-YYYY"
                                                        >MM-DD-YYYY</option
                                                    >
                                                    <option value="DD.MM.YYYY"
                                                        >DD.MM.YYYY</option
                                                    >
                                                    <option value="MM.DD.YYYY"
                                                        >MM.DD.YYYY</option
                                                    >
                                                </select>
                                            </div>
                                        </div>
                                        <div class="help is-danger">
                                            {{ errors.first("ac.dateformat") }}
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label" for="country"
                                            >Default Country</label
                                        >
                                        <div class="control">
                                            <v-select
                                                name="country"
                                                input-id="country"
                                                max-height="200px"
                                                :filterable="false"
                                                :searchable="false"
                                                :options="countries"
                                                v-validate="'required'"
                                                v-model="form.ac.country"
                                                :style="{ width: '100%' }"
                                                placeholder="Select Country..."
                                                :class="{
                                                    select: true,
                                                    'is-danger': errors.has(
                                                        'country'
                                                    )
                                                }"
                                            >
                                                <template slot="no-options">
                                                    Please type to search...
                                                </template>
                                            </v-select>
                                        </div>
                                        <div class="help is-danger">
                                            {{ errors.first("country") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-half"></div>
                                <div class="column is-12">
                                    <div class="field">
                                        <label class="label" for="ac.noRows"
                                            >Initial Table Rows</label
                                        >
                                        <div class="control">
                                            <radio-component
                                                id="10"
                                                label="10"
                                                value="10"
                                                name="ac.noRows"
                                                v-model="form.ac.noRows"
                                                :checked="form.ac.noRows == 10"
                                            ></radio-component>
                                            <radio-component
                                                id="25"
                                                label="25"
                                                value="25"
                                                name="ac.noRows"
                                                v-model="form.ac.noRows"
                                                :checked="form.ac.noRows == 25"
                                            ></radio-component>
                                            <radio-component
                                                id="50"
                                                label="50"
                                                value="50"
                                                name="ac.noRows"
                                                v-model="form.ac.noRows"
                                                :checked="form.ac.noRows == 50"
                                            ></radio-component>
                                            <radio-component
                                                id="100"
                                                label="100"
                                                value="100"
                                                name="ac.noRows"
                                                v-model="form.ac.noRows"
                                                :checked="form.ac.noRows == 100"
                                            ></radio-component>
                                        </div>
                                        <div class="help is-danger">
                                            {{ errors.first("ac.noRows") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-12">
                                    <div class="field">
                                        <label
                                            class="label"
                                            for="ac.navPosition"
                                            >Main Nav Position</label
                                        >
                                        <div class="control">
                                            <radio-component
                                                id="top"
                                                label="Top"
                                                value="top"
                                                name="ac.navPosition"
                                                v-model="form.ac.navPosition"
                                                :checked="
                                                    form.ac.navPosition == 'top'
                                                "
                                            ></radio-component>
                                            <radio-component
                                                id="left"
                                                value="left"
                                                label="Left"
                                                name="ac.navPosition"
                                                v-model="form.ac.navPosition"
                                                :checked="
                                                    form.ac.navPosition ==
                                                        'left'
                                                "
                                            ></radio-component>
                                        </div>
                                        <div class="help is-danger">
                                            {{ errors.first("ac.navPosition") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-12">
                                    <div class="field">
                                        <checkbox-component
                                            id="ac.idColumn"
                                            name="ac.idColumn"
                                            label="Hide ID Column"
                                            v-model="form.ac.idColumn"
                                            :checked="!!form.ac.idColumn"
                                        ></checkbox-component>
                                        <div class="help is-danger">
                                            {{ errors.first("ac.idColumn") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-12">
                                    <div class="field">
                                        <checkbox-component
                                            id="ac.select"
                                            name="ac.select"
                                            v-model="form.ac.select"
                                            :checked="!!form.ac.select"
                                            label="Add empty option to select"
                                        ></checkbox-component>
                                        <div class="help is-danger">
                                            {{ errors.first("ac.select") }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="field m-b-sm">
                                <button
                                    type="submit"
                                    class="button is-link"
                                    :disabled="errors.any()"
                                    :class="{ 'is-loading': isSaving }"
                                >
                                    Submit
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
            countries: [],
            isSaving: false,
            deferredPrompt: null
        };
    },
    created() {
        this.settings = JSON.parse(
            JSON.stringify(this.$store.getters.settings)
        );
        this.form = new this.$form(this.settings);
        this.$http
            .get("app/countries")
            .then(countries => (this.countries = countries.data))
            .catch(err => this.$event.fire("appError", err.response));
    },
    // beforeRouteLeave(to, from, next) {
    //     this.$modal.show("dialog", {
    //         title: "Are you sure!",
    //         text: "Do you really want to leave? you have unsaved changes!",
    //         buttons: [
    //             {
    //                 title: "Yes, please leave",
    //                 class: "button is-danger is-radiusless is-radius-bottom-left",
    //                 handler: () => {
    //                     this.$modal.hide("dialog");
    //                     next();
    //                 }
    //             },
    //             { title: "No, please cancel", class: "button is-warning is-radiusless is-radius-bottom-right" }
    //         ]
    //     });
    // },
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
                                this.$store.commit("UPDATE_SETTINGS", res.data);
                                this.notify(
                                    "success",
                                    "Settings has been successfully updated."
                                );
                                this.isSaving = false;
                            })
                            .catch(err =>
                                this.$event.fire("appError", err.response)
                            );
                    } else {
                        this.notify(
                            "error",
                            "All the fields are required. Please fill the form and try again."
                        );
                    }
                })
                .catch(err => this.$event.fire("appError", err));
        }
    }
};
</script>

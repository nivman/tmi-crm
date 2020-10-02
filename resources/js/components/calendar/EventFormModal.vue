<template>
    <modal
        name="event-form-modal"
        height="auto"
        :scrollable="true"
        width="300"
        :adaptive="true"
        classes="is-rounded"
        transition="custom"
        @before-open="beforeOpen"
    >
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div
                class="modal-card is-medium animated fastest zoomIn"
                style="width: 100%;"
            >
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "Edit Event" : "Add New Event" }}
                    </p>
                    <button
                        type="button"
                        class="delete"
                        @click="$modal.hide('event-form-modal')"
                    ></button>
                </header>
                <section class="modal-card-body is-radius-bottom">
                    <div class="field">
                        <label class="label" for="title">Title</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="input"
                            v-model="form.title"
                            v-validate="'required'"
                            :class="{ 'is-danger': errors.has('title') }"
                        />
                        <div class="help is-danger">
                            {{ errors.first("title") }}
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="details">Details</label>
                        <textarea
                            rows="3"
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
                        <label class="label" for="start_date">Start Date</label>
                        <flat-pickr
                            class="input"
                            id="start_date"
                            name="start_date"
                            format="yyyy-MM-dd"
                            v-validate="'required'"
                            v-model="form.start_date"
                            :class="{ 'is-danger': errors.has('start_date') }"
                        ></flat-pickr>
                        <div class="help is-danger">
                            {{ errors.first("start_date") }}
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="end_date">End Date</label>
                        <flat-pickr
                            class="input"
                            id="end_date"
                            name="end_date"
                            format="yyyy-MM-dd"
                            v-model="form.end_date"
                            :class="{ 'is-danger': errors.has('end_date') }"
                        ></flat-pickr>
                        <div class="help is-danger">
                            {{ errors.first("end_date") }}
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="color">Color</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select
                                    v-model="form.color"
                                    id="color"
                                    :class="{
                                        'is-danger': errors.has('color')
                                    }"
                                >
                                    <option value="black">Black</option>
                                    <option value="blue">Blue</option>
                                    <option value="green">Green</option>
                                    <option value="orange">Orange</option>
                                    <option value="purple">Purple</option>
                                    <option value="red">Red</option>
                                </select>
                            </div>
                        </div>
                        <div class="help is-danger">
                            {{ errors.first("color") }}
                        </div>
                    </div>
                    <!-- <div class="field">
                        <label class="label" for="url">URL</label>
                        <input v-model="form.url" v-validate="'url:true'" type="text" name="url" id="url" class="input" :class="{'is-danger': errors.has('url') }">
                        <div class="help is-danger">{{ errors.first('url') }}</div>
                    </div> -->
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
                    <div class="field" v-if="form.id">
                        <button
                            type="button"
                            @click="deleteEvent()"
                            class="button is-danger is-fullwidth"
                            :disabled="!form.id"
                        >
                            Delete
                        </button>
                    </div>
                </section>
            </div>
        </form>
        <button
            class="modal-close is-large is-hidden-mobile"
            aria-label="close"
            @click="$modal.hide('event-form-modal')"
        ></button>
    </modal>
</template>

<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

export default {
    components: { flatPickr },
    data() {
        return {
            form: new this.$form({
                id: "",
                start_date: "",
                end_date: "",
                title: "",
                details: "",
                color: ""
            }),
            loading: false,
            isSaving: false
        };
    },
    methods: {
        beforeOpen(e) {
            if (e.params.event) {
                this.form = new this.$form(e.params.event);
            } else {
                this.form = new this.$form({
                    id: "",
                    start_date: "",
                    end_date: "",
                    title: "",
                    details: "",
                    color: ""
                });
            }
        },
        submit() {
            this.isSaving = true;
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/events/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshEvents");
                        this.notify(
                            "success",
                            "Event has been successfully updated."
                        );
                        this.$modal.hide("event-form-modal");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            } else {
                this.form
                    .post("app/events")
                    .then(() => {
                        this.$event.fire("refreshEvents");
                        this.notify(
                            "success",
                            "Event has been successfully added."
                        );
                        this.$modal.hide("event-form-modal");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            }
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
        deleteEvent() {
            this.$modal.show("dialog", {
                title: "Delete Event!",
                text:
                    "This action will have permanent effect that can't be reversed.",
                buttons: [
                    {
                        title: "Yes, please delete",
                        class:
                            "button is-danger is-radiusless is-radius-bottom-left",
                        handler: () => {
                            this.$http
                                .delete(`app/events/${this.form.id}`)
                                .then(() => {
                                    this.$event.fire("refreshEvents");
                                    this.notify(
                                        "success",
                                        "Event has been successfully deleted."
                                    );
                                    this.$modal.hide("event-form-modal");
                                    this.$modal.hide("dialog");
                                })
                                .catch(err => {
                                    this.$event.fire("appError", err.response);
                                });
                        }
                    },
                    {
                        title: "No, please cancel",
                        class:
                            "button is-warning is-radiusless is-radius-bottom-right"
                    }
                ]
            });
        }
    }
};
</script>

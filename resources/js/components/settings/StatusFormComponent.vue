<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{
                            form.id
                                ? "עריכת סטטוס"
                                : "הוספת סטטוס"
                        }}
                    </p>
                    <button
                        type="button"
                        class="delete"
                        @click="$router.go(-1)"
                    ></button>
                </header>

                <section class="modal-card-body is-radius-bottom">
                    <div class="columns">
                        <div class="column is-half">
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
                                <div class="help is-danger">
                                    {{ errors.first("name") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="type">סוג</label>
                                <div class="select is-fullwidth">
                                    <select
                                        v-model="form.entity_name"
                                        id="type"
                                        :class="{
                                            'is-danger': errors.has('type')
                                        }">
                                        <option value="App\Customer">לקוח</option>
                                        <option value="App\Task">משימה</option>
                                        <option value="App\Lead">ליד</option>
                                        </select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("type") }}
                                </div>
                            </div>
                        </div>
                      <div class="column is-half">
                        <div class="field">
                        <compact-picker :value="colors" @input="updateValue"></compact-picker>
                        </div>
                      </div>
                     </div>

                    <div class="field">
                        <button
                            type="submit"
                            class="button is-link is-fullwidth"
                            :disabled="errors.any()">
                            אישור
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

import { Compact } from 'vue-color'
export default {
    data() {
        return {
            colors:'',
            form: new this.$form({
                id: "",
                name: "",
                entity_name: "",
                color: '',
            })
        };
    },
    computed: {
        is_edit() {
            return this.form.id && this.form.id !== "";
        }
    },
    methods: {
       updateValue(value) {
         this.form.color =value.hex8;

       },
        submit() {
            if (this.form.id && this.form.id !== "") {
                this.form
                    .post(`app/statuses/edit/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshStatuesTable");
                        this.notify(
                            "success",
                            "סטטוס עודכן"
                        );
                        this.$router.push("/settings/statuses");
                    })
                    .catch(err => {
                        this.$event.fire("appError", err.response);
                    });
            } else {
              this.form
                  .post("app/statuses/create")
                  .then(() => {
                    this.$event.fire("refreshStatuesTable");
                    this.notify(
                        "success",
                        "סטטוס נוסף "
                    );
                    this.$router.push("/settings/statuses");
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
    },
  components: {
    'compact-picker': Compact,
  }
};
</script>

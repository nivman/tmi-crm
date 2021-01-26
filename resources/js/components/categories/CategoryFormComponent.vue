<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card is-medium animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{ form.id ? "Edit " : "Add New Category" }}
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
                            name="name"
                            type="text"
                            class="input"
                            v-model="form.name"
                            v-validate="'required'"/>
                        <div class="help is-danger">
                            {{ errors.first("name") }}
                        </div>
                    </div>
                  <div class="field">
                    <label class="label" for="type">סוג</label>
                    <div class="select is-fullwidth">
                      <select
                          v-model="form.entity_name"
                          id="type">
                        <option value="App\Customer">לקוח</option>
                        <option value="App\Task">משימה</option>
                        <option value="App\Lead">ליד</option>
                        <option value="App\Expenses">הוצאות</option>
                        <option value="App\UpSale">(Upselling) מכירות נוספות</option>
                      </select>
                    </div>
                    <div class="help is-danger">
                      {{ errors.first("type") }}
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
            isSaving: false,
            form: new this.$form({ id: "", name: "", entity_name: "" })
        };
    },
    created() {
        if (this.$route.params.id) {
            this.fetchCategory(this.$route.params.id);
        }
    },
    methods: {
        submit() {
            this.isSaving = true;
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/categories/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshCategoriesTable");
                        this.notify(
                            "success",
                            " category has been successfully updated."
                        );
                        this.$router.push("/categories");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            } else {
                this.form
                    .post("app/categories")
                    .then(() => {
                        this.$event.fire("refreshCategoriesTable");
                        this.notify(
                            "success",
                            " category has been successfully added."
                        );
                        this.$router.push("/categories");
                    })
                    .catch(err => this.$event.fire("appError", err.response))
                    .finally(() => (this.isSaving = false));
            }
        },
        fetchCategory(id) {
            this.$http
                .get(`app/categories/${id}`)
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

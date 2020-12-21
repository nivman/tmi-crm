<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form autocomplete="off" action="#" @submit.prevent="validateForm()">
      <div class="modal-card animated fastest zoomIn">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            {{
              form.id
                  ? "עריכת תגית "
                  : "הוספת תגית"
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
                    validate="'required'"
                    :class="{ 'is-danger': errors.has('name') }"
                />
                <div class="help is-danger">
                  {{ errors.first("name") }}
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
        color: '',
      })
    };
  },
  computed: {
    is_edit() {
      return this.form.id && this.form.id !== "";
    }
  },
  created() {
    if (this.$route.params.id) {

      this.fetchSource(this.$route.params.id)
    }
  },
  methods: {
    fetchSource(id) {
      this.$http
          .get(`app/notes-categories/${id}`)
          .then(res => {
            console.log(res)
             this.form.id = res.data.id;
             this.form.name = res.data.name;
            // this.form.entity_name = res.data.entity_name;
             this.form.color = res.data.color;
          })
          .catch(err => this.$event.fire('appError', err.response))
    },
    updateValue(value) {
      this.form.color =value.hex8;

    },
    submit() {
      if (this.form.id && this.form.id !== "") {

        this.form
            .post(`app/notes-categories/update/${this.form.id}`)
            .then(() => {
              this.$event.fire("notesCategoriesTable");
              this.notify(
                  "success",
                  "תגית עודכנה"
              );
              this.$router.push("/settings/notes-categories");
            })
            .catch(err => {
              this.$event.fire("appError", err.response);
            });
      } else {

        this.form
            .post("app/notes-categories/create")
            .then(() => {
              this.$event.fire("notesCategoriesTable");
              this.notify(
                  "success",
                  "תגית נוספה "
              );
              this.$router.push("/settings/notes-categories");
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
    hasValue(v) {
      return (
          this.form.entities.filter(e => e.entity_type == v).length > 0
      );
    }
  },
  components: {
    'compact-picker': Compact,
  }
};
</script>

<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form action="#" autocomplete="off" @submit.prevent="validateForm()">
      <div class="modal-card animated is-medium fastest zoomIn">
        <header class="modal-card-head is-radius-top">

          <p class="modal-card-title">
            {{ form.id ? "עריכת פתק" : "יצירת  פתק" }}
          </p>
          <button
              type="button"
              class="delete"
              @click="$router.go(-1)"
          ></button>
        </header>
        <section class="modal-card-body is-radius-bottom">
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="title">נושא</label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    class="input"
                    v-model="form.title"
                    v-validate="'required'"
                    :class="{ 'is-danger': errors.has('title')  }"/>
                <div class="help is-danger">
                  {{ errors.first("title") }}
                </div>
              </div>
              <div class="field">
                <label class="label" for="subject">פרטים</label>
                <textarea
                    rows="3"
                    id="subject"
                    name="subject"
                    class="textarea"
                    v-model="form.subject"
                    :class="{ 'is-danger': errors.has('subject') }">
                  </textarea>
                <div class="help is-danger">
                  {{ errors.first('subject') }}
                </div>
              </div>
              <div class="column">
                <div class="field">
                  <label class="label" for="categories">קטגוריה</label>
                  <v-select
                      label="name"
                      id="categories"
                      name="categories"
                      class="rtl-direction"
                      item-value="id"
                      item-text="name"
                      :options="categories_notes"
                      v-model="form.category_note">
                    <template v-slot:option="categories_notes">
                      <div v-html="categories_notes.name"
                           :style="{
                      background: categories_notes.color,
                      textAlign: 'center',
                      display: 'block',
                      marginLeft:'-20px',
                      marginRight:'-20px'
                    } "></div>
                    </template>
                  </v-select>
                </div>
              </div>
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <button
                  type="submit"
                  class="button is-link is-fullwidth"
                  :class="{ 'is-loading': isSaving }"
                  :disabled="errors.any() || isSaving">
                הוספה
              </button>
            </div>
          </div>
        </section>
      </div>
    </form>
  </div>
</template>

<script>
import NotesListComponent from "./NotesListComponent";

export default {

  data() {
    return {
      isSaving: false,
      categories_notes: [],
      form: new this.$form({
        id: "",
        title: "",
        subject: "",
        category_note: ''
      })
    };
  },
  created() {

    if(this.$route.params.id ){
      this.$http
          .post(`app/notes/edit/${this.$route.params.id}`)
          .then((res) => {

            let category_note = res.data.notesCategories.filter((category)=>{
                if (category.id === res.data.notes.note_category_id) {
                      return category;
                }
            })
            this.form = res.data.notes
            this.categories_notes = res.data.notesCategories
            this.form.category_note = {'id' : res.data.notes.note_category_id, 'name': category_note[0].name, 'background' : category_note[0].color}


          })
          .catch(err => this.$event.fire("appError", err.response))
    }else{
      this.$http
          .post("app/notes/create")
          .then((res) => {
            this.categories_notes = res.data
          })
          .catch(err => this.$event.fire("appError", err.response))
    }

  },
  methods: {
    submit() {
      this.isSaving = true;
      this.form
          .post("app/notes/store")
          .then((res) => {
            this.$router.push('/notes')
            this.$store.commit('UPDATE_NOTES', {'note': res.data});

            this.notify(
                "success",
                " פתק נוסף בהצלחה"
            );

          })
          .catch(err => this.$event.fire("appError", err.response))
          .finally(() => (this.isSaving = false));
    },
    validateForm() {
      this.$validator
          .validateAll()
          .then(result => {
            if (result) {
              this.submit()
            }
          })
          .catch(err => this.$event.fire('appError', err))
    },
  },
  components: {NotesListComponent}
};
</script>
<style>

</style>
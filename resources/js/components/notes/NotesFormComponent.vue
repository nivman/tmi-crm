<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form action="#" autocomplete="off" @submit.prevent="validateForm()">
      <div class="modal-card animated fastest zoomIn">
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
                <template>
                  <label class="label" for="details">פרטים</label>

                  <quill-editor
                      id="details"
                      name="details"

                      ref="myQuillEditor"
                      v-model="form.subject"
                      :options="editorOption"
                      @blur="onEditorBlur($event)"
                      @focus="onEditorFocus($event)"
                      @ready="onEditorReady($event)" />

                </template>
<!--                <label class="label" for="subject">פרטים</label>-->
<!--                <textarea-->
<!--                    rows="3"-->
<!--                    id="subject"-->
<!--                    name="subject"-->
<!--                    class="textarea"-->
<!--                    v-model="form.subject"-->
<!--                    :class="{ 'is-danger': errors.has('subject') }">-->
<!--                  </textarea>-->
<!--                <div class="help is-danger">-->
<!--                  {{ errors.first('subject') }}-->
<!--                </div>-->
              </div>
              <div class="column">
                <div class="field">
                  <label class="label" for="category_note">קטגוריה</label>
                  <v-select
                      label="name"
                      id="category_note"
                      name="category_note"
                      class="rtl-direction"
                      item-value="id"
                      item-text="name"
                      return-object
                      single-line
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
              <div class="column">
                <div class="field">
                  <label class="label" for="project">פרוייקט</label>
                  <v-select
                      label="name"
                      id="project"
                      name="project"
                      class="rtl-direction"
                      item-value="id"
                      item-text="name"
                      return-object
                      single-line
                      :options="projects"
                      v-model="form.project">

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
import NotesListComponent from './NotesListComponent';

import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

import {quillEditor} from 'vue-quill-editor'
export default {

  data() {
    return {
      isSaving: false,
      categories_notes: [],
      projects: [],
      form: new this.$form({
        id: '',
        title: '',
        subject:'',
        category_note:'',
        project: ''
      }),
      editorOption: {
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'script': 'sub' }, { 'script': 'super' }],
            [{ 'indent': '-1' }, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'font': [] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            ['clean'],
            ['link', 'image', 'video']
          ],
          syntax: {
            highlight: text => hljs.highlightAuto(text).value
          }
        }
      }
    };
  },
  computed: {
    editor() {
      return this.$refs.myQuillEditor.quill
    }
  },
  created() {

    if(this.$route.params.id ){
      this.$http
          .post(`app/notes/edit/${this.$route.params.id}`)
          .then((res) => {

            let project_note = res.data.projects.filter((project)=>{
                if (project.id === res.data.notes.project_id) {
                      return project;
                }
            })

            let category_note = res.data.notesCategories.filter((category)=>{
              if (category.id === res.data.notes.note_category_id) {
                return category;
              }
            })

            this.form.id = res.data.notes.id;
            this.form.title = res.data.notes.title;
            this.form.subject = res.data.notes.subject;
            this.form.category_note = category_note;
            this.form.project = project_note;
            this.categories_notes = res.data.notesCategories;
            this.projects = res.data.projects;
          })
          .catch(err => this.$event.fire("appError", err.response))
    }else{
      this.$http
          .post("app/notes/create")
          .then((res) => {
            this.categories_notes = res.data.notesCategories;
            this.projects = res.data.projects;
            console.log(res.data)
          })
          .catch(err => this.$event.fire("appError", err.response))
    }

  },
  methods: {
    submit() {
      this.isSaving = true;

      if(this.form.id) {
        this.form
            .post(`app/notes/update/${this.form.id}`)
            .then((res) => {
              this.$router.push({path: '/notes', params: { state :'update'}})
              this.$store.commit('UPDATE_NOTES', {'note': res.data});

              this.notify(
                  "success",
                  " פתק נוסף בהצלחה"
              );

            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      }else {
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
      }

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
    onEditorBlur(quill) {
  //    console.log('editor blur!', quill)
    },
    onEditorFocus(quill) {
   //   console.log('editor focus!', quill)
    },
    onEditorReady(quill) {
  //    console.log('editor ready!', quill)
    },
    onEditorChange({ quill, html, text }) {
   //   console.log('editor change!', quill, html, text)
      this.content = html
    },
  },
  components: {NotesListComponent, quillEditor}
};
</script>
<style>
.ql-toolbar{
  padding: 2px !important;
}
.ql-formats{
  margin: 1px !important;
}
.ql-snow .ql-picker:not(.ql-color-picker):not(.ql-icon-picker) svg {

  right: unset;

}
</style>
<template>

  <div class="wrapper" ref="notesTable">
    <div v-if="$store.state.note">
      {{ updateNotesList($store.state.note) }}
    </div>

    <div class="panel panel-default">
      <div class="panel-heading panel-heading-notes">
        <div class="inner-panel-heading">
          פתקים
          <router-link to="/notes/add" class="button is-link is-small is-pulled-right">
            <i class="fas fa-plus m-l-sm"/> הוספת פתק
          </router-link>
        </div>

        <div class="columns">
          <input class="input column" ref="myBtn" id="search" v-model="searchTerm" type="text">
          <multiselect
              selectLabel=""
              deselectLabel=""
              class="column multiselect-column rtl-direction"
              v-model="searchNotesCategories"
              :options="notesCategories"
              :multiple="true"
              :option-height="104"
              :searchable="true"
              :allow-empty="true"
              :close-on-select="true"
              placeholder="בחירת תגיות"
              label="name"
              track-by="name">
          </multiselect>
          <v-select
              label="name"
              id="project"
              name="project"
               placeholder="פרוייקטים"
              class="rtl-direction column projects-column"
              item-value="id"
              item-text="name"
              return-object
              single-line
              :options="projects"
              v-model="searchProjects">

          </v-select>
        </div>
      </div>
      <div class="mail-view">
        <transition-group
            class="panel-content"
            v-on:before-enter="beforeEnter"
            v-on:enter="enter"
            v-on:leave="leave"
            v-bind:css="false">
          <div
              :class="{noMargin: index % 4 ===0}"
              class="panel-content outer-item"
              v-for="(note, index)  in notes" :key="note.id">


            <div class="card" style="width: 100%; overflow: initial;">
              <header class="card-header">
                <div class="card-header-title has-tooltip-right"
                     @click="changeTitle(note.id)"
                     :data-tooltip="clearContent(note.title)">
                  <p class="control has-tooltip-right" :class="{ active: note.id === showEditTitle}">
                    {{ note.title| truncate(35, '...',  note.title) }} </p>
                  <input

                      type="text"
                      name="title"
                      class="input"
                      @keyup="editTitle(note.title,note.id)"
                      v-model="note.title"
                      :class="{ active: note.id !== showEditTitle}">
                </div>
                <a class="card-header-icon" aria-label="more options">
                  <span class="icon">
                <p class="control tooltip">
                  <router-link :to="'/notes/edit/' + note.id" class="is-medium">
                    <i style="color: rgba(10, 10, 10, 0.2)" class="far fa-square m-l-sm"></i>
                    <span class="tooltip-text">עריכה</span>
                  </router-link>
                </p>
                  </span>
                  <span class="icon">
                    <i class="delete" @click="deleteNote(index, note.id)" aria-hidden="true"></i>
                  </span>

                </a>
              </header>
              <div class="card-content">
                <span class="label">{{note.project ? " פרויקט: "+note.project.name : ''}}</span>
                <div class="content details-content">
                    <label class="label" for="details">פרטים</label>
                    <template>
                      <div class="scroll-container">
                      <quill-editor
                          id="details"
                          name="details"

                          ref="myQuillEditor"
                          v-model="note.subject"
                          :options="editorOption"
                          @change="editSubject(note.subject, note.id)"
                          @blur="onEditorBlur($event)"
                          @focus="onEditorFocus($event)"
                          @ready="onEditorReady($event)" />
                      </div>
                    </template>

                </div>
              </div>
              <footer class="card-footer footer-note">

                <div
                    v-model="backgroundCategory"
                    @click="changeNotesCategories(note.id)"
                    class="footer-note card-footer columns"
                    :style="{background: note.note_category  ? note.note_category.color : ''} ">

                  <time class="column column-footer-note">
                    <date-format-component :dateTime="note.created_at"></date-format-component>
                  </time>
                  <div class="vl column"></div>
                  <div :class="{ active: note.id === showCategoriesSubject}"
                       class="has-text-centered column column-footer-note">
                    {{ note.note_category ? note.note_category.name : '' }}
                  </div>
                </div>
                <v-select
                    label="name"
                    name="notes_categories"
                    class="rtl-direction column-footer-note select-footer-note"
                    item-value="id"
                    item-text="name"
                    :style="{background: note_category ? note_category.color : ''}"
                    return-object
                    single-line
                    placeholder="--------"
                    :class="{ active: note.id !== showCategoriesSubject}"
                    :options="notesCategories"
                    v-model="note_category">
                  <template v-slot:option="notesCategories">
                    <div v-html="notesCategories.name"
                         :style="{
                      background: notesCategories.color,
                      textAlign: 'center',
                      display: 'block',
                      marginLeft:'-20px',
                      marginRight:'-20px'
                    } "></div>
                  </template>
                </v-select>
              </footer>
            </div>
          </div>
        </transition-group>
      </div>
      <div v-infinite-scroll="infiniteHandler" infinite-scroll-disabled="busy" infinite-scroll-distance="10">
        זהו. זה מה שיש
      </div>

    </div>
    <router-view></router-view>
  </div>

</template>

<script>

import Velocity from "velocity-animate";
import '@creativebulma/bulma-tooltip/dist/bulma-tooltip.min.css'
import DateFormatComponent from "../helpers/DateFormatComponent";
import tBus from '../../mixins/Tbus'
import NotesFormComponent from "./NotesFormComponent";
import infiniteScroll from 'vue-infinite-scroll'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

import {quillEditor} from 'vue-quill-editor'
export default {
  props: ['params'],
  mixins: [tBus('app/notes')],
  data() {
    return {
      counter: 0,
      showEditTitle: null,
      showEditSubject: null,
      showCategoriesSubject: null,
      notesCategories: [],
      project: '',
      projects: [],
      searchProjects: '',
      projectId: '',
      searchNotesCategories: '',
      searchTerm: '',
      notes: [],
      page: 1,
      update: false,
      busy: false,
      note: false,
      note_category: '',
      backgroundCategory: [],
      noteId: '',
      categoryIds: [],
      editorOption: {
        scrollingContainer: '.scroll-container',
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['clean'],
            ['link', 'image', 'video'],
            [{ 'direction': 'rtl' }],
          ],

          syntax: {
            highlight: text => hljs.highlightAuto(text).value
          }
        },
        theme: 'snow',
      }
    };
  },
  computed: {
    editor() {
      return this.$refs.myQuillEditor.quill
    }
  },
  created() {
    this.changeNotesCategories(-1);
  },
  watch: {
    'note': function () {
      this.page = this.page + 1;
      this.busy = false;
      this.notes.unshift(this.note.note);
    },
    'searchTerm': function () {
      let em = this
      em.page = 1;
      this.busy = false
      em.notes = []
      setTimeout(function () {
        em.infiniteHandler()
        em.busy = true
      }, 500)

    },
    'searchNotesCategories': function () {
      let categoryIds = []
      this.searchNotesCategories.filter(function (index, name) {
        categoryIds.push(index.id)
      });

      this.categoryIds = categoryIds;
      let em = this
      em.page = 1;
      this.busy = false
      em.notes = []
      setTimeout(function () {
        em.infiniteHandler()
        em.busy = true
      }, 1500)
    },
    'searchProjects': function () {

      this.projectId = this.searchProjects ?this.searchProjects.id : '';
      let em = this
      em.page = 1;
      this.busy = false
      em.notes = []
      setTimeout(function () {
        em.infiniteHandler()
        em.busy = true
      }, 1500)
    },
    'note_category': function (note) {

      if (!note) {
        this.updateColor(-1, this.noteId)
        return
      }
      let notes = this.notes.filter(note => {
        return note.id === this.noteId
      })
      let index = this.notes.indexOf(notes[0])


      this.notes[index].note_category = {
        color: note.color,
        id: note.id,
        name: note.name,
      }
      this.updateColor(note.id, this.noteId)
    }
  },
  methods: {
    updateNotesList() {

      let em = this
      let hasNote = this.notes.some(function (note) {
        return note.id == em.$store.getters.note.note.id
      })

      if (hasNote === false) {
        this.note = this.$store.getters.note
      } else {
        this.updateNote(this.$store.getters.note)
      }

    },
    updateNote(updateNote) {

      let em = this;

      this.notes.some(function (note) {

        if (note.id == updateNote.note.id) {

          let index = em.notes.indexOf(note)
          em.notes[index].title = updateNote.note.title
          em.notes[index].subject = updateNote.note.subject
          let hasCategory = em.notesCategories.some(function (category) {

            if (category.id == updateNote.note.note_category_id) {
              em.notes[index].note_category = category
              return true;
            }
          })
          if (!hasCategory) {
            em.notes[index].note_category = ''
          }

        }
      })
    },
    changeTitle(noteId) {
      this.showEditTitle = noteId;
    },

    changeNotesCategories(noteId) {
      this.noteId = noteId;
      this.$http.post(`/app/notes/extra-data`)
          .then(response => {

            this.notesCategories = response.data.notesCategories;
            this.projects = response.data.projects;
          });
      this.showCategoriesSubject = noteId;
    },
    infiniteHandler() {

      this.busy = true
      this.$http.get(`/app/notes?page=${this.page}&query=${this.searchTerm}&categoryId=${this.categoryIds}&projectId=${this.projectId}`)
          .then(response => {

            if (response.data.data.length > 0) {

              return response.data;
            }
          }).then(data => {

        if (data) {
          $.each(data.data, (key, value) => {
            this.notes.push(value);
          });
          this.busy = false;
          this.page = this.page + 1;
        }
      });

    },
    deleteNote(notes, id) {
      let em = this;

      this.$modal.show('dialog', {
        title: 'מחיקת פתק',
        text:
            '',
        buttons: [
          {
            title: 'מחיקה',
            class:
                'button is-danger is-radiusless is-radius-bottom-left',
            handler: () => {

              em.notes.splice(notes, 1)
              this.$http
                  .delete(`app/notes/${id}`)
                  .then(() => {
                    this.$event.fire('refreshEventsListTable')
                    this.notify(
                        'success',
                        'פתק נמחק'
                    )
                    this.$modal.hide('event-form-modal')
                    this.$modal.hide('dialog')
                  })
                  .catch(err => {
                    this.$event.fire('appError3', err.response)
                  })
            }
          },
          {
            title: 'לא',
            class:
                'button is-warning is-radiusless is-radius-bottom-right'
          }
        ]
      })
    },
    beforeEnter: function (el) {
      el.style.opacity = 0
      el.style.width = 0
      el.style.height = 0;
    },
    enter: function (el, done) {
      Velocity(el, {opacity: 1, width: "24%", height: 300}, {duration: "fast"})
    },
    leave: function (el, done) {
      Velocity(el, {opacity: 0, width: 0, height: 0, display: 'none'}, {duration: "slow"})
    },
    displayExtraFields() {
      this.showExtraFields = !this.showExtraFields;
    },
    clearContent(details) {
      if (details) {
        let removeEmailAddr = details.replace(/([^.@\s]+)(\.[^.@\s]+)*@([^.@\s]+\.)+([^.@\s]+)/, '')
        let removeEmptyLine = removeEmailAddr.replaceAll(/\r?\n|\r/g, '')
        let removeBiggerLessSign = removeEmptyLine.replaceAll(/<|>/g, '')
        let breakLineOnDot = removeBiggerLessSign.replaceAll(/\./g, '\n')
        return breakLineOnDot.replaceAll(/\[.+\n.+/gi, '')

      }
      return ''
    },
    updateColor(input, id) {
      this.$http.post(`/app/notes/color?id=${id}&color-id=${input}`)
          .then(response => {
          })
    },
    editTitle(input, id) {

      this.$http.post(`/app/notes/edit-title?id=${id}&input=${input}`)
          .then(response => {
          })
    },

    editSubject(input, id) {
      this.$http.post(`/app/notes/edit-subject?id=${id}&input=${input}`)
          .then(response => {
          })
    },
    onEditorBlur(quill) {

    },
    onEditorFocus(quill) {

    },
    onEditorReady(quill) {

    },
    onEditorChange({ quill, html, text }) {

      this.content = html
    },
  },
  components: {DateFormatComponent, NotesFormComponent, infiniteScroll, Multiselect, quillEditor}
}
</script>
<style>
.outer-item {
  display: inline-flex;
  flex-wrap: wrap;;
  background: white;
  width: 24%;
  margin: 0.5%;
}

.inner-item {

  height: 200px
}

.details-content {
  /*overflow: auto;*/
  height: 150px;
}

.active {
  display: none;
}

.noMargin {
  margin-right: 0px;
}

.footer-note {
  width: 100% !important;
  padding: 0px !important;
  margin: 0px !important;
}

.vl {
  border-left: 1px solid gray;
  height: 100%;
  position: absolute;
  left: 50%;
  margin-left: -3px;
  top: 0;
}

.column-footer-note {
  padding: 0px !important;
}

.select-footer-note {
  width: 100%;
  height: 100%;
}

.column-footer-note > div {
  height: 99% !important;
}

.multiselect-column {
  padding: 0px;
  text-align: right;
}
.projects-column{
  padding: 0px;
  text-align: right;
}

.multiselect__option {
  text-align: right;
}

.option__desc {
  border-radius: 5px;
  padding: 10px;
  position: relative;
}

.inner-panel-heading {
  height: 50px;
}

.panel-heading-notes {
  margin-left: 1%;
}
.ql-editor {
  /*text-align: right;*/
}
.scroll-container {
  overflow: auto;

  height: 150px;
}
</style>
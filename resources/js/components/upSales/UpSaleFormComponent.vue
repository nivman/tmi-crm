<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form autocomplete="off" action="#" @submit.prevent="validateForm()">
      <div class="modal-card is-medium animated fastest zoomIn">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            {{ form.id ? "עריכה " : "הוספת מכירה" }}
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
                v-model="form.title"
                v-validate="'required'"/>
            <div class="help is-danger">
              {{ errors.first("title") }}
            </div>
          </div>
          <div class="field">

            <label class="label" for="amount">סכום</label>
            <input
                id="amount"
                name="name"
                type="text"
                class="input"
                v-model="form.amount"
            />
            <div class="help is-danger">
              {{ errors.first("title") }}
            </div>

          </div>

          <div class="field">
            <label class="label" for="category">קטגוריה</label>
            <div class="control">
              <div class="control">
                <v-select
                    label="name"
                    id="category"
                    name="category"
                    item-value="id"
                    item-text="name"
                    v-model="form.category"
                    input-id="project"
                    :options="categories"
                    @input="categoryChange"
                    class="expenses-dropdown"
                    :style="{ width: '100%' }"
                    placeholder="בחירת קטגוריה">
                  <template slot="no-options">
                    :-( לא מצאתי קטגוריה
                  </template>
                </v-select>
              </div>
<!--              <div-->
<!--                  class="select is-fullwidth"-->
<!--                  :class="{'is-danger': errors.has('category') }">-->
<!--                <select-->
<!--                    id="category"-->
<!--                    name="category"-->
<!--                    @change="categoryChange"-->
<!--                    v-model="form.category"-->
<!--                    v-validate="'required'"-->
<!--                    placeholder="בחירת קטגוריה">-->
<!--                  <option-->
<!--                      v-if="$store.state.settings.ac.select"-->
<!--                      value=""-->
<!--                  >בחירת קטגוריה-->
<!--                  </option>-->
<!--                  <option-->
<!--                      v-for="category in categories"-->
<!--                      :key="category.id"-->
<!--                      :value="category.id">-->
<!--                    {{ category.name }}-->
<!--                  </option>-->

<!--                </select>-->
<!--              </div>-->
            </div>
            <div class="help is-danger">
              {{ errors.first("category") }}
            </div>
          </div>
          <div class="field">
            <label class="label" for="project">פרוייקט</label>
            <div class="control">
              <v-select
                  label="name"
                  id="project"
                  name="project"
                  item-value="id"
                  item-text="name"
                  v-model="form.project"
                  input-id="project"
                  :options="projects"
                  @input="projectChange"
                  class="expenses-dropdown"
                  :style="{ width: '100%' }"
                  placeholder="בחירת פרוייקט">
                <template slot="no-options">
                  :-( לא מצאתי פרוייקט
                </template>
              </v-select>
            </div>
            <div class="help is-danger">
              {{ errors.first("project") }}
            </div>
          </div>
          <div class="field">
            <button
                type="submit"
                class="button is-link is-fullwidth"
                :class="{ 'is-loading': isSaving }"
                :disabled="errors.any() || isSaving"
            >
              שמירה
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
  props:['projectId', 'projectName'],
  data() {
    return {
      categories: [],
      projects: [],
      isSaving: false,
      form: new this.$form(
          {
            id: "",
            title: "",
            amount: "",
            category_id: '',
            category: '',

          })
    };
  },
  created() {

    this.$http
        .get("app/categories?type=App\\UpSale")
        .then(res => {
          console.log(res.data)
          this.categories = res.data;

          if (this.$route.params.id && !this.projectId) {
            this.fetchUpSale(this.$route.params.id);
          }else {

                this.form.project = this.projectName;
            this.form.project_id = this.projectId;
            console.log(this.projectName)
            this.getProject(this.projectId);
          }

        })
        .catch(err => {
          this.$event.fire("appError", err.response);
        });
    this.$http
        .get("app/projects?all=1")
        .then(res => {

          this.projects = res.data.data;
        })
        .catch(err => {
          this.$event.fire("appError", err.response);
        });
  },
  methods: {
    submit() {
      this.isSaving = true;
      if (this.form.id && this.form.id !== "") {
        this.form
            .put(`app/upsales/${this.form.id}`)
            .then(() => {
              this.$event.fire("refreshUpsalesTable");
              this.notify(
                  "success",
                  "מכירה עודכנה"
              );
              this.$router.push("/upsales");
            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      } else {
        this.form
            .post("app/upsales")
            .then(() => {
              this.$event.fire("refreshUpsalesTable");
              this.notify(
                  "success",
                  "מכירה נוספה"
              );
              this.$router.push("/upsales");
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
    categoryChange() {
      this.form.category_id = this.form.category.id;
    },
    projectChange(selected) {
      this.project = selected;
      this.form.project_id = selected ? selected.id : "";
    },
    getProject(projectId) {

    },
    fetchUpSale(id) {
      this.$http
          .get(`app/upsales/${id}`)
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

<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <form autocomplete="off" action="#" @submit.prevent="validateForm()">
      <div class="modal-card animated fastest zoomIn">
        <header class="modal-card-head is-radius-top">
          <p class="modal-card-title">
            {{ form.id ? "עריכת הוצאה" : "הוספת הוצאה" }}
          </p>
          <button
              type="button"
              class="delete"
              @click="$router.go(-1)"
          ></button>
        </header>
        <section class="modal-card-body is-radius-bottom">
          <loading v-if="loading"></loading>
          <!-- <div class="field">
          <label class="label" for="date">Date</label>
          <div class="control">
              <flat-pickr v-model="form.date" format="yyyy-MM-dd" v-validate="'required'" name="date" id="date" class="input" :class="{'is-danger': errors.has('date') }"></flat-pickr>
          </div>
          <div class="help is-danger">{{ errors.first('date') }}</div>
      </div> -->
          <div class="columns m-b-none">
            <div class="column is-half">
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

              <number-input-component
                  :value.sync="form.amount"
                  :validation="{rules: 'required', name: 'amount'}"
                  :field="{label: 'Amount', name: 'amount', id: 'amount'}">
              </number-input-component>
              <div class="help is-danger">
                {{ errors.first("amount") }}
              </div>
            </div>
            <div class="column is-half">
              <div class="field">
                <label class="label" for="category">קטגוריה</label>
                <div class="control">
                  <div
                      class="select is-fullwidth"
                      :class="{'is-danger': errors.has('category') }">
                    <select
                        id="category"
                        name="category"
                        v-model="form.category"
                        v-validate="'required'"
                        placeholder="בחירת קטגוריה">
                      <option
                          v-if="$store.state.settings.ac.select"
                          value=""
                          disabled>בחירת קטגוריה
                      </option>
                      <option
                          v-for="category in categories"
                          :key="category.id"
                          :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>
                </div>
                <div class="help is-danger">
                  {{ errors.first("category") }}
                </div>
              </div>
              <div class="field">
                <label class="label" for="account">חשבון</label>
                <div class="control">
                  <v-select
                      label="name"
                      name="account"
                      id="account"
                      v-model="account"
                      input-id="account"
                      :options="accounts"
                      class="expenses-dropdown"
                      @input="accountChange"
                      v-validate="'required'"
                      :style="{ width: '100%' }"
                      placeholder="בחירת חשבון"
                      :class="{select: true,'is-danger': errors.has('account')}">
                    <template slot="no-options">
                      :-( לא מצאתי חשבון
                    </template>
                  </v-select>
                </div>
                <div class="help is-danger">
                  {{ errors.first("account") }}
                </div>
              </div>
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="project">פרוייקט</label>
                <div class="control">
                  <v-select
                      label="name"
                      name="project"
                      id="project"
                      v-model="project"
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
            </div>
            <div class="column">
              <div class="field">
                <label class="label" for="details">פרטים</label>
                <textarea
                    rows="2"
                    id="details"
                    name="details"
                    class="textarea"
                    v-model="form.details"
                    :class="{'is-danger': errors.has('details')}">

                </textarea>
                <div class="help is-danger">
                  {{ errors.first("details") }}
                </div>
              </div>


            </div>
          </div>
          <div v-if="attributes">
            <h5 class="cf">שדות דינמיים</h5>
            <div class="columns is-multiline">
              <div
                  class="column is-half"
                  v-for="attr in attributes"
                  :key="attr.slug">
                <custom-field-component
                    :attr="attr"
                    v-model="form[attr.slug]">
                </custom-field-component>
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
                עריכה
              </button>
            </div>
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
import _debounce from "lodash/debounce";

export default {
  data() {
    return {
      accounts: [],
      projects: [],
      account: null,
      project: null,
      loading: true,
      categories: [],
      attributes: [],
      isSaving: false,
      form: new this.$form({
        id: "",
        date: new Date(),
        title: "",
        reference: "",
        amount: "",
        category: "",
        account_id: "",
        details: "",
        project_id: ""
      })
    };
  },
  created() {
    this.$http
        .get("app/accounts?all=1")
        .then(res => {
          this.accounts = res.data;
          this.$http
              .get("app/categories?all=1")
              .then(res => {

                this.categories = res.data;
                if (
                    !this.$store.state.settings.ac.select &&
                    this.categories.length > 0
                ) {
                  this.form.category = this.categories[0].id;
                }
                if (this.$route.params.id) {
                  this.fetchExpense(this.$route.params.id);
                } else {
                  this.$http
                      .get(`app/expenses/create`)
                      .then(res => {
                        this.attributes = res.data;
                        this.loading = false;
                      })
                      .catch(err =>
                          this.$event.fire("appError", err.response)
                      );
                }
              })
              .catch(err => {
                this.$event.fire("appError", err.response);
              });
        })
        .catch(err => {
          this.$event.fire("appError", err.response);
        });
    this.$http
        .get("app/projects?all=1")
        .then(res => {

          this.projects = res.data.data;

          if (
              !this.$store.state.settings.ac.select &&
              this.projects.length > 0
          ) {
          //  this.form.project = this.projects[0].id;
          }
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
            .put(`app/expenses/${this.form.id}`)
            .then(() => {
              this.$event.fire("refreshExpensesTable");
              this.notify(
                  "success",
                  "Expense has been successfully updated."
              );
              this.$router.push("/expenses");
            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      } else {
        this.form
            .post("app/expenses")
            .then(() => {
              this.$event.fire("refreshExpensesTable");
              this.notify(
                  "success",
                  "Expense has been successfully added."
              );
              this.$router.push("/expenses");
            })
            .catch(err => this.$event.fire("appError", err.response))
            .finally(() => (this.isSaving = false));
      }
    },
    fetchExpense(id) {
      this.$http
          .get(`app/expenses/${id}`)
          .then(res => {
            this.attributes = res.data.attributes;
            res.data.category = res.data.categories[0].id;
            this.project = res.data.project ?res.data.project.name : '';
            delete res.data.attributes;
            delete res.data.categories;
            delete res.data.projects;
            this.form = new this.$form(res.data);
            this.account = this.accounts.find(
                account => account.id == res.data.account_id
            );
            this.loading = false;
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
    },
    projectChange(selected) {
      this.project = selected;
      this.form.project_id = selected ? selected.id : "";
    },
    accountChange(selected) {
      this.account = selected;
      this.form.account_id = selected ? selected.id : "";
    },
    getAccount(id) {
      this.$http
          .get(`app/accounts/${id}`)
          .then(res => {
            this.accountChange(res.data);
          })
          .catch(err => {
            this.$event.fire("appError", err.response);
          });
    }
  }
};
</script>
<style scoped>
.vs2__combobox{
  direction: ltr;
}
</style>
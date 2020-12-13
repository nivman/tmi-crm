<template>
  <div class="wrapper">
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">הגדרת חשבוניות</div>

          <form autocomplete="off" action="#" @submit.prevent="validateForm()" class="is-fullwidth">
            <div class="panel-body">
              <loading v-if="loading"></loading>
              <div class="columns">
                <div class="column is-half">
                  <div class="columns is-multiline">
                    <div class="column is-half">
                      <div class="field">
                        <label class="label" for="name">שם החברה</label>
                        <div class="control">
                          <input
                            id="name"
                            name="name"
                            type="text"
                            class="input"
                            v-model="form.name"
                            v-validate="'required'"
                            :class="{
                              'is-danger': errors.has('name'),
                            }"
                          />
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('name') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-half">
                      <div class="field">
                        <label class="label" for="number">מספר חברה</label>
                        <div class="control">
                          <input
                            type="text"
                            id="number"
                            name="number"
                            class="input"
                            v-model="form.number"
                            :class="{
                              'is-danger': errors.has('number'),
                            }"
                          />
                        </div>
                      </div>
                    </div>
                    <div class="column is-half">
                      <div class="field">
                        <label class="label" for="phone">מספר טלפון</label>
                        <div class="control">
                          <input
                            id="phone"
                            type="text"
                            name="phone"
                            class="input"
                            v-model="form.phone"
                            v-validate="'required'"
                            :class="{
                              'is-danger': errors.has('phone'),
                            }"
                          />
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('phone') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-half">
                      <div class="field">
                        <label class="label" for="email">כתבות מייל</label>
                        <div class="control">
                          <input
                            id="email"
                            name="email"
                            type="text"
                            class="input"
                            v-model="form.email"
                            v-validate="'required'"
                            :class="{
                              'is-danger': errors.has('email'),
                            }"
                          />
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('email') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-12">
                      <div class="field">
                        <label class="label" for="address">כתובת</label>
                        <div class="control">
                          <input
                            type="text"
                            id="address"
                            class="input"
                            name="address"
                            v-model="form.address"
                            v-validate="'required'"
                            :class="{
                              'is-danger': errors.has('address'),
                            }"
                          />
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('address') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-12">
                      <div class="field">
                        <label class="label" for="footer">טקסט בתחתית החשבונית</label>
                        <div class="control">
                          <textarea rows="3" id="footer" name="footer" class="textarea" v-model="form.footer"></textarea>
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('footer') }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="column is-half">
                  <div class="field">
                    <label class="label" for="email">לוגו</label>
                    <div class="control">
                      <div class="file has-name is-fullwidth">
                        <label class="file-label">
                          <input ref="logo" name="logo" type="file" accept="image/*" class="file-input" @change="displayName" />
                          <span class="file-cta">
                            <span class="file-icon">
                              <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">
                              Choose a file…
                            </span>
                          </span>
                          <span class="file-name" ref="fileUploadName"></span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="field p-t-md">
                    <label class="label" for="template">טמפלט</label>
                    <div class="control">
                      <div class="select is-fullwidth">
                        <select
                          id="template"
                          v-model="form.template"
                          v-validate="'required'"
                          :class="{
                            'is-danger': errors.has('template'),
                          }"
                        >
                          <option value="minimal">מצומצם</option>
                          <option value="simple">פשוט</option>
                          <option value="modern">מודרני</option>
                        </select>
                      </div>
                    </div>
                    <div class="help is-danger">
                      {{ errors.first('template') }}
                    </div>
                  </div>
                  <div class="columns is-centered m-t-lg p-x-md m-b-xs">
                    <div class="card is-rounded">
                      <div class="card-image is-rounded">
                        <figure class="image is-rounded">
                          <img
                            class="is-rounded"
                            alt="Invoice Template"
                            style="max-width:600px;"
                            :src="'/images/' + form.template + '.png'"
                          />
                        </figure>
                      </div>
                    </div>
                  </div>
                  <div class="field m-t-md">
                    <checkbox-component
                      id="show_image"
                      name="show_image"
                      v-model="form.show_image"
                      label="Show Product Photo"
                      :checked="!!form.show_image"
                    ></checkbox-component>

                    <checkbox-component
                      id="show_tax"
                      name="show_tax"
                      label="Show Tax Column"
                      v-model="form.show_tax"
                      :checked="!!form.show_tax"
                    ></checkbox-component>

                    <checkbox-component
                      id="show_discount"
                      name="show_discount"
                      label="Show Discount Column"
                      v-model="form.show_discount"
                      :checked="!!form.show_discount"
                    ></checkbox-component>
                  </div>
                </div>
              </div>
              <div class="field">
                <button type="submit" class="button is-link" :disabled="errors.any()" :class="{ 'is-loading': isSaving }">
                  אישור
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
      extra: {},
      loading: true,
      isSaving: false,
      form: new this.$form({
        template: 'simple',
        show_tax: true,
        show_image: true,
        show_discount: false,
      }),
    };
  },
  created() {
        this.$http
          .get('app/companies/1')
          .then(res => {
            this.fetchCompany(res);
          })
          .catch(err => this.$event.fire('appError', err.response));

  },
  methods: {
    fetchCompany(res) {
      delete res.data.logo;
      if (res.data.extra && res.data.extra != 'null' && res.data.extra != '') {
        this.extra = typeof res.data.extra === 'string' ? JSON.parse(res.data.extra) : res.data.extra;
      }
      res.data.show_tax = res.data.show_tax ? 1 : 0;
      res.data.show_image = res.data.show_image ? 1 : 0;
      res.data.show_discount = res.data.show_discount ? 1 : 0;
      this.form = new this.$form(res.data);
      this.loading = false;
      this.isSaving = false;
    },
    displayName(event) {
      this.form['logo'] = true;
      this.form.appendData('logo', this.$refs.logo.files[0]);
      this.$refs.fileUploadName.innerText = event.srcElement.files[0] ? event.srcElement.files[0].name : '';
    },
    validateForm() {
      this.$validator
        .validateAll()
        .then(result => {
          if (result) {
            this.isSaving = true;
            this.form['extra'] = JSON.stringify(this.extra);
            this.form
              .put('app/companies/1', this.form.logo)
              .then(res => {
                this.fetchCompany(res);
                this.notify('success', 'הגדרות חשבונית עודכנו');
              })
              .catch(err => {
                this.isSaving = false;
                this.$event.fire('appError', err.response);
              });
          } else {
            this.notify('error', 'כל השדות הם שדות חובה למעט שם החברה');
          }
        })
        .catch(err => this.$event.fire('appError', err));
    },
  },
};
</script>

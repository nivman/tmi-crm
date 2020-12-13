<template>
  <div class="wrapper">
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">הגדרות אמייל</div>
          <div class="panel-block">
          </div>
          <div class="panel panel-default is-shadowless">
            <div class="panel-heading">הגדרות מייל</div>
            <div class="panel-body is-shadowless p-t-md">
              <div class="columns is-multiline">
                <div class="column is-one-quarter">
                  <div class="field">
                    <label class="label" for="mainMailAddress">כתובת המייל של העסק</label>
                    <div class="control">
                      <input
                          type="text"
                          class="input"
                          name="from_name"
                          id="mainMailAddress"
                          v-validate="'required'"
                          v-model="form.mainMailAddress"/>
                    </div>
                    <div class="help is-danger">
                      {{ errors.first('from_name') }}
                    </div>
                  </div>
                </div>
                <div class="column is-one-quarter">
                  <div class="field">
                    <label class="label" for="mailFromName">שם המוען</label>
                    <div class="control">
                      <input
                          type="text"
                          class="input"
                          name="from_name"
                          id="mailFromName"
                          validate="'required'"
                          v-model="form.mailFromName"
                          :class="{
                              'is-danger': errors.has('from_name'),
                            }"
                      />
                    </div>
                    <div class="help is-danger">
                      {{ errors.first('from_name') }}
                    </div>
                  </div>
                </div>
                <div class="column is-one-quarter">
                  <div class="field">
                    <label class="label" for="imapUserName"> כתובת מייל אליה יופנו המיילים</label>
                    <div class="control">
                      <input
                          type="email"
                          class="input"
                          name="from_address"
                          id="imapUserName"
                          validate="'required|email'"
                          v-model="form.imapUserName"
                          :class="{
                              'is-danger': errors.has('from_address'),
                            }"
                      />
                    </div>
                    <div class="help is-danger">
                      {{ errors.first('from_address') }}
                    </div>
                  </div>
                </div>
                <div class="column is-one-quarter">
                  <div class="field">
                    <label class="label" for="mailDriver">סוג פרוטוקול</label>
                    <div class="control">
                      <v-select
                          id="mailDriver"

                          :options="drivers"
                          name="mailDriver"
                          input-id="mailDriver"
                          v-validate="'required'"
                          v-model="form.mailDriver"
                          :style="{width: '100%',}">

                      </v-select>
                    </div>
                    <div class="help is-danger">
                      {{ errors.first('MAIL_MAILER') }}
                    </div>
                  </div>
                </div>
              </div>
              <transition
                  mode="out-in"
                  name="fade"
                  enter-active-class="animated faster fadeInDown"
                  leave-active-class="animated fastest fadeOutLeft"
                  appear>
              </transition>
              <transition
                  appear
                  name="fade"
                  mode="out-in"
                  enter-active-class="animated faster fadeInDown"
                  leave-active-class="animated fastest fadeOutLeft"
              >

              </transition>
              <transition
                  mode="out-in"
                  name="fade"
                  enter-active-class="animated faster fadeInDown"
                  leave-active-class="animated fastest fadeOutLeft"
                  appear
              >
                <div  class="columns is-multiline">
                  <div class="column is-one-quarter">
                    <div class="field">
                      <label class="label" for="mailHost">(לשליחת מיילים) Mail Host</label>
                      <div class="control">
                        <input
                            type="text"
                            class="input"
                            id="mailHost"
                            name="mailHost"
                            v-validate="'required'"
                            v-model="form.mailHost"
                            :class="{
                                'is-danger': errors.has('MAIL_HOST'),
                              }"
                        />
                      </div>
                      <div class="help is-danger">
                        {{ errors.first('MAIL_HOST') }}
                      </div>
                    </div>
                  </div>
                  <div class="column is-one-quarter">
                    <div class="field">
                      <label class="label" for="mailPort">(לשליחת מיילים) Mail Port</label>
                      <div class="control">
                        <input
                            type="text"
                            class="input"
                            id="mailPort"
                            name="mailPort"
                            v-validate="'required'"
                            v-model="form.mailPort"
                            :class="{
                                'is-danger': errors.has('mailPort'),
                              }"
                        />
                      </div>
                      <div class="help is-danger">
                        {{ errors.first('MAIL_PORT') }}
                      </div>
                    </div>
                  </div>
                  <div class="column is-one-quarter">
                    <div class="field">
                      <label class="label" for="mailUserName">כתובת מייל ממנה ישלחו מיילים</label>
                      <div class="control">
                        <input
                            type="text"
                            class="input"
                            id="mailUserName"
                            name="mailUserName"
                            v-validate="'required'"
                            v-model="form.mailUserName"
                            :class="{
                                'is-danger': errors.has('MAIL_USERNAME'),
                              }"
                        />
                      </div>
                      <div class="help is-danger">
                        {{ errors.first('MAIL_USERNAME') }}
                      </div>
                    </div>
                  </div>
                  <div class="column is-one-quarter">
                    <div class="field">
                      <label class="label" for="mailPassword">סיסימה למייל</label>
                      <div class="control">
                        <input
                            type="password"
                            class="input"
                            id="mailPassword"
                            name="mailPassword"
                            v-model="form.mailPassword"
                            :class="{
                                'is-danger': errors.has('mailPassword'),
                              }"
                        />
                      </div>
                      <div class="help is-danger">
                        {{ errors.first('mailPassword') }}
                      </div>
                    </div>
                  </div>
                  <div class="column is-one-quarter">
                    <div class="field">
                      <label class="label" for="mailEncryption">Mail Encryption</label>
                      <div class="control">
                        <input type="text" class="input" id="mailEncryption" name="mailEncryption" v-model="form.mailEncryption" />
                      </div>
                    </div>
                  </div>
                </div>
              </transition>
              <div class="field">
                <button type="submit" class="button is-info" @click="submit">
                  עידכון
                </button>
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      accounts: [],
      form: new this.$form({
        id: "",
        mainMailAddress: "",
        mailFromName: "",
        imapUserName: "",
        mailHost: "",
        mailPort: "",
        mailUserName: "",
        mailPassword: "",
        mailEncryption: ""
      }),
      drivers: [
        {label: 'SMTP', value: 'smtp'},
      ],
    };
  },
  methods: {
    submit() {
      this.form
          .post(`app/email/settings/${this.form.id}`)
          .then(() => {

            this.notify(
                "success",
                "סטטוס עודכן"
            );
            this.$router.push("/settings/email_settings");
          })
          .catch(err => {
            this.$event.fire("appError", err.response);
          });
    },
  }
}
</script>

<style scoped>

</style>
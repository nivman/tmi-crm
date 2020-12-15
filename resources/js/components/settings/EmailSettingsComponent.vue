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
                          v-model="form.main_mail_address"/>
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
                          v-model="form.mail_from_name"
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
                          v-model="form.imap_user_name"
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
                          v-model="form.mail_driver"
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
                <div class="columns is-multiline">
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
                            v-model="form.mail_host"
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
                            v-model="form.mail_port"
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
                            v-model="form.mail_user_name"
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
                      <label class="label" for="mailPassword">סיסמה למייל</label>
                      <div class="control">
                        <input
                            type="password"
                            class="input"
                            id="mailPassword"
                            name="mailPassword"
                            v-model="form.mail_password"
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
                        <input type="text" class="input" id="mailEncryption" name="mailEncryption"
                               v-model="form.mail_encryption"/>
                      </div>
                    </div>
                  </div>
                  <div class="column is-one-quarter">
                    <div class="field">
                      <label class="label" for="mailEncryption">imap_port</label>
                      <div class="control">
                        <input type="text" class="input" id="imapPort" name="mailEncryption"
                               v-model="form.imap_port"/>
                      </div>
                    </div>
                  </div>
                </div>
              </transition>
              <div class="columns">
                <div class="column">
                  <div class="column" v-for="(action, index) in tasks" :key="index">
                    <div style="display: flex">
                      <input type="text" class="input" v-model="action.task">
                      <div class="input delete-action" >
                        <button @click="removeTaskRow(index)" class="delete"></button>
                      </div>
                    </div>
                    <div class="control">
                      <label class="label">משימה</label>
                    </div>
                  </div>
                  <div class="control column">
                    <button @click="addTaskRow" class="button is-success"> יצירת משימה</button>
                  </div>
                </div>
                <div class="column">
                  <div class="column" v-for="(action, index) in events" :key="index">
                    <div style="display: flex">
                      <input type="text" class="input" v-model="action.event">
                      <div class="input delete-action">
                        <button @click="removeEventRow(index)" class="delete"></button>
                      </div>
                    </div>
                    <div class="control">
                      <label class="label">התקשרות</label>
                    </div>
                  </div>
                  <div class="control column">
                  <button @click="addEventRow" class="button is-success">יצירת התקשרות</button>
                  </div>
                </div>
                <div class="column">
                  <div class="column" v-for="(action, index) in leads" :key="index">
                    <div style="display: flex">
                      <input type="text" class="input" v-model="action.lead">
                      <div class="input delete-action">
                        <button @click="removeLeadRow(index)" class="delete"></button>
                      </div>

                    </div>
                    <div class="control">
                      <label class="label">ליד</label>
                    </div>
                  </div>
                  <div class="control column">
                  <button @click="addLeadRow" class="button is-success">יצירת ליד</button>
                  </div>
                </div>

              </div>


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
      tasks: [],
      events: [],
      leads: [],
      form: new this.$form({
        id: "",
        main_mail_address: "",
        mail_from_name: "",
        imap_user_name: "",
        mail_host: "",
        mail_port: "",
        mail_user_name: "",
        mail_password: "",
        mail_encryption: "",
        mail_driver: "",
        imap_port: ""
      }),
      drivers: [
        {label: 'SMTP', value: 'smtp'},
      ],
    };
  },
  created() {

    this.$http
        .post(`app/email/settings`)
        .then(res => {

          if (res.data) {
            this.form.id = res.data[0].id;
            this.form.main_mail_address = res.data[0].main_mail_address;
            this.form.imap_user_name = res.data[0].imap_user_name;
            this.form.mail_from_name = res.data[0].mail_from_name;
            this.form.mail_host = res.data[0].mail_host;
            this.form.mail_port = res.data[0].mail_port;
            this.form.mail_user_name = res.data[0].mail_user_name;
            this.form.mail_encryption = res.data[0].mail_encryption;
            this.form.mail_driver = res.data[0].mail_driver;
            this.form.imap_port = res.data[0].imap_port;
            if (res.data[0].task_title) {
              JSON.parse(res.data[0].task_title).forEach((task) => {
                this.tasks.push({task: task})
              })
            }
            if (res.data[0].event_title) {
              JSON.parse(res.data[0].event_title).forEach((event) => {
                this.events.push({event: event})
              })
            }
            if (res.data[0].lead_title) {
              JSON.parse(res.data[0].lead_title).forEach((lead) => {
                this.leads.push({lead: lead})
              })
            }
          }
        })
        .catch(err => this.$event.fire("appError", err.response));
  },
  methods: {
    addTaskRow() {
      this.tasks.push({task: null});
    },
    addEventRow() {
      this.events.push({event: null});
    },
    addLeadRow() {
      this.leads.push({lead: null});
    },
    removeTaskRow(index) {

      this.tasks.splice(index);
    },
    removeEventRow(index) {
      this.events.splice(index);
    },
    removeLeadRow(index) {
      this.leads.splice(index);
    },
    submit() {
      this.form.tasks = this.tasks;
      this.form.events = this.events;
      this.form.leads = this.leads;
      if (this.form.id) {
        this.form
            .post(`app/email/settings/update/${this.form.id}`)
            .then((res) => {
              this.form = res.data
              this.form.mail_password = ''
              this.form = new this.$form(this.form);
              this.notify(
                  "success",
                  "סטטוס עודכן"
              );
              //this.$router.push("/settings/email_settings");
            })
            .catch(err => {
              this.$event.fire("appError", err.response);
            });
      } else {
        this.form
            .post(`app/email/settings/store/${this.form.id}`)
            .then(() => {

              this.notify(
                  "success",
                  "סטטוס עודכן"
              );
              //this.$router.push("/settings/email_settings");
            })
            .catch(err => {
              this.$event.fire("appError", err.response);
            });
      }

    },
  }
}
</script>

<style scoped>
 .delete-action {
   width: 0px; border: none
 }
</style>
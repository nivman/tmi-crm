<template>
  <div class="wrapper">
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">הגדרות מערכת</div>
          <div class="panel-block">
          </div>
          <form autocomplete="off" action="#" @submit.prevent="validateForm()" class="is-fullwidth">
            <div class="panel-body">
              <loading v-if="loading"></loading>
              <div class="panel panel-default is-shadowless">
                <div class="panel-heading">
                  הגדרות כלליות
                </div>
                <div class="panel-body is-shadowless p-t-md">
                  <div class="columns is-multiline">
                    <div class="column is-one-quarter">
                      <div class="field">
                        <label class="label" for="APP_NAME">שם מערכת</label>
                        <div class="control">
                          <input
                              type="text"
                              class="input"
                              id="APP_NAME"
                              name="APP_NAME"
                              v-model="form.APP_NAME"
                              v-validate="'required'"
                              :class="{
                              'is-danger': errors.has('APP_NAME'),
                            }"
                          />
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('APP_NAME') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-one-quarter">
                      <div class="field">
                        <label class="label" for="APP_URL">כתובת</label>
                        <div class="control">
                          <input
                              type="text"
                              id="APP_URL"
                              class="input"
                              name="APP_URL"
                              v-model="form.APP_URL"
                              v-validate="'required'"
                              :class="{
                              'is-danger': errors.has('APP_URL'),
                            }"
                          />
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('APP_URL') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-one-quarter">
                      <div class="field">
                        <label class="label" for="TIMEZONE">אזור זמן</label>
                        <div class="control">
                          <input
                              type="text"
                              class="input"
                              id="TIMEZONE"
                              name="TIMEZONE"
                              v-validate="'required'"
                              v-model="form.TIMEZONE"
                              :class="{
                              'is-danger': errors.has('TIMEZONE'),
                            }"
                          />
                        </div>
                        <div class="help">
                          List of available timezones:
                          <a href="https://www.php.net/manual/en/timezones.php" target="_blank">
                            https://www.php.net/manual/en/timezones.php
                          </a>
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('TIMEZONE') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-one-quarter">
                      <div class="field">
                        <label class="label" for="DEFAULT_ACCOUNT">חשבון ברירת מחדל</label>
                        <div class="control">
                          <v-select
                              label="name"
                              id="DEFAULT_ACCOUNT"
                              v-model="account"
                              :options="accounts"
                              name="DEFAULT_ACCOUNT"
                              v-validate="'required'"
                              @input="accountChange"
                              :style="{
                              width: '100%',
                            }"
                              input-id="DEFAULT_ACCOUNT"
                              placeholder="Select Account..."
                              :class="{ select: true,'is-danger': errors.has('DEFAULT_ACCOUNT'),}">
                          </v-select>
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('DEFAULT_ACCOUNT') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-12">
                      <div class="field">
                        <checkbox-component
                            id="STOCK"
                            name="STOCK"
                            v-model="form.STOCK"
                            :checked="!!form.STOCK"
                            label="אפשר מלאי / כמות מוצרים">

                        </checkbox-component>
                        <div class="help is-danger">
                          {{ errors.first('STOCK') }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel panel-default is-shadowless">
                <div class="panel-heading">הגדרות מייל</div>
                <div class="panel-body is-shadowless p-t-md">
                  <div class="columns is-multiline">
                    <div class="column is-one-quarter">
                      <div class="field">
                        <label class="label" for="MAIN_MAIL_ADDRESS">כתובת המייל של העסק</label>
                        <div class="control">
                          <input
                              type="text"
                              class="input"
                              name="from_name"
                              id="MAIN_MAIL_ADDRESS"
                              v-validate="'required'"
                              v-model="form.MAIN_MAIL_ADDRESS"/>
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('from_name') }}
                        </div>
                      </div>
                    </div>
                    <div class="column is-one-quarter">
                      <div class="field">
                        <label class="label" for="MAIL_FROM_NAME">שם המוען</label>
                        <div class="control">
                          <input
                              type="text"
                              class="input"
                              name="from_name"
                              id="MAIL_FROM_NAME"
                              v-validate="'required'"
                              v-model="form.MAIL_FROM_NAME"
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
                        <label class="label" for="IMAP_USERNAME"> כתובת מייל אליה יופנו המיילים</label>
                        <div class="control">
                          <input
                              type="email"
                              class="input"
                              name="from_address"
                              id="IMAP_USERNAME"
                              v-validate="'required|email'"
                              v-model="form.IMAP_USERNAME"
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
                        <label class="label" for="MAIL_MAILER">סוג פרוטוקול</label>
                        <div class="control">
                          <v-select
                              id="MAIL_MAILER"
                              v-model="driver"
                              :options="drivers"
                              name="MAIL_MAILER"
                              input-id="MAIL_MAILER"
                              v-validate="'required'"
                              @input="driverChange"
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
                    <div v-if="form.MAIL_MAILER == 'smtp'" class="columns is-multiline">
                      <div class="column is-one-quarter">
                        <div class="field">
                          <label class="label" for="MAIL_HOST">(לשליחת מיילים) Mail Host</label>
                          <div class="control">
                            <input
                                type="text"
                                class="input"
                                id="MAIL_HOST"
                                name="MAIL_HOST"
                                v-validate="'required'"
                                v-model="form.MAIL_HOST"
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
                          <label class="label" for="MAIL_PORT">(לשליחת מיילים) Mail Port</label>
                          <div class="control">
                            <input
                                type="text"
                                class="input"
                                id="MAIL_PORT"
                                name="MAIL_PORT"
                                v-validate="'required'"
                                v-model="form.MAIL_PORT"
                                :class="{
                                'is-danger': errors.has('MAIL_PORT'),
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
                          <label class="label" for="MAIL_USERNAME">כתובת מייל ממנה ישלחו מיילים</label>
                          <div class="control">
                            <input
                                type="text"
                                class="input"
                                id="MAIL_USERNAME"
                                name="MAIL_USERNAME"
                                v-validate="'required'"
                                v-model="form.MAIL_USERNAME"
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
                          <label class="label" for="MAIL_PASSWORD">סיסימה למייל</label>
                          <div class="control">
                            <input
                                type="password"
                                class="input"
                                id="MAIL_PASSWORD"
                                name="MAIL_PASSWORD"
                                v-model="form.MAIL_PASSWORD"
                                :class="{
                                'is-danger': errors.has('MAIL_PASSWORD'),
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('MAIL_PASSWORD') }}
                          </div>
                        </div>
                      </div>
                      <div class="column is-one-quarter">
                        <div class="field">
                          <label class="label" for="MAIL_ENCRYPTION">Mail Encryption</label>
                          <div class="control">
                            <input type="text" class="input" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION"
                                   v-model="form.MAIL_ENCRYPTION"/>
                          </div>
                        </div>
                      </div>
                    </div>
                  </transition>
                </div>
              </div>
              <div class="panel panel-default is-shadowless">
                <div class="panel-heading">
                  הגדרות תשלומים
                </div>
                <div class="panel-body is-shadowless p-t-md">
                  <div class="columns is-multiline">
                    <div class="column is-half">
                      <div class="field">
                        <label class="label" for="CURRENCY_CODE">Currency Code</label>
                        <div class="control">
                          <input
                              type="text"
                              class="input"
                              id="CURRENCY_CODE"
                              name="CURRENCY_CODE"
                              v-validate="'length:3'"
                              v-model="form.CURRENCY_CODE"
                              :class="{
                              'is-danger': errors.has('CURRENCY_CODE'),
                            }"
                          />
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('CURRENCY_CODE') }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="columns is-multiline">
                    <div class="column is-12">
                      <div class="field">
                        <checkbox-component
                            id="PAYPAL_ENABLED"
                            name="PAYPAL_ENABLED"
                            label="Enable PayPal Gateway"
                            v-model="form.PAYPAL_ENABLED"
                            :checked="form.PAYPAL_ENABLED"
                        ></checkbox-component>
                      </div>
                      <transition
                          mode="out-in"
                          name="fade"
                          enter-active-class="animated faster fadeInDown"
                          leave-active-class="animated fastest fadeOutRight"
                          appear
                      >
                        <div v-if="paypal" class="columns is-multiline">
                          <div class="column is-4">
                            <div class="field">
                              <label class="label" for="PAYPAL_USERNAME">PayPal API Username</label>
                              <div class="control">
                                <input
                                    type="text"
                                    class="input"
                                    id="PAYPAL_USERNAME"
                                    name="PAYPAL_USERNAME"
                                    v-model="form.PAYPAL_USERNAME"
                                    v-validate="{
                                    required: form.AUTHORISE_ENABLED,
                                  }"
                                    :class="{
                                    'is-danger': errors.has('PAYPAL_USERNAME'),
                                  }"
                                />
                              </div>
                              <div class="help is-danger">
                                {{ errors.first('PAYPAL_USERNAME') }}
                              </div>
                            </div>
                          </div>
                          <div class="column is-3">
                            <div class="field">
                              <label class="label" for="PAYPAL_PASSWORD">PayPal API Password</label>
                              <div class="control">
                                <input
                                    type="text"
                                    class="input"
                                    id="PAYPAL_PASSWORD"
                                    name="PAYPAL_PASSWORD"
                                    v-model="form.PAYPAL_PASSWORD"
                                    v-validate="{
                                    required: form.AUTHORISE_ENABLED,
                                  }"
                                    :class="{
                                    'is-danger': errors.has('PAYPAL_PASSWORD'),
                                  }"
                                />
                              </div>
                              <div class="help is-danger">
                                {{ errors.first('PAYPAL_PASSWORD') }}
                              </div>
                            </div>
                          </div>
                          <div class="column is-5">
                            <div class="field">
                              <label class="label" for="PAYPAL_SIGNATURE">PayPal API Signature</label>
                              <div class="control">
                                <input
                                    type="text"
                                    class="input"
                                    id="PAYPAL_SIGNATURE"
                                    name="PAYPAL_SIGNATURE"
                                    v-model="form.PAYPAL_SIGNATURE"
                                    v-validate="{
                                    required: form.AUTHORISE_ENABLED,
                                  }"
                                    :class="{
                                    'is-danger': errors.has('PAYPAL_SIGNATURE'),
                                  }"
                                />
                              </div>
                              <div class="help is-danger">
                                {{ errors.first('PAYPAL_SIGNATURE') }}
                              </div>
                            </div>
                          </div>
                        </div>
                      </transition>
                    </div>
                  </div>

                  <div class="columns is-multiline">
                    <div class="column is-half">
                      <div class="field">
                        <label class="label" for="CARD_GATEWAY">Accept credit/debit card payment with</label>
                        <div class="control">
                          <v-select
                              id="CARD_GATEWAY"
                              v-model="payment"
                              :options="payments"
                              name="CARD_GATEWAY"
                              input-id="CARD_GATEWAY"
                              @input="paymentChange"
                              :style="{
                              width: '100%',
                            }"
                              placeholder="Select payment gateway..."
                              :class="{
                              select: true,
                              'is-danger': errors.has('CARD_GATEWAY'),
                            }"
                          >
                            <template slot="no-options">
                              Please type to search...
                            </template>
                          </v-select>
                        </div>
                        <div class="help is-danger">
                          {{ errors.first('CARD_GATEWAY') }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <transition
                      mode="out-in"
                      name="fade"
                      enter-active-class="animated faster fadeInDown"
                      leave-active-class="animated fastest fadeOutRight"
                      appear
                  >
                    <div v-if="form.CARD_GATEWAY == 'Stripe'" class="columns is-multiline">
                      <div class="column is-half p-t-none">
                        <div class="field">
                          <label class="label" for="STRIPE_KEY">Stripe key</label>
                          <div class="control">
                            <input
                                type="text"
                                class="input"
                                id="STRIPE_KEY"
                                name="STRIPE_KEY"
                                v-model="form.STRIPE_KEY"
                                :class="{
                                'is-danger': errors.has('STRIPE_KEY'),
                              }"
                                v-validate="{
                                required: form.CARD_GATEWAY == 'Stripe',
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('STRIPE_KEY') }}
                          </div>
                        </div>
                      </div>
                      <div class="column is-half p-t-none">
                        <div class="field">
                          <label class="label" for="STRIPE_SECRET">Stripe Secret</label>
                          <div class="control">
                            <input
                                type="text"
                                class="input"
                                id="STRIPE_SECRET"
                                name="STRIPE_SECRET"
                                v-model="form.STRIPE_SECRET"
                                :class="{
                                'is-danger': errors.has('STRIPE_SECRET'),
                              }"
                                v-validate="{
                                required: form.CARD_GATEWAY == 'Stripe',
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('STRIPE_SECRET') }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </transition>
                  <transition
                      mode="out-in"
                      name="fade"
                      enter-active-class="animated faster fadeInDown"
                      leave-active-class="animated fastest fadeOutRight"
                      appear
                  >
                    <div v-if="form.CARD_GATEWAY == 'PayPal_Rest'" class="columns is-multiline">
                      <div class="column is-half p-t-none">
                        <div class="field">
                          <label class="label" for="PAYPAL_CLIENT_ID">PayPal Client Id</label>
                          <div class="control">
                            <input
                                type="text"
                                class="input"
                                id="PAYPAL_CLIENT_ID"
                                name="PAYPAL_CLIENT_ID"
                                v-model="form.PAYPAL_CLIENT_ID"
                                :class="{
                                'is-danger': errors.has('PAYPAL_CLIENT_ID'),
                              }"
                                v-validate="{
                                required: form.CARD_GATEWAY == 'PayPal_Rest',
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('PAYPAL_CLIENT_ID') }}
                          </div>
                        </div>
                      </div>
                      <div class="column is-half p-t-none">
                        <div class="field">
                          <label class="label" for="PAYPAL_SECRET">PayPal Secret</label>
                          <div class="control">
                            <input
                                type="text"
                                class="input"
                                id="PAYPAL_SECRET"
                                name="PAYPAL_SECRET"
                                v-model="form.PAYPAL_SECRET"
                                :class="{
                                'is-danger': errors.has('PAYPAL_SECRET'),
                              }"
                                v-validate="{
                                required: form.CARD_GATEWAY == 'PayPal_Rest',
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('PAYPAL_SECRET') }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </transition>

                  <transition
                      mode="out-in"
                      name="fade"
                      enter-active-class="animated faster fadeInDown"
                      leave-active-class="animated fastest fadeOutRight"
                      appear
                  >
                    <div v-if="form.CARD_GATEWAY == 'AuthorizeNetApi_Api'" class="columns is-multiline">
                      <div class="column is-half p-t-none">
                        <div class="field">
                          <label class="label" for="AUTHORIZE_LOGIN">Authorize Login</label>
                          <div class="control">
                            <input
                                type="text"
                                class="input"
                                id="AUTHORIZE_LOGIN"
                                name="AUTHORIZE_LOGIN"
                                v-model="form.AUTHORIZE_LOGIN"
                                :class="{
                                'is-danger': errors.has('AUTHORIZE_LOGIN'),
                              }"
                                v-validate="{
                                required: form.CARD_GATEWAY == 'AuthorizeNetApi_Api',
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('AUTHORIZE_LOGIN') }}
                          </div>
                        </div>
                      </div>
                      <div class="column is-half p-t-none">
                        <div class="field">
                          <label class="label" for="AUTHORIZE_TRANSACTION_KEY">Authorize Transaction Key</label>
                          <div class="control">
                            <input
                                type="text"
                                class="input"
                                id="AUTHORIZE_TRANSACTION_KEY"
                                name="AUTHORIZE_TRANSACTION_KEY"
                                v-model="form.AUTHORIZE_TRANSACTION_KEY"
                                :class="{
                                'is-danger': errors.has('AUTHORIZE_TRANSACTION_KEY'),
                              }"
                                v-validate="{
                                required: form.CARD_GATEWAY == 'AuthorizeNetApi_Api',
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('AUTHORIZE_TRANSACTION_KEY') }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </transition>
                </div>
              </div>
              <div class="field">
                <button type="submit" class="button is-link" :disabled="errors.any()"
                        :class="{ 'is-loading': isSaving }">
                  עידכון
                </button>
              </div>
            </div>
          </form>
          <form autocomplete="off" action="#"   class="is-fullwidth">
          <div class="panel panel-default is-shadowless">
            <div class="panel-heading">
              הגדרות אישיות
            </div>
            <div class="panel-body is-shadowless p-t-md">


              <div class="columns is-multiline">
                <div class="column is-12">
                  <div class="column is-one-quarter">
                    <div class="field">
                      <label class="label" for="hourlyPrice">מחיר שעתי</label>
                      <div class="control">
                        <input
                            type="text"
                            class="input"
                            name="hourlyPrice"
                            id="hourlyPrice"

                            v-model="hourlyPrice"
                            :class="{
                              'is-danger': errors.has('hourlyPrice'),
                            }"
                        />
                      </div>
                      <div class="help is-danger">
                        {{ errors.first('hourlyPrice') }}
                      </div>
                    </div>
                    <div class="field">
                      <button @click="saveAccountSettings" type="button" class="button is-link" :class="{ 'is-loading': isSaving }">
                        עידכון הגדרות אישיות
                      </button>
                    </div>
                  </div>
                </div>
              </div>
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
      accounts: [],
      driver: null,
      account: null,
      payment: null,
      loading: true,
      isSaving: false,
      form: new this.$form(),
      accountsSettingsForm: new this.$form(),
      dateformat: 'DD\/MM\/YYYY',
      booleans: ['PAYPAL_ENABLED', 'STOCK'],
      payments: [
        {label: 'Disable', value: ''},
        {label: 'Stripe', value: 'Stripe'},
        {label: 'PayPal Pro', value: 'PayPal_Pro'},
        {label: 'PayPal Rest', value: 'PayPal_Rest'},
        {label: 'Authorize.net', value: 'AuthorizeNetApi_Api'},
      ],
      drivers: [
        {label: 'SMTP', value: 'smtp'},
      ],
      hourlyPrice: ''
    };
  },
  beforeMount() {
    this.$http
        .get('app/accounts?all=1')
        .then(account => {
          this.accounts = account.data;
          this.$http
              .get('app/settings/system')
              .then(res => {
                console.log(res)
                this.settingsToForm(res.data.systemSettings);
                this.setAccountSettings(res.data.accountsSettings)
                this.loading = false;
              })
              .catch(err => this.$event.fire('appError', err.response));
        })
        .catch(err => this.$event.fire('appError', err.response));
  },
  computed: {
    paypal: function () {
      return this.form.PAYPAL_ENABLED || this.form.CARD_GATEWAY == 'PayPal_Pro';
    },
  },
  methods: {
    settingsToForm(settings) {
      let form = {};
      for (let setting of settings) {

        if (this.booleans.includes(setting.key)) {
          form[setting.key] = setting.value ? true : false;
        } else {
          form[setting.key] = setting.value;
        }
        if (setting.key == 'MAIL_MAILER') {
          this.driver = this.drivers.find(d => d.value == setting.value);
        } else if
        (setting.key == 'DEFAULT_ACCOUNT') {
          this.account = this.accounts.find(a => a.id == setting.value);
        } else if (setting.key == 'CARD_GATEWAY') {
          this.payment = this.payments.find(p => p.value == setting.value);
        }
      }
      this.form = new this.$form(form);
      //  this.form.dateformat = 'DD\/MM\/YYYY'
      // this.form.DEFAULT_ACCOUNT = this.account.id;
    },
    setAccountSettings(accountsSettings) {
      this.hourlyPrice = accountsSettings.price;
    },
    validateForm() {
      this.$validator
          .validateAll()
          .then(result => {

            if (result) {
              this.isSaving = true;
              this.form
                  .post('app/settings/system/update')
                  .then(res => {
                    this.form = new this.$form(res.data);
                    this.$store.commit('SET_STOCK', this.form.STOCK);
                    this.notify('success', 'הגדרות המערכת עודכנו בהצלחה');
                    this.isSaving = false;
                  })
                  .catch(err => {
                    this.$event.fire('appError', err.response);
                  });
            } else {
              this.notify(
                  'error',
                  'כל השדות נדרשים למעט הצפנת דואר ושער תשלום. אנא מלא את הטופס ונסה שוב.'
              );
            }
          })
          .catch(err => {
            this.$event.fire('appError', err);
          });
    },
    saveAccountSettings() {

      this.$http
          .post('app/settings/system/saveAccountSettings', {'price' : this.hourlyPrice})
          .then(res => {
            if (res.data === 'success') {
              this.notify('success', 'הגדרות חשבון עודכנו בהצלחה');
            }
          })
          .catch(err => {
            this.$event.fire('appError', err.response);
          });
    },
    accountChange(selected) {
      this.account = selected;
      this.form.DEFAULT_ACCOUNT = selected ? selected.id : '';
    },
    paymentChange(selected) {
      this.payment = selected;
      this.form.CARD_GATEWAY = selected ? selected.value : '';
    },
    driverChange(selected) {
      this.driver = selected;
      this.form.MAIL_MAILER = selected ? selected.value : '';
    },
  },
};
</script>

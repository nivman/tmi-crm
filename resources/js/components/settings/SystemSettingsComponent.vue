<template>
  <div class="wrapper">
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">System Settings</div>
          <div class="panel-block">
            <p class="block has-text-danger">
              Please update the settings by modifying them below
            </p>
          </div>
          <form autocomplete="off" action="#" @submit.prevent="validateForm()" class="is-fullwidth">
            <div class="panel-body">
              <loading v-if="loading"></loading>
              <!-- <div class="columns is-multiline">
                                <div v-for="item in settings" :key="item.key" class="column is-one-quarter">
                                    <div class="field">
                                        <label class="label" :for="item.key">{{ itemLabel(item.key) }}</label>
                                        <div class="control">
                                            <input :value="item.value" v-validate="'required'" type="text" :id="item.key" class="input" :class="{'is-danger': errors.has(item.key) }">
                                        </div>
                                        <div class="help is-danger">{{ errors.first(item.key) }}</div>
                                    </div>
                                </div>
                            </div> -->

              <div class="panel panel-default is-shadowless">
                <div class="panel-heading">
                  General Settings
                </div>
                <div class="panel-body is-shadowless p-t-md">
                  <div class="columns is-multiline">
                    <div class="column is-one-quarter">
                      <div class="field">
                        <label class="label" for="APP_NAME">App Name</label>
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
                        <label class="label" for="APP_URL">App URL</label>
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
                        <label class="label" for="TIMEZONE">App Timezone</label>
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
                        <label class="label" for="DEFAULT_ACCOUNT">Default Account</label>
                        <div class="control">
                          <v-select
                            label="name"
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
                            :class="{
                              select: true,
                              'is-danger': errors.has('DEFAULT_ACCOUNT'),
                            }"
                          >
                            <template slot="no-options">
                              Please type to search...
                            </template>
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
                          label="Enable Stock/Product Quantity"
                        ></checkbox-component>
                        <div class="help is-danger">
                          {{ errors.first('STOCK') }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel panel-default is-shadowless">
                <div class="panel-heading">Email Settings</div>
                <div class="panel-body is-shadowless p-t-md">
                  <div class="columns is-multiline">
                    <div class="column is-one-quarter">
                      <div class="field">
                        <label class="label" for="MAIL_FROM_NAME">From Name</label>
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
                        <label class="label" for="MAIL_FROM_ADDRESS">From Address</label>
                        <div class="control">
                          <input
                            type="email"
                            class="input"
                            name="from_address"
                            id="MAIL_FROM_ADDRESS"
                            v-validate="'required|email'"
                            v-model="form.MAIL_FROM_ADDRESS"
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
                        <label class="label" for="MAIL_MAILER">Email Driver</label>
                        <div class="control">
                          <v-select
                            v-model="driver"
                            :options="drivers"
                            name="MAIL_MAILER"
                            input-id="MAIL_MAILER"
                            v-validate="'required'"
                            @input="driverChange"
                            :style="{
                              width: '100%',
                            }"
                            placeholder="Select mail driver..."
                            :class="{
                              select: true,
                              'is-danger': errors.has('MAIL_MAILER'),
                            }"
                          >
                            <template slot="no-options">
                              Please type to search...
                            </template>
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
                    appear
                  >
                    <div v-if="form.MAIL_MAILER == 'mailgun'" class="columns is-multiline">
                      <div class="column is-one-quarter">
                        <div class="field">
                          <label class="label" for="MAILGUN_DOMAIN">Mailgun Domain</label>
                          <div class="control">
                            <input
                              type="text"
                              class="input"
                              id="MAILGUN_DOMAIN"
                              name="MAILGUN_DOMAIN"
                              v-validate="'required'"
                              v-model="form.MAILGUN_DOMAIN"
                              :class="{
                                'is-danger': errors.has('MAILGUN_DOMAIN'),
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('MAILGUN_DOMAIN') }}
                          </div>
                        </div>
                      </div>
                      <div class="column is-one-quarter">
                        <div class="field">
                          <label class="label" for="MAILGUN_SECRET">Mailgun Secret</label>
                          <div class="control">
                            <input
                              type="text"
                              class="input"
                              id="MAILGUN_SECRET"
                              name="MAILGUN_SECRET"
                              v-validate="'required'"
                              v-model="form.MAILGUN_SECRET"
                              :class="{
                                'is-danger': errors.has('MAILGUN_SECRET'),
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('MAILGUN_SECRET') }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </transition>
                  <transition
                    appear
                    name="fade"
                    mode="out-in"
                    enter-active-class="animated faster fadeInDown"
                    leave-active-class="animated fastest fadeOutLeft"
                  >
                    <div v-if="form.MAIL_MAILER == 'sparkpost'" class="columns is-multiline">
                      <div class="column is-one-quarter">
                        <div class="field">
                          <label class="label" for="SPARKPOST_ENDPOINT">SparkPost Endpoint</label>
                          <div class="control">
                            <input
                              type="text"
                              class="input"
                              v-validate="'required'"
                              id="SPARKPOST_ENDPOINT"
                              name="SPARKPOST_ENDPOINT"
                              v-model="form.SPARKPOST_ENDPOINT"
                              :class="{
                                'is-danger': errors.has('SPARKPOST_ENDPOINT'),
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('SPARKPOST_ENDPOINT') }}
                          </div>
                        </div>
                      </div>
                      <div class="column is-one-quarter">
                        <div class="field">
                          <label class="label" for="SPARKPOST_SECRET">SparkPost Secret</label>
                          <div class="control">
                            <input
                              type="text"
                              class="input"
                              id="SPARKPOST_SECRET"
                              name="SPARKPOST_SECRET"
                              v-validate="'required'"
                              v-model="form.SPARKPOST_SECRET"
                              :class="{
                                'is-danger': errors.has('SPARKPOST_SECRET'),
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('SPARKPOST_SECRET') }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </transition>
                  <transition
                    mode="out-in"
                    name="fade"
                    enter-active-class="animated faster fadeInDown"
                    leave-active-class="animated fastest fadeOutLeft"
                    appear
                  >
                    <div v-if="form.MAIL_MAILER == 'ses'" class="columns is-multiline">
                      <div class="column is-one-quarter">
                        <div class="field">
                          <label class="label" for="SES_KEY">SES Key</label>
                          <div class="control">
                            <input
                              type="text"
                              id="SES_KEY"
                              class="input"
                              name="SES_KEY"
                              v-model="form.SES_KEY"
                              v-validate="'required'"
                              :class="{
                                'is-danger': errors.has('SES_KEY'),
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('SES_KEY') }}
                          </div>
                        </div>
                      </div>
                      <div class="column is-one-quarter">
                        <div class="field">
                          <label class="label" for="SES_SECRET">SES Secret</label>
                          <div class="control">
                            <input
                              type="text"
                              class="input"
                              id="SES_SECRET"
                              name="SES_SECRET"
                              v-validate="'required'"
                              v-model="form.SES_SECRET"
                              :class="{
                                'is-danger': errors.has('SES_SECRET'),
                              }"
                            />
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('SES_SECRET') }}
                          </div>
                        </div>
                      </div>
                      <div class="column is-one-quarter">
                        <div class="field">
                          <label class="label" for="SES_REGION">SES Region</label>
                          <div class="control">
                            <input
                              type="text"
                              class="input"
                              id="SES_REGION"
                              name="SES_REGION"
                              v-validate="'required'"
                              v-model="form.SES_REGION"
                              :class="{
                                'is-danger': errors.has('SES_REGION'),
                              }"
                            />
                          </div>
                          <div class="help">
                            Default: us-east-1
                          </div>
                          <div class="help is-danger">
                            {{ errors.first('SES_REGION') }}
                          </div>
                        </div>
                      </div>
                    </div>
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
                          <label class="label" for="MAIL_HOST">Mail Host</label>
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
                          <label class="label" for="MAIL_PORT">Mail Port</label>
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
                          <label class="label" for="MAIL_USERNAME">Mail Username</label>
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
                          <label class="label" for="MAIL_PASSWORD">Mail Password</label>
                          <div class="control">
                            <input
                              type="text"
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
                            <input type="text" class="input" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION" v-model="form.MAIL_ENCRYPTION" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </transition>
                </div>
              </div>
              <div class="panel panel-default is-shadowless">
                <div class="panel-heading">
                  Payment Settings
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
                <button type="submit" class="button is-link" :disabled="errors.any()" :class="{ 'is-loading': isSaving }">
                  Submit
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
      accounts: [],
      driver: null,
      account: null,
      payment: null,
      loading: true,
      isSaving: false,
      form: new this.$form(),
      booleans: ['PAYPAL_ENABLED', 'STOCK'],
      payments: [
        { label: 'Disable', value: '' },
        { label: 'Stripe', value: 'Stripe' },
        { label: 'PayPal Pro', value: 'PayPal_Pro' },
        { label: 'PayPal Rest', value: 'PayPal_Rest' },
        { label: 'Authorize.net', value: 'AuthorizeNetApi_Api' },
      ],
      drivers: [
        { label: 'Amazon SES', value: 'ses' },
        { label: 'Log', value: 'log' },
        { label: 'Mailgun', value: 'mailgun' },
        { label: 'SendMail', value: 'sendmail' },
        { label: 'SMTP', value: 'smtp' },
        { label: 'SparkPost', value: 'sparkpost' },
      ],
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
            this.settingsToForm(res.data);
            this.loading = false;
          })
          .catch(err => this.$event.fire('appError', err.response));
      })
      .catch(err => this.$event.fire('appError', err.response));
  },
  computed: {
    paypal: function() {
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
        } else if (setting.key == 'DEFAULT_ACCOUNT') {
          this.account = this.accounts.find(a => a.id == setting.value);
        } else if (setting.key == 'CARD_GATEWAY') {
          this.payment = this.payments.find(p => p.value == setting.value);
        }
      }
      this.form = new this.$form(form);
      // this.form.DEFAULT_ACCOUNT = this.account.id;
    },
    validateForm() {
      this.$validator
        .validateAll()
        .then(result => {
          if (result) {
            this.isSaving = true;
            this.form
              .post('app/settings/system')
              .then(res => {
                this.form = new this.$form(res.data);
                this.$store.commit('SET_STOCK', this.form.STOCK);
                this.notify('success', 'System settings has been successfully updated.');
                this.isSaving = false;
              })
              .catch(err => {
                this.$event.fire('appError', err.response);
              });
          } else {
            this.notify(
              'error',
              'All the fields are required except Mail Encryption & Payment Gateway. Please fill the form and try again.'
            );
          }
        })
        .catch(err => {
          this.$event.fire('appError', err);
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

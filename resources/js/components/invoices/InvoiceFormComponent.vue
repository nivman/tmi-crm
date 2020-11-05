<template>
  <div class="wrapper">
    <div class="panel panel-default">
      <div class="panel-heading">
        {{ form.id ? 'עריכת חשבונית ' : 'חשבונית חדשה'  }}
      </div>
      <div class="panel-body">
        <loading v-if="loading" class="cl-top"></loading>
        <form autocomplete="off" @submit.prevent="validateForm">
          <div class="columns is-multiline">
            <div class="column is-half-tablet is-one-third-desktop">
              <div class="field">
                <label class="label" for="date">תאריך</label>
                <div class="control">
                  <flat-pickr
                    id="date"
                    name="date"
                    class="input"
                    v-model="form.date"
                    format="yyyy-MM-dd"
                    v-validate="'required'"
                    :class="{
                      'is-danger': errors.has('date'),
                    }"
                  ></flat-pickr>
                </div>
                <div class="help is-danger">
                  {{ errors.first('date') }}
                </div>
              </div>
            </div>
            <div class="column is-half-tablet is-one-third-desktop">
              <div class="field">
                <label class="label" for="reference">הפניה</label>
                <div class="control">
                  <input
                    type="text"
                    class="input"
                    id="reference"
                    name="reference"
                    v-model="form.reference"
                    :class="{'is-danger': errors.has('reference'),}"/>
                </div>
                <div class="help is-danger">
                  {{ errors.first('reference') }}
                </div>
              </div>
            </div>
            <div class="column is-half-tablet is-one-third-desktop">
              <div class="field">
                <label class="label" for="customer">לקוח</label>
                <div class="control">
                  <v-select
                    label="name"
                    name="customer"
                    id="customer"
                    v-model="customer"
                    :options="customers"
                    input-id="customer"
                    v-validate="'required'"
                    @input="customerChange"
                    @search="searchCustomer"
                    :style="{ width: '100%', textAlign: 'right' }"
                    placeholder="חיפוש לקוחות"
                    :class="{
                      select: true,
                      'is-danger': errors.has('customer'),
                    }"
                  >
                    <template slot="no-options">
                      :-( לא מצאתי לקוח
                    </template>
                  </v-select>
                </div>
                <div class="help is-danger">
                  {{ errors.first('customer') }}
                </div>
              </div>
            </div>
            <div class="column is-half-tablet is-one-third-desktop">
              <div class="field">
                <label class="label" for="taxes"> מיסים</label>
                <div class="control">
                  <v-select
                    multiple
                    label="code"
                    id="taxes"
                    class="select"
                    :options="taxes"
                    input-id="taxes"
                    max-height="150px"
                    v-model="form.taxes"
                    :style="{ width: '100%' }"
                    placeholder="בחירת מס"
                  ></v-select>
                </div>
                <div class="help is-danger">
                  {{ errors.first('taxes') }}
                </div>
              </div>
            </div>
            <div class="column is-half-tablet is-one-third-desktop">
              <div class="field">
                <label class="label" for="discount">הנחה</label>
                <div class="control">
                  <input class="input" id="discount" name="discount" v-model="form.discount" v-validate="{ regex: /^([0-9%]+)$/ }" />
                </div>
                <div class="help is-danger">
                  {{ errors.first('discount') }}
                </div>
              </div>
            </div>
            <!-- <transition mode="out-in" name="fade" enter-active-class="animated faster fadeInDown" leave-active-class="animated fastest fadeOutLeft" appear></transition> -->
<!--            <div class="column is-half-tablet is-one-third-desktop">-->
<!--              <div class="field">-->
<!--                <label class="label" for="shipping">Shipping</label>-->
<!--                <div class="control">-->
<!--                  <input id="shipping" class="input" name="shipping" v-model="form.shipping" v-validate="{ regex: /^([0-9%]+)$/ }" />-->
<!--                </div>-->
<!--                <div class="help is-danger">-->
<!--                  {{ errors.first('shipping') }}-->
<!--                </div>-->
<!--              </div>-->
<!--            </div>-->
          </div>
          <div class="field has-addons">
            <p class="control is-expanded">
              <autocomplete
                label="code"
                anchor="name"
                param="query"
                name="product"
                :debounce="250"
                id="add-product"
                ref="autocomplete"
                url="app/products/search"
                :onSelect="productSelected"
                :onShouldGetData="getProducts"
                :classes="{
                  input: 'input',
                  wrapper: 'dropdown',
                  item: 'dropdown-item',
                  list: 'dropdown-menu dropdown-content',
                }"
                placeholder="חיפוש מוצר"
                :customHeaders="{
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': $laravel.token,
                }"
              ></autocomplete>
            </p>
            <p class="control">
              <a class="button" @click="addProduct()">
                <i class="fas fa-plus" />
              </a>
            </p>
          </div>
          <div class="field table-body-br" v-if="form.products.length">
            <div v-if="errors.products_empty">
              <p class="alert alert-danger">
                {{ errors.products_empty[0] }}
              </p>
              <hr />
            </div>
            <table v-if="isMobile" class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth is-rounded table-form">
              <thead>
                <tr>
                  <th class="has-text-centered">
                    Order Items
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(product, key) in form.products" :key="key">
                  <td style="padding:10px 8px 6px 8px;">
                    <div class="field" style="margin-bottom:4px;">
                      <p class="control" :style="{ width: '100%' }">
                        <input class="input is-small has-text-centered" disabled :value="'# ' + (key + 1)" />
                      </p>
                    </div>
                    <div class="field has-addons" style="margin-bottom:4px;">
                      <p class="control">
                        <a class="button is-static">
                          <i class="fas fa-fw fa-barcode"></i>
                        </a>
                      </p>
                      <p class="control" :style="{ width: '100%' }">
                        <input class="input" v-model="product.name" />
                      </p>
                    </div>
                    <div class="field has-addons" style="margin-bottom:4px;">
                      <p class="control">
                        <a class="button is-static">
                          <i class="fas fa-fw fa-dollar-sign"></i>
                        </a>
                      </p>
                      <p class="control" :style="{ width: '100%' }">
                        <number-input-component
                          :value.sync="product.price"
                          :validation="{
                            rules: 'required',
                            name: 'price',
                          }"
                          :field="{
                            label: null,
                            name: 'price',
                            hideHelp: true,
                          }"
                        ></number-input-component>
                      </p>
                    </div>
                    <div class="field has-addons" style="margin-bottom:4px;">
                      <p class="control">
                        <a class="button is-static">
                          <i class="fas fa-fw fa-percentage has-text-warning"></i>
                        </a>
                      </p>
                      <p class="control" :style="{ width: '100%' }">
                        <input type="text" class="input" :value="product.discount" @input="applyDiscount(product, $event)" />
                      </p>
                    </div>
                    <div class="field has-addons" style="margin-bottom:4px;">
                      <p class="control">
                        <a class="button is-static">
                          <i class="fas fa-fw fa-boxes"></i>
                        </a>
                      </p>
                      <p class="control" :style="{ width: '100%' }">
                        <number-input-component
                          :value.sync="product.qty"
                          :validation="{
                            rules: 'required',
                            name: 'qty',
                          }"
                          :field="{
                            label: null,
                            name: 'qty',
                            hideHelp: true,
                            icon: Icon(product.stock.qty),
                          }"
                        ></number-input-component>
                      </p>
                    </div>
                    <div class="field has-addons" style="margin-bottom:4px;">
                      <p class="control">
                        <a class="button is-static">
                          <i class="fas fa-fw fa-book"></i>
                        </a>
                      </p>
                      <div class="control p-x-sm tax-border table-taxes" :style="{ width: '100%' }">
                        <div v-for="tax in product.taxes" :key="tax.id + '_' + tax.id" class="inline-block m-b-none">
                          <small class="text-muted">{{ tax.code }}</small>
                          <span class="is-pulled-right mono">{{ (tax.amount * product.qty) | formatNumber }}</span>
                        </div>
                        <div class="edit" @click="prTaxes(product, taxes)">
                          <i class="fas fa-edit"></i>
                        </div>
                      </div>
                    </div>
                    <div class="field has-addons" style="margin-bottom:4px;">
                      <a @click="removeLine(key)" class="button table-remove-btn">
                        <i class="fas fa-fw fa-trash has-text-danger" />
                      </a>
                      <p class="control" :style="{ width: '100%' }">
                        <input
                          disabled
                          readonly
                          type="text"
                          class="input has-text-right has-text-weight-bold"
                          :value="calcRowTotal(product) | formatDecimal(2)"
                        />
                      </p>
                    </div>
                  </td>
                </tr>
              </tbody>
              <tfoot class="has-text-weight-bold">
                <tr>
                  <td>
                    Total
                    <span class="is-pulled-right">{{ subTotal | formatDecimal(2) }}</span>
                  </td>
                </tr>
                <tr>
                  <td>
                    Product Tax
                    <span class="is-pulled-right">
                      <a @click="showProductsTaxDetails(productTaxes)">{{ totalProductTax | formatDecimal(2) }}</a>
                    </span>
                  </td>
                </tr>
                <!-- <tr v-for="(r, index)  in productTaxes" :key="index" v-html="r"></tr> -->
                <tr v-if="this.form.discount">
                  <td>
                    Discount
                    <span class="is-pulled-right">{{ discountAmount | formatDecimal(2) }}</span>
                  </td>
                </tr>
                <tr v-if="this.form.taxes && this.form.taxes.length">
                  <td>
                    Order Tax
                    <span class="is-pulled-right">{{ taxAmount | formatDecimal(2) }}</span>
                  </td>
                </tr>
                <tr v-if="this.form.shipping">
                  <td>
                    Shipping
                    <span class="is-pulled-right">{{ shippingAmount | formatDecimal(2) }}</span>
                  </td>
                </tr>
                <tr v-if="subTotal != grandTotal">
                  <td>
                    Grand Total
                    <span class="is-pulled-right">{{ grandTotal | formatDecimal(2) }}</span>
                  </td>
                </tr>
              </tfoot>
            </table>
            <table v-else class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth is-rounded table-form">
              <thead>
                <tr>
                  <th width="40" class="has-text-centered">
                    <i class="fas fa-trash"></i>
                  </th>
                  <th class="has-text-centered">
                    Product Name
                  </th>
                  <th class="has-text-centered" width="10%">
                    Price
                  </th>
                  <th class="has-text-centered" width="10%">
                    Discount
                  </th>
                  <th class="has-text-centered" width="10%">
                    Qty
                  </th>
                  <th class="has-text-centered" width="15%">
                    Taxes
                  </th>
                  <th class="has-text-centered" width="15%">
                    Total
                  </th>
                </tr>
              </thead>
              <transition-group
                tag="tbody"
                mode="out-in"
                name="fade"
                enter-active-class="animated faster fadeInDown"
                leave-active-class="animated fastest fadeOutDown"
                appear
              >
                <tr v-for="(product, key, index) in form.products" :key="key">
                  <td class="table-remove has-text-centered">
                    <a @click="removeLine(key)" class="table-remove-btn">
                      <i class="fas fa-trash has-text-danger" />
                    </a>
                  </td>
                  <td
                    class="table-name"
                    :class="{
                      'table-error': errors['products.' + index + '.name'],
                    }"
                  >
                    <input class="textarea" v-model="product.name" rows="1" />
                  </td>
                  <td
                    class="table-price"
                    :class="{
                      'table-error': errors['products.' + index + '.price'],
                    }"
                  >
                    <number-input-component
                      :value.sync="product.price"
                      :validation="{
                        rules: 'required',
                        name: 'price',
                      }"
                      :field="{
                        label: null,
                        name: 'price',
                        hideHelp: true,
                      }"
                    ></number-input-component>
                  </td>
                  <td
                    class="table-disc"
                    :class="{
                      'table-error': errors['products.' + index + '.disc'],
                    }"
                  >
                    <input type="text" class="input" :value="product.discount" @input="applyDiscount(product, $event)" />
                  </td>
                  <td
                    class="table-qty"
                    :class="{
                      'table-error': errors['products.' + index + '.qty'],
                    }"
                  >
                    <number-input-component
                      :value.sync="product.qty"
                      :validation="{
                        rules: 'required',
                        name: 'qty',
                      }"
                      :field="{
                        label: null,
                        name: 'qty',
                        hideHelp: true,
                        icon: Icon(product.stock.qty),
                      }"
                    ></number-input-component>
                  </td>
                  <td class="table-taxes">
                    <div v-for="tax in product.taxes" :key="tax.id + '_' + tax.id" class="inline-block m-b-none">
                      <small class="text-muted">{{ tax.code }}</small>
                      <span class="is-pulled-right mono">{{ (tax.amount * product.qty) | formatNumber }}</span>
                    </div>
                    <div class="edit" @click="prTaxes(product, taxes)">
                      <i class="fas fa-edit"></i>
                    </div>
                  </td>
                  <td class="table-total has-text-right">
                    <span class="table-text">{{ calcRowTotal(product) | formatDecimal(2) }}</span>
                  </td>
                </tr>
              </transition-group>

              <tfoot class="has-text-weight-bold">
                <tr>
                  <td class="table-label has-text-right" colspan="4">
                    <!-- <a @click="addLine" class="table-add_line is-pulled-left is-small">
                                            <span class="icon is-small"><i class="fas fa-plus" /></span>
                                            <span>Add Row</span>
                                        </a> -->
                    Total
                  </td>
                  <td class="table-amount has-text-right">
                    {{ totalQty | formatDecimal(2) }}
                  </td>
                  <td class="table-amount has-text-right">
                    <a @click="showProductsTaxDetails(productTaxes)">{{ totalProductTax | formatDecimal(2) }}</a>
                  </td>
                  <td class="table-amount has-text-right">
                    {{ subTotal | formatDecimal(2) }}
                  </td>
                </tr>
                <!-- <tr v-for="(r, index)  in productTaxes" :key="index" v-html="r"></tr> -->
                <tr v-if="this.form.discount">
                  <td class="table-label has-text-right" colspan="6">
                    Discount
                  </td>
                  <td class="table-amount has-text-right">
                    {{ discountAmount | formatDecimal(2) }}
                  </td>
                </tr>
                <tr v-if="this.form.taxes && this.form.taxes.length">
                  <td class="table-label has-text-right" colspan="6">
                    Order Tax
                  </td>
                  <td class="table-amount has-text-right">
                    {{ taxAmount | formatDecimal(2) }}
                  </td>
                </tr>
                <tr v-if="this.form.shipping">
                  <td class="table-label has-text-right" colspan="6">
                    Shipping
                  </td>
                  <td class="table-amount has-text-right">
                    {{ shippingAmount | formatDecimal(2) }}
                  </td>
                </tr>
                <tr v-if="subTotal != grandTotal">
                  <td class="table-label has-text-right" colspan="6">
                    Grand Total
                  </td>
                  <td class="table-amount has-text-right">
                    {{ grandTotal | formatDecimal(2) }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div v-if="attributes">
            <h5 class="cf">שדות דינמיים</h5>
            <div class="columns is-multiline">
              <div class="column is-one-third-desktop" v-for="attr in attributes" :key="attr.slug">
                <custom-field-component :attr="attr" v-model="form[attr.slug]"></custom-field-component>
              </div>
            </div>
          </div>
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label" for="note">הערות</label>
                <div class="control">
                  <textarea v-model="form.note" name="note" id="note" rows="3" class="textarea"></textarea>
                </div>
                <div class="help is-danger">
                  {{ errors.first('note') }}
                </div>
              </div>
              <div class="field" v-if="is_draftable">
                <checkbox-component
                  id="draft"
                  name="draft"
                  v-model="form.draft"
                  :checked="form.draft"
                  label="חשבונית זו עדיין טיוטה"
                ></checkbox-component>
              </div>
              <div class="field" v-if="is_draftable && !form.draft">
                <checkbox-component
                  id="create_payment"
                  name="create_payment"
                  v-model="form.create_payment"
                  :checked="form.create_payment"
                  label="יצירה אוטומטית של תשלום עבור חשבונית זו"
                ></checkbox-component>
              </div>
              <div class="field">
                <button type="submit" class="button is-link" :disabled="errors.any()" :class="{ 'is-loading': isSaving }">
                  הוספה
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <product-form-modal></product-form-modal>
  </div>
</template>

<script>
import Order from '../../mixins/Order';
export default {
  mixins: [Order('invoice')],
  data() {
    return {};
  },
  created() {
    this.$event.listen('addProductToInvoice', product => {
      this.$storage.remove('order');
      this.productSelected(product);
    });
  },
  methods: {
    Icon(qty) {
      return this.$store.getters.stock
        ? {
            html: '<small>' + this.$options.filters.formatDecimal(qty, 0) + '</small>',
          }
        : false;
    },
    addProduct() {
      this.$storage.write('order', 'ToInvoice');
      this.$modal.show('product-form-modal');
    },
  },
};
</script>

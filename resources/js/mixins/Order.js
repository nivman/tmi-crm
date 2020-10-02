import _debounce from 'lodash/debounce';
const inflection = require('inflection');
import Autocomplete from 'vue2-autocomplete-js';
import ProductFormModal from '../components/helpers/ProductFormModal';

const dict = {
  custom: {
    tax: { regex: 'Only % and numbers are allowed.' },
    discount: { regex: 'Only % and numbers are allowed.' },
  },
};

function Order(model) {
  var oName = inflection.capitalize(model);
  var route = '/' + inflection.pluralize(model);
  var fName = model == 'purchase' ? 'cost' : 'price';

  return {
    components: { Autocomplete, ProductFormModal },
    data() {
      return {
        taxes: [],
        vendors: [],
        // accounts: [],
        vendor: null,
        // account: null,
        create: false,
        company: null,
        loading: true,
        customers: [],
        customer: null,
        attributes: [],
        is_draft: true,
        form: new this.$form({
          id: '',
          date: '',
          reference: '',
          customer_id: '',
          vendor_id: '',
          draft: true,
          taxes: [],
          discount: '',
          shipping: '',
          note: '',
          products: [],
          create_payment: false,
        }),
        isSaving: false,
      };
    },
    watch: {
      $route(to) {
        this.form.reset();
        this.vendor = null;
        this.customer = null;
        this.form.date = new Date();
        this.$validator.reset();
        this.create = to.path == route + '/add' ? true : false;
        this.fillOrder();
      },
      customer: function(customer) {
        if (this.create) {
          this.$storage.update('customer', customer, oName);
        }
      },
      vendor: function(vendor) {
        if (this.create) {
          this.$storage.update('vendor', vendor, oName);
        }
      },
      // account: function(account) {
      //     if (this.create) {
      //         this.$storage.update("account", account);
      //     }
      // },
      form: {
        handler(form) {
          if (this.create) {
            this.$storage.update('date', form.date, oName, 'form');
            this.$storage.update('note', form.note, oName, 'form');
            this.$storage.update('taxes', form.taxes, oName, 'form');
            this.$storage.update('draft', form.draft, oName, 'form');
            this.$storage.update('discount', form.discount, oName, 'form');
            this.$storage.update('shipping', form.shipping, oName, 'form');
            this.$storage.update('reference', form.reference, oName, 'form');
            this.$storage.update('products', form.products, oName, 'form');
            for (var a in this.attributes) {
              this.$storage.update(a, form[a], oName, 'form');
            }
            if (model == 'recurring') {
              this.$storage.update('active', form.active, oName, 'form');
              this.$storage.update('repeat', form.repeat, oName, 'form');
              this.$storage.update('start_date', form.start_date, oName, 'form');
              this.$storage.update('create_before', form.create_before, oName, 'form');
            }
          }
        },
        deep: true,
      },
    },
    created() {
      this.$http
        .get('app/taxes?all=1')
        .then(taxes => {
          this.taxes = taxes.data;
          // this.$http
          //     .get("app/accounts?all=1")
          //     .then(accounts => {
          //         this.accounts = accounts.data;
          if (this.$route.params.id) {
            this.create = false;
            this.fetchOrder(this.$route.params.id);
          } else {
            this.create = true;
            this.fillOrder();
            this.loading = false;
          }
          // })
          // .catch(err => this.$event.fire("appError", err.response));
        })
        .catch(err => this.$event.fire('appError', err.response));
      this.form.date = new Date();
      this.$validator.localize('en', dict);
      // this.fillOrder();
    },
    mounted() {
      this.$http
        .get('app/companies/1')
        .then(res => (this.company = res.data))
        .catch(err => this.$event.fire('appError', err.response));
    },
    methods: {
      // accountChange(selected) {
      //     this.account = selected;
      //     this.form.account_id = selected ? selected.id : "";
      // },
      checkDisc(disc, amt) {
        if (disc) {
          if (disc.indexOf('%') !== -1) {
            var pds = disc.split('%');
            let amount = parseFloat((parseFloat(amt) * parseFloat(pds[0])) / 100);
            return { discount: disc, discount_amount: isNaN(parseFloat(amount)) ? 0 : parseFloat(amount) };
          }
          return { discount: disc, discount_amount: isNaN(parseFloat(disc)) ? 0 : parseFloat(disc) };
        }
        return { discount: '', discount_amount: 0 };
      },
      customerChange(selected) {
        this.customer = selected;
        this.form.customer_id = selected ? selected.value : '';

        this.form.products.map(p => {
          let disc = this.checkDisc(p.discount, p[fName]);
          p.discount = disc.discount;
          p.discount_amount = disc.discount_amount;
          p.taxes = this.calculateTaxes(p.allTaxes);
        });
      },
      vendorChange(selected) {
        this.vendor = selected;
        this.form.vendor_id = selected ? selected.value : '';
        this.form.products.map(p => {
          let disc = this.checkDisc(p.discount, p[fName]);
          p.discount = disc.discount;
          p.discount_amount = disc.discount_amount;
          p.taxes = this.calculateTaxes(p.allTaxes);
        });
      },
      searchCustomer(search, loading) {
        if (search) {
          this.getCustomers(search, loading, this);
        }
      },
      getCustomers: _debounce((search, loading, vm) => {
        loading(true);
        vm.$http
          .get('app/customers/search?query=' + search)
          .then(res => {
            vm.customers = res.data;
            loading(false);
          })
          .catch(err => {
            vm.$event.fire('appError', err.response);
          });
      }, 250),
      searchVendor(search, loading) {
        if (search) {
          this.getVendors(search, loading, this);
        }
      },
      getVendors: _debounce((search, loading, vm) => {
        loading(true);
        vm.$http
          .get('app/vendors/search?query=' + search)
          .then(res => {
            vm.vendors = res.data;
            loading(false);
          })
          .catch(err => {
            vm.$event.fire('appError', err.response);
          });
      }, 250),
      removeLine: function(index) {
        this.form.products.splice(index, 1);
      },
      submit() {
        if (this.form.id && this.form.id !== '') {
          this.form
            .put(`app${route}/${this.form.id}`)
            .then(() => {
              this.notify('success', oName + ' has been successfully updated.');
              this.$router.push(route);
            })
            .catch(err => {
              this.isSaving = false;
              this.$event.fire('appError', err.response);
            });
        } else {
          this.form
            .post('app' + route)
            .then(() => {
              this.$storage.remove(oName);
              this.notify('success', oName + ' has been successfully added.');
              this.$router.push(route);
            })
            .catch(err => {
              this.isSaving = false;
              this.$event.fire('appError', err.response);
            });
        }
      },
      fetchOrder(id) {
        this.$http
          .get(`app${route}/${id}`)
          .then(inv => {
            this.$http
              .get(`app/${model == 'purchase' ? 'vendors/' + inv.data.vendor_id : 'customers/' + inv.data.customer_id}`)
              .then(({ data }) => {
                data.value = data.id;
                if (model == 'purchase') {
                  this.vendor = data;
                  this.vendors = [data];
                } else {
                  this.customer = data;
                  this.customers = [data];
                }
                this.is_draft = inv.data.draft;
                this.attributes = inv.data.attributes;
                inv.data.products = inv.data.items.map(product => {
                  let amount = product[fName] - product.discount_amount;
                  product.allTaxes = this.taxes.filter(t => product.item_taxes.includes(t.id));
                  product.taxes = this.calculateTaxes(product.allTaxes, amount);
                  product.qty = +product.qty;
                  return product;
                });
                delete inv.data.attributes;
                delete inv.data.items;
                inv.data.create_payment = false;
                this.form = new this.$form(inv.data);
                this.loading = false;
              })
              .catch(err => this.$event.fire('appError', err.response));
          })
          .catch(err => this.$event.fire('appError', err.response));
      },
      validateForm() {
        this.$validator
          .validateAll()
          .then(result => {
            if (result) {
              this.isSaving = true;
              this.submit();
            }
          })
          .catch(err => this.$event.fire('appError', err));
      },
      productSelected(product) {
        product.qty = 1;
        product.discount = '';
        product[fName] = fName == 'price' ? +product.price : +product.cost;
        product.allTaxes = product.taxes;
        product.product_id = product.id;
        this.form.products.push(product);
        this.$refs.autocomplete.setValue('');
        this.$refs.autocomplete.$el.querySelector('input').focus();
      },
      calcRowTotal(product) {
        product.total_tax_amount = product.taxes.reduce((a, t) => a + parseFloat(t.amount), 0);
        return parseFloat(
          this.$options.filters.formatDecimal(
            parseFloat(product.qty) *
              (parseFloat(product[fName]) - parseFloat(product.discount_amount ? product.discount_amount : 0) + product.total_tax_amount),
            2
          )
        );
      },
      prTaxes(product, taxes) {
        this.$modal.show(
          {
            template: `
                        <div class="modal is-active">
                            <div class="modal-content is-rounded p-md" style="max-width:350px;background:#fff;">
                            <button type="button" class="delete is-pulled-right" @click="$modal.hide('ProductTaxes')"></button>
                                <div class="field">
                                    <label class="label" for="ptaxes">Product Taxes</label>
                                    <div class="control">
                                        <v-select multiple :options="taxes" v-model="product.allTaxes" label="code" :style="{ width: '100%' }" :searchable="false" :filterable="false" max-height="150px"></v-select>
                                    </div>
                                </div>
                                <p class="m-b-md">You can change product taxes here, as you need. If any tax is state related that will applied accordingly.</p>
                                <p>All other taxes, those are not related to state should be applied.</p>
                                <button class="modal-close is-large" aria-label="close" @click="$modal.hide('ProductTaxes')"></button>
                            </div>
                        </div>
                        `,
            props: ['product', 'taxes'],
          },
          {
            taxes: taxes,
            product: product,
          },
          {
            name: 'ProductTaxes',
            height: 'auto',
            width: '400',
          }
        );
      },
      applyDiscount(p, e) {
        let disc = this.checkDisc(e.target.value, p[fName]);
        p.discount = disc.discount;
        p.discount_amount = disc.discount_amount;
      },
      applicableTaxes(taxes) {
        return taxes.filter(t => {
          if (model == 'purchase') {
            if (t.state && this.vendor && this.company) {
              let same = this.vendor.state == this.company.state;
              return t.same ? same : !same;
            }
          } else {
            if (t.state && this.customer && this.company) {
              let same = this.customer.state == this.company.state;
              return t.same ? same : !same;
            }
          }
          return true;
        });
      },
      calculateTaxes(taxes, amount) {
        taxes = this.applicableTaxes(taxes);
        let c_taxes = taxes.filter(t => t.compound == true);
        let nc_taxes = taxes.filter(t => t.compound == false);
        let non_compound = nc_taxes.map(t => ({
          ...t,
          value: t.id,
          on: parseFloat(amount),
          amount: (parseFloat(amount) * parseFloat(t.rate ? t.rate : 0)) / 100,
        }));
        let tax_amount = non_compound.reduce((a, c) => a + parseFloat(c.amount), 0);
        let compound = c_taxes.map(t => ({
          ...t,
          value: t.id,
          on: parseFloat(amount) + parseFloat(tax_amount),
          amount: ((parseFloat(amount) + parseFloat(tax_amount)) * parseFloat(t.rate ? t.rate : 0)) / 100,
        }));
        return [...non_compound, ...compound];
      },
      getProducts(value) {
        return new Promise(resolve => {
          if (value) {
            this.$http
              .get(`app/products/search?query=${value}`)
              .then(res => {
                if (res.data.length) {
                  if (res.data.length === 1) {
                    this.productSelected(res.data[0]);
                  } else {
                    resolve(res.data);
                  }
                }
                resolve([]);
              })
              .catch(err => this.$event.fire('appError', err.response));
          } else {
            resolve([]);
          }
        });
      },
      fillOrder() {
        let order = this.$storage.read(oName);
        if (this.create && order) {
          let $els = {
            customer: '',
            // account: "",
            vendor: '',
            note: '',
            form: {
              products: '',
              tax: '',
              date: '',
              note: '',
              draft: '',
              taxes: '',
              discount: '',
              reference: '',
              shipping: '',
            },
          };
          if (model == 'recurring') {
            $els.form.active = '';
            $els.form.repeat = '';
            $els.form.start_date = '';
            $els.form.create_before = '';
          }
          for (let el in $els) {
            if (el === 'form') {
              for (let e in $els.form) {
                this.$set(this.form, e, order.form[e] ? order.form[e] : this.form[e]);
              }
            } else {
              this[el] = order[el] ? order[el] : this[el];
            }
          }
          // Object.keys($els).map(el => {
          //     if (el === "form") {
          //         Object.keys($els.form).map(e => {
          //             this.$set(this.form, e, order.form[e] ? order.form[e] : this.form[e]);
          //         });
          //     } else {
          //         this[el] = order[el] ? order[el] : this[el];
          //     }
          // });
        }
        this.$http
          .get(`app${route}/create`)
          .then(res => {
            this.attributes = res.data;
            if (order) {
              for (var a in this.attributes) {
                this.$set(this.form, a, order.form[a] ? order.form[a] : this.form[a]);
              }
            }
          })
          .catch(err => this.$event.fire('appError', err.response));
      },
      showProductsTaxDetails(productTaxes) {
        this.$modal.show(
          {
            template: `
                        <div class="modal-card" style="width:auto;">
                            <header class="modal-card-head is-radius-top">
                                <p class="modal-card-title">Products Tax Details</p>
                                <button type="button" class="delete" @click="$modal.hide('taxDetails')"></button>
                            </header>
                            <section class="modal-card-body is-radius-bottom">
                                <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                                    <thead class="is-active">
                                        <tr>
                                            <td>Tax</td>
                                            <td>Amount</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(r, index)  in productTaxes" :key="index" v-html="r"></tr>
                                    </tbody>
                                </table>
                            </section>
                        </div>
                        `,
            props: ['productTaxes'],
          },
          {
            productTaxes: productTaxes,
          },
          {
            name: 'taxDetails',
            height: 'auto',
            width: '300',
          }
        );
      },
    },
    computed: {
      productTaxes: function() {
        let taxes = [];
        this.form.products.map(p => {
          if (fName == 'price') {
            delete p.cost;
          }
          p[fName] = +p[fName];
          let disc = this.checkDisc(p.discount, p[fName]);
          p.discount = disc.discount;
          p.discount_amount = disc.discount_amount;
          p.taxes.map(t => {
            if (t.id) {
              taxes[t.id] = taxes[t.id]
                ? {
                    total: parseFloat(taxes[t.id].amount) + parseFloat(t.amount) * parseFloat(p.qty),
                    ...t,
                  }
                : { total: parseFloat(t.amount) * parseFloat(p.qty), ...t };
            }
          });
        });
        return taxes.map(t =>
          t ? `<td>${t.name} (${t.code})</td><td class="has-text-right">${this.$options.filters.formatDecimal(t.total, 2)}</td>` : ''
        );
      },
      totalQty: function() {
        return this.form.products.reduce((a, p) => a + parseFloat(p.qty), 0);
      },
      totalProductTax: function() {
        return this.form.products.reduce((tax, product) => {
          let amount = product[fName] - parseFloat(product.discount_amount ? product.discount_amount : 0);
          product.taxes = this.calculateTaxes(product.allTaxes, amount);
          product.qty = +product.qty;
          let rowTax = product.taxes.reduce((a, t) => a + parseFloat(t.amount) * parseFloat(product.qty), 0);
          return parseFloat(tax + rowTax);
        }, 0);
      },
      subTotal: function() {
        return this.form.products.reduce((a, p) => {
          return a + this.calcRowTotal(p);
        }, 0);
      },
      discountAmount: function() {
        if (this.form.discount && this.form.discount.indexOf('%') !== -1) {
          let od = this.form.discount.split('%');
          if (!isNaN(parseFloat(od[0]))) {
            return parseFloat(this.$options.filters.formatDecimal((this.subTotal * parseFloat(od[0])) / 100, 2));
          }
        }
        return parseFloat(this.form.discount ? this.form.discount : 0);
      },
      shippingAmount: function() {
        if (this.form.shipping && this.form.shipping.indexOf('%') !== -1) {
          let od = this.form.shipping.split('%');
          if (!isNaN(parseFloat(od[0]))) {
            return parseFloat(this.$options.filters.formatDecimal((this.subTotal * parseFloat(od[0])) / 100, 2));
          }
        }
        return parseFloat(this.form.shipping ? this.form.shipping : 0);
      },
      taxAmount: function() {
        if (this.form.taxes) {
          let taxable_amount = this.subTotal - this.discountAmount;
          let taxes = this.calculateTaxes(this.form.taxes, taxable_amount);
          return taxes.reduce((a, t) => a + t.amount, 0);
        }
        return 0;
      },
      grandTotal: function() {
        return this.subTotal + this.taxAmount - this.discountAmount + this.shippingAmount;
      },
      is_draftable() {
        if (this.$route.params.id) {
          return this.is_draft;
        }
        return true;
      },
    },
  };
}

export default Order;

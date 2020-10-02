<template>
    <div class="wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{
                    form.id
                        ? "Edit Recurring Invoice"
                        : "Add New Recurring Invoice"
                }}
            </div>
            <div class="panel-body">
                <loading v-if="loading" class="cl-top"></loading>
                <form autocomplete="off" @submit.prevent="validateForm">
                    <div class="columns is-multiline">
                        <div class="column is-half-tablet is-one-third-desktop">
                            <div class="field">
                                <label class="label" for="start_date"
                                    >Start Date</label
                                >
                                <div class="control">
                                    <flat-pickr
                                        class="input"
                                        id="start_date"
                                        name="start_date"
                                        format="yyyy-MM-dd"
                                        v-validate="'required'"
                                        v-model="form.start_date"
                                        :class="{
                                            'is-danger': errors.has(
                                                'start_date'
                                            )
                                        }"
                                    ></flat-pickr>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("start_date") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half-tablet is-one-third-desktop">
                            <div class="field">
                                <label class="label" for="create_before"
                                    >Create Before (Days)</label
                                >
                                <div class="control">
                                    <input
                                        type="number"
                                        class="input"
                                        id="create_before"
                                        name="create_before"
                                        v-model="form.create_before"
                                        :class="{
                                            'is-danger': errors.has(
                                                'create_before'
                                            )
                                        }"
                                    />
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("create_before") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half-tablet is-one-third-desktop">
                            <div class="field">
                                <label class="label" for="repeat">Repeat</label>
                                <div class="select is-fullwidth">
                                    <select
                                        id="repeat"
                                        v-model="form.repeat"
                                        placeholder="Please select repeat period"
                                        :class="{
                                            'is-danger': errors.has('repeat')
                                        }"
                                    >
                                        <option value="">Repeat Period</option>
                                        <option value="daily"
                                            >Every day (daily)</option
                                        >
                                        <option value="weekly"
                                            >Every 7 days (weekly)</option
                                        >
                                        <option value="monthly"
                                            >Every month (monthly)</option
                                        >
                                        <option value="quarterly"
                                            >Every 3 months (quarterly)</option
                                        >
                                        <option value="semiannually"
                                            >Every 6 months
                                            (semiannually)</option
                                        >
                                        <option value="annually"
                                            >Every year (annually)</option
                                        >
                                        <option value="biennially"
                                            >Every 2 years (biennially)</option
                                        >
                                    </select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("repeat") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half-tablet is-one-third-desktop">
                            <div class="field">
                                <label class="label" for="reference"
                                    >Reference</label
                                >
                                <div class="control">
                                    <input
                                        type="text"
                                        class="input"
                                        id="reference"
                                        name="reference"
                                        v-model="form.reference"
                                        :class="{
                                            'is-danger': errors.has('reference')
                                        }"
                                    />
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("reference") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half-tablet is-one-third-desktop">
                            <div class="field">
                                <label class="label" for="customer"
                                    >Customer</label
                                >
                                <div class="control">
                                    <v-select
                                        label="name"
                                        name="customer"
                                        v-model="customer"
                                        input-id="customer"
                                        :options="customers"
                                        v-validate="'required'"
                                        @input="customerChange"
                                        @search="searchCustomer"
                                        :style="{ width: '100%' }"
                                        placeholder="Search Customer..."
                                        :class="{
                                            select: true,
                                            'is-danger': errors.has('customer')
                                        }"
                                    >
                                        <template slot="no-options">
                                            Please type to search...
                                        </template>
                                    </v-select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("customer") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half-tablet is-one-third-desktop">
                            <div class="field">
                                <label class="label" for="taxes"
                                    >Order Taxes</label
                                >
                                <div class="control">
                                    <v-select
                                        multiple
                                        label="code"
                                        class="select"
                                        :options="taxes"
                                        input-id="taxes"
                                        max-height="150px"
                                        v-model="form.taxes"
                                        :style="{ width: '100%' }"
                                        placeholder="Select Tax..."
                                    ></v-select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("taxes") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half-tablet is-one-third-desktop">
                            <div class="field">
                                <label class="label" for="discount"
                                    >Order Discount</label
                                >
                                <div class="control">
                                    <input
                                        class="input"
                                        id="discount"
                                        name="discount"
                                        v-model="form.discount"
                                        v-validate="{ regex: /^([0-9%]+)$/ }"
                                    />
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("discount") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half-tablet is-one-third-desktop">
                            <div class="field">
                                <label class="label" for="shipping"
                                    >Shipping</label
                                >
                                <div class="control">
                                    <input
                                        class="input"
                                        id="shipping"
                                        name="shipping"
                                        v-model="form.shipping"
                                        v-validate="{ regex: /^([0-9%]+)$/ }"
                                    />
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("shipping") }}
                                </div>
                            </div>
                        </div>
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
                                    list: 'dropdown-menu dropdown-content'
                                }"
                                placeholder="Scan/Search Product or Click add button for manual product"
                                :customHeaders="{
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': $laravel.token
                                }"
                            ></autocomplete>
                        </p>
                        <p class="control">
                            <a class="button" @click="addProduct()">
                                <i class="fa fa-plus" />
                            </a>
                        </p>
                    </div>
                    <div
                        class="field table-body-br"
                        v-if="form.products.length"
                    >
                        <div v-if="errors.products_empty">
                            <p class="alert alert-danger">
                                {{ errors.products_empty[0] }}
                            </p>
                            <hr />
                        </div>
                        <table
                            v-if="isMobile"
                            class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth is-rounded table-form"
                        >
                            <thead>
                                <tr>
                                    <th class="has-text-centered">
                                        Order Items
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(product, key) in form.products"
                                    :key="key"
                                >
                                    <td style="padding:10px 8px 6px 8px;">
                                        <div
                                            class="field"
                                            style="margin-bottom:4px;"
                                        >
                                            <p
                                                class="control"
                                                :style="{ width: '100%' }"
                                            >
                                                <input
                                                    class="input is-small has-text-centered"
                                                    disabled
                                                    :value="'# ' + (key + 1)"
                                                />
                                            </p>
                                        </div>
                                        <div
                                            class="field has-addons"
                                            style="margin-bottom:4px;"
                                        >
                                            <p class="control">
                                                <a class="button is-static">
                                                    <i
                                                        class="fas fa-fw fa-barcode"
                                                    ></i>
                                                </a>
                                            </p>
                                            <p
                                                class="control"
                                                :style="{ width: '100%' }"
                                            >
                                                <input
                                                    class="input"
                                                    v-model="product.name"
                                                />
                                            </p>
                                        </div>
                                        <div
                                            class="field has-addons"
                                            style="margin-bottom:4px;"
                                        >
                                            <p class="control">
                                                <a class="button is-static">
                                                    <i
                                                        class="fas fa-fw fa-dollar-sign"
                                                    ></i>
                                                </a>
                                            </p>
                                            <p
                                                class="control"
                                                :style="{ width: '100%' }"
                                            >
                                                <number-input-component
                                                    :value.sync="product.price"
                                                    :field="{
                                                        label: null,
                                                        name: 'price',
                                                        hideHelp: true
                                                    }"
                                                    :validation="{
                                                        rules: 'required',
                                                        name: 'price'
                                                    }"
                                                ></number-input-component>
                                            </p>
                                        </div>
                                        <div
                                            class="field has-addons"
                                            style="margin-bottom:4px;"
                                        >
                                            <p class="control">
                                                <a class="button is-static">
                                                    <i
                                                        class="fas fa-fw fa-battery-quarter"
                                                    ></i>
                                                </a>
                                            </p>
                                            <p
                                                class="control"
                                                :style="{ width: '100%' }"
                                            >
                                                <number-input-component
                                                    :value.sync="product.qty"
                                                    :validation="{
                                                        rules: 'required',
                                                        name: 'qty'
                                                    }"
                                                    :field="{
                                                        label: null,
                                                        name: 'qty',
                                                        hideHelp: true
                                                    }"
                                                ></number-input-component>
                                            </p>
                                        </div>
                                        <div
                                            class="field has-addons"
                                            style="margin-bottom:4px;"
                                        >
                                            <p class="control">
                                                <a class="button is-static">
                                                    <i
                                                        class="fas fa-fw fa-microchip"
                                                    ></i>
                                                </a>
                                            </p>
                                            <div
                                                class="control p-x-sm tax-border table-taxes"
                                                :style="{ width: '100%' }"
                                            >
                                                <div
                                                    v-for="tax in product.taxes"
                                                    :key="tax.id + '_' + tax.id"
                                                    class="inline-block m-b-none"
                                                >
                                                    <small class="text-muted">{{
                                                        tax.code
                                                    }}</small>
                                                    <span
                                                        class="is-pulled-right mono"
                                                        >{{
                                                            (tax.amount *
                                                                product.qty)
                                                                | formatNumber
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="edit"
                                                    @click="
                                                        prTaxes(product, taxes)
                                                    "
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="field has-addons"
                                            style="margin-bottom:4px;"
                                        >
                                            <a
                                                @click="removeLine(key)"
                                                class="button table-remove-btn"
                                            >
                                                <i
                                                    class="fas fa-fw fa-trash has-text-danger"
                                                />
                                            </a>
                                            <p
                                                class="control"
                                                :style="{ width: '100%' }"
                                            >
                                                <input
                                                    disabled
                                                    readonly
                                                    type="text"
                                                    class="input has-text-right has-text-weight-bold"
                                                    :value="
                                                        calcRowTotal(product)
                                                            | formatDecimal(2)
                                                    "
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
                                        <span class="is-pulled-right">{{
                                            subTotal | formatDecimal(2)
                                        }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Product Tax
                                        <span class="is-pulled-right">
                                            <a
                                                @click="
                                                    showProductsTaxDetails(
                                                        productTaxes
                                                    )
                                                "
                                                >{{
                                                    totalProductTax
                                                        | formatDecimal(2)
                                                }}</a
                                            >
                                        </span>
                                    </td>
                                </tr>
                                <!-- <tr v-for="(r, index)  in productTaxes" :key="index" v-html="r"></tr> -->
                                <tr v-if="this.form.discount">
                                    <td>
                                        Discount
                                        <span class="is-pulled-right">{{
                                            discountAmount | formatDecimal(2)
                                        }}</span>
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        this.form.taxes &&
                                            this.form.taxes.length
                                    "
                                >
                                    <td>
                                        Order Tax
                                        <span class="is-pulled-right">{{
                                            taxAmount | formatDecimal(2)
                                        }}</span>
                                    </td>
                                </tr>
                                <tr v-if="this.form.shipping">
                                    <td>
                                        Shipping
                                        <span class="is-pulled-right">{{
                                            shippingAmount | formatDecimal(2)
                                        }}</span>
                                    </td>
                                </tr>
                                <tr v-if="subTotal != grandTotal">
                                    <td>
                                        Grand Total
                                        <span class="is-pulled-right">{{
                                            grandTotal | formatDecimal(2)
                                        }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <table
                            v-else
                            class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth is-rounded table-form"
                        >
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
                                <tr
                                    v-for="(product,
                                    key,
                                    index) in form.products"
                                    :key="key"
                                >
                                    <td class="table-remove has-text-centered">
                                        <a
                                            @click="removeLine(key)"
                                            class="table-remove-btn"
                                        >
                                            <i
                                                class="fas fa-trash has-text-danger"
                                            />
                                        </a>
                                    </td>
                                    <td
                                        class="table-name"
                                        :class="{
                                            'table-error':
                                                errors[
                                                    'products.' +
                                                        index +
                                                        '.name'
                                                ]
                                        }"
                                    >
                                        <input
                                            class="textarea"
                                            v-model="product.name"
                                            rows="1"
                                        />
                                    </td>
                                    <td
                                        class="table-price"
                                        :class="{
                                            'table-error':
                                                errors[
                                                    'products.' +
                                                        index +
                                                        '.price'
                                                ]
                                        }"
                                    >
                                        <number-input-component
                                            :value.sync="product.price"
                                            :validation="{
                                                rules: 'required',
                                                name: 'price'
                                            }"
                                            :field="{
                                                label: null,
                                                name: 'price',
                                                hideHelp: true
                                            }"
                                        ></number-input-component>
                                    </td>
                                    <td
                                        class="table-qty"
                                        :class="{
                                            'table-error':
                                                errors[
                                                    'products.' + index + '.qty'
                                                ]
                                        }"
                                    >
                                        <number-input-component
                                            :value.sync="product.qty"
                                            :validation="{
                                                rules: 'required',
                                                name: 'qty'
                                            }"
                                            :field="{
                                                label: null,
                                                name: 'qty',
                                                hideHelp: true
                                            }"
                                        ></number-input-component>
                                    </td>
                                    <td class="table-taxes">
                                        <div
                                            v-for="tax in product.taxes"
                                            :key="tax.id + '_' + tax.id"
                                            class="inline-block m-b-none"
                                        >
                                            <small class="text-muted">{{
                                                tax.code
                                            }}</small>
                                            <span
                                                class="is-pulled-right mono"
                                                >{{
                                                    (tax.amount * product.qty)
                                                        | formatNumber
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="edit"
                                            @click="prTaxes(product, taxes)"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </div>
                                    </td>
                                    <td class="table-total has-text-right">
                                        <span class="table-text">{{
                                            calcRowTotal(product)
                                                | formatDecimal(2)
                                        }}</span>
                                    </td>
                                </tr>
                            </transition-group>

                            <tfoot class="has-text-weight-bold">
                                <tr>
                                    <td
                                        class="table-label has-text-right"
                                        colspan="3"
                                    >
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
                                        <a
                                            @click="
                                                showProductsTaxDetails(
                                                    productTaxes
                                                )
                                            "
                                            >{{
                                                totalProductTax
                                                    | formatDecimal(2)
                                            }}</a
                                        >
                                    </td>
                                    <td class="table-amount has-text-right">
                                        {{ subTotal | formatDecimal(2) }}
                                    </td>
                                </tr>
                                <!-- <tr v-for="(r, index)  in productTaxes" :key="index" v-html="r"></tr> -->
                                <tr v-if="this.form.discount">
                                    <td
                                        class="table-label has-text-right"
                                        colspan="5"
                                    >
                                        Discount
                                    </td>
                                    <td class="table-amount has-text-right">
                                        {{ discountAmount | formatDecimal(2) }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        this.form.taxes &&
                                            this.form.taxes.length
                                    "
                                >
                                    <td
                                        class="table-label has-text-right"
                                        colspan="5"
                                    >
                                        Order Tax
                                    </td>
                                    <td class="table-amount has-text-right">
                                        {{ taxAmount | formatDecimal(2) }}
                                    </td>
                                </tr>
                                <tr v-if="this.form.shipping">
                                    <td
                                        class="table-label has-text-right"
                                        colspan="5"
                                    >
                                        Shipping
                                    </td>
                                    <td class="table-amount has-text-right">
                                        {{ shippingAmount | formatDecimal(2) }}
                                    </td>
                                </tr>
                                <tr v-if="subTotal != grandTotal">
                                    <td
                                        class="table-label has-text-right"
                                        colspan="5"
                                    >
                                        Grand Total
                                    </td>
                                    <td class="table-amount has-text-right">
                                        {{ grandTotal | formatDecimal(2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <article class="message" v-else>
                        <div class="message-body">
                            Please serach the product by code or name to add to
                            the order list, You can scan the barcode too.
                        </div>
                    </article>
                    <div v-if="attributes">
                        <h5 class="cf">Custom Fields</h5>
                        <div class="columns is-multiline">
                            <div
                                class="column is-one-third-desktop"
                                v-for="attr in attributes"
                                :key="attr.slug"
                            >
                                <custom-field-component
                                    :attr="attr"
                                    v-model="form[attr.slug]"
                                ></custom-field-component>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="note">Note</label>
                                <div class="control">
                                    <textarea
                                        v-model="form.note"
                                        name="note"
                                        id="note"
                                        rows="3"
                                        class="textarea"
                                    ></textarea>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("note") }}
                                </div>
                            </div>
                            <div class="field">
                                <checkbox-component
                                    id="active"
                                    name="active"
                                    v-model="form.active"
                                    :checked="form.active"
                                    label="This recurring invoice is active"
                                ></checkbox-component>
                            </div>
                            <div class="field">
                                <button
                                    type="submit"
                                    class="button is-link"
                                    :disabled="errors.any()"
                                    :class="{ 'is-loading': isSaving }"
                                >
                                    Submit
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
import Order from "../../mixins/Order";
export default {
    mixins: [Order("recurring")],
    data() {
        return {};
    },
    created() {
        this.$event.listen("addProductToRecurring", product => {
            this.$storage.remove("order");
            this.productSelected(product);
        });
    },
    methods: {
        addProduct() {
            this.$storage.write("order", "ToRecurring");
            this.$modal.show("product-form-modal");
        }
    }
};
</script>

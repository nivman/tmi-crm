<template>
    <div class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="select is-small is-pulled-right m-l-sm">
                            <!-- <v-select v-model="template" v-validate="'required'" @input="templateChange" :options="templates" placeholder="Select template..." input-id="template" :class="{'select': true, 'is-danger': errors.has('template')}" name="template"></v-select> -->
                            <select
                                name="template"
                                id="template"
                                v-model="template"
                            >
                                <option
                                    v-for="t in templates"
                                    :value="t"
                                    :key="t.value"
                                    >{{ t.label }}</option
                                >
                            </select>
                        </div>
                        Email Templates ({{ template.label }})
                    </div>
                    <div class="panel-block">
                        <p class="block">
                            Please update the chosen template by modifying html
                            below
                        </p>
                    </div>
                    <form
                        autocomplete="off"
                        action="#"
                        @submit.prevent="validateForm()"
                        class="is-fullwidth"
                        ref="form"
                    >
                        <div class="panel-body">
                            <loading v-if="loading"></loading>
                            <div class="columns is-multiline">
                                <div class="column is-6">
                                    <!-- <textarea rows="20" v-model="template_data" name="template_data" v-validate=" 'required'" class="textarea"></textarea> -->
                                    <codemirror
                                        ref="templateEditor"
                                        v-model="template_data"
                                        :options="{
                                            mode: 'htmlmixed',
                                            lineNumbers: true,
                                            tabSize: 2,
                                            lineWrapping: true
                                        }"
                                    ></codemirror>
                                    <div class="help is-danger">
                                        {{ errors.first("template_data") }}
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <iframe
                                        :srcdoc="editorCode"
                                        frameborder="0"
                                        class="frame"
                                    ></iframe>
                                </div>
                                <div class="column is-12">
                                    <strong>Available variables:</strong>
                                    <code
                                        v-if="template.value == 'user_created'"
                                        v-text="
                                            '{{$name}}, {{$username}}, {{$email}}, {{$password}}, {{$login_url}}'
                                        "
                                    ></code>
                                    <code
                                        v-else-if="
                                            template.value == 'user_reset'
                                        "
                                        v-text="
                                            '{{$name}}, {{$username}}, {{$email}}, {{$password}}, {{$login_url}}'
                                        "
                                    ></code>
                                    <code
                                        v-else-if="
                                            template.value == 'invoice_created'
                                        "
                                        v-text="
                                            '{{$customer_name}}, {{$customer_company}}, {{ $invoice_number }}, {{ $grand_total }}, {{$invoice_url}}'
                                        "
                                    ></code>
                                    <code
                                        v-else-if="
                                            template.value == 'purchase_created'
                                        "
                                        v-text="
                                            '{{$vendor_name}}, {{$vendor_company}}, {{ $purchase_number }}, {{ $grand_total }}, {{$purchase_url}}'
                                        "
                                    ></code>
                                    <span
                                        v-else-if="
                                            template.value == 'payment_created'
                                        "
                                    >
                                        <code
                                            v-text="
                                                '{{$amount}}, {{$payment_number}}, {{$payable_name}}, {{$payable_company}}, {{$payment_url}}, {{$customer}}'
                                            "
                                        ></code>
                                        <br />
                                        <span
                                            v-text="
                                                '{{$customer}} is boolean to check if the payable is customer or not.'
                                            "
                                        ></span>
                                    </span>
                                    <span
                                        v-else-if="
                                            template.value == 'payment_received'
                                        "
                                    >
                                        <code
                                            v-text="
                                                '{{$amount}}, {{$gateway}}, {{$payment_number}}, {{$gateway_transaction_id}}, {{$payable_name}}, {{$payable_company}}, {{$payment_url}}, {{$customer}}'
                                            "
                                        ></code>
                                        <br />
                                        <span
                                            v-text="
                                                '{{$customer}} is boolean to check if the payable is customer or not, while {{$gateway_transaction_id}} is only for online payments.'
                                            "
                                        ></span>
                                    </span>
                                </div>
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { codemirror } from "vue-codemirror-lite";
require("codemirror/mode/htmlmixed/htmlmixed");
export default {
    components: { codemirror },
    data() {
        return {
            loading: true,
            isSaving: false,
            template_data: "",
            full_template_data: "",
            form: new this.$form({ template: "invoice_created" }),
            template: { label: "User Created", value: "user_created" },
            templates: [
                { label: "Invoice Created", value: "invoice_created" },
                { label: "Payment Created", value: "payment_created" },
                { label: "Payment Received", value: "payment_received" },
                { label: "Purchase Created", value: "purchase_created" },
                { label: "User Created", value: "user_created" },
                { label: "User Password Reset", value: "user_reset" }
            ]
        };
    },
    created() {
        this.fetchTemplate();
    },
    computed: {
        editor() {
            return this.$refs.templateEditor.editor;
        },
        editorCode() {
            return (
                this.full_template_data.header +
                this.template_data +
                this.full_template_data.footer
            );
        }
    },
    watch: {
        template: function() {
            this.fetchTemplate();
        }
    },
    methods: {
        templateChange(selected) {
            this.template = selected;
            this.form.template = selected ? selected.value : "";
        },
        fetchTemplate() {
            this.loading = true;
            this.$http
                .get(`app/templates/${this.template.value}`)
                .then(res => {
                    this.full_template_data = res.data;
                    this.template_data = res.data.template;
                    setTimeout(() => {
                        this.editor.scrollTo(200, 120);
                        // this.editor.scrollIntoView({line: 333});
                    }, 300);
                    this.loading = false;
                })
                .catch(err => {
                    this.loading = false;
                    this.$event.fire("appError", err.response);
                });
        },
        validateForm() {
            this.$validator
                .validateAll()
                .then(result => {
                    console.log(result);
                    if (result) {
                        this.isSaving = true;
                        let form = new this.$form({
                            template: this.template_data
                        });
                        console.log(form);
                        form.put(`app/templates/${this.template.value}`)
                            .then(res => {
                                this.template_data = res.data.template;
                                this.notify(
                                    "success",
                                    "Template has been successfully updated."
                                );
                                this.isSaving = false;
                            })
                            .catch(err => {
                                this.isSaving = false;
                                this.$event.fire("appError", err.response);
                            });
                    }
                })
                .catch(err => this.$event.fire("appError", err));
        }
    }
};
</script>

<style lang="scss">
.CodeMirror {
    border-radius: 3px;
    min-height: 550px !important;
    border: 1px solid #ccc !important;
}
.frame {
    width: 100%;
    min-height: 550px;
    border-radius: 3px;
    border: 1px solid #ccc;
}
</style>

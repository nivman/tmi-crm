<template>
    <div class="field">
        <label :for="field.id" class="label" v-if="field.label">{{ field.label }}</label>
        <p class="control" :class="{ 'has-icons-right': field.icon }">
            <input
                ref="input"
                class="input"
                v-model="val"
                :id="field.id"
                @change="onInput"
                @blur="onFormat"
                :name="field.name"
                v-validate="rules"
                :disabled="field.disabled"
                @focus="$event.target.select()"
                :class="{ 'is-danger': errors.has(validation.name) }"
            />
            <span v-if="field.icon" class="icon is-small is-right" v-html="field.icon.html"></span>
        </p>
        <div class="help is-danger">{{ errors.first(validation.name) }}</div>
    </div>
</template>

<script>
export default {
    inject: ['$validator'],
    props: {
        field: { type: Object, required: false },
        value: { required: false, twoWay: true },
        validation: { type: Object, required: false },
    },
    data() {
        return { val: '' };
    },
    mounted() {
        this.onProp(this.value);
        this.$watch('value', this.onProp);
    },
    computed: {
        rules() {
            if (typeof this.validation.rules === 'string' || this.validation.rules instanceof String) {
                let rules = this.validation.rules.split(',');
                var rulesObject = {};
                for (let r of rules) {
                    rulesObject[r] = true;
                }
            } else {
                rulesObject = this.validation.rules;
            }
            return { decimal: true, ...rulesObject };
        },
    },
    methods: {
        onProp(value) {
            this.val = this.$options.filters.formatDecimal(value, 2);
        },
        onFormat(value) {
            this.val = this.$options.filters.formatDecimal(this.val, 2);
        },
        onInput() {
            this.$emit('update:value', this.val);
        },
    },
};
</script>

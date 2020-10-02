<template>
    <span class="radio">
        <input
            :id="id"
            :name="name"
            type="radio"
            :value="value"
            :checked="state"
            :class="className"
            @change="onChange"
            :required="required"
            :disabled="disabled"
        />
        <label :for="id" v-if="label"> <span></span> {{ label }} </label>
    </span>
</template>

<script>
export default {
    props: {
        label: {
            type: String,
            required: false,
        },
        id: {
            type: String,
            default: function() {
                return 'radio-id-' + this._uid;
            },
        },
        name: {
            type: String,
            default: null,
        },
        value: {
            default: '',
        },
        modelValue: {
            default: undefined,
        },
        className: {
            type: String,
            default: null,
        },
        checked: {
            type: Boolean,
            default: false,
        },
        required: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        model: {},
    },
    model: {
        prop: 'modelValue',
        event: 'input',
    },
    computed: {
        state() {
            if (this.modelValue === undefined) {
                return this.checked;
            }
            return this.modelValue === this.value;
        },
    },
    methods: {
        onChange() {
            this.toggle();
        },
        toggle() {
            this.$emit('input', this.state ? '' : this.value);
        },
    },
    watch: {
        checked(newValue) {
            if (newValue !== this.state) {
                this.toggle();
            }
        },
    },
    mounted() {
        if (this.checked && !this.state) {
            this.toggle();
        }
    },
};
</script>

<style lang="scss" scoped>
$grey: hsl(0, 0%, 29%);

.radio {
    margin-bottom: 5px;
    margin-right: 1rem;
    display: inline-block;
}
.radio + .radio {
    margin-left: 0;
}
input[type='radio'] {
    display: none;
}
label {
    color: $grey;
    cursor: pointer;
}
input[type='radio']:checked + label {
    color: $grey;
}
input[type='radio'] + label span {
    width: 22px;
    height: 22px;
    margin-right: 5px;
    position: relative;
    border-radius: 10px;
    vertical-align: top;
    display: inline-block;
    border: 1px solid #dbdbdb;
}
input[type='radio'] + label span {
    border-radius: 50%;
}
input[type='radio'] + label span::before {
    content: '';
    display: block;
}
input[type='radio']:checked + label span::before {
    top: 5px;
    right: 5px;
    content: '';
    width: 10px;
    height: 10px;
    background: $grey;
    border-radius: 50%;
    position: absolute;
    text-align: center;
}
</style>

<template>
    <span class="checkbox">
        <input
            :id="id"
            :name="name"
            :value="value"
            type="checkbox"
            class="invisible"
            :checked="checked"
            @change="$emit('input', $event.target.checked && v > 1 ? v : $event.target.checked)"
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
        name: {
            type: String,
            required: false,
        },
        value: {
            required: false,
        },
        id: {
            type: String,
            required: false,
        },
        checked: {
            type: Boolean,
            required: false,
        },
    },
    data() {
        return { v: this.value };
    },
};
</script>

<style lang="scss" scoped>
$grey: hsl(0, 0%, 29%);

.checkbox {
    margin-bottom: 5px;
    margin-right: 1rem;
    display: inline-block;
}
input[type='checkbox'] {
    display: none;
}
label {
    color: $grey;
    cursor: pointer;
    line-height: 22px;
    align-items: center;
    display: inline-flex;
    justify-content: center;
}
input[type='checkbox']:checked + label {
    color: $grey;
}
input[type='checkbox'] + label span {
    width: 22px;
    height: 22px;
    margin-right: 5px;
    border-radius: 3px;
    position: relative;
    vertical-align: top;
    display: inline-block;
    border: 1px solid #dbdbdb;
}

input[type='checkbox'] + label span::before {
    content: '';
    display: block;
}

// input[type="checkbox"]:checked + label span::before {
//     width: 20px;
//     color: $grey;
//     content: "✓";
//     height: 18px;
//     font-size: 18px;
//     position: absolute;
//     text-align: center;
// }
input[type='checkbox']:checked + label span::before {
    top: 5px;
    left: 5px;
    content: '';
    width: 10px;
    height: 10px;
    background: $grey;
    border-radius: 2px;
    position: absolute;
    text-align: center;
}
</style>

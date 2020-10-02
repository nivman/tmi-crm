<template>
    <div class="field">
        <label :for="attr.slug" class="label">{{ attr.name }}</label>

        <input
            type="text"
            class="input"
            :id="attr.slug"
            :name="attr.slug"
            v-bind:value="value"
            v-if="attr.type == 'varchar'"
            :class="{ 'is-danger': errors.has(attr.slug) }"
            v-validate="attr.is_required ? 'required' : {}"
            v-on:input="$emit('input', $event.target.value)"
        />

        <input
            type="number"
            class="input"
            :id="attr.slug"
            :name="attr.slug"
            v-bind:value="value"
            v-if="attr.type == 'integer'"
            :class="{ 'is-danger': errors.has(attr.slug) }"
            v-validate="attr.is_required ? 'required' : {}"
            v-on:input="$emit('input', $event.target.value)"
        />

        <flat-pickr
            class="input"
            v-model="date"
            :id="attr.slug"
            :name="attr.slug"
            :config="config"
            @on-change="updateDate"
            v-if="attr.type == 'datetime'"
            :class="{ 'is-danger': errors.has(attr.slug) }"
            v-validate="attr.is_required ? 'required' : {}"
        ></flat-pickr>

        <div class="select is-fullwidth" v-if="attr.type == 'boolean'">
            <select
                class="input"
                :id="attr.slug"
                :name="attr.slug"
                v-bind:value="value"
                :class="{ 'is-danger': errors.has(attr.slug) }"
                v-validate="attr.is_required ? 'required' : {}"
                v-on:input="$emit('input', $event.target.value)"
            >
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div v-if="attr.type == 'text'">
            <textarea
                rows="3"
                :id="attr.slug"
                class="textarea"
                :name="attr.slug"
                v-bind:value="value"
                :class="{ 'is-danger': errors.has(attr.slug) }"
                v-validate="attr.is_required ? 'required' : {}"
                v-on:input="$emit('input', $event.target.value)"
            >
            </textarea>
        </div>

        <div class="help is-danger">{{ errors.first(attr.slug) }}</div>
    </div>
</template>

<script>
export default {
    inject: ['$validator'],
    props: {
        attr: { type: Object, required: true },
        value: { required: false, twoWay: true },
        time: { type: Boolean, required: false },
    },
    data() {
        return {
            select: 0,
            date: new Date(),
            config: { enableTime: true, dateFormat: 'Y-m-d H:i' },
        };
    },
    mounted() {
        if (this.attr.type == 'datetime' && this.value) {
            this.date = this.value.date;
        }
        if (this.attr.type == 'boolean') {
            this.select = this.value ? 1 : 0;
        }
    },
    methods: {
        updateDate(date, str) {
            this.$emit('input', str);
        },
    },
};
</script>

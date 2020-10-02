<template>
    <div class="panel panel-default is-centered" @refresh="get" v-if="config">
        <div class="panel-heading">
            <span v-if="!params.hideFilter">
                <button type="button" class="button is-primary is-small is-pulled-right m-l-sm" @click="download()">
                    <i class="fas fa-download"></i>
                </button>
                <div class="select is-small is-pulled-right m-l-sm">
                    <select @change="$emit('yearChanged', $event.target.value)">
                        <option v-for="year in years" :value="year" :key="year" :selected="params.year == year"> {{ year }}</option>
                    </select>
                </div>
                <div class="select is-small is-pulled-right" v-if="params.month">
                    <select @change="$emit('monthChanged', $event.target.value)">
                        <option v-for="month in months" :value="month" :key="month" :selected="params.month == month"> {{ month }}</option>
                    </select>
                </div>
            </span>
            {{ config.title }}
        </div>
        <div class="panel-body">
            <loading v-if="loading"></loading>
            <div class="columns">
                <div class="column has-text-centered">
                    <chart
                        ref="chart"
                        :data="config.data"
                        :type="config.type"
                        :options="config.options"
                        class="has-padding-medium inline-block"
                        :style="{ width: width, height: height }"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Chart from './Chart.vue';
import _times from 'lodash/times';
import { saveAs } from 'file-saver';

export default {
    components: { Chart },
    props: {
        width: { default: '100%' },
        height: { default: '400px' },
        params: { type: Object, default: null },
        source: { type: String, required: true },
    },
    data() {
        return {
            loading: false,
            config: null,
        };
    },
    computed: {
        months() {
            let date = new Date();
            let months = this.params.year == date.getFullYear() ? date.getMonth() + 1 : 12;
            return _times(months).map(i =>
                this.$moment()
                    .month(i)
                    .format('MM')
            );
        },
        years() {
            let years = [];
            let date = new Date();
            let year = date.getFullYear();
            for (let y = 2017; y <= year; y++) {
                years.push(y);
            }
            return years;
        },
    },
    watch: {
        params: {
            handler() {
                this.get();
            },
            deep: true,
        },
    },
    mounted() {
        this.get();
    },
    methods: {
        get() {
            if (this.$store.getters.user) {
                this.loading = true;
                this.$http
                    .get(this.source, { params: this.params })
                    .then(({ data }) => {
                        this.config = data;
                        this.loading = false;
                    })
                    .catch(err => {
                        this.loading = false;
                        this.$event.fire('appError', err.response);
                    });
            }
        },
        download() {
            this.$refs.chart.$el.toBlob(blob => saveAs(blob, `${this.config.title}.png`));
        },
    },
};
</script>

<style lang="scss" scoped>
.panel-body {
    position: relative;
}
.inline-block {
    display: inline-block !important;
}
</style>

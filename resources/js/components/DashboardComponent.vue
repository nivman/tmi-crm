<template>
    <div class="wrapper">
        <div class="columns">
            <div class="column is-one-quarter">
                <chart-card :source="pie" :params="pieParams" @monthChanged="pieParamsMonthChanged" @yearChanged="pieParamsYearChanged" />
            </div>
            <div class="column is-three-quarters">
                <chart-card
                    width="100%"
                    :source="line"
                    :params="lineParams"
                    @monthChanged="lineParamsMonthChanged"
                    @yearChanged="lineParamsYearChanged"
                />
            </div>
        </div>
        <div class="columns">
            <div class="column">
                <chart-card :source="bar" width="100%" :params="barParams" @yearChanged="barParamsYearChanged" />
            </div>
        </div>
    </div>


</template>

<script>
import ChartCard from './charts/ChartCard.vue';
import * as moment from 'moment';
import 'moment/locale/he';
export default {
    components: { ChartCard },
    data() {
        return {
            loading: true,
            pie: 'app/charts/pie_chart',
            bar: 'app/charts/bar_chart',
            line: 'app/charts/line_chart',
            barParams: { year: moment().format('YYYY') },
            lineParams: { month: moment().format('MM'), year: moment().format('YYYY') },
            pieParams: { month: moment().format('MM'), year: moment().format('YYYY'), hideFilter: true },
        };
    },
    methods: {
        lineParamsMonthChanged(month) {
            this.lineParams.month = month;
        },
        lineParamsYearChanged(year) {
            this.lineParams.year = year;
        },
        pieParamsMonthChanged(month) {
            this.pieParams.month = month;
        },
        pieParamsYearChanged(year) {
            this.pieParams.year = year;
        },
        barParamsYearChanged(year) {
            this.barParams.year = year;
        },
    },
};
</script>

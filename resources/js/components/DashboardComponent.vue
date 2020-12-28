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
<!--              <multiselect-->
<!--                  selectLabel=""-->
<!--                  deselectLabel=""-->
<!--                  class="column multiselect-column rtl-direction"-->
<!--                  v-model="project"-->
<!--                  :options="projects"-->
<!--                  :multiple="true"-->
<!--                  :option-height="104"-->
<!--                  :searchable="true"-->
<!--                  :allow-empty="true"-->
<!--                  :close-on-select="true"-->
<!--                  placeholder="בחירת פרוייקטים"-->
<!--                  label="name"-->
<!--                  track-by="name">-->
<!--              </multiselect>-->
                <chart-card
                   :source="bar"
                    width="100%"
                    :params="barParams"
                    @yearChanged="lineParamsYearChanged"
                    @monthChanged="lineParamsMonthChanged"
                    @projectChanged = "barProjectChanged"
                />
            </div>
        </div>
    </div>


</template>

<script>
import ChartCard from './charts/ChartCard.vue';
import * as moment from 'moment';
import 'moment/locale/he';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
export default {
    components: { ChartCard, Multiselect},
    data() {
        return {
            project:'',
            projects:[],
            projectsIds: [],
            loading: true,
            pie: 'app/charts/pie_chart',
            bar: 'app/charts/bar_chart',
            line: 'app/charts/line_chart',
            barParams: { month: moment().format('MM'),year: moment().format('YYYY'), showProjectsList: true , projects :[]},
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
         barParamsMonthChanged(month) {

        this.barParams.month = month;
        },
      barProjectChanged(projects) {

        this.barParams.projects = projects;
      }
    },
};
</script>
